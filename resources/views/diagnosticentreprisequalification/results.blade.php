<x-app-layout title="Résultats du test de qualification" is-sidebar-open="true" is-header-blur="true">
    <main class="main-content w-full px-[var(--margin-x)] pb-8">
        <div class="flex items-center space-x-4 py-5 lg:py-6">
          <h2
            class="text-xl font-medium text-slate-800 dark:text-navy-50 lg:text-2xl"
          >
            Résultats du test de qualification
          </h2>
          <div class="hidden h-full py-1 sm:flex">
            <div class="h-full w-px bg-slate-300 dark:bg-navy-600"></div>
          </div>
        </div>
        <div class="grid grid-cols-12 lg:gap-6">
            <div class="col-span-12 pt-6 lg:col-span-8 lg:pb-6">
              
                @if(session('error'))
                    <div class="alert flex rounded-lg bg-danger px-4 py-4 text-white sm:px-5">{{ session('error') }}</div>
                @endif

                @if(isset($entreprise) && isset($diagnostic))
                    <div class="card mt-6">
                        <div class="card-body">
                            <div class="text-center mb-6">
                                <h3 class="text-2xl font-bold text-slate-800 dark:text-navy-100 mb-4">
                                    Test de qualification - {{ $entreprise->nom }}
                                </h3>
                                <p class="text-slate-600 dark:text-navy-400">
                                    Effectué le {{ $diagnostic->created_at->format('d/m/Y H:i') }}
                                </p>
                            </div>

                            <!-- Répartition des réponses -->
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8 max-w-3xl mx-auto">
                                <div class="bg-gradient-to-br from-yellow-50 to-yellow-100 dark:from-yellow-900/20 dark:to-yellow-800/20 rounded-lg p-4 border border-yellow-200 dark:border-yellow-700">
                                    <div class="text-center">
                                        <h4 class="text-sm font-semibold text-yellow-700 dark:text-yellow-300 mb-1">
                                            Réponses A
                                        </h4>
                                        <div class="text-2xl font-bold text-yellow-600 dark:text-yellow-400">
                                            {{ $countA ?? 0 }}
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="bg-gradient-to-br from-blue-50 to-blue-100 dark:from-blue-900/20 dark:to-blue-800/20 rounded-lg p-4 border border-blue-200 dark:border-blue-700">
                                    <div class="text-center">
                                        <h4 class="text-sm font-semibold text-blue-700 dark:text-blue-300 mb-1">
                                            Réponses B
                                        </h4>
                                        <div class="text-2xl font-bold text-blue-600 dark:text-blue-400">
                                            {{ $countB ?? 0 }}
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="bg-gradient-to-br from-green-50 to-green-100 dark:from-green-900/20 dark:to-green-800/20 rounded-lg p-4 border border-green-200 dark:border-green-700">
                                    <div class="text-center">
                                        <h4 class="text-sm font-semibold text-green-700 dark:text-green-300 mb-1">
                                            Réponses C
                                        </h4>
                                        <div class="text-2xl font-bold text-green-600 dark:text-green-400">
                                            {{ $countC ?? 0 }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Réponse majoritaire -->
                            <!-- <div class="bg-gradient-to-br from-primary/10 to-primary/20 dark:from-primary/5 dark:to-primary/10 rounded-lg p-6 mb-8 max-w-2xl mx-auto border border-primary/20">
                                <div class="text-center">
                                    <h4 class="text-lg font-semibold text-primary dark:text-primary-light mb-2">
                                        Réponse majoritaire
                                    </h4>
                                    <div class="text-2xl font-bold text-slate-800 dark:text-navy-100">
                                        @if(isset($reponseMajoritaire) && $reponseMajoritaire)
                                            <span class="badge badge-lg badge-{{ $reponseMajoritaire === 'A' ? 'warning' : ($reponseMajoritaire === 'B' ? 'info' : 'success') }}">
                                                {{ $reponseMajoritaire }}
                                            </span>
                                        @else
                                            <span class="text-slate-600 dark:text-navy-400">
                                                <i class="fas fa-question-circle mr-2"></i>Non déterminé
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div> -->

                            <!-- Profil de l'entreprise -->
                            <div class="bg-gradient-to-br from-success/10 to-success/20 dark:from-success/5 dark:to-success/10 rounded-lg p-6 mb-8 max-w-2xl mx-auto border border-success/20">
                                <div class="text-center">
                                    <h4 class="text-lg font-semibold text-success dark:text-success-light mb-2">
                                        Niveau de l'entreprise
                                    </h4>
                                    <div class="text-xl font-bold text-slate-800 dark:text-navy-100">
                                        @if(isset($entreprise) && $entreprise->entrepriseprofil)
                                            @switch($entreprise->entrepriseprofil->id)
                                                @case(1)
                                                    <span class="text-yellow-600 dark:text-yellow-400">
                                                        <i class="fas fa-seedling mr-2"></i>{{ $entreprise->entrepriseprofil->titre ?? 'Débutant' }}
                                                    </span>
                                                    @break
                                                @case(2)
                                                    <span class="text-blue-600 dark:text-blue-400">
                                                        <i class="fas fa-chart-line mr-2"></i>{{ $entreprise->entrepriseprofil->titre ?? 'Intermédiaire' }}
                                                    </span>
                                                    @break
                                                @case(3)
                                                    <span class="text-green-600 dark:text-green-400">
                                                        <i class="fas fa-trophy mr-2"></i>{{ $entreprise->entrepriseprofil->titre ?? 'Avancé' }}
                                                    </span>
                                                    @break
                                                @default
                                                    <span class="text-slate-600 dark:text-navy-400">
                                                        <i class="fas fa-question-circle mr-2"></i>Non évalué
                                                    </span>
                                            @endswitch
                                        @else
                                            <span class="text-slate-600 dark:text-navy-400">
                                                <i class="fas fa-question-circle mr-2"></i>Non évalué
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <!-- Récapitulatif des résultats -->
                            <div class="space-y-8">
                                @php
                                    $resultatsGroupes = $resultats->groupBy('diagnosticquestion.diagnosticmodule_id');
                                @endphp
                                
                                @foreach($resultatsGroupes as $moduleId => $reponsesModule)
                                    @php
                                        $module = \App\Models\Diagnosticmodule::find($moduleId);
                                    @endphp
                                    
                                    <div class="bg-slate-50 dark:bg-navy-800 rounded-lg p-6 border border-slate-200 dark:border-navy-700">
                                        <h4 class="text-lg font-bold text-slate-800 dark:text-navy-100 mb-4">
                                            {{ $module->titre }}
                                        </h4>
                                        
                                        <div class="space-y-4">
                                            @foreach($reponsesModule as $reponse)
                                                <div class="bg-white dark:bg-navy-700 rounded-lg p-4 border border-slate-200 dark:border-navy-600">
                                                    <div class="flex justify-between items-start">
                                                        <div class="flex-1">
                                                            <h5 class="font-semibold text-slate-700 dark:text-navy-200 mb-2">
                                                                {{ $reponse->diagnosticquestion->titre }}
                                                            </h5>
                                                            <p class="text-sm text-slate-600 dark:text-navy-400">
                                                                <strong>Votre réponse :</strong> {{ $reponse->diagnosticreponse->titre }}
                                                            </p>
                                                        </div>
                                                        
                                                        <div class="ml-4 text-right">
                                                            <div class="text-sm text-slate-500 dark:text-navy-400 mb-1">
                                                                Réponse
                                                            </div>
                                                            <div class="text-lg font-bold {{ $reponse->diagnosticreponse->score === 'A' ? 'text-yellow-600 dark:text-yellow-400' : ($reponse->diagnosticreponse->score === 'B' ? 'text-blue-600 dark:text-blue-400' : 'text-green-600 dark:text-green-400') }}">
                                                                {{ $reponse->diagnosticreponse->score ?? '-' }}
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <div class="flex flex-col sm:flex-row gap-4 justify-center mt-8">
                                <a href="{{ route('entreprise.index') }}" 
                                   class="btn bg-primary text-white hover:bg-primary-focus px-6 py-3">
                                    <i class="fas fa-arrow-left mr-2"></i>
                                    Retour à mes entreprises
                                </a>
                                <a href="{{ route('diagnosticentreprisequalification.showForm', $entreprise->id) }}" 
                                   class="btn bg-info text-white hover:bg-info-focus px-6 py-3">
                                    <i class="fas fa-redo mr-2"></i>
                                    Nouveau test
                                </a>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="card mt-6">
                        <div class="card-body text-center py-12">
                            <i class="fas fa-exclamation-triangle text-6xl text-warning mb-6"></i>
                            <h3 class="text-2xl font-bold text-slate-800 dark:text-navy-100 mb-4">
                                Aucun résultat trouvé
                            </h3>
                            <p class="text-slate-600 dark:text-navy-400 mb-8">
                                Aucun test de qualification terminé n'a été trouvé pour cette entreprise.
                            </p>
                            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                                <a href="{{ route('entreprise.index') }}" 
                                   class="btn bg-primary text-white hover:bg-primary-focus px-6 py-3">
                                    <i class="fas fa-arrow-left mr-2"></i>
                                    Retour à mes entreprises
                                </a>
                                <a href="{{ route('diagnosticentreprisequalification.showForm', $entreprise->id) }}" 
                                   class="btn bg-warning text-white hover:bg-warning-focus px-6 py-3">
                                    <i class="fas fa-clipboard-check mr-2"></i>
                                    Commencer un test
                                </a>
                            </div>
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
