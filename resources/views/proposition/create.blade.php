<x-app-layout title="Faire une proposition" is-sidebar-open="true" is-header-blur="true">
    <main class="main-content w-full px-[var(--margin-x)] pb-8">
        <div class="flex items-center space-x-4 py-5 lg:py-6">
            <h2 class="text-xl font-medium text-slate-800 dark:text-navy-50 lg:text-2xl">
                Faire une proposition pour ce plan
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
                            Plan d'accompagnement
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                            <div>
                                <span class="font-medium text-slate-600 dark:text-slate-300">Objectif:</span>
                                <span class="text-slate-800 dark:text-white ml-2">
                                    {{ $plan->objectif ? Str::limit($plan->objectif, 100) : 'Non défini' }}
                                </span>
                            </div>
                            <div>
                                <span class="font-medium text-slate-600 dark:text-slate-300">Entreprise:</span>
                                <span class="text-slate-800 dark:text-white ml-2">
                                    {{ $plan->accompagnement->entreprise->nom ?? 'Non spécifiée' }}
                                </span>
                            </div>
                            <div>
                                <span class="font-medium text-slate-600 dark:text-slate-300">Membre responsable:</span>
                                <span class="text-slate-800 dark:text-white ml-2">
                                    {{ $plan->accompagnement->membre->prenom ?? '' }} {{ $plan->accompagnement->membre->nom ?? 'Non spécifié' }}
                                </span>
                            </div>
                            <div>
                                <span class="font-medium text-slate-600 dark:text-slate-300">Secteur:</span>
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium 
                                    {{ $plan->accompagnement->entreprise->secteur->id === $expert->secteur_id 
                                        ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' 
                                        : 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200' }}">
                                    {{ $plan->accompagnement->entreprise->secteur->titre ?? 'Non spécifié' }}
                                </span>
                            </div>
                            @if($plan->dateplan)
                                <div>
                                    <span class="font-medium text-slate-600 dark:text-slate-300">Date du plan:</span>
                                    <span class="text-slate-800 dark:text-white ml-2">
                                        {{ \Carbon\Carbon::parse($plan->dateplan)->format('d/m/Y') }}
                                    </span>
                                </div>
                            @endif
                        </div>
                            <div>
                                <span class="font-medium text-slate-600 dark:text-slate-600">Action prioritaire:</span>
                                <span class="text-slate-800 dark:text-white ml-2">
                                    {{ $plan->actionprioritaire ?? 'Non défini' }}
                                </span>
                            </div>
                    </div>

                    {{-- Formulaire de proposition --}}
                    <form action="{{ route('proposition.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="plan_id" value="{{ $plan->id }}">
                        <input type="hidden" name="accompagnement_id" value="{{ $plan->accompagnement_id }}">

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            {{-- Message --}}
                            <div class="md:col-span-2">
                                <label class="block">
                                    <span class="text-sm font-medium text-slate-700 dark:text-slate-300">Message explicatif</span>
                                    <textarea name="message" rows="4" 
                                            class="form-textarea w-full rounded-lg border border-slate-300 bg-transparent p-3
                                                   placeholder:text-slate-400/70 hover:border-slate-400 focus:border-primary
                                                   dark:border-navy-450 dark:hover:border-navy-400 dark:focus:border-accent"
                                            placeholder="Décrivez votre proposition, vos compétences, votre approche...">{{ old('message') }}</textarea>
                                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                                        Expliquez pourquoi vous êtes le meilleur expert pour ce plan
                                    </p>
                                </label>
                            </div>

                            {{-- Prestation --}}
                            <div>
                                <label class="block">
                                    <span class="text-sm font-medium text-slate-700 dark:text-slate-300">Prestation proposée</span>
                                    <select name="prestation_id" 
                                            class="form-select mt-1.5 w-full rounded-lg border border-slate-300 bg-white px-3 py-2
                                                   hover:border-slate-400 focus:border-primary dark:border-navy-450
                                                   dark:bg-navy-700 dark:hover:border-navy-400 dark:focus:border-accent">
                                        <option value="">-- Choisir une prestation --</option>
                                        @foreach($prestations as $prestation)
                                            <option value="{{ $prestation->id }}" {{ old('prestation_id') == $prestation->id ? 'selected' : '' }}>
                                                {{ $prestation->titre }}
                                            </option>
                                        @endforeach
                                    </select>
                                </label>
                            </div>

                            {{-- Prix --}}
                            <div>
                                <label class="block">
                                    <span class="text-sm font-medium text-slate-700 dark:text-slate-300">Prix proposé (F CFA)</span>
                                    <input type="number" name="prix_propose" step="0.01" min="0"
                                            class="form-input mt-1.5 w-full rounded-lg border border-slate-300 bg-transparent px-3 py-2
                                                   placeholder:text-slate-400/70 hover:border-slate-400 focus:border-primary
                                                   dark:border-navy-450 dark:hover:border-navy-400 dark:focus:border-accent"
                                            placeholder="Ex: 50000" value="{{ old('prix_propose') }}">
                                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                                        Laissez vide si vous souhaitez discuter du prix
                                    </p>
                                </label>
                            </div>

                            {{-- Durée --}}
                            <div>
                                <label class="block">
                                    <span class="text-sm font-medium text-slate-700 dark:text-slate-300">Durée prévue (jours)</span>
                                    <input type="number" name="duree_prevue" min="1" max="365"
                                            class="form-input mt-1.5 w-full rounded-lg border border-slate-300 bg-transparent px-3 py-2
                                                   placeholder:text-slate-400/70 hover:border-slate-400 focus:border-primary
                                                   dark:border-navy-450 dark:hover:border-navy-400 dark:focus:border-accent"
                                            placeholder="Ex: 30" value="{{ old('duree_prevue') }}">
                                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                                        Durée estimée pour réaliser la prestation
                                    </p>
                                </label>
                            </div>

                            {{-- Date d'expiration --}}
                            <div>
                                <label class="block">
                                    <span class="text-sm font-medium text-slate-700 dark:text-slate-300">Date d'expiration de l'offre</span>
                                    <input type="date" name="date_expiration"
                                            class="form-input mt-1.5 w-full rounded-lg border border-slate-300 bg-transparent px-3 py-2
                                                   placeholder:text-slate-400/70 hover:border-slate-400 focus:border-primary
                                                   dark:border-navy-450 dark:hover:border-navy-400 dark:focus:border-accent"
                                            value="{{ old('date_expiration') ?? \Carbon\Carbon::now()->addDays(7)->format('Y-m-d') }}"
                                            min="{{ \Carbon\Carbon::tomorrow()->format('Y-m-d') }}">
                                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                                        Date limite de validité de votre proposition
                                    </p>
                                </label>
                            </div>
                        </div>

                        {{-- Boutons d'action --}}
                        <div class="flex justify-end space-x-3 pt-6 border-t border-slate-200 dark:border-navy-600">
                            <a href="{{ route('expert.plans.show', $plan->id) }}" 
                               class="btn bg-slate-150 text-slate-700 hover:bg-slate-200 dark:bg-navy-700 dark:text-slate-300 dark:hover:bg-navy-600">
                                Annuler
                            </a>
                            <button type="submit" 
                                    class="btn bg-primary text-white hover:bg-primary-focus focus:bg-primary-focus
                                           active:bg-primary-focus/90 dark:bg-accent dark:hover:bg-accent-focus 
                                           dark:focus:bg-accent-focus dark:active:bg-accent/90">
                                Envoyer ma proposition
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Sidebar --}}
            <div class="col-span-12 py-6 lg:sticky lg:bottom-0 lg:col-span-4 lg:self-end">
                @include('layouts.sidebar')
            </div>
        </div>
    </main>
</x-app-layout>
