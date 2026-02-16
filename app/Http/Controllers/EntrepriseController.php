<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Entreprise;
use App\Models\Secteur;
use App\Models\Entreprisetype;
use App\Models\Entreprisemembre;
use App\Models\Pays;
use App\Models\Membre;
use App\Models\Diagnosticstatutregle;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

use App\Services\SupabaseStorageService;
use App\Services\DiagnosticStatutService;

class EntrepriseController extends Controller
{
    protected $diagnosticStatutService;

    public function __construct(DiagnosticStatutService $diagnosticStatutService)
    {
        $this->diagnosticStatutService = $diagnosticStatutService;
    }
    public function index()
    {
        $userId = Auth::id();

        // Récupérer le membre lié à ce user
        $membre = Membre::where('user_id', $userId)->first();

        if (!$membre) {
            return redirect()
                ->route('membre.createOrEdit')
                ->with('error', '⚠️ Vous devez d’abord créer votre profil membre.');
        }

        // Récupérer les entreprises du membre
        $entreprises = Entreprisemembre::with(['entreprise', 'entreprise.entrepriseprofil'])
            ->where('membre_id', $membre->id)
            ->get();

        return view('entreprise.index', compact('entreprises'));
    }

    public function create()
    {
        $userId = Auth::id();
        $membre = Membre::where('user_id', $userId)->first();

        if (!$membre) {
            return redirect()->route('membre.createOrEdit')->with('error', 'Vous devez d\'abord créer votre profil membre.');
        }

        $secteurs = Secteur::where('etat', 1)->get();
        $entreprisetypes = Entreprisetype::where('etat', 1)->get();
        $payss = (new Pays())->all();

        return view('entreprise.form', [
            'entreprise' => null,
            'entreprisemembre' => null,
            'secteurs' => $secteurs,
            'entreprisetypes' => $entreprisetypes,
            'payss' => $payss,
        ]);
    }

    public function edit($id)
    {
        $userId = Auth::id();
        $membre = Membre::where('user_id', $userId)->first();

        $entreprise = Entreprise::findOrFail($id);
        $entreprisemembre = Entreprisemembre::where('entreprise_id', $id)
            ->where('membre_id', $membre->id)
            ->first();

        $secteurs = Secteur::where('etat', 1)->get();
        $entreprisetypes = Entreprisetype::where('etat', 1)->get();
        $payss = (new Pays())->all();

        return view('entreprise.form', compact('entreprise', 'entreprisemembre', 'secteurs', 'entreprisetypes', 'payss'));
    }

    public function store(Request $request)
    {
        $userId = Auth::id();
        $membre = Membre::where('user_id', $userId)->first();

        if (!$membre) {
            return redirect()->route('membre.createOrEdit')->with('error', 'Vous devez d\'abord créer votre profil membre.');
        }

        $validated = $this->validateData($request);

        // Vérifier si l'entreprise essaie d'être membre CJES sans être éligible
        $messageInfo = null;
        if (isset($validated['annee_creation']) && $validated['annee_creation']) {
            $anneeMin = date('Y') - 10;
            if ($validated['annee_creation'] < $anneeMin) {
                // Forcer à false si l'entreprise a plus de 10 ans
                if (isset($validated['est_membre_cijes']) && $validated['est_membre_cijes']) {
                    $messageInfo = "L'entreprise a été enregistrée mais ne peut pas être membre CJES car elle a plus de 10 ans.";
                }
                $validated['est_membre_cijes'] = 0;
            }
        }

        if ($request->hasFile('vignette')) {
            //$validated['vignette'] = $request->file('vignette')->store('entreprises', 'public');

                $storage = new \App\Services\SupabaseStorageService();
                $file = $request->file('vignette');
                $cleanName = \App\Helpers\FileHelper::sanitizeFileName($file->getClientOriginalName());
                $path = 'entreprises/' . time() . '_' . $cleanName;
                $url = $storage->upload($path, file_get_contents($file->getRealPath()));
                $validated['vignette'] = $path;
        }

        $entreprise = new Entreprise($validated);
        $entreprise->etat = 1;
        $entreprise->save();

        Entreprisemembre::create([
            'membre_id' => $membre->id,
            'entreprise_id' => $entreprise->id,
            'fonction' => $request->input('fonction', 'Fondateur'),
            'bio' => $request->input('bio', ''),
            'etat' => 1,
        ]);

        return redirect()->route('entreprise.index')->with('success', 'Entreprise créée avec succès.')->with('info', $messageInfo);
    }

