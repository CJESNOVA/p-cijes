<x-app-layout title="Ajouter une cotisation" is-sidebar-open="true" is-header-blur="true">
    <main class="main-content w-full px-[var(--margin-x)] pb-8">

        <!-- Header moderne -->
        <div class="mb-2">
            <div class="flex items-center gap-4 mb-2">
                <div class="h-14 w-14 rounded-xl bg-gradient-to-br from-[#4FBE96] to-[#4FBE96] flex items-center justify-center shadow-lg">
                    <i class="fas fa-wallet text-white text-xl"></i>
                </div>
                <div>
                    <h1 class="text-3xl font-bold text-slate-800 dark:text-navy-50">
                        Ajouter une cotisation
                    </h1>
                    <p class="mt-2 text-slate-600 dark:text-navy-200 text-lg">
                        Paiement automatique via votre solde KOBO
                    </p>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-12 lg:gap-6">
            <div class="col-span-12 pt-6 lg:col-span-8 lg:pb-6">

                {{-- Messages --}}
                @if(session('error'))
                    <div class="alert flex rounded-lg bg-red-500 px-6 py-4 text-white mb-6 shadow-lg">
                        <i class="fas fa-exclamation-circle mr-2 mt-1"></i>
                        {{ session('error') }}
                    </div>
                @endif

                {{-- Info KOBO --}}
                <div class="alert flex rounded-lg bg-info px-6 py-4 text-white mb-6 shadow-lg">
                    <i class="fas fa-info-circle mr-3 mt-1"></i>
                    <div>
                        <p class="font-semibold mb-1">Paiement automatique KOBO</p>
                        <p class="text-sm opacity-90">
                            Le montant, la période et les dates sont calculés automatiquement selon le type de cotisation sélectionné.
                        </p>

                        @if($entreprise->entrepriseprofil)
                            <p class="text-sm mt-2">
                                <i class="fas fa-building mr-1"></i>
                                Profil de l'entreprise :
                                <strong>{{ $entreprise->entrepriseprofil->titre }}</strong>
                            </p>
                        @endif
                    </div>
                </div>

                <form action="{{ route('cotisation.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="entreprise_id" value="{{ $entreprise->id }}">

                    <div class="card shadow-xl mb-6">
                        <div class="card-header border-b border-slate-200 dark:border-navy-500 px-6 py-4">
                            <h3 class="text-xl font-semibold text-slate-800 dark:text-navy-50 flex items-center">
                                <i class="fas fa-list mr-2 text-primary"></i>
                                Informations de la cotisation
                            </h3>
                        </div>

                        <div class="card-body p-6">
                            <div class="grid grid-cols-1 gap-6">

                                <!-- Type -->
                                <div>
                                    <label class="block text-sm font-medium mb-2">
                                        Type de cotisation <span class="text-red-500">*</span>
                                    </label>
                                    <select name="cotisationtype_id" id="cotisationtype_id" required
                                            class="form-select w-full rounded-lg border border-slate-300 bg-white px-3 py-2 focus:border-primary">
                                        <option value="">-- Sélectionner --</option>
                                        @foreach($cotisationtypes as $type)
                                            <option value="{{ $type->id }}"
                                                    data-montant="{{ $type->montant }}"
                                                    data-jours="{{ $type->nombre_jours }}"
                                                    data-titre="{{ $type->titre }}">
                                                {{ $type->titre }} – {{ number_format($type->montant, 2) }} XOF
                                                @if($type->nombre_jours)
                                                    ({{ $type->nombre_jours }} jours)
                                                @endif
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Infos auto -->
                                <div id="auto-info" class="hidden">
                                    <div class="rounded-lg border border-slate-200 bg-slate-50 dark:bg-navy-800 p-4">
                                        <h4 class="font-semibold mb-3">Informations automatiques</h4>
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-sm">
                                            <div>Montant : <strong id="montant-affiche">-</strong></div>
                                            <div>Période : <strong id="periode-affiche">-</strong></div>
                                            <div>Début : <strong id="debut-affiche">-</strong></div>
                                            <div>Fin : <strong id="fin-affiche">-</strong></div>
                                            <div>Échéance : <strong id="echeance-affiche">-</strong></div>
                                            <div>
                                                Statut :
                                                <span class="ml-1 px-2 py-1 text-xs rounded-full bg-green-100 text-green-700">
                                                    Payée automatiquement
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Commentaires -->
                                <div>
                                    <label class="block text-sm font-medium mb-2">
                                        Commentaires
                                    </label>
                                    <textarea name="commentaires" rows="4"
                                              class="form-textarea w-full rounded-lg border border-slate-300 px-3 py-2"
                                              placeholder="Informations complémentaires..."></textarea>
                                </div>

                            </div>
                        </div>
                    </div>

                    <!-- Section Compte Ressource KOBO -->
                    <div class="card shadow-xl mb-6">
                        <div class="card-header border-b border-slate-200 dark:border-navy-500 px-6 py-4">
                            <h3 class="text-xl font-semibold text-slate-800 dark:text-navy-50 flex items-center">
                                <i class="fas fa-coins mr-2 text-amber-500"></i>
                                Compte Ressource KOBO
                            </h3>
                        </div>

                        <div class="card-body p-6">
                            <div class="mb-4">
                                <label class="block text-sm font-medium mb-2">
                                    Sélectionnez le compte ressource à débiter <span class="text-red-500">*</span>
                                </label>
                                <select name="ressourcecompte_id" required
                                        class="form-select w-full rounded-lg border border-slate-300 bg-white px-3 py-2 focus:border-primary">
                                    <option value="">-- Sélectionner un compte KOBO --</option>
                                    @if(isset($ressourcecomptes) && $ressourcecomptes->count() > 0)
                                        @foreach($ressourcecomptes as $ressource)
                                            <option value="{{ $ressource->id }}">
                                                {{ $ressource->nom_complet ?? 'Compte #' . $ressource->id }} 
                                                (Solde: {{ number_format($ressource->solde, 2) }} FCFA)
                                            </option>
                                        @endforeach
                                    @else
                                        <option value="" disabled>Aucun compte ressource disponible</option>
                                    @endif
                                </select>
                                @if(!isset($ressourcecomptes) || $ressourcecomptes->count() == 0)
                                    <p class="text-sm text-amber-600 mt-2">
                                        <i class="fas fa-exclamation-triangle mr-1"></i>
                                        Vous devez d'abord créer des comptes ressources KOBO pour pouvoir payer les cotisations.
                                    </p>
                                @endif
                            </div>

                            <div class="rounded-lg border border-blue-200 bg-blue-50 p-4">
                                <h4 class="font-semibold mb-2 text-blue-800">
                                    <i class="fas fa-info-circle mr-1"></i>
                                    Informations de paiement
                                </h4>
                                <p class="text-sm text-blue-700">
                                    Le montant de la cotisation sera automatiquement débité du compte ressource sélectionné.
                                    Assurez-vous d'avoir un solde suffisant avant de valider.
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex justify-end gap-4">
                        <a href="{{ route('cotisation.index') }}"
                           class="btn bg-slate-200 text-slate-800">
                            Annuler
                        </a>
                        <button type="submit"
                                class="btn bg-primary text-white">
                            <i class="fas fa-wallet mr-2"></i>
                            Créer et payer
                        </button>
                    </div>
                </form>
            </div>

            <div class="col-span-12 py-6 lg:sticky lg:bottom-0 lg:col-span-4 lg:self-end">
                @include('layouts.sidebar')
            </div>
        </div>
    </main>

    

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const cotisationSelect = document.getElementById('cotisationtype_id');
            const autoInfo = document.getElementById('auto-info');
            
            const montantAffiche = document.getElementById('montant-affiche');
            const periodeAffiche = document.getElementById('periode-affiche');
            const debutAffiche = document.getElementById('debut-affiche');
            const finAffiche = document.getElementById('fin-affiche');
            const echeanceAffiche = document.getElementById('echeance-affiche');
            
            function formatDate(dateStr) {
                const date = new Date(dateStr);
                return date.toLocaleDateString('fr-FR', { day: '2-digit', month: '2-digit', year: 'numeric' });
            }
            
            function calculateDates(jours) {
                const today = new Date();
                let debut = new Date(today);
                let fin = new Date(today);
                let echeance = new Date(today);
                let periode = '';
                
                // Utiliser le nombre de jours pour calculer
                const joursCalcul = jours > 0 ? parseInt(jours) : 30; // 30 jours par défaut
                
                fin.setDate(fin.getDate() + joursCalcul);
                echeance.setDate(echeance.getDate() + joursCalcul);
                
                // Déterminer la période en fonction du nombre de jours
                if (joursCalcul <= 31) {
                    periode = 'Mensuel';
                } else if (joursCalcul <= 120) {
                    periode = 'Trimestriel';
                } else if (joursCalcul <= 240) {
                    periode = 'Semestriel';
                } else {
                    periode = 'Annuel';
                }
                
                return {
                    debut: formatDate(debut.toISOString().split('T')[0]),
                    fin: formatDate(fin.toISOString().split('T')[0]),
                    echeance: formatDate(echeance.toISOString().split('T')[0]),
                    periode: periode
                };
            }
            
            cotisationSelect.addEventListener('change', function() {
                const selectedOption = this.options[this.selectedIndex];
                
                if (this.value && selectedOption.dataset.montant) {
                    const montant = parseFloat(selectedOption.dataset.montant);
                    const titre = selectedOption.dataset.titre;
                    const jours = selectedOption.dataset.jours || 30;
                    const profil = selectedOption.dataset.profil || 'Générique';
                    const dates = calculateDates(jours);
                    
                    // Afficher les informations automatiques
                    autoInfo.classList.remove('hidden');
                    montantAffiche.textContent = new Intl.NumberFormat('fr-FR', { style: 'currency', currency: 'XOF' }).format(montant);
                    periodeAffiche.textContent = dates.periode + ' (' + jours + ' jours)';
                    debutAffiche.textContent = dates.debut;
                    finAffiche.textContent = dates.fin;
                    echeanceAffiche.textContent = dates.echeance;
                } else {
                    autoInfo.classList.add('hidden');
                }
            });
        });
    </script>
</x-app-layout>

