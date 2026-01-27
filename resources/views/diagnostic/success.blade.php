<x-app-layout title="Diagnostic Résultat" is-sidebar-open="true" is-header-blur="true">
    <main class="main-content w-full px-[var(--margin-x)] pb-8">
        <!-- Header moderne -->
        <div class="mb-2">
            <div class="flex items-center gap-4 mb-2">
                <div class="h-14 w-14 rounded-xl bg-gradient-to-br from-green-500 to-green-600 flex items-center justify-center shadow-lg">
                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div>
                    <h1 class="text-3xl font-bold text-slate-800 dark:text-navy-50">
                        Diagnostic Résultat
                    </h1>
                    <p class="mt-2 text-slate-600 dark:text-navy-200 text-lg">
                        Consultez les résultats de votre évaluation
                    </p>
                </div>
            </div>
        </div>
        <div class="grid grid-cols-12 lg:gap-6">
            <div class="col-span-12 pt-6 lg:col-span-8 lg:pb-6">

          <!-- Input Validation -->
          <div class="card px-4 pb-4 sm:px-5">
            <div class="max-w-xxl">
              <div
                x-data="pages.formValidation.initFormValidationExample"
                class="mt-5 grid grid-cols-1 gap-4 sm:grid-cols-2 sm:gap-5 lg:gap-6"
              >
   
        <div class="mt-10 text-center">
            <h1 class="text-2xl font-bold text-green-600">Diagnostic enregistré avec succès !</h1>
            <p class="text-gray-600 mt-2">Votre accompagnement a été créé. Vous pouvez maintenant consulter et gérer vos plans d'action.</p>
            
            <div class="mt-6 space-x-4">
                <a href="{{ route('diagnostic.form') }}" class="inline-block btn bg-gray-500 text-white hover:bg-gray-600">
                    <i class="fas fa-arrow-left mr-2"></i>Refaire un diagnostic
                </a>
                @if(session('diagnostic_id'))
                    <a href="{{ route('diagnostic.plans', session('diagnostic_id')) }}" class="inline-block btn bg-primary text-white hover:bg-blue-600">
                        <i class="fas fa-tasks mr-2"></i>Voir les plans d'accompagnement
                    </a>
                @endif
            </div>
        </div>

              </div>
            </div>
            
          </div> 

          </div> 

            <div class="col-span-12 py-6 lg:sticky lg:bottom-0 lg:col-span-4 lg:self-end">
                @include('layouts.sidebar')
            </div>    

        </div>
      </main>
</x-app-layout>