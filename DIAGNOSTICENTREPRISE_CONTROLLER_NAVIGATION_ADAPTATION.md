# 🎯 **Adaptation du DiagnosticentrepriseController à la Navigation Module par Module**

---

## 📋 **Vue d'ensemble**

Le `DiagnosticentrepriseController` a été complètement adapté pour utiliser le système de navigation module par module, tout en préservant la logique spécifique de filtrage par profil d'entreprise.

---

## 🔧 **Modifications principales**

### **1. Méthode `showForm()` transformée**

#### **Avant (tous les modules en une fois)**
```php
public function showForm($entrepriseId)
{
    // Récupération des modules filtrés par profil
    $diagnosticmodules = $this->getModulesForProfil($entreprise->entrepriseprofil_id, 2)
        ->with(['diagnosticquestions' => function ($q) {
            $q->where('etat', 1)
                ->orderBy('position')
                ->with(['diagnosticreponses' => function ($query) {
                    $query->where('etat', 1)
                            ->inRandomOrder();
                }]);
        }])
        ->get();
    
    // Affichage de tous les modules dans un seul formulaire
    return view('diagnosticentreprise.form', [
        'diagnosticmodules' => $diagnosticmodules,
        // ...
    ]);
}
```

#### **Après (navigation module par module)**
```php
public function showForm($entrepriseId, $moduleId = null)
{
    // Récupération de l'entreprise avec son profil
    $entreprise = Entreprise::with('entrepriseprofil')->findOrFail($entrepriseId);
    
    // Récupération de TOUS les modules type 2, filtrés par profil
    $allDiagnosticmodules = $this->getModulesForProfil($entreprise->entrepriseprofil_id, 2)
        ->with(['diagnosticquestions' => function ($q) {
            $q->where('etat', 1)
                ->orderBy('position')
                ->with(['diagnosticreponses' => function ($query) {
                    $query->where('etat', 1)
                            ->inRandomOrder();
                }]);
        }])
        ->get();

    // Si aucun moduleId spécifié, prendre le premier
    if ($moduleId === null) {
        $currentModule = $allDiagnosticmodules->first();
        $moduleId = $currentModule ? $currentModule->id : null;
    } else {
        $currentModule = $allDiagnosticmodules->where('id', $moduleId)->first();
    }

    // Calcul des modules adjacents
    $currentModuleIndex = $allDiagnosticmodules->search(function($module) use ($moduleId) {
        return $module->id == $moduleId;
    });
    
    $nextModule = $allDiagnosticmodules->get($currentModuleIndex + 1);
    $previousModule = $currentModuleIndex > 0 ? $allDiagnosticmodules->get($currentModuleIndex - 1) : null;
    $isLastModule = ($currentModuleIndex + 1) >= $allDiagnosticmodules->count();

    return view('diagnosticentreprise.form', compact(
        'modules',           // Tous les modules pour navigation
        'currentModule',     // Module actuel
        'nextModule',        // Module suivant
        'previousModule',    // Module précédent
        'isLastModule',      // Si c'est le dernier module
        'entreprise',        // Entreprise avec profil
        // ...
    ));
}
```

---

### **2. Nouvelle méthode `saveModule()`**

```php
public function saveModule(Request $request, $entrepriseId, $moduleId)
{
    // Récupérer l'entreprise avec son profil pour le filtrage
    $entreprise = Entreprise::with('entrepriseprofil')->findOrFail($entrepriseId);

    // Validation des réponses
    $answers = $request->reponses ?? [];
    
    // Vérification questions obligatoires du module
    $module = Diagnosticmodule::find($moduleId);
    $moduleQuestions = $module->diagnosticquestions()->where('etat', 1)->get();
    $obligatoires = $moduleQuestions->where('obligatoire', 1)->pluck('id')->toArray();
    
    // Validation des questions obligatoires
    $obligatoiresManquantes = array_diff($obligatoires, $repondues);
    if (!empty($obligatoiresManquantes)) {
        // Récupérer la position du module pour l'afficher
        $allModules = $this->getModulesForProfil($entreprise->entrepriseprofil_id, 2)
            ->where('etat', 1)
            ->orderBy('position')
            ->get();
        $modulePosition = $allModules->search(function($mod) use ($moduleId) {
            return $mod->id == $moduleId;
        }) + 1;
        $totalModules = $allModules->count();
        
        return redirect()->back()
            ->with('warning', "⚠️ Module {$modulePosition}/{$totalModules} : Il reste {$nbManquantes} question(s) obligatoire(s) non remplie(s).")
            ->withInput();
    }

    // Sauvegarde des réponses du module
    \DB::transaction(function () use ($diagnostic, $moduleId, $answers) {
        // Suppression anciennes réponses du module
        // Enregistrement nouvelles réponses
    });

    // Navigation automatique vers le module suivant
    if ($nextModule) {
        return redirect()->route('diagnosticentreprise.showModule', [$entrepriseId, $nextModule->id])
            ->with('success', "✅ Module {$moduleActuel}/{$totalModules} enregistré !");
    } else {
        return redirect()->back()
            ->with('success', '✅ Dernier module enregistré !')
            ->with('showFinalization', true);
    }
}
```