    public function update(Request $request, $id)
    {
        $userId = Auth::id();
        $membre = Membre::where('user_id', $userId)->first();

        $entreprise = Entreprise::findOrFail($id);

        $validated = $this->validateData($request);

        // Vérifier si l'entreprise essaie d'être membre CJES sans être éligible
        $messageInfo = null;
        if (isset($validated['annee_creation']) && $validated['annee_creation']) {
            $anneeMin = date('Y') - 10;
            if ($validated['annee_creation'] < $anneeMin) {
                // Forcer à false si l'entreprise a plus de 10 ans
                if (isset($validated['est_membre_cijes']) && $validated['est_membre_cijes']) {
                    $messageInfo = "L'entreprise a été mise à jour mais ne peut pas être membre CJES car elle a plus de 10 ans.";
                }
                $validated['est_membre_cijes'] = 0;
            }
        }

        if ($request->hasFile('vignette')) {
            //$validated['vignette'] = $request->file('vignette')->store('entreprises', 'public');

                $storage = new \App\Services\SupabaseStorageService();
                $file = $request->file('vignette');
                $cleanName = \App\Helpers\FileHelper::sanitizeFileName($file->getClientOriginalName());
                $path = 'entreprises/' . time() . '_' . $cleanName;
                $url = $storage->upload($path, file_get_contents($file->getRealPath()));
                $validated['vignette'] = $path;
        }

        $entreprise->update($validated);

        Entreprisemembre::updateOrCreate(
            ['membre_id' => $membre->id, 'entreprise_id' => $entreprise->id],
            ['fonction' => $request->fonction, 'bio' => $request->bio, 'etat' => 1]
        );

        return redirect()->route('entreprise.index')->with('success', 'Entreprise mise à jour avec succès.')->with('info', $messageInfo);
    }

    private function validateData(Request $request)
    {
        return $request->validate([
            'nom' => 'required|string|max:255',
            'email' => 'required|email',
            'telephone' => 'required|string|max:30',
            'adresse' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'secteur_id' => 'required|exists:secteurs,id',
            'entreprisetype_id' => 'required|exists:entreprisetypes,id',
            'annee_creation' => 'nullable|integer|min:1900|max:' . date('Y'),
            'est_membre_cijes' => 'nullable|boolean',
            'pays_id' => 'required',
            'vignette' => 'nullable|image|max:2048',
            'fonction' => 'nullable|string|max:255',
            'bio' => 'nullable|string|max:1000',
        ]);
    }


    public function destroy($id)
{
    $userId = Auth::id();
    $membre = Membre::where('user_id', $userId)->first();

    if (!$membre) {
        return redirect()
            ->route('membre.createOrEdit')
            ->with('error', '⚠️ Vous devez d’abord créer votre profil membre.');
    }

    // Vérifie si le membre est bien lié à cette entreprise
    $entreprisemembre = Entreprisemembre::where('membre_id', $membre->id)
        ->where('entreprise_id', $id)
        ->first();

    if (!$entreprisemembre) {
        return redirect()
            ->route('entreprise.index')
            ->with('error', '❌ Vous n’êtes pas autorisé à supprimer cette entreprise.');
    }

    // Supprimer la relation membre-entreprise
    $entreprisemembre->delete();

    // Supprimer l’entreprise si plus aucun membre n’y est lié
    $autresMembres = Entreprisemembre::where('entreprise_id', $id)->count();

    if ($autresMembres === 0) {
        $entreprise = Entreprise::findOrFail($id);

        // Supprime la vignette du stockage si elle existe
        if ($entreprise->vignette && Storage::disk('public')->exists($entreprise->vignette)) {
            Storage::disk('public')->delete($entreprise->vignette);
        }

        $entreprise->delete();
    }

    return redirect()
        ->route('entreprise.index')
        ->with('success', '✅ Entreprise supprimée avec succès.');
    }

