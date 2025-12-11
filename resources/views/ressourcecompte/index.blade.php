<x-app-layout title="Liste des ressources" is-sidebar-open="true" is-header-blur="true">
    <main class="main-content w-full px-[var(--margin-x)] pb-8">
        <div class="flex items-center space-x-4 py-5 lg:py-6">
          <h2
            class="text-xl font-medium text-slate-800 dark:text-navy-50 lg:text-2xl"
          >
            Liste des ressources
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
            <li>Mes prescriptions</li>
          </ul> -->
    <a href="{{ route('ressourcecompte.create') }}"
       class="btn bg-primary text-white hover:bg-primary-focus">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none"
             viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round"
                  d="M12 4v16m8-8H4"/>
        </svg>
        Ajouter une ressource
    </a>
        </div>
        <div class="grid grid-cols-12 lg:gap-6">
            <div class="col-span-12 pt-6 lg:col-span-8 lg:pb-6">

{{-- <div class="flex justify-end mb-4">
    <form action="{{ route('ressourcecomptes.sync') }}" method="POST" onsubmit="return confirm('Voulez-vous vraiment lancer la synchronisation complète vers Supabase ?')">
        @csrf
        <button type="submit"
            class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-semibold rounded hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
            🔁 Synchroniser avec Supabase
        </button>
    </form>
</div> --}}


@if(session('error'))
    <div class="mb-4 p-3 bg-red-100 text-red-800 rounded">
        {{ session('error') }}
    </div>
@endif

        @if(session('success'))
            <div class="alert flex rounded-lg bg-success px-4 py-4 text-white sm:px-5">{{ session('success') }}</div>
        @endif



@if($types && $types->isNotEmpty())
    <div class="space-y-6">

        @foreach($types as $type)
            <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                {{-- Header Accordion --}}
                <button type="button"
                        class="w-full flex justify-between items-center p-4 font-semibold text-left bg-gradient-to-r from-gray-200 to-gray-300 hover:from-gray-300 hover:to-gray-400 transition-colors duration-200 focus:outline-none focus:ring"
                        onclick="document.getElementById('type-{{ $type->id }}').classList.toggle('hidden')">
                    <span class="text-lg">{{ $type->titre }}</span>
                    <svg class="w-6 h-6 transition-transform duration-300" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>

                {{-- Contenu Accordion --}}
                <div id="type-{{ $type->id }}" class="hidden p-4 space-y-4 bg-gray-50">
                    @if($type->ressourcecomptes && $type->ressourcecomptes->isNotEmpty())
                        @foreach($type->ressourcecomptes as $compte)
                            <div class="border-l-4 border-blue-400 bg-white p-4 rounded shadow-sm hover:shadow-md transition-shadow duration-200">
                                <p class="text-lg font-semibold text-blue-700">
                                    Solde : {{ number_format($compte->solde, 2) }}
                                    - {{ $compte->entreprise?->nom ?? 'Compte personnel' }}
                                </p>

                                <h3 class="mt-3 font-semibold text-gray-800">Transactions :</h3>
                                @if($compte->ressourcetransactions && $compte->ressourcetransactions->isNotEmpty())
                                    <ul class="mt-2 space-y-3">
                                        @foreach($compte->ressourcetransactions as $tx)
                                            <li class="p-3 rounded-lg bg-gray-100 flex flex-col md:flex-row md:justify-between md:items-center shadow-sm hover:bg-gray-200 transition-colors duration-150">
                                                <div>
                                                    <span class="font-bold text-gray-800">{{ number_format($tx->montant, 2) }}</span>
                                                    <span class="text-gray-600">- {{ $tx->reference }}</span>
                                                    ({{ $tx->operationtype->titre ?? 'N/A' }})<br />
                                                    <span>{{ optional($tx->created_at)->format('d M Y H:i') }}</span>
                                                </div>
                                                <div class="mt-2 md:mt-0 text-gray-500 flex flex-col md:flex-row md:items-center space-y-1 md:space-y-0 md:space-x-2">
                                                    <span class="px-2 py-1 rounded text-sm font-semibold
                                                        @if($tx->formationRessource) bg-blue-200 text-blue-800
                                                        @elseif($tx->prestationRessource) bg-green-200 text-green-800
                                                        @elseif($tx->evenementRessource) bg-orange-200 text-orange-800
                                                        @elseif($tx->espaceRessource) bg-purple-200 text-purple-800
                                                        @else bg-gray-200 text-gray-800 @endif">
                                                        @if($tx->formationRessource)
                                                            Formation - {{ $tx->formationRessource->formation->titre ?? 'N/A' }}
                                                        @elseif($tx->prestationRessource)
                                                            Prestation - {{ $tx->prestationRessource->prestation->titre ?? 'N/A' }}
                                                        @elseif($tx->evenementRessource)
                                                            Événement - {{ $tx->evenementRessource->evenement->titre ?? 'N/A' }}
                                                        @elseif($tx->espaceRessource)
                                                            Espace - {{ $tx->espaceRessource->espace->titre ?? 'N/A' }}
                                                        @else
                                                        {{ $tx->ressourcecompte->entreprise ? $tx->ressourcecompte->entreprise->nom : '' }}
                                                        @endif
                                                    </span>
                                                </div>
                                            </li>
                                        @endforeach
                                    </ul>
                                @else
                                    <p class="text-gray-500">Aucune transaction</p>
                                @endif
                            </div>
                        @endforeach
                    @else
                        <p class="text-gray-500">Aucune ressource pour ce type</p>
                    @endif
                </div>
            </div>
        @endforeach

    </div>
@else
    <p class="text-gray-500">Aucun type de ressource trouvé.</p>
@endif


          </div> 

            <div class="col-span-12 py-6 lg:sticky lg:bottom-0 lg:col-span-4 lg:self-end">
                @include('layouts.sidebar')
            </div>    

        </div>
      </main>
</x-app-layout>
