# 🎯 Système Avancé de Gestion des Statuts de Diagnostics

---

## 📋 **Vue d'ensemble**

Ce système permet une gestion intelligente des statuts de diagnostics avec des règles personnalisables, un historique des changements, et des orientations automatiques basées sur les scores.

---

## 🗄️ **Structure des tables**

### **1. `diagnosticstatutregles`** - Règles de statut
```sql
- id
- diagnosticstatut_id (FK)
- score_total_min (score minimum total)
- score_total_max (score maximum total)
- min_blocs_score (nombre minimum de blocs avec score)
- min_score_bloc (score minimum par bloc)
- bloc_juridique_min (score minimum bloc juridique)
- bloc_finance_min (score minimum bloc finance)
- aucun_bloc_inf (aucun bloc inférieur à ce seuil)
- duree_min_mois (durée minimale en mois)
- created_at, updated_at
```

### **2. `diagnosticstatuthistoriques`** - Historique des changements
```sql
- id
- diagnostic_id (FK)
- ancien_statut_id (FK, nullable)
- nouveau_statut_id (FK)
- raison (raison du changement)
- score_global (score global au moment du changement)
- created_at, updated_at
```

### **3. `diagnosticorientations`** - Orientations par module/statut
```sql
- id
- diagnosticmodule_id (FK)
- diagnosticstatut_id (FK)
- seuil_max (seuil maximum pour cette orientation)
- dispositif (dispositif recommandé)
- created_at, updated_at
```

### **4. `diagnosticblocstatuts`** - Types de blocs
```sql
- id
- code (code unique: JURIDIQUE, FINANCE, etc.)
- titre (titre du bloc)
- description (description optionnelle)
- created_at, updated_at
```

### **5. Modifications des tables existantes**
- **`diagnosticmodules`** : Ajout de `est_bloquant` (boolean)
- **`diagnosticmodulescores`** : Ajout de `diagnosticblocstatut_id` (FK, nullable)

---

## 🏗️ **Architecture des modèles**

### **Diagnosticstatutregle**
- **Relations** : `belongsTo(Diagnosticstatut)`
- **Méthodes** : `verifierScore()` - Vérifie si un score satisfait la règle
- **Logique** : Évaluation multi-critères (scores, blocs, durée)

### **Diagnosticstatuthistorique**
- **Relations** : `belongsTo(Diagnostic)`, `belongsTo(Diagnosticstatut)` (ancien/nouveau)
- **Méthodes** : `creerChangement()` - Crée un historique de changement
- **Scopes** : `recent()`, `pourDiagnostic()`

### **Diagnosticorientation**
- **Relations** : `belongsTo(Diagnosticmodule)`, `belongsTo(Diagnosticstatut)`
- **Méthodes** : `getOrientationsPourModule()`, `getDispositifRecommande()`
- **Logique** : Orientations basées sur les scores par module

### **Diagnosticblocstatut**
- **Relations** : `hasMany(Diagnosticmodulescore)`
- **Méthodes** : `getByCode()`, `creerBlocsPrincipaux()`
- **Blocs prédéfinis** : JURIDIQUE, FINANCE, RH, STRATEGIE, etc.

---

## ⚙️ **Service principal**

### **DiagnosticStatutService**

#### **Méthodes principales**
```php
// Évaluer et mettre à jour le statut d'un diagnostic
evaluerStatutDiagnostic($diagnosticId, $force = false)

// Obtenir les orientations pour un diagnostic
getOrientationsDiagnostic($diagnosticId)

// Obtenir l'historique des changements
getHistoriqueStatut($diagnosticId, $limit = 10)

// Réévaluer tous les diagnostics
reevaluerTousLesDiagnostics()

// Obtenir les statistiques des statuts
getStatistiquesStatuts()
```

#### **Logique d'évaluation**
1. **Calcul des scores par bloc** : Agrégation des scores par type de bloc
2. **Application des règles** : Vérification séquentielle des règles de statut
3. **Mise à jour** : Changement de statut si nécessaire
4. **Historisation** : Enregistrement automatique des changements

---

## 🎯 **Exemples d'utilisation**

### **1. Évaluation automatique**
```php
$service = new DiagnosticStatutService();
$resultat = $service->evaluerStatutDiagnostic($diagnosticId);

if ($resultat['statut_change']) {
    echo "Statut changé de {$resultat['ancien_statut']->titre} vers {$resultat['nouveau_statut']->titre}";
}
```

