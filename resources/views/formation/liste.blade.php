<x-app-layout title="Formations ouvertes" is-sidebar-open="true" is-header-blur="true">
    <main class="main-content w-full px-[var(--margin-x)] pb-8">
        <!-- Header moderne -->
        <div class="mb-2">
            <div class="flex items-center gap-4 mb-2">
                <div class="h-14 w-14 rounded-xl bg-gradient-to-br from-teal-500 to-teal-600 flex items-center justify-center shadow-lg">
                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                    </svg>
                </div>
                <div>
                    <h1 class="text-3xl font-bold text-slate-800 dark:text-navy-50">
                        Formations ouvertes
                    </h1>
                    <p class="mt-2 text-slate-600 dark:text-navy-200 text-lg">
                        Découvrez nos programmes de formation disponibles
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

                @if(session('error'))
                    <div class="alert flex rounded-lg bg-red-500 px-6 py-4 text-white mb-6 shadow-lg">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        {{ session('error') }}
                    </div>
                @endif

                @if($formations && $formations->count())
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-6">
                        @foreach($formations as $formation)
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
                                       class="btn bg-warning text-white w-full mt-2">
                                        📚 Voir les quiz de cette formation
                                    </a>
                                @endif
                                <br />
                                
                                @if($dejaInscrit)
                                    <p class="mt-4 text-green-600 font-semibold">✅ Vous êtes déjà inscrit</p>
                                @else
                                    <a href="{{ route('formation.inscrire.form', $formation->id) }}" 
                                      class="btn bg-primary text-white w-full mt-2">
                                      S'inscrire
                                    </a>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8">
                        <div class="h-20 w-20 rounded-xl bg-gradient-to-br from-teal-100 to-teal-200 flex items-center justify-center mx-auto mb-6">
                            <svg class="w-10 h-10 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-slate-800 dark:text-navy-50 mb-2">
                            Aucune formation disponible
                        </h3>
                        <p class="text-slate-600 dark:text-navy-200">
                            Revenez plus tard pour découvrir nos nouvelles formations.
                        </p>
                    </div>
                @endif

            </div>
            <div class="col-span-12 py-6 lg:sticky lg:bottom-0 lg:col-span-4 lg:self-end">
                @include('layouts.sidebar')
            </div>    

        </div>
      </main>
</x-app-layout>