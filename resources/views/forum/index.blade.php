<x-app-layout title="Liste des forums" is-sidebar-open="true" is-header-blur="true">
    <main class="main-content w-full px-[var(--margin-x)] pb-8">
        <!-- Header moderne -->
        <div class="mb-2">
            <div class="flex items-center gap-4 mb-2">
                <div class="h-14 w-14 rounded-xl bg-gradient-to-br from-indigo-500 to-indigo-600 flex items-center justify-center shadow-lg">
                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z"></path>
                    </svg>
                </div>
                <div>
                    <h1 class="text-3xl font-bold text-slate-800 dark:text-navy-50">
                        Forums de discussion
                    </h1>
                    <p class="mt-2 text-slate-600 dark:text-navy-200 text-lg">
                        Participez aux conversations et échanges
                    </p>
                </div>
            </div>
        </div>
        <div class="grid grid-cols-12 lg:gap-6">
            <div class="col-span-12 pt-6 lg:col-span-8 lg:pb-6">

              
              @if($forums && $forums->count() > 0)
    <div class="grid grid-cols-1 gap-6 mt-4">
        @foreach($forums as $forum)
    <div class="bg-white">
            {{-- Carte forum --}}
            <a href="{{ route('sujet.index', $forum->id) }}" 
               class="group block bg-white dark:bg-navy-700 rounded-2xl overflow-hidden shadow hover:shadow-lg transition duration-300">
               
                {{-- Image de couverture si disponible --}}
                @if($forum->vignette)
                    <img src="{{ env('SUPABASE_BUCKET_URL') . '/' . $forum->vignette }}" 
                         alt="{{ $forum->titre }}" 
                         class="h-40 w-full object-cover group-hover:scale-105 transition duration-300">
                @endif

                {{-- Contenu de la carte --}}
                <div class="p-4">
                    <h2 class="font-semibold text-lg text-slate-800 dark:text-navy-100 truncate">
                        {{ $forum->titre }}
                    </h2>

                    <p class="text-sm text-slate-500 dark:text-navy-300 mt-1 line-clamp-2">
                        {{ $forum->resume ?? 'Aucun résumé disponible.' }}
                    </p>

                    @if($forum->dateforum)
                        <p class="text-xs text-slate-400 dark:text-navy-200 mt-2">
                            📅 {{ \Carbon\Carbon::parse($forum->dateforum)->translatedFormat('d M Y') }}
                        </p>
                    @endif
                </div>
            </a>

            {{-- Card des sujets du membre dans ce forum --}}
            <div class="card p-4 shadow rounded mt-2">
                @if($forum->mesSujets && $forum->mesSujets->count())
                    <a href="{{ route('sujet.index', $forum->id) }}" class="text-sm font-medium text-primary hover:underline">
                        {{ $forum->mesSujets->count() }} sujet(s) de discussion
                    </a>
                @else
                    <p class="text-sm text-gray-500">Vous n'avez pas encore de sujet dans ce forum.</p>
                @endif

                <div class="mt-3">
                    <a href="{{ route('sujet.create', $forum->id) }}" 
                       class="inline-block px-3 py-1 text-xs font-medium bg-primary text-white rounded hover:bg-primary-focus transition">
                        ➕ Ajouter un sujet
                    </a>
                </div>
            </div>
    </div>
        @endforeach
    </div>
@else
    <div class="p-4 bg-yellow-50 dark:bg-navy-700/40 rounded-lg text-slate-700 dark:text-navy-100 text-sm mt-4">
        Aucun forum disponible pour le moment.
    </div>
@endif

          </div> 

            <div class="col-span-12 py-6 lg:sticky lg:bottom-0 lg:col-span-4 lg:self-end">
                @include('layouts.sidebar')
            </div>    

        </div>
      </main>
</x-app-layout>

