<x-app-layout title="Détails de ma proposition" is-sidebar-open="true" is-header-blur="true">
    <main class="main-content w-full px-[var(--margin-x)] pb-8">
        <div class="flex items-center space-x-4 py-5 lg:py-6">
            <h2 class="text-xl font-medium text-slate-800 dark:text-navy-50 lg:text-2xl">
                Détails de ma proposition
            </h2>
            <div class="hidden h-full py-1 sm:flex">
                <div class="h-full w-px bg-slate-300 dark:bg-navy-600"></div>
            </div>
        </div>

        {{-- Message succès --}}
        @if (session('success'))
            <div class="alert flex rounded-lg bg-success px-4 py-4 text-white sm:px-5 mb-4">
                {{ session('success') }}
            </div>
        @endif

        <div class="grid grid-cols-12 lg:gap-6">
            <div class="col-span-12 pt-6 lg:col-span-8 lg:pb-6">
                <div class="card bg-white dark:bg-navy-800 rounded-xl shadow-lg p-6 space-y-6">
                    {{-- Informations du plan --}}
                    <div class="bg-slate-50 dark:bg-navy-700 rounded-lg p-4 mb-6">
                        <h3 class="text-lg font-semibold text-slate-800 dark:text-white mb-3 flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                            </svg>
                            Plan d'accompagnement concerné
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                            <div>
                                <span class="font-medium text-slate-600 dark:text-slate-300">Objectif:</span>
                                <span class="text-slate-800 dark:text-white ml-2">
                                    {{ $proposition->plan && $proposition->plan->objectif ? $proposition->plan->objectif : 'Non défini' }}
                                </span>
                            </div>
                            <div>
                                <span class="font-medium text-slate-600 dark:text-slate-300">Entreprise:</span>
                                <span class="text-slate-800 dark:text-white ml-2">
                                    {{ $proposition->accompagnement && $proposition->accompagnement->entreprise ? $proposition->accompagnement->entreprise->nom : 'Non spécifiée' }}
                                </span>
                            </div>
                            <div>
                                <span class="font-medium text-slate-600 dark:text-slate-300">Membre responsable:</span>
                                <span class="text-slate-800 dark:text-white ml-2">
                                    {{ $proposition->accompagnement && $proposition->accompagnement->membre ? ($proposition->accompagnement->membre->prenom ?? '') . ' ' . ($proposition->accompagnement->membre->nom ?? '') : 'Non spécifié' }}
                                </span>
                            </div>
                            <div>
                                <span class="font-medium text-slate-600 dark:text-slate-300">Date du plan:</span>
                                <span class="text-slate-800 dark:text-white ml-2">
                                    {{ $proposition->plan && $proposition->plan->dateplan ? $proposition->plan->dateplan->format('d/m/Y') : 'Non spécifiée' }}
                                </span>
                            </div>
                        </div>
                    </div>

                    {{-- Détails de la proposition --}}
                    <div>
                        <h3 class="text-lg font-semibold text-slate-800 dark:text-white mb-4">Ma proposition</h3>
                        
                        <div class="space-y-4">
                            {{-- Message --}}
                            @if($proposition->message)
                                <div>
                                    <span class="font-medium text-slate-600 dark:text-slate-300">Message explicatif:</span>
                                    <p class="text-slate-800 dark:text-white mt-2 p-4 bg-slate-50 dark:bg-navy-700 rounded-lg">
                                        {{ $proposition->message }}
                                    </p>
                                </div>
                            @endif

                            {{-- Prestation --}}
                            @if($proposition->prestation)
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <span class="font-medium text-slate-600 dark:text-slate-300">Prestation proposée:</span>
                                        <span class="text-slate-800 dark:text-white ml-2">{{ $proposition->prestation->titre }}</span>
                                    </div>
                                    <div>
                                        <span class="font-medium text-slate-600 dark:text-slate-300">Prix proposé:</span>
                                        <span class="text-slate-800 dark:text-white ml-2">{{ $proposition->prix_propose_formate ?? 'Non spécifié' }}</span>
                                    </div>
                                </div>
                            @endif

                            {{-- Durée et dates --}}
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <span class="font-medium text-slate-600 dark:text-slate-300">Durée prévue:</span>
                                    <span class="text-slate-800 dark:text-white ml-2">
                                        {{ $proposition->duree_prevue ? $proposition->duree_prevue . ' jours' : 'Non spécifiée' }}
                                    </span>
                                </div>
                                <div>
                                    <span class="font-medium text-slate-600 dark:text-slate-300">Date d'expiration:</span>
                                    <span class="text-slate-800 dark:text-white ml-2">
                                        {{ $proposition->date_expiration_formatee ?? 'Non spécifiée' }}
                                    </span>
                                </div>
                            </div>

                            {{-- Statut --}}
                            <div>
                                <span class="font-medium text-slate-600 dark:text-slate-300">Statut actuel:</span>
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium ml-2
                                    {{ $proposition->statut && $proposition->statut->titre === 'Acceptée' 
                                        ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' 
                                        : ($proposition->statut && $proposition->statut->titre === 'Refusée' 
                                            ? 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200'
                                            : ($proposition->statut && $proposition->statut->titre === 'Annulée'
                                                ? 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200'
                                                : 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200')) }}">
                                    {{ $proposition->statut ? $proposition->statut->titre : 'En attente' }}
                                </span>
                                @if($proposition->isExpired())
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium ml-2 bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">
                                        Expirée
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Actions --}}
                    <div class="flex justify-end space-x-3 pt-6 border-t border-slate-200 dark:border-navy-600">
                        <a href="{{ route('proposition.index') }}" 
                           class="btn bg-slate-150 text-slate-700 hover:bg-slate-200 dark:bg-navy-700 dark:text-slate-300 dark:hover:bg-navy-600">
                            Retour à la liste
                        </a>
                        @if($proposition->isEnAttente())
                            <a href="{{ route('proposition.edit', $proposition->id) }}" 
                               class="btn bg-warning text-white hover:bg-warning-focus">
                                Modifier
                            </a>
                            <form action="{{ route('proposition.destroy', $proposition->id) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" 
                                        onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette proposition ?')"
                                        class="btn bg-danger text-white hover:bg-danger-focus">
                                    Supprimer
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Sidebar --}}
            <div class="col-span-12 py-6 lg:sticky lg:bottom-0 lg:col-span-4 lg:self-end">
                @include('layouts.sidebar')
            </div>
        </div>
    </main>
</x-app-layout>
