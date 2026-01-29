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
                                <strong>Score global :</strong> {{ $diagnostic->scoreglobal ?? 0 }}%
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
                    
                    @foreach($modules as $module)
                        <div class="bg-white border border-slate-200 rounded-lg p-4 mb-4">
                            <h4 class="text-lg font-semibold text-slate-800 mb-3">
                                {{ $module->titre }}
                            </h4>
                            
                            @php
                                // Récupérer les réponses pour ce module
                                $moduleQuestions = $module->diagnosticquestions->pluck('id');
                                $moduleResults = $diagnostic->diagnosticresultats
                                    ->whereIn('diagnosticquestion_id', $moduleQuestions)
                                    ->groupBy('diagnosticquestion_id');
                            @endphp
                            
                            @foreach($module->diagnosticquestions as $question)
                                <div class="border-l-4 border-[#4FBE96] pl-4 mb-4">
                                    <div class="font-medium text-slate-700 mb-2">
                                        {{ $question->position }} - {{ $question->titre }}
                                        @if($question->obligatoire)
                                            <span class="text-red-500 ml-1">*</span>
                                        @endif
                                    </div>
                                    
                                    @if(isset($moduleResults[$question->id]))
                                        @foreach($moduleResults[$question->id] as $result)
                                            @php
                                                $reponse = $result->diagnosticreponse;
                                            @endphp
                                            <div class="bg-[#4FBE96]/10 rounded p-3 mb-2">
                                                <div class="flex items-center justify-between">
                                                    <div>
                                                        <span class="font-medium">{{ $reponse->titre }}</span>
                                                        <span class="ml-2 text-sm text-slate-600">
                                                            (Score: {{ $reponse->score ?? 0 }})
                                                        </span>
                                                    </div>
                                                    <div class="text-[#4FBE96] font-semibold">
                                                        {{ $reponse->score ?? 0 }} pts
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    @else
                                        <div class="bg-gray-50 rounded p-3 text-slate-500 italic">
                                            Non répondu
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @endforeach
                </div>
            @endif
            
            <h1 class="text-2xl font-bold text-[#4FBE96]">Diagnostic enregistré avec succès !</h1>
            <p class="text-gray-600 mt-2">Votre accompagnement d'entreprise a été créé. Vous pouvez maintenant consulter et gérer vos plans d'action.</p>
            
            <div class="mt-6 space-x-4">
                <a href="{{ route('diagnosticentreprise.indexForm') }}" class="inline-block btn bg-gray-500 text-white hover:bg-gray-600">
                    <i class="fas fa-arrow-left mr-2"></i>Retour aux diagnostics
                </a>
                @if(isset($diagnostic))
                    <a href="{{ route('diagnosticentreprise.plans', $diagnostic->id) }}" class="inline-block btn bg-[#152737] text-white hover:bg-[#152737]/90">
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