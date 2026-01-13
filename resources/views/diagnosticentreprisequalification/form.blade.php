<x-app-layout title="Test de qualification" is-sidebar-open="true" is-header-blur="true">
    <main class="main-content w-full px-[var(--margin-x)] pb-8">
        <div class="flex items-center space-x-4 py-5 lg:py-6">
          <h2
            class="text-xl font-medium text-slate-800 dark:text-navy-50 lg:text-2xl"
          >
            Test de qualification
          </h2>
          <div class="hidden h-full py-1 sm:flex">
            <div class="h-full w-px bg-slate-300 dark:bg-navy-600"></div>
          </div>
        </div>
        <div class="grid grid-cols-12 lg:gap-6">
            <div class="col-span-12 pt-6 lg:col-span-8 lg:pb-6">
              
        <h2 class="text-xl font-medium text-slate-800 lg:text-2xl mb-6">
            Test de qualification – {{ $diagnostic->entreprise->nom ?? 'Nouvelle entreprise' }}
        </h2>

        @if($currentModule)
            <div class="mb-4 flex items-center justify-between">
                <div class="text-sm text-slate-600 dark:text-navy-300">
                    Module {{ $modules->search(function($module) use ($currentModule) { return $module->id == $currentModule->id; }) + 1 }} 
                    sur {{ $modules->count() }}
                </div>
                @if(session('success'))
                    <div class="alert flex rounded-lg bg-success px-3 py-2 text-white text-sm">
                        {{ session('success') }}
                    </div>
                @endif
            </div>

            <form action="{{ route('diagnosticentreprisequalification.saveModule', [$entrepriseId, $currentModule->id]) }}" method="POST">
                @csrf
                <input type="hidden" name="entreprise_id" value="{{ $entrepriseId }}">
    
@php
    $existing = $existing ?? collect();
