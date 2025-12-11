<x-app-layout title="Nouvelle Réponse" is-sidebar-open="true" is-header-blur="true">
<main class="main-content w-full px-[var(--margin-x)] pb-8">
        <div class="flex items-center space-x-4 py-5 lg:py-6">
          <h2
            class="text-xl font-medium text-slate-800 dark:text-navy-50 lg:text-2xl"
          >
            Ajouter une réponse à la question : {{ $quizquestion->titre }}
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
            <li>Ajouter une réponse à la question : {{ $quizquestion->titre }}</li>
          </ul> -->
        </div>
        <div class="grid grid-cols-12 lg:gap-6">
            <div class="col-span-12 pt-6 lg:col-span-8 lg:pb-6">

            <form action="{{ route('quizreponse.store', $quizquestion) }}" method="POST">
                @csrf

    <!-- Input Validation -->
    <div class="card px-4 pb-4 sm:px-5">
        <div class="max-w-xxl">
            <div
                x-data="pages.formValidation.initFormValidationExample"
                class="mt-5 grid grid-cols-1 gap-4 sm:grid-cols-1 sm:gap-5 lg:gap-6"
            >
                <div class="mb-4">
                    <label class="block text-sm font-medium text-slate-700">Texte de la réponse</label>
                    <textarea name="text" class="form-input w-full rounded-lg border border-slate-300 bg-transparent px-3 py-2
                                  placeholder:text-slate-400/70 hover:border-slate-400 focus:border-primary
                                  dark:border-navy-450 dark:hover:border-navy-400 dark:focus:border-accent" required>{{ old('text') }}</textarea>
              </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-slate-700">Correcte ?</label>
                    <select name="correcte" class="form-select mt-1.5 w-full rounded-lg border border-slate-300 bg-white px-3 py-2
                                   hover:border-slate-400 focus:border-primary dark:border-navy-450 dark:bg-navy-700
                                   dark:hover:border-navy-400 dark:focus:border-accent" required>
                        <option value="0" {{ old('correcte') == "0" ? 'selected' : '' }}>❌ Non</option>
                        <option value="1" {{ old('correcte') == "1" ? 'selected' : '' }}>✅ Oui</option>
                    </select>
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