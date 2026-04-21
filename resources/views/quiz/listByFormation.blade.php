<x-app-layout title="Quiz disponibles pour la formation : {{ $formation->titre }}" is-sidebar-open="true" is-header-blur="true">
<main class="main-content w-full px-[var(--margin-x)] pb-8">
        <div class="flex items-center space-x-4 py-5 lg:py-6">
          <h2
            class="text-xl font-medium text-slate-800 dark:text-navy-50 lg:text-2xl"
          >
            Quiz disponibles pour la formation : {{ $formation->titre }}
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
            <li>Quiz disponibles pour la formation : {{ $formation->titre }}</li>
          </ul> -->
        </div>
        <div class="grid grid-cols-12 lg:gap-6">
            <div class="col-span-12 pt-6 lg:col-span-8 lg:pb-6">


        @if(session('success'))
            <div class="alert flex rounded-lg bg-success px-4 py-4 text-white sm:px-5">
                {{ session('success') }}
            </div>
        @endif
        

    @if($quizs->count())
        <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-2">
            @foreach($quizs as $quiz)
                <div class="bg-white dark:bg-navy-700 shadow-lg rounded-2xl p-6 flex flex-col justify-between border border-slate-200 dark:border-navy-500">
                    <div>
                        <h3 class="text-lg font-semibold text-slate-800 dark:text-navy-50 mb-2">
                            {{ $quiz->titre }}
                        </h3>
                        <p class="text-sm text-slate-500 dark:text-navy-200 flex items-center">
                            📘 {{ $quiz->quizquestions_count }} questions
                        </p>
                    </div>

                    <div class="mt-4">
                        <a href="{{ route('quiz.show', [$formation->id, $quiz->id]) }}"
                           class="inline-flex items-center px-4 py-2 bg-primary text-white rounded-lg shadow hover:bg-primary/90 transition">
                            🚀 Faire le quiz
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <p class="text-gray-500">Aucun quiz disponible pour cette formation.</p>
    @endif
            

          </div> 

            <div class="col-span-12 py-6 lg:sticky lg:bottom-0 lg:col-span-4 lg:self-end">
                @include('layouts.sidebar')
            </div>    

        </div>
      </main>
</x-app-layout>
