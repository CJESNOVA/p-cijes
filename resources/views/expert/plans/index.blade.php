<x-app-layout title="Plans d'accompagnement" is-sidebar-open="true" is-header-blur="true">
    <main class="main-content w-full px-[var(--margin-x)] pb-8">
        <div class="flex items-center space-x-4 py-5 lg:py-6">
            <h2 class="text-xl font-medium text-slate-800 dark:text-navy-50 lg:text-2xl">
                Plans d'accompagnement disponibles
            </h2>
            <div class="hidden h-full py-1 sm:flex">
                <div class="h-full w-px bg-slate-300 dark:bg-navy-600"></div>
            </div>
        </div>

        <div class="grid grid-cols-12 lg:gap-6">
            <div class="col-span-12 pt-6 lg:col-span-8 lg:pb-6">
                {{-- Statistiques --}}
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
            <div class="card bg-white dark:bg-navy-800 p-4 rounded-lg shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-slate-500 dark:text-slate-400">Total des plans</p>
                        <p class="text-2xl font-bold text-slate-800 dark:text-white">{{ $stats['total_plans'] }}</p>
                    </div>
                    <div class="w-12 h-12 bg-[#152737]/20 rounded-full flex items-center justify-center">
                        <svg class="w-6 h-6 text-[#152737]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="card bg-white dark:bg-navy-800 p-4 rounded-lg shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-slate-500 dark:text-slate-400">Votre secteur</p>
                        <p class="text-2xl font-bold text-[#4FBE96] dark:text-[#4FBE96]">{{ $stats['plans_meme_secteur'] }}</p>
                    </div>
                    <div class="w-12 h-12 bg-[#4FBE96]/20 rounded-full flex items-center justify-center">
                        <svg class="w-6 h-6 text-[#4FBE96]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="card bg-white dark:bg-navy-800 p-4 rounded-lg shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-slate-500 dark:text-slate-400">Autres secteurs</p>
                        <p class="text-2xl font-bold text-[#152737] dark:text-[#152737]">{{ $stats['plans_autres_secteurs'] }}</p>
                    </div>
                    <div class="w-12 h-12 bg-[#152737]/20 rounded-full flex items-center justify-center">
                        <svg class="w-6 h-6 text-[#152737]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path>
                        </svg>
                    </div>
                </div>
            </div>
            </div>

        {{-- Message succès --}}
        @if (session('success'))
            <div class="alert flex rounded-lg bg-success px-4 py-4 text-white sm:px-5 mb-4">
                {{ session('success') }}
            </div>
        @endif

        {{-- Liste des plans --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-2 gap-6">
            @forelse($plans as $plan)
                <div class="card bg-white dark:bg-navy-800 rounded-xl shadow-lg hover:shadow-xl transition-shadow duration-300 overflow-hidden">
                    {{-- En-tête avec badge de secteur --}}
                    <div class="relative">
                        @if($plan->accompagnement->entreprise->secteur->id === $expert->secteur_id)
                            <div class="absolute top-2 right-2 z-10">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-[#4FBE96]/20 text-[#4FBE96] dark:bg-[#4FBE96]/90 dark:text-[#4FBE96]">
                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                    </svg>
                                    Votre secteur
                                </span>
                            </div>
                        @endif
                        
                        <div class="h-32 bg-gradient-to-r from-blue-500 to-purple-600 flex items-center justify-center">
                            <div class="text-white text-center">
                                <svg class="w-12 h-12 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                                </svg>
                                <p class="text-sm font-medium">Plan d'accompagnement</p>
                            </div>
                        </div>
                    </div>

                    <div class="p-6 space-y-4">
                        {{-- Informations principales --}}
                        <div>
                            <h3 class="text-lg font-semibold text-slate-800 dark:text-white mb-2">
                                {{ $plan->objectif ? Str::limit($plan->objectif, 60) : 'Objectif non défini' }}
                            </h3>
                            
                            <div class="space-y-2 text-sm">
                                <div class="flex items-center text-slate-600 dark:text-slate-300">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                    </svg>
                                    <span class="font-medium">Entreprise:</span>
                                    {{ $plan->accompagnement->entreprise->nom ?? 'Non spécifiée' }}
                                </div>
                                
                                <div class="flex items-center text-slate-600 dark:text-slate-300">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                    <span class="font-medium">Membre:</span>
                                    {{ $plan->accompagnement->membre->prenom ?? '' }} {{ $plan->accompagnement->membre->nom ?? 'Non spécifié' }}
                                </div>
                                
                                <div class="flex items-center text-slate-600 dark:text-slate-300">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                                    </svg>
                                    <span class="font-medium">Secteur:</span>
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium 
                                        {{ $plan->accompagnement->entreprise->secteur->id === $expert->secteur_id 
                                            ? 'bg-[#4FBE96]/20 text-[#4FBE96] dark:bg-[#4FBE96]/90 dark:text-[#4FBE96]' 
                                            : 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200' }}">
                                        {{ $plan->accompagnement->entreprise->secteur->titre ?? 'Non spécifié' }}
                                    </span>
                                </div>
                                
                                @if($plan->dateplan)
                                    <div class="flex items-center text-slate-600 dark:text-slate-300">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                        <span class="font-medium">Date:</span>
                                        {{ \Carbon\Carbon::parse($plan->dateplan)->format('d/m/Y') }}
                                    </div>
                                @endif
                            </div>
                        </div>

                        {{-- Actions prioritaires --}}
                        @if($plan->actionprioritaire)
                            <div class="bg-[#152737]/10 dark:bg-[#152737]/20 border border-[#152737]/30 dark:border-[#152737]/80 rounded-lg p-3">
                                <p class="text-sm font-medium text-[#152737] dark:text-[#152737] mb-1">Action prioritaire:</p>
                                <p class="text-xs text-[#152737] dark:text-[#152737]">{{ Str::limit($plan->actionprioritaire, 100) }}</p>
                            </div>
                        @endif

                        {{-- Actions --}}
                        <div class="flex space-x-2 pt-2">
                            <a href="{{ route('expert.plans.show', $plan->id) }}" 
                               class="flex-1 btn bg-primary text-white hover:bg-primary-focus text-sm py-2 px-3 rounded-lg transition-colors">
                                Voir détails
                            </a>
                            <a href="{{ route('proposition.create', $plan->id) }}" 
                               class="flex-1 btn bg-success text-white hover:bg-success-focus text-sm py-2 px-3 rounded-lg transition-colors">
                                Faire une proposition
                            </a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full">
                    <div class="card bg-white dark:bg-navy-800 rounded-xl shadow-lg p-8 text-center">
                        <svg class="w-16 h-16 mx-auto text-slate-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                        <h3 class="text-lg font-medium text-slate-800 dark:text-white mb-2">Aucun plan disponible</h3>
                        <p class="text-slate-600 dark:text-slate-400">Il n'y a actuellement aucun plan d'accompagnement disponible.</p>
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
