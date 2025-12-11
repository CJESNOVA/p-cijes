<x-app-layout title="Niveaux de structuration" is-sidebar-open="true" is-header-blur="true">
    <main class="main-content w-full px-[var(--margin-x)] pb-8">
        <div class="flex items-center space-x-4 py-5 lg:py-6">
          <h2
            class="text-xl font-medium text-slate-800 dark:text-navy-50 lg:text-2xl"
          >
            Niveaux de structuration
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
            <li>Diagnostic</li>
          </ul> -->
        </div>
        <div class="grid grid-cols-12 lg:gap-6">
            <div class="col-span-12 pt-6 lg:col-span-8 lg:pb-6">

        <form action="{{ route('diagnosticentreprise.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
    
@php
    $existing = $existing ?? collect();
@endphp


          <!-- Input Validation -->
          <div class="card px-4 pb-4 sm:px-5">
            <div class="max-w-xxl">
              <div
                x-data="pages.formValidation.initFormValidationExample"
                class="mt-5 grid grid-cols-1 gap-4 sm:grid-cols-1 sm:gap-5 lg:gap-6"
              >


            <div class="max-w-xxl">
                  <label class="block">
                    <span>Entreprise </span>
                <select name="entreprise_id" 
      class="form-select mt-1.5 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 hover:border-slate-400 focus:border-primary dark:border-navy-450 dark:bg-navy-700 dark:hover:border-navy-400 dark:focus:border-accent" required>
                    <option value="">-- Choisir --</option>
                    @foreach ($entreprises as $entreprise)
                        <option value="{{ $entreprise->id }}"
                            {{ old('entreprise_id', $diagnostic->entreprise_id ?? '') == $entreprise->id ? 'selected' : '' }}>
                            {{ $entreprise->nom }}
                        </option>
                    @endforeach
                </select>

                  </label>
                </div>

              <div>
            </div>


            
            @foreach($diagnosticmodules as $diagnosticmodule)

                <div class="mb-8 border-b pb-4">
                    <h2 class="text-xl font-bold text-slate-800">{{ $diagnosticmodule->titre }}</h2>
                    <p class="text-slate-500">{!! $diagnosticmodule->description !!}</p>

                    @foreach($diagnosticmodule->diagnosticquestions as $diagnosticquestion)
                        <div class="mt-6">
                            <label class="block text-lg font-medium text-slate-700">
                                {{ $diagnosticquestion->position }} - {{ $diagnosticquestion->titre }} 
                                @if($diagnosticquestion->obligatoire) <span class="text-red-500">*</span> @endif
                            </label>

                            @php
                                $type = $diagnosticquestion->diagnosticquestiontype_id;
                                $inputName = $type == 2 ? "diagnosticreponses[{$diagnosticquestion->id}][]" : "diagnosticreponses[{$diagnosticquestion->id}]";
                                $inputClass = $type == 2 ? 
                                "form-checkbox is-basic size-5 rounded border-slate-400/70 checked:bg-slate-500 checked:border-slate-500 hover:border-slate-500 focus:border-slate-500 dark:border-navy-400 dark:checked:bg-navy-400" : 
                                "form-radio is-basic size-5 rounded-full border-slate-400/70 checked:border-slate-500 checked:bg-slate-500 hover:border-slate-500 focus:border-slate-500 dark:border-navy-400 dark:checked:bg-navy-400";
                            @endphp

                            @foreach($diagnosticquestion->diagnosticreponses as $diagnosticreponse)
                            @php
                                $isChecked = false;
                                if ($type == 2 && isset($existing[$diagnosticquestion->id])) {
                                    $isChecked = in_array($diagnosticreponse->id, $existing[$diagnosticquestion->id]);
                                } elseif ($type == 1 && isset($existing[$diagnosticquestion->id])) {
                                    $isChecked = $diagnosticreponse->id == $existing[$diagnosticquestion->id][0];
                                }
                            @endphp
                                <div class="mt-2">
                                    <label class="inline-flex items-center space-x-2">
                                        <input type="{{ $type == 2 ? 'checkbox' : 'radio' }}"
                                            name="{{ $inputName }}"
                                            value="{{ $diagnosticreponse->id }}"
                                            class="{{ $inputClass }}"
                                            {{ $isChecked ? 'checked' : '' }}
                                        />
                                        <span>{{ $diagnosticreponse->titre }}</span>
                                    </label>
                                </div>
                            @endforeach
                        </div>
                    @endforeach
                </div>
            @endforeach

            <div class="mt-8">
                <button type="submit"
                        class="btn bg-primary text-white hover:bg-primary-focus">
                    Valider le diagnostic
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