<x-app-layout title="{{ $quiz->id ? 'Modifier le Quiz' : 'Créer un Quiz' }}" is-sidebar-open="true" is-header-blur="true">
    <main class="main-content w-full px-[var(--margin-x)] pb-8">
        <!-- Header moderne -->
        <div class="mb-10">
            <div class="flex items-center gap-4 mb-8">
                <div class="h-14 w-14 rounded-xl bg-gradient-to-br from-[#4FBE96] to-[#4FBE96] flex items-center justify-center shadow-lg">
                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
                <div>
                    <h1 class="text-3xl font-bold text-slate-800 dark:text-navy-50">{{ $quiz->id ? 'Modifier le Quiz' : 'Créer un Quiz' }}</h1>
                    <p class="mt-2 text-slate-600 dark:text-navy-200 text-lg">{{ $quiz->id ? 'Mettez à jour les informations du quiz' : 'Créez un nouveau quiz pour évaluer les connaissances' }}</p>
                </div>
            </div>
        </div>
        <div class="grid grid-cols-12 lg:gap-6">
            <div class="col-span-12 pt-6 lg:col-span-8 lg:pb-6">

            <form action="{{ $quiz->id ? route('quiz.update', $quiz) : route('quiz.store') }}" method="POST">
                @csrf
                @if($quiz->id) @method('PUT') @endif

    <!-- Input Validation -->
    <div class="card px-4 pb-4 sm:px-5">
        <div class="max-w-xxl">
            <div
                x-data="pages.formValidation.initFormValidationExample"
                class="mt-5 grid grid-cols-1 gap-4 sm:grid-cols-2 sm:gap-5 lg:gap-6"
            >
                <div class="mb-4">
                    <label class="block mb-1">Titre</label>
                    <input type="text" name="titre" value="{{ old('titre', $quiz->titre) }}"
                           class="form-input w-full rounded-lg border border-slate-300 bg-transparent px-3 py-2
                                  placeholder:text-slate-400/70 hover:border-slate-400 focus:border-primary
                                  dark:border-navy-450 dark:hover:border-navy-400 dark:focus:border-accent" required>
                </div>

                <div class="mb-4">
                    <label class="block mb-1">Seuil de réussite (%)</label>
                    <input type="number" name="seuil_reussite" min="0" max="100"
                           value="{{ old('seuil_reussite', $quiz->seuil_reussite) }}"
                           class="form-input w-full rounded-lg border border-slate-300 bg-transparent px-3 py-2
                                  placeholder:text-slate-400/70 hover:border-slate-400 focus:border-primary
                                  dark:border-navy-450 dark:hover:border-navy-400 dark:focus:border-accent" required>
                </div>

                <div class="mb-4">
                    <label class="block mb-1">Formation liée</label>
                    <select name="formation_id" class="form-select mt-1.5 w-full rounded-lg border border-slate-300 bg-white px-3 py-2
                                   hover:border-slate-400 focus:border-primary dark:border-navy-450 dark:bg-navy-700
                                   dark:hover:border-navy-400 dark:focus:border-accent">
                        <option value="">-- Aucune --</option>
                        @foreach($formations as $formation)
                            <option value="{{ $formation->id }}" {{ old('formation_id', $quiz->formation_id) == $formation->id ? 'selected' : '' }}>
                                {{ $formation->titre }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                </div>

                <div>
                    <button type="submit" class="btn bg-primary text-white">
                        {{ $quiz->id ? 'Modifier' : 'Créer' }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</form>


          </div> 

            <div class="col-span-12 py-6 lg:sticky lg:bottom-0 lg:col-span-4 lg:self-end">
                @include('layouts.sidebar')
            </div>    

        </div>
      </main>
</x-app-layout>