    /**
     * Afficher le dashboard d'une entreprise
     */
    public function dashboard($entrepriseId)
    {
        $userId = Auth::id();
        $membre = Membre::where('user_id', $userId)->first();

        if (!$membre) {
            return redirect()->route('membre.createOrEdit')
                ->with('error', '⚠️ Vous devez d\'abord créer votre profil membre.');
        }

        // Vérifier que l'utilisateur est membre de cette entreprise
        $entrepriseMembre = Entreprisemembre::where('entreprise_id', $entrepriseId)
            ->where('membre_id', $membre->id)
            ->first();

        if (!$entrepriseMembre && !Auth::user()->hasRole('admin')) {
            return redirect()->route('entreprise.index')
                ->with('error', '⚠️ Accès non autorisé à cette entreprise.');
        }

        $entreprise = Entreprise::with(['diagnostics' => function($query) {
            $query->where('diagnosticstatut_id', 2)->latest();
        }])->findOrFail($entrepriseId);

        // Récupérer les données pour le dashboard
        $dernierDiagnostic = $entreprise->diagnostics->first();
        $scoreGlobal = $dernierDiagnostic ? $dernierDiagnostic->scoreglobal : 0;
        $evolutions = $this->diagnosticStatutService->getEvolutions($entrepriseId, 10);
        
        // Calculer la prochaine évaluation selon les règles
        $prochaineEvaluation = null;
        $delaiMois = 6; // Valeur par défaut
        if ($dernierDiagnostic) {
            // Chercher la règle applicable pour ce profil
            $regle = \App\Models\Diagnosticstatutregle::where('entrepriseprofil_id', $entreprise->entrepriseprofil_id)
                ->where(function($query) use ($scoreGlobal) {
                    $query->whereNull('score_total_min')
                          ->orWhere('score_total_min', '<=', $scoreGlobal);
                })
                ->where(function($query) use ($scoreGlobal) {
                    $query->whereNull('score_total_max')
                          ->orWhere('score_total_max', '>=', $scoreGlobal);
                })
                ->orderBy('duree_min_mois', 'desc')
                ->first();
            
            // Utiliser le délai de la règle ou la valeur par défaut
            $delaiMois = $regle ? $regle->duree_min_mois : 6;
            $prochaineEvaluation = $dernierDiagnostic->created_at->addMonths($delaiMois);
        }

        // Calculer les scores par bloc
        $scoresParBloc = [];
        $blocsCritiques = [];
        $blocsCritiquesCount = 0;
        if ($dernierDiagnostic) {
            $scoresParBloc = $this->diagnosticStatutService->calculerScoresParBloc($dernierDiagnostic);
            
            // Récupérer tous les modules avec leur diagnosticblocstatut ET leurs orientations
            $blocsCritiques = $dernierDiagnostic->diagnosticmodulescores()
                ->with(['diagnosticmodule', 'diagnosticblocstatut'])
                ->get()
                ->map(function($moduleScore) {
                    // Récupérer les orientations pour ce module selon son score
                    $orientations = \App\Models\Diagnosticorientation::getOrientationsPourModule(
                        $moduleScore->diagnosticmodule_id, 
                        $moduleScore->score_total ?? 0
                    );
                    
                    return [
                        'nom' => $moduleScore->diagnosticmodule->titre ?? $moduleScore->diagnosticmodule->code,
                        'score' => $moduleScore->score_total ?? 0,
                        'pourcentage' => $moduleScore->score_pourcentage ?? 0,
                        'statut' => $moduleScore->diagnosticblocstatut->titre ?? 'Non défini',
                        'module_id' => $moduleScore->diagnosticmodule_id,
                        'orientations' => $orientations
                    ];
                })
                ->toArray();
            
            // Débogage pour vérifier le contenu
            \Log::info('Blocs critiques trouvés', [
                'blocsCritiques' => $blocsCritiques,
                'count' => count($blocsCritiques),
                'diagnostic_id' => $dernierDiagnostic->id
            ]);
            
            $blocsCritiquesCount = count($blocsCritiques);
        }

        return view('entreprise.dashboard', compact('entreprise', 'dernierDiagnostic', 'scoreGlobal', 'scoresParBloc', 'evolutions', 'prochaineEvaluation', 'delaiMois', 'blocsCritiques', 'blocsCritiquesCount'));
    }

