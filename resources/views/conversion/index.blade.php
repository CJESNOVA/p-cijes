<x-app-layout title="Conversions effectuées" is-sidebar-open="true" is-header-blur="true">
    <main class="main-content w-full px-[var(--margin-x)] pb-8">
        <!-- Header moderne -->
        <div class="mb-2">
            <div class="flex items-center justify-between mb-2">
                <div class="flex items-center gap-4">
                    <div class="h-14 w-14 rounded-xl bg-gradient-to-br from-green-500 to-green-600 flex items-center justify-center shadow-lg">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"></path>
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-3xl font-bold text-slate-800 dark:text-navy-50">
                            Conversions effectuées
                        </h1>
                        <p class="mt-2 text-slate-600 dark:text-navy-200 text-lg">
                            Suivez vos échanges de ressources
                        </p>
                    </div>
                </div>
                <a href="{{ route('conversion.create') }}"
                   class="px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Effectuer une conversion
                </a>
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
