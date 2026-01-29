<x-app-layout title="Gestion des Quiz" is-sidebar-open="true" is-header-blur="true">
    <main class="main-content w-full px-[var(--margin-x)] pb-8">
        <!-- Header moderne -->
        <div class="mb-2">
            <div class="flex items-center justify-between mb-2">
                <div class="flex items-center gap-4">
                    <div class="h-14 w-14 rounded-xl bg-gradient-to-br from-[#4FBE96] to-[#4FBE96] flex items-center justify-center shadow-lg">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-3xl font-bold text-slate-800 dark:text-navy-50">
                            Gestion des Quiz
                        </h1>
                        <p class="mt-2 text-slate-600 dark:text-navy-200 text-lg">
                            Créez et gérez vos quiz d'évaluation
                        </p>
                    </div>
                </div>
                <a href="{{ route('quiz.create') }}" 
                   class="px-6 py-3 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Nouveau Quiz
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
