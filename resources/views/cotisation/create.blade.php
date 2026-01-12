<x-app-layout title="Ajouter une cotisation" is-sidebar-open="true" is-header-blur="true">
    <main class="main-content w-full px-[var(--margin-x)] pb-8">
        <div class="flex items-center space-x-4 py-5 lg:py-6">
            <h2 class="text-xl font-medium text-slate-800 dark:text-navy-50 lg:text-2xl">
                Ajouter une cotisation
            </h2>
            <div class="hidden h-full py-1 sm:flex">
                <div class="h-full w-px bg-slate-300 dark:bg-navy-600"></div>
            </div>
        </div>
        <div class="grid grid-cols-12 lg:gap-6">
            <div class="col-span-12 pt-6 lg:col-span-8 lg:pb-6">
                
                @if(session('error'))
                    <div class="alert flex rounded-lg bg-danger px-4 py-4 text-white sm:px-5">{{ session('error') }}</div>
                @endif

                <!-- Information sur le paiement KOBO -->
                <div class="alert flex rounded-lg bg-info px-4 py-4 text-white sm:px-5">
                    <div class="flex items-start">
                        <i class="fas fa-info-circle mr-3 mt-1"></i>
                        <div>
                            <p class="font-medium mb-1">Paiement automatique par KOBO</p>
                            <p class="text-sm text-white/90">
                                Cette cotisation sera payée automatiquement avec votre solde KOBO. 
                                Le montant et les dates sont calculés automatiquement selon le type sélectionné.
                            </p>
                            @if($entreprise->entrepriseprofil)
                                <p class="text-sm text-white/90 mt-2">
                                    <i class="fas fa-building mr-1"></i>
                                    Profil de l'entreprise : <strong>{{ $entreprise->entrepriseprofil->titre }}</strong>
                                </p>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="card px-4 pb-4 sm:px-5">
                    <div class="max-w-xxl">
                        <form action="{{ route('cotisation.store') }}" method="POST" class="space-y-6">
                            @csrf
                            
                            <input type="hidden" name="entreprise_id" value="{{ $entreprise->id }}">

                            <div class="grid grid-cols-1 gap-6">
                                <div>
                                    <label class="block">
                                        <span>Type de cotisation <span class="text-danger">*</span></span>
                                        <select name="cotisationtype_id" required id="cotisationtype_id"
                                                class="form-select mt-1.5 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 hover:border-slate-400 focus:border-primary dark:border-navy-450 dark:bg-navy-700 dark:hover:border-navy-400 dark:focus:border-accent">
                                            <option value="">Sélectionner un type</option>
                                            @foreach($cotisationtypes as $type)
                                                <option value="{{ $type->id }}" 
                                                        data-montant="{{ $type->montant }}" 
                                                        data-titre="{{ $type->titre }}"
                                                        data-jours="{{ $type->nombre_jours }}"
                                                        data-profil="{{ $type->entrepriseprofil?->titre ?? 'Générique' }}">
                                                    {{ $type->titre }} - {{ number_format($type->montant, 2) }} XOF
                                                    @if($type->nombre_jours > 0)
                                                        ({{ $type->nombre_jours }} jours)
                                                    @endif
                                                    @if($type->entrepriseprofil)
                                                        - {{ $type->entrepriseprofil->titre }}
                                                    @endif
                                                </option>
                                            @endforeach
                                        </select>
                                    </label>
                                </div>

                                <!-- Informations automatiques -->
                                <div id="auto-info" class="hidden">
                                    <div class="bg-slate-50 dark:bg-navy-800 rounded-lg p-4 border border-slate-200 dark:border-navy-700">
                                        <h4 class="font-semibold text-slate-700 dark:text-navy-200 mb-3">Informations automatiques</h4>
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                                            <div>
                                                <span class="text-slate-600 dark:text-navy-400">Montant :</span>
                                                <span class="font-medium text-slate-800 dark:text-navy-100" id="montant-affiche">-</span>
                                            </div>
                                            <div>
                                                <span class="text-slate-600 dark:text-navy-400">Période :</span>
                                                <span class="font-medium text-slate-800 dark:text-navy-100" id="periode-affiche">-</span>
                                            </div>
                                            <div>
                                                <span class="text-slate-600 dark:text-navy-400">Date de début :</span>
                                                <span class="font-medium text-slate-800 dark:text-navy-100" id="debut-affiche">-</span>
                                            </div>
                                            <div>
                                                <span class="text-slate-600 dark:text-navy-400">Date de fin :</span>
                                                <span class="font-medium text-slate-800 dark:text-navy-100" id="fin-affiche">-</span>
                                            </div>
                                            <div>
                                                <span class="text-slate-600 dark:text-navy-400">Date d'échéance :</span>
                                                <span class="font-medium text-slate-800 dark:text-navy-100" id="echeance-affiche">-</span>
                                            </div>
                                            <div>
                                                <span class="text-slate-600 dark:text-navy-400">Statut :</span>
                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                                    <i class="fas fa-check mr-1"></i>Payée automatiquement
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div>
                                    <label class="block">
                                        <span>Commentaires</span>
                                        <textarea name="commentaires" rows="4" maxlength="1000"
                                                  class="form-input mt-1.5 w-full rounded-lg border border-slate-300 bg-transparent px-3 py-2 placeholder:text-slate-400/70 hover:border-slate-400 focus:border-primary dark:border-navy-450 dark:hover:border-navy-400 dark:focus:border-accent"
                                                  placeholder="Informations complémentaires sur la cotisation..."></textarea>
                                    </label>
                                </div>
                            </div>

                            <div class="flex justify-end space-x-4 mt-6">
                                <a href="{{ route('cotisation.index') }}" 
                                   class="btn bg-slate-150 font-medium text-slate-800 hover:bg-slate-200 focus:bg-slate-200 active:bg-slate-200/80 dark:bg-navy-500 dark:text-navy-50 dark:hover:bg-navy-450 dark:focus:bg-navy-450 dark:active:bg-navy-450/90">
                                    <i class="fas fa-times mr-2"></i>
                                    Annuler
                                </a>
                                <button type="submit" 
                                        class="btn bg-primary font-medium text-white hover:bg-primary-focus focus:bg-primary-focus active:bg-primary-focus/90 dark:bg-accent dark:hover:bg-accent-focus dark:focus:bg-accent-focus dark:active:bg-accent/90">
                                    <i class="fas fa-wallet mr-2"></i>
                                    Créer et payer avec KOBO
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
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
