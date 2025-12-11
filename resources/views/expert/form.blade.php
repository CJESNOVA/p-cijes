<x-app-layout title="Devenir expert" is-sidebar-open="true" is-header-blur="true">
    <main class="main-content w-full px-[var(--margin-x)] pb-8">
        <div class="flex items-center space-x-4 py-5 lg:py-6">
          <h2
            class="text-xl font-medium text-slate-800 dark:text-navy-50 lg:text-2xl"
          >
            Devenir expert
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
            <li>Devenir expert</li>
          </ul> -->
        </div>
        <div class="grid grid-cols-12 lg:gap-6">
            <div class="col-span-12 pt-6 lg:col-span-8 lg:pb-6">

                {{-- Formulaire création / édition --}}
                <form action="{{ $expert->exists ? route('expert.update', $expert->id) : route('expert.store') }}"
                      method="POST"
                      enctype="multipart/form-data">
                    @csrf
                    @if($expert->exists)
                        @method('PUT')
                    @endif

                    {{-- Message succès --}}
                    @if (session('success'))
                        <div class="alert flex rounded-lg bg-success px-4 py-4 text-white sm:px-5 mb-4">
                            {{ session('success') }}
                        </div>
                    @endif

                    <div class="card px-4 pb-4 sm:px-5">
                        <div class="max-w-xxl">
                            <div x-data="pages.formValidation.initFormValidationExample"
                                 class="mt-5 grid grid-cols-1 gap-4 sm:grid-cols-1 sm:gap-5 lg:gap-6">

                                {{-- Type d'expert --}}
                                <div>
                                    <label class="block">
                                        <span>Type d’expert</span>
                                        <select name="experttype_id" required
                                                class="form-select mt-1.5 w-full rounded-lg border border-slate-300 bg-white px-3 py-2
                                                       hover:border-slate-400 focus:border-primary dark:border-navy-450
                                                       dark:bg-navy-700 dark:hover:border-navy-400 dark:focus:border-accent">
                                            <option value="">-- Choisir --</option>
                                            @foreach ($experttypes as $type)
                                                <option value="{{ $type->id }}"
                                                    {{ old('experttype_id', $expert->experttype_id) == $type->id ? 'selected' : '' }}>
                                                    {{ $type->titre }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('experttype_id')
                                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                        @enderror
                                    </label>
                                </div>

                                {{-- Domaine d'expertise --}}
                                <div>
                                    <label class="block">
                                        <span>Domaine d'expertise</span>
                                        <textarea name="domaine" required
                                            placeholder="Domaine d'expertise"
                                            class="form-textarea w-full rounded-lg border border-slate-300 bg-transparent p-2.5
                                                   placeholder:text-slate-400/70 hover:border-slate-400 focus:border-primary
                                                   dark:border-navy-450 dark:hover:border-navy-400 dark:focus:border-accent">{{ old('domaine', $expert->domaine) }}</textarea>
                                        @error('domaine')
                                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                        @enderror
                                    </label>
                                </div>

                                {{-- Fichier --}}
                                <div>
                                    <span>Fichier (facultatif)</span>
                                    <div class="mt-1">
                                        <label
                                            class="btn bg-slate-150 font-medium text-slate-800 hover:bg-slate-200
                                                   focus:bg-slate-200 active:bg-slate-200/80 dark:bg-navy-500
                                                   dark:text-navy-50 dark:hover:bg-navy-450 dark:focus:bg-navy-450
                                                   dark:active:bg-navy-450/90 cursor-pointer">
                                            <input type="file" name="fichier"
                                                   accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.odt,.ods,.txt"
                                                   class="hidden">
                                            <span>Choisir un document</span>
                                        </label>

                                        @if($expert->fichier)
                                            <p class="mt-2 text-sm text-gray-600">
                                                Fichier actuel : <a href="{{ env('APP_URL') . 'storage/' . $expert->fichier }}" target="_blank" class="underline">{{ basename($expert->fichier) }}</a>
                                            </p>
                                        @endif

                                        @error('fichier')
                                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>

                                {{-- Bouton soumettre --}}
                                <div class="flex justify-end mt-4">
                                    <button type="submit"
                                        class="btn bg-primary font-medium text-white hover:bg-primary-focus focus:bg-primary-focus
                                               active:bg-primary-focus/90 dark:bg-accent dark:hover:bg-accent-focus
                                               dark:focus:bg-accent-focus dark:active:bg-accent/90">
                                        {{ $expert->exists ? 'Mettre à jour' : 'Devenir expert' }}
                                    </button>
                                </div>

                            </div>
                        </div>
                    </div>
                </form>

            </div>

            {{-- Sidebar --}}
            <div class="col-span-12 py-6 lg:sticky lg:bottom-0 lg:col-span-4 lg:self-end">
                @include('layouts.sidebar')
            </div>
        </div>
    </main>
</x-app-layout>
