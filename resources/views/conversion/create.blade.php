<x-app-layout title="Nouvelle conversion" is-sidebar-open="true" is-header-blur="true">
    <main class="main-content w-full px-[var(--margin-x)] pb-8">
        <div class="flex items-center space-x-4 py-5 lg:py-6">
          <h2
            class="text-xl font-medium text-slate-800 dark:text-navy-50 lg:text-2xl"
          >
            Nouvelle conversion
          </h2>
          <div class="hidden h-full py-1 sm:flex">
            <div class="h-full w-px bg-slate-300 dark:bg-navy-600"></div>
          </div>
          <!-- <ul class="hidden flex-wrap items-center space-x-2 sm:flex">
            <li class="flex items-center space-x-2">
              <a
                class="text-primary transition-colors hover:text-primary-focus dark:text-accent-light dark:hover:text-accent"
                href="#"
                >Forms</a
              >
              <svg
                x-ignore
                xmlns="http://www.w3.org/2000/svg"
                class="size-4"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M9 5l7 7-7 7"
                />
              </svg>
            </li>
            <li>Nouvelle conversion</li>
          </ul> -->
        </div>
        <div class="grid grid-cols-12 lg:gap-6">
            <div class="col-span-12 pt-6 lg:col-span-8 lg:pb-6">

        
        @if(session('error'))
            <div class="alert flex rounded-lg bg-error px-4 py-4 text-white sm:px-5">{{ session('error') }}</div>
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
