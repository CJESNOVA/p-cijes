<x-app-layout title="Sujets du forum : {{ $forum->titre }}" is-sidebar-open="true" is-header-blur="true">
    <main class="main-content w-full px-[var(--margin-x)] pb-8">
        <!-- Header moderne -->
        <div class="mb-2">
            <div class="flex items-center justify-between mb-2">
                <div class="flex items-center gap-4">
                    <div class="h-14 w-14 rounded-xl bg-gradient-to-br from-[#4FBE96] to-[#4FBE96] flex items-center justify-center shadow-lg">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z"></path>
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-3xl font-bold text-slate-800 dark:text-navy-50">
                            Sujets du forum
                        </h1>
                        <p class="mt-2 text-slate-600 dark:text-navy-200 text-lg">
                            {{ $forum->titre }} - Participez aux discussions
                        </p>
                    </div>
                </div>
                <a href="{{ route('sujet.create', $forum->id) }}" 
                   class="px-6 py-3 bg-[#4FBE96] text-white rounded-lg hover:bg-[#4FBE96]/90 transition-colors flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Créer un sujet
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

