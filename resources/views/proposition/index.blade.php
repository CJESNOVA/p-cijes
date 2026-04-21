<x-app-layout title="Mes propositions" is-sidebar-open="true" is-header-blur="true">
    <main class="main-content w-full px-[var(--margin-x)] pb-8">
        <div class="flex items-center space-x-4 py-5 lg:py-6">
            <h2 class="text-xl font-medium text-slate-800 dark:text-navy-50 lg:text-2xl">
                Mes propositions
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

        {{-- Liste des propositions --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-2 gap-6">
            @forelse($propositions as $proposition)
                <div class="card bg-white dark:bg-navy-800 rounded-xl shadow-lg hover:shadow-xl transition-shadow duration-300 overflow-hidden">
                    {{-- En-tête avec statut --}}
                    <div class="relative">
                        <div class="h-32 bg-gradient-to-r from-[#4FBE96] to-[#4FBE96] flex items-center justify-center">
                            <div class="text-white text-center">
                                <svg class="w-12 h-12 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2 2v5a2 2 0 012 2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707L19.586 16.414A1 1 0 0118 17V11a2 2 0 00-2-2H7a2 2 0 00-2 2v5a2 2 0 002 2z"></path>
                                </svg>
                                <p class="text-sm font-medium">Proposition</p>
                            </div>
                        </div>
                        
                        {{-- Badge de statut --}}
                        <div class="absolute top-2 right-2 z-10">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                {{ $proposition->statut->titre === 'Acceptée' 
                                    ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' 
                                    : ($proposition->statut->titre === 'Refusée' 
                                        ? 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200'
                                        : 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200') }}">
                                {{ $proposition->statut->titre ?? 'En attente' }}
                            </span>
                        </div>
                    </div>

                    <div class="p-6 space-y-4">
                        {{-- Informations principales --}}
                        <div>
                            <h3 class="text-lg font-semibold text-slate-800 dark:text-white mb-2">
                                {{ $proposition->plan && $proposition->plan->objectif ? Str::limit($proposition->plan->objectif, 60) : 'Objectif non défini' }}
                            </h3>
                            
                            <div class="space-y-2 text-sm">
                                <div class="flex items-center text-slate-600 dark:text-slate-300">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                    </svg>
                                    <span class="font-medium">Entreprise:</span>
                                    {{ $proposition->accompagnement && $proposition->accompagnement->entreprise ? $proposition->accompagnement->entreprise->nom : 'Non spécifiée' }}
                                </div>
                                
                                <div class="flex items-center text-slate-600 dark:text-slate-300">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                    <span class="font-medium">Membre:</span>
                                    {{ $proposition->accompagnement && $proposition->accompagnement->membre ? ($proposition->accompagnement->membre->prenom ?? '') . ' ' . ($proposition->accompagnement->membre->nom ?? '') : 'Non spécifié' }}
                                </div>
                                
                                @if($proposition->prestation)
                                    <div class="flex items-center text-slate-600 dark:text-slate-300">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                        </svg>
                                        <span class="font-medium">Prestation:</span>
                                        {{ $proposition->prestation->titre }}
                                    </div>
                                @endif
                            </div>
                        </div>

                        {{-- Détails de la proposition --}}
                        @if($proposition->message || $proposition->prix_propose || $proposition->duree_prevue)
                            <div class="bg-slate-50 dark:bg-navy-700 rounded-lg p-4">
                                <h4 class="text-sm font-semibold text-slate-800 dark:text-white mb-2">Détails de votre proposition</h4>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                                    @if($proposition->message)
                                        <div class="md:col-span-2">
                                            <span class="font-medium text-slate-600 dark:text-slate-300">Message:</span>
                                            <p class="text-slate-700 dark:text-slate-300 mt-1">{{ Str::limit($proposition->message, 150) }}</p>
                                        </div>
                                    @endif
                                    
                                    @if($proposition->prix_propose)
                                        <div>
                                            <span class="font-medium text-slate-600 dark:text-slate-300">Prix proposé:</span>
                                            <span class="text-slate-700 dark:text-slate-300">{{ number_format($proposition->prix_propose, 0, ',', ' ') }} F CFA</span>
                                        </div>
                                    @endif
                                    
                                    @if($proposition->duree_prevue)
                                        <div>
                                            <span class="font-medium text-slate-600 dark:text-slate-300">Durée prévue:</span>
                                            <span class="text-slate-700 dark:text-slate-300">{{ $proposition->duree_prevue }} jours</span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endif

                        {{-- Dates --}}
                        <div class="flex items-center justify-between text-xs text-slate-500 dark:text-slate-400 pt-2 border-t border-slate-200 dark:border-navy-600">
                            <div>
                                <span>Proposée le: {{ $proposition->date_proposition_formatee }}</span>
                            </div>
                            @if($proposition->date_expiration)
                                <div>
                                    @if($proposition->isExpired())
                                        <span class="text-red-600 dark:text-red-400">⚠️ Expirée le: {{ $proposition->date_expiration_formatee }}</span>
                                    @else
                                        <span>Valide jusqu'au: {{ $proposition->date_expiration_formatee }}</span>
                                    @endif
                                </div>
                            @endif
                        </div>

                        {{-- Actions --}}
                        <div class="flex space-x-2 pt-2">
                            <a href="{{ route('proposition.show', $proposition->id) }}" 
                               class="flex-1 btn bg-primary text-white hover:bg-primary-focus text-sm py-2 px-3 rounded-lg transition-colors">
                                Voir détails
                            </a>
                            @if($proposition->isEnAttente())
                                <a href="{{ route('proposition.edit', $proposition->id) }}" 
                                   class="flex-1 btn bg-warning text-white hover:bg-warning-focus text-sm py-2 px-3 rounded-lg transition-colors">
                                    Modifier
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full">
                    <div class="card bg-white dark:bg-navy-800 rounded-xl shadow-lg p-8 text-center">
                        <svg class="w-16 h-16 mx-auto text-slate-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2 2v5a2 2 0 012 2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707L19.586 16.414A1 1 0 0118 17V11a2 2 0 00-2-2H7a2 2 0 00-2 2v5a2 2 0 002 2z"></path>
                        </svg>
                        <h3 class="text-lg font-medium text-slate-800 dark:text-white mb-2">Aucune proposition</h3>
                        <p class="text-slate-600 dark:text-slate-400">Vous n'avez pas encore fait de proposition.</p>
                        <a href="{{ route('expert.plans.index') }}" 
                           class="btn bg-primary text-white hover:bg-primary-focus mt-4">
                            Voir les plans disponibles
                        </a>
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
