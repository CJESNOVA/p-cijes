<x-app-layout title="Propositions reçues" is-sidebar-open="true" is-header-blur="true">
    <main class="main-content w-full px-[var(--margin-x)] pb-8">
        <!-- Header moderne -->
        <div class="mb-2">
            <div class="flex items-center gap-4 mb-2">
                <div class="h-14 w-14 rounded-xl bg-gradient-to-br from-purple-500 to-purple-600 flex items-center justify-center shadow-lg">
                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2 2v5a2 2 0 012 2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707L19.586 16.414A1 1 0 0118 17V11a2 2 0 00-2-2H7a2 2 0 00-2 2v5a2 2 0 002 2z"></path>
                    </svg>
                </div>
                <div>
                    <h1 class="text-3xl font-bold text-slate-800 dark:text-navy-50">
                        Propositions reçues
                    </h1>
                    <p class="mt-2 text-slate-600 dark:text-navy-200 text-lg">
                        Gérez les offres et propositions que vous avez reçues
                    </p>
                </div>
            </div>
        </div>
        <div class="grid grid-cols-12 lg:gap-6">
            <div class="col-span-12 pt-6 lg:col-span-8 lg:pb-6">
                
                <!-- Messages modernes -->
                @if (session('success'))
                    <div class="alert flex rounded-lg bg-green-500 px-6 py-4 text-white mb-6 shadow-lg">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        {{ session('success') }}
                    </div>
                @endif

                @if (session('error'))
                    <div class="alert flex rounded-lg bg-red-500 px-6 py-4 text-white mb-6 shadow-lg">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        {{ session('error') }}
                    </div>
                @endif

                {{-- Liste des propositions --}}
                <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-2 gap-6">
                    @forelse($propositions as $proposition)
                        <div class="card bg-white dark:bg-navy-800 rounded-xl shadow-lg hover:shadow-xl transition-shadow duration-300 overflow-hidden">
                            {{-- En-tête avec statut --}}
                            <div class="relative">
                                <div class="h-32 bg-gradient-to-r from-blue-500 to-purple-600 flex items-center justify-center">
                                    <div class="text-white text-center">
                                        <svg class="w-12 h-12 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2 2v5a2 2 0 012 2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707L19.586 16.414A1 1 0 0118 17V11a2 2 0 00-2-2H7a2 2 0 00-2 2v5a2 2 0 002 2z"></path>
                                        </svg>
                                        <p class="text-sm font-medium">Proposition</p>
                                    </div>
                                    
                                    {{-- Badge statut --}}
                                    <div class="absolute top-4 right-4">
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
                                </div>
                            </div>

                            {{-- Contenu --}}
                            <div class="p-6 space-y-4">
                                {{-- Expert --}}
                                <div class="flex items-center space-x-3">
                                    <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 font-bold">
                                        {{ substr($proposition->expert->membre->nom ?? 'E', 0, 1) }}{{ substr($proposition->expert->membre->prenom ?? 'X', 0, 1) }}
                                    </div>
                                    <div>
                                        <p class="font-medium text-slate-800 dark:text-white">
                                            {{ $proposition->expert->membre->nom ?? '' }} {{ $proposition->expert->membre->prenom ?? '' }}
                                        </p>
                                        <p class="text-sm text-slate-500 dark:text-slate-400">Expert</p>
                                    </div>
                                </div>

                                {{-- Prestation --}}
                                @if($proposition->prestation)
                                    <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-3">
                                        <p class="text-sm font-medium text-blue-800 dark:text-blue-200 mb-1">Prestation proposée</p>
                                        <p class="font-medium text-slate-800 dark:text-white">{{ $proposition->prestation->titre }}</p>
                                        @if($proposition->prestation->prix)
                                            <p class="text-sm text-slate-600 dark:text-slate-400">Prix: {{ number_format($proposition->prestation->prix, 2, ',', ' ') }} €</p>
                                        @endif
                                        @if($proposition->prestation->duree)
                                            <p class="text-sm text-slate-600 dark:text-slate-400">Durée: {{ $proposition->prestation->duree }}</p>
                                        @endif
                                    </div>
                                @endif

                                {{-- Message --}}
                                @if($proposition->message)
                                    <div>
                                        <p class="text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Message de l'expert:</p>
                                        <p class="text-sm text-slate-600 dark:text-slate-400">{{ $proposition->message }}</p>
                                    </div>
                                @endif

                                {{-- Prix et durée proposés --}}
                                <div class="grid grid-cols-2 gap-4">
                                    @if($proposition->prix_propose)
                                        <div>
                                            <p class="text-sm text-slate-500 dark:text-slate-400">Prix proposé</p>
                                            <p class="font-medium text-slate-800 dark:text-white">{{ number_format($proposition->prix_propose, 2, ',', ' ') }} €</p>
                                        </div>
                                    @endif
                                    @if($proposition->duree_prevue)
                                        <div>
                                            <p class="text-sm text-slate-500 dark:text-slate-400">Durée prévue</p>
                                            <p class="font-medium text-slate-800 dark:text-white">{{ $proposition->duree_prevue }} jours</p>
                                        </div>
                                    @endif
                                </div>

                                {{-- Date --}}
                                <div class="text-xs text-slate-500 dark:text-slate-400">
                                    Reçue le {{ $proposition->created_at->format('d/m/Y à H:i') }}
                                    @if($proposition->date_expiration)
                                        • Expire le {{ $proposition->date_expiration->format('d/m/Y') }}
                                    @endif
                                </div>

                                {{-- Actions --}}
                                @if($proposition->statut && $proposition->statut->titre === 'En attente')
                                    <div class="flex space-x-2 pt-2">
                                        <a href="{{ route('proposition.membre.show', $proposition) }}" 
                                           class="btn btn-primary flex-1 text-sm">
                                            Voir les détails
                                        </a>
                                        <form action="{{ route('proposition.membre.accepter', $proposition) }}" method="POST" class="flex-1">
                                            @csrf
                                            <button type="submit" class="btn btn-success w-full text-sm">
                                                Accepter
                                            </button>
                                        </form>
                                        <form action="{{ route('proposition.membre.refuser', $proposition) }}" method="POST" class="flex-1">
                                            @csrf
                                            <button type="submit" class="btn btn-danger w-full text-sm">
                                                Refuser
                                            </button>
                                        </form>
                                    </div>
                                @else
                                    <div class="pt-2">
                                        <a href="{{ route('proposition.membre.show', $proposition) }}" 
                                           class="btn btn-outline w-full text-sm">
                                            Voir les détails
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="col-span-full">
                            <div class="card bg-white dark:bg-navy-800 rounded-xl shadow-lg p-8 text-center">
                                <svg class="w-16 h-16 mx-auto text-slate-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                                </svg>
                                <h3 class="text-lg font-medium text-slate-800 dark:text-white mb-2">Aucune proposition reçue</h3>
                                <p class="text-slate-600 dark:text-slate-400">Vous n'avez reçu aucune proposition pour vos plans d'accompagnement.</p>
                            </div>
                        </div>
                    @endforelse
                </div>
            </div>

            <div class="col-span-12 py-6 lg:sticky lg:bottom-0 lg:col-span-4 lg:self-end">
                @include('layouts.sidebar')
            </div>    
        </div>
    </main>
</x-app-layout>
