<x-app-layout title="{{ $plan->exists ? 'Modifier le Plan' : 'Créer un Plan' }}" is-sidebar-open="true" is-header-blur="true">
    <main class="main-content w-full px-[var(--margin-x)] pb-8">
        <div class="flex items-center space-x-4 py-5 lg:py-6">
          <h2
            class="text-xl font-medium text-slate-800 dark:text-navy-50 lg:text-2xl"
          >
            {{ $plan->exists ? 'Modifier le Plan' : 'Créer un Plan' }}
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
            <li>{{ $plan->exists ? 'Modifier le Plan' : 'Créer un Plan' }}</li>
          </ul> -->
        </div>
        <div class="grid grid-cols-12 lg:gap-6">
            <div class="col-span-12 pt-6 lg:col-span-8 lg:pb-6">

    <form method="POST" action="{{ $plan->exists ? route('plan.update', $plan->id) : route('plan.store') }}">
        @csrf
        @if($plan->exists)
            @method('PUT')
        @endif

          <!-- Input Validation -->
          <div class="card px-4 pb-4 sm:px-5">
            <div class="max-w-xxl">
              <div
                x-data="pages.formValidation.initFormValidationExample"
                class="mt-5 grid grid-cols-1 gap-4 sm:grid-cols-1 sm:gap-5 lg:gap-6"
              >
<div>
    <label class="block">
        <span>Objectif</span>
        <textarea
            class="form-textarea w-full rounded-lg border border-slate-300 bg-transparent p-2.5 placeholder:text-slate-400/70 hover:border-slate-400 focus:border-primary dark:border-navy-450 dark:hover:border-navy-400 dark:focus:border-accent"
            name="objectif"
            placeholder="Ex: Augmenter le chiffre d'affaires de 20%"
            required
        />{{ old('objectif', $plan->objectif ?? '') }}</textarea>
    </label>
</div>

<div>
    <label class="block">
        <span>Action prioritaire</span>
        <textarea
            class="form-textarea w-full rounded-lg border border-slate-300 bg-transparent p-2.5 placeholder:text-slate-400/70 hover:border-slate-400 focus:border-primary dark:border-navy-450 dark:hover:border-navy-400 dark:focus:border-accent"
            name="actionprioritaire"
            placeholder="Ex: Lancer une nouvelle campagne marketing"
            required
        />{{ old('actionprioritaire', $plan->actionprioritaire ?? '') }}</textarea>
    </label>
</div>

                    <div>
                        <span>Date du plan</span>
                  <label class="relative flex">
                  <input
                    x-init="$el._x_flatpickr = flatpickr($el)"
                    class="form-input peer w-full rounded-lg border border-slate-300 bg-transparent px-3 py-2 pl-9 placeholder:text-slate-400/70 hover:border-slate-400 focus:border-primary dark:border-navy-450 dark:hover:border-navy-400 dark:focus:border-accent"
                    placeholder="Date du plan"
                    type="text" name="dateplan" value="{{ old('dateplan', $plan->dateplan ?? '') }}" required
                  />
    <span
      class="pointer-events-none absolute flex h-full w-10 items-center justify-center text-slate-400 peer-focus:text-primary dark:text-navy-300 dark:peer-focus:text-accent"
    >
      <svg
        xmlns="http://www.w3.org/2000/svg"
        class="size-5 transition-colors duration-200"
        fill="none"
        viewBox="0 0 24 24"
        stroke="currentColor"
        stroke-width="1.5"
      >
        <path
          stroke-linecap="round"
          stroke-linejoin="round"
          d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"
        />
      </svg>
    </span>
                </label>
                </div>

<div>
    <label class="block">
        @if(isset($accompagnements) && $accompagnements->count() == 1)
            <input type="hidden" name="accompagnement_id" value="{{ $accompagnements->first()->id }}">
        @else
        <span>Accompagnement</span>
        <select
            name="accompagnement_id"
            class="form-select mt-1.5 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 
                   hover:border-slate-400 focus:border-primary dark:border-navy-450 dark:bg-navy-700 
                   dark:hover:border-navy-400 dark:focus:border-accent"
            required
            {{ isset($accompagnements) && $accompagnements->count() == 1 ? 'disabled' : '' }}
        >
            <option value="">-- Sélectionner --</option>
            @foreach($accompagnements as $acc)
                <option value="{{ $acc->id }}"
                    {{ old('accompagnement_id', $plan->accompagnement_id ?? '') == $acc->id ? 'selected' : '' }}>
                    {{ $acc->membre->nom ?? '' }} {{ $acc->membre->prenom ?? '' }} {{ $acc->entreprise->nom ?? '' }} - {{ $acc->accompagnementniveau->titre ?? '' }}
                </option>
            @endforeach
        </select>
        @endif

    </label>
</div>

<div>
    <button
        type="submit"
        class="btn bg-primary font-medium text-white hover:bg-primary-focus focus:bg-primary-focus active:bg-primary-focus/90 dark:bg-accent dark:hover:bg-accent-focus dark:focus:bg-accent-focus dark:active:bg-accent/90"
    >
        {{ isset($plan) && $plan->exists ? 'Mettre à jour' : 'Créer' }}
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


