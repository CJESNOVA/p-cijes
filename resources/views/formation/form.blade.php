<x-app-layout title="Formation" is-sidebar-open="true" is-header-blur="true">
<main class="main-content w-full px-[var(--margin-x)] pb-8">
        <div class="flex items-center space-x-4 py-5 lg:py-6">
          <h2
            class="text-xl font-medium text-slate-800 dark:text-navy-50 lg:text-2xl"
          >
            {{ isset($formation) ? 'Modifier' : 'Créer' }} une formation
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
            <li>{{ isset($formation) ? 'Modifier' : 'Créer' }} une formation</li>
          </ul> -->
        </div>
        <div class="grid grid-cols-12 lg:gap-6">
            <div class="col-span-12 pt-6 lg:col-span-8 lg:pb-6">

    <form action="{{ $formation ? route('formation.update', $formation->id) : route('formation.store') }}" method="POST">
    @csrf
    @if($formation) @method('PUT') @endif

    @if (session('success'))
        <div class="alert flex rounded-lg bg-success px-4 py-4 text-white sm:px-5">
            {{ session('success') }}
        </div>
    @endif

    <!-- Input Validation -->
    <div class="card px-4 pb-4 sm:px-5">
        <div class="max-w-xxl">
            <div
                x-data="pages.formValidation.initFormValidationExample"
                class="mt-5 grid grid-cols-1 gap-4 sm:grid-cols-2 sm:gap-5 lg:gap-6"
            >
                <!-- Titre -->
                <div>
                    <label class="block">Titre</label>
                    <input type="text" name="titre"
                           value="{{ old('titre', $formation->titre ?? '') }}"
                           class="form-input w-full rounded-lg border border-slate-300 bg-transparent px-3 py-2
                                  placeholder:text-slate-400/70 hover:border-slate-400 focus:border-primary
                                  dark:border-navy-450 dark:hover:border-navy-400 dark:focus:border-accent"
                           required>
                </div>

                <!-- Prix -->
                <div>
                    <label class="block">Prix</label>
                    <input type="number" step="0.01" name="prix"
                           value="{{ old('prix', $formation->prix ?? '') }}"
                           class="form-input w-full rounded-lg border border-slate-300 bg-transparent px-3 py-2
                                  placeholder:text-slate-400/70 hover:border-slate-400 focus:border-primary
                                  dark:border-navy-450 dark:hover:border-navy-400 dark:focus:border-accent">
                </div>

                <!-- Expert -->
                <div>
                    <label class="block">Expert</label>
                    <select name="expert_id"
                            class="form-select mt-1.5 w-full rounded-lg border border-slate-300 bg-white px-3 py-2
                                   hover:border-slate-400 focus:border-primary dark:border-navy-450 dark:bg-navy-700
                                   dark:hover:border-navy-400 dark:focus:border-accent"
                            required>
                        <option value="">-- Choisir --</option>
                        @foreach($experts as $expert)
                            <option value="{{ $expert->id }}"
                                {{ old('expert_id', $formation->expert_id ?? '') == $expert->id ? 'selected' : '' }}>
                                {{ $expert->domaine ?? '' }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Niveau -->
                <div>
                    <label class="block">Niveau</label>
                    <select name="formationniveau_id"
                            class="form-select mt-1.5 w-full rounded-lg border border-slate-300 bg-white px-3 py-2
                                   hover:border-slate-400 focus:border-primary dark:border-navy-450 dark:bg-navy-700
                                   dark:hover:border-navy-400 dark:focus:border-accent"
                            required>
                        <option value="">-- Choisir --</option>
                        @foreach($formationniveaux as $niveau)
                            <option value="{{ $niveau->id }}"
                                {{ old('formationniveau_id', $formation->formationniveau_id ?? '') == $niveau->id ? 'selected' : '' }}>
                                {{ $niveau->titre }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Type -->
                <div>
                    <label class="block">Type</label>
                    <select name="formationtype_id"
                            class="form-select mt-1.5 w-full rounded-lg border border-slate-300 bg-white px-3 py-2
                                   hover:border-slate-400 focus:border-primary dark:border-navy-450 dark:bg-navy-700
                                   dark:hover:border-navy-400 dark:focus:border-accent"
                            required>
                        <option value="">-- Choisir --</option>
                        @foreach($formationtypes as $type)
                            <option value="{{ $type->id }}"
                                {{ old('formationtype_id', $formation->formationtype_id ?? '') == $type->id ? 'selected' : '' }}>
                                {{ $type->titre }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Date début -->
                <div>
                    <label class="block">Date de début</label>
                    <input x-init="$el._x_flatpickr = flatpickr($el)"
                           type="text" name="datedebut"
                           value="{{ old('datedebut', $formation->datedebut ?? '') }}"
                           placeholder="Date de début"
                           class="form-input peer w-full rounded-lg border border-slate-300 bg-transparent px-3 py-2 pl-9
                                  placeholder:text-slate-400/70 hover:border-slate-400 focus:border-primary
                                  dark:border-navy-450 dark:hover:border-navy-400 dark:focus:border-accent"
                           required>
                </div>

                <!-- Date fin -->
                <div>
                    <label class="block">Date de fin</label>
                    <input x-init="$el._x_flatpickr = flatpickr($el)"
                           type="text" name="datefin"
                           value="{{ old('datefin', $formation->datefin ?? '') }}"
                           placeholder="Date de fin"
                           class="form-input peer w-full rounded-lg border border-slate-300 bg-transparent px-3 py-2 pl-9
                                  placeholder:text-slate-400/70 hover:border-slate-400 focus:border-primary
                                  dark:border-navy-450 dark:hover:border-navy-400 dark:focus:border-accent"
                           required>
                </div>

                <!-- Description -->
                <div class="md:col-span-2">
                    <label class="block">Description</label>
                    <textarea name="description"
                              class="form-textarea w-full rounded-lg border border-slate-300 bg-transparent p-2.5
                                     placeholder:text-slate-400/70 hover:border-slate-400 focus:border-primary
                                     dark:border-navy-450 dark:hover:border-navy-400 dark:focus:border-accent"
                              rows="4">{{ old('description', $formation->description ?? '') }}</textarea>
                </div>

                <!-- Bouton -->
                <div class="md:col-span-2">
                    <button type="submit" class="btn bg-primary text-white">
                        {{ $formation ? 'Mettre à jour' : 'Créer' }}
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