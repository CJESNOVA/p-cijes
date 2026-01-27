<x-app-layout title="Plans d'accompagnement" is-sidebar-open="true" is-header-blur="true">
    <main class="main-content w-full px-[var(--margin-x)] pb-8">
        <!-- Header moderne -->
        <div class="mb-2">
            <div class="flex items-center gap-4 mb-2">
                <div class="h-14 w-14 rounded-xl bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center shadow-lg">
                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                    </svg>
                </div>
                <div>
                    <h1 class="text-3xl font-bold text-slate-800 dark:text-navy-50">
                        Plans d'accompagnement
                    </h1>
                    <p class="mt-2 text-slate-600 dark:text-navy-200 text-lg">
                        Suivez votre progression et vos objectifs personnalisés
                    </p>
                </div>
            </div>
        </div>
        <div class="grid grid-cols-12 lg:gap-6">
            <div class="col-span-12 pt-6 lg:col-span-8 lg:pb-6">

          <!-- En-tête diagnostic -->
          <div class="card px-4 pb-4 sm:px-5 mb-6">
            <div class="max-w-xxl">
              <div class="flex items-center justify-between">
                <div>
                  <h3 class="text-lg font-semibold text-slate-800">Diagnostic PME</h3>
                  <p class="text-sm text-slate-500 mt-1">
                    Score : <span class="font-bold text-primary">{{ $diagnostic->scoreglobal ?? 0 }}</span> | 
                    Créé le : {{ $diagnostic->created_at->format('d/m/Y') }}
                  </p>
                </div>
              </div>
            </div>
          </div>

          <!-- Liste des plans -->
          <div class="card px-4 pb-4 sm:px-5">
            <div class="max-w-xxl">
              <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-semibold text-slate-800">
                  <i class="fas fa-tasks mr-2 text-success"></i>
                  Plans d'accompagnement 
                  @if($diagnostic->accompagnement && $diagnostic->accompagnement->plans->count() > 0)
                    <span class="badge badge-primary badge-sm ml-2">{{ $diagnostic->accompagnement->plans->count() }}</span>
                  @endif
                </h3>
              </div>


              @if($diagnostic->accompagnement && isset($diagnostic->accompagnement->plans) && $diagnostic->accompagnement->plans->count() > 0)
                <div class="space-y-4">
                  @foreach($diagnostic->accompagnement->plans as $plan)
                    <div class="card border-l-4 border-l-primary bg-slate-50 hover:bg-slate-100 transition-colors">
                      <div class="p-4">
                        <div class="flex justify-between items-start">
                          <div class="flex-1">
                            <h4 class="font-semibold text-slate-800 mb-2">
                              {{ $loop->iteration }}. {{ $plan->objectif }}
                            </h4>
                            <p class="text-slate-600 mb-3">
                              <i class="fas fa-bullseye mr-2 text-warning"></i>
                              {{ $plan->actionprioritaire }}
                            </p>
                            <div class="flex items-center text-sm text-slate-500">
                              <i class="fas fa-calendar-alt mr-2 text-info"></i>
                              {{ \Carbon\Carbon::parse($plan->dateplan)->format('d/m/Y') }}
                              @if($plan->dateplan < now())
                                <span class="badge badge-danger badge-sm ml-2">En retard</span>
                              @elseif($plan->dateplan <= now()->addDays(7))
                                <span class="badge badge-warning badge-sm ml-2">Bientôt</span>
                              @else
                                <span class="badge badge-success badge-sm ml-2">À venir</span>
                              @endif
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  @endforeach
                </div>
              @else
                <div class="text-center py-12">
                  <i class="fas fa-clipboard-list text-6xl text-slate-300 mb-4"></i>
                  <h4 class="text-xl font-semibold text-slate-600 mb-2">Aucun plan d'accompagnement</h4>
                  <p class="text-slate-500 mb-6">
                    Commencez par créer des plans d'accompagnement personnalisés basés sur les résultats de votre diagnostic.
                  </p>
                </div>
              @endif
            </div>
          </div>

          <!-- Scores par module -->
          @if($diagnostic->diagnosticmodulescores->count() > 0)
            <div class="card px-4 pb-4 sm:px-5 mt-6">
              <div class="max-w-xxl">
                <h3 class="text-lg font-semibold text-slate-800 mb-6">
                  <i class="fas fa-chart-bar mr-2 text-purple-500"></i>
                  Scores par module
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                  @foreach($diagnostic->diagnosticmodulescores as $score)
                    <div class="card bg-gradient-to-r from-blue-50 to-purple-50 border border-blue-200">
                      <div class="p-4">
                        <h4 class="font-semibold text-slate-800 mb-2 text-sm">
                          {{ $score->diagnosticmodule->titre ?? 'Module ' . $score->diagnosticmodule_id }}
                        </h4>
                        <div class="flex justify-between items-center mb-2">
                          <span class="text-xl font-bold 
                            {{ $score->niveau === 'D' ? 'text-success' : 
                               ($score->niveau === 'C' ? 'text-info' : 
                               ($score->niveau === 'B' ? 'text-warning' : 'text-danger')) }}">
                            Niveau {{ $score->niveau }}
                          </span>
                          <span class="text-sm text-slate-600">
                            {{ $score->score_pourcentage }}%
                          </span>
                        </div>
                        <div class="w-full bg-slate-200 rounded-full h-2">
                          <div class="bg-gradient-to-r from-blue-500 to-purple-500 h-2 rounded-full" 
                               style="width: {{ $score->score_pourcentage }}%"></div>
                        </div>
                      </div>
                    </div>
                  @endforeach
                </div>
              </div>
            </div>
          @endif

          <!-- Expertise recommandée -->
          @if($diagnostic->diagnosticmodulescores->count() > 0)
            <div class="card px-4 pb-4 sm:px-5 mt-6">
              <div class="max-w-xxl">
                <h3 class="text-lg font-semibold text-slate-800 mb-6">
                  <i class="fas fa-user-tie mr-2 text-indigo-500"></i>
                  Expertise recommandée
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                  <div class="card bg-indigo-50 border border-indigo-200">
                    <div class="p-4">
                      <h4 class="font-semibold text-slate-800 mb-3">
                        <i class="fas fa-lightbulb mr-2 text-warning"></i>
                        Domaines d'amélioration
                      </h4>
                      <ul class="text-sm text-slate-600 space-y-2">
                        @php
                          $domainesAmelioration = $diagnostic->diagnosticmodulescores->filter(function($score) {
                            return in_array($score->niveau, ['A', 'B']);
                          });
                        @endphp
                        @foreach($domainesAmelioration as $score)
                          <li class="flex items-center">
                            <i class="fas fa-arrow-right mr-2 text-indigo-500"></i>
                            {{ $score->diagnosticmodule->titre ?? 'Module ' . $score->diagnosticmodule_id }}
                          </li>
                        @endforeach
                        @if($domainesAmelioration->count() === 0)
                          <li class="text-slate-400 italic">Aucun domaine identifié</li>
                        @endif
                      </ul>
                    </div>
                  </div>
                  
                  <div class="card bg-green-50 border border-green-200">
                    <div class="p-4">
                      <h4 class="font-semibold text-slate-800 mb-3">
                        <i class="fas fa-star mr-2 text-success"></i>
                        Points forts
                      </h4>
                      <ul class="text-sm text-slate-600 space-y-2">
                        @php
                          $pointsForts = $diagnostic->diagnosticmodulescores->filter(function($score) {
                            return in_array($score->niveau, ['C', 'D']);
                          });
                        @endphp
                        @foreach($pointsForts as $score)
                          <li class="flex items-center">
                            <i class="fas fa-check mr-2 text-success"></i>
                            {{ $score->diagnosticmodule->titre ?? 'Module ' . $score->diagnosticmodule_id }}
                          </li>
                        @endforeach
                        @if($pointsForts->count() === 0)
                          <li class="text-slate-400 italic">Aucun point fort identifié</li>
                        @endif
                      </ul>
                    </div>
                  </div>
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