### **2. Création de règles personnalisées**
```php
Diagnosticstatutregle::create([
    'diagnosticstatut_id' => $statutEligible->id,
    'score_total_min' => 80,
    'min_blocs_score' => 4,
    'bloc_juridique_min' => 15,
    'bloc_finance_min' => 15,
]);
```

### **3. Configuration des orientations**
```php
Diagnosticorientation::create([
    'diagnosticmodule_id' => $moduleFinance->id,
    'diagnosticstatut_id' => $statutEligible->id,
    'seuil_max' => 100,
    'dispositif' => 'Accompagnement financier complet',
]);
```

---

## 📊 **Scénarios de fonctionnement**

### **Scénario 1 : Diagnostic Éligible**
```
Score total: 85/100
Scores par bloc: JURIDIQUE(18), FINANCE(20), RH(16), STRATEGIE(17)
Règle applicable: score_total_min >= 80, min_blocs_score >= 4
Résultat: Statut "Éligible"
Orientations: Accompagnement complet
```

### **Scénario 2 : Diagnostic Conditionnel**
```
Score total: 65/100
Scores par bloc: JURIDIQUE(14), FINANCE(12), RH(15), STRATEGIE(10)
Règle applicable: score_total entre 60-79, min_blocs_score >= 3
Résultat: Statut "Éligible conditionnel"
Orientations: Accompagnement modulé
```

### **Scénario 3 : Historique des changements**
```
01/01/2024: Non évalué → Éligible conditionnel (Score: 65)
15/01/2024: Éligible conditionnel → Éligible (Score: 82)
Raison: Réévaluation après complément d'informations
```

---

## 🔄 **Processus d'évaluation**

### **1. Déclenchement**
- Manuel : `$service->evaluerStatutDiagnostic($id)`
- Automatique : Après sauvegarde d'un diagnostic
- Batch : `$service->reevaluerTousLesDiagnostics()`

### **2. Calcul**
```php
$scoresParBloc = [
    'JURIDIQUE' => 18,
    'FINANCE' => 20,
    'RH' => 16,
    'STRATEGIE' => 17,
];
$scoreGlobal = 71;
```

### **3. Application des règles**
```php
foreach ($regles->orderBy('score_total_min', 'desc') as $regle) {
    if ($regle->verifierScore($scoreGlobal, $scoresParBloc, $dureeMois)) {
        return $regle->diagnosticstatut;
    }
}
```

### **4. Historisation**
```php
Diagnosticstatuthistorique::creerChangement(
    $diagnosticId,
    $ancienStatutId,
    $nouveauStatutId,
    'Évaluation automatique',
    $scoreGlobal
);
```

---

## 🎛️ **Configuration**

### **1. Seeder initial**
```bash
php artisan db:seed --class=DiagnosticStatutSeeder
```

### **2. Migration**
```bash
php artisan migrate
```

### **3. Initialisation des blocs**
```php
$service = new DiagnosticStatutService();
$service->initialiserBlocsStatuts();
```

---

## 📈 **Avantages du système**

### **🎯 Personnalisation**
- Règles flexibles et multi-critères
- Blocs de statut personnalisables
- Orientations adaptées à chaque profil

### **📊 Traçabilité**
- Historique complet des changements
- Audit trail automatique
- Statistiques détaillées

### **⚡ Performance**
- Évaluation optimisée
- Mise en cache possible
- Traitement batch disponible

### **🔧 Maintenabilité**
- Architecture modulaire
- Code réutilisable
- Tests unitaires possibles

---

## 🚀 **Évolutions possibles**

### **Court terme**
- Interface d'administration des règles
- Notifications automatiques de changements
- Export des statistiques

### **Moyen terme**
- Machine Learning pour les prédictions
- Intégration avec des API externes
- Dashboard analytique avancé

### **Long terme**
- Système expert pour les recommandations
- Analyse prédictive des parcours
- Personnalisation par secteur d'activité

---

## 📝 **Notes importantes**

- **Performance** : Prévoir des index sur les clés étrangères
- **Sécurité** : Valider les entrées utilisateur dans les règles
- **Scalabilité** : Prévoir du cache pour les évaluations fréquentes
- **Backup** : Sauvegarder régulièrement l'historique des changements

---

*Ce système offre une base solide pour une gestion intelligente et évolutive des statuts de diagnostics.* 🎯✨
