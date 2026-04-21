<x-app-layout title="Orientations Entreprise" is-sidebar-open="true" is-header-blur="true">
    <main class="main-content w-full px-[var(--margin-x)] pb-8">
        <!-- Header moderne -->
        <div class="mb-8">
            <div class="flex items-center gap-4 mb-4">
                <div class="h-16 w-16 rounded-xl bg-gradient-to-br from-green-500 to-teal-600 flex items-center justify-center shadow-lg">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                    </svg>
                </div>
                <div>
                    <h1 class="text-3xl font-bold text-slate-800 dark:text-navy-50">
                        Orientations Personnalisées
                    </h1>
                    <p class="mt-2 text-slate-600 dark:text-navy-200 text-lg">
                        Dispositifs CJES recommandés pour votre entreprise
                    </p>
                    <div class="mt-3 flex items-center gap-3">
                        <span class="text-sm text-slate-500">Entreprise:</span>
                        <span class="px-3 py-1 bg-green-500/10 text-green-600 rounded-full text-sm font-medium">
                            {{ $entreprise->nom }}
                        </span>
                        @if($dernierDiagnostic)
                            <span class="text-sm text-slate-500">
                                Basé sur le diagnostic du {{ $dernierDiagnostic->created_at->format('d/m/Y') }}
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
                
                <!-- Résumé des Orientations -->
                <div class="bg-white dark:bg-navy-800 rounded-xl shadow-lg p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-xl font-semibold text-slate-800 dark:text-navy-50">
                            Résumé de vos Orientations
                        </h2>
                        <div class="flex items-center gap-2">
                            <span class="px-3 py-1 bg-blue-100 text-blue-600 rounded-full text-sm font-medium">
                                {{ count($orientations) }} dispositifs
                            </span>
                            @if($blocsCritiques && count($blocsCritiques) > 0)
                                <span class="px-3 py-1 bg-red-100 text-red-600 rounded-full text-sm font-medium">
                                    {{ count($blocsCritiques) }} blocs critiques
                                </span>
                            @endif
                        </div>
                    </div>
                    
                    @if(count($orientations) > 0)
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @foreach($orientations as $orientation)
                                <div class="p-4 rounded-lg border border-slate-200 dark:border-navy-700 hover:shadow-md transition-shadow">
                                    <div class="flex items-start gap-3">
                                        <div class="w-10 h-10 rounded-full bg-green-100 flex items-center justify-center flex-shrink-0">
                                            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                            </svg>
                                        </div>
                                        <div class="flex-1">
                                            <h3 class="font-medium text-slate-800 dark:text-navy-50 mb-1">
                                                Orientations pour {{ $orientation['bloc'] }}
                                            </h3>
                                            <p class="text-sm text-slate-600 dark:text-navy-300 mb-2">
                                                Bloc: {{ $orientation['bloc'] }} (Score: {{ $orientation['score'] }}/20)
                                            </p>
                                            <div class="flex items-center gap-2">
                                                <span class="px-2 py-1 bg-slate-100 text-slate-600 rounded text-xs">
                                                    {{ $orientation['bloc'] }}
                                                </span>
                                                <span class="px-2 py-1 bg-blue-100 text-blue-600 rounded text-xs">
                                                    {{ $orientation['orientations']->count() }} orientation(s)
                                                </span>
                                                <span class="px-2 py-1 {{ $orientation['score'] < 8 ? 'bg-red-100 text-red-600' : 'bg-yellow-100 text-yellow-600' }} rounded text-xs">
                                                    {{ $orientation['score'] < 8 ? 'Critique' : 'À améliorer' }}
                                                </span>
                                            </div>
                                            
                                            @if($orientation['orientations']->isNotEmpty())
                                                <div class="mt-3 space-y-2">
                                                    @foreach($orientation['orientations'] as $item)
                                                        <div class="p-2 bg-slate-50 dark:bg-navy-700 rounded text-sm">
                                                            <p class="text-slate-700 dark:text-navy-300">{{ $item->description ?? 'Orientation disponible' }}</p>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8">
                            <div class="w-16 h-16 rounded-full bg-green-100 flex items-center justify-center mx-auto mb-4">
                                <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <h3 class="text-lg font-medium text-slate-800 dark:text-navy-50 mb-2">
                                Félicitations !
                            </h3>
                            <p class="text-slate-600 dark:text-navy-300">
                                Aucune orientation n'est nécessaire pour le moment. Votre entreprise est sur la bonne voie !
                            </p>
                        </div>
                    @endif
                </div>

                <!-- Détails par Bloc -->
                @if($orientationsParBloc && count($orientationsParBloc) > 0)
                    <div class="bg-white dark:bg-navy-800 rounded-xl shadow-lg p-6">
                        <h2 class="text-xl font-semibold text-slate-800 dark:text-navy-50 mb-6">
                            Orientations Détaillées par Bloc
                        </h2>
                        
                        <div class="space-y-6">
                            @foreach($orientationsParBloc as $blocCode => $blocData)
                                <div class="border border-slate-200 dark:border-navy-700 rounded-lg p-4">
                                    <div class="flex items-center justify-between mb-4">
                                        <div class="flex items-center gap-3">
                                            <div class="w-12 h-12 rounded-full bg-slate-100 dark:bg-navy-700 flex items-center justify-center">
                                                <svg class="w-6 h-6 text-slate-600 dark:text-navy-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                                </svg>
                                            </div>
                                            <div>
                                                <h3 class="font-semibold text-slate-800 dark:text-navy-50">
                                                    {{ $blocData['nom'] }}
                                                </h3>
                                                <p class="text-sm text-slate-600 dark:text-navy-300">
                                                    Score: {{ $blocData['score'] }}/20
                                                </p>
                                            </div>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <span class="px-3 py-1 {{ $blocData['score'] < 8 ? 'bg-red-100 text-red-600' : 'bg-yellow-100 text-yellow-600' }} rounded-full text-sm font-medium">
                                                {{ $blocData['score'] < 8 ? 'Bloc Critique' : 'À Améliorer' }}
                                            </span>
                                        </div>
                                    </div>
                                    
                                    <div class="space-y-3">
                                        @foreach($blocData['orientations'] as $orientation)
                                            <div class="flex items-start gap-3 p-3 rounded-lg bg-slate-50 dark:bg-navy-700">
                                                <div class="w-8 h-8 rounded-full bg-green-100 flex items-center justify-center flex-shrink-0 mt-0.5">
                                                    <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                                    </svg>
                                                </div>
                                                <div class="flex-1">
                                                    <h4 class="font-medium text-slate-800 dark:text-navy-50 mb-1">
                                                        Orientations disponibles
                                                    </h4>
                                                    <p class="text-sm text-slate-600 dark:text-navy-300">
                                                        Bloc: {{ $orientation['bloc'] }} (Score: {{ $orientation['score'] }}/20)
                                                    </p>
                                                    
                                                    @if(isset($orientation['orientations']) && count($orientation['orientations']) > 0)
                                                        <div class="mt-2 space-y-1">
                                                            @foreach($orientation['orientations'] as $item)
                                                                <div class="text-xs text-slate-600 dark:text-navy-400">
                                                                    • {{ $item->description ?? 'Orientation recommandée' }}
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Plan d'Action -->
                <div class="bg-white dark:bg-navy-800 rounded-xl shadow-lg p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-xl font-semibold text-slate-800 dark:text-navy-50">
                            Plan d'Action Recommandé
                        </h2>
                        <button class="btn btn-sm bg-primary text-white hover:bg-primary-focus">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                            </svg>
                            Exporter
                        </button>
                    </div>
                    
                    <div class="space-y-4">
                        @if($blocsCritiques && count($blocsCritiques) > 0)
                            @foreach($blocsCritiques as $index => $bloc)
                                <div class="flex items-start gap-4 p-4 rounded-lg border border-red-200 bg-red-50 dark:bg-navy-700">
                                    <div class="w-8 h-8 rounded-full bg-red-500 flex items-center justify-center flex-shrink-0 text-white font-bold">
                                        {{ $index + 1 }}
                                    </div>
                                    <div class="flex-1">
                                        <h3 class="font-semibold text-red-800 dark:text-red-300 mb-2">
                                            Priorité {{ $index + 1 }}: {{ $bloc['nom'] }}
                                        </h3>
                                        <p class="text-sm text-red-700 dark:text-red-400 mb-3">
                                            Bloc critique nécessitant une attention immédiate (Score: {{ $bloc['score'] }}/20)
                                        </p>
                                        
                                        @php
                                            $orientationsBloc = collect($orientations)->where('bloc', $bloc['code'])->first();
                                        @endphp
                                        @if($orientationsBloc)
                                            <div class="p-3 rounded-lg bg-white dark:bg-navy-800 border border-red-200">
                                                <h4 class="font-medium text-slate-800 dark:text-navy-50 mb-2">
                                                    Orientations recommandées:
                                                </h4>
                                                <p class="text-sm text-slate-600 dark:text-navy-300 mb-2">
                                                    Bloc: {{ $orientationsBloc['bloc'] }} (Score: {{ $orientationsBloc['score'] }}/20)
                                                </p>
                                                
                                                @if(isset($orientationsBloc['orientations']) && $orientationsBloc['orientations']->isNotEmpty())
                                                    <div class="space-y-2">
                                                        @foreach($orientationsBloc['orientations'] as $item)
                                                            <div class="text-sm text-slate-600 dark:text-navy-300">
                                                                • {{ $item->description ?? 'Orientation disponible' }}
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                @endif
                                                
                                                <div class="flex items-center gap-2 mt-3">
                                                    <button class="btn btn-sm bg-red-500 text-white hover:bg-red-600">
                                                        Démarrer maintenant
                                                    </button>
                                                    <button class="btn btn-sm bg-slate-100 text-slate-600 hover:bg-slate-200 dark:bg-navy-700 dark:text-navy-300">
                                                        En savoir plus
                                                    </button>
                                                </div>
                                            </div>
                                        @endif
                                </div>
                            @endforeach
                        @else
                            <div class="text-center py-8">
                                <div class="w-16 h-16 rounded-full bg-green-100 flex items-center justify-center mx-auto mb-4">
                                    <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                <h3 class="text-lg font-medium text-slate-800 dark:text-navy-50 mb-2">
                                    Excellent travail !
                                </h3>
                                <p class="text-slate-600 dark:text-navy-300">
                                    Aucun bloc critique détecté. Continuez sur cette voie !
                                </p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Colonne latérale -->
            <div class="col-span-12 lg:col-span-4 space-y-6">
                
                <!-- Statistiques -->
                <div class="bg-white dark:bg-navy-800 rounded-xl shadow-lg p-6">
                    <h3 class="text-lg font-semibold text-slate-800 dark:text-navy-50 mb-4">
                        Statistiques
                    </h3>
                    
                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-slate-500 dark:text-navy-300">Score global</span>
                            <span class="text-sm font-medium text-slate-800 dark:text-navy-50">
                                {{ $scoreGlobal ?? 0 }}/200
                            </span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-slate-500 dark:text-navy-300">Blocs critiques</span>
                            <span class="text-sm font-medium text-red-600">
                                {{ $blocsCritiques ? count($blocsCritiques) : 0 }}
                            </span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-slate-500 dark:text-navy-300">Blocs conformes</span>
                            <span class="text-sm font-medium text-green-600">
                                {{ $nbBlocConformes ?? 0 }}
                            </span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-slate-500 dark:text-navy-300">Dispositifs recommandés</span>
                            <span class="text-sm font-medium text-blue-600">
                                {{ count($orientations) }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Actions Rapides -->
                <div class="bg-white dark:bg-navy-800 rounded-xl shadow-lg p-6">
                    <h3 class="text-lg font-semibold text-slate-800 dark:text-navy-50 mb-4">
                        Actions Rapides
                    </h3>
                    
                    <div class="space-y-3">
                        <a href="{{ route('diagnosticentreprise.choix_entreprise') }}" 
                           class="flex items-center p-3 rounded-lg bg-blue-50 dark:bg-navy-700 hover:bg-blue-100 dark:hover:bg-navy-600 transition-colors">
                            <div class="w-8 h-8 rounded-full bg-blue-500 flex items-center justify-center mr-3">
                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                            </div>
                            <div>
                                <div class="font-medium text-slate-800 dark:text-navy-50 text-sm">
                                    Refaire le diagnostic
                                </div>
                                <div class="text-xs text-slate-500 dark:text-navy-300">
                                    Mettre à jour vos scores
                                </div>
                            </div>
                        </a>

                        {{--                        
                        <a href="{{ route('entreprise.profil.show', $entreprise->id) }}" 
                           class="flex items-center p-3 rounded-lg bg-purple-50 dark:bg-navy-700 hover:bg-purple-100 dark:hover:bg-navy-600 transition-colors">
                            <div class="w-8 h-8 rounded-full bg-purple-500 flex items-center justify-center mr-3">
                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                            </div>
                            <div>
                                <div class="font-medium text-slate-800 dark:text-navy-50 text-sm">
                                    Voir mon profil
                                </div>
                                <div class="text-xs text-slate-500 dark:text-navy-300">
                                    Analyse détaillée
                                </div>
                            </div>
                        </a>
--}}        <div class="text-xs text-slate-500 dark:text-navy-300">
                                    Analyse détaillée
                                </div>
                            </div>
                        </a>

                        <button onclick="window.print()" 
                                class="flex items-center p-3 rounded-lg bg-green-50 dark:bg-navy-700 hover:bg-green-100 dark:hover:bg-navy-600 transition-colors w-full">
                            <div class="w-8 h-8 rounded-full bg-green-500 flex items-center justify-center mr-3">
                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                                </svg>
                            </div>
                            <div>
                                <div class="font-medium text-slate-800 dark:text-navy-50 text-sm">
                                    Imprimer le plan
                                </div>
                                <div class="text-xs text-slate-500 dark:text-navy-300">
                                    PDF pour partage
                                </div>
                            </div>
                        </button>
                    </div>
                </div>

                <!-- Ressources Utiles -->
                <div class="bg-white dark:bg-navy-800 rounded-xl shadow-lg p-6">
                    <h3 class="text-lg font-semibold text-slate-800 dark:text-navy-50 mb-4">
                        Ressources Utiles
                    </h3>
                    
                    <div class="space-y-3">
                        <a href="#" class="flex items-center p-3 rounded-lg bg-slate-50 dark:bg-navy-700 hover:bg-slate-100 dark:hover:bg-navy-600 transition-colors">
                            <div class="w-8 h-8 rounded-full bg-slate-500 flex items-center justify-center mr-3">
                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                </svg>
                            </div>
                            <div>
                                <div class="font-medium text-slate-800 dark:text-navy-50 text-sm">
                                    Guide des dispositifs
                                </div>
                                <div class="text-xs text-slate-500 dark:text-navy-300">
                                    Documentation complète
                                </div>
                            </div>
                        </a>

                        <a href="#" class="flex items-center p-3 rounded-lg bg-slate-50 dark:bg-navy-700 hover:bg-slate-100 dark:hover:bg-navy-600 transition-colors">
                            <div class="w-8 h-8 rounded-full bg-slate-500 flex items-center justify-center mr-3">
                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                            <div>
                                <div class="font-medium text-slate-800 dark:text-navy-50 text-sm">
                                    Tutoriels vidéo
                                </div>
                                <div class="text-xs text-slate-500 dark:text-navy-300">
                                    Guides pratiques
                                </div>
                            </div>
                        </a>

                        <a href="#" class="flex items-center p-3 rounded-lg bg-slate-50 dark:bg-navy-700 hover:bg-slate-100 dark:hover:bg-navy-600 transition-colors">
                            <div class="w-8 h-8 rounded-full bg-slate-500 flex items-center justify-center mr-3">
                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div>
                                <div class="font-medium text-slate-800 dark:text-navy-50 text-sm">
                                    Support CJES
                                </div>
                                <div class="text-xs text-slate-500 dark:text-navy-300">
                                    Contactez un conseiller
                                </div>
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
