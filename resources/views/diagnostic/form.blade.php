<x-app-layout title="Tests psychotechniques" is-sidebar-open="true" is-header-blur="true">
    <main class="main-content w-full px-[var(--margin-x)] pb-8">
        <!-- Header moderne -->
        <div class="mb-2">
            <div class="flex items-center gap-4 mb-2">
                <div class="h-14 w-14 rounded-xl bg-gradient-to-br from-[#4FBE96] to-[#4FBE96] flex items-center justify-center shadow-lg">
                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                    </svg>
                </div>
                <div>
                    <h1 class="text-3xl font-bold text-slate-800 dark:text-navy-50">
                        Tests psychotechniques
                    </h1>
                    <p class="mt-2 text-slate-600 dark:text-navy-200 text-lg">
                        Évaluez vos compétences et aptitudes professionnelles
                    </p>
                </div>
            </div>
        </div>
        <div class="grid grid-cols-12 lg:gap-6">
            <div class="col-span-12 pt-6 lg:col-span-8 lg:pb-6">

                <!-- Messages -->
                @if(session('error'))
                    <div class="alert flex rounded-lg bg-red-500 px-6 py-4 text-white mb-6 shadow-lg">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        {{ session('error') }}
                    </div>
                @endif

                @if(session('success'))
                    <div class="alert flex rounded-lg bg-green-500 px-6 py-4 text-white mb-6 shadow-lg">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        {{ session('success') }}
                    </div>
                @endif

                @if(session('warning'))
                    <div class="alert flex rounded-lg bg-yellow-500 px-6 py-4 text-white mb-6 shadow-lg">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                        </svg>
                        {{ session('warning') }}
                    </div>
                @endif

                <form action="{{ route('diagnostic.store') }}" method="POST" enctype="multipart/form-data">
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