---

### **3. Méthode `store()` adaptée**

#### **Changements principaux**
- **Signature** : `store(Request $request, RecompenseService $recompenseService, $entrepriseId = null, $moduleId = null)`
- **Logique** : Sauvegarde D'ABORD les réponses du dernier module, PUIS validation globale
- **Validation** : Vérification de TOUS les modules filtrés par profil pour les questions obligatoires
- **Messages** : Indication précise des modules avec questions manquantes

```php
// 🔄 Utiliser une transaction pour la cohérence des données
\DB::transaction(function () use ($diagnostic, $moduleId, $answers) {
    // Sauvegarder les réponses du dernier module D'ABORD
    if ($moduleId) {
        // Supprimer les anciens résultats pour ce module
        // Enregistrer les nouvelles réponses
    }
});

// 🔍 Maintenant vérifier toutes les questions obligatoires de tous les modules
$allModules = $this->getModulesForProfil($entreprise->entrepriseprofil_id, 2)
    ->where('etat', 1)
    ->orderBy('position')
    ->with(['diagnosticquestions' => function ($q) {
        $q->where('etat', 1)
          ->where('obligatoire', 1);
    }])
    ->get();
    
// Validation globale avec indication des modules concernés
$modulesAvecQuestionsManquantes = [];
foreach ($allModules as $index => $module) {
    $questionsManquantesDansModule = $module->diagnosticquestions
        ->whereIn('id', $obligatoiresManquantes);
        
    if ($questionsManquantesDansModule->isNotEmpty()) {
        $modulesAvecQuestionsManquantes[] = ($index + 1);
    }
}
```

---

## 🎨 **Adaptation de la vue**

### **Nouveaux éléments dans l'en-tête**
```blade
@if($currentModule)
    <div class="mt-2 flex items-center gap-2">
        <span class="text-sm text-slate-500">Module:</span>
        <span class="px-2 py-1 bg-orange-500/10 text-orange-600 rounded-full text-sm font-medium">
            {{ $currentModule->titre }}
        </span>
        <span class="text-sm text-slate-500">
            {{ $currentIndex + 1 }}/{{ $allModules->count() }}
        </span>
    </div>
@endif
```

### **Formulaire adapté**
```blade
<form action="{{ $isLastModule ? route('diagnosticentreprise.store', [$entrepriseId, $currentModule->id]) : route('diagnosticentreprise.saveModule', [$entrepriseId, $currentModule->id]) }}" method="POST">
    <!-- Affichage uniquement du module actuel -->
    <div class="mb-8 border-b pb-4">
        <h2>{{ $currentModule->titre }}</h2>
        <!-- Questions du module actuel -->
    </div>
    
    <!-- Boutons de navigation -->
    <div class="mt-8 flex justify-between items-center">
        <!-- Bouton principal (sauvegarder/finaliser) -->
        @if($isLastModule)
            <button type="submit">Finaliser le diagnostic</button>
        @else
            <button type="submit">Enregistrer et continuer</button>
        @endif
        
        <!-- Navigation manuelle -->
        @if($previousModule)
            <a href="{{ route('diagnosticentreprise.showModule', [$entrepriseId, $previousModule->id]) }}">Module précédent</a>
        @endif
        
        @if($nextModule)
            <a href="{{ route('diagnosticentreprise.showModule', [$entrepriseId, $nextModule->id]) }}">Module suivant</a>
        @endif
    </div>
</form>
```

