<x-app-layout title="Demande de SIKA" is-sidebar-open="true" is-header-blur="true">
    <main class="main-content w-full px-[var(--margin-x)] pb-8">
        <!-- Header moderne -->
        <div class="mb-2">
            <div class="flex items-center gap-4 mb-2">
                <div class="h-14 w-14 rounded-xl bg-gradient-to-br from-[#4FBE96] to-[#4FBE96] flex items-center justify-center shadow-lg">
                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                </div>
                <div>
                    <h1 class="text-3xl font-bold text-slate-800 dark:text-navy-50">
                        Demande de SIKA
                    </h1>
                    <p class="mt-2 text-slate-600 dark:text-navy-200 text-lg">
                        Type: {{ $type->titre }}
                    </p>
                </div>
            </div>
        </div>
        <div class="grid grid-cols-12 lg:gap-6">
            <div class="col-span-12 pt-6 lg:col-span-8 lg:pb-6">

                <!-- Messages modernes -->
                @if (session('success'))
                    <div class="alert flex rounded-lg bg-green-500 px-6 py-4 text-white mb-6 shadow-lg">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m0 6l4 4 4m0 6-4"></path>
                        </svg>
                        {{ session('success') }}
                    </div>
                @endif

                @if (session('error'))
                    <div class="alert flex rounded-lg bg-red-500 px-6 py-4 text-white mb-6 shadow-lg">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 0 9 0 9z"></path>
                        </svg>
                        {{ session('error') }}
                    </div>
                @endif

                <!-- Carte d'information SIKA -->
                <div class="card bg-gradient-to-r from-blue-50 to-indigo-50 border-l-4 border-blue-400 p-4 mb-6">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-blue-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 000 2h1a1 1 0 000 2v3a1 1 0 000 2H9z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-blue-700">
                                <strong>SIKA</strong> est un dispositif de financement CIJES Africa pour soutenir les entrepreneurs. 
                                Votre demande sera étudiée par notre comité de validation.
                            </p>
                        </div>
                    </div>
                </div>

                <form method="POST" action="{{ route('ressourcecompte.storeDemande') }}" class="space-y-4">
                    @csrf

                    <div class="card px-4 pb-4 sm:px-5">
                        <div class="max-w-xxl">
                            <div class="mt-5 grid grid-cols-1 gap-4 sm:grid-cols-2 sm:gap-5 lg:gap-6">

                                <!-- Informations du demandeur -->
                                <div class="sm:col-span-2 mt-4">
                                    <h3 class="text-lg font-semibold text-slate-800 mb-4">Informations du demandeur</h3>
                                </div>

                                <div>
                                    <label class="block font-medium mb-1">Nom complet</label>
                                    <input type="text" 
                                        class="w-full border rounded-lg px-3 py-2 bg-gray-50"
                                        value="{{ $membre->nom }} {{ $membre->prenoms }}" 
                                        readonly>
                                </div>

                                <div>
                                    <label class="block font-medium mb-1">Email</label>
                                    <input type="email" 
                                        class="w-full border rounded-lg px-3 py-2 bg-gray-50"
                                        value="{{ $membre->email }}" 
                                        readonly>
                                </div>

                                <!-- Sélection de l'entreprise -->
                                @if($entreprises->isNotEmpty())
                                    <div class="sm:col-span-2">
                                        <label class="block font-medium mb-1">Entreprise (optionnel)</label>
                                        <select name="entreprise_id"
                                            class="form-select mt-1.5 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 
                                                hover:border-slate-400 focus:border-primary dark:border-navy-450 
                                                dark:bg-navy-700 dark:hover:border-navy-400 dark:focus:border-accent">
                                            <option value="">-- Personnel (aucune entreprise) --</option>
                                            @foreach ($entreprises as $e)
                                                <option value="{{ $e->entreprise->id }}" {{ old('entreprise_id') == $e->entreprise->id ? 'selected' : '' }}>
                                                    {{ $e->entreprise->nom }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('entreprise_id')
                                            <span class="text-red-500 text-sm">{{ $message }}</span>
                                        @enderror
                                    </div>
                                @else
                                    <input type="hidden" name="entreprise_id" value="">
                                @endif

                                <!-- Détails de la demande -->
                                <div class="sm:col-span-2 mt-4">
                                    <h3 class="text-lg font-semibold text-slate-800 mb-4">Détails de la demande</h3>
                                </div>

                                <div>
                                    <label class="block font-medium mb-1">Montant demandé (FCFA) <span class="text-red-500">*</span></label>
                                    <input type="number" name="montant_demande" id="montant_demande"
                                        class="w-full border rounded-lg px-3 py-2 focus:border-primary focus:ring-1 focus:ring-primary"
                                        value="{{ old('montant_demande') }}" 
                                        min="1000" 
                                        step="100"
                                        required>
                                    <p class="mt-1 text-sm text-gray-500">Montant minimum : 1,000 FCFA</p>
                                    @error('montant_demande')
                                        <span class="text-red-500 text-sm">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div>
                                    <label class="block font-medium mb-1">Description de la demande <span class="text-red-500">*</span></label>
                                    <textarea name="description" 
                                        class="w-full border rounded-lg px-3 py-2 focus:border-primary focus:ring-1 focus:ring-primary"
                                        rows="5" 
                                        placeholder="Décrivez brièvement l'objet de votre demande de financement..."
                                        required>{{ old('description') }}</textarea>
                                    <p class="mt-1 text-sm text-gray-500">Minimum 10 caractères, maximum 500 caractères</p>
                                    @error('description')
                                        <span class="text-red-500 text-sm">{{ $message }}</span>
                                    @enderror
                                </div>

                                <!-- Boutons -->
                                <div class="sm:col-span-2 mt-6">
                                    <button type="submit"
                                        class="px-6 py-3 bg-[#4FBE96] hover:bg-[#4FBE96]/90 text-white rounded-lg transition font-medium">
                                        <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                                        </svg>
                                        Soumettre la demande
                                    </button>
                                    <a href="{{ route('ressourcecompte.index') }}"
                                        class="ml-2 px-6 py-3 bg-gray-200 hover:bg-gray-300 text-gray-800 rounded-lg transition font-medium">
                                        Annuler
                                    </a>
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
</x-app-layout>
