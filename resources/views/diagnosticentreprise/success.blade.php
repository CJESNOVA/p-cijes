<x-app-layout title="Diagnostic Résultat" is-sidebar-open="true" is-header-blur="true">
    <main class="main-content w-full px-[var(--margin-x)] pb-8">
        <div class="flex items-center space-x-4 py-5 lg:py-6">
          <h2
            class="text-xl font-medium text-slate-800 dark:text-navy-50 lg:text-2xl"
          >
            Diagnostic Résultat
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
            <li>Diagnostic</li>
          </ul> -->
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
   
    @if (session('success'))
        <div class="mt-10 text-center">
            <h1 class="text-2xl font-bold text-green-600">{{ session('success') }}</h1>
            <p class="text-gray-600 mt-2">Votre accompagnement d'entreprise a été créé. Vous pouvez maintenant consulter et gérer vos plans d'action.</p>
            
            <div class="mt-6 space-x-4">
                <a href="{{ route('diagnosticentreprise.indexForm') }}" class="inline-block btn bg-gray-500 text-white hover:bg-gray-600">
                    <i class="fas fa-arrow-left mr-2"></i>Retour aux diagnostics
                </a>
                @if(session('diagnostic_id'))
                    <a href="{{ route('diagnosticentreprise.plans', session('diagnostic_id')) }}" class="inline-block btn bg-purple-500 text-white hover:bg-purple-600">
                        <i class="fas fa-tasks mr-2"></i>Voir les plans d'accompagnement
                    </a>
                @endif
            </div>
        </div>
    @endif

    @if (session('error'))
        <div class="mt-10 text-center">
            <h1 class="text-2xl font-bold text-red-600">{{ session('error') }}</h1>
            <!--<a href="{{ route('diagnosticentreprise.indexForm') }}" class="mt-4 inline-block btn bg-primary text-white">Retour un diagnostic</a>-->
        </div>
    @endif

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