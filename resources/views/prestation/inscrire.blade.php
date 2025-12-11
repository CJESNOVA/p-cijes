<x-app-layout title="Paiement de la prestation" is-sidebar-open="true" is-header-blur="true">
<main class="main-content w-full px-[var(--margin-x)] pb-8">
        <div class="flex items-center space-x-4 py-5 lg:py-6">
          <h2
            class="text-xl font-medium text-slate-800 dark:text-navy-50 lg:text-2xl"
          >
            Paiement de la prestation
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
            <li>Paiement de la prestation</li>
          </ul> -->
        </div>
        <div class="grid grid-cols-12 lg:gap-6">
            <div class="col-span-12 pt-6 lg:col-span-8 lg:pb-6">

        <div class="bg-white p-6 rounded shadow-md mb-6">
                <h2 class="text-xl font-semibold mb-2">{{ $prestation->titre }}</h2>
                <p class="mb-1"><strong>Entreprise :</strong> {{ $prestation->entreprise->nom ?? 'Inconnue' }}</p>
                <p class="mb-1"><strong>Type :</strong> {{ $prestation->prestationtype->titre ?? '-' }}</p>
                <p class="mb-1"><strong>Prix :</strong> {{ number_format($prestation->prix, 2) }}</p>
                <p class="mb-1"><strong>Durée :</strong> {{ $prestation->duree }} {{ $prestation->duree > 1 ? 'heures' : 'heure' }}</p>
                <div class="mt-3">{!! $prestation->description !!}</div>
            </div>

            <form action="{{ route('prestation.inscrire.store', $prestation->id) }}" method="POST">
                @csrf

                @if(session('error'))
                    <div class="alert flex rounded-lg bg-error px-4 py-4 text-white sm:px-5 mb-4">
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
                            {{-- Compte ressource --}}
                            @if($ressources->count() > 0)
                            <div class="mb-4">
                                <label class="block font-medium mb-1">Compte ressource pour le paiement</label>
                                <select name="ressourcecompte_id" class="w-full border rounded p-2 @error('ressourcecompte_id') border-red-500 @enderror">
                                    <option value="">-- Sélectionner un compte --</option>
                                    @foreach($ressources as $res)
                                        <option value="{{ $res->id }}" {{ old('ressourcecompte_id') == $res->id ? 'selected' : '' }}>
                                            {{ $res->nom_complet }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('ressourcecompte_id')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            @endif
                            
                            {{-- Montant --}}
                            <div class="mb-4">
                                <label class="block font-medium mb-1">Montant à payer</label>
                                <input type="number" name="montant" value="{{ old('montant', $prestation->prix) }}" min="0" step="0.01"
                                       class="w-full border rounded p-2 @error('montant') border-red-500 @enderror">
                                @error('montant')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                <div class="flex justify-end">
                    <button type="submit" class="btn bg-primary text-white">Confirmer le paiement de la prestation</button>
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
