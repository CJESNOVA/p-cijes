<x-app-layout title="Détail de la proposition" is-sidebar-open="true" is-header-blur="true">
    <main class="main-content w-full px-[var(--margin-x)] pb-8">
        <div class="flex items-center space-x-4 py-5 lg:py-6">
            <h2 class="text-xl font-medium text-slate-800 dark:text-navy-50 lg:text-2xl">
                Détail de la proposition
            </h2>
            <div class="hidden h-full py-1 sm:flex">
                <div class="h-full w-px bg-slate-300 dark:bg-navy-600"></div>
            </div>
        </div>

        <div class="grid grid-cols-12 lg:gap-6">
            <div class="col-span-12 pt-6 lg:col-span-8 lg:pb-6">
                
                {{-- Message succès --}}
                @if (session('success'))
                    <div class="alert flex rounded-lg bg-success px-4 py-4 text-white sm:px-5 mb-4">
                        {{ session('success') }}
                    </div>
                @endif

                {{-- Message erreur --}}
                @if (session('error'))
                    <div class="alert flex rounded-lg bg-danger px-4 py-4 text-white sm:px-5 mb-4">
                        {{ session('error') }}
                    </div>
                @endif

                {{-- Carte principale --}}
                <div class="card bg-white dark:bg-navy-800 rounded-xl shadow-lg p-6 space-y-6">
                    
                    {{-- En-tête avec statut --}}
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-slate-800 dark:text-white">Proposition reçue</h3>
                        @if($proposition->statut)
                            @if($proposition->statut->titre === 'En attente')
                                <span class="badge badge-warning">En attente</span>
                            @elseif($proposition->statut->titre === 'Acceptée')
                                <span class="badge badge-success">Acceptée</span>
                            @elseif($proposition->statut->titre === 'Refusée')
                                <span class="badge badge-danger">Refusée</span>
                            @elseif($proposition->statut->titre === 'Annulée')
                                <span class="badge badge-secondary">Annulée</span>
                            @else
                                <span class="badge badge-info">{{ $proposition->statut->titre }}</span>
                            @endif
                        @else
                            <span class="badge badge-secondary">Non défini</span>
                        @endif
                    </div>

                    {{-- Informations de l'expert --}}
                    <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-4">
                        <h4 class="text-lg font-semibold text-blue-800 dark:text-blue-200 mb-3">Expert proposant</h4>
                        <div class="flex items-center space-x-4">
                            <div class="w-16 h-16 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 font-bold text-xl">
                                {{ substr($proposition->expert->membre->nom ?? 'E', 0, 1) }}{{ substr($proposition->expert->membre->prenom ?? 'X', 0, 1) }}
                            </div>
                            <div>
                                <p class="font-medium text-slate-800 dark:text-white text-lg">
                                    {{ $proposition->expert->membre->nom ?? '' }} {{ $proposition->expert->membre->prenom ?? '' }}
                                </p>
                                <p class="text-sm text-slate-600 dark:text-slate-400">Expert indépendant</p>
                                @if($proposition->expert->domaine)
                                    <p class="text-sm text-slate-600 dark:text-slate-400">Domaine: {{ $proposition->expert->domaine }}</p>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Prestation proposée --}}
                    @if($proposition->prestation)
                        <div class="bg-green-50 dark:bg-green-900/20 rounded-lg p-4">
                            <h4 class="text-lg font-semibold text-green-800 dark:text-green-200 mb-3">Prestation proposée</h4>
                            <div class="space-y-2">
                                <p class="font-medium text-slate-800 dark:text-white text-lg">{{ $proposition->prestation->titre }}</p>
                                @if($proposition->prestation->description)
                                    <p class="text-sm text-slate-600 dark:text-slate-400">{{ $proposition->prestation->description }}</p>
                                @endif
                                <div class="grid grid-cols-2 gap-4 mt-3">
                                    @if($proposition->prestation->prix)
                                        <div>
                                            <p class="text-sm text-slate-500 dark:text-slate-400">Prix standard</p>
                                            <p class="font-medium text-slate-800 dark:text-white">{{ number_format($proposition->prestation->prix, 2, ',', ' ') }} €</p>
                                        </div>
                                    @endif
                                    @if($proposition->prestation->duree)
                                        <div>
                                            <p class="text-sm text-slate-500 dark:text-slate-400">Durée standard</p>
                                            <p class="font-medium text-slate-800 dark:text-white">{{ $proposition->prestation->duree }}</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- Proposition de l'expert --}}
                    <div class="bg-yellow-50 dark:bg-yellow-900/20 rounded-lg p-4">
                        <h4 class="text-lg font-semibold text-yellow-800 dark:text-yellow-200 mb-3">Proposition personnalisée</h4>
                        <div class="space-y-3">
                            @if($proposition->message)
                                <div>
                                    <p class="text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Message de l'expert:</p>
                                    <div class="bg-white dark:bg-navy-700 rounded-lg p-3 border border-yellow-200 dark:border-yellow-800">
                                        <p class="text-sm text-slate-600 dark:text-slate-400">{{ $proposition->message }}</p>
                                    </div>
                                </div>
                            @endif
                            
                            <div class="grid grid-cols-2 gap-4">
                                @if($proposition->prix_propose)
                                    <div>
                                        <p class="text-sm text-slate-500 dark:text-slate-400">Prix proposé</p>
                                        <p class="font-medium text-lg text-slate-800 dark:text-white">{{ number_format($proposition->prix_propose, 2, ',', ' ') }} €</p>
                                    </div>
                                @endif
                                @if($proposition->duree_prevue)
                                    <div>
                                        <p class="text-sm text-slate-500 dark:text-slate-400">Durée prévue</p>
                                        <p class="font-medium text-lg text-slate-800 dark:text-white">{{ $proposition->duree_prevue }} jours</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Plan d'accompagnement concerné --}}
                    @if($proposition->plan)
                        <div class="bg-purple-50 dark:bg-purple-900/20 rounded-lg p-4">
                            <h4 class="text-lg font-semibold text-purple-800 dark:text-purple-200 mb-3">Plan d'accompagnement concerné</h4>
                            <div class="space-y-2">
                                <p class="font-medium text-slate-800 dark:text-white">{{ $proposition->plan->objectif ?? 'Objectif non défini' }}</p>
                                @if($proposition->plan->actionprioritaire)
                                    <p class="text-sm text-slate-600 dark:text-slate-400">
                                        <span class="font-medium">Action prioritaire:</span> {{ $proposition->plan->actionprioritaire }}
                                    </p>
                                @endif
                                <p class="text-xs text-slate-500 dark:text-slate-400">
                                    Créé le {{ $proposition->plan->created_at->format('d/m/Y') }}
                                </p>
                            </div>
                        </div>
                    @endif

                    {{-- Dates --}}
                    <div class="flex justify-between text-sm text-slate-500 dark:text-slate-400 pt-4 border-t border-slate-200 dark:border-navy-700">
                        <div>
                            <p>Reçue le {{ $proposition->created_at->format('d/m/Y à H:i') }}</p>
                            @if($proposition->date_expiration)
                                <p>Expire le {{ $proposition->date_expiration->format('d/m/Y') }}</p>
                            @endif
                        </div>
                        <div>
                            @if($proposition->updated_at->ne($proposition->created_at))
                                <p>Dernière modification: {{ $proposition->updated_at->format('d/m/Y à H:i') }}</p>
                            @endif
                        </div>
                    </div>

                    {{-- Actions --}}
                    @if($proposition->statut && $proposition->statut->titre === 'En attente')
                        <div class="flex space-x-3 pt-4 border-t border-slate-200 dark:border-navy-700">
                            <form action="{{ route('proposition.membre.accepter', $proposition) }}" method="POST" class="flex-1">
                                @csrf
                                <button type="submit" class="btn btn-success w-full" onclick="return confirm('Êtes-vous sûr de vouloir accepter cette proposition ?')">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    Accepter la proposition
                                </button>
                            </form>
                            <form action="{{ route('proposition.membre.refuser', $proposition) }}" method="POST" class="flex-1">
                                @csrf
                                <button type="submit" class="btn btn-danger w-full" onclick="return confirm('Êtes-vous sûr de vouloir refuser cette proposition ?')">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                    Refuser la proposition
                                </button>
                            </form>
                        </div>
                    @elseif($proposition->statut && $proposition->statut->titre === 'Acceptée')
                        <div class="bg-green-50 dark:bg-green-900/20 rounded-lg p-4 text-center">
                            <svg class="w-12 h-12 mx-auto text-green-600 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <p class="text-green-800 dark:text-green-200 font-medium">Cette proposition a été acceptée</p>
                        </div>
                    @elseif($proposition->statut && $proposition->statut->titre === 'Refusée')
                        <div class="bg-red-50 dark:bg-red-900/20 rounded-lg p-4 text-center">
                            <svg class="w-12 h-12 mx-auto text-red-600 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <p class="text-red-800 dark:text-red-200 font-medium">Cette proposition a été refusée</p>
                        </div>
                    @endif
                </div>
            </div>

            <div class="col-span-12 py-6 lg:sticky lg:bottom-0 lg:col-span-4 lg:self-end">
                @include('layouts.sidebar')
            </div>    
        </div>
    </main>
</x-app-layout>
