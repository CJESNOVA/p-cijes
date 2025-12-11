<x-app-layout title="Gestion des Quiz" is-sidebar-open="true" is-header-blur="true">
<main class="main-content w-full px-[var(--margin-x)] pb-8">
        <div class="flex items-center space-x-4 py-5 lg:py-6">
          <h2
            class="text-xl font-medium text-slate-800 dark:text-navy-50 lg:text-2xl"
          >
            Gestion des Quiz
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
            <li>Gestion des Quiz</li>
          </ul> -->
            <a href="{{ route('quiz.create') }}" class="btn bg-primary text-white hover:bg-primary-focus">+ Nouveau Quiz</a>
        </div>
        <div class="grid grid-cols-12 lg:gap-6">
            <div class="col-span-12 pt-6 lg:col-span-8 lg:pb-6">
              


        @if(session('success'))
            <div class="alert flex rounded-lg bg-success px-4 py-4 text-white sm:px-5">
                {{ session('success') }}
            </div>
        @endif

@if($quizs)
              <div class="grid grid-cols-1 gap-4 sm:grid-cols-1 sm:gap-5 lg:grid-cols-1 lg:gap-6 xl:grid-cols-1">

        <div class="grid grid-cols-1 md:grid-cols-1 lg:grid-cols-1 gap-6">
    @forelse($quizs as $quiz)
        <div class="card border rounded-lg shadow-sm bg-white dark:bg-navy-700 p-4">
            <h3 class="text-lg font-bold text-slate-800 dark:text-navy-50 mb-2">
                {{ $quiz->titre }}
            </h3>
            <p class="text-sm text-slate-600 dark:text-navy-200">
                <strong>Seuil :</strong> {{ $quiz->seuil_reussite }}%
            </p>
            <p class="text-sm text-slate-600 dark:text-navy-200 mb-4">
                <strong>Formation :</strong> {{ $quiz->formation->titre ?? '-' }}
            </p>
            <p class="text-sm text-slate-600 dark:text-navy-200">
                <strong>{{ $quiz->quizquestions_count }} questions</strong>
            </p>

            <div class="flex justify-between items-center mt-4">
                <a href="{{ route('quiz.edit', $quiz) }}" 
                   class="text-blue-600 hover:underline font-medium">
                   ✏️ Modifier
                </a>

                <form action="{{ route('quiz.destroy', $quiz) }}" method="POST" class="inline">
                    @csrf @method('DELETE')
                    <button type="submit" 
                            onclick="return confirm('Supprimer ce quiz ?')" 
                            class="text-red-600 hover:underline font-medium">
                        🗑️ Supprimer
                    </button>
                </form>

                <a href="{{ route('quizquestion.index', $quiz->id) }}" class="text-green-600">Questions</a>
                    
            </div>
        </div>
    @empty
        <div class="col-span-full text-center text-slate-500 dark:text-navy-200">
            Aucun quiz disponible.
        </div>
    @endforelse
</div>

          </div>
@endif
            

          </div> 

            <div class="col-span-12 py-6 lg:sticky lg:bottom-0 lg:col-span-4 lg:self-end">
                @include('layouts.sidebar')
            </div>    

        </div>
      </main>
</x-app-layout>
