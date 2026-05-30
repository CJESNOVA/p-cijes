<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Membre;
use App\Models\Entreprise;
use App\Models\Entreprisemembre;
use App\Models\Expert;
use App\Models\Diagnosticmodule;
use App\Models\Reservation;
use App\Models\Message;
use App\Models\Evenement;
use App\Models\Pays;
use App\Models\Ressourcetransaction;
use App\Models\Ressourcecompte;
use App\Models\Accompagnement;
use App\Models\Accompagnementconseiller;
use App\Models\Diagnostic;
use App\Models\Espace;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * 🏠 Afficher le tableau de bord principal du membre connecté.
     */
    public function index()
    {
        $userId = Auth::id();

        // 🔹 Vérifier que le membre est bien enregistré
        $membre = Membre::where('user_id', $userId)->first();
        if (!$membre) {
            return redirect()->route('membre.createOrEdit')
                ->with('error', '⚠️ Vous devez d’abord créer votre profil membre avant d’accéder au tableau de bord.');
        }

        // 🔹 Récupérer le pays du membre via Supabase
        $pays = null;
        if ($membre->pays_id) {
            $paysService = new \App\Services\SupabaseService(); // ou un service Pays dédié
            $paysData = $paysService->get('pays', ['id' => 'eq.' . $membre->pays_id]);

            // Supabase renvoie un tableau, prendre le premier élément
            if (!empty($paysData) && isset($paysData[0])) {
                $pays = (object) $paysData[0]; // convertir en objet pour un accès type $pays->nom
            }
        }

        // 🔹 1. ENTREPRISES liées au membre
        $entreprises = Entreprisemembre::with('entreprise')
            ->where('membre_id', $membre->id)
            ->get()
            ->pluck('entreprise') // récupère uniquement le modèle Entreprise
            ->filter(); // filtre les valeurs nulles au cas où une relation est manquante

        // 🔹 2. conseillers du membre
        // Entreprises du membre
        $entrepriseIds = Entreprisemembre::where('membre_id', $membre->id)
            ->pluck('entreprise_id');

        // Accompagnements où le membre est bénéficiaire (direct ou via entreprise)
        $accompagnementIds = Accompagnement::where('membre_id', $membre->id)
            ->orWhereIn('entreprise_id', $entrepriseIds)
            ->pluck('id')
            ->toArray();

        // Accompagnements liés au membre en tant que conseiller
        $accompagnementConseillerIds = Accompagnementconseiller::whereHas('conseiller', function ($q) use ($membre) {
                $q->where('membre_id', $membre->id);
            })
            ->pluck('accompagnement_id')
            ->toArray();

        // Fusionner les deux listes
        $allAccompagnementIds = array_unique(array_merge($accompagnementIds, $accompagnementConseillerIds));

        // Récupérer les conseillers
        $conseillers = Accompagnementconseiller::with([
                'conseiller.membre',
                'conseiller.conseillertype',
                'conseiller.prescriptions.prestation',
                'conseiller.prescriptions.formation',
                'accompagnement.entreprise',
                'accompagnement.membre',
            ])
            ->whereIn('accompagnement_id', $allAccompagnementIds)
            ->whereHas('conseiller')
            ->get();

        // 🔹 3. EXPERTS du même pays que le membre (visibles sur le tableau de bord)
        $experts = Expert::with(['experttype', 'secteur', 'membre'])
            ->whereHas('membre', function ($q) use ($membre) {
                $q->where('pays_id', $membre->pays_id);
            })
            ->where('etat', 1)
            ->latest()
            ->take(6)
            ->get();

        // 🔹 4. MODULES DE DIAGNOSTIC déjà réalisés par le membre avec score
        $diagnostics = Diagnostic::with(['diagnostictype', 'entreprise', 'membre', 'accompagnement'])
            ->where('membre_id', $membre->id)
            ->where('diagnosticstatut_id', 2) // Uniquement les diagnostics validés
            ->orderBy('created_at', 'desc')
            ->get();

        // 🔹 5. RÉSERVATIONS récentes du membre
        $reservations = Reservation::with(['espace', 'reservationstatut'])
            ->where('membre_id', $membre->id)
            ->orderByDesc('created_at')
            ->take(6)
            ->get();

        // 🔹 6. MESSAGES récents (boîte de réception)
        $messages = Message::with(['membre', 'conversation'])
            ->where('lu', 0)
            ->whereHas('conversation', function ($q) use ($membre) {
                // On sélectionne les conversations où le membre connecté est un participant
                $q->where(function ($q2) use ($membre) {
                    $q2->where('membre_id1', $membre->id)
                    ->orWhere('membre_id2', $membre->id);
                });
            })
            // On exclut les messages envoyés par le membre lui-même
            ->where('membre_id', '!=', $membre->id)
            ->orderByDesc('created_at')
            ->take(6)
            ->get();

        // 🔹 7. ÉVÉNEMENTS à venir (par pays ou globaux)
        $calendarEvents = Evenement::with(['inscriptions' => function($q) use ($membre) {
        $q->where('membre_id', $membre->id);
    }])
    ->where('etat', 1)
    ->where('pays_id', $membre->pays_id)
    ->whereDate('dateevenement', '>=', Carbon::now())
    ->orderBy('dateevenement', 'asc')
    ->take(5)
    ->get();


        $espaces = Espace::with('reservationsAVenir')
        ->with('espacetype')
        ->where('etat', 1)
        ->where('pays_id', $membre->pays_id)
        ->take(5)
        ->get();

        // 🔹 8. STATS du tableau de bord (cartes + graphiques)
        $stats = $this->getStats($membre);
        
        // Créer la variable pour la vue
        $diagnosticsModules = $diagnostics;
        
        // Créer la variable myExperts à partir des conseillers
        $myExperts = $conseillers->map(function($conseiller) {
            return $conseiller->conseiller;
        })->filter();

        return view('dashboard.dashboard', compact(
            'membre',
            'pays',
            'stats',
            'entreprises',
            'experts',
            'myExperts',
            'conseillers',
            'diagnosticsModules',
            'reservations',
            'messages',
            'calendarEvents',
            'espaces'
        ));
    }

    /**
     * 📊 Statistiques globales du tableau de bord.
     */
    private function getStats(Membre $membre)
{
    // IDs des entreprises liées au membre
    $entrepriseIds = Entreprisemembre::where('membre_id', $membre->id)
        ->pluck('entreprise_id')
        ->toArray();

    // Calcul des revenus du mois courant et du mois précédent (uniquement transactions réussies)
    $revenue_current_month = Ressourcetransaction::whereHas('ressourcecompte', function ($q) use ($membre, $entrepriseIds) {
        $q->where('membre_id', $membre->id)
          ->orWhereIn('entreprise_id', $entrepriseIds);
    })->where('etat', 1) // Uniquement les transactions réussies
      ->whereMonth('created_at', now()->month)
      ->sum('montant');

    $revenue_last_month = Ressourcetransaction::whereHas('ressourcecompte', function ($q) use ($membre, $entrepriseIds) {
        $q->where('membre_id', $membre->id)
          ->orWhereIn('entreprise_id', $entrepriseIds);
    })->where('etat', 1) // Uniquement les transactions réussies
      ->whereMonth('created_at', now()->subMonth()->month)
      ->sum('montant');

    $variation = $revenue_last_month > 0
        ? (($revenue_current_month - $revenue_last_month) / $revenue_last_month) * 100
        : 0;

    // Nombre d'inscriptions
    $inscriptions = Membre::where('user_id', $membre->user_id)->count();

    // Nombre d'experts liés au membre
    $experts = Expert::where('membre_id', $membre->id)->count();

    // Nombre de diagnostics réalisés par le membre (uniquement les diagnostics validés)
    $diagnosticsCount = Diagnostic::where('membre_id', $membre->id)
        ->where('diagnosticstatut_id', 2) // Uniquement les diagnostics validés (statut 2)
        ->count();

    // Nombre d'entreprises "pepite" liées au membre
    $pepite = Entreprise::whereIn('id', $entrepriseIds)
        ->where('entrepriseprofil_id', 1)
        ->count();

    // Nombre de membres associés aux entreprises du membre
    $membres_associes = Membre::whereHas('entreprisemembres', fn($q) => $q->whereIn('entreprise_id', $entrepriseIds))->count();

    // Nombre de PME parmi les entreprises du membre
    $pme = Entreprise::whereIn('id', $entrepriseIds)
        ->where('entreprisetype_id', 3)
        ->count();

    // Solde des comptes de ressources de type 1
    $soldeType1 = Ressourcecompte::whereHas('ressourcetype', function($q) {
            $q->where('id', 1); // Type = 1
        })
        ->where(function($q) use ($membre, $entrepriseIds) {
            $q->where('membre_id', $membre->id)
              ->orWhereIn('entreprise_id', $entrepriseIds);
        })
        ->sum('solde');

        // Solde cumulé par type de ressource
$soldeParType = Ressourcecompte::selectRaw('ressourcetype_id, SUM(solde) as total_solde')
    ->where(function($q) use ($membre, $entrepriseIds) {
        $q->where('membre_id', $membre->id)
          ->orWhereIn('entreprise_id', $entrepriseIds);
    })
    ->groupBy('ressourcetype_id')
    ->with('ressourcetype')
    ->get();


    return [
        'revenue_month'     => $revenue_current_month,
        'revenue_variation' => round($variation, 2),
        'solde_type1'      => $soldeType1,
        'inscriptions'      => $inscriptions,
        'entreprises'       => count($entrepriseIds),
        'experts'           => $experts,
        'diagnostics'       => $diagnosticsCount,
        'pepite'            => $pepite,
        'membres_associes'  => $membres_associes,
        'pme'               => $pme,
        'earning_series'    => [$revenue_current_month * 0.1, $revenue_current_month * 0.25, $revenue_current_month * 0.3, $revenue_current_month * 0.4, $revenue_current_month * 0.6, $revenue_current_month * 0.45, $revenue_current_month * 0.7], // à adapter selon ton graphique
        'solde_par_type'    => $soldeParType,
    ];
}


}