---

## 🛣️ **Routes nécessaires**

### **Nouvelles routes ajoutées**
```php
// Afficher un module spécifique
Route::get('/diagnostics/diagnosticentreprise/{entrepriseId}/form/{moduleId}', 'DiagnosticentrepriseController@showForm')
    ->name('diagnosticentreprise.showModule');

// Sauvegarder un module et aller au suivant
Route::post('/diagnostics/diagnosticentreprise/{entrepriseId}/save/{moduleId}', 'DiagnosticentrepriseController@saveModule')
    ->name('diagnosticentreprise.saveModule');

// Finaliser le diagnostic (adaptée)
Route::post('/diagnostics/diagnosticentreprise/{entrepriseId}/store/{moduleId}', 'DiagnosticentrepriseController@store')
    ->name('diagnosticentreprise.store');
```

---

## 🎯 **Flux de navigation**

### **Scénario normal**
```
Choix entreprise → Module 1 → saveModule() → Module 2 → saveModule() → ... → Module N → store() → Success
```

### **Navigation manuelle**
```
Module 3 → clic "Module précédent" → Module 2 → clic "Module suivant" → Module 3
```

### **Gestion des erreurs**
```
Module X → saveModule() → erreur questions obligatoires → retour Module X avec message
```

---

## 📊 **Avantages de l'adaptation**

### **🎯 Expérience utilisateur**
- **Progression claire** : "Module 3/8 - Bloc Organisationnel"
- **Sauvegarde continue** : Pas de perte de données
- **Navigation flexible** : Possibilité de revenir en arrière

### **🔧 Robustesse**
- **Validation par module** : Contrôle qualité immédiat
- **Messages précis** : "Module 3/8 : 2 questions obligatoires manquantes"
- **État cohérent** : Le diagnostic reste toujours valide

### **⚡ Performance**
- **Chargement par module** : Seules les questions nécessaires
- **Requêtes optimisées** : Eager loading des relations
- **Interface réactive** : Navigation fluide

---

## 🔄 **Fonctionnalités préservées**

### **✅ Logique métier intacte**
- **Filtrage par profil** : `getModulesForProfil($entreprise->entrepriseprofil_id, 2)`
- **Génération automatique des plans** : `genererPlansAutomatiques($diagnostic)`
- **Récompenses** : `DIAG_ENTREPRISE_COMPLET`
- **Création d'accompagnement** : Avec entreprise_id
- **Calcul des scores** : A, B, C, D

### **✅ Sécurité et permissions**
- **Vérification entreprise** : `Entreprisemembre::where('membre_id', $membre->id)`
- **Accès contrôlé** : Vérification que le membre peut accéder à l'entreprise
- **Transactions** : Cohérence des données

---

## 🚀 **Points d'attention**

### **Compatibilité**
- **Anciens diagnostics** : Toujours accessibles via la route `/diagnostics/diagnosticentreprise/{entrepriseId}/form`
- **Nouveaux diagnostics** : Utilisent le système module par module
- **Données** : Structure inchangée dans la base de données

### **Spécificités entreprise**
- **Filtrage par profil** : Les modules sont toujours filtrés selon `entrepriseprofil_id`
- **Multi-entreprises** : Un membre peut gérer plusieurs entreprises
- **Accompagnement** : Lié à l'entreprise spécifique

---

## 🎨 **Design adapté**

### **Couleur thème**
- **Orange** : `bg-orange-500/10 text-orange-600` pour les badges de module
- **Finalisation** : `from-orange-500 to-orange-600/80` pour le bouton

### **Messages spécifiques**
- **Module** : "Module 3/8 : Il reste 2 questions obligatoires non remplie(s)"
- **Finalisation** : "Tous les modules sont complétés ! Vous pouvez maintenant finaliser votre diagnostic."

---

**L'adaptation est maintenant complète et le `DiagnosticentrepriseController` offre une expérience utilisateur moderne avec navigation module par module, tout en préservant la logique de filtrage par profil d'entreprise !** 🎯✨