    /**
     * Afficher le profil détaillé d'une entreprise
     */
    public function showProfil($entrepriseId)
    {
        $userId = Auth::id();
        $membre = Membre::where('user_id', $userId)->first();

        if (!$membre) {
            return redirect()->route('membre.createOrEdit')
                ->with('error', '⚠️ Vous devez d\'abord créer votre profil membre.');
        }

        // Vérifier que l'utilisateur est membre de cette entreprise
        $entrepriseMembre = Entreprisemembre::where('entreprise_id', $entrepriseId)
            ->where('membre_id', $membre->id)
            ->first();

        if (!$entrepriseMembre && !Auth::user()->hasRole('admin')) {
            return redirect()->route('entreprise.index')
                ->with('error', '⚠️ Accès non autorisé à cette entreprise.');
        }

        $entreprise = Entreprise::with(['diagnostics' => function($query) {
            $query->where('diagnosticstatut_id', 2)->latest();
        }])->findOrFail($entrepriseId);

        // Récupérer les données pour le profil
        $dernierDiagnostic = $entreprise->diagnostics->first();
        $scoreGlobal = $dernierDiagnostic ? $dernierDiagnostic->scoreglobal : 0;
        $evolutions = $this->diagnosticStatutService->getEvolutions($entrepriseId, 20);

        // Calculer les scores par bloc
        $scoresParBloc = [];
        $nbBlocCritiques = 0;
        $nbBlocConformes = 0;
        $financeScore = 0;
        $juridiqueScore = 0;
        $delaiMois = 0;
        
        if ($dernierDiagnostic) {
            $scoresParBloc = $this->diagnosticStatutService->calculerScoresParBloc($dernierDiagnostic);
            
            // Extraire les statistiques des scores par bloc
            $nbBlocCritiques = $scoresParBloc['nb_blocs_critiques_score'] ?? 0;
            $nbBlocConformes = $scoresParBloc['nb_blocs_conformes'] ?? 0;
            $financeScore = $scoresParBloc['FINANCE'] ?? 0;
            $juridiqueScore = $scoresParBloc['JURIDIQUE'] ?? 0;
            
            // Calculer le délai depuis le dernier diagnostic
            if ($dernierDiagnostic->created_at) {
                $delaiMois = $dernierDiagnostic->created_at->diffInMonths(now());
            }
        }

        // Récupérer les règles de progression depuis la base de données
        $reglesActuelles = [];
        $reglesSuivantes = [];
        $profilActuelId = $entreprise->entrepriseprofil_id ?? 1;
        
        // Règles du profil actuel (pour vérification)
        $reglesActuelles = Diagnosticstatutregle::where('entrepriseprofil_id', $profilActuelId)
            ->where(function($query) use ($scoreGlobal) {
                $query->whereNull('score_total_min')
                      ->orWhere('score_total_min', '<=', $scoreGlobal);
            })
            ->where(function($query) use ($scoreGlobal) {
                $query->whereNull('score_total_max')
                      ->orWhere('score_total_max', '>=', $scoreGlobal);
            })
            ->get();

        // Règles du profil suivant (pour objectifs)
        $profilSuivantId = $profilActuelId < 3 ? $profilActuelId + 1 : null;
        if ($profilSuivantId) {
            $reglesSuivantes = Diagnosticstatutregle::where('entrepriseprofil_id', $profilSuivantId)->get();
        }

        return view('entreprise.profil', compact(
            'entreprise', 
            'dernierDiagnostic', 
            'scoreGlobal', 
            'scoresParBloc', 
            'evolutions',
            'nbBlocCritiques',
            'nbBlocConformes',
            'financeScore',
            'juridiqueScore',
            'delaiMois',
            'reglesActuelles',
            'reglesSuivantes',
            'profilSuivantId'
        ));
    }

