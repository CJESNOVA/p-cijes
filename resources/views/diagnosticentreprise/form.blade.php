<x-app-layout title="Niveaux de structuration" is-sidebar-open="true" is-header-blur="true">
    <main class="main-content w-full px-[var(--margin-x)] pb-8">
        <!-- Header moderne -->
        <div class="mb-2">
            <div class="flex items-center gap-4 mb-2">
                <div class="h-14 w-14 rounded-xl bg-gradient-to-br from-orange-500 to-orange-600 flex items-center justify-center shadow-lg">
                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h-1m2-5h-8"></path>
                    </svg>
                </div>
                <div>
                    <h1 class="text-3xl font-bold text-slate-800 dark:text-navy-50">
                        Niveaux de structuration
                    </h1>
                    <p class="mt-2 text-slate-600 dark:text-navy-200 text-lg">
                        Évaluez la maturité de votre entreprise
                    </p>
                </div>
            </div>
        </div>
        <div class="grid grid-cols-12 lg:gap-6">
            <div class="col-span-12 pt-6 lg:col-span-8 lg:pb-6">
              
        <h2 class="text-xl font-medium text-slate-800 lg:text-2xl mb-6">
            Diagnostic – {{ $diagnostic->entreprise->nom ?? 'Nouvelle entreprise' }}
        </h2>

        <form action="{{ route('diagnosticentreprise.store') }}" method="POST">
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


            <input type="hidden" name="entreprise_id" value="{{ $entrepriseId }}">

            @foreach($diagnosticmodules as $diagnosticmodule)
                <div class="mb-8 border-b pb-4">
                    <h2 class="text-xl font-bold">{{ $diagnosticmodule->titre }}</h2>
                    <p class="text-slate-500">{!! $diagnosticmodule->description !!}</p>

                    @foreach($diagnosticmodule->diagnosticquestions as $diagnosticquestion)
                        <div class="mt-6">
                            <label class="block text-lg font-medium">
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