<x-app-layout title="Conversions effectuées" is-sidebar-open="true" is-header-blur="true">
    <main class="main-content w-full px-[var(--margin-x)] pb-8">
        <div class="flex items-center space-x-4 py-5 lg:py-6">
          <h2
            class="text-xl font-medium text-slate-800 dark:text-navy-50 lg:text-2xl"
          >
            Conversions effectuées
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
            <li>Conversions effectuées</li>
          </ul> -->
            <a href="{{ route('conversion.create') }}"
               class="btn bg-primary text-white font-medium px-4 py-2 rounded-lg shadow hover:bg-primary-focus transition">
                + Effectuer une conversion
            </a>
        </div>
        <div class="grid grid-cols-12 lg:gap-6">
            <div class="col-span-12 pt-6 lg:col-span-8 lg:pb-6">

        @if(session('success'))
            <div class="alert flex rounded-lg bg-success px-4 py-4 text-white sm:px-5">{{ session('success') }}</div>
        @endif


@if($conversions->isNotEmpty())
              <div class="grid grid-cols-1 gap-4 sm:grid-cols-1 sm:gap-5 lg:grid-cols-1 lg:gap-6 xl:grid-cols-1">
<div class="grid gap-6 md:grid-cols-1 lg:grid-cols-1">
    @forelse($conversions as $conv)
        <div class="bg-white dark:bg-navy-700 shadow rounded-2xl p-5 border border-gray-200 dark:border-navy-600">
            <div class="flex items-center justify-between mb-3">
                <h3 class="text-lg font-semibold text-slate-800 dark:text-navy-50">
                    Conversion #{{ $conv->id }}
                </h3>
                <span class="text-xs px-2 py-1 rounded-full bg-primary/10 text-primary">
                    {{ $conv->taux }}
                </span>
            </div>

            <div class="space-y-2 text-sm">
                <p>
                    <span class="font-medium text-slate-600 dark:text-navy-100">Source :</span>
                    {{ $conv->ressourcetransactionsource->ressourcecompte->nom_complet ?? 'N/A' }}
                    <br>
                    <span class="text-slate-500">Montant :</span>
                    <span class="font-bold">{{ number_format($conv->ressourcetransactionsource->montant, 2) }}</span>
                </p>

                <p>
                    <span class="font-medium text-slate-600 dark:text-navy-100">Cible :</span>
                    {{ $conv->ressourcetransactioncible->ressourcecompte->nom_complet ?? 'N/A' }}
                    <br>
                    <span class="text-slate-500">Montant après conversion :</span>
                    <span class="font-bold">{{ number_format($conv->ressourcetransactioncible->montant, 2) }}</span>
                </p>
            </div>

        </div>
    @empty
        <div class="col-span-full text-center text-slate-500 py-6">
            Aucune conversion trouvée.
        </div>
    @endforelse
</div>

          </div>
@else
    <p>Aucune conversion trouvée.</p>
@endif
          </div> 

            <div class="col-span-12 py-6 lg:sticky lg:bottom-0 lg:col-span-4 lg:self-end">
                @include('layouts.sidebar')
            </div>    

        </div>
      </main>
</x-app-layout>
