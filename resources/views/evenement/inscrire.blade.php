<x-app-layout title="Inscription Evenement" is-sidebar-open="true" is-header-blur="true">
<main class="main-content w-full px-[var(--margin-x)] pb-8">
        <div class="flex items-center space-x-4 py-5 lg:py-6">
          <h2
            class="text-xl font-medium text-slate-800 dark:text-navy-50 lg:text-2xl"
          >
            Inscription à l'évènement
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
            <li>Inscription à l'évènement</li>
          </ul> -->
        </div>
        <div class="grid grid-cols-12 lg:gap-6">
            <div class="col-span-12 pt-6 lg:col-span-8 lg:pb-6">

        <div class="bg-white shadow rounded-lg p-6">
            
    <h2 class="mb-4">Inscription à l’événement : <strong>{{ $evenement->titre }}</strong></h2>

    <div class="card mb-4">
        <div class="card-body">
            <p><strong>Date :</strong> {{ \Carbon\Carbon::parse($evenement->dateevenement)->format('d/m/Y H:i') }}</p>
            <p><strong>Type :</strong> {{ $evenement->evenementtype->titre ?? '-' }}</p>
            <p><strong>Résumé :</strong> {{ $evenement->resume }}</p>
            <p><strong>Prix :</strong> 
                @if($evenement->prix > 0)
                    {{ number_format($evenement->prix, 0, ',', ' ') }} FCFA
                @else
                    Gratuit
                @endif
            </p>
        </div>
    </div>

    <form method="POST" action="{{ route('evenement.inscrire.store', $evenement->id) }}">
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
                                <select name="accompagnement_id" class="w-full border rounded p-2 @error('accompagnement_id') border-red-500 @enderror" required>
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
                            
        @if(($evenement->prix ?? 0) > 0)
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
                           value="{{ old('montant', $evenement->prix ?? '') }}" >
                    @error('montant') <p class="text-red-500 text-sm">{{ $message }}</p> @enderror
                </div>
        @else
        <input type="hidden" name="montant" value="{{ $evenement->prix ?? 0 }}">
            <div class="alert alert-info">
                Cet événement est <strong>gratuit</strong>. Cliquez simplement sur "Valider" pour vous inscrire.
            </div>
        @endif

        <button type="submit" class="btn bg-primary text-white">
            <i class="fas fa-check-circle"></i> Valider mon inscription
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
