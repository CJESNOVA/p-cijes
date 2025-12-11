<x-app-layout title="Nouvelle Question" is-sidebar-open="true" is-header-blur="true">
<main class="main-content w-full px-[var(--margin-x)] pb-8">
        <div class="flex items-center space-x-4 py-5 lg:py-6">
          <h2
            class="text-xl font-medium text-slate-800 dark:text-navy-50 lg:text-2xl"
          >
            Ajouter une nouvelle question au quiz : {{ $quiz->titre }}
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
            <li>Ajouter une nouvelle question au quiz : {{ $quiz->titre }}</li>
          </ul> -->
        </div>
        <div class="grid grid-cols-12 lg:gap-6">
            <div class="col-span-12 pt-6 lg:col-span-8 lg:pb-6">

            <form action="{{ route('quizquestion.store', $quiz) }}" method="POST">
                @csrf

    <!-- Input Validation -->
    <div class="card px-4 pb-4 sm:px-5">
        <div class="max-w-xxl">
            <div
                x-data="pages.formValidation.initFormValidationExample"
                class="mt-5 grid grid-cols-1 gap-4 sm:grid-cols-2 sm:gap-5 lg:gap-6"
            >
                <div class="mb-4">
                    <label class="block text-sm font-medium text-slate-700">Titre de la question</label>
                    <input type="text" name="titre" class="form-input w-full rounded-lg border border-slate-300 bg-transparent px-3 py-2
                                  placeholder:text-slate-400/70 hover:border-slate-400 focus:border-primary
                                  dark:border-navy-450 dark:hover:border-navy-400 dark:focus:border-accent" value="{{ old('titre') }}" required>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-slate-700">Nombre de points</label>
                    <input type="number" name="point" class="form-input w-full rounded-lg border border-slate-300 bg-transparent px-3 py-2
                                  placeholder:text-slate-400/70 hover:border-slate-400 focus:border-primary
                                  dark:border-navy-450 dark:hover:border-navy-400 dark:focus:border-accent" value="{{ old('point') }}" required>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-slate-700">Type de question</label>
                    <select name="quizquestiontype_id" class="form-select mt-1.5 w-full rounded-lg border border-slate-300 bg-white px-3 py-2
                                   hover:border-slate-400 focus:border-primary dark:border-navy-450 dark:bg-navy-700
                                   dark:hover:border-navy-400 dark:focus:border-accent" required>
                        <option value="">-- Choisir --</option>
                        @foreach($types as $type)
                            <option value="{{ $type->id }}" {{ old('quizquestiontype_id') == $type->id ? 'selected' : '' }}>
                                {{ $type->titre }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                </div>

                <div class="flex justify-end space-x-3">
                    <button type="submit" class="btn bg-primary text-white">Enregistrer</button>
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
