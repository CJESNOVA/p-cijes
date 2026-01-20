
<x-app-layout title="Mes Accompagnements" is-sidebar-open="true" is-header-blur="true">
    <main class="main-content w-full px-[var(--margin-x)] pb-8">
        <div class="flex items-center space-x-4 py-5 lg:py-6">
          <h2
            class="text-xl font-medium text-slate-800 dark:text-navy-50 lg:text-2xl"
          >
            Mes Accompagnements
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
            <li>Mes Accompagnements</li>
          </ul> -->
        </div>
        <div class="grid grid-cols-12 lg:gap-6">
            <div class="col-span-12 pt-6 lg:col-span-8 lg:pb-6">
              @if($accompagnements)
                  @if($accompagnements->isEmpty())
                      <p>Aucun accompagnement trouvé.</p>
                  @else
                      <div class="grid grid-cols-2 gap-4 sm:grid-cols-2 sm:gap-5 lg:grid-cols-2 lg:gap-6 xl:grid-cols-2">
                          @foreach($accompagnements as $accompagnement)
                              <div class="bg-white dark:bg-navy-700 shadow rounded-lg p-4 flex flex-col justify-between">
                                  <div class="space-y-2">
                                      <h3 class="text-lg font-semibold text-slate-800 dark:text-navy-50">
                                          {{ $accompagnement->entreprise->nom ?? '—' }}
                                      </h3>
                                      <p class="text-sm text-slate-600 dark:text-navy-200">
                                          <strong>Membre :</strong> {{ $accompagnement->membre->nom ?? '' }} {{ $accompagnement->membre->prenom ?? '' }}
                                      </p>
                                      <p class="text-sm text-slate-600 dark:text-navy-200">
                                          <strong>Niveau :</strong> {{ $accompagnement->accompagnementniveau->titre ?? '' }}
                                      </p>
                                      <p class="text-sm text-slate-600 dark:text-navy-200">
                                          <strong>Statut :</strong> {{ $accompagnement->accompagnementstatut->titre ?? '' }}
                                      </p>
                                      <p class="text-sm text-slate-600 dark:text-navy-200">
                                          <strong>Date :</strong> {{ \Carbon\Carbon::parse($accompagnement->dateaccompagnement)->format('d/m/Y') }}
                                      </p>
                                  </div>
                                  <div class="mt-4 space-y-2">
                                      @if($accompagnement->accompagnementstatut_id == 1)
                                        @php
                                          // Récupérer le diagnostic PME
                                          $diagnosticPME = $accompagnement->diagnostics->where('membre_id', $accompagnement->membre_id)->where('entreprise_id', 0)->first();
                                        @endphp
                                        <!-- Bouton pour voir les plans PME -->
                                        @if($diagnosticPME && $diagnosticPME->diagnosticstatut_id == 2)
                                          <a href="{{ route('diagnostic.plans', $diagnosticPME->id) }}" 
                                           class="btn w-full bg-blue-500 text-white flex items-center justify-center gap-2">
                                              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 012-2v10a2 2 0 012 2h2a2 2 0 012-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 012-2z"/>
                                                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l6-6m-5.5.5.5a1 1 0 11.414 1.414L9 14z"/>
                                              </svg>
                                              Voir les plans PME
                                          </a>
                                        @endif
                                        
                                        <!-- Bouton pour voir les plans Entreprise -->
                                        @if($accompagnement->entreprise)
                                          @php
                                            // Récupérer le diagnostic Entreprise
                                            $diagnosticEntreprise = $accompagnement->diagnostics->where('entreprise_id', $accompagnement->entreprise_id)->first();
                                          @endphp
                                          @if($diagnosticEntreprise && $diagnosticEntreprise->diagnosticstatut_id == 2)
                                            <a href="{{ route('diagnosticentreprise.plans', $diagnosticEntreprise->id) }}" 
                                               class="btn w-full bg-purple-500 text-white flex items-center justify-center gap-2">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 012-2v10a2 2 0 012 2h2a2 2 0 012-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 012-2z"/>
                                                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l6-6m-5.5.5.5a1 1 0 11.414 1.414L9 14z"/>
                                                </svg>
                                                Voir les plans Entreprise
                                            </a>
                                          @endif
                                        @endif
                                      @else
                                        <span class="text-slate-400 text-sm text-center">
                                          Les plans ne sont pas encore disponibles
                                        </span>
                                      @endif
                                  </div>
                              </div>
                          @endforeach
                      </div>
                  @endif
              @endif

          </div> 

            <div class="col-span-12 py-6 lg:sticky lg:bottom-0 lg:col-span-4 lg:self-end">
                @include('layouts.sidebar')
            </div>    

        </div>
      </main>
</x-app-layout>