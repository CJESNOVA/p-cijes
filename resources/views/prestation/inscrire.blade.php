<x-app-layout title="Inscription à la prestation" is-sidebar-open="true" is-header-blur="true">
    <main class="main-content w-full px-[var(--margin-x)] pb-8">
        <div class="flex items-center space-x-4 py-5 lg:py-6">
          <h2 class="text-xl font-medium text-slate-800 dark:text-navy-50 lg:text-2xl">
            Inscription à la prestation
          </h2>
          <div class="hidden h-full py-1 sm:flex">
            <div class="h-full w-px bg-slate-300 dark:bg-navy-600"></div>
          </div>
        </div>
        <div class="grid grid-cols-12 lg:gap-6">
            <div class="col-span-12 pt-6 lg:col-span-8 lg:pb-6">
              
                @if(session('error'))
                    <div class="alert flex rounded-lg bg-red-500 px-4 py-4 text-white sm:px-5 mb-4">
                        {{ session('error') }}
                    </div>
                @endif

                @if(session('success'))
                    <div class="alert flex rounded-lg bg-green-500 px-4 py-4 text-white sm:px-5 mb-4">
                        {{ session('success') }}
                    </div>
                @endif

                <!-- Informations sur la prestation -->
                <div class="bg-white p-6 rounded-lg shadow-md mb-6">
                    <h2 class="text-xl font-semibold mb-4">{{ $prestation->titre }}</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <p class="mb-2"><strong>Entreprise :</strong> {{ $prestation->entreprise->nom ?? 'Inconnue' }}</p>
                            <p class="mb-2"><strong>Type :</strong> {{ $prestation->prestationtype->titre ?? '-' }}</p>
                            <p class="mb-2"><strong>Durée :</strong> {{ $prestation->duree }} {{ $prestation->duree > 1 ? 'heures' : 'heure' }}</p>
                        </div>
                        <div>
                            <p class="mb-2"><strong>Prix unitaire :</strong> {{ number_format($prestation->prix, 2) }} FCFA</p>
                            <p class="mb-2"><strong>Disponibilité :</strong> {{ $prestation->disponibilite ?? 'Sur demande' }}</p>
                        </div>
                    </div>
                    @if($prestation->description)
                        <div class="mt-4 pt-4 border-t">
                            <h3 class="font-semibold mb-2">Description</h3>
                            <div class="text-gray-700">{!! $prestation->description !!}</div>
                        </div>
                    @endif
                </div>

                <form action="{{ route('prestation.inscrire.store', $prestation->id) }}" method="POST" id="inscription-form">
                    @csrf

                    <!-- Section Choix du contexte de paiement -->
                    <div class="card mb-6">
                        <div class="card-header bg-blue-50 px-4 py-3">
                            <h3 class="text-lg font-semibold text-primary">💳 Qui va payer pour cette prestation ?</h3>
                            <p class="text-sm text-gray-600 mt-1">Choisissez le contexte qui sera utilisé pour le paiement et l'application des réductions éventuelles</p>
                        </div>
                        <div class="card-body p-4">
                            <!-- Options de paiement -->
                            @if(isset($optionsPaiement['accompagnements']) && count($optionsPaiement['accompagnements']) > 0)
                                <div class="border-2 border-blue-200 rounded-lg p-4 bg-blue-50 mb-4">
                                    <h4 class="font-semibold mb-3 text-blue-700 flex items-center">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                        </svg>
                                        Payer via un accompagnement actif
                                    </h4>
                                    <p class="text-sm text-gray-600 mb-3">Utilisez un de vos accompagnements en cours pour bénéficier des avantages associés</p>
                                    @foreach($optionsPaiement['accompagnements'] as $acc)
                                        <label class="flex items-start p-4 border rounded-lg cursor-pointer hover:bg-white hover:shadow-md transition-all mb-3 bg-white">
                                            <input type="radio" name="contexte_type" value="accompagnement" 
                                                   data-context-id="{{ $acc['id'] }}" 
                                                   data-nom="{{ $acc['nom'] }}"
                                                   data-type="{{ $acc['type'] }}"
                                                   data-est-cjes="{{ $acc['est_cjes'] ? 'true' : 'false' }}"
                                                   data-cotisation-a-jour="{{ $acc['cotisation_a_jour'] ? 'true' : 'false' }}"
                                                   data-profil-id="{{ $acc['profil_id'] ?? '' }}"
                                                   class="contexte-radio mr-3 mt-1">
                                            <input type="hidden" name="accompagnement_id" value="{{ $acc['id'] }}">
                                            <div class="flex-1">
                                                <div class="font-semibold text-gray-800">{{ $acc['nom'] }}</div>
                                                @if($acc['type'] === 'accompagnement_entreprise')
                                                    <div class="text-sm text-gray-600 mt-1">
                                                        <div class="flex items-center">
                                                            <svg class="w-4 h-4 mr-1 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                                            </svg>
                                                            Entreprise: {{ $acc['entreprise_nom'] ?? '' }}
                                                        </div>
                                                        <div class="flex items-center mt-2 space-x-2">
                                                            @if($acc['est_cjes'])
                                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                                                    </svg>
                                                                    Membre CJES
                                                                </span>
                                                                @if($acc['cotisation_a_jour'])
                                                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                                            <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                                                        </svg>
                                                                        Cotisations à jour
                                                                    </span>
                                                                    <span class="text-xs text-green-600 font-medium">✓ Réductions applicables</span>
                                                                @else
                                                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-amber-100 text-amber-800">
                                                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                                                        </svg>
                                                                        Cotisations en retard
                                                                    </span>
                                                                    <span class="text-xs text-amber-600 font-medium">⚠️ Pas de réduction</span>
                                                                @endif
                                                            @else
                                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM7 9a1 1 0 100-2 1 1 0 000 2zm7-1a1 1 0 11-2 0 1 1 0 012 0zm-.464 5.535a1 1 0 10-1.415-1.414 3 3 0 01-4.242 0 1 1 0 00-1.415 1.414 5 5 0 007.072 0z" clip-rule="evenodd"></path>
                                                                    </svg>
                                                                    Non-CJES
                                                                </span>
                                                                <span class="text-xs text-gray-600 font-medium">Pas de réduction</span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                @else
                                                    <div class="text-sm text-gray-600 mt-1">
                                                        <div class="flex items-center">
                                                            <svg class="w-4 h-4 mr-1 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                                            </svg>
                                                            Accompagnement personnel
                                                        </div>
                                                        <div class="text-xs text-gray-500 mt-1">Cet accompagnement est lié à votre profil personnel</div>
                                                    </div>
                                                @endif
                                            </div>
                                        </label>
                                    @endforeach
                                </div>
                            @endif
                            
                            <!-- Section entreprises -->
                            @if(isset($optionsPaiement['entreprises']) && count($optionsPaiement['entreprises']) > 0)
                                <div class="border-2 border-green-200 rounded-lg p-4 bg-green-50 mb-4">
                                    <h4 class="font-semibold mb-3 text-green-700 flex items-center">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                        </svg>
                                        Payer via une entreprise
                                    </h4>
                                    <p class="text-sm text-gray-600 mb-3">Utilisez une de vos entreprises pour le paiement et bénéficier des réductions professionnelles</p>
                                    @foreach($optionsPaiement['entreprises'] as $entreprise)
                                        <label class="flex items-start p-4 border rounded-lg cursor-pointer hover:bg-white hover:shadow-md transition-all mb-3 bg-white">
                                            <input type="radio" name="contexte_type" value="entreprise" 
                                                   data-context-id="{{ $entreprise['id'] }}" 
                                                   data-nom="{{ $entreprise['nom'] }}"
                                                   data-type="{{ $entreprise['type'] }}"
                                                   data-est-cjes="{{ $entreprise['est_cjes'] ? 'true' : 'false' }}"
                                                   data-cotisation-a-jour="{{ $entreprise['cotisation_a_jour'] ? 'true' : 'false' }}"
                                                   data-profil-id="{{ $entreprise['profil_id'] ?? '' }}"
                                                   class="contexte-radio mr-3 mt-1">
                                            <input type="hidden" name="entreprise_id" value="{{ $entreprise['id'] }}">
                                            <div class="flex-1">
                                                <div class="font-semibold text-gray-800">{{ $entreprise['nom'] }}</div>
                                                <div class="text-sm text-gray-600 mt-1">
                                                    <div class="flex items-center mt-2 space-x-2">
                                                        @if($entreprise['est_cjes'])
                                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                                                </svg>
                                                                Membre CJES
                                                            </span>
                                                            @if($entreprise['cotisation_a_jour'])
                                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                                        <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                                                    </svg>
                                                                    Cotisations à jour
                                                                </span>
                                                                <span class="text-xs text-green-600 font-medium">✓ Réductions applicables</span>
                                                            @else
                                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-amber-100 text-amber-800">
                                                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                                                    </svg>
                                                                    Cotisations en retard
                                                                </span>
                                                                <span class="text-xs text-amber-600 font-medium">⚠️ Pas de réduction</span>
                                                            @endif
                                                        @else
                                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM7 9a1 1 0 100-2 1 1 0 000 2zm7-1a1 1 0 11-2 0 1 1 0 012 0zm-.464 5.535a1 1 0 10-1.415-1.414 3 3 0 01-4.242 0 1 1 0 00-1.415 1.414 5 5 0 007.072 0z" clip-rule="evenodd"></path>
                                                                </svg>
                                                                Non-CJES
                                                            </span>
                                                            <span class="text-xs text-gray-600 font-medium">Pas de réduction</span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </label>
                                    @endforeach
                                </div>
                            @endif
                            
                            <!-- Section paiement personnel -->
                            <div class="border-2 border-purple-200 rounded-lg p-4 bg-purple-50">
                                <h4 class="font-semibold mb-3 text-purple-700 flex items-center">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                    Payer personnellement
                                </h4>
                                <p class="text-sm text-gray-600 mb-3">Utilisez votre compte personnel pour cette inscription</p>
                                <label class="flex items-start p-4 border rounded-lg cursor-pointer hover:bg-white hover:shadow-md transition-all bg-white">
                                    <input type="radio" name="contexte_type" value="membre" 
                                           data-context-id="{{ $optionsPaiement['membre']['id'] }}" 
                                           data-nom="{{ $optionsPaiement['membre']['nom'] }}"
                                           data-type="{{ $optionsPaiement['membre']['type'] }}"
                                           data-est-cjes="{{ $optionsPaiement['membre']['est_cjes'] ? 'true' : 'false' }}"
                                           data-cotisation-a-jour="{{ $optionsPaiement['membre']['cotisation_a_jour'] ? 'true' : 'false' }}"
                                           data-profil-id=""
                                           class="contexte-radio mr-3 mt-1" checked>
                                    <input type="hidden" name="membre_id" value="{{ $optionsPaiement['membre']['id'] }}">
                                    <div class="flex-1">
                                        <div class="font-semibold text-gray-800">{{ $optionsPaiement['membre']['nom'] }}</div>
                                        <div class="text-sm text-gray-600 mt-1">
                                            <div class="flex items-center mt-2">
                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM7 9a1 1 0 100-2 1 1 0 000 2zm7-1a1 1 0 11-2 0 1 1 0 012 0zm-.464 5.535a1 1 0 10-1.415-1.414 3 3 0 01-4.242 0 1 1 0 00-1.415 1.414 5 5 0 007.072 0z" clip-rule="evenodd"></path>
                                                    </svg>
                                                    Paiement personnel
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Section Calcul du prix -->
                    <div class="card mb-6">
                        <div class="card-header bg-green-50 px-4 py-3">
                            <h3 class="text-lg font-semibold text-green-700">💰 Calcul du prix</h3>
                            <p class="text-sm text-gray-600 mt-1">Le prix est calculé automatiquement selon le contexte choisi</p>
                        </div>
                        <div class="card-body p-4">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Prix unitaire de la prestation</label>
                                    <div class="text-2xl font-bold text-gray-900">
                                        {{ number_format($prestation->prix ?? 0, 2) }} FCFA
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Quantité</label>
                                    <input type="number" name="quantite" id="quantite" value="1" min="1" step="1" 
                                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                </div>
                            </div>
                            
                            <div class="mt-4 p-4 bg-gray-50 rounded-lg">
                                <div class="flex justify-between items-center mb-2">
                                    <span class="text-gray-600">Montant de base:</span>
                                    <span class="font-semibold" id="montant-base">{{ number_format($prestation->prix ?? 0, 2) }} FCFA</span>
                                </div>
                                <div class="flex justify-between items-center mb-2" id="reduction-container" style="display: none;">
                                    <span class="text-gray-600">Réduction:</span>
                                    <span class="font-semibold text-green-600" id="montant-reduction">-0 FCFA</span>
                                </div>
                                <div class="border-t pt-2 mt-2">
                                    <div class="flex justify-between items-center">
                                        <span class="text-lg font-bold text-gray-900">Montant final:</span>
                                        <span class="text-2xl font-bold text-blue-600" id="montant-final">{{ number_format($prestation->prix ?? 0, 2) }} FCFA</span>
                                    </div>
                                </div>
                            </div>
                            
                            <input type="hidden" name="montant" id="montant-hidden" value="{{ $prestation->prix ?? 0 }}">
                        </div>
                    </div>

                    <!-- Section Compte ressource -->
                    <div class="card mb-6" id="ressource-section" style="display: none;">
                        <div class="card-header bg-orange-50 px-4 py-3">
                            <h3 class="text-lg font-semibold text-orange-700">💳 Compte ressource à débiter</h3>
                            <p class="text-sm text-gray-600 mt-1">Sélectionnez le compte ressource qui sera débité pour le paiement</p>
                        </div>
                        <div class="card-body p-4">
                            @if($ressources->count() > 0)
                                <div class="mb-6">
                                    <label class="block font-medium mb-1">Compte ressource</label>
                                    <select name="ressourcecompte_id" id="ressourcecompte_id" 
                                            class="form-select w-full rounded-lg border border-slate-300 bg-white px-3 py-2">
                                        <option value="">-- Sélectionnez --</option>
                                        @foreach($ressources as $r)
                                            <option value="{{ $r->id }}">{{ $r->nom_complet ?? '' }} (Solde: {{ number_format($r->solde, 2) }} FCFA)</option>
                                        @endforeach
                                    </select>
                                    @error('ressourcecompte_id')
                                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            @else
                                <div class="text-center py-4 text-gray-500">
                                    <svg class="w-12 h-12 mx-auto mb-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <p>Aucun compte ressource disponible</p>
                                    <p class="text-sm mt-1">Vous devez d'abord créer des comptes ressources pour effectuer des paiements</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Section Validation -->
                    <div class="card">
                        <div class="card-body p-4">
                            <div class="flex justify-between items-center">
                                <div>
                                    <p class="text-sm text-gray-600">En confirmant, vous vous engagez à payer pour cette prestation</p>
                                    <p class="text-xs text-gray-500 mt-1">Les réductions seront appliquées automatiquement selon votre contexte</p>
                                </div>
                                <div class="flex space-x-3">
                                    <a href="{{ route('prestation.liste') }}" class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                                        Annuler
                                    </a>
                                    <button type="submit" class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors flex items-center">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        Confirmer l'inscription
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <div class="col-span-12 py-6 lg:sticky lg:bottom-0 lg:col-span-4 lg:self-end">
                @include('layouts.sidebar')
            </div>    
        </div>
    </main>

    <!-- JavaScript pour le calcul dynamique -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const prixBase = {{ $prestation->prix ?? 0 }};
            const quantiteInput = document.getElementById('quantite');
            const montantBase = document.getElementById('montant-base');
            const montantFinal = document.getElementById('montant-final');
            const montantHidden = document.getElementById('montant-hidden');
            const reductionContainer = document.getElementById('reduction-container');
            const montantReduction = document.getElementById('montant-reduction');
            const ressourceSection = document.getElementById('ressource-section');
            const contexteRadios = document.querySelectorAll('.contexte-radio');

            function calculerMontant() {
                const quantite = parseInt(quantiteInput.value) || 1;
                const montantBaseCalcul = prixBase * quantite;
                
                // Récupérer le contexte sélectionné
                const contexteSelectionne = document.querySelector('.contexte-radio:checked');
                if (!contexteSelectionne) return;

                const contexte = {
                    type: contexteSelectionne.dataset.type,
                    id: contexteSelectionne.dataset.contextId,
                    est_cjes: contexteSelectionne.dataset.estCjes === 'true',
                    cotisation_a_jour: contexteSelectionne.dataset.cotisationAJour === 'true',
                    profil_id: contexteSelectionne.dataset.profilId
                };

                // Mettre à jour le montant de base
                montantBase.textContent = new Intl.NumberFormat('fr-FR').format(montantBaseCalcul) + ' FCFA';

                // Appel AJAX pour calculer le montant avec réductions
                fetch('{{ route("prestation.calculer.montant", $prestation->id) }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        contexte: contexte,
                        contexte_id: contexte.id,
                        prix_base: prixBase,
                        quantite: quantite
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        montantFinal.textContent = new Intl.NumberFormat('fr-FR').format(data.montant_final) + ' FCFA';
                        montantHidden.value = data.montant_final;
                        
                        if (data.reduction > 0) {
                            reductionContainer.style.display = 'flex';
                            montantReduction.textContent = '-' + new Intl.NumberFormat('fr-FR').format(data.reduction) + ' FCFA';
                        } else {
                            reductionContainer.style.display = 'none';
                        }
                        
                        // Afficher/masquer la section des ressources
                        if (data.montant_final > 0) {
                            ressourceSection.style.display = 'block';
                        } else {
                            ressourceSection.style.display = 'none';
                        }
                    }
                })
                .catch(error => {
                    console.error('Erreur:', error);
                    // En cas d'erreur, utiliser le montant de base
                    montantFinal.textContent = new Intl.NumberFormat('fr-FR').format(montantBaseCalcul) + ' FCFA';
                    montantHidden.value = montantBaseCalcul;
                    reductionContainer.style.display = 'none';
                    ressourceSection.style.display = montantBaseCalcul > 0 ? 'block' : 'none';
                });
            }

            // Écouter les changements
            quantiteInput.addEventListener('input', calculerMontant);
            contexteRadios.forEach(radio => {
                radio.addEventListener('change', calculerMontant);
            });

            // Calculer au chargement
            calculerMontant();
        });
    </script>
</x-app-layout>
