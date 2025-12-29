<x-app-layout title="Sujets du forum : {{ $forum->titre }}" is-sidebar-open="true" is-header-blur="true">
    <main class="main-content w-full px-[var(--margin-x)] pb-8">
        <div class="flex items-center space-x-4 py-5 lg:py-6">
          <h2
            class="text-xl font-medium text-slate-800 dark:text-navy-50 lg:text-2xl"
          >
            Sujets du forum 
          </h2>
          <div class="hidden h-full py-1 sm:flex">
            <div class="h-full w-px bg-slate-300 dark:bg-navy-600"></div>
          </div>
          <ul class="hidden flex-wrap items-center space-x-2 sm:flex">
            <li class="flex items-center space-x-2">
              <a
                class="text-primary transition-colors hover:text-primary-focus dark:text-accent-light dark:hover:text-accent"
                href="#"
                >{{ $forum->titre }}</a
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
            <!-- <li>Mes entreprises</li> -->
          </ul>

            <a href="{{ route('sujet.create', $forum->id) }}" class="btn bg-primary text-white hover:bg-primary-focus">Créer un sujet</a>

        </div>
        <div class="grid grid-cols-12 lg:gap-6">
            <div class="col-span-12 pt-6 lg:col-span-8 lg:pb-6">
              
        @if(session('success'))
            <div class="alert flex rounded-lg bg-success px-4 py-4 text-white sm:px-5">{{ session('success') }}</div>
        @endif
@if(isset($sujets) && $sujets->count())
    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-2 xl:grid-cols-2">
        @foreach($sujets as $sujet)
            <div class="bg-white dark:bg-navy-700 rounded-2xl shadow p-5 transition hover:shadow-lg">
                
                {{-- Image de couverture si disponible --}}
                @if($sujet->vignette)
                    <img src="{{ env('SUPABASE_BUCKET_URL') . '/' . $sujet->vignette }}" 
                         alt="{{ $sujet->titre }}" 
                         class="h-40 w-full object-cover group-hover:scale-105 transition duration-300">
                @endif

                <div class="flex items-start justify-between">
                    <div>
                        <h2 class="text-lg font-semibold text-slate-800 dark:text-navy-100">
                            {{ $sujet->titre }}
                        </h2>
                        @if(!empty($sujet->resume))
                            <p class="mt-1 text-sm text-slate-500 dark:text-navy-300 line-clamp-2">
                                {{ $sujet->resume }}
                            </p>
                        @endif
                    </div>
                </div>

                <div class="mt-3 text-xs text-slate-400 dark:text-navy-200">
                    Publié par 
                    <span class="font-medium text-slate-600 dark:text-navy-100">
                        {{ $sujet->membre->prenom ?? 'Utilisateur' }} {{ $sujet->membre->nom ?? '' }}
                    </span>
                </div>

                <div class="mt-4 flex items-center space-x-2">
                    <a href="{{ route('sujet.edit', $sujet->id) }}"
                       class="inline-flex items-center px-3 py-1 text-xs font-medium text-white bg-primary rounded-lg hover:bg-primary-focus transition">
                        ✏️ Modifier
                    </a>
                    <form action="{{ route('sujet.destroy', $sujet->id) }}" method="POST"
                          onsubmit="return confirm('Confirmer la suppression de ce sujet ?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                                class="inline-flex items-center px-3 py-1 text-xs font-medium text-slate-600 bg-slate-200 rounded-lg hover:bg-slate-300 dark:bg-navy-600 dark:text-navy-100 dark:hover:bg-navy-500 transition">
                            🗑️ Supprimer
                        </button>
                    </form>
                </div>
            </div>
        @endforeach
    </div>
@else
    <div class="p-4 bg-yellow-50 border border-yellow-200 rounded-xl text-slate-600 dark:bg-navy-700/40 dark:text-navy-100">
        Aucun sujet pour ce forum pour le moment.
    </div>
@endif


          </div> 

            <div class="col-span-12 py-6 lg:sticky lg:bottom-0 lg:col-span-4 lg:self-end">
                @include('layouts.sidebar')
            </div>    

        </div>
      </main>
</x-app-layout>

