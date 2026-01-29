# Configuration des Emails CJES Africa

## 📧 Emails implémentés

### 1. Email de bienvenue (`WelcomeNotification`)
- **Déclenchement** : Après inscription réussie
- **Contenu** : Message de bienvenue avec instructions
- **Action** : Lien vers le dashboard

### 2. Email de confirmation (`EmailVerifiedNotification`)
- **Déclenchement** : Après vérification de l'email
- **Contenu** : Confirmation que l'email est validé
- **Action** : Lien pour commencer à utiliser la plateforme

### 3. Email de mot de passe oublié (`PasswordResetNotification`)
- **Déclenchement** : Lors de la demande de réinitialisation
- **Contenu** : Lien de réinitialisation avec instructions de sécurité
- **Action** : Lien vers la page de réinitialisation

### 4. Email de confirmation de réinitialisation (`PasswordResetConfirmationNotification`)
- **Déclenchement** : Après changement du mot de passe
- **Contenu** : Confirmation du changement avec conseils de sécurité
- **Action** : Lien vers le dashboard

## 🔧 Configuration requise

### Variables d'environnement (.env)
```env
# Configuration Mail
MAIL_MAILER=smtp
MAIL_HOST=smtp.votrefournisseur.com
MAIL_PORT=587
MAIL_USERNAME=votre-email@domaine.com
MAIL_PASSWORD=votre-mot-de-passe
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@cjes.africa
MAIL_FROM_NAME="${APP_NAME}"

# Optionnel: pour les logs
MAIL_LOG_CHANNEL=mail
```

### Fournisseurs SMTP recommandés

#### 1. **Brevo (Sendinblue)** - Gratuit
```env
MAIL_HOST=smtp-relay.sendinblue.com
MAIL_PORT=587
MAIL_ENCRYPTION=tls
```
- 300 emails/jour gratuits
- Interface simple
- Bonne délivrabilité

#### 2. **SendGrid** - Gratuit
```env
MAIL_HOST=smtp.sendgrid.net
MAIL_PORT=587
MAIL_ENCRYPTION=tls
```
- 100 emails/jour gratuits
- Très fiable
- Documentation complète

#### 3. **Mailgun** - Payant (5,000 emails/mois gratuits)
```env
MAIL_HOST=smtp.mailgun.org
MAIL_PORT=587
MAIL_ENCRYPTION=tls
```

#### 4. **Gmail SMTP** - Pour les tests
```env
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_ENCRYPTION=tls
```
- Nécessite un "App Password" Google
- Limitations quotidiennes

## 🧪 Tests

### Routes de test (développement uniquement)
```bash
# Test email simple
GET /test-mail

# Test notification avec utilisateur connecté
GET /test-notification
```

### Test manuel
```bash
# Depuis Tinker
php artisan tinker

# Test email simple
Mail::raw('Test email', function($m) { $m->to('votre@email.com')->subject('Test'); });

# Test notification
$user = App\Models\User::first();
$user->notify(new App\Notifications\WelcomeNotification($user->name));
```

## 🚨 Dépannage

### Problèmes courants

#### 1. "Connection could not be established"
- **Cause** : Mauvaise configuration SMTP
- **Solution** : Vérifier les identifiants et le port

#### 2. "Authentication failed"
- **Cause** : Mauvais email/password ou 2FA activée
- **Solution** : Utiliser un "App Password" (Gmail) ou vérifier les identifiants

#### 3. Emails non reçus
- **Causes** : 
  - Dossier spam/indésirables
  - Configuration en mode `log`
  - Adresse email invalide
- **Solutions** :
  - Vérifier les dossiers spam
  - Changer `MAIL_MAILER` de `log` à `smtp`
  - Tester avec une adresse email réelle

### Logs
```bash
# Voir les logs d'emails
tail -f storage/logs/laravel.log

# Voir les logs spécifiques mail
tail -f storage/logs/mail.log
```

## 📊 Monitoring

### En production
- Surveiller les logs d'erreurs
- Configurer des alertes pour les échecs d'envoi
- Tester régulièrement les flux critiques

### Métriques à surveiller
- Taux d'envoi réussi
- Temps de livraison
- Emails en erreur
- Réclamations spam

## 🎨 Personnalisation

### Modifier le design des emails
Les emails utilisent le système `MailMessage` de Laravel. Pour personnaliser :

1. **Templates par défaut** : `resources/views/vendor/mail/`
2. **Thèmes** : Configurer dans `config/mail.php`
3. **Styles** : CSS inline dans les notifications

### Exemple de personnalisation
```php
// Dans une notification
return (new MailMessage)
    ->theme('votre-theme')  // Thème personnalisé
    ->subject('Sujet personnalisé')
    ->markdown('emails.custom') // Template Blade
    ->with(['data' => $customData]);
```

## 🔄 Flux complets

### 1. Inscription
```
Utilisateur s'inscrit 
→ Supabase crée le compte
→ AuthController crée l'utilisateur local
→ 📧 WelcomeNotification envoyée
→ Redirection vers page de confirmation
```

### 2. Confirmation email
```
Utilisateur clique sur le lien Supabase
→ Redirection vers /emails/verify
→ AuthController::emailVerified()
→ 📧 EmailVerifiedNotification envoyée
→ Affichage page de succès
```

### 3. Mot de passe oublié
```
Utilisateur demande la réinitialisation
→ Supabase envoie l'email officiel
→ AuthController envoie notre email personnalisé
→ 📧 PasswordResetNotification envoyée
→ Utilisateur réinitialise son mot de passe
→ 📧 PasswordResetConfirmationNotification envoyée
```

## 🚀 Déploiement

### Avant de mettre en production
1. ✅ Configurer un SMTP réel
2. ✅ Tester tous les flux
3. ✅ Supprimer les routes de test
4. ✅ Configurer les logs
5. ✅ Surveiller les premiers envois

### Checklist production
- [ ] Variables d'environnement configurées
- [ ] SMTP testé et fonctionnel
- [ ] Routes de test supprimées
- [ ] Logs configurés
- [ ] Monitoring en place
