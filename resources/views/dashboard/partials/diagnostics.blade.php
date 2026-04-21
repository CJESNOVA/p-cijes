{{-- resources/views/dashboard/partials/diagnostics.blade.php --}}
<div class="mt-4 ml-4 grid grid-cols-12 gap-4" style="margin-left: 30px;">
    <div class="col-span-12 card p-6 space-y-6">
    <div class="flex items-center justify-between">
        <h3 class="text-lg font-semibold text-slate-700">Diagnostics réalisés</h3>
    </div>

    @if($diagnosticsModules->isEmpty())
        <div class="p-4 bg-yellow-50 rounded-lg text-yellow-800 text-center font-medium">
            Aucun diagnostic réalisé pour le moment.
        </div>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($diagnosticsModules as $diag)
                <div class="flex flex-col bg-white rounded-xl shadow hover:shadow-lg transition-shadow duration-300 p-5 space-y-3">
                    <div class="flex-1">
                        @if(!empty($diag->entreprise))
                            <p class="font-medium text-slate-800">
                                {{ $diag->entreprise->nom }}
                            </p>
                        @else
                            <p class="font-medium text-slate-800">
                                {{ $diag->membre->prenom ?? '-' }} {{ $diag->membre->nom ?? '-' }}
                            </p>
                        @endif
                        {{-- <p class="text-sm text-slate-600">
                            Accompagnement : {{ $diag->accompagnement->entreprise->nom ?? '-' }}
                        </p> --}}
                    </div>
                    <div class="flex items-center space-x-4 mt-2 sm:mt-0">
                        @php
                            // Calculer le vrai pourcentage comme dans l'API
                            $scoreTotal = 0;
                            $scoreMaximum = 0;
                            
                            // Récupérer les résultats de ce diagnostic
                            $resultats = \App\Models\Diagnosticresultat::where('diagnostic_id', $diag->id)
                                ->with('diagnosticreponse')
                                ->get();
                            
                            foreach ($resultats as $resultat) {
                                if ($resultat->diagnosticreponse) {
                                    $scoreTotal += (int)($resultat->diagnosticreponse->score ?? 0);
                                    
                                    // Calculer le score maximum pour cette question
                                    $maxQuestionScore = (int)\App\Models\Diagnosticreponse::where('diagnosticquestion_id', $resultat->diagnosticquestion_id)
                                        ->max('score') ?? 0;
                                    $scoreMaximum += $maxQuestionScore;
                                }
                            }
                            
                            $scorePourcentage = $scoreMaximum > 0 ? round(($scoreTotal * 100) / $scoreMaximum, 2) : 0;
                        @endphp
                        <span class="text-sm font-medium text-[#12CEB7]">
                            Score : {{ $scorePourcentage }}%
                        </span>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
</div>
