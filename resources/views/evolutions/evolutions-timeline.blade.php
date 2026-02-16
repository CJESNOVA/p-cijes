@if($evolutions && $evolutions->count() > 0)
    <div class="relative">
        <!-- Timeline -->
        <div class="absolute left-8 top-0 bottom-0 w-0.5 bg-slate-200 dark:bg-navy-700"></div>
        
        <div class="space-y-6">
            @foreach($evolutions as $index => $evolution)
                @php
                    $isLast = $index === 0; // Le premier élément (index 0) est maintenant le plus récent à cause du reverse()
                    $evolutionScore = $evolution->getEvolutionScore();
                    $evolutionPercent = $evolution->getEvolutionPourcentage();
                @endphp
                
                <div class="relative flex items-start gap-4">
                    <!-- Point sur la timeline -->
                    <div class="relative z-10 w-16 h-16 rounded-full bg-white dark:bg-navy-800 border-4 {{ $isLast ? 'border-purple-500' : 'border-slate-300 dark:border-navy-600' }} flex items-center justify-center">
                        @if($isLast)
                            <div class="w-8 h-8 rounded-full bg-purple-500 flex items-center justify-center">
                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                            </div>
                        @else
                            @if($evolution->estProgression())
                                <div class="w-3 h-3 rounded-full bg-green-500"></div>
                            @elseif($evolution->estRegression())
                                <div class="w-3 h-3 rounded-full bg-red-500"></div>
                            @else
                                <div class="w-3 h-3 rounded-full bg-slate-300 dark:bg-navy-600"></div>
                            @endif
                        @endif
                    </div>
                    
                    <!-- Contenu -->
                    <div class="flex-1 pb-6">
                        <div class="p-4 rounded-lg border {{ $isLast ? 'border-purple-200 bg-purple-50 dark:bg-navy-700' : 'border-slate-200 bg-slate-50 dark:bg-navy-700' }}">
                            <div class="flex items-center justify-between mb-3">
                                <div class="flex items-center gap-3">
                                    @if($evolution->diagnosticstatut)
                                        <span class="px-2 py-1 bg-blue-100 text-blue-600 rounded text-sm">
                                            {{ $evolution->diagnosticstatut->titre }}
                                        </span>
                                    @endif
                                    @if($evolution->entrepriseprofil)
                                        <span class="px-2 py-1 bg-purple-100 text-purple-600 rounded text-sm font-medium">
                                            {{ $evolution->entrepriseprofil->titre }}
                                        </span>
                                    @endif
                                </div>
                                <div class="text-sm text-slate-500">
                                    {{ $evolution->created_at->format('d/m/Y H:i') }}
                                </div>
                            </div>
                            
                            <div class="space-y-2">
                                <p class="text-sm text-slate-600 dark:text-navy-300">
                                    {{ $evolution->commentaire ?? 'Évolution automatique' }}
                                </p>
                                
                                @if($evolution->score_global)
                                    <div class="flex items-center gap-2">
                                        <span class="text-sm text-slate-500">Score:</span>
                                        <span class="px-2 py-1 bg-blue-100 text-blue-600 rounded text-xs">
                                            {{ $evolution->score_global }}/200
                                        </span>
                                        
                                        @if($evolutionScore !== null)
                                            <span class="px-2 py-1 {{ $evolutionScore > 0 ? 'bg-green-100 text-green-600' : ($evolutionScore < 0 ? 'bg-red-100 text-red-600' : 'bg-gray-100 text-gray-600') }} rounded text-xs">
                                                {{ $evolutionScore > 0 ? '+' : '' }}{{ $evolutionScore }} pts
                                            </span>
                                        @endif
                                        
                                        @if($evolutionPercent !== null)
                                            <span class="px-2 py-1 {{ $evolutionPercent > 0 ? 'bg-green-100 text-green-600' : ($evolutionPercent < 0 ? 'bg-red-100 text-red-600' : 'bg-gray-100 text-gray-600') }} rounded text-xs">
                                                {{ $evolutionPercent > 0 ? '+' : '' }}{{ $evolutionPercent }}%
                                            </span>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@else
    <div class="text-center py-8">
        <div class="w-16 h-16 rounded-full bg-slate-100 flex items-center justify-center mx-auto mb-4">
            <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
            </svg>
        </div>
        <h3 class="text-lg font-medium text-slate-800 dark:text-navy-50 mb-2">
            Aucune évolution
        </h3>
        <p class="text-slate-600 dark:text-navy-300">
            Commencez votre premier diagnostic pour suivre votre progression
        </p>
        <div class="mt-4">
            <a href="{{ route('diagnosticentreprise.choix_entreprise') }}" 
               class="btn bg-purple-500 text-white hover:bg-purple-600">
                Commencer un diagnostic
            </a>
        </div>
    </div>
@endif
