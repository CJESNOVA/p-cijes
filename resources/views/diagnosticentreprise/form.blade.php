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
                    @if($currentModule)
                        <div class="mt-2 flex items-center gap-2">
                            <span class="text-sm text-slate-500">Module:</span>
                            <span class="px-2 py-1 bg-orange-500/10 text-orange-600 rounded-full text-sm font-medium">
                                {{ $currentModule->titre }}
                            </span>
                            <span class="text-sm text-slate-500">
                                @php
                                    $allModules = $modules;
                                    $currentIndex = $allModules->search(function($module) use ($currentModule) {
                                        return $module->id == $currentModule->id;
                                    });
                                    echo ($currentIndex + 1) . '/' . $allModules->count();
                                @endphp
                            </span>
                        </div>
                    @endif
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

                @if(session('showFinalization'))
                    <div class="alert flex rounded-lg bg-orange-500 px-4 py-3 text-white mb-4 shadow-lg">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Tous les modules sont complétés ! Vous pouvez maintenant finaliser votre diagnostic.
                    </div>
                @endif

        <h2 class="text-xl font-medium text-slate-800 lg:text-2xl mb-6">
            Diagnostic – {{ $entreprise->nom ?? 'Nouvelle entreprise' }}
        </h2>

        @if($currentModule)
            <!-- Indicateur de progression -->
            <div class="mb-6">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm font-medium text-slate-700 dark:text-navy-300">
                        Progression du diagnostic
                    </span>
                    <span class="text-sm font-medium text-orange-500">
                        {{ $modules->search(function($module) use ($currentModule) { return $module->id == $currentModule->id; }) + 1 }} / {{ $modules->count() }}
                    </span>
                </div>
                <div class="w-full bg-slate-200 dark:bg-navy-700 rounded-full h-2">
                    <div class="bg-gradient-to-r from-orange-500 to-orange-600/80 h-2 rounded-full transition-all duration-300" 
                         style="width: {{ (($modules->search(function($module) use ($currentModule) { return $module->id == $currentModule->id; }) + 1) / $modules->count()) * 100 }}%">
                    </div>
                </div>
            </div>

            <form action="{{ $isLastModule ? route('diagnosticentreprise.finalize', [$entrepriseId, $currentModule->id]) : route('diagnosticentreprise.saveModule', [$entrepriseId, $currentModule->id]) }}" method="POST">
                @csrf
    
@php
    $existing = $existing ?? collect();
