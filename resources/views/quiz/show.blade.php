<x-app-layout title="{{ $quiz->titre }}" is-sidebar-open="true" is-header-blur="true">
<main class="main-content w-full px-[var(--margin-x)] pb-8">
        <div class="flex items-center space-x-4 py-5 lg:py-6">
          <h2
            class="text-xl font-medium text-slate-800 dark:text-navy-50 lg:text-2xl"
          >
            Quiz : {{ $quiz->titre }}
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
            <li>{{ $quiz->titre }}</li>
          </ul> -->
        </div>
        <div class="grid grid-cols-12 lg:gap-6">
            <div class="col-span-12 pt-6 lg:col-span-8 lg:pb-6">


<form method="POST" action="{{ route('quiz.submit', [$formation->id, $quiz->id]) }}">
    @csrf

    
    @foreach($quiz->quizquestions as $question)
        <div class="mb-6 p-5 bg-white dark:bg-navy-700 rounded-xl shadow border border-slate-200 dark:border-navy-500">
            <h4 class="text-lg font-semibold text-slate-800 dark:text-navy-50 mb-3">
                Q{{ $loop->iteration }}. {{ $question->titre }}
            </h4>

            @if($question->quizquestiontype_id === 1)
                {{-- Choix unique --}}
                @foreach($question->quizreponses as $reponse)
                    <label class="flex items-center mb-2 cursor-pointer">
                        <input type="radio" name="question_{{ $question->id }}" value="{{ $reponse->id }}" class="mr-2 h-4 w-4 text-primary">
                        <span class="text-slate-700 dark:text-navy-100">{{ $reponse->text }}</span>
                    </label>
                @endforeach

            @elseif($question->quizquestiontype_id === 2)
                {{-- Choix multiple --}}
                @foreach($question->quizreponses as $reponse)
                    <label class="flex items-center mb-2 cursor-pointer">
                        <input type="checkbox" name="question_{{ $question->id }}[]" value="{{ $reponse->id }}" class="mr-2 h-4 w-4 text-primary">
                        <span class="text-slate-700 dark:text-navy-100">{{ $reponse->text }}</span>
                    </label>
                @endforeach

            @elseif($question->quizquestiontype_id === 3)
                {{-- Saisie texte --}}
                <input type="text" name="question_{{ $question->id }}" 
                       class="w-full rounded-lg border border-slate-300 dark:border-navy-450 bg-transparent px-3 py-2 text-slate-700 dark:text-navy-100 placeholder:text-slate-400/70 focus:border-primary focus:ring focus:ring-primary/20"
                       placeholder="Votre réponse...">
            @endif
        </div>
    @endforeach

    <div class="mt-6 text-right">
        <button type="submit" class="px-6 py-3 bg-primary text-white font-semibold rounded-lg shadow hover:bg-primary/90 transition">
            ✅ Valider mes réponses
        </button>
    </div>

</form>
            

          </div> 

            <div class="col-span-12 py-6 lg:sticky lg:bottom-0 lg:col-span-4 lg:self-end">
                @include('layouts.sidebar')
            </div>    

        </div>
      </main>
</x-app-layout>
