<x-app-layout title="Mes entreprises" is-sidebar-open="true" is-header-blur="true">
    <main class="main-content w-full px-[var(--margin-x)] pb-8">
        <!-- Header moderne -->
        <div class="mb-2">
            <div class="flex items-center justify-between mb-2">
                <div class="flex items-center gap-4">
                    <div class="h-14 w-14 rounded-xl bg-gradient-to-br from-teal-500 to-blue-600 flex items-center justify-center shadow-lg">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h-1m2-5h-8"></path>
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-3xl font-bold text-slate-800 dark:text-navy-50">
                            Mes entreprises
                        </h1>
                        <p class="mt-2 text-slate-600 dark:text-navy-200 text-lg">
                            Gérez vos entreprises et accédez aux tests de qualification
                        </p>
                    </div>
                </div>
                <a href="{{ route('entreprise.create') }}"
                   class="px-6 py-3 bg-gradient-to-r from-teal-500 to-blue-600 text-white rounded-lg hover:from-teal-600 hover:to-blue-700 transition-all font-medium shadow-lg flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Ajouter une entreprise
                </a>
            </div>
        </div>

        <div class="grid grid-cols-12 lg:gap-6">
            <div class="col-span-12 pt-6 lg:col-span-8 lg:pb-6">
        <!-- Statistiques -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="card shadow-xl border-0 bg-gradient-to-br from-teal-500 to-teal-600">
                <div class="card-body p-6 text-white">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-teal-100 text-sm font-medium">Total entreprises</p>
                            <p class="text-3xl font-bold mt-1">{{ $entreprises ? $entreprises->count() : 0 }}</p>
                        </div>
                        <div class="h-12 w-12 rounded-xl bg-white/20 flex items-center justify-center">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h-1m2-5h-8"></path>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow-xl border-0 bg-gradient-to-br from-blue-500 to-blue-600">
                <div class="card-body p-6 text-white">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-blue-100 text-sm font-medium">Membres CJES</p>
                            <p class="text-3xl font-bold mt-1">{{ $entreprises ? $entreprises->where('entreprise.est_membre_cijes', true)->count() : 0 }}</p>
                        </div>
                        <div class="h-12 w-12 rounded-xl bg-white/20 flex items-center justify-center">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow-xl border-0 bg-gradient-to-br from-purple-500 to-purple-600">
                <div class="card-body p-6 text-white">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-purple-100 text-sm font-medium">Tests complétés</p>
                            <p class="text-3xl font-bold mt-1">
                                {{ $entreprises ? $entreprises->sum(function($em) {
                                    return \App\Models\Diagnostic::where('entreprise_id', $em->entreprise->id)
                                        ->where('membre_id', $em->membre_id ?? auth()->id())
                                        ->where('diagnostictype_id', 3)
                                        ->where('diagnosticstatut_id', 2)
                                        ->count();
                                }) : 0 }}
                            </p>
                        </div>
                        <div class="h-12 w-12 rounded-xl bg-white/20 flex items-center justify-center">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
        </div>
                <!-- Messages modernes -->
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

                @if(session('info'))
                    <div class="alert flex rounded-lg bg-blue-500 px-6 py-4 text-white mb-6 shadow-lg">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        {{ session('info') }}
                    </div>
                @endif

                @if($entreprises && $entreprises->count() > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        @foreach($entreprises as $em)
                            <div class="card shadow-xl hover:shadow-2xl transition-all duration-300 hover:-translate-y-1">
                                <div class="card-body p-6">
                                    <!-- Header de la carte -->
                                    <div class="flex items-start justify-between mb-4">
                                        <div class="flex items-center gap-4">
                                            <div class="relative">
                                                <div class="h-16 w-16 rounded-xl bg-gradient-to-br from-teal-100 to-blue-100 flex items-center justify-center">
                                                    @if($em->entreprise && $em->entreprise->vignette)
                                                        <img class="h-14 w-14 rounded-lg object-cover" src="{{ env('SUPABASE_BUCKET_URL') . '/' . $em->entreprise->vignette }}" alt="logo" />
                                                    @else
                                                        <svg class="w-8 h-8 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h-1m2-5h-8"></path>
                                                        </svg>
                                                    @endif
                                                </div>
                                                @if($em->entreprise && $em->entreprise->spotlight)
                                                    <div class="absolute -top-1 -right-1 h-4 w-4 rounded-full bg-amber-500 border-2 border-white animate-pulse"></div>
                                                @endif
                                            </div>
                                            <div>
                                                <h3 class="text-lg font-semibold text-slate-800 dark:text-navy-50">
                                                    {{ $em->entreprise->nom }}
                                                </h3>
                                                @if($em->entreprise && $em->entreprise->entrepriseprofil && $em->entreprise->est_membre_cijes)
                                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium mt-1
                                                        @switch($em->entreprise->entrepriseprofil->id)
                                                            @case(1)
                                                                bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200
                                                            @break
                                                            @case(2)
                                                                bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200
                                                            @break
                                                            @case(3)
                                                                bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                                                            @break
                                                            @default
                                                                bg-slate-100 text-slate-800 dark:bg-slate-900 dark:text-slate-200
                                                        @endswitch
                                                    ">
                                                        @switch($em->entreprise->entrepriseprofil->id)
                                                            @case(1)
                                                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                                                </svg>{{ $em->entreprise->entrepriseprofil->titre ?? 'CJES' }}
                                                            @break
                                                            @case(2)
                                                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                                                                </svg>{{ $em->entreprise->entrepriseprofil->titre ?? 'CJES' }}
                                                            @break
                                                            @case(3)
                                                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                                </svg>{{ $em->entreprise->entrepriseprofil->titre ?? 'CJES' }}
                                                            @break
                                                        @endswitch
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Actions de test -->
                                    <div class="mb-4">
                                        @php
                                            $testEnCours = \App\Models\Diagnostic::where('entreprise_id', $em->entreprise->id)
                                                ->where('membre_id', $em->membre_id ?? auth()->id())
                                                ->where('diagnostictype_id', 3)
                                                ->where('diagnosticstatut_id', 1)
                                                ->first();
                                            
                                            $testTermine = \App\Models\Diagnostic::where('entreprise_id', $em->entreprise->id)
                                                ->where('membre_id', $em->membre_id ?? auth()->id())
                                                ->where('diagnostictype_id', 3)
                                                ->where('diagnosticstatut_id', 2)
                                                ->first();
                                        @endphp
                                        
                                        @if($testEnCours)
                                            <a href="{{ route('diagnosticentreprisequalification.showForm', $em->entreprise->id) }}" 
                                               class="inline-flex items-center px-4 py-2 bg-amber-500 text-white text-sm font-medium rounded-lg hover:bg-amber-600 transition-colors">
                                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                                </svg>
                                                Continuer le test
                                            </a>
                                        @elseif($testTermine)
                                            <a href="{{ route('diagnosticentreprisequalification.results', $em->entreprise->id) }}" 
                                               class="inline-flex items-center px-4 py-2 bg-blue-500 text-white text-sm font-medium rounded-lg hover:bg-blue-600 transition-colors">
                                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                                </svg>
                                                Voir les résultats
                                            </a>
                                        @else
                                            <a href="{{ route('diagnosticentreprisequalification.showForm', $em->entreprise->id) }}" 
                                               class="inline-flex items-center px-4 py-2 bg-purple-500 text-white text-sm font-medium rounded-lg hover:bg-purple-600 transition-colors">
                                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                                </svg>
                                                Test de qualification
                                            </a>
                                        @endif
                                    </div>

                                    <!-- Description -->
                                    @if($em->entreprise->description)
                                        <p class="text-sm text-slate-600 dark:text-navy-200 mb-4 line-clamp-2">{!! $em->entreprise->description !!}</p>
                                    @endif

                                    <!-- Informations détaillées -->
                                    <div class="space-y-3 mb-4">
                                        <div class="flex items-center gap-3">
                                            <div class="h-8 w-8 rounded-lg bg-teal-100 flex items-center justify-center">
                                                <svg class="w-4 h-4 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                                </svg>
                                            </div>
                                            <div>
                                                <p class="text-xs text-slate-500">Votre fonction</p>
                                                <p class="text-sm font-medium text-slate-800 dark:text-navy-50">{{ $em->fonction }}</p>
                                            </div>
                                        </div>

                                        @if($em->entreprise->telephone)
                                        <div class="flex items-center gap-3">
                                            <div class="h-8 w-8 rounded-lg bg-blue-100 flex items-center justify-center">
                                                <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.495 1.495c.51.51.51 1.336 0 1.847l-1.495 1.495a1 1 0 01-.948.279H8a6 6 0 01-6-6v-.728a1 1 0 01.279-.948l1.495-1.495c.51-.51 1.336-.51 1.847 0l1.495 1.495A1 1 0 0110.72 5H21a2 2 0 012 2v14a2 2 0 01-2 2H8a2 2 0 01-2-2V7a2 2 0 012-2z"></path>
                                                </svg>
                                            </div>
                                            <div>
                                                <p class="text-xs text-slate-500">Téléphone</p>
                                                <p class="text-sm font-medium text-slate-800 dark:text-navy-50">{{ $em->entreprise->telephone }}</p>
                                            </div>
                                        </div>
                                        @endif

                                        @if($em->entreprise->email)
                                        <div class="flex items-center gap-3">
                                            <div class="h-8 w-8 rounded-lg bg-purple-100 flex items-center justify-center">
                                                <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                                </svg>
                                            </div>
                                            <div>
                                                <p class="text-xs text-slate-500">Email</p>
                                                <p class="text-sm font-medium text-slate-800 dark:text-navy-50">{{ $em->entreprise->email }}</p>
                                            </div>
                                        </div>
                                        @endif

                                        @if($em->entreprise->est_membre_cijes)
                                        <div class="flex items-center gap-3">
                                            <div class="h-8 w-8 rounded-lg bg-green-100 flex items-center justify-center">
                                                <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                            </div>
                                            <div>
                                                <p class="text-xs text-slate-500">Statut</p>
                                                <p class="text-sm font-medium text-green-600">Membre CJES</p>
                                            </div>
                                        </div>
                                        @endif

                                        @if($em->entreprise->annee_creation)
                                        <div class="flex items-center gap-3">
                                            <div class="h-8 w-8 rounded-lg bg-slate-100 flex items-center justify-center">
                                                <svg class="w-4 h-4 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 0l4 4m0 0l-4 4m4-4v-4m0 0H4m4 0h4"></path>
                                                </svg>
                                            </div>
                                            <div>
                                                <p class="text-xs text-slate-500">Année de création</p>
                                                <p class="text-sm font-medium text-slate-800 dark:text-navy-50">{{ $em->entreprise->annee_creation }}</p>
                                            </div>
                                        </div>
                                        @endif
                                    </div>

                                    <!-- Actions -->
                                    <div class="flex gap-3 pt-4 border-t border-slate-200 dark:border-navy-500">
                                        <a href="{{ route('entreprise.edit', $em->entreprise->id) }}"
                                           class="flex-1 inline-flex items-center justify-center px-4 py-2 bg-teal-500 text-white text-sm font-medium rounded-lg hover:bg-teal-600 transition-colors">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                            </svg>
                                            Modifier
                                        </a>
                                        <form action="{{ route('entreprise.destroy', $em->entreprise->id) }}" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    class="px-4 py-2 bg-red-500 text-white text-sm font-medium rounded-lg hover:bg-red-600 transition-colors"
                                                    onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette entreprise ?')">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <!-- État vide -->
                    <div class="card shadow-xl">
                        <div class="card-body p-12 text-center">
                            <div class="h-20 w-20 rounded-xl bg-gradient-to-br from-teal-100 to-blue-100 flex items-center justify-center mx-auto mb-6">
                                <svg class="w-10 h-10 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h-1m2-5h-8"></path>
                                </svg>
                            </div>
                            <h3 class="text-xl font-semibold text-slate-800 dark:text-navy-50 mb-2">
                                Aucune entreprise enregistrée
                            </h3>
                            <p class="text-slate-600 dark:text-navy-200 mb-6">
                                Commencez par ajouter votre première entreprise pour accéder aux tests de qualification.
                            </p>
                            <a href="{{ route('entreprise.create') }}"
                               class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-teal-500 to-blue-600 text-white font-medium rounded-lg hover:from-teal-600 hover:to-blue-700 transition-all shadow-lg">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                Ajouter votre première entreprise
                            </a>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Sidebar -->
            <div class="col-span-12 py-6 lg:sticky lg:bottom-0 lg:col-span-4 lg:self-end">
                @include('layouts.sidebar')
            </div>
        </div>
    </main>
</x-app-layout>