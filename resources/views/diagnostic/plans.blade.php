<x-app-layout title="Plans d'accompagnement" is-sidebar-open="true" is-header-blur="true">
    <main class="main-content w-full px-[var(--margin-x)] pb-8">
        <!-- Header moderne -->
        <div class="mb-2">
            <div class="flex items-center gap-4 mb-2">
                <div class="h-14 w-14 rounded-xl bg-gradient-to-br from-[#4FBE96] to-[#4FBE96]/80 flex items-center justify-center shadow-lg">
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
                  <h3 class="text-lg font-semibold text-slate-800">Diagnostic </h3>
                  <p class="text-sm text-slate-500 mt-1">
                    Score : <span class="font-bold text-primary">{{ $diagnostic->scoreglobal ?? 0 }}</span> | 
                    Créé le : {{ $diagnostic->created_at->format('d/m/Y') }}
                  </p>
                </div>
              </div>
            </div>
          </div>

          

          <!-- Barre de recherche et filtres -->
          <div class="card px-4 pb-4 sm:px-5 mb-6">
            <div class="max-w-xxl">
              <div class="flex flex-col lg:flex-row gap-4 mb-6">
                <!-- Recherche -->
                <div class="flex-1">
                  <div class="relative">
                    <input type="text" 
                           id="searchPlans" 
                           placeholder="Rechercher un plan..." 
                           class="w-full pl-10 pr-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-[#4FBE96] focus:border-transparent">
                    <i class="fas fa-search absolute left-3 top-3 text-slate-400"></i>
                  </div>
                </div>
                
                <!-- Filtres -->
                <div class="flex gap-2">
                  <select id="filterModule" class="px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-[#4FBE96]">
                    <option value="">Tous les modules</option>
                    @php
                      $modulesList = [];
                      // Charger les modules depuis les plans
                      if($diagnostic->accompagnement && $diagnostic->accompagnement->plans) {
                        foreach($diagnostic->accompagnement->plans as $plan) {
                          if($plan->diagnosticmodule) {
                            $modulesList[$plan->diagnosticmodule->id] = $plan->diagnosticmodule->titre;
                          }
                        }
                      }
                      // Charger aussi les modules depuis les résultats du diagnostic
                      if($diagnostic->diagnosticresultats) {
                        foreach($diagnostic->diagnosticresultats as $result) {
                          if($result->diagnosticquestion && $result->diagnosticquestion->diagnosticmodule) {
                            $module = $result->diagnosticquestion->diagnosticmodule;
                            $modulesList[$module->id] = $module->titre;
                          }
                        }
                      }
                      $modulesList = array_unique($modulesList);
                      // Trier par nom de module
                      asort($modulesList);
                    @endphp
                    @foreach($modulesList as $moduleId => $moduleTitle)
                      <option value="{{ $moduleId }}">{{ $moduleTitle }}</option>
                    @endforeach
                  </select>
                  
                  <select id="filterStatus" class="px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-[#4FBE96]">
                    <option value="">Tous les statuts</option>
                    <option value="late">En retard</option>
                    <option value="soon">Bientôt</option>
                    <option value="upcoming">À venir</option>
                  </select>
                  
                  <select id="sortBy" class="px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-[#4FBE96]">
                    <option value="date">Date d'échéance</option>
                    <option value="module">Module</option>
                    <option value="objective">Objectif</option>
                  </select>
                </div>
              </div>
              
              <!-- Statistiques -->
              <div class="flex flex-wrap gap-4 text-sm">
                <div class="flex items-center">
                  <span class="w-3 h-3 bg-red-500 rounded-full mr-2"></span>
                  <span class="text-slate-600">En retard: <span id="countLate" class="font-bold">0</span></span>
                </div>
                <div class="flex items-center">
                  <span class="w-3 h-3 bg-yellow-500 rounded-full mr-2"></span>
                  <span class="text-slate-600">Bientôt: <span id="countSoon" class="font-bold">0</span></span>
                </div>
                <div class="flex items-center">
                  <span class="w-3 h-3 bg-green-500 rounded-full mr-2"></span>
                  <span class="text-slate-600">À venir: <span id="countUpcoming" class="font-bold">0</span></span>
                </div>
                <div class="flex items-center">
                  <span class="text-slate-600">Total: <span id="countTotal" class="font-bold">{{ $diagnostic->accompagnement ? $diagnostic->accompagnement->plans->count() : 0 }}</span></span>
                </div>
              </div>
            </div>
          </div>

          <!-- Liste des plans -->
          <div id="tousLesPlans" class="space-y-4">
            <div class="max-w-xxl">
              <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-semibold text-slate-800">
                  <i class="fas fa-tasks mr-2 text-[#4FBE96]"></i>
                  Plans d'accompagnement 
                  <span class="badge badge-sm ml-2" style="background-color: #4FBE96; color: white;" id="visibleCount">{{ $diagnostic->accompagnement ? $diagnostic->accompagnement->plans->count() : 0 }}</span>
                </h3>
                <div class="flex space-x-2">
                  <button onclick="afficherTousLesPlans()" class="px-3 py-1 bg-[#4FBE96] text-white rounded text-sm hover:bg-[#4FBE96]/90 transition-colors" id="btnTousPlans">
                    <i class="fas fa-list mr-1"></i>Tous les plans
                  </button>
                  <button onclick="afficherPlansParModule()" class="px-3 py-1 bg-[#4FBE96] text-white rounded text-sm hover:bg-[#4FBE96]/90 transition-colors" id="btnPlansModule">
                    <i class="fas fa-cube mr-1"></i>Par module
                  </button>
                </div>
              </div>

              @if($diagnostic->accompagnement && isset($diagnostic->accompagnement->plans) && $diagnostic->accompagnement->plans->count() > 0)
                <!-- Plans paginés -->
                <div id="plansContainer" class="space-y-4">
                  @foreach($diagnostic->accompagnement->plans as $plan)
                    <div class="plan-item card border-l-4 border-l-[#4FBE96] bg-slate-50 hover:bg-slate-100 transition-colors" 
                         data-module="{{ $plan->diagnosticmodule_id ?? '' }}"
                         data-status="{{ \Carbon\Carbon::parse($plan->dateplan) < now() ? 'late' : (\Carbon\Carbon::parse($plan->dateplan) <= now()->addDays(7) ? 'soon' : 'upcoming') }}"
                         data-date="{{ \Carbon\Carbon::parse($plan->dateplan)->format('Y-m-d') }}"
                         data-objective="{{ Str::lower($plan->objectif) }}">
                      <div class="p-4">
                        <div class="flex justify-between items-start">
                          <div class="flex-1">
                            <div class="flex items-center space-x-2 mb-2">
                              @if($plan->diagnosticmodule)
                                <span class="text-xs bg-[#4FBE96]/20 text-[#4FBE96] px-2 py-1 rounded">
                                  <i class="fas fa-cube mr-1"></i>{{ $plan->diagnosticmodule->titre }}
                                </span>
                              @endif
                              @if($plan->diagnosticquestion)
                                <span class="text-xs bg-[#4FBE96]/20 text-[#4FBE96] px-2 py-1 rounded">
                                  <i class="fas fa-question-circle mr-1"></i>Q{{ $plan->diagnosticquestion->position }}
                                </span>
                              @endif
                            </div>
                            <h4 class="font-semibold text-slate-800 mb-2">
                              {{ $loop->iteration }}. {{ $plan->objectif }}
                            </h4>
                            <p class="text-slate-600 mb-3">
                              <i class="fas fa-bullseye mr-2 text-[#4FBE96]"></i>
                              {!! $plan->actionprioritaire !!}
                            </p>
                            <div class="flex items-center text-sm text-slate-500">
                              <i class="fas fa-calendar-alt mr-2 text-[#4FBE96]"></i>
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
                          <div class="ml-4 text-right">
                            <div class="text-xs text-slate-500">
                              @if($plan->diagnosticquestion)
                                Plan pour la question {{ $plan->diagnosticquestion->position }}
                              @else
                                Plan pour le module
                              @endif
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  @endforeach
                </div>
                
                <!-- Pagination -->
                <div id="pagination" class="flex justify-center items-center space-x-2 mt-6">
                  <button id="prevPage" class="px-3 py-1 bg-slate-200 text-slate-600 rounded hover:bg-slate-300 disabled:opacity-50" disabled>
                    <i class="fas fa-chevron-left"></i>
                  </button>
                  <span id="pageInfo" class="text-sm text-slate-600">Page 1 sur 1</span>
                  <button id="nextPage" class="px-3 py-1 bg-slate-200 text-slate-600 rounded hover:bg-slate-300 disabled:opacity-50" disabled>
                    <i class="fas fa-chevron-right"></i>
                  </button>
                </div>
                
                <!-- Message si aucun résultat -->
                <div id="noResults" class="hidden text-center py-12">
                  <i class="fas fa-search text-6xl text-slate-300 mb-4"></i>
                  <h4 class="text-xl font-semibold text-slate-600 mb-2">Aucun plan trouvé</h4>
                  <p class="text-slate-500">Essayez de modifier vos critères de recherche</p>
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

          <!-- Plans regroupés par module (caché par défaut) -->
          @php
            $plansParModule = [];
            if($diagnostic->accompagnement && $diagnostic->accompagnement->plans) {
              foreach($diagnostic->accompagnement->plans as $plan) {
                  // Ne garder que les plans associés à un module (diagnosticmodule_id non null)
                  if($plan->diagnosticmodule_id) {
                      $moduleId = $plan->diagnosticmodule_id;
                      if (!isset($plansParModule[$moduleId])) {
                          $plansParModule[$moduleId] = [];
                      }
                      $plansParModule[$moduleId][] = $plan;
                  }
              }
            }
          @endphp
          
          @isset($plansParModule)
          <div id="plansParModule" class="space-y-6 hidden">
            <!-- Bouton de retour -->
            <div class="flex items-center justify-between mb-6">
              <button onclick="afficherTousLesPlans()" class="flex items-center px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors duration-200">
                <i class="fas fa-arrow-left mr-2"></i>
                Retour à tous les plans
              </button>
              <div class="text-sm text-gray-600">
                <i class="fas fa-cube mr-2 text-[#4FBE96]"></i>
                {{ count($plansParModule) }} module{{ count($plansParModule) > 1 ? 's' : '' }}
              </div>
            </div>
            
            @if(count($plansParModule) > 0)
            @foreach($plansParModule as $moduleId => $plans)
              <div class="bg-gradient-to-r from-[#4FBE96]/10 to-[#4FBE96]/5 border border-[#4FBE96]/30 rounded-lg shadow-sm hover:shadow-md transition-shadow duration-200">
                <!-- Header du module cliquable -->
                <div class="cursor-pointer flex items-center justify-between p-6 rounded-lg hover:bg-[#4FBE96]/10 transition-colors duration-200" 
                     onclick="toggleModulePlans('module-{{ $moduleId }}')">
                  <h4 class="text-lg font-semibold text-[#4FBE96] flex items-center">
                    <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-[#4FBE96] to-[#4FBE96]/80 flex items-center justify-center mr-4">
                      <i class="fas fa-cube text-white"></i>
                    </div>
                    {{ $plans[0]->diagnosticmodule->titre }}
                  </h4>
                  <div class="flex items-center space-x-3">
                    <span class="text-sm font-medium text-[#4FBE96] bg-[#4FBE96]/20 px-3 py-2 rounded-full">
                      {{ count($plans) }} plan{{ count($plans) > 1 ? 's' : '' }}
                    </span>
                    <svg id="chevron-module-{{ $moduleId }}" class="w-6 h-6 text-[#4FBE96] transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                  </div>
                </div>
                
                <!-- Contenu du module (collapsible) -->
                <div id="module-{{ $moduleId }}" class="px-6 pb-6">
                  <div class="space-y-4">
                    @foreach($plans as $plan)
                      <div class="bg-white rounded-lg border border-[#4FBE96]/30 p-4 shadow-sm hover:shadow transition-shadow duration-200">
                        <div class="flex justify-between items-start">
                          <div class="flex-1">
                            <div class="flex items-center space-x-3 mb-3">
                              @if($plan->diagnosticquestion)
                                <span class="text-xs bg-[#4FBE96]/20 text-[#4FBE96] px-3 py-1 rounded-full font-medium">
                                  <i class="fas fa-question-circle mr-1"></i>Q{{ $plan->diagnosticquestion->position }}
                                </span>
                              @endif
                              <span class="text-sm font-medium text-[#4FBE96] bg-[#4FBE96]/10 px-2 py-1 rounded">
                                #{{ $loop->index + 1 }}
                              </span>
                            </div>
                            <h5 class="font-semibold text-slate-800 mb-3 text-lg">
                              {{ $plan->objectif }}
                            </h5>
                            <p class="text-slate-600 mb-3">
                              <i class="fas fa-bullseye mr-2 text-[#4FBE96]"></i>
                              {!! $plan->actionprioritaire !!}
                            </p>
                            <div class="flex items-center text-sm space-x-4">
                              <div class="flex items-center">
                                <i class="fas fa-calendar-alt mr-2 text-[#4FBE96]"></i>
                                <span class="text-slate-600 font-medium">{{ \Carbon\Carbon::parse($plan->dateplan)->format('d/m/Y') }}</span>
                              </div>
                              @if($plan->dateplan < now())
                                <span class="px-3 py-1 bg-red-100 text-red-800 text-xs rounded-full font-medium">En retard</span>
                              @elseif($plan->dateplan <= now()->addDays(7))
                                <span class="px-3 py-1 bg-yellow-100 text-yellow-800 text-xs rounded-full font-medium">Bientôt</span>
                              @else
                                <span class="px-3 py-1 bg-green-100 text-green-800 text-xs rounded-full font-medium">À venir</span>
                              @endif
                            </div>
                          </div>
                        </div>
                      </div>
                    @endforeach
                  </div>
                </div>
              </div>
            @endforeach
            @else
              <div class="text-center py-12">
                <i class="fas fa-cube text-6xl text-slate-300 mb-4"></i>
                <h4 class="text-xl font-semibold text-slate-600 mb-2">Aucun plan associé à un module</h4>
                <p class="text-slate-500">
                  Les plans existants ne sont pas liés à des modules spécifiques. 
                  Vous pouvez les consulter dans la vue "Tous les plans".
                </p>
              </div>
            @endif
          </div>
          @endif

<!-- Scores et Expertise par module -->
          @if($diagnostic->diagnosticresultats->count() > 0)
            <div class="card px-4 pb-4 sm:px-5 mb-6">
              <div class="max-w-xxl">
                <h3 class="text-xl font-semibold text-slate-800 mb-6">
                  <i class="fas fa-chart-line mr-2 text-[#4FBE96]"></i>
                  Scores et Expertise par module
                </h3>
                
                @php
                    // Calculer les scores totaux et niveaux par module
                    $scoresCalculés = [];
                    foreach($diagnostic->diagnosticresultats as $result) {
                        if($result->diagnosticquestion && $result->diagnosticreponse) {
                            $moduleId = $result->diagnosticquestion->diagnosticmodule_id;
                            $score = $result->diagnosticreponse->score ?? 0;
                            
                            if(!isset($scoresCalculés[$moduleId])) {
                                $scoresCalculés[$moduleId] = [
                                    'module' => $result->diagnosticquestion->diagnosticmodule,
                                    'score_total' => 0,
                                    'nombre_questions' => 0,
                                    'score_max_possible' => 0
                                ];
                            }
                            
                            $scoresCalculés[$moduleId]['score_total'] += $score;
                            $scoresCalculés[$moduleId]['nombre_questions']++;
                            
                            // Calculer le score max possible pour cette question (comme dans le contrôleur)
                            $pointsMax = $result->diagnosticquestion->diagnosticreponses()
                                ->max('score') ?? 0;
                            $scoresCalculés[$moduleId]['score_max_possible'] += $pointsMax;
                        }
                    }
                    
                    // Calculer les niveaux et recommandations pour chaque module
                    foreach($scoresCalculés as $moduleId => &$data) {
                        $pourcentage = $data['score_max_possible'] > 0 ? ($data['score_total'] / $data['score_max_possible']) * 100 : 0;
                        
                        // Calculer le niveau (A, B, C, D)
                        if($pourcentage >= 80) {
                            $niveau = 'D'; // Excellent
                            $niveauText = 'Excellent';
                            $niveauColor = 'text-green-600';
                            $bgColor = 'bg-green-100';
                            $barColor = 'from-green-500 to-green-600';
                            $expertiseText = 'Expert confirmé';
                            $recommandation = 'Maintenir le niveau d\'expertise';
                            $icon = 'fa-star';
                        } elseif($pourcentage >= 60) {
                            $niveau = 'C'; // Bon
                            $niveauText = 'Bon';
                            $niveauColor = 'text-blue-600';
                            $bgColor = 'bg-blue-100';
                            $barColor = 'from-blue-500 to-blue-600';
                            $expertiseText = 'Compétent';
                            $recommandation = 'Approfondir les connaissances';
                            $icon = 'fa-check-circle';
                        } elseif($pourcentage >= 40) {
                            $niveau = 'B'; // Moyen
                            $niveauText = 'Moyen';
                            $niveauColor = 'text-yellow-600';
                            $bgColor = 'bg-yellow-100';
                            $barColor = 'from-yellow-500 to-yellow-600';
                            $expertiseText = 'En développement';
                            $recommandation = 'Formation recommandée';
                            $icon = 'fa-exclamation-triangle';
                        } else {
                            $niveau = 'A'; // Faible
                            $niveauText = 'Faible';
                            $niveauColor = 'text-red-600';
                            $bgColor = 'bg-red-100';
                            $barColor = 'from-red-500 to-red-600';
                            $expertiseText = 'Besoin de formation';
                            $recommandation = 'Formation urgente requise';
                            $icon = 'fa-times-circle';
                        }
                        
                        $data['pourcentage'] = $pourcentage;
                        $data['niveau'] = $niveau;
                        $data['niveau_text'] = $niveauText;
                        $data['niveau_color'] = $niveauColor;
                        $data['bg_color'] = $bgColor;
                        $data['bar_color'] = $barColor;
                        $data['expertise_text'] = $expertiseText;
                        $data['recommandation'] = $recommandation;
                        $data['icon'] = $icon;
                    }
                    
                    // Trier par ID numérique du module
                    uasort($scoresCalculés, function($a, $b) {
                        return $a['module']->id - $b['module']->id;
                    });
                    $topModules = array_slice($scoresCalculés, 0, 10, true);
                @endphp
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                  @foreach($topModules as $moduleId => $data)
                    <div class="bg-white border border-slate-200 rounded-lg p-4 shadow-sm hover:shadow-md transition-all duration-200">
                        <!-- Header avec niveau -->
                        <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center">
                                <div class="w-10 h-10 rounded-lg bg-gradient-to-br {{ $data['bar_color'] }} flex items-center justify-center mr-3">
                                    <span class="text-white font-bold text-lg">{{ $data['niveau'] }}</span>
                                </div>
                                <div>
                                    <h4 class="font-semibold text-slate-800 text-sm">{{ $data['module']->titre }}</h4>
                                    <p class="text-xs text-slate-500">{{ $data['nombre_questions'] }} question(s)</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <div class="text-2xl font-bold {{ $data['niveau_color'] }}">{{ $data['score_total'] }}</div>
                                <div class="text-xs text-slate-500">/{{ $data['score_max_possible'] }} pts</div>
                            </div>
                        </div>
                        
                        <!-- Niveau et expertise -->
                        <div class="flex items-center justify-between mb-3">
                            <span class="px-3 py-1 {{ $data['bg_color'] }} {{ $data['niveau_color'] }} rounded-full text-xs font-semibold">
                                Niveau {{ $data['niveau'] }} - {{ $data['niveau_text'] }}
                            </span>
                            <span class="text-sm font-medium {{ $data['niveau_color'] }}">
                                {{ round($data['pourcentage'], 1) }}%
                            </span>
                        </div>
                        
                        <!-- Barre de progression -->
                        <div class="w-full bg-gray-200 rounded-full h-3 mb-3">
                            <div class="bg-gradient-to-r {{ $data['bar_color'] }} h-3 rounded-full transition-all duration-500"
                                 style="width: {{ min(100, $data['pourcentage']) }}%"></div>
                        </div>
                        
                        <!-- Expertise et recommandation -->
                        <div class="{{ $data['bg_color'] }} rounded-lg p-3 mb-3">
                            <div class="flex items-start">
                                <i class="fas {{ $data['icon'] }} {{ $data['niveau_color'] }} mr-2 mt-1"></i>
                                <div>
                                    <p class="text-sm font-medium {{ $data['niveau_color'] }} mb-1">
                                        {{ $data['expertise_text'] }} - {{ $data['recommandation'] }}
                                    </p>
                                    <p class="text-xs {{ $data['niveau_color'] }} opacity-75">
                                        @if($data['niveau'] === 'D')
                                            🏆 Performance excellente - Module maîtrisé. Continuez à maintenir vos compétences et explorez des sujets avancés.
                                        @elseif($data['niveau'] === 'C')
                                            ✅ Bon niveau de compréhension. Identifiez les points restants à améliorer pour atteindre l'excellence.
                                        @elseif($data['niveau'] === 'B')
                                            ⚠️ Niveau moyen - Amélioration possible. Une formation structurée pourrait vous aider à progresser rapidement.
                                        @else
                                            ❌ Niveau faible - Révision nécessaire. Une formation ciblée est recommandée pour construire des bases solides.
                                        @endif
                                    </p>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Actions recommandées -->
                        <div class="border-t pt-3">
                            <h5 class="text-xs font-semibold text-slate-600 mb-2">Actions suggérées :</h5>
                            <ul class="text-xs text-slate-600 space-y-1">
                                @if($data['niveau'] === 'D')
                                    <li class="flex items-center">
                                        <i class="fas fa-book-open text-green-500 mr-2"></i>
                                        Explorer des formations avancées
                                    </li>
                                    <li class="flex items-center">
                                        <i class="fas fa-users text-green-500 mr-2"></i>
                                        Partager votre expertise
                                    </li>
                                @elseif($data['niveau'] === 'C')
                                    <li class="flex items-center">
                                        <i class="fas fa-tasks text-blue-500 mr-2"></i>
                                        Pratiquer sur des cas complexes
                                    </li>
                                    <li class="flex items-center">
                                        <i class="fas fa-certificate text-blue-500 mr-2"></i>
                                        Obtenir une certification
                                    </li>
                                @elseif($data['niveau'] === 'B')
                                    <li class="flex items-center">
                                        <i class="fas fa-graduation-cap text-yellow-500 mr-2"></i>
                                        Suivre une formation structurée
                                    </li>
                                    <li class="flex items-center">
                                        <i class="fas fa-user-tie text-yellow-500 mr-2"></i>
                                        Travailler avec un mentor
                                    </li>
                                @else
                                    <li class="flex items-center">
                                        <i class="fas fa-play-circle text-red-500 mr-2"></i>
                                        Commencer par les bases fondamentales
                                    </li>
                                    <li class="flex items-center">
                                        <i class="fas fa-calendar text-red-500 mr-2"></i>
                                        Planifier un programme de formation
                                    </li>
                                @endif
                            </ul>
                        </div>
                    </div>
                  @endforeach
                </div>
              </div>
            </div>
          @endif


          </div>
          </div>
          </div>

            <div class="col-span-12 py-6 lg:sticky lg:bottom-0 lg:col-span-4 lg:self-end">
                @include('layouts.sidebar')
            </div>    

        </div>
      </main>


<script>
// Variables globales
let currentPage = 1;
const itemsPerPage = 10;
let allPlans = [];
let filteredPlans = [];

// Initialisation
document.addEventListener('DOMContentLoaded', function() {
    // Récupérer tous les plans
    allPlans = Array.from(document.querySelectorAll('.plan-item'));
    filteredPlans = [...allPlans];
    
    // Mettre à jour les statistiques
    updateStatistics();
    
    // Appliquer les filtres initiaux
    applyFilters();
    
    // Initialiser l'affichage par défaut
    afficherTousLesPlans();
    
    // Ajouter les écouteurs d'événements
    document.getElementById('searchPlans').addEventListener('input', applyFilters);
    document.getElementById('filterModule').addEventListener('change', applyFilters);
    document.getElementById('filterStatus').addEventListener('change', applyFilters);
    document.getElementById('sortBy').addEventListener('change', applyFilters);
    document.getElementById('prevPage').addEventListener('click', () => changePage(-1));
    document.getElementById('nextPage').addEventListener('click', () => changePage(1));
    
    // Masquer les boutons de vue s'il n'y a pas de plans
    if (allPlans.length === 0) {
        const btnTousPlans = document.getElementById('btnTousPlans');
        const btnPlansModule = document.getElementById('btnPlansModule');
        if (btnTousPlans) btnTousPlans.style.display = 'none';
        if (btnPlansModule) btnPlansModule.style.display = 'none';
    }
});

function updateStatistics() {
    const late = allPlans.filter(plan => plan.dataset.status === 'late').length;
    const soon = allPlans.filter(plan => plan.dataset.status === 'soon').length;
    const upcoming = allPlans.filter(plan => plan.dataset.status === 'upcoming').length;
    const total = allPlans.length;
    
    document.getElementById('countLate').textContent = late;
    document.getElementById('countSoon').textContent = soon;
    document.getElementById('countUpcoming').textContent = upcoming;
    document.getElementById('countTotal').textContent = total;
}

function applyFilters() {
    const searchTerm = document.getElementById('searchPlans').value.toLowerCase();
    const moduleFilter = document.getElementById('filterModule').value;
    const statusFilter = document.getElementById('filterStatus').value;
    const sortBy = document.getElementById('sortBy').value;
    
    // Filtrer les plans
    filteredPlans = allPlans.filter(plan => {
        const matchesSearch = !searchTerm || 
            plan.dataset.objective.includes(searchTerm) ||
            plan.textContent.toLowerCase().includes(searchTerm);
        
        const matchesModule = !moduleFilter || plan.dataset.module === moduleFilter;
        const matchesStatus = !statusFilter || plan.dataset.status === statusFilter;
        
        return matchesSearch && matchesModule && matchesStatus;
    });
    
    // Trier les plans
    switch(sortBy) {
        case 'date':
            filteredPlans.sort((a, b) => new Date(a.dataset.date) - new Date(b.dataset.date));
            break;
        case 'module':
            filteredPlans.sort((a, b) => {
                const moduleA = a.querySelector('.bg-\\[#4FBE96\\]\\/20')?.textContent || '';
                const moduleB = b.querySelector('.bg-\\[#4FBE96\\]\\/20')?.textContent || '';
                return moduleA.localeCompare(moduleB);
            });
            break;
        case 'objective':
            filteredPlans.sort((a, b) => a.dataset.objective.localeCompare(b.dataset.objective));
            break;
    }
    
    // Réinitialiser la pagination
    currentPage = 1;
    displayPlans();
}

function displayPlans() {
    const container = document.getElementById('plansContainer');
    const noResults = document.getElementById('noResults');
    const pagination = document.getElementById('pagination');
    
    if (filteredPlans.length === 0) {
        container.innerHTML = '';
        noResults.classList.remove('hidden');
        pagination.classList.add('hidden');
        document.getElementById('visibleCount').textContent = '0';
        return;
    }
    
    noResults.classList.add('hidden');
    pagination.classList.remove('hidden');
    
    // Calculer les indices pour la pagination
    const startIndex = (currentPage - 1) * itemsPerPage;
    const endIndex = Math.min(startIndex + itemsPerPage, filteredPlans.length);
    
    // Vider et remplir le conteneur
    container.innerHTML = '';
    for (let i = startIndex; i < endIndex; i++) {
        container.appendChild(filteredPlans[i].cloneNode(true));
    }
    
    // Mettre à jour la pagination
    updatePagination();
    
    // Mettre à jour le compteur
    document.getElementById('visibleCount').textContent = filteredPlans.length;
}

function updatePagination() {
    const totalPages = Math.ceil(filteredPlans.length / itemsPerPage);
    const pageInfo = document.getElementById('pageInfo');
    const prevBtn = document.getElementById('prevPage');
    const nextBtn = document.getElementById('nextPage');
    
    pageInfo.textContent = `Page ${currentPage} sur ${totalPages}`;
    
    prevBtn.disabled = currentPage === 1;
    nextBtn.disabled = currentPage === totalPages;
}

function changePage(direction) {
    const totalPages = Math.ceil(filteredPlans.length / itemsPerPage);
    const newPage = currentPage + direction;
    
    if (newPage >= 1 && newPage <= totalPages) {
        currentPage = newPage;
        displayPlans();
        
        // Scroll en haut de la liste
        document.getElementById('plansContainer').scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
}

function afficherTousLesPlans() {
    console.log('Affichage de tous les plans');
    document.getElementById('tousLesPlans').classList.remove('hidden');
    document.getElementById('plansParModule').classList.add('hidden');
    document.getElementById('btnTousPlans').classList.add('bg-[#4FBE96]', 'ring-2', 'ring-[#4FBE96]');
    document.getElementById('btnPlansModule').classList.remove('bg-gray-500', 'ring-2', 'ring-gray-500');
}

function afficherPlansParModule() {
    console.log('Affichage des plans par module');
    document.getElementById('tousLesPlans').classList.add('hidden');
    document.getElementById('plansParModule').classList.remove('hidden');
    document.getElementById('btnPlansModule').classList.add('bg-gray-500', 'ring-2', 'ring-gray-500');
    document.getElementById('btnTousPlans').classList.remove('bg-[#4FBE96]', 'ring-2', 'ring-[#4FBE96]');
    
    // Initialiser tous les modules comme fermés quand on affiche par module
    @php
      if(isset($plansParModule)) {
        foreach($plansParModule as $moduleId => $plans) {
    @endphp
          const module{{ $moduleId }} = document.getElementById('module-{{ $moduleId }}');
          const chevron{{ $moduleId }} = document.getElementById('chevron-module-{{ $moduleId }}');
          if (module{{ $moduleId }}) {
              module{{ $moduleId }}.style.display = 'none';
              chevron{{ $moduleId }}.style.transform = 'rotate(-90deg)';
          }
    @php
        }
      }
    @endphp
}

function toggleModulePlans(moduleId) {
    const content = document.getElementById(moduleId);
    const chevron = document.getElementById('chevron-module-' + moduleId);
    
    if (content.style.display === 'none') {
        content.style.display = 'block';
        chevron.style.transform = 'rotate(0deg)';
    } else {
        content.style.display = 'none';
        chevron.style.transform = 'rotate(-90deg)';
    }
}

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