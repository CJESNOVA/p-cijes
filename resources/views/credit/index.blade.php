<x-app-layout title="Mes Crédits disponibles" is-sidebar-open="true" is-header-blur="true">
    <main class="main-content w-full px-[var(--margin-x)] pb-8">
        <div class="flex items-center space-x-4 py-5 lg:py-6">
          <h2
            class="text-xl font-medium text-slate-800 dark:text-navy-50 lg:text-2xl"
          >
            Mes Crédits disponibles
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
            <li>Mes Crédits disponibles</li>
          </ul> -->
        </div>
        <div class="grid grid-cols-12 lg:gap-6">
            <div class="col-span-12 pt-6 lg:col-span-8 lg:pb-6">

@if($credits)
              <div class="grid grid-cols-2 gap-4 sm:grid-cols-2 sm:gap-5 lg:grid-cols-2 lg:gap-6 xl:grid-cols-2">



    @if($credits->isEmpty())
        <p>Aucun crédit trouvé.</p>
    @else
                        
                @foreach($credits as $credit)
                <div class="card p-4">
                    <h3 class="font-bold mt-2">
                        Entreprise : {{ $credit->entreprise->nom ?? '—' }}
                    </h3>

                    <p class="text-sm mt-2">
                        Montant total : 
                        <span class="font-semibold">{{ number_format($credit->montanttotal, 0, ',', ' ') }} FCFA</span>
                    </p>

                    <p class="text-sm mt-2">
                        Montant utilisé : 
                        <span class="text-red-600">{{ number_format($credit->montantutilise, 0, ',', ' ') }} FCFA</span>
                    </p>

                    <p class="text-sm mt-2">
                        Solde restant : 
                        <span class="text-blue-600 font-bold">
                            {{ number_format($credit->montanttotal - $credit->montantutilise, 0, ',', ' ') }} FCFA
                        </span>
                    </p>

                    <p class="text-sm mt-2">
                        Type : {{ $credit->credittype->titre ?? '—' }}
                    </p>

                    <p class="text-sm mt-2">
                        Statut : 
                        <span class="px-2 py-1 rounded text-white 
                            {{ $credit->creditstatut->titre == 'Actif' ? 'bg-green-500' : 'bg-red-500' }}">
                            {{ $credit->creditstatut->titre ?? '—' }}
                        </span>
                    </p>

                    <p class="text-sm mt-2">
                        Partenaire : {{ $credit->partenaire->titre ?? '—' }}
                    </p>

                    <p class="text-sm mt-2">
                        Attribué par : {{ $credit->user->name ?? 'Admin' }}
                    </p>

                    <p class="text-xs mt-2 text-gray-500">
                        Délivré le {{ \Carbon\Carbon::parse($credit->datecredit)->format('d/m/Y') }}
                    </p>
                    
                <br />
                    <a href="#" class="btn bg-blue-500 text-white">A utiliser</a>
                    
                </div>
                @endforeach
        @endif
          </div>
@endif
          </div> 

            <div class="col-span-12 py-6 lg:sticky lg:bottom-0 lg:col-span-4 lg:self-end">
                @include('layouts.sidebar')
            </div>    

        </div>
      </main>
</x-app-layout>