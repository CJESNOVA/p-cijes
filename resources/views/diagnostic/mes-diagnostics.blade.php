<x-app-layout title="Mes Diagnostics" is-sidebar-open="true" is-header-blur="true">
    <main class="main-content w-full px-[var(--margin-x)] pb-8">
        <!-- En-tête -->
        <div class="mb-2">
            <div class="flex items-center gap-4 mb-2">
                <div class="h-14 w-14 rounded-xl bg-gradient-to-br from-[#4FBE96] to-[#4FBE96] flex items-center justify-center shadow-lg">
                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
                <div>
                    <h1 class="text-3xl font-bold text-slate-800 dark:text-navy-50">
                        Mes Diagnostics
                    </h1>
                    <p class="text-slate-600 dark:text-navy-300">
                        Historique de tous vos diagnostics et leurs résultats
                    </p>
                </div>
            </div>
        </div>
<div class="grid grid-cols-12 lg:gap-6">
    <div class="col-span-12 pt-6 lg:col-span-8 lg:pb-6">
        @if($diagnostics->isEmpty())
            <!-- Aucun diagnostic -->
            <div class="bg-white dark:bg-navy-800 rounded-xl shadow-lg p-8 text-center">
                <div class="w-16 h-16 rounded-full bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-slate-800 dark:text-navy-50 mb-2">
                    Aucun diagnostic réalisé
                </h3>
                <p class="text-slate-600 dark:text-navy-300 mb-6">
                    Vous n'avez pas encore passé de diagnostic. Commencez dès maintenant !
                </p>
                <a href="{{ route('diagnostic.form') }}" class="btn bg-primary text-white hover:bg-primary-focus">
                    Commencer un diagnostic
                </a>
            </div>
        @else
            <!-- Liste des diagnostics -->
            <div class="space-y-8">
                @foreach($diagnostics as $diagnostic)
                    <div class="bg-white dark:bg-navy-800 rounded-xl shadow-lg border border-slate-200 dark:border-navy-700 p-6 hover:shadow-xl transition-shadow duration-300">
                        <!-- En-tête du diagnostic -->
                        <div class="flex items-start justify-between mb-4">
                            <div>
                                <h3 class="text-xl font-semibold text-slate-800 dark:text-navy-50 mb-1">
                                    Diagnostic du {{ $diagnostic->created_at->format('d/m/Y') }}
                                </h3>
                                <div class="flex items-center gap-3 text-sm text-slate-600 dark:text-navy-300">
                                    <span>{{ $diagnostic->created_at->format('H:i') }}</span>
                                    @if($diagnostic->diagnostictype)
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400">
                                            {{ $diagnostic->diagnostictype->titre }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                            <div class="text-right">
                                @if($diagnostic->diagnosticstatut)
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium 
                                        @if($diagnostic->diagnosticstatut->code == 'critique') bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400
                                        @elseif($diagnostic->diagnosticstatut->code == 'fragile') bg-orange-100 text-orange-800 dark:bg-orange-900/30 dark:text-orange-400
                                        @elseif($diagnostic->diagnosticstatut->code == 'intermediaire') bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400
                                        @elseif($diagnostic->diagnosticstatut->code == 'conforme') bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400
                                        @else bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400
                                        @endif">
                                        {{ $diagnostic->diagnosticstatut->titre }}
                                    </span>
                                @endif
                            </div>
                        </div>

                        <!-- Score global -->
                        <div class="mb-4">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-sm font-medium text-slate-700 dark:text-navy-100">
                                    Score global
                                </span>
                                <span class="text-sm font-bold text-slate-800 dark:text-navy-50">
                                    {{ $diagnostic->scoreglobal ?? 'N/A' }}/200
                                </span>
                            </div>
                            <div class="w-full bg-slate-200 dark:bg-navy-600 rounded-full h-2">
                                @php
                                    $scoreValue = is_numeric($diagnostic->scoreglobal) ? (int)$diagnostic->scoreglobal : 0;
                                    $percentage = min(100, ($scoreValue / 200) * 100);
                                @endphp
                                <div class="bg-primary h-2 rounded-full" style="width: {{ $percentage }}%"></div>
                            </div>
                        </div>

                        <!-- Modules et scores -->
                        @if($diagnostic->diagnosticmodulescores->isNotEmpty())
                            <div class="mb-4">
                                <h4 class="text-sm font-medium text-slate-700 dark:text-navy-100 mb-3">
                                    Scores par module
                                </h4>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                    @foreach($diagnostic->diagnosticmodulescores->take(4) as $moduleScore)
                                        @if($moduleScore->diagnosticmodule)
                                            <div class="flex items-center justify-between p-3 rounded-lg bg-slate-50 dark:bg-navy-700 border border-slate-200 dark:border-navy-600">
                                                <div class="flex-1">
                                                    <div class="text-sm font-medium text-slate-800 dark:text-navy-50 truncate" title="{{ $moduleScore->diagnosticmodule->titre }}">
                                                        {{ Str::limit($moduleScore->diagnosticmodule->titre, 30) }}
                                                    </div>
                                                    <div class="text-xs text-slate-600 dark:text-navy-300">
                                                        {{ $moduleScore->score_pourcentage ?? 0 }}%
                                                    </div>
                                                </div>
                                                @if($moduleScore->diagnosticblocstatut)
                                                    <div class="ml-3">
                                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                                                            @if($moduleScore->diagnosticblocstatut->code == 'critique') bg-red-100 text-red-800
                                                            @elseif($moduleScore->diagnosticblocstatut->code == 'fragile') bg-orange-100 text-orange-800
                                                            @elseif($moduleScore->diagnosticblocstatut->code == 'intermediaire') bg-yellow-100 text-yellow-800
                                                            @elseif($moduleScore->diagnosticblocstatut->code == 'conforme') bg-green-100 text-green-800
                                                            @else bg-blue-100 text-blue-800
                                                            @endif">
                                                            {{ $moduleScore->diagnosticblocstatut->code }}
                                                        </span>
                                                    </div>
                                                @endif
                                            </div>
                                        @endif
                                    @endforeach
                                    @if($diagnostic->diagnosticmodulescores->count() > 4)
                                        <div class="text-sm text-slate-500 dark:text-navy-300 text-center">
                                            +{{ $diagnostic->diagnosticmodulescores->count() - 4 }} autres modules
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endif

                        <!-- Boutons d'action -->
                        @if($diagnostic->diagnosticstatut_id == 2)
                        <div class="flex flex-wrap gap-3 pt-4 border-t border-slate-200 dark:border-navy-600">
                            @if($diagnostic->diagnostictype)
                                @if($diagnostic->diagnostictype->id == 1)
                                    <!-- Type 1 : PME -->
                                    <a href="{{ route('diagnostic.success', $diagnostic->id) }}" 
                                       class="btn btn-sm bg-green-500 text-white hover:bg-green-600">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        Page de succès
                                    </a>
                                    
                                    <a href="{{ route('diagnostic.plans', $diagnostic->id) }}" 
                                       class="btn btn-sm bg-blue-500 text-white hover:bg-blue-600">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                        </svg>
                                        Plans d'action
                                    </a>
                                @elseif($diagnostic->diagnostictype->id == 2)
                                    <!-- Type 2 : ENTREPRISE -->
                                    <a href="{{ route('diagnosticentreprise.success', $diagnostic->id) }}" 
                                       class="btn btn-sm bg-green-500 text-white hover:bg-green-600">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        Page de succès
                                    </a>
                                    
                                    <a href="{{ route('diagnosticentreprise.plans', $diagnostic->id) }}" 
                                       class="btn btn-sm bg-blue-500 text-white hover:bg-blue-600">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                        </svg>
                                        Plans d'action
                                    </a>
                                    
                                    @if($diagnostic->entreprise_id)
                                        <a href="{{ route('entreprise.dashboard', $diagnostic->entreprise_id) }}" 
                                           class="btn btn-sm bg-purple-500 text-white hover:bg-purple-600">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                                            </svg>
                                            Dashboard
                                        </a>
                                    @endif
                                @elseif($diagnostic->diagnostictype->id == 3)
                                    <!-- Type 3 : QUALIFICATION -->
                                    <a href="{{ route('diagnosticentreprisequalification.results', $diagnostic->entreprise_id) }}" 
                                       class="btn btn-sm bg-green-500 text-white hover:bg-green-600">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        Résultats
                                    </a>
                                @endif
                            @else
                                <!-- Type non reconnu - boutons par défaut -->
                                <a href="{{ route('diagnostic.success', $diagnostic->id) }}" 
                                   class="btn btn-sm bg-green-500 text-white hover:bg-green-600">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    Page de succès
                                </a>
                                
                                <a href="{{ route('diagnostic.plans', $diagnostic->id) }}" 
                                   class="btn btn-sm bg-blue-500 text-white hover:bg-blue-600">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                    </svg>
                                    Plans d'action
                                </a>
                            @endif
                        </div>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <div class="col-span-12 py-6 lg:sticky lg:bottom-0 lg:col-span-4 lg:self-end">
        @include('layouts.sidebar')
    </div>    
</div>
      </main>
</x-app-layout>
