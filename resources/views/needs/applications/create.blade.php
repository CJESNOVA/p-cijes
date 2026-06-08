<x-app-layout title="Postuler à un besoin" is-sidebar-open="true" is-header-blur="true">

    <main class="main-content w-full px-[var(--margin-x)] pb-8">

        <!-- Header -->
        <div class="mb-10">
            <div class="flex items-center gap-4 mb-8">
                <a href="{{ route('needs.show', $needId) }}" class="flex items-center gap-2 text-primary dark:text-accent hover:underline">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                    Retour au besoin
                </a>
            </div>
            <div class="flex items-center gap-4 mb-8">
                <div class="h-14 w-14 rounded-xl bg-gradient-to-br from-green-500 to-green-600 flex items-center justify-center shadow-lg">
                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div>
                    <h1 class="text-3xl font-bold text-slate-800 dark:text-navy-50">Postuler à ce besoin</h1>
                    <p class="mt-2 text-slate-600 dark:text-navy-200 text-lg">Présentez votre candidature</p>
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
                <form method="POST" action="{{ route('needs.storeApplication', $needId) }}">
                    @csrf

                    <!-- Card principale -->
                    <div class="card px-4 pb-4 sm:px-5">
                        <div class="mt-5">

                            <!-- Identifiant du candidat -->
                            <div class="mb-6">
                                <label class="block mb-2">
                                    <span class="font-medium text-slate-700 dark:text-navy-100">Identifiant du candidat <span class="text-error">*</span></span>
                                </label>
                                <input type="text"
                                       name="applicant_id"
                                       value="{{ old('applicant_id', auth()->user()->email ?? '') }}"
                                       class="form-input w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 placeholder:text-slate-400/70 hover:border-slate-400 focus:border-primary dark:border-navy-450 dark:bg-navy-700 dark:text-navy-100 dark:hover:border-navy-400 dark:focus:border-accent"
                                       placeholder="Votre email ou identifiant"
                                       required>
                                @error('applicant_id')
                                    <span class="text-xs text-error mt-1">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Grille 2 colonnes -->
                            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 sm:gap-5 lg:gap-6 mb-6">

                                <!-- Montant demandé -->
                                <div>
                                    <label class="block mb-2">
                                        <span class="font-medium text-slate-700 dark:text-navy-100">Montant demandé (FCFA)</span>
                                    </label>
                                    <input type="number"
                                           name="expected_amount"
                                           value="{{ old('expected_amount') }}"
                                           step="0.01"
                                           min="0"
                                           class="form-input w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 placeholder:text-slate-400/70 hover:border-slate-400 focus:border-primary dark:border-navy-450 dark:bg-navy-700 dark:text-navy-100 dark:hover:border-navy-400 dark:focus:border-accent"
                                           placeholder="0">
                                    @error('expected_amount')
                                        <span class="text-xs text-error mt-1">{{ $message }}</span>
                                    @enderror
                                </div>

                                <!-- URL Portfolio -->
                                <div>
                                    <label class="block mb-2">
                                        <span class="font-medium text-slate-700 dark:text-navy-100">Lien vers votre portfolio</span>
                                    </label>
                                    <input type="url"
                                           name="portfolio_url"
                                           value="{{ old('portfolio_url') }}"
                                           class="form-input w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 placeholder:text-slate-400/70 hover:border-slate-400 focus:border-primary dark:border-navy-450 dark:bg-navy-700 dark:text-navy-100 dark:hover:border-navy-400 dark:focus:border-accent"
                                           placeholder="https://example.com/portfolio">
                                    @error('portfolio_url')
                                        <span class="text-xs text-error mt-1">{{ $message }}</span>
                                    @enderror
                                </div>

                                <!-- Message de candidature -->
                                <div class="sm:col-span-2">
                                    <label class="block mb-2">
                                        <span class="font-medium text-slate-700 dark:text-navy-100">Lettre de motivation <span class="text-error">*</span></span>
                                    </label>
                                    <textarea name="message"
                                              rows="6"
                                              class="form-textarea w-full rounded-lg border border-slate-300 bg-white p-2.5 placeholder:text-slate-400/70 hover:border-slate-400 focus:border-primary dark:border-navy-450 dark:bg-navy-700 dark:text-navy-100 dark:hover:border-navy-400 dark:focus:border-accent"
                                              placeholder="Présentez-vous et expliquez pourquoi vous êtes le candidat idéal pour ce besoin..."
                                              required>{{ old('message') }}</textarea>
                                    @error('message')
                                        <span class="text-xs text-error mt-1">{{ $message }}</span>
                                    @enderror
                                </div>

                            </div>

                            <!-- Boutons d'action -->
                            <div class="mt-8 flex gap-3 justify-end">
                                <a href="{{ route('needs.show', $needId) }}"
                                   class="btn rounded-lg border border-slate-300 bg-white px-6 py-2.5 font-medium text-slate-700 hover:bg-slate-50 dark:border-navy-450 dark:bg-navy-700 dark:text-navy-100 dark:hover:bg-navy-600">
                                    Annuler
                                </a>
                                <button type="submit"
                                        class="btn rounded-lg bg-green-500 px-6 py-2.5 font-medium text-white hover:bg-green-600 dark:bg-green-600 dark:hover:bg-green-700">
                                    <svg class="inline-block h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    Soumettre ma candidature
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

</x-app-layout>
