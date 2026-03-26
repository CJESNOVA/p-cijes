<x-app-layout title="Choisir une entreprise" is-sidebar-open="true" is-header-blur="true">
    <main class="main-content w-full px-[var(--margin-x)] pb-8">
        <!-- Header avec breadcrumb -->
        <div class="flex flex-col space-y-4 py-5 lg:py-6">
            <div class="flex items-center space-x-2 text-sm">
                <a href="{{ route('dashboard') }}" class="text-slate-500 hover:text-primary transition-colors">
                    <i class="fas fa-home mr-1"></i>Tableau de bord
                </a>
                <span class="text-slate-400">/</span>
                <span class="text-slate-700 dark:text-navy-200">Test de classification</span>
            </div>
            
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-slate-800 dark:text-navy-50 lg:text-3xl flex items-center">
                        <i class="fas fa-clipboard-check text-warning mr-3"></i>
                        Test de classification
                    </h1>
                    <p class="text-slate-600 dark:text-navy-300 mt-2">
                        Évaluez votre entreprise avec notre test de classification personnalisé
                    </p>
                </div>
                
                <!-- Statistiques rapides -->
                <div class="hidden lg:flex items-center space-x-6">
                    <div class="text-center">
                        <div class="text-2xl font-bold text-primary">{{ $entreprises->count() }}</div>
                        <div class="text-xs text-slate-500">Entreprises disponibles</div>
                    </div>
                    <div class="w-px h-8 bg-slate-200 dark:bg-navy-600"></div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-success">~15 min</div>
                        <div class="text-xs text-slate-500">Durée estimée</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Messages flash -->
        @if(session('success'))
            <div class="alert alert-success flex items-center rounded-lg bg-success/10 border border-success/20 px-4 py-4 text-success sm:px-5 mb-6">
                <i class="fas fa-check-circle mr-3"></i>
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-error flex items-center rounded-lg bg-danger/10 border border-danger/20 px-4 py-4 text-danger sm:px-5 mb-6">
                <i class="fas fa-exclamation-triangle mr-3"></i>
                {{ session('error') }}
            </div>
        @endif

        @if(session('warning'))
            <div class="alert alert-warning flex items-center rounded-lg bg-warning/10 border border-warning/20 px-4 py-4 text-warning sm:px-5 mb-6">
                <i class="fas fa-info-circle mr-3"></i>
                {{ session('warning') }}
            </div>
        @endif

        <div class="grid grid-cols-12 lg:gap-6">
            <!-- Contenu principal -->
            <div class="col-span-12 lg:col-span-8">
                <!-- Carte principale -->
                <div class="card bg-white dark:bg-navy-800 shadow-lg border border-slate-200 dark:border-navy-700">
                    <div class="card-body p-6">
                        <!-- En-tête de la carte -->
                        <div class="flex items-center justify-between mb-6">
                            <div>
                                <h2 class="text-xl font-bold text-slate-800 dark:text-navy-100 flex items-center">
                                    <i class="fas fa-building text-primary mr-2"></i>
                                    Sélectionnez une entreprise
                                </h2>
                                <p class="text-slate-600 dark:text-navy-300 mt-1">
                                    Choisissez l'entreprise que vous souhaitez évaluer
                                </p>
                            </div>
                            
                            @if($entreprises->count() > 0)
                                <div class="badge bg-primary/10 text-primary px-3 py-1">
                                    <i class="fas fa-filter mr-1"></i>
                                    {{ $entreprises->count() }} {{ $entreprises->count() > 1 ? 'entreprises' : 'entreprise' }}
                                </div>
                            @endif
                        </div>

                        @if($entreprises->count() > 0)
                            <!-- Grille d'entreprises -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                @foreach($entreprises as $entreprise)
                                    <div class="group relative bg-gradient-to-br from-slate-50 to-white dark:from-navy-700 dark:to-navy-800 rounded-xl border border-slate-200 dark:border-navy-600 hover:border-primary/50 transition-all duration-300 hover:shadow-xl hover:-translate-y-1 overflow-hidden">
                                        <!-- Badge de statut -->
                                        <div class="absolute top-4 right-4 z-10">
                                            <div class="w-3 h-3 bg-success rounded-full animate-pulse"></div>
                                        </div>
                                        
                                        <!-- Contenu de la carte -->
                                        <div class="p-6">
                                            <!-- Header entreprise -->
                                            <div class="flex items-start space-x-4 mb-4">
                                                <div class="relative">
                                                    @if($entreprise->vignette)
                                                        <img src="{{ env('SUPABASE_BUCKET_URL') . '/' . $entreprise->vignette }}" 
                                                             alt="{{ $entreprise->nom }}" 
                                                             class="w-16 h-16 rounded-xl object-cover ring-2 ring-slate-200 dark:ring-navy-600 group-hover:ring-primary/50 transition-all">
                                                    @else
                                                        <div class="w-16 h-16 rounded-xl bg-gradient-to-br from-primary to-primary/80 dark:from-primary dark:to-primary/60 flex items-center justify-center">
                                                            <i class="fas fa-building text-white text-xl"></i>
                                                        </div>
                                                    @endif
                                                    
                                                    <!-- Icône de vérification -->
                                                    <div class="absolute -bottom-1 -right-1 w-6 h-6 bg-success rounded-full flex items-center justify-center ring-2 ring-white dark:ring-navy-800">
                                                        <i class="fas fa-check text-white text-xs"></i>
                                                    </div>
                                                </div>
                                                
                                                <div class="flex-1 min-w-0">
                                                    <h3 class="font-bold text-slate-800 dark:text-navy-100 text-lg group-hover:text-primary transition-colors truncate">
                                                        {{ $entreprise->nom }}
                                                    </h3>
                                                    <div class="flex items-center space-x-2 mt-1">
                                                        @if($entreprise->secteur)
                                                            <span class="text-xs px-2 py-1 bg-slate-100 dark:bg-navy-600 text-slate-600 dark:text-navy-300 rounded-full">
                                                                <i class="fas fa-briefcase mr-1"></i>
                                                                {{ $entreprise->secteur->titre }}
                                                            </span>
                                                        @endif
                                                        
                                                        @if($entreprise->entrepriseprofil)
                                                            <span class="text-xs px-2 py-1 bg-primary/10 text-primary rounded-full">
                                                                <i class="fas fa-chart-line mr-1"></i>
                                                                {{ $entreprise->entrepriseprofil->titre }}
                                                            </span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <!-- Description -->
                                            @if($entreprise->description)
                                                <p class="text-sm text-slate-600 dark:text-navy-300 line-clamp-2 mb-4">
                                                    {{ Str::limit(strip_tags($entreprise->description), 120) }}
                                                </p>
                                            @endif
                                            
                                            <!-- Actions -->
                                            <div class="flex items-center justify-between">
                                                <div class="flex items-center space-x-2 text-xs text-slate-500">
                                                    <i class="fas fa-clock"></i>
                                                    <span>~15 min</span>
                                                </div>
                                                
                                                <a href="{{ route('diagnosticentreprisequalification.showForm', $entreprise->id) }}" 
                                                   class="btn bg-gradient-to-r from-primary to-primary/80 text-white hover:from-primary/90 hover:to-primary/70 px-4 py-2 rounded-lg font-medium transition-all duration-300 transform hover:scale-105 shadow-lg hover:shadow-xl">
                                                    <i class="fas fa-play mr-2"></i>
                                                    Commencer
                                                </a>
                                            </div>
                                        </div>
                                        
                                        <!-- Effet de brillance au hover -->
                                        <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/5 to-transparent -translate-x-full group-hover:translate-x-full transition-transform duration-1000"></div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <!-- État vide -->
                            <div class="text-center py-12">
                                <div class="w-24 h-24 mx-auto bg-gradient-to-br from-slate-100 to-slate-200 dark:from-navy-700 dark:to-navy-600 rounded-full flex items-center justify-center mb-6">
                                    <i class="fas fa-building text-3xl text-slate-400 dark:text-navy-400"></i>
                                </div>
                                
                                <h3 class="text-xl font-bold text-slate-700 dark:text-navy-200 mb-3">
                                    Aucune entreprise disponible
                                </h3>
                                
                                <p class="text-slate-600 dark:text-navy-400 mb-8 max-w-md mx-auto">
                                    Vous devez d'abord créer une entreprise avant de pouvoir effectuer un test de classification.
                                </p>
                                
                                <div class="flex flex-col sm:flex-row items-center justify-center space-y-3 sm:space-y-0 sm:space-x-4">
                                    <a href="{{ route('entreprise.create') }}" 
                                       class="btn bg-primary text-white hover:bg-primary-focus px-6 py-3 rounded-lg font-medium transition-all duration-300 transform hover:scale-105 shadow-lg">
                                        <i class="fas fa-plus mr-2"></i>
                                        Créer une entreprise
                                    </a>
                                    
                                    <a href="{{ route('dashboard') }}" 
                                       class="btn bg-slate-100 dark:bg-navy-600 text-slate-700 dark:text-navy-200 hover:bg-slate-200 dark:hover:bg-navy-500 px-6 py-3 rounded-lg font-medium transition-all duration-300">
                                        <i class="fas fa-arrow-left mr-2"></i>
                                        Retour au tableau de bord
                                    </a>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="col-span-12 lg:col-span-4">
                <div class="lg:sticky lg:top-6 space-y-6">
                    <!-- Aide contextuelle -->
                    <div class="card bg-gradient-to-br from-primary/5 to-primary/10 dark:from-navy-700 dark:to-navy-800 border border-primary/20">
                        <div class="card-body p-6">
                            <h3 class="text-lg font-bold text-slate-800 dark:text-navy-100 mb-4 flex items-center">
                                <i class="fas fa-question-circle text-primary mr-2"></i>
                                Besoin d'aide ?
                            </h3>
                            
                            <div class="space-y-4">
                                <div class="flex items-start space-x-3">
                                    <div class="w-8 h-8 bg-primary/20 rounded-full flex items-center justify-center flex-shrink-0">
                                        <span class="text-primary font-bold text-sm">1</span>
                                    </div>
                                    <div>
                                        <h4 class="font-medium text-slate-700 dark:text-navy-200">Sélectionnez votre entreprise</h4>
                                        <p class="text-sm text-slate-600 dark:text-navy-400">Choisissez l'entreprise que vous souhaitez évaluer</p>
                                    </div>
                                </div>
                                
                                <div class="flex items-start space-x-3">
                                    <div class="w-8 h-8 bg-primary/20 rounded-full flex items-center justify-center flex-shrink-0">
                                        <span class="text-primary font-bold text-sm">2</span>
                                    </div>
                                    <div>
                                        <h4 class="font-medium text-slate-700 dark:text-navy-200">Répondez aux questions</h4>
                                        <p class="text-sm text-slate-600 dark:text-navy-400">Le test prend environ 15 minutes</p>
                                    </div>
                                </div>
                                
                                <div class="flex items-start space-x-3">
                                    <div class="w-8 h-8 bg-primary/20 rounded-full flex items-center justify-center flex-shrink-0">
                                        <span class="text-primary font-bold text-sm">3</span>
                                    </div>
                                    <div>
                                        <h4 class="font-medium text-slate-700 dark:text-navy-200">Obtenez vos résultats</h4>
                                        <p class="text-sm text-slate-600 dark:text-navy-400">Recevez une analyse détaillée et des recommandations</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Sidebar navigation -->
                    @include('layouts.sidebar')
                </div>
            </div>
        </div>
    </main>
</x-app-layout>
