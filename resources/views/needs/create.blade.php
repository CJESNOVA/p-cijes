<x-app-layout title="Publier un besoin" is-sidebar-open="true" is-header-blur="true">

    <main class="main-content w-full px-[var(--margin-x)] pb-8">

        <!-- Header moderne -->
        <div class="mb-10">
            <div class="flex items-center gap-4 mb-8">
                <div class="h-14 w-14 rounded-xl bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center shadow-lg">
                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                    </svg>
                </div>
                <div>
                    <h1 class="text-3xl font-bold text-slate-800 dark:text-navy-50">Publier un besoin</h1>
                    <p class="mt-2 text-slate-600 dark:text-navy-200 text-lg">Trouvez rapidement les bons profils pour votre projet</p>
                </div>
            </div>
        </div>

        <!-- Messages d'alerte -->
        @if(session('success'))
            <div class="alert flex rounded-lg bg-success px-4 py-4 text-white sm:px-5 mb-6">
                <svg class="h-5 w-5 flex-none" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                </svg>
                <div class="ml-3">{{ session('success') }}</div>
            </div>
        @endif

        @if(session('error'))
            <div class="alert flex rounded-lg bg-error px-4 py-4 text-white sm:px-5 mb-6">
                <svg class="h-5 w-5 flex-none" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                </svg>
                <div class="ml-3">{{ session('error') }}</div>
            </div>
        @endif

        <div class="grid grid-cols-12 gap-6">
            <!-- Formulaire -->
            <div class="col-span-12 lg:col-span-8">
                <form method="POST" action="{{ route('needs.store') }}" enctype="multipart/form-data">
                    @csrf

                    <!-- Card principale -->
                    <div class="card px-4 pb-4 sm:px-5">
                        <div class="mt-5">

                            <!-- Entreprise -->
                            <div class="mb-6">
                                <label class="block mb-2">
                                    <span class="font-medium text-slate-700 dark:text-navy-100">Entreprise</span>
                                </label>
                                <select name="entreprise_id" 
                                        class="form-select w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-slate-700 placeholder:text-slate-400/70 hover:border-slate-400 focus:border-primary dark:border-navy-450 dark:bg-navy-700 dark:text-navy-100 dark:hover:border-navy-400 dark:focus:border-accent"
                                        required>
                                    <option value="">-- Choisir une entreprise --</option>
                                    @foreach($entreprises as $entreprise)
                                        <option value="{{ $entreprise->id }}">{{ $entreprise->nom }}</option>
                                    @endforeach
                                </select>
                                @error('entreprise_id')
                                    <span class="text-xs text-error mt-1">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Grid 2 colonnes pour les champs principaux -->
                            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 sm:gap-5 lg:gap-6 mb-6">

                                <!-- Titre -->
                                <div class="sm:col-span-2">
                                    <label class="block mb-2">
                                        <span class="font-medium text-slate-700 dark:text-navy-100">Titre du besoin <span class="text-error">*</span></span>
                                    </label>
                                    <input type="text"
                                           name="title"
                                           value="{{ old('title') }}"
                                           class="form-input w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 placeholder:text-slate-400/70 hover:border-slate-400 focus:border-primary dark:border-navy-450 dark:bg-navy-700 dark:text-navy-100 dark:hover:border-navy-400 dark:focus:border-accent"
                                           placeholder="Ex: Recherche développeur web senior"
                                           required>
                                    @error('title')
                                        <span class="text-xs text-error mt-1">{{ $message }}</span>
                                    @enderror
                                </div>

                                <!-- Description -->
                                <div class="sm:col-span-2">
                                    <label class="block mb-2">
                                        <span class="font-medium text-slate-700 dark:text-navy-100">Description détaillée <span class="text-error">*</span></span>
                                    </label>
                                    <textarea name="description"
                                              rows="5"
                                              class="form-textarea w-full rounded-lg border border-slate-300 bg-white p-2.5 placeholder:text-slate-400/70 hover:border-slate-400 focus:border-primary dark:border-navy-450 dark:bg-navy-700 dark:text-navy-100 dark:hover:border-navy-400 dark:focus:border-accent"
                                              placeholder="Décrivez en détail votre besoin, les tâches principales, les compétences requises..."
                                              required>{{ old('description') }}</textarea>
                                    @error('description')
                                        <span class="text-xs text-error mt-1">{{ $message }}</span>
                                    @enderror
                                </div>

                                <!-- Profils recherchés -->
                                <div>
                                    <label class="block mb-2">
                                        <span class="font-medium text-slate-700 dark:text-navy-100">Profils recherchés</span>
                                    </label>
                                    <input type="text"
                                           name="profiles"
                                           value="{{ old('profiles') }}"
                                           class="form-input w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 placeholder:text-slate-400/70 hover:border-slate-400 focus:border-primary dark:border-navy-450 dark:bg-navy-700 dark:text-navy-100 dark:hover:border-navy-400 dark:focus:border-accent"
                                           placeholder="Ex: comptable, designer, développeur...">
                                    @error('profiles')
                                        <span class="text-xs text-error mt-1">{{ $message }}</span>
                                    @enderror
                                </div>

                                <!-- Date de clôture -->
                                <div>
                                    <label class="block mb-2">
                                        <span class="font-medium text-slate-700 dark:text-navy-100">Date de clôture</span>
                                    </label>
                                    <input type="date"
                                           name="closingDate"
                                           value="{{ old('closingDate') }}"
                                           class="form-input w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 placeholder:text-slate-400/70 hover:border-slate-400 focus:border-primary dark:border-navy-450 dark:bg-navy-700 dark:text-navy-100 dark:hover:border-navy-400 dark:focus:border-accent">
                                    @error('closingDate')
                                        <span class="text-xs text-error mt-1">{{ $message }}</span>
                                    @enderror
                                </div>

                                <!-- Conditions d'éligibilité -->
                                <div class="sm:col-span-2">
                                    <label class="block mb-2">
                                        <span class="font-medium text-slate-700 dark:text-navy-100">Conditions d'éligibilité</span>
                                    </label>
                                    <textarea name="eligibility"
                                              rows="3"
                                              class="form-textarea w-full rounded-lg border border-slate-300 bg-white p-2.5 placeholder:text-slate-400/70 hover:border-slate-400 focus:border-primary dark:border-navy-450 dark:bg-navy-700 dark:text-navy-100 dark:hover:border-navy-400 dark:focus:border-accent"
                                              placeholder="Ex: 3 ans d'expérience minimum, disponible à partir du mois prochain...">{{ old('eligibility') }}</textarea>
                                    @error('eligibility')
                                        <span class="text-xs text-error mt-1">{{ $message }}</span>
                                    @enderror
                                </div>

                                <!-- Pièce jointe -->
                                <div class="sm:col-span-2">
                                    <label class="block mb-2">
                                        <span class="font-medium text-slate-700 dark:text-navy-100">Pièce jointe</span>
                                    </label>
                                    <input type="file"
                                           name="file"
                                           accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png"
                                           class="form-input w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-slate-700 file:mr-3 file:rounded file:border-0 file:bg-primary file:px-3 file:py-1.5 file:text-white hover:border-slate-400 focus:border-primary dark:border-navy-450 dark:bg-navy-700 dark:text-navy-100 dark:hover:border-navy-400 dark:focus:border-accent">
                                    <p class="text-xs text-slate-500 dark:text-navy-300 mt-1">Fichiers acceptés: PDF, Word, Excel, Images (max {{ \App\Support\UploadLimit::recommendedLabel() }})</p>
                                    @error('file')
                                        <span class="text-xs text-error mt-1">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <!-- Boutons d'action -->
                            <div class="mt-8 flex gap-3 justify-end">
                                <a href="{{ route('dashboard') }}"
                                   class="btn rounded-lg border border-slate-300 bg-white px-6 py-2.5 font-medium text-slate-700 hover:bg-slate-50 dark:border-navy-450 dark:bg-navy-700 dark:text-navy-100 dark:hover:bg-navy-600">
                                    Annuler
                                </a>
                                <button type="submit"
                                        class="btn rounded-lg bg-primary px-6 py-2.5 font-medium text-white hover:bg-primary-focus dark:bg-accent dark:hover:bg-accent-focus">
                                    <svg class="inline-block h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                    </svg>
                                    Publier le besoin
                                </button>
                            </div>

                        </div>
                    </div>
                </form>
            </div>

            <!-- Sidebar -->
            <div class="col-span-12 py-6 lg:sticky lg:bottom-0 lg:col-span-4 lg:self-end">
                @include('layouts.sidebar')
            </div>
        </div>

    </main>
    @include('partials.upload-size-guard')

</x-app-layout>
