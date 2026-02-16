# 🎯 **Nouvelle Structure des Blocs de Statut**

---

## 📋 **Vue d'ensemble**

Les blocs de statut ont été redéfinis pour refléter une approche par **niveaux de performance** plutôt que par domaines fonctionnels.

---

## 🏗️ **Structure des 5 Niveaux**

### **🔴 Niveau 0 : Critique**
```php
'code' => 'critique'
'titre' => 'Bloc critique'
'description' => 'Bloc bloquant nécessitant un accompagnement prioritaire'
'niveau_performance' => 0
'couleur' => '#dc2626' (rouge)
'est_bloquant' => true
```

### **🟠 Niveau 1 : Fragile**
```php
'code' => 'fragile'
'titre' => 'Bloc fragile'
'description' => 'Bloc insuffisamment structuré'
'niveau_performance' => 1
'couleur' => '#f97316' (orange)
'est_bloquant' => true
```

### **🟡 Niveau 2 : Intermédiaire**
```php
'code' => 'intermediaire'
'titre' => 'Bloc intermédiaire'
'description' => 'Bloc partiellement structuré'
'niveau_performance' => 2
'couleur' => '#eab308' (jaune)
'est_bloquant' => false
```

### **🟢 Niveau 3 : Conforme**
```php
'code' => 'conforme'
'titre' => 'Bloc conforme'
'description' => 'Bloc conforme aux attentes du palier'
'niveau_performance' => 3
'couleur' => '#22c55e' (vert)
'est_bloquant' => false
```

### **🔵 Niveau 4 : Référence**
```php
'code' => 'reference'
'titre' => 'Bloc de référence CJES'
'description' => 'Bloc exemplaire – niveau référence'
'niveau_performance' => 4
'couleur' => '#3b82f6' (bleu)
'est_bloquant' => false
```

---

## 🔧 **Méthodes du modèle**

### **Méthodes de niveau**
```php
// Obtenir le niveau de performance (0-4)
$bloc->getNiveauPerformance(); // Retourne 2 pour 'intermediaire'

// Obtenir la couleur associée
$bloc->getCouleur(); // Retourne '#eab308' pour 'intermediaire'

// Vérifier si le bloc est bloquant
$bloc->estBloquant(); // Retourne true pour 'critique' et 'fragile'
```

### **Méthodes de recherche**
```php
// Obtenir un bloc par son code
$bloc = Diagnosticblocstatut::getByCode('critique');

// Obtenir tous les blocs d'un niveau
$blocsNiveau3 = Diagnosticblocstatut::getByNiveau(3); // Retourne les blocs 'conforme'

// Obtenir la liste pour select
$liste = Diagnosticblocstatut::getListePourSelect();
```

---

## 📊 **Logique d'évaluation**

### **Calcul des scores par niveau**
```php
$scoresParBloc = [
    'critique' => 15,      // Score total des blocs critiques
    'fragile' => 25,       // Score total des blocs fragiles
    'intermediaire' => 40, // Score total des blocs intermédiaires
    'conforme' => 60,      // Score total des blocs conformes
    'reference' => 35,     // Score total des blocs de référence
    
    // Méta-données pour l'évaluation
    'par_niveau' => [
        0 => 15,  // Total niveau critique
        1 => 25,  // Total niveau fragile
        2 => 40,  // Total niveau intermédiaire
        3 => 60,  // Total niveau conforme
        4 => 35,  // Total niveau référence
    ],
    'nb_blocs_critiques' => 2,
    'nb_blocs_reference' => 3,
];
```

### **Exemples de règles adaptées**
```php
// Règle pour Éligible : minimum 80% en niveaux 3-4
'score_total_min' => 80,
'min_blocs_score' => 4,
'min_score_bloc' => 15,

// Règle pour Non éligible : trop de blocs critiques/fragiles
'bloc_critique_max' => 2,
'bloc_fragile_max' => 3,

// Règle pour Référence : majorité en niveau 4
'reference_min_percent' => 60,
```

---

## 🎯 **Scénarios d'utilisation**

### **Scénario 1 : Diagnostic Éligible**
```
Scores par niveau:
- critique: 5 points (1 bloc)
- fragile: 10 points (1 bloc)  
- intermediaire: 20 points (2 blocs)
- conforme: 45 points (3 blocs)
- reference: 30 points (2 blocs)

Score global: 110/100
Statut: Éligible
Orientation: Accompagnement complet
```

### **Scénario 2 : Diagnostic À revoir**
```
Scores par niveau:
- critique: 25 points (3 blocs)
- fragile: 20 points (2 blocs)
- intermediaire: 15 points (1 bloc)

Score global: 60/100
Statut: À revoir (trop de blocs bloquants)
Orientation: Pré-diagnostic prioritaire
```

### **Scénario 3 : Diagnostic Référence**
```
Scores par niveau:
- conforme: 40 points (2 blocs)
- reference: 60 points (3 blocs)

Score global: 100/100
Statut: Référence CJES
Orientation: Programme d'excellence
```

---

## 🔄 **Migration depuis l'ancien système**

### **Anciens blocs → Nouveaux niveaux**
```
JURIDIQUE → Évaluation par niveau (critique à référence)
FINANCE → Évaluation par niveau (critique à référence)
RH → Évaluation par niveau (critique à référence)
STRATEGIE → Évaluation par niveau (critique à référence)
etc.
```

### **Impact sur les règles**
- **Avant** : Basé sur des domaines fonctionnels
- **Après** : Basé sur des niveaux de performance
- **Avantage** : Plus flexible et adaptable à tout type de module

---

## 🎨 **Interface visuelle**

### **Palette de couleurs**
- 🔴 Critique : `#dc2626` (rouge vif)
- 🟠 Fragile : `#f97316` (orange)
- 🟡 Intermédiaire : `#eab308` (jaune)
- 🟢 Conforme : `#22c55e` (vert)
- 🔵 Référence : `#3b82f6` (bleu)

### **Icônes suggérées**
- Critique : ⚠️ ou 🚫
- Fragile : ⚡ ou 🔧
- Intermédiaire : 📈 ou 🔄
- Conforme : ✅ ou 👍
- Référence : ⭐ ou 🏆

---

## 📈 **Avantages de la nouvelle structure**

### **🎯 Précision**
- Évaluation par performance plutôt que par domaine
- Meilleure identification des blocs bloquants
- Hiérarchie claire des niveaux

### **⚡ Flexibilité**
- Adaptable à tout type de module/diagnostic
- Évolution possible des niveaux
- Règles plus fines possibles

### **🔧 Maintenabilité**
- Code plus simple et logique
- Méthodes utilitaires intégrées
- Couleurs et icônes standardisées

---

## 🚀 **Prochaines étapes**

1. **Adapter les règles existantes** aux nouveaux niveaux
2. **Mettre à jour les vues** avec les nouvelles couleurs/icônes
3. **Créer des tableaux de bord** par niveau de performance
4. **Définir les parcours** d'accompagnement par niveau

---

*La nouvelle structure par niveaux de performance offre une approche plus précise et flexible de l'évaluation diagnostique !* 🎯✨
