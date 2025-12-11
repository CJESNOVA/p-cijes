<x-app-layout title="Prescrire une formation" is-sidebar-open="true" is-header-blur="true">
    <main class="main-content w-full px-[var(--margin-x)] pb-8">
        <div class="flex items-center space-x-4 py-5 lg:py-6">
          <h2
            class="text-xl font-medium text-slate-800 dark:text-navy-50 lg:text-2xl"
          >
            Prescrire une formation
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
            <li>Prescrire une formation</li>
          </ul> -->
        </div>
        <div class="grid grid-cols-12 lg:gap-6">
            <div class="col-span-12 pt-6 lg:col-span-8 lg:pb-6">

        @if ($errors->any())
            <div class="alert flex rounded-lg bg-error px-4 py-4 text-white sm:px-5 mb-4">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>- {{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('conseiller.storeFormation') }}" method="POST">
            @csrf

            <input type="hidden" name="conseiller_id" value="{{ $conseillerId }}">

          <div class="card px-4 pb-4 sm:px-5">
            <div class="max-w-xxl">
              <div
                x-data="pages.formValidation.initFormValidationExample"
                class="mt-5 grid grid-cols-1 gap-4 sm:grid-cols-2 sm:gap-5 lg:gap-6"
              >
                        {{-- Formation --}}
                        <div>
                            <label class="block">
                                <span>Formation</span>
                                <select name="formation_id"
                                        class="form-select mt-1.5 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 
                                            hover:border-slate-400 focus:border-primary dark:border-navy-450 
                                            dark:bg-navy-700 dark:hover:border-navy-400 dark:focus:border-accent"
                                        required>
                                    <option value="">-- Choisir une formation --</option>
                                    @foreach($formations as $p)
                                        <option value="{{ $p->id }}" {{ old('formation_id') == $p->id ? 'selected' : '' }}>
                                            {{ $p->titre }} - {{ $p->formationniveau->titre ?? 'Sans type' }}
                                        </option>
                                    @endforeach
                                </select>
                            </label>
                        </div>

                        {{-- Membre --}}
                        @if($membres->isEmpty())
                            <input type="hidden" name="membre_id" value="0">
                        @else
                        <div>
                            <label class="block">
                                <span>Membre</span>
                                <select name="membre_id"
                                        class="form-select mt-1.5 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 
                                            hover:border-slate-400 focus:border-primary dark:border-navy-450 
                                            dark:bg-navy-700 dark:hover:border-navy-400 dark:focus:border-accent"
                                        required>
                                    <option value="">-- Choisir un membre --</option>
                                    @foreach ($membres as $m)
                                        <option value="{{ $m->id }}" {{ old('membre_id') == $m->id ? 'selected' : '' }}>
                                            {{ $m->nom }} {{ $m->prenom }}
                                        </option>
                                    @endforeach
                                </select>
                            </label>
                        </div>
                        @endif

                        {{-- Entreprise --}}
                        @if($entreprises->isEmpty())
                            <input type="hidden" name="entreprise_id" value="0">
                        @else
                        <div>
                            <label class="block">
                                <span>Entreprise</span>
                                <select name="entreprise_id"
                                        class="form-select mt-1.5 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 
                                            hover:border-slate-400 focus:border-primary dark:border-navy-450 
                                            dark:bg-navy-700 dark:hover:border-navy-400 dark:focus:border-accent">
                                    <option value="">-- Choisir une entreprise --</option>
                                    @foreach ($entreprises as $e)
                                        <option value="{{ $e->id }}" {{ old('entreprise_id') == $e->id ? 'selected' : '' }}>
                                            {{ $e->nom }}
                                        </option>
                                    @endforeach
                                </select>
                            </label>
                        </div>
                        @endif

                        {{-- Bouton --}}
                        <div class="col-span-2">
                            <button type="submit"
                                    class="btn bg-primary font-medium text-white hover:bg-primary-focus 
                                        focus:bg-primary-focus active:bg-primary-focus/90 
                                        dark:bg-accent dark:hover:bg-accent-focus 
                                        dark:focus:bg-accent-focus dark:active:bg-accent/90">
                                Enregistrer la prescription
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