<x-app-layout title="Détails du plan d'accompagnement" is-sidebar-open="true" is-header-blur="true">
    <main class="main-content w-full px-[var(--margin-x)] pb-8">
        <div class="flex items-center space-x-4 py-5 lg:py-6">
            <h2 class="text-xl font-medium text-slate-800 dark:text-navy-50 lg:text-2xl">
                Détails du plan d'accompagnement
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
                    {{-- En-tête du plan --}}
                    <div class="border-b border-slate-200 dark:border-navy-600 pb-4">
                        <div class="flex items-start justify-between">
                            <div>
                                <h1 class="text-2xl font-bold text-slate-800 dark:text-white mb-2">
                                    {{ $plan->objectif ?? 'Objectif non défini' }}
                                </h1>
                                <div class="flex items-center space-x-4 text-sm text-slate-600 dark:text-slate-300">
                                    @if($plan->accompagnement->entreprise->secteur->id === $expert->secteur_id)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                            </svg>
                                            Votre secteur
                                        </span>
                                    @endif
                                    
                                    @if($plan->dateplan)
                                        <span class="flex items-center">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                            </svg>
                                            {{ \Carbon\Carbon::parse($plan->dateplan)->format('d/m/Y') }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Informations sur l'entreprise et le membre --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="bg-slate-50 dark:bg-navy-700 rounded-lg p-4">
                            <h3 class="text-lg font-semibold text-slate-800 dark:text-white mb-3 flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                </svg>
                                Entreprise
                            </h3>
                            <div class="space-y-2">
                                <p class="font-medium text-slate-800 dark:text-white">
                                    {{ $plan->accompagnement->entreprise->nom ?? 'Non spécifiée' }}
                                </p>
                                <p class="text-sm text-slate-600 dark:text-slate-300">
                                    Secteur: {{ $plan->accompagnement->entreprise->secteur->titre ?? 'Non spécifié' }}
                                </p>
                                @if($plan->accompagnement->entreprise->description)
                                    <p class="text-sm text-slate-600 dark:text-slate-300">
                                        {{ Str::limit($plan->accompagnement->entreprise->description, 150) }}
                                    </p>
                                @endif
                            </div>
                        </div>

                        <div class="bg-slate-50 dark:bg-navy-700 rounded-lg p-4">
                            <h3 class="text-lg font-semibold text-slate-800 dark:text-white mb-3 flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                                Membre responsable
                            </h3>
                            <div class="space-y-2">
                                <p class="font-medium text-slate-800 dark:text-white">
                                    {{ $plan->accompagnement->membre->prenom ?? '' }} {{ $plan->accompagnement->membre->nom ?? 'Non spécifié' }}
                                </p>
                                @if($plan->accompagnement->membre->email)
                                    <p class="text-sm text-slate-600 dark:text-slate-300">
                                        {{ $plan->accompagnement->membre->email }}
                                    </p>
                                @endif
                                @if($plan->accompagnement->membre->telephone)
                                    <p class="text-sm text-slate-600 dark:text-slate-300">
                                        {{ $plan->accompagnement->membre->telephone }}
                                    </p>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Action prioritaire --}}
                    @if($plan->actionprioritaire)
                        <div>
                            <h3 class="text-lg font-semibold text-slate-800 dark:text-white mb-3 flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                </svg>
                                Action prioritaire
                            </h3>
                            <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
                                <p class="text-slate-700 dark:text-blue-300">{{ $plan->actionprioritaire }}</p>
                            </div>
                        </div>
                    @endif

                    {{-- Informations sur l'accompagnement --}}
                    <div>
                        <h3 class="text-lg font-semibold text-slate-800 dark:text-white mb-3 flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            Contexte de l'accompagnement
                        </h3>
                        <div class="bg-slate-50 dark:bg-navy-700 rounded-lg p-4">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                                <div>
                                    <span class="font-medium text-slate-600 dark:text-slate-300">Niveau:</span>
                                    <span class="text-slate-800 dark:text-white ml-2">
                                        {{ $plan->accompagnement->accompagnementniveau->titre ?? 'Non spécifié' }}
                                    </span>
                                </div>
                                <div>
                                    <span class="font-medium text-slate-600 dark:text-slate-300">Statut:</span>
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium 
                                        {{ $plan->accompagnement->accompagnementstatut->couleur ?? 'bg-gray-100 text-gray-800' }}">
                                        {{ $plan->accompagnement->accompagnementstatut->titre ?? 'Non spécifié' }}
                                    </span>
                                </div>
                                <div>
                                    <span class="font-medium text-slate-600 dark:text-slate-300">Date d'accompagnement:</span>
                                    <span class="text-slate-800 dark:text-white ml-2">
                                        {{ \Carbon\Carbon::parse($plan->accompagnement->dateaccompagnement)->format('d/m/Y') }}
                                    </span>
                                </div>
                                <div>
                                    <span class="font-medium text-slate-600 dark:text-slate-300">Diagnostics associés:</span>
                                    <span class="text-slate-800 dark:text-white ml-2">
                                        {{ $plan->accompagnement->diagnostics->count() }} diagnostic(s)
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Actions --}}
                    <div class="flex space-x-3 pt-4 border-t border-slate-200 dark:border-navy-600">
                        <a href="{{ route('expert.plans.index') }}" 
                           class="btn bg-slate-150 text-slate-700 hover:bg-slate-200 dark:bg-navy-700 dark:text-slate-300 dark:hover:bg-navy-600">
                            Retour à la liste
                        </a>
                        {{-- TODO: Ajouter le bouton pour faire une proposition quand le système sera prêt --}}
                        {{-- <button class="btn bg-success text-white hover:bg-success-focus">
                            Faire une proposition pour ce plan
                        </button> --}}
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
