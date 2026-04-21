<x-app-layout title="Profil Entreprise" is-sidebar-open="true" is-header-blur="true">
    <main class="main-content w-full px-[var(--margin-x)] pb-8">
        <!-- Header moderne -->
        <div class="mb-8">
            <div class="flex items-center gap-4 mb-4">
                <div class="h-16 w-16 rounded-xl bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center shadow-lg">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                </div>
                <div>
                    <h1 class="text-3xl font-bold text-slate-800 dark:text-navy-50">
                        Profil Entreprise
                    </h1>
                    <p class="mt-2 text-slate-600 dark:text-navy-200 text-lg">
                        Analyse détaillée de votre positionnement
                    </p>
                    <div class="mt-3 flex items-center gap-3">
                        <span class="text-sm text-slate-500">Entreprise:</span>
                        <span class="px-3 py-1 bg-blue-500/10 text-blue-600 rounded-full text-sm font-medium">
                            {{ $entreprise->nom }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-12 lg:gap-6">
            <!-- Colonne principale -->
            <div class="col-span-12 lg:col-span-8 space-y-6">
                
                <!-- Profil Actuel Détaillé -->
                <div class="bg-white dark:bg-navy-800 rounded-xl shadow-lg p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-xl font-semibold text-slate-800 dark:text-navy-50">
                            Profil Actuel
                        </h2>
                        <button onclick="window.print()" class="btn btn-sm bg-slate-100 text-slate-600 hover:bg-slate-200 dark:bg-navy-700 dark:text-navy-300">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                            </svg>
                            Imprimer
                        </button>
                    </div>
                    
                    @php
                        $profilId = $entreprise->entrepriseprofil_id ?? 1;
                        $profilLibelles = [1 => 'PÉPITE', 2 => 'ÉMERGENTE', 3 => 'ÉLITE'];
                        $profilColors = [
                            1 => 'from-yellow-400 to-orange-500', // PÉPITE
                            2 => 'from-blue-400 to-blue-600',   // ÉMERGENTE
                            3 => 'from-purple-400 to-purple-600' // ÉLITE
                        ];
                        $profilDescriptions = [
                            1 => 'Entreprise en phase de structuration, besoin de renforcer les fondations',
                            2 => 'Entreprise consolidée, prête pour la croissance',
                            3 => 'Entreprise performante, prête pour l\'accès aux marchés majeurs'
                        ];
                    @endphp
                    
                    <div class="flex items-center gap-6 mb-6">
                        <div class="flex-shrink-0">
                            <div class="w-24 h-24 rounded-full bg-gradient-to-br {{ $profilColors[$profilId] }} shadow-lg flex items-center justify-center">
                                @if($profilId == 1)
                                    <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"></path>
                                    </svg>
                                @elseif($profilId == 2)
                                    <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                    </svg>
                                @else
                                    <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path>
                                    </svg>
                                @endif
                            </div>
                        </div>
                        <div>
                            <h3 class="text-2xl font-bold text-slate-800 dark:text-navy-50">
                                {{ $profilLibelles[$profilId] }}
                            </h3>
                            <p class="text-slate-600 dark:text-navy-300 mt-1">
                                {{ $profilDescriptions[$profilId] }}
                            </p>
                            <div class="mt-3 flex items-center gap-2">
                                <span class="px-3 py-1 bg-blue-100 text-blue-600 rounded-full text-sm font-medium">
                                    Score: {{ $scoreGlobal ?? 0 }}/200
                                </span>
                                @if($dernierDiagnostic)
                                    <span class="px-3 py-1 bg-green-100 text-green-600 rounded-full text-sm font-medium">
                                        Depuis {{ $dernierDiagnostic->created_at->diffForHumans() }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Conditions du Profil -->
                    <div class="border-t border-slate-200 dark:border-navy-700 pt-6">
                        <h4 class="text-lg font-medium text-slate-800 dark:text-navy-50 mb-4">
                            Conditions du Profil
                        </h4>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @if($profilId == 1) <!-- PÉPITE -->
                                <div class="space-y-3">
                                    @foreach($reglesSuivantes as $regle)
                                        <div class="flex items-center p-3 rounded-lg {{ 
                                            ($regle->score_total_min && $scoreGlobal >= $regle->score_total_min) || 
                                            ($regle->min_blocs_score && $nbBlocCritiques >= $regle->min_blocs_score) ||
                                            ($regle->bloc_finance_min && $financeScore >= $regle->bloc_finance_min) ||
                                            ($regle->bloc_juridique_min && $juridiqueScore >= $regle->bloc_juridique_min) ||
                                            ($regle->duree_min_mois && $delaiMois >= $regle->duree_min_mois) 
                                            ? 'bg-green-50 border-green-200' : 'bg-slate-50 border-slate-200' }} border">
                                            <div class="w-5 h-5 rounded-full {{ 
                                                ($regle->score_total_min && $scoreGlobal >= $regle->score_total_min) || 
                                                ($regle->min_blocs_score && $nbBlocCritiques >= $regle->min_blocs_score) ||
                                                ($regle->bloc_finance_min && $financeScore >= $regle->bloc_finance_min) ||
                                                ($regle->bloc_juridique_min && $juridiqueScore >= $regle->bloc_juridique_min) ||
                                                ($regle->duree_min_mois && $delaiMois >= $regle->duree_min_mois) 
                                                ? 'bg-green-500' : 'bg-slate-300' }} flex items-center justify-center mr-3">
                                                <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                </svg>
                                            </div>
                                            <div>
                                                <div class="font-medium text-slate-800 dark:text-navy-50 text-sm">
                                                    @if($regle->score_total_min)
                                                        Score ≥ {{ $regle->score_total_min }}
                                                    @elseif($regle->min_blocs_score)
                                                        {{ $regle->min_blocs_score }}+ blocs critiques
                                                    @elseif($regle->bloc_finance_min)
                                                        Finance ≥ {{ $regle->bloc_finance_min }}
                                                    @elseif($regle->bloc_juridique_min)
                                                        Juridique ≥ {{ $regle->bloc_juridique_min }}
                                                    @elseif($regle->duree_min_mois)
                                                        {{ $regle->duree_min_mois }}+ mois
                                                    @endif
                                                </div>
                                                <div class="text-xs text-slate-500">
                                                    @if($regle->score_total_min)
                                                        Actuel: {{ $scoreGlobal ?? 0 }}
                                                    @elseif($regle->min_blocs_score)
                                                        Actuel: {{ $nbBlocCritiques ?? 0 }}
                                                    @elseif($regle->bloc_finance_min)
                                                        Actuel: {{ $financeScore ?? 0 }}
                                                    @elseif($regle->bloc_juridique_min)
                                                        Actuel: {{ $juridiqueScore ?? 0 }}
                                                    @elseif($regle->duree_min_mois)
                                                        Actuel: {{ $delaiMois ?? 0 }}
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @elseif($profilId == 2) <!-- ÉMERGENTE -->
                                <div class="space-y-3">
                                    @foreach($reglesSuivantes as $regle)
                                        <div class="flex items-center p-3 rounded-lg {{ 
                                            ($regle->score_total_min && $scoreGlobal >= $regle->score_total_min) || 
                                            ($regle->min_blocs_score && $nbBlocConformes >= $regle->min_blocs_score) ||
                                            ($regle->bloc_finance_min && $financeScore >= $regle->bloc_finance_min) ||
                                            ($regle->bloc_juridique_min && $juridiqueScore >= $regle->bloc_juridique_min) ||
                                            ($regle->duree_min_mois && $delaiMois >= $regle->duree_min_mois) 
                                            ? 'bg-green-50 border-green-200' : 'bg-slate-50 border-slate-200' }} border">
                                            <div class="w-5 h-5 rounded-full {{ 
                                                ($regle->score_total_min && $scoreGlobal >= $regle->score_total_min) || 
                                                ($regle->min_blocs_score && $nbBlocConformes >= $regle->min_blocs_score) ||
                                                ($regle->bloc_finance_min && $financeScore >= $regle->bloc_finance_min) ||
                                                ($regle->bloc_juridique_min && $juridiqueScore >= $regle->bloc_juridique_min) ||
                                                ($regle->duree_min_mois && $delaiMois >= $regle->duree_min_mois) 
                                                ? 'bg-green-500' : 'bg-slate-300' }} flex items-center justify-center mr-3">
                                                <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                </svg>
                                            </div>
                                            <div>
                                                <div class="font-medium text-slate-800 dark:text-navy-50 text-sm">
                                                    @if($regle->score_total_min)
                                                        Score ≥ {{ $regle->score_total_min }}
                                                    @elseif($regle->min_blocs_score)
                                                        {{ $regle->min_blocs_score }}+ blocs ≥ 16
                                                    @elseif($regle->bloc_finance_min)
                                                        Finance ≥ {{ $regle->bloc_finance_min }}
                                                    @elseif($regle->bloc_juridique_min)
                                                        Juridique ≥ {{ $regle->bloc_juridique_min }}
                                                    @elseif($regle->duree_min_mois)
                                                        {{ $regle->duree_min_mois }}+ mois
                                                    @endif
                                                </div>
                                                <div class="text-xs text-slate-500">
                                                    @if($regle->score_total_min)
                                                        Actuel: {{ $scoreGlobal ?? 0 }}
                                                    @elseif($regle->min_blocs_score)
                                                        Actuel: {{ $nbBlocConformes ?? 0 }}
                                                    @elseif($regle->bloc_finance_min)
                                                        Actuel: {{ $financeScore ?? 0 }}
                                                    @elseif($regle->bloc_juridique_min)
                                                        Actuel: {{ $juridiqueScore ?? 0 }}
                                                    @elseif($regle->duree_min_mois)
                                                        Actuel: {{ $delaiMois ?? 0 }}
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else <!-- ÉLITE -->
                                <div class="flex items-center p-3 rounded-lg bg-green-50 border-green-200 border">
                                    <div class="w-5 h-5 rounded-full bg-green-500 flex items-center justify-center mr-3">
                                        <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                    </div>
                                    <div>
                                        <div class="font-medium text-slate-800 dark:text-navy-50 text-sm">
                                            Excellence atteinte
                                        </div>
                                        <div class="text-xs text-slate-500">
                                            Tous les critères ÉLITE remplis
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Scores Détaillés par Bloc -->
                <div class="bg-white dark:bg-navy-800 rounded-xl shadow-lg p-6">
                    <h2 class="text-xl font-semibold text-slate-800 dark:text-navy-50 mb-6">
                        Scores Détaillés par Bloc
                    </h2>
                    
                    <div class="space-y-4">
                        @foreach($scoresParBloc as $blocCode => $score)
                            @if(!in_array($blocCode, ['par_niveau', 'nb_blocs_critiques', 'nb_blocs_reference']))
                                @php
                                    $blocNoms = [
                                        'STRATEGIE' => 'Stratégie',
                                        'FINANCE' => 'Finance & Comptabilité',
                                        'JURIDIQUE' => 'Juridique',
                                        'RH' => 'Ressources humaines',
                                        'MARKETING' => 'Marketing',
                                        'COMMUNICATION' => 'Communication',
                                        'COMMERCIAL' => 'Commercial',
                                        'OPERATIONS' => 'Opérations',
                                        'DIGITAL' => 'Digital',
                                        'ADMINISTRATION' => 'Administration'
                                    ];
                                    $blocName = $blocNoms[$blocCode] ?? $blocCode;
                                    $isCritical = $score < 8;
                                    $isGood = $score >= 16;
                                    $colorClass = $isCritical ? 'bg-red-500' : ($isGood ? 'bg-green-500' : 'bg-yellow-500');
                                    $textColor = $isCritical ? 'text-red-600' : ($isGood ? 'text-green-600' : 'text-yellow-600');
                                @endphp
                                <div class="flex items-center justify-between p-4 rounded-lg bg-slate-50 dark:bg-navy-700">
                                    <div class="flex items-center flex-1">
                                        <div class="w-10 h-10 rounded-full bg-slate-200 dark:bg-navy-600 flex items-center justify-center mr-4">
                                            <svg class="w-5 h-5 text-slate-600 dark:text-navy-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                            </svg>
                                        </div>
                                        <div class="flex-1">
                                            <div class="font-medium text-slate-800 dark:text-navy-50">
                                                {{ $blocName }}
                                            </div>
                                            <div class="text-sm {{ $textColor }}">
                                                {{ $isCritical ? 'Bloc critique - Attention requise' : ($isGood ? 'Bloc performant' : 'Bloc à améliorer') }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-4">
                                        <div class="text-right">
                                            <div class="text-lg font-bold text-slate-800 dark:text-navy-50">
                                                {{ $score }}/20
                                            </div>
                                            <div class="text-xs text-slate-500">
                                                {{ round(($score / 20) * 100) }}%
                                            </div>
                                        </div>
                                        <div class="w-16 h-2 bg-slate-200 dark:bg-navy-600 rounded-full overflow-hidden">
                                            <div class="h-full {{ $colorClass }}" style="width: {{ ($score / 20) * 100 }}%"></div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>

                <!-- Conditions de Progression -->
                @if($profilId < 3)
                    <div class="bg-white dark:bg-navy-800 rounded-xl shadow-lg p-6">
                        <h2 class="text-xl font-semibold text-slate-800 dark:text-navy-50 mb-6">
                            Conditions de Progression
                        </h2>
                        
                        @if($profilId == 1) <!-- PÉPITE → ÉMERGENTE -->
                            <div class="space-y-4">
                                <div class="p-4 rounded-lg bg-blue-50 dark:bg-navy-700">
                                    <h3 class="font-medium text-blue-800 dark:text-blue-300 mb-2">
                                        Pour passer au profil ÉMERGENTE
                                    </h3>
                                    <ul class="space-y-2 text-sm text-blue-700 dark:text-blue-400">
                                        @foreach($reglesSuivantes as $regle)
                                            <li class="flex items-center">
                                                <svg class="w-4 h-4 mr-2 {{ 
                                                    ($regle->score_total_min && $scoreGlobal >= $regle->score_total_min) || 
                                                    ($regle->min_blocs_score && $nbBlocConformes >= $regle->min_blocs_score) ||
                                                    ($regle->bloc_finance_min && $financeScore >= $regle->bloc_finance_min) ||
                                                    ($regle->bloc_juridique_min && $juridiqueScore >= $regle->bloc_juridique_min) ||
                                                    ($regle->duree_min_mois && $delaiMois >= $regle->duree_min_mois) 
                                                    ? 'text-green-500' : 'text-blue-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                </svg>
                                                @if($regle->score_total_min)
                                                    Score global ≥ {{ $regle->score_total_min }} (actuel: {{ $scoreGlobal ?? 0 }})
                                                @elseif($regle->min_blocs_score)
                                                    {{ $regle->min_blocs_score }}+ blocs conformes (actuel: {{ $nbBlocConformes ?? 0 }})
                                                @elseif($regle->bloc_finance_min)
                                                    Bloc Finance ≥ {{ $regle->bloc_finance_min }} (actuel: {{ $financeScore ?? 0 }})
                                                @elseif($regle->bloc_juridique_min)
                                                    Bloc Juridique ≥ {{ $regle->bloc_juridique_min }} (actuel: {{ $juridiqueScore ?? 0 }})
                                                @elseif($regle->duree_min_mois)
                                                    {{ $regle->duree_min_mois }}+ mois dans le palier (actuel: {{ $delaiMois ?? 0 }})
                                                @endif
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        @elseif($profilId == 2) <!-- ÉMERGENTE → ÉLITE -->
                            <div class="space-y-4">
                                <div class="p-4 rounded-lg bg-purple-50 dark:bg-navy-700">
                                    <h3 class="font-medium text-purple-800 dark:text-purple-300 mb-2">
                                        Pour passer au profil ÉLITE
                                    </h3>
                                    <ul class="space-y-2 text-sm text-purple-700 dark:text-purple-400">
                                        @foreach($reglesSuivantes as $regle)
                                            <li class="flex items-center">
                                                <svg class="w-4 h-4 mr-2 {{ 
                                                    ($regle->score_total_min && $scoreGlobal >= $regle->score_total_min) || 
                                                    ($regle->min_blocs_score && $nbBlocConformes >= $regle->min_blocs_score) ||
                                                    ($regle->bloc_finance_min && $financeScore >= $regle->bloc_finance_min) ||
                                                    ($regle->bloc_juridique_min && $juridiqueScore >= $regle->bloc_juridique_min) ||
                                                    ($regle->duree_min_mois && $delaiMois >= $regle->duree_min_mois) 
                                                    ? 'text-green-500' : 'text-purple-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                </svg>
                                                @if($regle->score_total_min)
                                                    Score global ≥ {{ $regle->score_total_min }} (actuel: {{ $scoreGlobal ?? 0 }})
                                                @elseif($regle->min_blocs_score)
                                                    {{ $regle->min_blocs_score }}+ blocs ≥ 16 (actuel: {{ $nbBlocConformes ?? 0 }})
                                                @elseif($regle->bloc_finance_min)
                                                    Bloc Finance ≥ {{ $regle->bloc_finance_min }} (actuel: {{ $financeScore ?? 0 }})
                                                @elseif($regle->bloc_juridique_min)
                                                    Bloc Juridique ≥ {{ $regle->bloc_juridique_min }} (actuel: {{ $juridiqueScore ?? 0 }})
                                                @elseif($regle->duree_min_mois)
                                                    {{ $regle->duree_min_mois }}+ mois dans le palier (actuel: {{ $delaiMois ?? 0 }})
                                                @endif
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        @else <!-- ÉLITE -->
                            <div class="space-y-4">
                                <div class="p-4 rounded-lg bg-green-50 dark:bg-navy-700">
                                    <h3 class="font-medium text-green-800 dark:text-green-300 mb-2">
                                        Excellence maintenue
                                    </h3>
                                    <p class="text-sm text-green-700 dark:text-green-400">
                                        Vous avez atteint le niveau le plus élevé. Continuez à maintenir ces excellents résultats !
                                    </p>
                                </div>
                            </div>
                        @endif
                    </div>
                @endif

            <!-- Deux colonnes pour Actions et Informations Complémentaires -->
            <div class="col-span-12 lg:col-span-4 space-y-6">
                <div class="grid grid-cols-12 gap-4">
                    <!-- Colonne Actions -->
                    <div class="col-span-12 lg:col-span-6">
                        <div class="bg-white dark:bg-navy-800 rounded-xl shadow-lg p-6">
                            <h3 class="text-lg font-semibold text-slate-800 dark:text-navy-50 mb-4">
                                Actions
                            </h3>
                            
                            <div class="space-y-3">
                                <a href="{{ route('diagnosticentreprise.indexForm') }}" 
                                   class="btn w-full bg-primary text-white hover:bg-primary-focus">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                    Nouveau Diagnostic
                                </a>
                                
                                {{--                                
                                <a href="{{ route('entreprise.orientations.index', $entreprise->id) }}" 
                                   class="btn w-full bg-green-500 text-white hover:bg-green-600">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                    </svg>
                                    Voir Orientations
                                </a>
--}}
                                
                                <a href="{{ route('entreprise.progression.show', $entreprise->id) }}" 
                                   class="btn w-full bg-purple-500 text-white hover:bg-purple-600">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                    </svg>
                                    Ma Progression
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Colonne Informations Complémentaires -->
                    <div class="col-span-12 lg:col-span-6">
                        <div class="bg-white dark:bg-navy-800 rounded-xl shadow-lg p-6">
                            <h3 class="text-lg font-semibold text-slate-800 dark:text-navy-50 mb-4">
                                Informations Complémentaires
                            </h3>
                            
                            <div class="space-y-3">
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-slate-500 dark:text-navy-300">Date de création</span>
                                    <span class="text-sm font-medium text-slate-800 dark:text-navy-50">
                                        {{ $entreprise->created_at->format('d/m/Y') }}
                                    </span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-slate-500 dark:text-navy-300">Dernière mise à jour</span>
                                    <span class="text-sm font-medium text-slate-800 dark:text-navy-50">
                                        {{ $entreprise->updated_at->format('d/m/Y') }}
                                    </span>
                                </div>
                                @if($dernierDiagnostic)
                                    <div class="flex items-center justify-between">
                                        <span class="text-sm text-slate-500 dark:text-navy-300">Dernier diagnostic</span>
                                        <span class="text-sm font-medium text-slate-800 dark:text-navy-50">
                                            {{ $dernierDiagnostic->created_at->format('d/m/Y') }}
                                        </span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            </div>

            <div class="col-span-12 py-6 lg:sticky lg:bottom-0 lg:col-span-4 lg:self-end">
                @include('layouts.sidebar')
            </div>    

        </div>
    </main>
</x-app-layout>