@endphp

                <!-- Input Validation -->
                <div class="card px-4 pb-4 sm:px-5">
                    <div class="max-w-xxl">
                        <div class="mt-5">
                            <input type="hidden" name="entreprise_id" value="{{ $entrepriseId }}">

                            <div class="mb-8 border-b pb-4">
                                <h2 class="text-xl font-bold">{{ $currentModule->titre }}</h2>
                                <p class="text-slate-500">{!! $currentModule->description !!}</p>

                                @if($currentModule->vignette)
                                    <div class="mt-4">
                                        <img src="{{ env('SUPABASE_BUCKET_URL') . '/' . $currentModule->vignette }}" 
                                             alt="Vignette du module {{ $currentModule->titre }}" 
                                             class="w-full h-auto rounded-lg shadow-lg">
                                    </div>
                                @endif

                                @foreach($currentModule->diagnosticquestions as $diagnosticquestion)
                                    <div class="mt-6">
                                        <label class="block text-lg font-medium">
                                            {{ $diagnosticquestion->position }} - {{ $diagnosticquestion->titre }}
                                            @if($diagnosticquestion->obligatoire) <span class="text-red-500">*</span> @endif
                                        </label>

                                        @php
                                            $type = $diagnosticquestion->diagnosticquestiontype_id;
                                            $inputName = $type == 2 ? "reponses[{$diagnosticquestion->id}][]" : "reponses[{$diagnosticquestion->id}]";
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

                            <!-- Barre de navigation par numéros -->
                            <div class="mt-8 mb-6">
                                <div class="flex items-center justify-center space-x-2 flex-wrap">
                                    @php
                                        $currentModuleIndex = $modules->search(function($module) use ($currentModule) { return $module->id == $currentModule->id; }) + 1;
                                    @endphp
                                    
                                    @foreach($modules as $index => $module)
                                        @php
                                            $moduleNumber = $index + 1;
                                            $isCurrentModule = $module->id == $currentModule->id;
                                            // Vérifier si le module est complété en cherchant dans les réponses existantes
                                            $isCompleted = isset($existing) && !empty($existing) && 
                                                         collect($existing)->filter(function($responses) use ($module) {
                                                             return collect($responses)->keys()->first(function($questionId) use ($module) {
                                                                 $question = \App\Models\Diagnosticquestion::find($questionId);
                                                                 return $question && $question->diagnosticmodule_id == $module->id;
                                                             });
                                                         })->isNotEmpty();
                                        @endphp
                                        
                                        @if($isCurrentModule)
                                            <a href="{{ route('diagnosticentreprise.showModule', [$entrepriseId, $module->id]) }}" 
                                               class="flex items-center justify-center w-10 h-10 rounded-lg bg-gradient-to-r from-orange-500 to-orange-600/80 text-white font-semibold shadow-lg transform scale-110 transition-all duration-200"
                                               title="Module actuel: {{ $module->titre }}">
                                                {{ $moduleNumber }}
                                            </a>
                                        @elseif($isCompleted)
                                            <a href="{{ route('diagnosticentreprise.showModule', [$entrepriseId, $module->id]) }}" 
                                               class="flex items-center justify-center w-10 h-10 rounded-lg bg-gradient-to-r from-green-500 to-green-500/80 text-white font-medium shadow-md hover:from-green-600 hover:to-green-600/80 transition-all duration-200"
                                               title="Module complété: {{ $module->titre }}">
                                                <i class="fas fa-check text-xs"></i>
                                            </a>
                                        @else
                                            <a href="{{ route('diagnosticentreprise.showModule', [$entrepriseId, $module->id]) }}" 
                                               class="flex items-center justify-center w-10 h-10 rounded-lg bg-white dark:bg-navy-700 border-2 border-slate-300 dark:border-navy-600 text-slate-600 dark:text-navy-300 font-medium hover:border-orange-500 hover:text-orange-500 hover:bg-orange-500/5 transition-all duration-200"
                                               title="Module {{ $moduleNumber }}: {{ $module->titre }}">
                                                {{ $moduleNumber }}
                                            </a>
                                        @endif
                                    @endforeach
                                </div>
                                <div class="text-center mt-3">
                                    <span class="text-sm text-slate-500 dark:text-navy-400">
                                        Module {{ $currentModuleIndex }} sur {{ $modules->count() }}
                                    </span>
                                </div>
                            </div>

                            <!-- Boutons de navigation -->
                            <div class="mt-8 flex justify-between items-center">
                                <div class="flex space-x-3">
                                    @if($isLastModule)
                                        @if(session('showFinalization'))
                                            <button type="submit" 
                                                    class="btn bg-gradient-to-r from-orange-500 to-orange-600/80 text-white hover:from-orange-600 hover:to-orange-700/70 px-6 py-3 rounded-lg shadow-lg hover:shadow-xl transition-all duration-200 transform hover:scale-105">
                                                <i class="fas fa-check-circle mr-2"></i>
                                                Finaliser le diagnostic
                                            </button>
                                        @endif
                                    @else
                                        <button type="submit" 
                                                class="btn bg-gradient-to-r from-[#152737] to-[#152737]/80 text-white hover:from-[#152737]/90 hover:to-[#152737]/70 px-6 py-3 rounded-lg shadow-lg hover:shadow-xl transition-all duration-200 transform hover:scale-105">
                                            <i class="fas fa-save mr-2"></i>
                                            Enregistrer et continuer
                                        </button>
                                    @endif
                                </div>
                                
                                <div class="flex space-x-3">
                                    @if($previousModule)
                                        <a href="{{ route('diagnosticentreprise.showModule', [$entrepriseId, $previousModule->id]) }}" 
                                           class="btn bg-gradient-to-r from-slate-500 to-slate-500/80 text-white hover:from-slate-600 hover:to-slate-600/70 px-6 py-3 rounded-lg shadow-lg hover:shadow-xl transition-all duration-200 transform hover:scale-105">
                                            <i class="fas fa-arrow-left mr-2"></i>
                                            Module précédent
                                        </a>
                                    @endif
                                    
                                    @if($nextModule)
                                        <a href="{{ route('diagnosticentreprise.showModule', [$entrepriseId, $nextModule->id]) }}" 
                                           class="btn bg-gradient-to-r from-slate-400 to-slate-400/80 text-white hover:from-slate-500 hover:to-slate-500/70 px-6 py-3 rounded-lg shadow-lg hover:shadow-xl transition-all duration-200 transform hover:scale-105">
                                            <i class="fas fa-arrow-right mr-2"></i>
                                            Module suivant
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>      

            </form>
        @else
            <div class="card px-4 pb-4 sm:px-5">
                <div class="text-center py-8">
                    <div class="text-slate-500 mb-4">
                        <svg class="w-16 h-16 mx-auto mb-4 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        <h3 class="text-xl font-semibold mb-2">Aucun module disponible</h3>
                        <p>Il n'y a actuellement aucun module de diagnostic disponible pour ce profil d'entreprise.</p>
                    </div>
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