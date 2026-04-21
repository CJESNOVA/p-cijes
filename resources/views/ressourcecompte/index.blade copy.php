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


        @if(session('success'))
            <div class="alert flex rounded-lg bg-success px-4 py-4 text-white sm:px-5">{{ session('success') }}</div>
        @endif


        
@if($types && $types->isNotEmpty())
    <div class="space-y-4">

        @foreach($types as $type)
            <div class="border rounded bg-gray-50">
                {{-- Header Accordion --}}
                <button type="button"
                        class="w-full flex justify-between items-center p-4 font-semibold text-left bg-gray-200 hover:bg-gray-300 focus:outline-none focus:ring"
                        onclick="document.getElementById('type-{{ $type->id }}').classList.toggle('hidden')">
                    <span>{{ $type->titre }}</span>
                    <svg class="w-5 h-5 transition-transform duration-200"
                         xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>

                {{-- Contenu Accordion --}}
                <div id="type-{{ $type->id }}" class="hidden p-4 space-y-4">
                    @if($type->ressourcecomptes && $type->ressourcecomptes->isNotEmpty())
                        @foreach($type->ressourcecomptes as $compte)
                            <div class="border p-3 rounded bg-white">
                                <p><strong>Solde :</strong> {{ number_format($compte->solde, 2) }}</p>

                                <h3 class="mt-3 font-semibold">Transactions :</h3>
                                @if($compte->ressourcetransactions && $compte->ressourcetransactions->isNotEmpty())
                                    <ul class="list-disc ml-6 space-y-1">
                                        @foreach($compte->ressourcetransactions as $tx)
                                            <li>
                                                <span class="font-semibold">{{ number_format($tx->montant, 2) }}</span> 
                                                - {{ $tx->reference }} 
                                                ({{ $tx->operationtype->titre ?? 'N/A' }}) 
                                                <br>
                                                <small class="text-gray-500">
                                                    {{ optional($tx->created_at)->format('d/m/Y H:i') }} 
                                                    | 
                                                    @if($tx->formationRessource)
                                                        Formation - {{ $tx->formationRessource->formation->titre ?? 'N/A' }}
                                                    @elseif($tx->prestationRessource)
                                                        Prestation - {{ $tx->prestationRessource->prestation->titre ?? 'N/A' }}
                                                    @elseif($tx->evenementRessource)
                                                        Événement - {{ $tx->evenementRessource->evenement->titre ?? 'N/A' }}
                                                    @elseif($tx->espaceRessource)
                                                        Espace - {{ $tx->espaceRessource->espace->titre ?? 'N/A' }}
                                                    @else
                                                        {{ $tx->ressourcecompte->membre ? $tx->ressourcecompte->membre->prenom.' '.$tx->ressourcecompte->membre->nom : 'Entreprise' }}
                                                    @endif
                                                </small>
                                            </li>
                                        @endforeach
                                    </ul>
                                @else
                                    <p>Aucune transaction</p>
                                @endif
                            </div>
                        @endforeach
                    @else
                        <p>Aucune ressource pour ce type</p>
                    @endif
                </div>
            </div>
        @endforeach

    </div>
@else
    <p>Aucun type de ressource trouvé.</p>
@endif

          </div> 

            <div class="col-span-12 py-6 lg:sticky lg:bottom-0 lg:col-span-4 lg:self-end">
                @include('layouts.sidebar')
            </div>    

        </div>
      </main>
</x-app-layout>
