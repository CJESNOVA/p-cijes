<x-app-layout title="Test de qualification" is-sidebar-open="true" is-header-blur="true">
    <main class="main-content w-full px-[var(--margin-x)] pb-8">
        <div class="flex items-center space-x-4 py-5 lg:py-6">
          <h2
            class="text-xl font-medium text-slate-800 dark:text-navy-50 lg:text-2xl"
          >
            Test de qualification
          </h2>
          <div class="hidden h-full py-1 sm:flex">
            <div class="h-full w-px bg-slate-300 dark:bg-navy-600"></div>
          </div>
        </div>
        <div class="grid grid-cols-12 lg:gap-6">
            <div class="col-span-12 pt-6 lg:col-span-8 lg:pb-6">
              
        <h2 class="text-xl font-medium text-slate-800 lg:text-2xl mb-6">
            Test de qualification – {{ $diagnostic->entreprise->nom ?? 'Nouvelle entreprise' }}
        </h2>

        @if($currentModule)
            <div class="mb-4 flex items-center justify-between">
                <div class="text-sm text-slate-600 dark:text-navy-300">
                    Module {{ $modules->search(function($module) use ($currentModule) { return $module->id == $currentModule->id; }) + 1 }} 
                    sur {{ $modules->count() }}
                </div>
                @if(session('success'))
                    <div class="alert flex rounded-lg bg-[#4FBE96] px-4 py-3 text-white mb-4 shadow-lg">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        {{ session('success') }}
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert flex rounded-lg bg-red-500 px-4 py-3 text-white mb-4 shadow-lg">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        {{ session('error') }}
                    </div>
                @endif

                @if(session('warning'))
                    <div class="alert flex rounded-lg bg-yellow-500 px-4 py-3 text-white mb-4 shadow-lg">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                        </svg>
                        {{ session('warning') }}
                    </div>
                @endif
            </div>

            <form action="{{ route('diagnosticentreprisequalification.saveModule', [$entrepriseId, $currentModule->id]) }}" method="POST">
                @csrf
                <input type="hidden" name="entreprise_id" value="{{ $entrepriseId }}">
    
@php
    $existing = $existing ?? collect();
