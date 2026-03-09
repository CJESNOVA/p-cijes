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
                        Consultez les résultats de votre évaluation entreprise
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
                                <strong>Entreprise :</strong> {{ $diagnostic->entreprise->nom ?? 'N/A' }}
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

                    <!-- Résultats par module -->
                    <h3 class="text-xl font-semibold text-slate-800 mb-4">Détail par module</h3>
                    
                    @php
                        // Récupérer les modules qui ont des résultats
                        $modulesWithResults = $diagnostic->diagnosticresultats
                            ->filter(function($result) {
                                return $result->diagnosticquestion;
                            })
                            ->map(function($result) {
                                return $result->diagnosticquestion->diagnosticmodule;
                            })
                            ->unique('id')
                            ->sortBy('id'); // Tri numérique par ID
                    @endphp
                    
                    @foreach($modulesWithResults as $module)
                        @php
                            // Calculer les questions pour ce module (avant de les utiliser)
                            $moduleQuestions = $diagnostic->diagnosticresultats
                                ->filter(function($result) use ($module) {
                                    return $result->diagnosticquestion && $result->diagnosticquestion->diagnosticmodule_id == $module->id;
                                })
                                ->map(function($result) {
                                    return $result->diagnosticquestion;
                                })
                                ->unique('id')
                                ->sortBy(function($question) {
                                    return (int)$question->position;
                                });
                                
                            $moduleResults = $diagnostic->diagnosticresultats
                                ->whereIn('diagnosticquestion_id', $moduleQuestions->pluck('id'))
                                ->groupBy('diagnosticquestion_id');
                        @endphp
                        
                        <div class="bg-gradient-to-r from-[#4FBE96]/10 to-[#4FBE96]/5 border border-[#4FBE96]/30 rounded-lg p-4 mb-4 shadow-sm hover:shadow-md transition-shadow duration-200">
                            <!-- Header du module cliquable -->
                            <div class="cursor-pointer flex items-center justify-between p-2 -m-2 rounded-lg hover:bg-[#4FBE96]/5 transition-colors duration-200" onclick="toggleModule('module-{{ $module->id }}')">
                                <h4 class="text-lg font-semibold text-[#152737] mb-0 flex items-center">
                                    <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-[#4FBE96] to-[#4FBE96]/80 flex items-center justify-center mr-3">
                                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                    </div>
                                    {{ $module->titre }}
                                </h4>
                                <div class="flex items-center space-x-2">
                                    <span class="text-sm font-medium text-[#4FBE96] bg-[#4FBE96]/10 px-2 py-1 rounded-full">
                                        {{ $moduleQuestions->count() }} question(s)
                                    </span>
                                    <svg id="chevron-module-{{ $module->id }}" class="w-5 h-5 text-[#4FBE96] transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                </div>
                            </div>
                            
                            <!-- Contenu du module (collapsible) -->
                            <div id="module-{{ $module->id }}" class="mt-4 pl-11">
                            
                            @foreach($moduleQuestions as $question)
                                <div class="bg-white rounded-lg border border-[#4FBE96]/20 p-4 mb-4 shadow-sm">
                                    <div class="font-semibold text-[#152737] mb-3 flex items-center">
                                        <span class="w-6 h-6 rounded-full bg-[#4FBE96]/20 text-[#4FBE96] text-sm font-bold flex items-center justify-center mr-2">
                                            {{ $question->position }}
                                        </span>
                                        {{ $question->titre }}
                                        @if($question->obligatoire)
                                            <span class="text-red-500 ml-2 text-sm font-medium">*</span>
                                        @endif
                                    </div>
                                    
                                    @if(isset($moduleResults[$question->id]))
                                        @foreach($moduleResults[$question->id] as $result)
                                            @if($result->diagnosticreponse)
                                                <div class="bg-gradient-to-r from-[#4FBE96]/5 to-[#4FBE96]/10 rounded-lg p-3 border border-[#4FBE96]/20">
                                                    <div class="flex items-start justify-between">
                                                        <div class="flex-1">
                                                            <div class="text-slate-700 mb-2">
                                                                {{ $result->diagnosticreponse->titre }} - {{ $result->diagnosticreponse->score }}
                                                            </div>
                                                            
                                                            @if($result->diagnosticreponse->explication)
                                                                <div class="bg-[#4FBE96]/10 rounded p-2 mt-2">
                                                                    <div class="text-sm text-slate-600">
                                                                        {{ $result->diagnosticreponse->explication }}
                                                                    </div>
                                                                </div>
                                                            @endif
                                                        </div>
                                                        
                                                        
                                                    </div>
                                                </div>
                                            @endif
                                        @endforeach
                                    @else
                                        <div class="bg-red-50 border border-red-200 rounded-lg p-3">
                                            <div class="text-red-600 font-medium flex items-center">
                                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                                Non répondu
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
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

    <script>
        function toggleModule(moduleId) {
            const content = document.getElementById(moduleId);
            const chevron = document.getElementById('chevron-' + moduleId);
            
            if (content.style.display === 'none') {
                content.style.display = 'block';
                chevron.style.transform = 'rotate(0deg)';
            } else {
                content.style.display = 'none';
                chevron.style.transform = 'rotate(-90deg)';
            }
        }
        
        // Initialiser tous les modules comme fermés
        document.addEventListener('DOMContentLoaded', function() {
            @foreach($modulesWithResults as $module)
                const module{{ $module->id }} = document.getElementById('module-{{ $module->id }}');
                const chevron{{ $module->id }} = document.getElementById('chevron-module-{{ $module->id }}');
                if (module{{ $module->id }}) {
                    module{{ $module->id }}.style.display = 'none';
                    chevron{{ $module->id }}.style.transform = 'rotate(-90deg)';
                }
            @endforeach
        });
    </script>
</x-app-layout>