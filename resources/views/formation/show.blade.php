<x-app-layout title="🎓 {{ $formation->titre }}" is-sidebar-open="true" is-header-blur="true">
<main class="main-content w-full px-[var(--margin-x)] pb-8">
        <div class="flex items-center space-x-4 py-5 lg:py-6">
          <h2
            class="text-xl font-medium text-slate-800 dark:text-navy-50 lg:text-2xl"
          >
            🎓 {{ $formation->titre }}
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
            <li>🎓 {{ $formation->titre }}</li>
          </ul> -->
        </div>
        <div class="grid grid-cols-12 lg:gap-6">
            <div class="col-span-12 pt-6 lg:col-span-8 lg:pb-6">

<!-- Infos principales -->
    <div class="bg-white dark:bg-navy-700 shadow rounded-xl p-6 mb-6">
        <p><strong>Niveau :</strong> {{ $formation->formationniveau->titre ?? '-' }}</p>
        <p><strong>Type :</strong> {{ $formation->formationtype->titre ?? '-' }}</p>
        <p><strong>Expert :</strong> {{ $formation->expert->membre->nom ?? '---' }}</p>
        <p><strong>Date début :</strong> {{ \Carbon\Carbon::parse($formation->datedebut)->format('d/m/Y') }}</p>
        <p><strong>Date fin :</strong> {{ \Carbon\Carbon::parse($formation->datefin)->format('d/m/Y') }}</p>
        <p><strong>Prix :</strong> {{ $formation->prix ? number_format($formation->prix, 0, ',', ' ') . ' F CFA' : 'Gratuit' }}</p>
    </div>

    <!-- Description -->
    <div class="bg-white dark:bg-navy-700 shadow rounded-xl p-6 mb-6">
        <h2 class="text-xl font-semibold mb-2">📖 Description</h2>
        <p class="text-slate-600 dark:text-navy-100">{!! nl2br(e($formation->description)) !!}</p>
    </div>

    <!-- Participants -->
    <div class="bg-white dark:bg-navy-700 shadow rounded-xl p-6 mb-6">
        <h2 class="text-xl font-semibold mb-2">👥 Participants ({{ $formation->participants->count() }})</h2>
        @if($formation->participants->count())
            <ul class="list-disc pl-5 text-slate-700 dark:text-navy-100">
                @foreach($formation->participants as $participant)
                    <li>{{ $participant->membre->nom ?? '' }} {{ $participant->membre->prenom ?? '' }}</li>
                @endforeach
            </ul>
        @else
            <p class="text-gray-500">Aucun participant inscrit pour le moment.</p>
        @endif
    </div>

    <!-- Quiz liés -->
    <div class="bg-white dark:bg-navy-700 shadow rounded-xl p-6">
        <h2 class="text-xl font-semibold mb-2">📝 Quiz associés</h2>
        @if($formation->quizs->count())
            <ul class="space-y-3">
                @foreach($formation->quizs as $quiz)
                    <li class="flex justify-between items-center p-3 border rounded-lg">
                        <span>{{ $quiz->titre }} ({{ $quiz->quizquestions_count }} questions)</span>
                        <a href="{{ route('quiz.show', [$formation->id, $quiz->id]) }}"
                           class="px-3 py-1 bg-primary text-white rounded-lg hover:bg-primary/90">
                            🚀 Faire le quiz
                        </a>
                    </li>
                @endforeach
            </ul>
        @else
            <p class="text-gray-500">Aucun quiz associé à cette formation.</p>
        @endif
    </div>

    <!-- Bouton retour -->
    <div class="mt-6">
        <a href="{{ route('formation.liste') }}"
           class="px-4 py-2 bg-slate-600 text-white rounded-lg hover:bg-slate-500">
            ⬅️ Retour aux formations
        </a>
    </div>

            

          </div> 

            <div class="col-span-12 py-6 lg:sticky lg:bottom-0 lg:col-span-4 lg:self-end">
                @include('layouts.sidebar')
            </div>    

        </div>
      </main>
</x-app-layout>
