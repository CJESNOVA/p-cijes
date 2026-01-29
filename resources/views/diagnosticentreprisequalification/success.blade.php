<x-app-layout title="Test de qualification terminé" is-sidebar-open="true" is-header-blur="true">
    <main class="main-content w-full px-[var(--margin-x)] pb-8">
        <div class="flex items-center space-x-4 py-5 lg:py-6">
          <h2
            class="text-xl font-medium text-slate-800 dark:text-navy-50 lg:text-2xl"
          >
            Test de qualification terminé
          </h2>
          <div class="hidden h-full py-1 sm:flex">
            <div class="h-full w-px bg-slate-300 dark:bg-navy-600"></div>
          </div>
        </div>
        <div class="grid grid-cols-12 lg:gap-6">
            <div class="col-span-12 pt-6 lg:col-span-8 lg:pb-6">
              
                @if(session('success'))
                    <div class="alert flex rounded-lg bg-[#4FBE96] px-4 py-3 text-white mb-4 shadow-lg">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <div>
                            <h3 class="font-semibold text-lg mb-1">Succès !</h3>
                            <p>{{ session('success') }}</p>
                        </div>
                    </div>
                @endif

                <div class="card mt-6">
                    <div class="card-body text-center py-12">
                        <div class="w-20 h-20 bg-[#4FBE96]/10 rounded-full flex items-center justify-center mx-auto mb-6">
                            <i class="fas fa-trophy text-3xl text-[#4FBE96]"></i>
                        </div>
                        <h3 class="text-2xl font-bold text-slate-800 dark:text-navy-100 mb-4">
                            Test de qualification terminé avec succès
                        </h3>
                        <p class="text-slate-600 dark:text-navy-400 mb-8 max-w-md mx-auto">
                            Votre test de qualification a été soumis avec succès. Voici le récapitulatif de vos réponses :
                        </p>
                        
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
                            
                            <div class="bg-gradient-to-br from-[#152737]/10 to-[#152737]/20 dark:from-[#152737]/20 dark:to-[#152737]/30 rounded-lg p-4 border border-[#152737]/30 dark:border-[#152737]/70">
                                <div class="text-center">
                                    <h4 class="text-sm font-semibold text-[#152737] dark:text-[#152737]/80 mb-1">
                                        Réponses B
                                    </h4>
                                    <div class="text-2xl font-bold text-[#152737] dark:text-[#152737]/90">
                                        {{ $countB ?? 0 }}
                                    </div>
                                </div>
                            </div>
                            
                            <div class="bg-gradient-to-br from-[#4FBE96]/10 to-[#4FBE96]/20 dark:from-[#4FBE96]/20 dark:to-[#4FBE96]/30 rounded-lg p-4 border border-[#4FBE96]/30 dark:border-[#4FBE96]/70">
                                <div class="text-center">
                                    <h4 class="text-sm font-semibold text-[#4FBE96] dark:text-[#4FBE96]/80 mb-1">
                                        Réponses C
                                    </h4>
                                    <div class="text-2xl font-bold text-[#4FBE96] dark:text-[#4FBE96]/90">
                                        {{ $countC ?? 0 }}
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Réponse majoritaire et progression -->
                        <div class="bg-gradient-to-br from-[#4FBE96]/10 to-[#4FBE96]/20 dark:from-[#4FBE96]/5 dark:to-[#4FBE96]/10 rounded-lg p-6 mb-8 max-w-2xl mx-auto border border-[#4FBE96]/20">
                            <div class="text-center">
                                <h4 class="text-lg font-semibold text-[#4FBE96] dark:text-[#4FBE96]/80 mb-4">
                                    Votre profil de qualification
                                </h4>
                                <div class="text-2xl font-bold text-slate-800 dark:text-navy-100 mb-3">
                                    @if(isset($reponseMajoritaire) && $reponseMajoritaire)
                                        <span class="inline-flex items-center px-4 py-2 rounded-full text-lg font-medium
                                            {{ $reponseMajoritaire === 'A' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/20 dark:text-yellow-400' : 
                                               ($reponseMajoritaire === 'B' ? 'bg-[#152737]/10 text-[#152737] dark:bg-[#152737]/20 dark:text-[#152737]/80' : 
                                               'bg-[#4FBE96]/10 text-[#4FBE96] dark:bg-[#4FBE96]/20 dark:text-[#4FBE96]/80') }}">
                                            <i class="{{ $reponseMajoritaire === 'A' ? 'fas fa-seedling' : 
                                                    ($reponseMajoritaire === 'B' ? 'fas fa-chart-line' : 
                                                    'fas fa-trophy') }} mr-2"></i>
                                            Profil {{ $reponseMajoritaire }}
                                        </span>
                                    @else
                                        <span class="text-slate-600 dark:text-navy-400">
                                            <i class="fas fa-question-circle mr-2"></i>Non déterminé
                                        </span>
                                    @endif
                                </div>
                                <p class="text-sm text-slate-600 dark:text-navy-400">
                                    Basé sur {{ ($countA ?? 0) + ($countB ?? 0) + ($countC ?? 0) }} réponses au total
                                </p>
                            </div>
                        </div>
                        
                        <!-- Profil de l'entreprise -->
                        <div class="bg-gradient-to-br from-success/10 to-success/20 dark:from-success/5 dark:to-success/10 rounded-lg p-6 mb-8 max-w-2xl mx-auto border border-success/20">
                            <div class="text-center">
                                <h4 class="text-lg font-semibold text-success dark:text-success-light mb-2">
                                    Niveau de l'entreprise
                                </h4>
                                <div class="text-xl font-bold text-slate-800 dark:text-navy-100">
                                    @if(isset($entreprise) && $entreprise->entrepriseprofil_id)
                                        @switch($entreprise->entrepriseprofil_id)
                                            @case(1)
                                                <span class="text-yellow-600 dark:text-yellow-400">
                                                    <i class="fas fa-seedling mr-2"></i>{{ $entreprise->entrepriseprofil->titre ?? 'Débutant' }}
                                                </span>
                                                @break
                                            @case(2)
                                                <span class="text-[#152737] dark:text-[#152737]/80">
                                                    <i class="fas fa-chart-line mr-2"></i>{{ $entreprise->entrepriseprofil->titre ?? 'Intermédiaire' }}
                                                </span>
                                                @break
                                            @case(3)
                                                <span class="text-[#4FBE96] dark:text-[#4FBE96]/80">
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
                        @if(isset($diagnostic))
                            @php
                                // Récupérer tous les résultats avec les détails
                                $resultats = \App\Models\Diagnosticresultat::where('diagnostic_id', $diagnostic->id)
                                    ->with(['diagnosticquestion', 'diagnosticreponse'])
                                    ->get()
                                    ->groupBy('diagnosticquestion.diagnosticmodule_id');
                            @endphp
                            
                            <div class="space-y-8 mb-8">
                                @foreach($resultats as $moduleId => $reponsesModule)
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
                                                            <div class="text-lg font-bold {{ $reponse->diagnosticreponse->score === 'A' ? 'text-yellow-600 dark:text-yellow-400' : ($reponse->diagnosticreponse->score === 'B' ? 'text-[#152737] dark:text-[#152737]/80' : 'text-[#4FBE96] dark:text-[#4FBE96]/80') }}">
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
                        @endif
                        
                        <div class="flex flex-col sm:flex-row gap-4 justify-center">
                            <a href="{{ route('entreprise.index') }}" 
                               class="btn bg-gradient-to-r from-[#152737] to-[#152737]/80 text-white hover:from-[#152737]/90 hover:to-[#152737]/70 px-6 py-3 rounded-lg shadow-lg hover:shadow-xl transition-all duration-200 transform hover:scale-105">
                                <i class="fas fa-arrow-left mr-2"></i>
                                Retour à mes entreprises
                            </a>
                            <a href="{{ route('diagnosticentreprisequalification.results', $entreprise->id) }}" 
                               class="btn bg-gradient-to-r from-[#4FBE96] to-[#4FBE96]/80 text-white hover:from-[#4FBE96]/90 hover:to-[#4FBE96]/70 px-6 py-3 rounded-lg shadow-lg hover:shadow-xl transition-all duration-200 transform hover:scale-105">
                                <i class="fas fa-chart-bar mr-2"></i>
                                Voir les résultats détaillés
                            </a>
                            <a href="{{ route('diagnosticentreprisequalification.indexForm') }}" 
                               class="btn bg-gradient-to-r from-slate-500 to-slate-500/80 text-white hover:from-slate-600 hover:to-slate-600/70 px-6 py-3 rounded-lg shadow-lg hover:shadow-xl transition-all duration-200 transform hover:scale-105">
                                <i class="fas fa-plus mr-2"></i>
                                Nouveau test
                            </a>
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
