<x-app-layout title="Parrainages en attente" is-sidebar-open="true" is-header-blur="true">
    <main class="main-content w-full px-[var(--margin-x)] pb-8">
        <div class="flex items-center space-x-4 py-5 lg:py-6">
          <h2
            class="text-xl font-medium text-slate-800 dark:text-navy-50 lg:text-2xl"
          >
            Parrainages en attente
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
            <li>Parrainages en attente</li>
          </ul> -->
    <a href="{{ route('parrainage.create') }}"
       class="btn bg-primary text-white hover:bg-primary-focus 
              font-medium px-4 py-2 rounded">
        ➕ Ajouter un parrain
    </a>
        </div>
        <div class="grid grid-cols-12 lg:gap-6">
            <div class="col-span-12 pt-6 lg:col-span-8 lg:pb-6">


        @if(session('success'))
            <div class="alert flex rounded-lg bg-success px-4 py-4 text-white sm:px-5">{{ session('success') }}</div>
        @endif

        @if(session('error'))
            <div class="alert flex rounded-lg bg-error px-4 py-4 text-white sm:px-5">{{ session('error') }}</div>
        @endif


              <div class="grid grid-cols-1 gap-4 sm:grid-cols-1 lg:grid-cols-1">
    @if(!$parrainages->isEmpty() || !$parrainages2->isEmpty())
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-2 gap-6">

            {{-- Liste des filleuls (je suis le parrain) --}}
            @foreach($parrainages as $p)
                <div class="card border rounded-xl shadow p-5 bg-white dark:bg-navy-700 flex flex-col justify-between transition hover:shadow-lg">
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="font-semibold text-slate-800 dark:text-navy-100">
                            👨‍🎓 Filleul
                        </h3>
                        {{-- Badge d’état --}}
                        @if($p->etat == 1)
                            <span class="flex items-center gap-1 text-green-600 text-sm font-medium">
                                <i class="fas fa-check-circle"></i> Activé
                            </span>
                        @else
                            <span class="flex items-center gap-1 text-red-500 text-sm font-medium">
                                <i class="fas fa-times-circle"></i> Inactif
                            </span>
                        @endif
                    </div>

                    <div class="mb-4">
                        <p class="text-slate-700 dark:text-navy-100">
                            <span class="font-medium">Nom :</span>
                            {{ $p->membrefilleul->prenom ?? 'N/A' }} {{ $p->membrefilleul->nom ?? 'N/A' }}
                        </p>
                        <p class="text-xs text-slate-500 dark:text-navy-300">
                            📅 {{ $p->created_at->format('d/m/Y') }}
                        </p>
                    </div>

                    @if($p->etat == 0)
                        <form action="{{ route('parrainage.activer', $p->id) }}" method="POST">
                            @csrf
                            <button type="submit"
                                class="w-full btn bg-green-500 text-white hover:bg-green-600 rounded-lg py-2 mt-2 transition">
                                <i class="fas fa-toggle-on mr-1"></i> Activer
                            </button>
                        </form>
                    @endif
                </div>
            @endforeach

            {{-- Liste des parrains (je suis le filleul) --}}
            @foreach($parrainages2 as $p)
                <div class="card border rounded-xl shadow p-5 bg-white dark:bg-navy-700 flex flex-col justify-between transition hover:shadow-lg">
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="font-semibold text-slate-800 dark:text-navy-100">
                            🧑‍🏫 Parrain
                        </h3>
                        @if($p->etat == 1)
                            <span class="flex items-center gap-1 text-green-600 text-sm font-medium">
                                <i class="fas fa-check-circle"></i> Activé
                            </span>
                        @else
                            <span class="flex items-center gap-1 text-red-500 text-sm font-medium">
                                <i class="fas fa-times-circle"></i> Inactif
                            </span>
                        @endif
                    </div>

                    <div class="mb-4">
                        <p class="text-slate-700 dark:text-navy-100">
                            <span class="font-medium">Nom :</span>
                            {{ $p->membreparrain->prenom ?? 'N/A' }} {{ $p->membreparrain->nom ?? 'N/A' }}
                        </p>
                        <p class="text-xs text-slate-500 dark:text-navy-300">
                            📅 {{ $p->created_at->format('d/m/Y') }}
                        </p>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="p-4 bg-yellow-50 border border-yellow-200 rounded-lg text-slate-600 dark:bg-navy-700/40 dark:text-navy-100">
            Aucun parrainage trouvé pour le moment.
        </div>
    @endif
</div>
 

          </div> 

            <div class="col-span-12 py-6 lg:sticky lg:bottom-0 lg:col-span-4 lg:self-end">
                @include('layouts.sidebar')
            </div>    

        </div>
      </main>
</x-app-layout>