<script>
function showAddPlanModal() {
    console.log('showAddPlanModal called');
    document.getElementById('addPlanModal').classList.remove('hidden');
}

function hideAddPlanModal() {
    console.log('hideAddPlanModal called');
    document.getElementById('addPlanModal').classList.add('hidden');
    document.getElementById('addPlanForm').reset();
}

// Gérer la soumission du formulaire
document.getElementById('addPlanForm').addEventListener('submit', function(e) {
    console.log('Form submit triggered');
    e.preventDefault();
    
    const formData = new FormData(this);
    formData.append('accompagnement_id', '{{ $diagnostic->accompagnement->id ?? 0 }}');
    
    console.log('FormData:', Object.fromEntries(formData));
    console.log('Route:', '{{ route("plans.store") }}');
    
    fetch('{{ route("plans.store") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
        },
        body: formData
    })
    .then(response => {
        console.log('Response status:', response.status);
        return response.json();
    })
    .then(data => {
        console.log('Response data:', data);
        if (data.success) {
            hideAddPlanModal();
            location.reload();
        } else {
            alert('Erreur lors de l\'ajout du plan: ' + (data.message || 'Erreur inconnue'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Erreur lors de l\'ajout du plan: ' + error.message);
    });
});
</script>
</x-app-layout>