    /**
     * Afficher les orientations d'une entreprise
     */
    public function orientations($entrepriseId)
    {
        $userId = Auth::id();
        $membre = Membre::where('user_id', $userId)->first();

        if (!$membre) {
            return redirect()->route('membre.createOrEdit')
                ->with('error', '⚠️ Vous devez d\'abord créer votre profil membre.');
        }

        // Vérifier que l'utilisateur est membre de cette entreprise
        $entrepriseMembre = Entreprisemembre::where('entreprise_id', $entrepriseId)
            ->where('membre_id', $membre->id)
            ->first();

        if (!$entrepriseMembre && !Auth::user()->hasRole('admin')) {
            return redirect()->route('entreprise.index')
                ->with('error', '⚠️ Accès non autorisé à cette entreprise.');
        }

        $entreprise = Entreprise::findOrFail($entrepriseId);
        $dernierDiagnostic = $entreprise->diagnostics()->where('diagnosticstatut_id', 2)->latest()->first();

        if (!$dernierDiagnostic) {
            return view('entreprise.orientations.index', compact('entreprise'))
                ->with('error', '⚠️ Aucun diagnostic terminé trouvé pour cette entreprise.');
        }

        // Récupérer les scores par bloc pour les orientations
        $scoresParBloc = $this->diagnosticStatutService->calculerScoresParBloc($dernierDiagnostic);
        $orientations = $this->diagnosticStatutService->getOrientationsDiagnostic($dernierDiagnostic->id);
        $nbBlocConformes = $scoresParBloc['nb_blocs_conformes'] ?? 0;

        // Préparer les orientations détaillées par bloc
        $orientationsParBloc = [];
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

        foreach ($scoresParBloc as $blocCode => $score) {
            if (!in_array($blocCode, ['par_niveau', 'nb_blocs_critiques', 'nb_blocs_reference', 'nb_blocs_conformes'])) {
                $orientationsBloc = collect($orientations)->where('bloc', $blocCode)->toArray();
                $orientationsParBloc[$blocCode] = [
                    'nom' => $blocNoms[$blocCode] ?? $blocCode,
                    'score' => $score,
                    'orientations' => $orientationsBloc
                ];
            }
        }

        // Identifier les blocs critiques
        $blocsCritiques = [];
        foreach ($scoresParBloc as $blocCode => $score) {
            if (!in_array($blocCode, ['par_niveau', 'nb_blocs_critiques', 'nb_blocs_reference', 'nb_blocs_conformes']) && $score < 8) {
                $blocsCritiques[] = [
                    'code' => $blocCode,
                    'nom' => $blocNoms[$blocCode] ?? $blocCode,
                    'score' => $score
                ];
            }
        }

        return view('entreprise.orientations.index', compact(
            'entreprise', 
            'dernierDiagnostic', 
            'scoresParBloc', 
            'orientations', 
            'blocsCritiques',
            'nbBlocConformes',
            'orientationsParBloc'
        ));
    }

