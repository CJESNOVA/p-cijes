<x-app-layout title="Réserver l'espace : {{ $espace->titre }}" is-sidebar-open="true" is-header-blur="true">
<main class="main-content w-full px-[var(--margin-x)] pb-8">
    <div class="flex items-center space-x-4 py-5 lg:py-6">
        <h2 class="text-xl font-medium text-slate-800 dark:text-navy-50 lg:text-2xl">
            Réserver l'espace : {{ $espace->titre }}
        </h2>
    </div>
    <div class="grid grid-cols-12 lg:gap-6">
        <div class="col-span-12 pt-6 lg:col-span-8 lg:pb-6">
            <div class="bg-white shadow rounded-lg p-6">
                
                <h2 class="mb-4">Réserver l'espace : {{ $espace->titre }}</h2>

                <div class="card mb-4">
                    <div class="card-body">
                        <p><strong>Type :</strong> {{ $espace->espacetype->titre ?? '-' }}</p>
                        <p><strong>Capacité :</strong> {{ $espace->capacite }}</p>
                        <p><strong>Prix de base :</strong> <span id="prix-base">{{ number_format($espace->prix, 0, ',', ' ') }}</span> FCFA</p>
                        <p><strong>Description :</strong></p>
                        <p>{!! $espace->description !!}</p>
                    </div>
                </div>

                @if(session('error'))
                    <div class="alert flex rounded-lg bg-red-500 px-4 py-4 text-white sm:px-5 mb-4">
                        {{ session('error') }}
                    </div>
                @endif

                @if(session('success'))
                    <div class="alert flex rounded-lg bg-[#4FBE96] px-4 py-4 text-white sm:px-5 mb-4">
                        {{ session('success') }}
                    </div>
                @endif

                <form action="{{ route('espace.reserver.store', $espace->id) }}" method="POST" id="reservation-form">
                    @csrf

                    <!-- Section Choix du contexte de paiement -->
                    <div class="card mb-6">
                        <div class="card-header bg-primary/10 px-4 py-3">
                            <h3 class="text-lg font-semibold text-primary">💳 Qui va payer pour cette réservation ?</h3>
                            <p class="text-sm text-gray-600 mt-1">Choisissez le contexte qui sera utilisé pour le paiement et l'application des réductions éventuelles</p>
                        </div>
                        <div class="card-body">
                            @if($doitChoisirPaiement)
                                <!-- Plusieurs options : afficher le choix -->
                                <div class="space-y-4">
                                    @if(isset($optionsPaiement['accompagnements']) && count($optionsPaiement['accompagnements']) > 0)
                                        <div class="border-2 border-[#4FBE96]/30 rounded-lg p-4 bg-[#4FBE96]/10">
                                            <h4 class="font-semibold mb-3 text-[#4FBE96] flex items-center">
                                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
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
                                                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-[#4FBE96]/20 text-[#4FBE96]">
                                                                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                                                            </svg>
                                                                            Membre CJES
                                                                        </span>
                                                                        @if($acc['cotisation_a_jour'])
                                                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-[#152737]/20 text-[#152737]">
                                                                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                                                    <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                                                                </svg>
                                                                                Cotisations à jour
                                                                            </span>
                                                                            <span class="text-xs text-[#4FBE96] font-medium">✓ Réductions applicables</span>
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
                                    @if(isset($optionsPaiement['entreprises']) && count($optionsPaiement['entreprises']) > 0)
                                        <div class="border-2 border-[#4FBE96]/30 rounded-lg p-4 bg-[#4FBE96]/10">
                                            <h4 class="font-semibold mb-3 text-[#4FBE96] flex items-center">
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

                                    <div class="border-2 border-purple-200 rounded-lg p-4 bg-purple-50/30">
                                        <h4 class="font-semibold mb-3 text-purple-700 flex items-center">
                                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                            </svg>
                                            Payer personnellement
                                        </h4>
                                        <p class="text-sm text-gray-600 mb-3">Utilisez votre compte personnel pour cette réservation</p>
                                        <label class="flex items-start p-4 border rounded-lg cursor-pointer hover:bg-white hover:shadow-md transition-all bg-white">
                                            <input type="radio" name="contexte_type" value="membre" 
                                                   data-context-id="{{ $optionsPaiement['membre']['id'] }}" 
                                                   data-nom="{{ $optionsPaiement['membre']['nom'] }}"
                                                   data-type="{{ $optionsPaiement['membre']['type'] }}"
                                                   data-est-cjes="false"
                                                   data-cotisation-a-jour="false"
                                                   data-profil-id=""
                                                   class="contexte-radio mr-3 mt-1">
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
                                                        <span class="text-xs text-gray-600 font-medium ml-2">Pas de réduction applicable</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </label>
                                    </div>
                                </div>
                            @else
                                <!-- Sélection automatique -->
                                <div class="bg-gradient-to-r from-[#4FBE96]/10 to-[#152737]/10 border-2 border-[#4FBE96]/30 rounded-lg p-4">
                                    <div class="flex items-start">
                                        <div class="flex-shrink-0">
                                            <svg class="w-6 h-6 text-[#4FBE96]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                        </div>
                                        <div class="ml-3 flex-1">
                                            <p class="font-semibold text-green-800">✅ Contexte de paiement sélectionné automatiquement</p>
                                            <p class="text-green-700 mt-1">{{ $contexteAuto['nom'] }}</p>
                                            <div class="mt-2 flex items-center space-x-2">
                                                @if($contexteAuto['est_cjes'])
                                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                                        </svg>
                                                        CJES
                                                    </span>
                                                    @if($contexteAuto['cotisation_a_jour'])
                                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                                <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                                            </svg>
                                                            Réductions applicables
                                                        </span>
                                                        <span class="text-xs text-green-600 font-medium">🎉 Meilleur prix garanti</span>
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
                                    <input type="hidden" name="contexte_type" value="{{ $contexteAuto['type'] }}">
                                    <input type="hidden" name="contexte_id" value="{{ $contexteAuto['id'] }}">
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Section Calcul du prix -->
                    <div class="card mb-6">
                        <div class="card-header bg-amber/10 px-4 py-3">
                            <h3 class="text-lg font-semibold text-amber-600">💰 Calcul du prix (Paiement unique)</h3>
                            <p class="text-sm text-gray-600 mt-1">Le prix affiché est pour 24h. Il sera multiplié selon le nombre de jours. <strong>Paiement en une seule fois.</strong></p>
                        </div>
                        <div class="card-body">
                            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Prix par jour</label>
                                    <div class="text-lg font-bold text-gray-900">
                                        <span id="prix-par-jour">{{ number_format($espace->prix, 0, ',', ' ') }}</span> FCFA
                                    </div>
                                    <div class="text-xs text-gray-500">Pour 24 heures</div>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Durée de réservation</label>
                                    <div class="text-lg font-bold text-blue-600">
                                        <span id="nombre-jours">1</span> jour(s)
                                    </div>
                                    <div class="text-xs text-gray-500" id="periode-details">Du [date] au [date]</div>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Réduction appliquée</label>
                                    <div class="text-lg font-bold text-green-600">
                                        <span id="montant-reduction">0</span> FCFA
                                    </div>
                                    <div id="reduction-details" class="text-xs text-gray-500 mt-1"></div>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Montant total (Paiement unique)</label>
                                    <div class="text-2xl font-bold text-primary">
                                        <span id="montant-final">{{ number_format($espace->prix, 0, ',', ' ') }}</span> FCFA
                                    </div>
                                    <div class="text-xs text-red-600 font-medium">À payer en une seule fois</div>
                                </div>
                            </div>
                            
                            @if($contexteAuto && $reductionsApplicables->isNotEmpty())
                                <div class="mt-4 p-3 bg-blue-50 border border-blue-200 rounded">
                                    <p class="text-sm text-blue-800">
                                        <strong>🎉 Réduction automatique appliquée :</strong> 
                                        {{ $reductionsApplicables->first()->titre ?? '' }}
                                    </p>
                                </div>
                            @endif

                            <!-- Détails du calcul -->
                            <div class="mt-4 p-3 bg-gray-50 border border-gray-200 rounded">
                                <div class="text-sm text-gray-600">
                                    <div class="flex justify-between mb-1">
                                        <span>Prix de base (<span id="jours-calc">1</span> jour(s)):</span>
                                        <span id="sous-total-base">{{ number_format($espace->prix, 0, ',', ' ') }} FCFA</span>
                                    </div>
                                    <div class="flex justify-between mb-1 text-green-600">
                                        <span>Réduction:</span>
                                        <span id="sous-total-reduction">-0 FCFA</span>
                                    </div>
                                    <div class="border-t pt-1 mt-1 flex justify-between font-semibold">
                                        <span>Total à payer (Paiement unique):</span>
                                        <span id="sous-total-final">{{ number_format($espace->prix, 0, ',', ' ') }} FCFA</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Dates de réservation -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                        <div>
                            <label class="block font-medium mb-1">Date de début</label>
                            <input type="date" name="datedebut" 
                                   value="{{ old('datedebut') }}"
                                   min="{{ now()->format('Y-m-d') }}"
                                   class="form-input w-full rounded-lg border border-slate-300 bg-transparent px-3 py-2"
                                   required>
                            @error('datedebut')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block font-medium mb-1">Date de fin</label>
                            <input type="date" name="datefin" 
                                   value="{{ old('datefin') }}"
                                   min="{{ now()->format('Y-m-d') }}"
                                   class="form-input w-full rounded-lg border border-slate-300 bg-transparent px-3 py-2"
                                   required>
                            @error('datefin')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    @if($espace->prix > 0)
                        <!-- Champ caché pour le montant automatique -->
                        <input type="hidden" name="montant" id="montant-input" value="{{ $espace->prix }}">

                        <!-- Compte ressource -->
                        <div class="mb-6">
                            <label class="block font-medium mb-1">Compte ressource</label>
                            <select name="ressourcecompte_id" id="ressourcecompte_id" 
                                    class="form-select w-full rounded-lg border border-slate-300 bg-white px-3 py-2">
                                <option value="">-- Sélectionnez --</option>
                                @foreach($ressources as $r)
                                    <option value="{{ $r->id }}">{{ $r->nom_complet ?? '' }}</option>
                                @endforeach
                            </select>
                            @error('ressourcecompte_id')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    @else
                        <input type="hidden" name="montant" value="0">
                    @endif

                    <!-- Observation -->
                    <div class="mb-6">
                        <label class="block font-medium mb-1">Observation (optionnel)</label>
                        <textarea name="observation" rows="3" 
                                  class="form-textarea w-full rounded-lg border border-slate-300 bg-transparent p-2.5"
                                  placeholder="Ajoutez des remarques si nécessaire...">{{ old('observation') }}</textarea>
                    </div>

                    <button type="submit" class="btn bg-primary text-white px-6 py-3">
                        {{ $espace->prix > 0 ? '💳 Payer la réservation (Paiement unique)' : '✅ Réserver gratuitement' }}
                    </button>
                </form>
            </div>
        </div>

        <div class="col-span-12 py-6 lg:sticky lg:bottom-0 lg:col-span-4 lg:self-end">
            @include('layouts.sidebar')
        </div>    
    </div>
</main>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const espaceId = {{ $espace->id }};
    const prixBase = {{ $espace->prix }};
    
    // Éléments du DOM
    const prixParJourEl = document.getElementById('prix-par-jour');
    const nombreJoursEl = document.getElementById('nombre-jours');
    const periodeDetailsEl = document.getElementById('periode-details');
    const montantReductionEl = document.getElementById('montant-reduction');
    const montantFinalEl = document.getElementById('montant-final');
    const montantInput = document.getElementById('montant-input');
    const reductionDetailsEl = document.getElementById('reduction-details');
    
    // Éléments de détail du calcul
    const joursCalcEl = document.getElementById('jours-calc');
    const sousTotalBaseEl = document.getElementById('sous-total-base');
    const sousTotalReductionEl = document.getElementById('sous-total-reduction');
    const sousTotalFinalEl = document.getElementById('sous-total-final');
    
    // Champs de date
    const dateDebutInput = document.querySelector('input[name="datedebut"]');
    const dateFinInput = document.querySelector('input[name="datefin"]');
    
    // Fonction pour calculer le nombre de jours entre deux dates
    function calculerNombreJours(dateDebut, dateFin) {
        const debut = new Date(dateDebut);
        const fin = new Date(dateFin);
        
        // Ajouter 1 jour pour inclure le jour de fin
        const diffTime = Math.abs(fin - debut) + (24 * 60 * 60 * 1000);
        const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
        
        return Math.max(1, diffDays); // Minimum 1 jour
    }
    
    // Fonction pour formater la date
    function formaterDate(dateStr) {
        const date = new Date(dateStr);
        return date.toLocaleDateString('fr-FR', { 
            day: '2-digit', 
            month: '2-digit', 
            year: 'numeric' 
        });
    }
    
    // Fonction pour mettre à jour la durée
    function mettreAJourDuree() {
        if (!dateDebutInput.value || !dateFinInput.value) {
            nombreJoursEl.textContent = '1';
            joursCalcEl.textContent = '1';
            periodeDetailsEl.textContent = 'Veuillez sélectionner les dates';
            return 1;
        }
        
        const nombreJours = calculerNombreJours(dateDebutInput.value, dateFinInput.value);
        nombreJoursEl.textContent = nombreJours;
        joursCalcEl.textContent = nombreJours;
        
        const dateDebutFormatee = formaterDate(dateDebutInput.value);
        const dateFinFormatee = formaterDate(dateFinInput.value);
        periodeDetailsEl.textContent = `Du ${dateDebutFormatee} au ${dateFinFormatee}`;
        
        return nombreJours;
    }
    
    // Fonction pour calculer le montant
    function calculerMontant() {
        const contexteRadios = document.querySelectorAll('.contexte-radio:checked');
        if (contexteRadios.length === 0) return;
        
        const radio = contexteRadios[0];
        const contexteType = radio.value;
        const contexteId = radio.dataset.contextId;
        
        // Calculer le nombre de jours
        const nombreJours = mettreAJourDuree();
        
        // Calculer le montant de base selon la durée
        const montantBase = prixBase * nombreJours;
        
        // Mettre à jour le champ caché avec le montant de base
        if (montantInput) {
            montantInput.value = montantBase;
        }
        
        // Mettre à jour l'affichage du montant de base
        sousTotalBaseEl.textContent = montantBase.toLocaleString('fr-FR') + ' FCFA';
        
        // Requête AJAX pour calculer avec réduction
        fetch(`{{ route('espace.calculer.montant', $espace->id) }}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: new URLSearchParams({
                contexte_type: contexteType,
                contexte_id: contexteId,
                montant: montantBase
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                console.error(data.error);
                return;
            }
            
            // Mettre à jour le champ caché avec le montant final (avec réduction)
            if (montantInput) {
                montantInput.value = data.montant_final;
            }
            
            // Mettre à jour l'affichage
            montantReductionEl.textContent = data.economie.toLocaleString('fr-FR');
            montantFinalEl.textContent = data.montant_final.toLocaleString('fr-FR');
            
            // Mettre à jour les détails du calcul
            sousTotalReductionEl.textContent = '-' + data.economie.toLocaleString('fr-FR') + ' FCFA';
            sousTotalFinalEl.textContent = data.montant_final.toLocaleString('fr-FR') + ' FCFA';
            
            // Afficher les détails de la réduction
            if (data.reduction) {
                let details = `Réduction: ${data.reduction.titre}`;
                if (data.reduction.pourcentage) {
                    details += ` (${data.reduction.pourcentage}%)`;
                }
                reductionDetailsEl.textContent = details;
            } else {
                reductionDetailsEl.textContent = 'Aucune réduction applicable';
            }
            
            // Mettre à jour le champ caché contexte_id
            let contexteIdInput = document.querySelector('input[name="contexte_id"]');
            if (!contexteIdInput) {
                contexteIdInput = document.createElement('input');
                contexteIdInput.type = 'hidden';
                contexteIdInput.name = 'contexte_id';
                document.getElementById('reservation-form').appendChild(contexteIdInput);
            }
            contexteIdInput.value = contexteId;
        })
        .catch(error => {
            console.error('Erreur:', error);
            montantReductionEl.textContent = '0';
            montantFinalEl.textContent = montantBase.toLocaleString('fr-FR');
            sousTotalReductionEl.textContent = '-0 FCFA';
            sousTotalFinalEl.textContent = montantBase.toLocaleString('fr-FR') + ' FCFA';
            reductionDetailsEl.textContent = 'Erreur de calcul';
            
            // En cas d'erreur, utiliser le montant de base
            if (montantInput) {
                montantInput.value = montantBase;
            }
        });
    }
    
    // Écouteurs d'événements
    document.querySelectorAll('.contexte-radio').forEach(radio => {
        radio.addEventListener('change', calculerMontant);
    });
    
    // Écouteurs pour les dates
    dateDebutInput.addEventListener('change', () => {
        mettreAJourDuree();
        calculerMontant();
    });
    
    dateFinInput.addEventListener('change', () => {
        mettreAJourDuree();
        calculerMontant();
    });
    
    // Validation que la date de fin est après la date de début
    dateFinInput.addEventListener('change', () => {
        if (dateDebutInput.value && dateFinInput.value) {
            const debut = new Date(dateDebutInput.value);
            const fin = new Date(dateFinInput.value);
            
            if (fin <= debut) {
                // Ajuster la date de fin pour être au moins 1 jour après
                const nouvelleDateFin = new Date(debut);
                nouvelleDateFin.setDate(nouvelleDateFin.getDate() + 1);
                dateFinInput.value = nouvelleDateFin.toISOString().split('T')[0];
            }
        }
    });
    
    // Calcul initial si un contexte est déjà sélectionné
    const selectedRadio = document.querySelector('.contexte-radio:checked');
    if (selectedRadio) {
        mettreAJourDuree();
        calculerMontant();
    } else {
        mettreAJourDuree();
    }
});
</script>

<meta name="csrf-token" content="{{ csrf_token() }}">
</x-app-layout>
