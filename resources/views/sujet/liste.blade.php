<x-app-layout title="Sujets de discussion" is-sidebar-open="true" is-header-blur="true">
    <main class="main-content w-full px-[var(--margin-x)] pb-8">
        <!-- Header moderne -->
        <div class="mb-2">
            <div class="flex items-center gap-4 mb-2">
                <div class="h-14 w-14 rounded-xl bg-gradient-to-br from-cyan-500 to-cyan-600 flex items-center justify-center shadow-lg">
                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                    </svg>
                </div>
                <div>
                    <h1 class="text-3xl font-bold text-slate-800 dark:text-navy-50">
                        Sujets de discussion
                    </h1>
                    <p class="mt-2 text-slate-600 dark:text-navy-200 text-lg">
                        Explorez tous les sujets disponibles
                    </p>
                </div>
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
            <a href="{{ route('sujet.show', $sujet->id) }}" class="block group">
                <div class="bg-white dark:bg-navy-700 rounded-2xl shadow p-5 transition hover:shadow-lg">

                    {{-- Image de couverture si disponible --}}
                    @if($sujet->vignette)
                        <img src="{{ env('SUPABASE_BUCKET_URL') . '/' . $sujet->vignette }}" 
                             alt="{{ $sujet->titre }}" 
                             class="h-40 w-full object-cover group-hover:scale-105 transition duration-300">
                    @endif

                    <div class="flex items-start justify-between mt-3">
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
                        {{-- Nombre de messages --}}
                        <div class="text-xs text-slate-400 dark:text-navy-200 text-right">
                            {{ $sujet->messageforums->count() ?? 0 }} message{{ $sujet->messageforums->count() > 1 ? 's' : '' }}
                        </div>
                    </div>

                    <div class="mt-3 text-xs text-slate-400 dark:text-navy-200">
                        Publié par 
                        <span class="font-medium text-slate-600 dark:text-navy-100">
                            {{ $sujet->membre->prenom ?? 'Utilisateur' }} {{ $sujet->membre->nom ?? '' }}
                        </span>
                    </div>

                </div>
            </a>
        @endforeach
    </div>
@else
    <div class="p-4 bg-yellow-50 border border-yellow-200 rounded-xl text-slate-600 dark:bg-navy-700/40 dark:text-navy-100">
        Aucun sujet de discussion pour le moment.
    </div>
@endif

          </div> 

            <div class="col-span-12 py-6 lg:sticky lg:bottom-0 lg:col-span-4 lg:self-end">
                @include('layouts.sidebar')
            </div>    

        </div>
      </main>
</x-app-layout>