    /**
     * Afficher la progression d'une entreprise
     */
    public function progression($entrepriseId)
    {
        $userId = Auth::id();
        $membre = Membre::where('user_id', $userId)->first();

        if (!$membre) {
            return redirect()->route('membre.createOrEdit')
                ->with('error', '⚠️ Vous devez d\'abord créer votre profil membre.');
        }

        // Vérifier que l'utilisateur est membre de cette entreprise
        $entrepriseMembre = Entreprisemembre::where('entreprise_id', $entrepriseId)
            ->where('membre_id', $membre->id)
            ->first();

        if (!$entrepriseMembre && !Auth::user()->hasRole('admin')) {
            return redirect()->route('entreprise.index')
                ->with('error', '⚠️ Accès non autorisé à cette entreprise.');
        }

        $entreprise = Entreprise::with(['diagnostics' => function($query) {
            $query->where('diagnosticstatut_id', 2)->latest();
        }])->findOrFail($entrepriseId);

        // Récupérer les données pour la progression
        $dernierDiagnostic = $entreprise->diagnostics->first();
        $scoreGlobal = $dernierDiagnostic ? $dernierDiagnostic->scoreglobal : 0;
        $evolutions = $this->diagnosticStatutService->getEvolutions($entrepriseId, 5);
        $nbDiagnostics = $entreprise->diagnostics()->count();

        // Calculer les scores par bloc
        $scoresParBloc = [];
        if ($dernierDiagnostic) {
            $scoresParBloc = $this->diagnosticStatutService->calculerScoresParBloc($dernierDiagnostic);
        }

        // Récupérer les règles de progression pour le prochain objectif
        $regleProchainObjectif = null;
        $profilActuelId = $entreprise->entrepriseprofil_id ?? 1;
        
        if ($profilActuelId < 3) { // Si pas déjà ÉLITE
            $profilSuivantId = $profilActuelId + 1;
            $regleProchainObjectif = \App\Models\Diagnosticstatutregle::where('entrepriseprofil_id', $profilSuivantId)
                ->orderBy('score_total_min', 'asc')
                ->first();
        }

        // Préparer les données d'évolution pour les graphiques
        $scoresEvolution = collect();
        $blocsEvolution = [];
        $derniersScoresEvolution = collect(); // Pour le graphique des 5 derniers diagnostics
        if ($evolutions && $evolutions->count() > 1) {
            // Récupérer le premier diagnostic pour initialiser
            $premierDiagnostic = $entreprise->diagnostics()->where('diagnosticstatut_id', 2)->oldest()->first();
            $premiersScores = [];
            if ($premierDiagnostic) {
                $premiersScores = $this->diagnosticStatutService->calculerScoresParBloc($premierDiagnostic);
            }
            
            // Initialiser les blocs avec le premier score (le plus ancien)
            foreach ($premiersScores as $blocCode => $score) {
                if (!in_array($blocCode, ['par_niveau', 'nb_blocs_critiques', 'nb_blocs_reference', 'nb_blocs_conformes'])) {
                    $blocsEvolution[$blocCode] = ['first' => $score, 'last' => $score];
                }
            }
            
            // Mettre à jour avec les scores actuels (dernier diagnostic)
            foreach ($scoresParBloc as $blocCode => $score) {
                if (!in_array($blocCode, ['par_niveau', 'nb_blocs_critiques', 'nb_blocs_reference', 'nb_blocs_conformes'])) {
                    $blocsEvolution[$blocCode]['last'] = $score;
                }
            }
            
            // Ajouter les scores pour le graphique en ordre chronologique (plus ancien au plus récent)
            foreach ($evolutions->sortBy('created_at') as $evolution) {
                $scoresEvolution->push([
                    'date' => $evolution->created_at->format('d/m/Y'),
                    'score' => $evolution->score_global
                ]);
                $derniersScoresEvolution->push([
                    'date' => $evolution->created_at->format('d/m/Y'),
                    'score' => $evolution->score_global
                ]);
            }
        }

        return view('entreprise.progression.show', compact(
            'entreprise', 
            'dernierDiagnostic', 
            'scoreGlobal', 
            'scoresParBloc', 
            'evolutions',
            'scoresEvolution',
            'derniersScoresEvolution',
            'blocsEvolution',
            'nbDiagnostics',
            'regleProchainObjectif'
        ));
    }



}
