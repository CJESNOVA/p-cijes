<x-app-layout title="{{ isset($prestation) ? 'Modifier' : 'Créer' }} une prestation" is-sidebar-open="true" is-header-blur="true">
    <main class="main-content w-full px-[var(--margin-x)] pb-8">
        <div class="flex items-center space-x-4 py-5 lg:py-6">
          <h2
            class="text-xl font-medium text-slate-800 dark:text-navy-50 lg:text-2xl"
          >
            {{ isset($prestation) ? 'Modifier' : 'Créer' }} une prestation
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
            <li>Définir ma disponibilité </li>
          </ul> -->
        </div>
        <div class="grid grid-cols-12 lg:gap-6">
            <div class="col-span-12 pt-6 lg:col-span-8 lg:pb-6">


            @if (session('success'))
                <div class="alert flex rounded-lg bg-success px-4 py-4 text-white sm:px-5">{{ session('success') }}</div>
            @endif
                
        <form action="{{ $prestation ? route('prestation.update', $prestation->id) : route('prestation.store') }}"
              method="POST">
            @csrf
            
            @if($prestation)
                @method('PUT')
            @endif

            <div class="card px-4 pb-4 sm:px-5">
                <div class="max-w-xxl">
                    <div class="mt-5 grid grid-cols-1 gap-4 sm:grid-cols-2 sm:gap-5 lg:gap-6">
                        <div>
                            <label class="block">
                                <span>Entreprise</span>
                                <select name="entreprise_id" class="form-select mt-1.5 w-full rounded-lg border border-slate-300 bg-white px-3 py-2" required>
                                    <option value="">-- Choisir --</option>
                                    @foreach ($entreprises as $entreprise)
                                        <option value="{{ $entreprise->entreprise_id }}" {{ old('entreprise_id', $prestation->entreprise_id ?? '') == $entreprise->entreprise_id ? 'selected' : '' }}>
                                            {{ $entreprise->entreprise->nom }}
                                        </option>
                                    @endforeach
                                </select>
                            </label>
                        </div>
                        <div>
                            <label class="block">
                                <span>Type de prestation</span>
                                <select name="prestationtype_id" class="form-select mt-1.5 w-full rounded-lg border border-slate-300 bg-white px-3 py-2" required>
                                    <option value="">-- Choisir --</option>
                                    @foreach ($prestationtypes as $type)
                                        <option value="{{ $type->id }}" {{ old('prestationtype_id', $prestation->prestationtype_id ?? '') == $type->id ? 'selected' : '' }}>
                                            {{ $type->titre }}
                                        </option>
                                    @endforeach
                                </select>
                            </label>
                        </div>
                <div>
                  <label class="block">
                    <span>Libellé de la prestation </span>
                  <input
                    class="form-input w-full rounded-lg border border-slate-300 bg-transparent px-3 py-2 placeholder:text-slate-400/70 hover:border-slate-400 focus:border-primary dark:border-navy-450 dark:hover:border-navy-400 dark:focus:border-accent"
                    placeholder="Libellé de la prestation"
                    type="text" name="titre" value="{{ old('titre', $prestation->titre ?? '') }}" required
                  />
                </label>
                </div>
                        <div>
                            <label class="block">
                                <span>Prix</span>
                                <input type="number" name="prix" class="form-input w-full rounded-lg border border-slate-300 bg-transparent px-3 py-2"
                                       placeholder="Prix" value="{{ old('prix', $prestation->prix ?? '') }}" required min="0" step="0.01" />
                            </label>
                        </div>
                        <div>
                            <label class="block">
                                <span>Durée</span>
                                <input type="text" name="duree" class="form-input w-full rounded-lg border border-slate-300 bg-transparent px-3 py-2"
                                       placeholder="Durée" value="{{ old('duree', $prestation->duree ?? '') }}" required />
                            </label>
                        </div>

                <div class="md:col-span-2">
                  <label class="block">
                    <span>Description </span>
                <textarea name="description" rows="4" class="form-textarea w-full rounded-lg border border-slate-300 bg-transparent p-2.5 placeholder:text-slate-400/70 hover:border-slate-400 focus:border-primary dark:border-navy-450 dark:hover:border-navy-400 dark:focus:border-accent"
    >{{ old('description', $prestation->description ?? '') }}</textarea>
                  </label>
                </div>
                        <div></div>
                        <div>
                            <button type="submit"
                                    class="btn bg-primary font-medium text-white hover:bg-primary-focus focus:bg-primary-focus">
                                Enregistrer
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