@endphp

                @if(session('error'))
                    <div class="alert flex rounded-lg bg-red-500 px-4 py-3 text-white mb-4 shadow-lg">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        {{ session('error') }}
                    </div>
                @endif

                @if(session('warning'))
                    <div class="alert flex rounded-lg bg-yellow-500 px-4 py-3 text-white mb-4 shadow-lg">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                        </svg>
                        {{ session('warning') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="alert flex rounded-lg bg-red-500 px-4 py-3 text-white mb-4 shadow-lg">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <div>
                            <p class="font-semibold">Erreurs de validation :</p>
                            <ul class="list-disc list-inside mt-1">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                @endif

                <!-- Indicateur de progression -->
                <div class="mb-6">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-sm font-medium text-slate-700 dark:text-navy-300">
                            Progression du test
                        </span>
                        <span class="text-sm font-medium text-[#4FBE96]">
                            {{ $modules->search(function($module) use ($currentModule) { return $module->id == $currentModule->id; }) + 1 }} / {{ $modules->count() }}
                        </span>
                    </div>
                    <div class="w-full bg-slate-200 dark:bg-navy-700 rounded-full h-2">
                        <div class="bg-gradient-to-r from-[#4FBE96] to-[#4FBE96]/80 h-2 rounded-full transition-all duration-300" 
                             style="width: {{ (($modules->search(function($module) use ($currentModule) { return $module->id == $currentModule->id; }) + 1) / $modules->count()) * 100 }}%">
                        </div>
                    </div>
                </div>

                <!-- Module actuel -->
                <div class="card overflow-hidden shadow-2xl">
                    <div class="card-title bg-gradient-to-r from-slate-50 to-white dark:from-navy-800 dark:to-navy-700 p-6">
                        <div class="flex items-center space-x-4">
                            <h3 class="text-xl font-bold text-slate-800 dark:text-white">
                                {{ $currentModule->titre }}
                            </h3>
                        </div>
                        <div class="flex items-center space-x-4">
                            @if($currentModule->description)
                                <p class="text-sm text-slate-600 dark:text-navy-300 mt-1">
                                    {{ $currentModule->description }}
                                </p>
                            @endif
                        </div>
                    </div>

                    @if($currentModule->vignette)
                        <div class="p-6 bg-white dark:bg-navy-900">
                            <img src="{{ env('SUPABASE_BUCKET_URL') . '/' . $currentModule->vignette }}" 
                                 alt="Vignette du module {{ $currentModule->titre }}" 
                                 class="w-full h-auto rounded-lg shadow-lg">
                        </div>
                    @endif



                    <div class="card-body bg-white dark:bg-navy-900 p-6">
                        @if($currentModule->diagnosticquestions->count() > 0)
                            @foreach($currentModule->diagnosticquestions as $diagnosticquestion)
                                <div class="mb-8 p-4 rounded-lg bg-slate-50 dark:bg-navy-800 border border-slate-200 dark:border-navy-700" data-question-id="{{ $diagnosticquestion->id }}">
                                    <label class="block text-lg font-semibold text-slate-700 dark:text-navy-200 mb-3">
                                        <span class="inline-flex items-center space-x-2">
                                            <span class="flex items-center justify-center w-8 h-8 rounded-full bg-slate-200 dark:bg-navy-700 text-slate-700 dark:text-navy-300 text-sm font-bold">
                                                {{ $diagnosticquestion->position }}
                                            </span>
                                            <span>{{ $diagnosticquestion->titre }}</span>
                                        </span>
                                        @if($diagnosticquestion->obligatoire) <span class="text-red-500 ml-2">*</span> @endif
                                    </label>

                                    @php
                                        $type = $diagnosticquestion->diagnosticquestiontype_id;
                                        $inputName = $type == 2 ? "reponses[{$diagnosticquestion->id}][]" : "reponses[{$diagnosticquestion->id}]";
                                        $inputClass = $type == 2 ? 
                                        "form-checkbox is-basic size-5 rounded border-slate-400/70 checked:bg-primary checked:border-primary hover:border-primary focus:border-primary dark:border-navy-400 dark:checked:bg-primary dark:checked:border-primary" : 
                                        "form-radio is-basic size-5 rounded-full border-slate-400/70 checked:border-primary checked:bg-primary hover:border-primary focus:border-primary dark:border-navy-400 dark:checked:bg-primary dark:checked:border-primary";
                                    @endphp

                                    <div class="space-y-3">
                                        @foreach($diagnosticquestion->diagnosticreponses as $diagnosticreponse)
                                        @php
                                            $isChecked = false;
                                            if ($type == 2 && isset($existing[$diagnosticquestion->id])) {
                                                $isChecked = in_array($diagnosticreponse->id, $existing[$diagnosticquestion->id]);
                                            } elseif ($type == 1 && isset($existing[$diagnosticquestion->id])) {
                                                $isChecked = $diagnosticreponse->id == $existing[$diagnosticquestion->id][0];
                                            }
                                        @endphp
                                            <label class="flex items-start space-x-3 p-3 rounded-lg bg-white dark:bg-navy-700 hover:bg-slate-50 dark:hover:bg-navy-600 border border-slate-200 dark:border-navy-600 cursor-pointer transition-all duration-200 hover:shadow-md">
                                                <input type="{{ $type == 2 ? 'checkbox' : 'radio' }}"
                                                    name="{{ $inputName }}"
                                                    value="{{ $diagnosticreponse->id }}"
                                                    data-question-id="{{ $diagnosticquestion->id }}"
                                                    class="{{ $inputClass }} mt-1"
                                                    {{ $isChecked ? 'checked' : '' }}
                                                />
                                                <span class="text-sm text-slate-600 dark:text-navy-300 leading-relaxed">
                                                    {{ $diagnosticreponse->titre }}
                                                </span>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="text-center py-8">
                                <i class="fas fa-clipboard-check text-4xl text-slate-400 mb-4"></i>
                                <p class="text-slate-500 dark:text-navy-400">
                                    Aucune question disponible pour ce module.
                                </p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Boutons de navigation -->
                <div class="mt-8 flex justify-between items-center">
                    <div class="flex space-x-3">
                        @if($isLastModule)
                            @if(session('showFinalization'))
                                <div class="alert flex rounded-lg bg-[#4FBE96] px-4 py-3 text-white mb-4 shadow-lg">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    Tous les modules sont complétés ! Vous pouvez maintenant finaliser votre test.
                                </div>
                            @endif
                            <button type="submit" formaction="{{ route('diagnosticentreprisequalification.store', ['entrepriseId' => $entrepriseId, 'moduleId' => $currentModule->id]) }}" 
                                    class="btn bg-gradient-to-r from-[#4FBE96] to-[#4FBE96]/80 text-white hover:from-[#4FBE96]/90 hover:to-[#4FBE96]/70 px-6 py-3 rounded-lg shadow-lg hover:shadow-xl transition-all duration-200 transform hover:scale-105">
                                <i class="fas fa-check-circle mr-2"></i>
                                Finaliser le test
                            </button>
                        @else
                            <button type="submit" formaction="{{ route('diagnosticentreprisequalification.saveModule', ['entrepriseId' => $entrepriseId, 'moduleId' => $currentModule->id]) }}" 
                                    class="btn bg-gradient-to-r from-[#152737] to-[#152737]/80 text-white hover:from-[#152737]/90 hover:to-[#152737]/70 px-6 py-3 rounded-lg shadow-lg hover:shadow-xl transition-all duration-200 transform hover:scale-105">
                                <i class="fas fa-save mr-2"></i>
                                Enregistrer et continuer
                            </button>
                        @endif
                    </div>
                    
                    <div class="flex space-x-3">
                        @if($previousModule)
                            <a href="{{ route('diagnosticentreprisequalification.showModule', [$entrepriseId, $previousModule->id]) }}" 
                               class="btn bg-gradient-to-r from-slate-500 to-slate-500/80 text-white hover:from-slate-600 hover:to-slate-600/70 px-6 py-3 rounded-lg shadow-lg hover:shadow-xl transition-all duration-200 transform hover:scale-105">
                                <i class="fas fa-arrow-left mr-2"></i>
                                Module précédent
                            </a>
                        @endif
                        
                        @if($nextModule)
                            <button type="button" 
                                   id="nextModuleBtn"
                                   onclick="validerEtContinuer()"
                                   class="btn bg-gradient-to-r from-slate-400 to-slate-400/80 text-white hover:from-slate-500 hover:to-slate-500/70 px-6 py-3 rounded-lg shadow-lg hover:shadow-xl transition-all duration-200 transform hover:scale-105 disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none">
                                <i class="fas fa-arrow-right mr-2"></i>
                                <span id="nextModuleText">Module suivant</span>
                            </button>
                        @endif
                    </div>
                </div>
            </form>
        @else
            <div class="card">
                <div class="card-body text-center py-8">
                    <i class="fas fa-clipboard-check text-4xl text-slate-400 mb-4"></i>
                    <h3 class="text-lg font-medium text-slate-700 dark:text-navy-200 mb-2">
                        Aucun module disponible
                    </h3>
                    <p class="text-slate-600 dark:text-navy-400">
                        Les tests de qualification ne sont pas encore disponibles pour le moment.
                    </p>
                </div>
            </div>
        @endif

            </div>

            <div class="col-span-12 py-6 lg:sticky lg:bottom-0 lg:col-span-4 lg:self-end">
                @include('layouts.sidebar')
            </div>    

        </div>
      </main>
</x-app-layout>

@push('scripts')
<script>
function validerEtContinuer() {
    // Récupérer tous les IDs de questions uniques
    const questionIds = [...new Set(Array.from(document.querySelectorAll('input[data-question-id]'))
        .map(input => input.getAttribute('data-question-id')))];
    
    let allQuestionsAnswered = true;
    
    questionIds.forEach(questionId => {
        const answeredInputs = document.querySelectorAll(`input[data-question-id="${questionId}"]:checked`);
        
        // Si aucune réponse cochée pour cette question
        if (answeredInputs.length === 0) {
            allQuestionsAnswered = false;
        }
    });
    
    if (!allQuestionsAnswered) {
        // Afficher une alerte moderne
        afficherAlerte('⚠️ Veuillez répondre à toutes les questions avant de passer au module suivant.', 'warning');
        
        // Faire défiler vers le haut du formulaire
        const form = document.querySelector('form');
        form.scrollIntoView({ behavior: 'smooth', block: 'start' });
        
        return;
    }
    
    // Si toutes les questions sont répondues, rediriger vers le module suivant
    window.location.href = '{{ route("diagnosticentreprisequalification.showModule", [$entrepriseId, $nextModule->id]) }}';
}

function afficherAlerte(message, type = 'warning') {
    // Supprimer les alertes existantes
    const alertesExistantes = document.querySelectorAll('.alert-validation');
    alertesExistantes.forEach(alerte => alerte.remove());
    
    // Créer la nouvelle alerte
    const alerte = document.createElement('div');
    alerte.className = `alert-validation alert flex rounded-lg px-4 py-3 text-white mb-4 shadow-lg ${
        type === 'warning' ? 'bg-yellow-500' : 
        type === 'error' ? 'bg-red-500' : 
        'bg-[#4FBE96]'
    }`;
    
    alerte.innerHTML = `
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            ${
                type === 'warning' 
                ? '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>'
                : '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>'
            }
        </svg>
        ${message}
    `;
    
    // Insérer l'alerte au début du formulaire
    const form = document.querySelector('form');
    form.insertBefore(alerte, form.firstChild);
    
    // Auto-suppression après 5 secondes
    setTimeout(() => {
        if (alerte.parentNode) {
            alerte.remove();
        }
    }, 5000);
}

// Validation en temps réel lors des changements
document.addEventListener('DOMContentLoaded', function() {
    const inputs = document.querySelectorAll('input[type="radio"], input[type="checkbox"]');
    const nextBtn = document.getElementById('nextModuleBtn');
    const nextBtnText = document.getElementById('nextModuleText');
    
    // Fonction pour mettre à jour l'état du bouton
    function updateNextButtonState() {
        // Récupérer tous les IDs de questions uniques
        const questionIds = [...new Set(Array.from(document.querySelectorAll('input[data-question-id]'))
            .map(input => input.getAttribute('data-question-id')))];
        
        let allQuestionsAnswered = true;
        
        questionIds.forEach(questionId => {
            const answeredInputs = document.querySelectorAll(`input[data-question-id="${questionId}"]:checked`);
            
            // Si aucune réponse cochée pour cette question
            if (answeredInputs.length === 0) {
                allQuestionsAnswered = false;
            }
        });
        
        if (nextBtn) {
            if (allQuestionsAnswered) {
                // Activer le bouton
                nextBtn.disabled = false;
                nextBtn.classList.remove('disabled:opacity-50', 'disabled:cursor-not-allowed', 'disabled:transform-none');
                nextBtn.classList.add('hover:scale-105');
                if (nextBtnText) {
                    nextBtnText.textContent = 'Module suivant';
                }
            } else {
                // Désactiver le bouton
                nextBtn.disabled = true;
                nextBtn.classList.add('disabled:opacity-50', 'disabled:cursor-not-allowed', 'disabled:transform-none');
                nextBtn.classList.remove('hover:scale-105');
                if (nextBtnText) {
                    nextBtnText.textContent = 'Répondez à tout';
                }
            }
        }
    }
    
    // Initialiser l'état du bouton au chargement
    updateNextButtonState();
    
    // Écouter les changements sur tous les inputs
    inputs.forEach(input => {
        input.addEventListener('change', function() {
            // Mettre à jour l'état du bouton
            updateNextButtonState();
            
            // Supprimer les alertes de warning quand l'utilisateur répond
            const alertesWarning = document.querySelectorAll('.alert-validation.bg-yellow-500');
            alertesWarning.forEach(alerte => alerte.remove());
        });
    });
    
    // Empêcher la navigation par clavier sur le bouton désactivé
    if (nextBtn) {
        nextBtn.addEventListener('keydown', function(e) {
            if (this.disabled && (e.key === 'Enter' || e.key === ' ')) {
                e.preventDefault();
                afficherAlerte('⚠️ Veuillez répondre à toutes les questions avant de passer au module suivant.', 'warning');
            }
        });
    }
});
</script>
@endpush