@endphp

                @if(session('error'))
                    <div class="alert flex rounded-lg bg-danger px-4 py-4 text-white sm:px-5">{{ session('error') }}</div>
                @endif

                @if ($errors->any())
                    <div class="alert flex rounded-lg bg-warning px-4 py-4 text-white sm:px-5">
                        <ul class="list-disc list-inside">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- Module actuel -->
                <div class="card overflow-hidden shadow-2xl">
                    <div class="card-title bg-gradient-to-r from-slate-50 to-white dark:from-navy-800 dark:to-navy-700 p-6">
                        <div class="flex items-center space-x-4">
                            <h3 class="text-xl font-bold text-slate-800 dark:text-white">
                                {{ $currentModule->titre }}
                            </h3>
                        </div>
                        <div class="flex items-center space-x-4">
                            @if($currentModule->description)
                                <p class="text-sm text-slate-600 dark:text-navy-300 mt-1">
                                    {{ $currentModule->description }}
                                </p>
                            @endif
                        </div>
                    </div>

                    @if($currentModule->vignette)
                        <div class="p-6 bg-white dark:bg-navy-900">
                            <img src="{{ env('SUPABASE_BUCKET_URL') . '/' . $currentModule->vignette }}" 
                                 alt="Vignette du module {{ $currentModule->titre }}" 
                                 class="w-full h-auto rounded-lg shadow-lg">
                        </div>
                    @endif



                    <div class="card-body bg-white dark:bg-navy-900 p-6">
                        @if($currentModule->diagnosticquestions->count() > 0)
                            @foreach($currentModule->diagnosticquestions as $diagnosticquestion)
                                <div class="mb-8 p-4 rounded-lg bg-slate-50 dark:bg-navy-800 border border-slate-200 dark:border-navy-700">
                                    <label class="block text-lg font-semibold text-slate-700 dark:text-navy-200 mb-3">
                                        <span class="inline-flex items-center space-x-2">
                                            <span class="flex items-center justify-center w-8 h-8 rounded-full bg-slate-200 dark:bg-navy-700 text-slate-700 dark:text-navy-300 text-sm font-bold">
                                                {{ $diagnosticquestion->position }}
                                            </span>
                                            <span>{{ $diagnosticquestion->titre }}</span>
                                        </span>
                                        @if($diagnosticquestion->obligatoire) <span class="text-red-500 ml-2">*</span> @endif
                                    </label>

                                    @php
                                        $type = $diagnosticquestion->diagnosticquestiontype_id;
                                        $inputName = $type == 2 ? "reponses[{$diagnosticquestion->id}][]" : "reponses[{$diagnosticquestion->id}]";
                                        $inputClass = $type == 2 ? 
                                        "form-checkbox is-basic size-5 rounded border-slate-400/70 checked:bg-primary checked:border-primary hover:border-primary focus:border-primary dark:border-navy-400 dark:checked:bg-primary dark:checked:border-primary" : 
                                        "form-radio is-basic size-5 rounded-full border-slate-400/70 checked:border-primary checked:bg-primary hover:border-primary focus:border-primary dark:border-navy-400 dark:checked:bg-primary dark:checked:border-primary";
                                    @endphp

                                    <div class="space-y-3">
                                        @foreach($diagnosticquestion->diagnosticreponses as $diagnosticreponse)
                                        @php
                                            $isChecked = false;
                                            if ($type == 2 && isset($existing[$diagnosticquestion->id])) {
                                                $isChecked = in_array($diagnosticreponse->id, $existing[$diagnosticquestion->id]);
                                            } elseif ($type == 1 && isset($existing[$diagnosticquestion->id])) {
                                                $isChecked = $diagnosticreponse->id == $existing[$diagnosticquestion->id][0];
                                            }
                                        @endphp
                                            <label class="flex items-start space-x-3 p-3 rounded-lg bg-white dark:bg-navy-700 hover:bg-slate-50 dark:hover:bg-navy-600 border border-slate-200 dark:border-navy-600 cursor-pointer transition-all duration-200 hover:shadow-md">
                                                <input type="{{ $type == 2 ? 'checkbox' : 'radio' }}"
                                                    name="{{ $inputName }}"
                                                    value="{{ $diagnosticreponse->id }}"
                                                    class="{{ $inputClass }} mt-1"
                                                    {{ $isChecked ? 'checked' : '' }}
                                                />
                                                <span class="text-sm text-slate-600 dark:text-navy-300 leading-relaxed">
                                                    {{ $diagnosticreponse->titre }}
                                                </span>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="text-center py-8">
                                <i class="fas fa-clipboard-check text-4xl text-slate-400 mb-4"></i>
                                <p class="text-slate-500 dark:text-navy-400">
                                    Aucune question disponible pour ce module.
                                </p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Boutons de navigation -->
                <div class="mt-8 flex justify-between items-center">
                    <div class="flex space-x-3">
                        @if($isLastModule)
                            <button type="submit" formaction="{{ route('diagnosticentreprisequalification.store', ['entrepriseId' => $entrepriseId, 'moduleId' => $currentModule->id]) }}" 
                                    class="btn bg-gradient-to-r from-success to-success/80 text-white hover:from-success/90 hover:to-success/70 px-6 py-3 rounded-lg shadow-lg hover:shadow-xl transition-all duration-200 transform hover:scale-105">
                                <i class="fas fa-check mr-2"></i>
                                Terminer
                            </button>
                        @else
                            <button type="submit" formaction="{{ route('diagnosticentreprisequalification.saveModule', ['entrepriseId' => $entrepriseId, 'moduleId' => $currentModule->id]) }}" 
                                    class="btn bg-gradient-to-r from-info to-info/80 text-white hover:from-info/90 hover:to-info/70 px-6 py-3 rounded-lg shadow-lg hover:shadow-xl transition-all duration-200 transform hover:scale-105">
                                <i class="fas fa-save mr-2"></i>
                                Enregistrer
                            </button>
                        @endif
                    </div>
                    
                    <div class="flex space-x-3">
                        @if($previousModule)
                            <a href="{{ route('diagnosticentreprisequalification.showModule', [$entrepriseId, $previousModule->id]) }}" 
                               class="btn bg-gradient-to-r from-secondary to-secondary/80 text-white hover:from-secondary/90 hover:to-secondary/70 px-6 py-3 rounded-lg shadow-lg hover:shadow-xl transition-all duration-200 transform hover:scale-105">
                                <i class="fas fa-arrow-left mr-2"></i>
                                Précédent
                            </a>
                        @endif
                        
                        @if($nextModule)
                            <a href="{{ route('diagnosticentreprisequalification.showModule', [$entrepriseId, $nextModule->id]) }}" 
                               class="btn bg-gradient-to-r from-primary to-primary/80 text-white hover:from-primary/90 hover:to-primary/70 px-6 py-3 rounded-lg shadow-lg hover:shadow-xl transition-all duration-200 transform hover:scale-105">
                                <i class="fas fa-arrow-right mr-2"></i>
                                Suivant
                            </a>
                        @endif
                    </div>
                </div>
            </form>
        @else
            <div class="card">
                <div class="card-body text-center py-8">
                    <i class="fas fa-clipboard-check text-4xl text-slate-400 mb-4"></i>
                    <h3 class="text-lg font-medium text-slate-700 dark:text-navy-200 mb-2">
                        Aucun module disponible
                    </h3>
                    <p class="text-slate-600 dark:text-navy-400">
                        Les tests de qualification ne sont pas encore disponibles pour le moment.
                    </p>
                </div>
            </div>
        @endif

            </div>

            <div class="col-span-12 py-6 lg:sticky lg:bottom-0 lg:col-span-4 lg:self-end">
                @include('layouts.sidebar')
            </div>    

        </div>
      </main>
</x-app-layout>
