<x-app-layout title="Détails de l'abonnement payé" is-sidebar-open="true" is-header-blur="true">
    <main class="main-content w-full px-[var(--margin-x)] pb-8">
        <div class="flex items-center space-x-4 py-5 lg:py-6">
            <h2 class="text-xl font-medium text-slate-800 dark:text-navy-50 lg:text-2xl">
                Détails de l'abonnement payé
            </h2>
            <div class="hidden h-full py-1 sm:flex">
                <div class="h-full w-px bg-slate-300 dark:bg-navy-600"></div>
            </div>
        </div>

        <div class="grid grid-cols-12 lg:gap-6">
            <div class="col-span-12 pt-6 lg:col-span-8 lg:pb-6">
                
                <div class="card">
                    <div class="card-body">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Informations de l'abonnement -->
                            <div class="space-y-4">
                                <h3 class="text-lg font-semibold text-slate-800 dark:text-navy-100 mb-4">
                                    <i class="fas fa-receipt mr-2"></i>Informations de l'abonnement
                                </h3>
                                
                                <div class="space-y-3">
                                    <div>
                                        <span class="text-sm text-slate-600 dark:text-navy-400">Type d'abonnement</span>
                                        <p class="font-medium text-slate-800 dark:text-navy-100">
                                            {{ $abonnementressource->abonnement->abonnementtype->titre ?? '-' }}
                                        </p>
                                    </div>
                                    
                                    <div>
                                        <span class="text-sm text-slate-600 dark:text-navy-400">Entreprise</span>
                                        <p class="font-medium text-slate-800 dark:text-navy-100">
                                            {{ $abonnementressource->abonnement->entreprise->nom ?? '-' }}
                                        </p>
                                    </div>
                                    
                                    <div>
                                        <span class="text-sm text-slate-600 dark:text-navy-400">Montant payé</span>
                                        <p class="font-medium text-slate-800 dark:text-navy-100">
                                            {{ number_format($abonnementressource->montant, 2) }} XOF
                                        </p>
                                    </div>
                                    
                                    <div>
                                        <span class="text-sm text-slate-600 dark:text-navy-400">Date de paiement</span>
                                        <p class="font-medium text-slate-800 dark:text-navy-100">
                                            {{ $abonnementressource->created_at->format('d/m/Y H:i') }}
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <!-- Informations de la transaction -->
                            <div class="space-y-4">
                                <h3 class="text-lg font-semibold text-slate-800 dark:text-navy-100 mb-4">
                                    <i class="fas fa-wallet mr-2"></i>Informations de la transaction
                                </h3>
                                
                                <div class="space-y-3">
                                    <div>
                                        <span class="text-sm text-slate-600 dark:text-navy-400">Référence</span>
                                        <p class="font-medium text-slate-800 dark:text-navy-100">
                                            <code class="bg-slate-100 dark:bg-navy-800 px-2 py-1 rounded text-sm">
                                                {{ $abonnementressource->reference }}
                                            </code>
                                        </p>
                                    </div>
                                    
                                    <div>
                                        <span class="text-sm text-slate-600 dark:text-navy-400">Ressource utilisée</span>
                                        <p class="font-medium text-slate-800 dark:text-navy-100">
                                            KOBO - Solde: {{ number_format($abonnementressource->ressourcecompte->solde, 2) }} XOF
                                        </p>
                                    </div>
                                    
                                    <div>
                                        <span class="text-sm text-slate-600 dark:text-navy-400">Statut du paiement</span>
                                        <p>
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-success/10 text-success dark:bg-success/20 dark:text-success-light">
                                                <i class="fas fa-check-circle mr-1"></i>
                                                Payé avec succès
                                            </span>
                                        </p>
                                    </div>
                                    
                                    <div>
                                        <span class="text-sm text-slate-600 dark:text-navy-400">Membre</span>
                                        <p class="font-medium text-slate-800 dark:text-navy-100">
                                            {{ $abonnementressource->membre->prenom ?? '' }} {{ $abonnementressource->membre->nom ?? '' }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Période de l'abonnement -->
                        @if($abonnementressource->abonnement)
                        <div class="mt-6 pt-6 border-t border-slate-200 dark:border-navy-700">
                            <h3 class="text-lg font-semibold text-slate-800 dark:text-navy-100 mb-4">
                                <i class="fas fa-calendar mr-2"></i>Période de l'abonnement
                            </h3>
                            
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div>
                                    <span class="text-sm text-slate-600 dark:text-navy-400">Date de début</span>
                                    <p class="font-medium text-slate-800 dark:text-navy-100">
                                        {{ $abonnementressource->abonnement->date_debut->format('d/m/Y') }}
                                    </p>
                                </div>
                                
                                <div>
                                    <span class="text-sm text-slate-600 dark:text-navy-400">Date de fin</span>
                                    <p class="font-medium text-slate-800 dark:text-navy-100">
                                        {{ $abonnementressource->abonnement->date_fin->format('d/m/Y') }}
                                    </p>
                                </div>
                                
                                <div>
                                    <span class="text-sm text-slate-600 dark:text-navy-400">Date d'échéance</span>
                                    <p class="font-medium text-slate-800 dark:text-navy-100">
                                        {{ $abonnementressource->abonnement->date_echeance->format('d/m/Y') }}
                                    </p>
                                </div>
                            </div>
                        </div>
                        @endif

                        <div class="flex justify-end space-x-4 mt-6 pt-6 border-t border-slate-200 dark:border-navy-700">
                            <a href="{{ route('abonnementressource.index') }}" 
                               class="btn bg-slate-150 font-medium text-slate-800 hover:bg-slate-200 focus:bg-slate-200 active:bg-slate-200/80 dark:bg-navy-500 dark:text-navy-50 dark:hover:bg-navy-450 dark:focus:bg-navy-450 dark:active:bg-navy-450/90">
                                <i class="fas fa-arrow-left mr-2"></i>
                                Retour à la liste
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-span-12 py-6 lg:sticky lg:bottom-0 lg:col-span-4 lg:self-end">
                @include('layouts.sidebar')
            </div>    
        </div>
    </main>
</x-app-layout>
