# Audit — API du système de comptes ressources (SIKA / Kobo)

**Périmètre analysé** : contrôleurs Laravel qui gèrent les comptes ressources (portefeuille interne prépayé), le paiement SEMOA, l'authentification API et le déclenchement de paiements par action.

**Statut** : 🔴 2 vulnérabilités critiques confirmées, à corriger avant toute exposition externe de ces endpoints.

---

## 1. Vue d'ensemble du système

Le projet est en **Laravel**. Le terme « ressource » désigne un **compte financier interne** (crédit prépayé type « SIKA », « Kobo ») utilisé par les membres/entreprises pour payer formations, prestations, événements, espaces, cotisations et abonnements.

### Contrôleurs principaux

| Contrôleur | Rôle |
|---|---|
| `RessourcecompteController` | CRUD du compte ressource + déclenchement du paiement SEMOA |
| `Api\RessourceCompteCallbackController` | Callback de validation de paiement (webhook SEMOA) |
| `RessourceSyncController` | Synchronisation vers Supabase |
| `ModuleressourceController` | Attribution de module ressource (débite un compte) |
| `Api\PaymentApiController` | Déclenchement de paiement via action, pour intégrations externes |
| `Api\RewardApiController` | Attribution de récompenses |
| `Api\AuthController` | Authentification (délègue à Supabase) + émission de token API local |

### Endpoints clés

| Méthode | URL | Contrôleur::méthode | Middleware |
|---|---|---|---|
| POST | `/api/callback/ressourcecompte/{transaction}` | `RessourceCompteCallbackController::handle` | **Aucun** |
| POST | `/api/payments/trigger` | `PaymentApiController::triggerPayment` | **Aucun** |
| GET | `/api/payments/actions` | `PaymentApiController::listPaymentActions` | **Aucun** |
| GET | `/api/payments/status/{reference}` | `PaymentApiController::checkPaymentStatus` | **Aucun** |
| POST | `/api/rewards/attribute` | `RewardApiController::attribuerRecompense` | **Aucun** |
| GET/POST | `/api/modules/*` | `ModuleressourceController` | **Aucun** |
| POST | `/api/v1/auth/login` | `AuthController::login` | Aucun (normal, public) |
| POST/GET | `/api/v1/auth/logout`, `/me` | `AuthController` | `api.token` ✅ |
| GET | `/api/v1/diagnostic/complet/{email}` | `DiagnosticController` | `api.token` ✅ |

**Constat général** : seules les routes du groupe `v1/diagnostic` sont protégées par le middleware `api.token`. Tout le groupe historique (`callback`, `payments`, `rewards`, `modules`) est **public, sans authentification ni vérification de signature**.

---

## 2. 🔴 Vulnérabilité critique #1 — Callback de paiement falsifiable

**Fichier** : `app/Http/Controllers/Api/RessourceCompteCallbackController.php`
**Route** : `POST /api/callback/ressourcecompte/{transaction}` (`routes/api.php:12-14`)

### Le problème

Ce endpoint reçoit le webhook du prestataire **SEMOA** confirmant qu'un paiement a été effectué. Il n'y a **aucune vérification d'authenticité** du payload reçu (pas de signature HMAC, pas de secret partagé, pas d'appel retour vers l'API SEMOA pour confirmer l'état réel) :

```php
if (
    (isset($payload['state']) && strtolower($payload['state']) == 'paid') ||
    (isset($payload['state']) && strtolower($payload['state']) == 'success') ||
    (isset($payload['received_amount']) && (int)$payload['received_amount'] >= (int)$transaction->montant)
) {
    $success = true;
}
```

### Scénario d'exploitation

Un attaquant qui connaît (ou devine, si les ID sont séquentiels) l'identifiant d'une transaction en attente peut simplement envoyer :

```
POST /api/callback/ressourcecompte/42
Content-Type: application/json

{"state": "paid"}
```

→ Le compte ressource est crédité **sans qu'aucun paiement réel n'ait eu lieu**.

### Correctif recommandé

- Vérifier une signature HMAC (secret partagé avec SEMOA) sur chaque requête entrante, ou
- Ré-interroger l'API SEMOA (`GET /transactions/{id}`) pour confirmer l'état réel avant de créditer, plutôt que de faire confiance au payload reçu.
- Ne pas dépendre uniquement de l'absence de CSRF pour justifier l'absence de contrôle — CSRF et authentification/signature sont deux problèmes différents.

