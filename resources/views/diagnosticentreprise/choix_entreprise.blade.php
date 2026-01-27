<x-app-layout title="Choisir une entreprise" is-sidebar-open="true" is-header-blur="true">
    <main class="main-content w-full px-[var(--margin-x)] pb-8">
        <!-- Header moderne -->
        <div class="mb-2">
            <div class="flex items-center gap-4 mb-2">
                <div class="h-14 w-14 rounded-xl bg-gradient-to-br from-teal-500 to-teal-600 flex items-center justify-center shadow-lg">
                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h-1m2-5h-8"></path>
                    </svg>
                </div>
                <div>
                    <h1 class="text-3xl font-bold text-slate-800 dark:text-navy-50">
                        Sélectionner une entreprise
                    </h1>
                    <p class="mt-2 text-slate-600 dark:text-navy-200 text-lg">
                        Choisissez l'entreprise à évaluer
                    </p>
                </div>
            </div>
        </div>
        <div class="grid grid-cols-12 lg:gap-6">
            <div class="col-span-12 pt-6 lg:col-span-8 lg:pb-6">

        <form method="GET" action="{{ route('diagnosticentreprise.showForm', ['entrepriseId' => 'ID_PLACEHOLDER']) }}"
              onsubmit="event.preventDefault(); 
                        window.location = this.action.replace('ID_PLACEHOLDER', document.getElementById('entreprise_id').value)">
            @csrf
            
            <!-- Input Validation -->
          <div class="card px-4 pb-4 sm:px-5">
            <div class="max-w-xxl">
              <div
                x-data="pages.formValidation.initFormValidationExample"
                class="mt-5 grid grid-cols-1 gap-4 sm:grid-cols-1 sm:gap-5 lg:gap-6"
              >


            <div class="max-w-xxl">
            <label class="block">
                <span>Entreprise</span>
                <select id="entreprise_id" name="entreprise_id"
                        class="form-select mt-1.5 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 hover:border-slate-400 focus:border-primary dark:border-navy-450 dark:bg-navy-700 dark:hover:border-navy-400 dark:focus:border-accent">
                    <option value="">-- Choisir --</option>
                    @foreach ($entreprises as $entreprise)
                        <option value="{{ $entreprise->id }}">{{ $entreprise->nom }}</option>
                    @endforeach
                </select>
            </label>
                </div>

              <div>
            </div>


            <button type="submit" class="btn bg-primary text-white">Continuer</button>
            
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