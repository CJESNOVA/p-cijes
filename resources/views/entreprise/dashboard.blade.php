<x-app-layout title="Dashboard Entreprise" is-sidebar-open="true" is-header-blur="true">
    <main class="main-content w-full px-[var(--margin-x)] pb-8">
        <!-- Header moderne -->
        <div class="mb-8">
            <div class="flex items-center gap-4 mb-4">
                <div class="h-16 w-16 rounded-xl bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center shadow-lg">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h-1m2-5h-8"></path>
                    </svg>
                </div>
                <div>
                    <h1 class="text-3xl font-bold text-slate-800 dark:text-navy-50">
                        Dashboard Entreprise
                    </h1>
                    <p class="mt-2 text-slate-600 dark:text-navy-200 text-lg">
                        Suivez votre progression et vos orientations
                    </p>
                    <div class="mt-3 flex items-center gap-3">
                        <span class="text-sm text-slate-500">Entreprise:</span>
                        <span class="px-3 py-1 bg-blue-500/10 text-blue-600 rounded-full text-sm font-medium">
                            {{ $entreprise->nom }}
                        </span>
                        @if($dernierDiagnostic)
                            <span class="text-sm text-slate-500">
                                Dernier diagnostic: {{ $dernierDiagnostic->created_at->format('d/m/Y') }}
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Messages -->
        @if(session('success'))
            <div class="alert flex rounded-lg bg-green-500 px-6 py-4 text-white mb-6 shadow-lg">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="alert flex rounded-lg bg-red-500 px-6 py-4 text-white mb-6 shadow-lg">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                {{ session('error') }}
            </div>
        @endif

        <div class="grid grid-cols-12 lg:gap-6">
            <!-- Colonne principale -->
            <div class="col-span-12 lg:col-span-8 space-y-6">
                
                <!-- Profil Actuel -->
                <div class="bg-white dark:bg-navy-800 rounded-xl shadow-lg p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-xl font-semibold text-slate-800 dark:text-navy-50">
                            Profil Actuel
                        </h2>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <!-- Badge de profil -->
                        <div class="text-center">
                            @php
                                $profilColors = [
                                    1 => 'from-yellow-400 to-orange-500', // PÉPITE
                                    2 => 'from-blue-400 to-blue-600',   // ÉMERGENTE
                                    3 => 'from-purple-400 to-purple-600' // ÉLITE
                                ];
                                $profilIcons = [
                                    1 => 'M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z',
                                    2 => 'M13 10V3L4 14h7v7l9-11h-7z',
                                    3 => 'M5 3v14M14 3v14M9 9h6M9 15h6'
                                ];
                                $profilId = $entreprise->entrepriseprofil_id ?? 1;
                                $profil = \App\Models\Entrepriseprofil::find($profilId);
                            @endphp
                            <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-gradient-to-br {{ $profilColors[$profilId] }} shadow-lg mb-3">
                                <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $profilIcons[$profilId] }}"></path>
                                </svg>
                            </div>
                            <h3 class="text-lg font-bold text-slate-800 dark:text-navy-50">
                                {{ $profil->nom ?? 'PÉPITE' }}
                            </h3>
                            <p class="text-sm text-slate-500 dark:text-navy-300 mt-1">
                                {{ $profil->description ?? 'Entreprise en phase de structuration' }}
                            </p>
                        </div>

                        <!-- Score Global -->
                        <div class="text-center">
                            <div class="relative inline-flex items-center justify-center w-20 h-20 mb-3">
                                <svg class="w-20 h-20 transform -rotate-90">
                                    <circle cx="40" cy="40" r="36" stroke="currentColor" stroke-width="8" fill="none" class="text-slate-200 dark:text-navy-600"></circle>
                                    <circle cx="40" cy="40" r="36" stroke="currentColor" stroke-width="8" fill="none" 
                                            class="text-blue-500" stroke-dasharray="{{ ($scoreGlobal / 200) * 226 }} 226"></circle>
                                </svg>
                                <span class="absolute text-2xl font-bold text-slate-800 dark:text-navy-50">
                                    {{ $scoreGlobal ?? 0 }}
                                </span>
                            </div>
                            <h3 class="text-lg font-bold text-slate-800 dark:text-navy-50">
                                Score Global
                            </h3>
                            <p class="text-sm text-slate-500 dark:text-navy-300 mt-1">
                                sur 200 points
                            </p>
                        </div>

                        <!-- Prochaine Évaluation -->
                        <div class="text-center">
                            <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-slate-100 dark:bg-navy-600 mb-3">
                                <svg class="w-10 h-10 text-slate-500 dark:text-navy-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <h3 class="text-lg font-bold text-slate-800 dark:text-navy-50">
                                Prochaine Évaluation
                            </h3>
                            <p class="text-sm text-slate-500 dark:text-navy-300 mt-1">
                                @if($prochaineEvaluation && $dernierDiagnostic)
                                    <div class="space-y-1">
                                        <div>
                                            <i class="fas fa-calendar-alt mr-1"></i>
                                            Prochaine: {{ $prochaineEvaluation->format('d/m/Y') }}
                                        </div>
                                    </div>
                                    
                                @else
                                    <span class="text-slate-400">
                                        <i class="fas fa-info-circle mr-1"></i>
                                        Aucune évaluation planifiée
                                    </span>
                                @endif
                            </p>
                        </div>
                    </div>
                </div>

                

                <!-- Blocs Critiques -->
                @if($blocsCritiquesCount > 0)
                    <div class="bg-white dark:bg-navy-800 rounded-xl shadow-lg p-6">
                        <h3 class="text-lg font-semibold text-slate-800 dark:text-navy-50 mb-4">
                            Blocs Critiques ({{ $blocsCritiquesCount }})
                        </h3>
                        
                        <div class="space-y-3">
                            @foreach($blocsCritiques as $bloc)
                                <div class="p-3 rounded-lg bg-red-50 dark:bg-navy-700">
                                    <!-- En-tête du bloc -->
                                    <div class="flex items-center justify-between mb-3">
                                        <div class="flex items-center">
                                            <div class="w-8 h-8 rounded-full bg-red-500 flex items-center justify-center mr-3">
                                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                                </svg>
                                            </div>
                                            <div>
                                                <div class="font-medium text-slate-800 dark:text-navy-50">
                                                    {{ $bloc['nom'] }}
                                                </div>
                                                <div class="flex items-center space-x-3 text-sm">
                                                    <span class="text-red-500 font-medium">
                                                        {{ $bloc['pourcentage'] }}%
                                                    </span>
                                                    <span class="text-slate-500">
                                                        {{ $bloc['statut'] }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Orientations recommandées -->
                                    @if(isset($bloc['orientations']) && $bloc['orientations']->isNotEmpty())
                                        <div class="border-t border-red-200 dark:border-red-800 pt-3">
                                            <h4 class="text-sm font-medium text-red-700 dark:text-red-400 mb-2">
                                                Orientations recommandées (Score: {{ $bloc['score'] }}/20)
                                            </h4>
                                            <div class="space-y-2">
                                                @foreach($bloc['orientations'] as $orientation)
                                                    <div class="flex items-start gap-2 p-2 bg-white dark:bg-navy-600 rounded border border-red-200 dark:border-red-800">
                                                        <div class="w-5 h-5 rounded-full bg-green-100 flex items-center justify-center flex-shrink-0 mt-0.5">
                                                            <svg class="w-3 h-3 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                                            </svg>
                                                        </div>
                                                        <div class="flex-1">
                                                            <div class="text-sm font-medium text-slate-700 dark:text-navy-300">
                                                                {{ $orientation->dispositif }}
                                                            </div>
                                                            <div class="text-xs text-slate-500 dark:text-navy-400 mt-1">
                                                                Recommandé pour un score ≤ {{ $orientation->seuil_max }}
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    @else
                                        <div class="border-t border-red-200 dark:border-red-800 pt-3">
                                            <p class="text-sm text-slate-600 dark:text-navy-400 italic">
                                                Aucune orientation spécifique disponible pour ce niveau de score.
                                            </p>
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                        
                    </div>
                @endif

                
                
                
                <!-- Prochaines Étapes -->
                <div class="bg-white dark:bg-navy-800 rounded-xl shadow-lg p-6">
                    <h3 class="text-lg font-semibold text-slate-800 dark:text-navy-50 mb-4">
                        Prochaines Étapes
                    </h3>
                    
                    <div class="space-y-3">
                        @if($entreprise->entrepriseprofil_id == 1) <!-- PÉPITE -->
                            <div class="flex items-start">
                                <div class="w-6 h-6 rounded-full bg-yellow-500 flex items-center justify-center mr-3 mt-0.5">
                                    <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                </div>
                                <div>
                                    <div class="font-medium text-slate-800 dark:text-navy-50 text-sm">
                                        Améliorer les blocs critiques
                                    </div>
                                    <div class="text-xs text-slate-500 dark:text-navy-300">
                                        Atteindre 160+ points pour progresser
                                    </div>
                                </div>
                            </div>
                        @elseif($entreprise->entrepriseprofil_id == 2) <!-- ÉMERGENTE -->
                            <div class="flex items-start">
                                <div class="w-6 h-6 rounded-full bg-blue-500 flex items-center justify-center mr-3 mt-0.5">
                                    <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                </div>
                                <div>
                                    <div class="font-medium text-slate-800 dark:text-navy-50 text-sm">
                                        Maintenir la performance
                                    </div>
                                    <div class="text-xs text-slate-500 dark:text-navy-300">
                                        Viser le profil ÉLITE
                                    </div>
                                </div>
                            </div>
                        @else <!-- ÉLITE -->
                            <div class="flex items-start">
                                <div class="w-6 h-6 rounded-full bg-purple-500 flex items-center justify-center mr-3 mt-0.5">
                                    <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                </div>
                                <div>
                                    <div class="font-medium text-slate-800 dark:text-navy-50 text-sm">
                                        Excellence maintenue
                                    </div>
                                    <div class="text-xs text-slate-500 dark:text-navy-300">
                                        Accès aux marchés majeurs
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Actions Rapides -->
                <div class="bg-white dark:bg-navy-800 rounded-xl shadow-lg p-6">
                    <h3 class="text-lg font-semibold text-slate-800 dark:text-navy-50 mb-4">
                        Actions Rapides
                    </h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Boutons actifs -->
                        <a href="{{ route('diagnosticentreprise.indexForm') }}" 
                           class="flex items-center p-4 rounded-lg bg-blue-50 dark:bg-navy-700 hover:bg-blue-100 dark:hover:bg-navy-600 transition-colors">
                            <div class="w-10 h-10 rounded-full bg-blue-500 flex items-center justify-center mr-3">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                            </div>
                            <div>
                                <div class="font-medium text-slate-800 dark:text-navy-50">Nouveau Diagnostic</div>
                                <div class="text-xs text-slate-500 dark:text-navy-300">Mettre à jour vos scores</div>
                            </div>
                        </a>

                        <a href="{{ route('entreprise.progression.show', $entreprise->id) }}" 
                           class="flex items-center p-4 rounded-lg bg-purple-50 dark:bg-navy-700 hover:bg-purple-100 dark:hover:bg-navy-600 transition-colors">
                            <div class="w-10 h-10 rounded-full bg-purple-500 flex items-center justify-center mr-3">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                </svg>
                            </div>
                            <div>
                                <div class="font-medium text-slate-800 dark:text-navy-50">Ma Progression</div>
                                <div class="text-xs text-slate-500 dark:text-navy-300">Voir l'évolution</div>
                            </div>
                        </a>
                    </div>
                </div>
            </div>

            <div class="col-span-12 py-6 lg:sticky lg:bottom-0 lg:col-span-4 lg:self-end">
                @include('layouts.sidebar')
            </div>    

        </div>
    </main>
</x-app-layout>
