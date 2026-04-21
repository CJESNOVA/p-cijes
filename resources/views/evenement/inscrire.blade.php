<x-app-layout title="Inscription à l'événement : {{ $evenement->titre }}" is-sidebar-open="true" is-header-blur="true">
    <main class="main-content w-full px-[var(--margin-x)] pb-8">
    <div class="flex items-center space-x-4 py-5 lg:py-6">
        <h2 class="text-xl font-medium text-slate-800 dark:text-navy-50 lg:text-2xl">
            Inscription à l'événement : {{ $evenement->titre }}
        </h2>
    </div>

        <div class="grid grid-cols-12 lg:gap-6">
        <div class="col-span-12 pt-6 lg:col-span-8 lg:pb-6">
            <div class="bg-white shadow rounded-lg p-6">
                
                <h2 class="mb-4">Inscription à l'événement : {{ $evenement->titre }}</h2>

                <div class="card mb-4">
                    <div class="card-body">
                        <p><strong>Type :</strong> {{ $evenement->evenementtype->titre ?? '-' }}</p>
                        <p><strong>Date :</strong> {{ \Carbon\Carbon::parse($evenement->dateevenement)->format('d/m/Y H:i') }}</p>
                        <p><strong>Lieu :</strong> {{ $evenement->lieu ?? '-' }}</p>
                        <p><strong>Prix de base :</strong> <span id="prix-base">{{ number_format($evenement->prix, 0, ',', ' ') }}</span> FCFA</p>
                        @if($evenement->resume)
                            <p><strong>Description :</strong></p>
                            <p>{!! $evenement->resume !!}</p>
                        @endif
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
                        <form action="{{ route('evenement.inscrire.store', $evenement->id) }}" method="POST" id="inscription-form">
                    @csrf

                    <!-- Section Choix du contexte de paiement -->
                    <div class="card mb-6">
                        <div class="card-header bg-blue-50 px-4 py-3">
                            <h3 class="text-lg font-semibold text-primary">💳 Qui va payer pour cette inscription ?</h3>
                            <p class="text-sm text-gray-600 mt-1">Choisissez le contexte qui sera utilisé pour le paiement et l'application des réductions éventuelles</p>
                        </div>
                        <div class="card-body">
                            @if(count($optionsPaiement['entreprises']) > 0 || count($optionsPaiement['accompagnements']) > 0)
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
                                    @endif
                                    
                                    @if(isset($optionsPaiement['entreprises']) && count($optionsPaiement['entreprises']) > 0)
                                        <div class="border-2 border-green-200 rounded-lg p-4 bg-green-50">
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
                                <div class="bg-green-50 border-2 border-green-200 rounded-lg p-4">
                                    <div class="flex items-start">
                                        <div class="flex-shrink-0">
                                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                        </div>
                                        <div class="ml-3 flex-1">
                                            <p class="font-semibold text-green-800">✅ Contexte de paiement sélectionné automatiquement</p>
                                            <p class="text-green-700 mt-1">{{ $optionsPaiement['membre']['nom'] }}</p>
                                            <div class="mt-2 flex items-center space-x-2">
                                                @if($optionsPaiement['membre']['est_cjes'])
                                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                                        </svg>
                                                        CJES
                                                    </span>
                                                    @if($optionsPaiement['membre']['cotisation_a_jour'])
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
                                    <input type="hidden" name="contexte_type" value="membre">
                                    <input type="hidden" name="contexte_id" value="{{ $optionsPaiement['membre']['id'] }}">
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Section Calcul du prix -->
                    <div class="card mb-6">
                        <div class="card-header bg-blue-50 px-4 py-3">
                            <h3 class="text-lg font-semibold text-primary">💰 Calcul du prix</h3>
                            <p class="text-sm text-gray-600 mt-1">Le prix est calculé automatiquement selon le contexte sélectionné et les réductions applicables</p>
                        </div>
                        <div class="card-body">
                            <div class="bg-gray-50 rounded-lg p-4">
                                <div class="space-y-3">
                                    <div class="flex justify-between items-center">
                                        <span class="text-gray-600">Prix par événement</span>
                                        <span class="font-semibold">{{ number_format($evenement->prix, 0, ',', ' ') }} FCFA</span>
                                    </div>
                                    <div class="flex justify-between items-center">
                                        <span class="text-gray-600">Durée</span>
                                        <span class="font-semibold">1 événement</span>
                                    </div>
                                    <div class="border-t pt-3">
                                        <div class="flex justify-between items-center">
                                            <span class="text-gray-600">Montant de base</span>
                                            <span class="font-semibold" id="montant-base">{{ number_format($evenement->prix, 0, ',', ' ') }} FCFA</span>
                                        </div>
                                    </div>
                                    <div id="reduction-section" class="border-t pt-3">
                                        <div class="flex justify-between items-center">
                                            <span class="text-green-600" id="reduction-label">Réduction</span>
                                            <span class="font-semibold text-green-600" id="reduction-montant">0 FCFA</span>
                                        </div>
                                    </div>
                                    <div class="border-t pt-3">
                                        <div class="flex justify-between items-center">
                                            <span class="text-lg font-bold">Montant final</span>
                                            <span class="text-xl font-bold text-primary" id="montant-final">{{ number_format($evenement->prix, 0, ',', ' ') }} FCFA</span>
                                        </div>
                                    </div>
                                    <div id="economie-section" class="hidden mt-2">
                                        <p class="text-sm text-green-600">
                                            <span class="font-semibold">Économie réalisée :</span> 
                                            <span id="economie-montant">0 FCFA</span>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Section Compte ressource -->
                    <div class="card mb-6" id="compte-section">
                        <div class="card-header bg-blue-50 px-4 py-3">
                            <h3 class="text-lg font-semibold text-primary">💳 Compte à débiter</h3>
                            <p class="text-sm text-gray-600 mt-1">Sélectionnez le compte qui sera débité pour cette inscription</p>
                        </div>
                        <div class="card-body">
                            <div class="mb-4">
                                <label for="ressourcecompte_id" class="block text-sm font-medium text-gray-700 mb-2">
                                    Compte ressource
                                </label>
                                <select name="ressourcecompte_id" id="ressourcecompte_id" 
                                        class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent">
                                    <option value="">-- Sélectionnez un compte --</option>
                                    @foreach($ressources as $compte)
                                        <option value="{{ $compte->id }}">{{ $compte->libelle }} (Solde: {{ number_format($compte->solde, 2, ',', ' ') }} €)</option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <input type="hidden" name="contexte_type" id="contexte-type" value="membre">
                            <input type="hidden" name="contexte_id" id="contexte-id" value="{{ $optionsPaiement['membre']['id'] }}">
                            <input type="hidden" name="montant" id="montant-final-input" value="{{ $evenement->prix }}">
                            <input type="hidden" name="duree" value="1">
                        </div>
                    </div>

                    <!-- Section Validation -->
                    <div class="card">
                        <div class="card-body">
                            <div class="flex justify-end space-x-3">
                                <a href="{{ route('evenement.index') }}" 
                                   class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                                    Annuler
                                </a>
                                <button type="submit" 
                                        class="px-6 py-2 bg-primary text-white rounded-lg hover:bg-primary/90 transition-colors">
                                    Confirmer l'inscription
                                </button>
                            </div>
                        </div>
                    </div>
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
    const contexteRadios = document.querySelectorAll('.contexte-radio');
    const montantBase = {{ $evenement->prix }};
    const evenementId = {{ $evenement->id }};
    
    // Initialiser avec le contexte par défaut
    calculerMontant();
    
    // Écouter les changements de contexte
    contexteRadios.forEach(radio => {
        radio.addEventListener('change', function() {
            if (this.checked) {
                const contexteType = this.value;
                const contexteId = this.dataset.contextId;
                
                // Mettre à jour les champs cachés
                document.getElementById('contexte-type').value = contexteType;
                document.getElementById('contexte-id').value = contexteId;
                
                // Calculer le montant
                calculerMontant();
            }
        });
    });
    
    async function calculerMontant() {
        const contexteType = document.getElementById('contexte-type').value;
        const contexteId = document.getElementById('contexte-id').value;
        
        if (!contexteType || !contexteId) {
            return;
        }
        
        try {
            const response = await fetch(`/evenements/${evenementId}/calculer`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    contexte: contexteType,
                    contexte_id: contexteId,
                    prix_base: montantBase,
                    duree: 1
                })
            });
            
            const data = await response.json();
            
            if (data.success) {
                // Mettre à jour l'affichage
                document.getElementById('montant-base').textContent = data.montant_base.toLocaleString('fr-FR') + ' FCFA';
                document.getElementById('montant-final').textContent = data.montant_final.toLocaleString('fr-FR') + ' FCFA';
                document.getElementById('montant-final-input').value = data.montant_final;
                
                // Gérer l'affichage des réductions
                if (data.reduction > 0) {
                    document.getElementById('reduction-section').classList.remove('hidden');
                    document.getElementById('reduction-label').textContent = data.reduction_description || 'Réduction';
                    document.getElementById('reduction-montant').textContent = '-' + data.reduction.toLocaleString('fr-FR') + ' FCFA';
                    document.getElementById('economie-section').classList.remove('hidden');
                    document.getElementById('economie-montant').textContent = data.reduction.toLocaleString('fr-FR') + ' FCFA';
                } else {
                    document.getElementById('reduction-section').classList.add('hidden');
                    document.getElementById('economie-section').classList.add('hidden');
                }
                
                // Afficher/masquer la section du compte selon le montant
                if (data.montant_final > 0) {
                    document.getElementById('compte-section').classList.remove('hidden');
                } else {
                    document.getElementById('compte-section').classList.add('hidden');
                }
            } else {
                console.error('Erreur lors du calcul:', data.message);
            }
        } catch (error) {
            console.error('Erreur:', error);
        }
    }
});
</script>
</x-app-layout>
