<x-app-layout title="Réserver l’espace : {{ $espace->titre }}" is-sidebar-open="true" is-header-blur="true">
<main class="main-content w-full px-[var(--margin-x)] pb-8">
        <div class="flex items-center space-x-4 py-5 lg:py-6">
          <h2
            class="text-xl font-medium text-slate-800 dark:text-navy-50 lg:text-2xl"
          >
            Réserver l’espace : {{ $espace->titre }}
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
            <li>Réserver l’espace : {{ $espace->titre }}</li>
          </ul> -->
        </div>
        <div class="grid grid-cols-12 lg:gap-6">
            <div class="col-span-12 pt-6 lg:col-span-8 lg:pb-6">

        <div class="bg-white shadow rounded-lg p-6">
            
    <h2 class="mb-4">Réserver l’espace : {{ $espace->titre }}</h2>

    <div class="card mb-4">
        <div class="card-body">
            <p><strong>Type :</strong> {{ $espace->espacetype->titre ?? '-' }}</p>
            <p><strong>Capacité :</strong> {{ $espace->capacite }}</p>
            <p><strong>Prix :</strong> {{ $espace->prix > 0 ? number_format($espace->prix, 0, ',', ' ') . ' FCFA' : 'Gratuit' }}</p>
            <p><strong>Description :</strong></p>
            <p>{!! $espace->description !!}</p>
        </div>
    </div>

    <form action="{{ route('espace.reserver.store', $espace->id) }}" method="POST">
        @csrf


            @if(session('error'))
                <div class="alert flex rounded-lg bg-error px-4 py-4 text-white sm:px-5">
                    {{ session('error') }}
                </div>
            @endif


          <!-- Input Validation -->
          <div class="card px-4 pb-4 sm:px-5">
            <div class="max-w-xxl">
              <div
                x-data="pages.formValidation.initFormValidationExample"
                class="mt-5 grid grid-cols-1 gap-4 sm:grid-cols-2 sm:gap-5 lg:gap-6"
              >

                            {{-- Sélection de l'accompagnement --}}
                            @if($accompagnements->count() > 0)
                            <div class="mb-4">
                                <label class="block font-medium mb-1">Accompagnement</label>
                                <select name="accompagnement_id" class="w-full border rounded p-2 @error('accompagnement_id') border-red-500 @enderror">
                                    <option value="">-- Sélectionner un accompagnement --</option>
                                    @foreach($accompagnements as $acc)
                                        <option value="{{ $acc->id }}" {{ old('accompagnement_id') == $acc->id ? 'selected' : '' }}>
                                            {{ 
                                                optional($acc->entreprise)->nom 
                                                    ? 'Accompagnement # ' . optional($acc->entreprise)->nom 
                                                    : (optional($acc->membre)->nom 
                                                        ? 'Accompagnement # ' . optional($acc->membre)->nom . ' ' . optional($acc->membre)->prenom 
                                                        : 'Accompagnement # ' . $acc->id)
                                            }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('accompagnement_id')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            @endif

                    <div></div> 
                           
        <!-- Date début -->
                <div>
                    <label class="block">Date de début</label>
                    <input x-init="$el._x_flatpickr = flatpickr($el)"
                           type="text" name="datedebut"
                           value="{{ old('datedebut', $espace->datedebut ?? '') }}"
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
                           value="{{ old('datefin', $espace->datefin ?? '') }}"
                           placeholder="Date de fin"
                           class="form-input peer w-full rounded-lg border border-slate-300 bg-transparent px-3 py-2 pl-9
                                  placeholder:text-slate-400/70 hover:border-slate-400 focus:border-primary
                                  dark:border-navy-450 dark:hover:border-navy-400 dark:focus:border-accent"
                           required>
                </div>


        @if($espace->prix > 0)
            <div class="mb-4">
                    <label for="ressourcecompte_id" class="block font-medium">Choisir un compte ressource :</label>
                    <select name="ressourcecompte_id" id="ressourcecompte_id" class="form-select mt-1.5 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 hover:border-slate-400 focus:border-primary dark:border-navy-450 dark:bg-navy-700 dark:hover:border-navy-400 dark:focus:border-accent" >
                        <option value="">-- Sélectionnez --</option>
                        @foreach($ressources as $r)
                            <option value="{{ $r->id }}">
                                {{ $r->nom_complet ?? '' }} 
                            </option>
                        @endforeach
                    </select>
                    @error('ressourcecompte_id') <p class="text-red-500 text-sm">{{ $message }}</p> @enderror
                </div>
                
                {{-- Montant --}}
                <div class="mb-4">
                    <label for="montant" class="block font-medium">Montant à payer :</label>
                    <input type="number" name="montant" id="montant"  class="form-input w-full rounded-lg border border-slate-300 bg-transparent px-3 py-2 placeholder:text-slate-400/70 hover:border-slate-400 focus:border-primary dark:border-navy-450 dark:hover:border-navy-400 dark:focus:border-accent"
                           value="{{ old('montant', $espace->prix ?? '') }}" >
                    @error('montant') <p class="text-red-500 text-sm">{{ $message }}</p> @enderror
                </div>
        @else
        <input type="hidden" name="montant" value="{{ $espace->prix ?? 0 }}">
        @endif

        <div class="mb-4">
            <label for="observation" class="form-label">Observation (optionnel)</label>
            <textarea name="observation" id="observation" rows="3" class="form-textarea w-full rounded-lg border border-slate-300 bg-transparent p-2.5
                                     placeholder:text-slate-400/70 hover:border-slate-400 focus:border-primary
                                     dark:border-navy-450 dark:hover:border-navy-400 dark:focus:border-accent">{{ old('observation') }}</textarea>
        </div>

          <div> 
          </div> 
        <button type="submit" class="btn bg-primary text-white">
            {{ $espace->prix > 0 ? 'Réserver et payer' : 'Réserver gratuitement' }}
        </button>
          </div> 
          </div> 
          </div> 
    </form>
</div>
          </div> 

            <div class="col-span-12 py-6 lg:sticky lg:bottom-0 lg:col-span-4 lg:self-end">
                @include('layouts.sidebar')
            </div>    

        </div>
      </main>
</x-app-layout>
