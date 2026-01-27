<x-app-layout title="Mes ressources" is-sidebar-open="true" is-header-blur="true">
    <main class="main-content w-full px-[var(--margin-x)] pb-8">
        <!-- Header moderne -->
        <div class="mb-8">
            <div class="flex items-center justify-between mb-6">
                <div class="flex items-center gap-4">
                    <div class="h-14 w-14 rounded-xl bg-gradient-to-br from-emerald-500 to-emerald-600 flex items-center justify-center shadow-lg">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-3xl font-bold text-slate-800 dark:text-navy-50">
                            Mes ressources
                        </h1>
                        <p class="mt-2 text-slate-600 dark:text-navy-200 text-lg">
                            Gérez vos crédits et transactions
                        </p>
                    </div>
                </div>
                <a href="{{ route('ressourcecompte.create') }}"
                   class="px-6 py-3 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition-colors flex items-center shadow-lg">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Ajouter une ressource
                </a>
            </div>

            <!-- Cartes statistiques -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="card bg-gradient-to-br from-emerald-500 to-emerald-600 text-white border-0 shadow-xl">
                    <div class="card-body p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-emerald-100 text-sm font-medium">Total ressources</p>
                                <p class="text-3xl font-bold mt-2">{{ $types->count() }}</p>
                            </div>
                            <div class="h-12 w-12 bg-white/20 rounded-xl flex items-center justify-center">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card bg-gradient-to-br from-blue-500 to-blue-600 text-white border-0 shadow-xl">
                    <div class="card-body p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-blue-100 text-sm font-medium">Solde total</p>
                                <p class="text-3xl font-bold mt-2">
                                    {{ number_format($types->sum(function($type) { 
                                        return $type->ressourcecomptes->sum('solde'); 
                                    }), 0, ',', ' ') }}
                                </p>
                            </div>
                            <div class="h-12 w-12 bg-white/20 rounded-xl flex items-center justify-center">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card bg-gradient-to-br from-purple-500 to-purple-600 text-white border-0 shadow-xl">
                    <div class="card-body p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-purple-100 text-sm font-medium">Transactions</p>
                                <p class="text-3xl font-bold mt-2">
                                    {{ $types->sum(function($type) { 
                                        return $type->ressourcecomptes->sum(function($compte) { 
                                            return $compte->ressourcetransactions->count(); 
                                        }); 
                                    }) }}
                                </p>
                            </div>
                            <div class="h-12 w-12 bg-white/20 rounded-xl flex items-center justify-center">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card bg-gradient-to-br from-orange-500 to-orange-600 text-white border-0 shadow-xl">
                    <div class="card-body p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-orange-100 text-sm font-medium">Entreprises</p>
                                <p class="text-3xl font-bold mt-2">
                                    {{ $types->sum(function($type) { 
                                        return $type->ressourcecomptes->whereNotNull('entreprise_id')->count(); 
                                    }) }}
                                </p>
                            </div>
                            <div class="h-12 w-12 bg-white/20 rounded-xl flex items-center justify-center">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-12 lg:gap-6">
            <div class="col-span-12 pt-6 lg:col-span-8 lg:pb-6">

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

                @if($types && $types->isNotEmpty())
                    <div class="space-y-6">
                        @foreach($types as $type)
                            <div class="card bg-white dark:bg-navy-800 rounded-xl shadow-lg overflow-hidden">
                                <!-- Header du type -->
                                <div class="bg-gradient-to-r from-emerald-500 to-emerald-600 p-6">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center gap-4">
                                            <div class="h-12 w-12 bg-white/20 rounded-xl flex items-center justify-center">
                                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                            </div>
                                            <div>
                                                <h3 class="text-xl font-bold text-white">{{ $type->titre }}</h3>
                                                <p class="text-emerald-100">
                                                    {{ $type->ressourcecomptes->count() }} compte(s) • 
                                                    Solde total: {{ number_format($type->ressourcecomptes->sum('solde'), 0, ',', ' ') }}
                                                </p>
                                            </div>
                                        </div>
                                        <button onclick="toggleType('type-{{ $type->id }}')" 
                                                class="text-white hover:bg-white/20 p-2 rounded-lg transition-colors">
                                            <svg class="w-6 h-6 transition-transform duration-300" id="icon-type-{{ $type->id }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                            </svg>
                                        </button>
                                    </div>
                                </div>

                                <!-- Contenu du type -->
                                <div id="type-{{ $type->id }}" class="p-6 space-y-4">
                                    @if($type->ressourcecomptes && $type->ressourcecomptes->isNotEmpty())
                                        @foreach($type->ressourcecomptes as $compte)
                                            <div class="border-l-4 border-emerald-500 bg-gray-50 dark:bg-navy-900 rounded-lg p-4">
                                                <div class="flex items-center justify-between mb-3">
                                                    <div>
                                                        <h4 class="text-lg font-semibold text-slate-800 dark:text-navy-50">
                                                            {{ $compte->entreprise?->nom ?? 'Compte personnel' }}
                                                        </h4>
                                                        <p class="text-2xl font-bold text-emerald-600">
                                                            {{ number_format($compte->solde, 2) }} crédits
                                                        </p>
                                                    </div>
                                                    <div class="text-right">
                                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-emerald-100 text-emerald-800">
                                                            Actif
                                                        </span>
                                                    </div>
                                                </div>

                                                @if($compte->ressourcetransactions && $compte->ressourcetransactions->isNotEmpty())
                                                    <div class="mt-4">
                                                        <h5 class="text-sm font-semibold text-slate-600 dark:text-navy-200 mb-3">Transactions récentes</h5>
                                                        <div class="space-y-2">
                                                            @foreach($compte->ressourcetransactions->take(3) as $tx)
                                                                <div class="flex items-center justify-between p-3 bg-white dark:bg-navy-700 rounded-lg border border-slate-200 dark:border-navy-600">
                                                                    <div class="flex items-center gap-3">
                                                                        <div class="w-8 h-8 rounded-full bg-emerald-100 flex items-center justify-center">
                                                                            <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"></path>
                                                                            </svg>
                                                                        </div>
                                                                        <div>
                                                                            <p class="font-medium text-slate-800 dark:text-navy-50">
                                                                                {{ number_format($tx->montant, 2) }} crédits
                                                                            </p>
                                                                            <p class="text-sm text-slate-500 dark:text-navy-300">
                                                                                {{ optional($tx->created_at)->format('d M Y H:i') }}
                                                                            </p>
                                                                        </div>
                                                                    </div>
                                                                    <div class="text-right">
                                                                        <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium
                                                                            @if($tx->formationRessource) bg-blue-100 text-blue-800
                                                                            @elseif($tx->prestationRessource) bg-green-100 text-green-800
                                                                            @elseif($tx->evenementRessource) bg-orange-100 text-orange-800
                                                                            @elseif($tx->espaceRessource) bg-purple-100 text-purple-800
                                                                            @elseif($tx->cotisationRessource) bg-red-100 text-red-800
                                                                            @else bg-gray-100 text-gray-800 @endif">
                                                                            @if($tx->formationRessource) Formation
                                                                            @elseif($tx->prestationRessource) Prestation
                                                                            @elseif($tx->evenementRessource) Événement
                                                                            @elseif($tx->espaceRessource) Espace
                                                                            @elseif($tx->cotisationRessource) Cotisation
                                                                            @else Autre @endif
                                                                        </span>
                                                                    </div>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                        
                                                        @if($compte->ressourcetransactions->count() > 3)
                                                            <button class="mt-3 text-emerald-600 hover:text-emerald-700 text-sm font-medium">
                                                                Voir toutes les transactions ({{ $compte->ressourcetransactions->count() }})
                                                            </button>
                                                        @endif
                                                    </div>
                                                @else
                                                    <div class="text-center py-6 text-slate-500 dark:text-navy-300">
                                                        <svg class="w-12 h-12 mx-auto mb-2 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414A1 1 0 0118 8.586V17a2 2 0 01-2 2z"></path>
                                                        </svg>
                                                        <p>Aucune transaction</p>
                                                    </div>
                                                @endif
                                            </div>
                                        @endforeach
                                    @else
                                        <div class="text-center py-8 text-slate-500 dark:text-navy-300">
                                            <svg class="w-16 h-16 mx-auto mb-4 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                                            </svg>
                                            <p class="text-lg font-medium">Aucune ressource pour ce type</p>
                                            <p class="text-sm mt-1">Commencez par ajouter une ressource</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-12">
                        <div class="h-20 w-20 rounded-xl bg-gradient-to-br from-slate-100 to-slate-200 flex items-center justify-center mx-auto mb-6">
                            <svg class="w-10 h-10 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-slate-800 dark:text-navy-50 mb-2">
                            Aucune ressource disponible
                        </h3>
                        <p class="text-slate-600 dark:text-navy-200 mb-6">
                            Commencez par ajouter votre première ressource
                        </p>
                        <a href="{{ route('ressourcecompte.create') }}" 
                           class="inline-flex items-center px-6 py-3 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition-colors">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                            </svg>
                            Ajouter une ressource
                        </a>
                    </div>
                @endif

            </div>
            <div class="col-span-12 py-6 lg:sticky lg:bottom-0 lg:col-span-4 lg:self-end">
                @include('layouts.sidebar')
            </div>    
        </div>
      </main>
</x-app-layout>

<script>
function toggleType(typeId) {
    const element = document.getElementById(typeId);
    const icon = document.getElementById('icon-' + typeId);
    
    element.classList.toggle('hidden');
    icon.classList.toggle('rotate-180');
}
</script>