---

## 3. 🔴 Vulnérabilité critique #2 — Débit de compte sans authentification

**Fichier** : `app/Http/Controllers/Api/PaymentApiController.php`
**Route** : `POST /api/payments/trigger` (`routes/api.php:24-28`)

### Le problème

`triggerPayment` accepte un `user_id` ou `supabase_user_id` **fourni librement dans le corps de la requête**, sans vérifier que l'appelant est bien cet utilisateur :

```php
$validator = Validator::make($request->all(), [
    'action_code' => 'required|string|max:50',
    'user_id' => 'required_without:supabase_user_id|string|max:255',
    'supabase_user_id' => 'required_without:user_id|string|max:255',
    ...
]);
```

Ce `user_id`/`supabase_user_id` sert ensuite à retrouver le `Membre`, puis `ModuleressourceController::attribuerModuleViaAction` **débite réellement son solde** :

```php
Ressourcetransaction::create([
    'montant' => -$montant,
    'operationtype_id' => 2, // Débit
    ...
]);
$ressourcecompte->decrement('solde', $montant);
```

### Scénario d'exploitation

Sans aucun jeton d'authentification, un attaquant peut envoyer :

```
POST /api/payments/trigger
Content-Type: application/json

{"action_code": "PREMIER_DIAG_DIRIGEANT", "user_id": "<ID d'une victime>"}
```

→ Le compte ressource **d'un tiers** est débité pour financer une action au bénéfice de l'appelant (ou d'un compte cible arbitraire), sans son consentement.

### Correctif recommandé

- Appliquer le middleware `api.token` sur tout le groupe `payments` (et `rewards`, `modules`, qui présentent le même schéma).
- Dériver l'utilisateur à débiter **du token authentifié** (`$request->user()`), jamais d'un champ `user_id` fourni par le client.
- Si des intégrations externes légitimes doivent agir "pour le compte de" tel ou tel membre, mettre en place une clé API par intégration + une vérification explicite d'autorisation (ex. consentement préalable, scope limité).

---

## 4. Autres constats (priorité moindre)

| Fichier | Constat | Recommandation |
|---|---|---|
| `RessourceCompteCallbackController.php` | Log de `$request->headers->all()` en entier | Retirer ou filtrer les headers sensibles avant log |
| `AuthController.php:39-42` | Log de la réponse Supabase complète, incluant `access_token` | Retirer le token du log ou logger uniquement un identifiant de requête |
| `AuthController.php:72` | Un login supprime tous les tokens précédents de l'utilisateur (session unique) | Confirmer que c'est le comportement voulu (sinon, gérer plusieurs tokens actifs) |
| `PaymentApiController::checkPaymentStatus` | Renvoie toujours `status: "completed"` en dur (stub non implémenté) | Implémenter la vraie logique avant mise en production |
| `PaymentApiController.php:191` | `error => $e->getMessage()` renvoyé brut au client | Ne pas exposer les messages d'exception internes en prod |
| Racine du projet | `RessourcecompteController copy.php` et `copy 2.php` | Fichiers dupliqués obsolètes à supprimer |
| `api_payments_documentation.html` | La doc existante affirme *"Authentification Laravel"* comme mesure de sécurité en place | À corriger : aucune authentification n'est réellement appliquée sur ces routes actuellement |

---

## 5. Plan d'action recommandé

1. **Urgent** — Ajouter `api.token` (ou équivalent) sur `/api/payments/*`, `/api/rewards/*`, `/api/modules/*`.
2. **Urgent** — Sécuriser le callback SEMOA par signature/secret partagé, ou vérification côté serveur SEMOA.
3. **Court terme** — Ne plus faire confiance à un `user_id` fourni par le client pour identifier « qui paie » ; utiliser l'utilisateur authentifié.
4. **Court terme** — Nettoyer les logs contenant des tokens/headers sensibles.
5. **Quand possible** — Supprimer les fichiers contrôleurs dupliqués, implémenter `checkPaymentStatus`, corriger la documentation existante.

---

*Document généré suite à une revue de code ciblée sur le module ressources/paiements. À compléter par une revue du contrôleur `RewardApiController` et de la logique `ModuleressourceController` dans leur intégralité si ce périmètre est confirmé comme prioritaire.*
