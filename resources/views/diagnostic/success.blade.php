<x-app-layout title="Diagnostic Résultat" is-sidebar-open="true" is-header-blur="true">
    <main class="main-content w-full px-[var(--margin-x)] pb-8">
        <!-- Header moderne -->
        <div class="mb-2">
            <div class="flex items-center gap-4 mb-2">
                <div class="h-14 w-14 rounded-xl bg-gradient-to-br from-[#4FBE96] to-[#4FBE96] flex items-center justify-center shadow-lg">
                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h-1m2-5h-8"></path>
                    </svg>
                </div>
                <div>
                    <h1 class="text-3xl font-bold text-slate-800 dark:text-navy-50">
                        Diagnostic Résultat
                    </h1>
                    <p class="mt-2 text-slate-600 dark:text-navy-200 text-lg">
                        Consultez les résultats de votre évaluation
                    </p>
                </div>
            </div>
        </div>
        <div class="grid grid-cols-12 lg:gap-6">
            <div class="col-span-12 pt-6 lg:col-span-8 lg:pb-6">

          <!-- Input Validation -->
          <div class="card px-4 pb-4 sm:px-5">
            <div class="max-w-xxl">
              <div
                x-data="pages.formValidation.initFormValidationExample"
                class="mt-5 grid grid-cols-1 gap-4 sm:grid-cols-1 sm:gap-5 lg:gap-6"
              >
   
        <div class="mt-10 text-center">
            @if(session('success'))
                <div class="mb-6 p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg">
                    {{ session('success') }}
                </div>
            @endif
            
            @if(session('error'))
                <div class="mb-6 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg">
                    {{ session('error') }}
                </div>
            @endif

            @if(session('warning'))
                <div class="mb-6 p-4 bg-yellow-100 border border-yellow-400 text-yellow-700 rounded-lg">
                    {{ session('warning') }}
                </div>
            @endif
            
            @if(isset($diagnostic))
                <div class="text-left mb-8">
                    <h2 class="text-2xl font-bold text-[#4FBE96] mb-4">Résultats du diagnostic</h2>
                    
                    <!-- Informations générales -->
                    <div class="bg-[#4FBE96]/10 border border-[#4FBE96]/30 rounded-lg p-4 mb-6">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <strong>Membre :</strong> {{ $diagnostic->membre->nom ?? '' }} {{ $diagnostic->membre->prenom ?? '' }}
                            </div>
                            <div>
                                <strong>Score global :</strong> {{ $diagnostic->scoreglobal ?? 0 }}
                            </div>
                            <div>
                                <strong>Date :</strong> {{ \Carbon\Carbon::parse($diagnostic->created_at)->format('d/m/Y H:i') }}
                            </div>
                            <div>
                                <strong>Statut :</strong> 
                                @if($diagnostic->diagnosticstatut_id == 2)
                                    <span class="text-green-600 font-semibold">Terminé</span>
                                @else
                                    <span class="text-yellow-600 font-semibold">En cours</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    
                    
                    <!-- Résultats par module avec collapses -->
                    <h3 class="text-xl font-semibold text-slate-800 mb-4">Détail par module</h3>
                    
                    @if($modules->isNotEmpty() && $modules->first()->diagnosticmodulecategory)
                        <!-- Header de la catégorie du diagnostic -->
                        <div class="bg-gradient-to-r from-[#4FBE96] to-[#4FBE96]/90 text-white rounded-lg p-4 mb-4">
                            <div class="flex items-center">
                                <i class="fas fa-folder-open mr-3 text-lg"></i>
                                <h4 class="text-lg font-semibold">{{ $modules->first()->diagnosticmodulecategory->titre }}</h4>
                                <span class="ml-3 bg-white/20 px-2 py-1 rounded-full text-sm">
                                    {{ $modules->count() }} module(s)
                                </span>
                            </div>
                        </div>
                    @endif
                    
                    @foreach($modules as $index => $module)
                        <div class="bg-white border border-slate-200 rounded-lg mb-4 shadow-sm">
                            <!-- Header du module (collapsable) -->
                            <div class="p-4 cursor-pointer hover:bg-slate-50 transition-colors duration-200 rounded-t-lg"
                                 onclick="toggleModule('module-{{ $module->id }}')">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center flex-1">
                                        <div class="w-8 h-8 bg-[#4FBE96]/10 rounded-full flex items-center justify-center mr-3">
                                            <span class="text-[#4FBE96] font-semibold text-sm">{{ $index + 1 }}</span>
                                        </div>
                                        <div class="flex-1">
                                            <h5 class="font-semibold text-slate-800">{{ $module->titre }}</h5>
                                            <div class="text-sm text-slate-600 mt-1">
                                                {{ $module->diagnosticquestions->count() }} question(s)
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex items-center space-x-3">
                                        <span class="px-3 py-1 bg-[#4FBE96] text-white rounded-full text-sm font-semibold">
                                            Niveau {{ $niveauxModules[$module->id] ?? 'A' }}
                                        </span>
                                        <span class="text-sm text-slate-600">
                                            ({{ $niveauxModules[$module->id] == 'A' ? 'Faible' : ($niveauxModules[$module->id] == 'B' ? 'Moyen' : ($niveauxModules[$module->id] == 'C' ? 'Bon' : 'Excellent')) }})
                                        </span>
                                        <i id="icon-module-{{ $module->id }}" class="fas fa-chevron-down text-slate-400 transition-transform duration-300"></i>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Contenu du module (collapsable) -->
                            <div id="module-{{ $module->id }}" class="hidden border-t border-slate-100 rounded-b-lg">
                                <div class="p-4 bg-slate-50/50">
                                    @php
                                        // Récupérer les réponses pour ce module
                                        $moduleQuestions = $module->diagnosticquestions->pluck('id');
                                        $moduleResults = $diagnostic->diagnosticresultats
                                            ->whereIn('diagnosticquestion_id', $moduleQuestions)
                                            ->groupBy('diagnosticquestion_id');
                                    @endphp
                                    
                                    @foreach($module->diagnosticquestions as $question)
                                        <div class="border-l-4 border-[#4FBE96] pl-4 mb-4 last:mb-0">
                                            <div class="font-medium text-slate-700 mb-2">
                                                <span class="inline-block w-6 h-6 bg-[#4FBE96]/10 rounded-full text-center text-sm text-[#4FBE96] font-semibold mr-2">
                                                    {{ $question->position }}
                                                </span>
                                                {{ $question->titre }}
                                                @if($question->obligatoire)
                                                    <span class="text-red-500 ml-1">*</span>
                                                @endif
                                            </div>
                                            
                                            @if(isset($moduleResults[$question->id]))
                                                @foreach($moduleResults[$question->id] as $result)
                                                    @php
                                                        $reponse = $result->diagnosticreponse;
                                                    @endphp
                                                    <div class="bg-white rounded-lg p-3 mb-2 border border-slate-200">
                                                        <div class="flex items-start justify-between">
                                                            <div class="flex-1">
                                                                <div class="flex items-start">
                                                                    <i class="fas fa-check-circle text-green-500 mt-1 mr-2"></i>
                                                                    <div class="flex-1">
                                                                        <span class="font-medium text-slate-800">{{ $reponse->titre }}</span>
                                                                        @if($reponse->explication)
                                                                            <div class="mt-2 text-sm text-slate-600 italic bg-slate-50 p-2 rounded">
                                                                                <i class="fas fa-info-circle text-slate-400 mr-1"></i>
                                                                                {{ $reponse->explication }}
                                                                            </div>
                                                                        @endif
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="ml-4 text-right">
                                                                <div class="bg-[#4FBE96] text-white px-2 py-1 rounded text-sm font-semibold">
                                                                    {{ $reponse->score ?? 0 }} pts
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            @else
                                                <div class="bg-gray-50 rounded-lg p-3 border border-gray-200">
                                                    <div class="flex items-center text-slate-500 italic">
                                                        <i class="fas fa-times-circle text-gray-400 mr-2"></i>
                                                        Non répondu
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
            
            <h1 class="text-2xl font-bold text-[#4FBE96]">Diagnostic enregistré avec succès !</h1>
            <p class="text-gray-600 mt-2">Votre accompagnement a été créé. Vous pouvez maintenant consulter et gérer vos plans d'action.</p>
            
            <div class="mt-6 space-x-4">
                <a href="{{ route('diagnostic.select.category') }}" class="inline-block btn bg-gray-500 text-white hover:bg-gray-600">
                    <i class="fas fa-arrow-left mr-2"></i>Refaire un diagnostic
                </a>
                @if(isset($diagnostic))
                    <a href="{{ route('diagnostic.plans', $diagnostic->id) }}" class="inline-block btn bg-[#152737] text-white hover:bg-[#152737]/90">
                        <i class="fas fa-tasks mr-2"></i>Voir les plans d'accompagnement
                    </a>
                @endif
            </div>
        </div>

              </div>
            </div>
            
          </div> 

          </div> 

            <div class="col-span-12 py-6 lg:sticky lg:bottom-0 lg:col-span-4 lg:self-end">
                @include('layouts.sidebar')
            </div>    

        </div>
      </main>
</x-app-layout>

<script>
function toggleModule(moduleId) {
    const element = document.getElementById(moduleId);
    const icon = document.getElementById('icon-' + moduleId);
    
    if (element.classList.contains('hidden')) {
        element.classList.remove('hidden');
        icon.classList.remove('fa-chevron-right');
        icon.classList.add('fa-chevron-down');
    } else {
        element.classList.add('hidden');
        icon.classList.remove('fa-chevron-down');
        icon.classList.add('fa-chevron-right');
    }
}

// Initialiser tous les modules comme fermés au chargement
document.addEventListener('DOMContentLoaded', function() {
    // Initialiser tous les modules comme fermés au chargement
    const modules = document.querySelectorAll('[id^="module-"]');
    modules.forEach(function(module) {
        if (module.id.startsWith('module-')) {
            module.classList.add('hidden');
            const icon = document.getElementById('icon-' + module.id);
            if (icon) {
                icon.classList.remove('fa-chevron-down');
                icon.classList.add('fa-chevron-right');
            }
        }
    });
});
</script>