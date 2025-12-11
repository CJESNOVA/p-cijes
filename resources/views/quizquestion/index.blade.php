<x-app-layout title="Questions du quiz : {{ $quiz->titre }}" is-sidebar-open="true" is-header-blur="true">
<main class="main-content w-full px-[var(--margin-x)] pb-8">
        <div class="flex items-center space-x-4 py-5 lg:py-6">
          <h2
            class="text-xl font-medium text-slate-800 dark:text-navy-50 lg:text-2xl"
          >
            Questions du quiz : {{ $quiz->titre }}
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
            <li>Questions du quiz : {{ $quiz->titre }}</li>
          </ul> -->
            <a href="{{ route('quizquestion.create', $quiz) }}" class="btn bg-primary text-white">+ Nouvelle question</a>
        </div>
        <div class="grid grid-cols-12 lg:gap-6">
            <div class="col-span-12 pt-6 lg:col-span-8 lg:pb-6">
              

@if($questions)
              <div class="grid grid-cols-1 gap-4 sm:grid-cols-1 sm:gap-5 lg:grid-cols-1 lg:gap-6 xl:grid-cols-1">

        <div class="grid grid-cols-1 md:grid-cols-1 lg:grid-cols-1 gap-6">
        @forelse($questions as $question)
            <div class="card p-4 border rounded-lg shadow-sm">
                <h3 class="font-bold text-lg">{{ $question->titre }}</h3>
                <p class="text-slate-600">Points : {{ $question->point }}</p>
                <p class="text-slate-600">Type : {{ $question->quizquestiontype->titre ?? '-' }}</p>
                <p class="text-slate-600"><strong>{{ $question->quizreponses_count }} réponses</strong></p>

                <div class="mt-3 flex space-x-4">
                    <a href="{{ route('quizreponse.index', [$quiz, $question]) }}" class="text-indigo-600">⚡ Gérer réponses</a>
                    <a href="{{ route('quizquestion.edit', [$quiz, $question]) }}" class="text-blue-600">✏️ Modifier</a>
                    <form action="{{ route('quizquestion.destroy', [$quiz, $question]) }}" method="POST" class="inline">
                        @csrf @method('DELETE')
                        <button type="submit" class="text-red-600" onclick="return confirm('Supprimer cette question ?')">🗑️ Supprimer</button>
                    </form>
                </div>
            </div>
        @empty
            <p>Aucune question pour ce quiz.</p>
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

