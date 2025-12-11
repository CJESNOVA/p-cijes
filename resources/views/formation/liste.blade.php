<x-app-layout title="Formations ouvertes" is-sidebar-open="true" is-header-blur="true">
<main class="main-content w-full px-[var(--margin-x)] pb-8">
        <div class="flex items-center space-x-4 py-5 lg:py-6">
          <h2
            class="text-xl font-medium text-slate-800 dark:text-navy-50 lg:text-2xl"
          >
            Formations ouvertes
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
            <li>Formations ouvertes</li>
          </ul> -->
        </div>
        <div class="grid grid-cols-12 lg:gap-6">
            <div class="col-span-12 pt-6 lg:col-span-8 lg:pb-6">


    @if(session('success'))
        <div class="alert flex rounded-lg bg-success px-4 py-4 text-white sm:px-5">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="alert flex rounded-lg bg-error px-4 py-4 text-white sm:px-5">{{ session('error') }}</div>
    @endif

@if($formations)
    <div class="grid grid-cols-2 gap-4 sm:grid-cols-2 sm:gap-5 lg:grid-cols-2 lg:gap-6 xl:grid-cols-2">
        @foreach($formations as $formation)
            @php
                $dejaInscrit = $membreId && $formation->participants->contains('membre_id', $membreId);
            @endphp

            <div class="card p-4">
                <h3 class="font-bold">{{ $formation->titre }}</h3>
                <p class="text-sm">Prix: {{ $formation->prix ?? '' }}</p>
                <p class="text-sm">{{ $formation->formationniveau->titre ?? '' }}</p>
                <p class="text-sm">{{ $formation->formationtype->titre ?? '' }}</p>
                <p class="text-xs">
                    Du {{ $formation->datedebut }} au {{ $formation->datefin }}
                </p>
                <p class="mt-2 text-xs">
                    Animée par : {{ $formation->expert->membre->nom ?? 'N/A' }} {{ $formation->expert->membre->prenom ?? 'N/A' }}
                </p>
                
        <p class="text-sm text-slate-500 dark:text-navy-200">
            {{ Str::limit($formation->description, 100) }}
        </p>

        <div class="mt-3 flex justify-between items-center">
            <!-- Lien vers la page détail -->
            <a href="{{ route('formation.show', $formation->id) }}"
               class="inline-flex items-center px-3 py-2 bg-primary text-white rounded-lg shadow hover:bg-primary/90 transition">
                📖 Voir détails
            </a>

            <!-- Exemple de stats -->
            <span class="text-xs text-slate-500">
                {{ $formation->participants->count() }} participants
            </span>
        </div>

@if($formation->quizs()->whereHas('quizquestions.quizreponses')->count())
        <a href="{{ route('quiz', $formation->id) }}"
           class="btn bg-warning text-white w-full">
            📚 Voir les quiz de cette formation
        </a>
@endif
<br />
                @if($dejaInscrit)
                    <p class="mt-4 text-green-600 font-semibold">✅ Vous êtes déjà inscrit</p>
                @else
                <a href="{{ route('formation.inscrire.form', $formation->id) }}" 
                  class="btn bg-primary text-white w-full">
                  S'inscrire
                </a>
                @endif
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