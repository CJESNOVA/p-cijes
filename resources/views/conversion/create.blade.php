<x-app-layout title="Nouvelle conversion" is-sidebar-open="true" is-header-blur="true">
    <main class="main-content w-full px-[var(--margin-x)] pb-8">
        <!-- Header moderne -->
        <div class="mb-2">
            <div class="flex items-center gap-4 mb-2">
                <div class="h-14 w-14 rounded-xl bg-gradient-to-br from-green-500 to-green-600 flex items-center justify-center shadow-lg">
                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path>
                    </svg>
                </div>
                <div>
                    <h1 class="text-3xl font-bold text-slate-800 dark:text-navy-50">
                        Nouvelle conversion
                    </h1>
                    <p class="mt-2 text-slate-600 dark:text-navy-200 text-lg">
                        Convertissez vos points en récompenses
                    </p>
                </div>
            </div>
        </div>
        <div class="grid grid-cols-12 lg:gap-6">
            <div class="col-span-12 pt-6 lg:col-span-8 lg:pb-6">

                <!-- Messages modernes -->
                @if(session('success'))
                    <div class="alert flex rounded-lg bg-green-500 px-6 py-4 text-white mb-6 shadow-lg">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        {{ session('success') }}
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert flex rounded-lg bg-red-500 px-6 py-4 text-white mb-6 shadow-lg">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        {{ session('error') }}
                    </div>
                @endif

                <form action="{{ route('conversion.store') }}" method="POST">
                    @csrf

                      <!-- Input Validation --> 
                      <div class="card px-4 pb-4 sm:px-5"> 
                        <div class="max-w-xxl"> 
                          <div x-data="pages.formValidation.initFormValidationExample" 
                          class="mt-5 grid grid-cols-1 gap-4 sm:grid-cols-1 sm:gap-5 lg:gap-6" >

                            <!-- Compte source -->
                            <div>
                                <label for="compte_source_id" class="block font-semibold mb-1">
                                    Choisir le compte source
                                </label>
                                <select name="compte_source_id" id="compte_source_id" class="w-full border rounded p-2" required>
                                    <option value="">-- Sélectionner une ressource --</option>
                                    @foreach($comptesSource as $compte)
                                        <option value="{{ $compte->id }}">
                                            {{ $compte->nom_complet ?? 'N/A' }} 
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Montant -->
                            <div>
                                <label for="montant" class="block font-semibold mb-1">
                                    Montant à convertir
                                </label>
                                <input type="number" step="0.01" min="0.01" name="montant" id="montant"
                                       class="w-full border rounded p-2" placeholder="Ex: 100.00" required>
                            </div>

                            <!-- Bouton -->
                            <div class="mt-4">
                                <button type="submit"
                                    class="btn bg-primary font-medium text-white hover:bg-primary-focus 
                                           focus:bg-primary-focus active:bg-primary-focus/90 
                                           dark:bg-accent dark:hover:bg-accent-focus 
                                           dark:focus:bg-accent-focus dark:active:bg-accent/90 w-full">
                                    Convertir
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
</x-app-layout>
