<x-app-layout title="Progression Entreprise" is-sidebar-open="true" is-header-blur="true">
    <main class="main-content w-full px-[var(--margin-x)] pb-8">
        <!-- Header moderne -->
        <div class="mb-8">
            <div class="flex items-center gap-4 mb-4">
                <div class="h-16 w-16 rounded-xl bg-gradient-to-br from-purple-500 to-indigo-600 flex items-center justify-center shadow-lg">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                </div>
                <div>
                    <h1 class="text-3xl font-bold text-slate-800 dark:text-navy-50">
                        Ma Progression
                    </h1>
                    <p class="mt-2 text-slate-600 dark:text-navy-200 text-lg">
                        Suivez l'évolution de votre entreprise dans le temps
                    </p>
                    <div class="mt-3 flex items-center gap-3">
                        <span class="text-sm text-slate-500">Entreprise:</span>
                        <span class="px-3 py-1 bg-purple-500/10 text-purple-600 rounded-full text-sm font-medium">
                            {{ $entreprise->nom }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-12 lg:gap-6">
            <!-- Colonne principale -->
            <div class="col-span-12 lg:col-span-8 space-y-6">
                
                <!-- Timeline de progression avec les nouvelles évolutions -->
                <div class="bg-white dark:bg-navy-800 rounded-xl shadow-lg p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-xl font-semibold text-slate-800 dark:text-navy-50">
                            Historique de Progression
                        </h2>
                    </div>
                    
                    @include('evolutions.evolutions-timeline', ['evolutions' => $evolutions ?? collect()])
                </div>

                <!-- Graphique d'Évolution -->
                @if($scoresEvolution && count($scoresEvolution) > 2)
                    <div class="bg-white dark:bg-navy-800 rounded-xl shadow-lg p-6">
                        <h2 class="text-xl font-semibold text-slate-800 dark:text-navy-50 mb-6">
                            Évolution des Scores
                        </h2>
                        
                        <div class="h-64">
                            <canvas id="scoresChart"></canvas>
                        </div>
                        
                        <div class="mt-4 grid grid-cols-2 md:grid-cols-4 gap-4">
                            <div class="text-center">
                                <div class="text-2xl font-bold text-slate-800 dark:text-navy-50">
                                    {{ $derniersScoresEvolution->first()['score'] }}
                                </div>
                                <div class="text-sm text-slate-500">Score initial</div>
                            </div>
                            <div class="text-center">
                                <div class="text-2xl font-bold text-green-600">
                                    {{ $derniersScoresEvolution->last()['score'] }}
                                </div>
                                <div class="text-sm text-slate-500">Score actuel</div>
                            </div>
                            <div class="text-center">
                                <div class="text-2xl font-bold text-blue-600">
                                    {{ $derniersScoresEvolution->last()['score'] - $derniersScoresEvolution->first()['score'] }}
                                </div>
                                <div class="text-sm text-slate-500">Progression</div>
                            </div>
                            <div class="text-center">
                                <div class="text-2xl font-bold text-purple-600">
                                    {{ $derniersScoresEvolution->count() }}
                                </div>
                                <div class="text-sm text-slate-500">Diagnostics</div>
                            </div>
                        </div>
                    </div>
                @endif


            <!-- Colonne latérale -->
            <div class="col-span-6 lg:col-span-4 space-y-6">

                <!-- Objectifs -->
                <div class="bg-white dark:bg-navy-800 rounded-xl shadow-lg p-6">
                    <h3 class="text-lg font-semibold text-slate-800 dark:text-navy-50 mb-4">
                        
                    </h3>
                    
                    @if($entreprise->entrepriseprofil_id == 1) <!-- PÉPITE -->
                        <div class="space-y-4">
                            <div class="p-4 rounded-lg bg-blue-50 dark:bg-navy-700">
                                <h4 class="font-medium text-blue-800 dark:text-blue-300 mb-2">
                                    Passer au profil ÉMERGENTE
                                </h4>
                                <div class="space-y-2 text-sm text-blue-700 dark:text-blue-400">
                                    <div class="flex items-center justify-between">
                                        <span>Score global</span>
                                        <span class="font-medium">{{ $scoreGlobal ?? 0 }}/160</span>
                                    </div>
                                    <div class="w-full h-2 bg-blue-200 rounded-full overflow-hidden">
                                        <div class="h-full bg-blue-500" style="width: {{ min((($scoreGlobal ?? 0) / 160) * 100, 100) }}%"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @elseif($entreprise->entrepriseprofil_id == 2) <!-- ÉMERGENTE -->
                        <div class="space-y-4">
                            <div class="p-4 rounded-lg bg-purple-50 dark:bg-navy-700">
                                <h4 class="font-medium text-purple-800 dark:text-purple-300 mb-2">
                                    Passer au profil ÉLITE
                                </h4>
                                <div class="space-y-2 text-sm text-purple-700 dark:text-purple-400">
                                    <div class="flex items-center justify-between">
                                        <span>Score global</span>
                                        <span class="font-medium">{{ $scoreGlobal ?? 0 }}/180</span>
                                    </div>
                                    <div class="w-full h-2 bg-purple-200 rounded-full overflow-hidden">
                                        <div class="h-full bg-purple-500" style="width: {{ min((($scoreGlobal ?? 0) / 180) * 100, 100) }}%"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @else <!-- ÉLITE -->
                        <div class="space-y-4">
                            <div class="p-4 rounded-lg bg-green-50 dark:bg-navy-700">
                                <h4 class="font-medium text-green-800 dark:text-green-300 mb-2">
                                    Maintenir l'excellence
                                </h4>
                                <p class="text-sm text-green-700 dark:text-green-400">
                                    Continuez sur cette voie remarquable !
                                </p>
                            </div>
                        </div>
                    @endif
                </div>
                </div>

            <!-- Deux colonnes pour Actions et  -->
            <div class="col-span-6 lg:col-span-4 space-y-6">
                <div class="grid grid-cols-12 gap-4">
                    <!-- Colonne Actions -->
                    <div class="col-span-12 lg:col-span-6">
                        <div class="bg-white dark:bg-navy-800 rounded-xl shadow-lg p-6">
                            <h3 class="text-lg font-semibold text-slate-800 dark:text-navy-50 mb-4">
                                Actions
                            </h3>
                            
                            <div class="space-y-3">
                                <a href="{{ route('diagnosticentreprise.indexForm') }}" 
                                   class="btn w-full bg-primary text-white hover:bg-primary-focus">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                    Nouveau Diagnostic
                                </a>
                                
                                {{--                                
                                <a href="{{ route('entreprise.profil.show', $entreprise->id) }}" 
                                   class="btn w-full bg-purple-500 text-white hover:bg-purple-600">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                    Voir Profil
                                </a>
--}}
                                
                                {{--                                
                                <a href="{{ route('entreprise.orientations.index', $entreprise->id) }}" 
                                   class="btn w-full bg-green-500 text-white hover:bg-green-600">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                    </svg>
                                    Mes Orientations
                                </a>
--}}
                            </div>
                        </div>
                    </div>

                    <!-- Colonne Prochain objectif -->
                    <div class="col-span-12 lg:col-span-6">
                        <!-- Objectifs -->
                        <div class="bg-white dark:bg-navy-800 rounded-xl shadow-lg p-6">
                            <h3 class="text-lg font-semibold text-slate-800 dark:text-navy-50 mb-4">
                                Prochain Objectif
                            </h3>
                            
                            @if($regleProchainObjectif)
                                @php
                                    $profilActuelId = $entreprise->entrepriseprofil_id ?? 1;
                                    $profilSuivantNom = $profilActuelId == 1 ? 'ÉMERGENTE' : 'ÉLITE';
                                    $couleurProfil = $profilActuelId == 1 ? 'blue' : 'purple';
                                @endphp
                                
                                <div class="space-y-3">
                                    <div class="p-3 rounded-lg bg-{{ $couleurProfil }}-50 dark:bg-navy-700">
                                        <h4 class="font-medium text-{{ $couleurProfil }}-800 dark:text-{{ $couleurProfil }}-300 mb-2">
                                            Atteindre le niveau {{ $profilSuivantNom }}
                                        </h4>
                                        <ul class="text-sm text-{{ $couleurProfil }}-700 dark:text-{{ $couleurProfil }}-400 space-y-1">
                                            @if($regleProchainObjectif->score_total_min)
                                                <li>• Score global ≥ {{ $regleProchainObjectif->score_total_min }}</li>
                                            @endif
                                            
                                            @if($regleProchainObjectif->min_blocs_score)
                                                <li>• {{ $regleProchainObjectif->min_blocs_score }}+ blocs avec score > 0</li>
                                            @endif
                                            
                                            @if($regleProchainObjectif->min_score_bloc)
                                                <li>• Tous les blocs ≥ {{ $regleProchainObjectif->min_score_bloc }}</li>
                                            @endif
                                            
                                            @if($regleProchainObjectif->bloc_finance_min)
                                                <li>• Finance ≥ {{ $regleProchainObjectif->bloc_finance_min }}</li>
                                            @endif
                                            
                                            @if($regleProchainObjectif->bloc_juridique_min)
                                                <li>• Juridique ≥ {{ $regleProchainObjectif->bloc_juridique_min }}</li>
                                            @endif
                                            
                                            @if($regleProchainObjectif->aucun_bloc_inf)
                                                <li>• Aucun bloc < {{ $regleProchainObjectif->aucun_bloc_inf }}</li>
                                            @endif
                                        </ul>
                                    </div>
                                </div>
                            @else
                                <div class="space-y-3">
                                    <div class="p-3 rounded-lg bg-green-50 dark:bg-navy-700">
                                        <h4 class="font-medium text-green-800 dark:text-green-300 mb-2">
                                            Maintenir l'excellence
                                        </h4>
                                        <p class="text-sm text-green-700 dark:text-green-400">
                                            Continuez sur cette voie remarquable !
                                        </p>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    
    @if($scoresEvolution && count($scoresEvolution) > 1)
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const ctx = document.getElementById('scoresChart').getContext('2d');
                
                const scoresChart = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: @json($derniersScoresEvolution->pluck('date')->toArray()),
                        datasets: [{
                            label: 'Score Global - 5 derniers diagnostics',
                            data: @json($derniersScoresEvolution->pluck('score')->toArray()),
                            borderColor: 'rgb(147, 51, 234)',
                            backgroundColor: 'rgba(147, 51, 234, 0.1)',
                            tension: 0.4,
                            fill: true
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                max: 200
                            }
                        }
                    }
                });
            });
        </script>
    @endif

            <div class="col-span-12 py-6 lg:sticky lg:bottom-0 lg:col-span-4 lg:self-end">
                @include('layouts.sidebar')
            </div>    

        </div>
    </main>
</x-app-layout>
