<x-app-layout title="Mes entreprises" is-sidebar-open="true" is-header-blur="true">
    <main class="main-content w-full px-[var(--margin-x)] pb-8">
        <div class="flex items-center space-x-4 py-5 lg:py-6">
          <h2
            class="text-xl font-medium text-slate-800 dark:text-navy-50 lg:text-2xl"
          >
            Mes entreprises
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
            <li>Mes entreprises</li>
          </ul> -->

      <a href="{{ route('entreprise.create') }}"
         class="btn bg-primary text-white hover:bg-primary-focus">Ajouter une entreprise</a>

        </div>
        <div class="grid grid-cols-12 lg:gap-6">
            <div class="col-span-12 pt-6 lg:col-span-8 lg:pb-6">
              

    @if(session('success'))
      <div class="alert flex rounded-lg bg-success px-4 py-4 text-white sm:px-5">{{ session('success') }}</div>
    @endif

    @if(session('error'))
      <div class="alert flex rounded-lg bg-danger px-4 py-4 text-white sm:px-5">{{ session('error') }}</div>
    @endif

    @if(session('info'))
      <div class="alert flex rounded-lg bg-info px-4 py-4 text-white sm:px-5">{{ session('info') }}</div>
    @endif

@if($entreprises)
              <div class="grid grid-cols-2 gap-4 sm:grid-cols-2 sm:gap-5 lg:grid-cols-2 lg:gap-6 xl:grid-cols-2">

      @foreach($entreprises as $em)
            <div class="card grow items-center p-4 sm:p-5">
                <div class="avatar size-20">
                  @if($em->entreprise && $em->entreprise->vignette)
                    <img class="rounded-full " src="{{ env('SUPABASE_BUCKET_URL') . '/' . $em->entreprise->vignette }}" alt="avatar" />
                    @endif
                  @if($em->entreprise && $em->entreprise->spotlight)
                    <div
                        class="absolute right-0 m-1 size-4 rounded-full border-2 border-white bg-primary dark:border-navy-700 dark:bg-accent">
                    </div>
                    @endif
                </div>
                <h3 class="pt-3 text-lg font-medium text-slate-700 dark:text-navy-100">
                    {{ $em->entreprise->nom }}<br />
                    @if($em->entreprise && $em->entreprise->entrepriseprofil && $em->entreprise->est_membre_cijes)
                        <span class="ml-2 inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                            @switch($em->entreprise->entrepriseprofil->id)
                                @case(1)
                                    bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200
                                @break
                                @case(2)
                                    bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200
                                @break
                                @case(3)
                                    bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                                @break
                                @default
                                    bg-slate-100 text-slate-800 dark:bg-slate-900 dark:text-slate-200
                            @endswitch
                        ">
                            @switch($em->entreprise->entrepriseprofil->id)
                                @case(1)
                                    <i class="fas fa-seedling mr-1"></i>{{ $em->entreprise->entrepriseprofil->titre ?? 'CJES' }}
                                @break
                                @case(2)
                                    <i class="fas fa-chart-line mr-1"></i>{{ $em->entreprise->entrepriseprofil->titre ?? 'CJES' }}
                                @break
                                @case(3)
                                    <i class="fas fa-trophy mr-1"></i>{{ $em->entreprise->entrepriseprofil->titre ?? 'CJES' }}
                                @break
                            @endswitch
                        </span>
                    @endif
                    <div class="flex flex-wrap gap-2 mt-2">
                        @php
                            // Vérifier si un test est en cours pour cette entreprise
                            $testEnCours = \App\Models\Diagnostic::where('entreprise_id', $em->entreprise->id)
                                ->where('membre_id', $em->membre_id ?? auth()->id())
                                ->where('diagnostictype_id', 3)
                                ->where('diagnosticstatut_id', 1) // En cours
                                ->first();
                            
                            // Vérifier si un test est terminé pour cette entreprise
                            $testTermine = \App\Models\Diagnostic::where('entreprise_id', $em->entreprise->id)
                                ->where('membre_id', $em->membre_id ?? auth()->id())
                                ->where('diagnostictype_id', 3)
                                ->where('diagnosticstatut_id', 2) // Terminé
                                ->first();
                        @endphp
                        
                        @if($testEnCours)
                            <a href="{{ route('diagnosticentreprisequalification.showForm', $em->entreprise->id) }}" 
                               class="btn bg-warning text-white hover:bg-warning-focus text-sm px-3 py-1 rounded-full">
                                <i class="fas fa-clipboard-check mr-1"></i>
                                Continuer le test
                            </a>
                        @elseif($testTermine)
                            <a href="{{ route('diagnosticentreprisequalification.results', $em->entreprise->id) }}" 
                               class="btn bg-info text-white hover:bg-info-focus text-sm px-3 py-1 rounded-full">
                                <i class="fas fa-chart-bar mr-1"></i>
                                Résultats du test
                            </a>
                        @else
                            <a href="{{ route('diagnosticentreprisequalification.showForm', $em->entreprise->id) }}" 
                               class="btn bg-warning text-white hover:bg-warning-focus text-sm px-3 py-1 rounded-full">
                                <i class="fas fa-clipboard-check mr-1"></i>
                                Test de qualification
                            </a>
                        @endif
                    </div>
                </h3>
                <p class="text-xs-plus">{!! $em->entreprise->description !!}</p>
                <div class="my-4 h-px w-full bg-slate-200 dark:bg-navy-500"></div>
                <div class="grow space-y-4">
                    <div class="flex items-center space-x-4">
                        <div
                            class="flex size-7 items-center rounded-lg bg-primary/10 p-2 text-primary dark:bg-accent-light/10 dark:text-accent-light">
                            <i class="fa fa-user text-xs"></i>
                        </div>
                        <p>{{ $em->fonction }}</p>
                    </div>
                    <div class="flex items-center space-x-4">
                        <p>{{ $em->bio }}</p>
                    </div>
                </div>
                <div class="my-4 h-px w-full bg-slate-200 dark:bg-navy-500"></div>
                <div class="grow space-y-4">
                    <div class="flex items-center space-x-4">
                        <div
                            class="flex size-7 items-center rounded-lg bg-primary/10 p-2 text-primary dark:bg-accent-light/10 dark:text-accent-light">
                            <i class="fa fa-phone text-xs"></i>
                        </div>
                        <p>{{ $em->entreprise->telephone }}</p>
                    </div>
                    <div class="flex items-center space-x-4">
                        <div
                            class="flex size-7 items-center rounded-lg bg-primary/10 p-2 text-primary dark:bg-accent-light/10 dark:text-accent-light">
                            <i class="fa fa-envelope text-xs"></i>
                        </div>
                        <p>{{ $em->entreprise->email }}</p>
                    </div>
                    <div class="flex items-center space-x-4">
                        <div
                            class="flex size-7 items-center rounded-lg bg-primary/10 p-2 text-primary dark:bg-accent-light/10 dark:text-accent-light">
                            <i class="fa fa-location text-xs"></i>
                        </div>
                        <p>{{ $em->entreprise->adresse }}</p>
                    </div>
                    @if($em->entreprise->entrepriseprofil)
                    <div class="flex items-center space-x-4">
                        <div
                            class="flex size-7 items-center rounded-lg bg-info/10 p-2 text-info dark:bg-info-light/10 dark:text-info-light">
                            <i class="fa fa-briefcase text-xs"></i>
                        </div>
                        <p>{{ $em->entreprise->entrepriseprofil->titre }}</p>
                    </div>
                    @endif
                    @if($em->entreprise->est_membre_cijes)
                    <div class="flex items-center space-x-4">
                        <div
                            class="flex size-7 items-center rounded-lg bg-success/10 p-2 text-success dark:bg-success-light/10 dark:text-success-light">
                            <i class="fa fa-star text-xs"></i>
                        </div>
                        <p class="text-success font-medium">Membre CJES</p>
                    </div>
                    @endif
                    @if($em->entreprise->annee_creation)
                    <div class="flex items-center space-x-4">
                        <div
                            class="flex size-7 items-center rounded-lg bg-slate-100 p-2 text-slate-600 dark:bg-navy-100 dark:text-navy-300">
                            <i class="fa fa-calendar text-xs"></i>
                        </div>
                        <p>Créée en {{ $em->entreprise->annee_creation }}</p>
                    </div>
                    @endif
                </div>
                
                <a href="{{ route('entreprise.edit', $em->entreprise->id) }}"
               class="btn mt-5 space-x-2 rounded-full bg-primary font-medium text-white hover:bg-primary-focus focus:bg-primary-focus active:bg-primary-focus/90 dark:bg-accent dark:hover:bg-accent-focus dark:focus:bg-accent-focus dark:active:bg-accent/90">Modifier</a>
                    <form action="{{ route('entreprise.destroy', $em->entreprise->id) }}" method="POST" style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn bg-red-500 text-white">Supprimer</button>
                    </form>
            </div>
      @endforeach
          </div>
@endif
            

          </div> 

            <div class="col-span-12 py-6 lg:sticky lg:bottom-0 lg:col-span-4 lg:self-end">
                @include('layouts.sidebar')
            </div>    

        </div>
      </main>
</x-app-layout>