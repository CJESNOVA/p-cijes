<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Abonnement;
use App\Models\Abonnementtype;
use App\Models\Ressourcecompte;
use App\Models\Ressourcetransaction;
use App\Models\Abonnementressource;
use App\Models\Ressourcetypeoffretype;
use App\Services\RecompenseService;
use Carbon\Carbon;
use Illuminate\Support\Str;

class RenouvelerAbonnements extends Command
{
    protected $signature = 'abonnement:renouveler';
    protected $description = 'Renouveler automatiquement les abonnements expirés selon la priorité des ressources';

    // Ordre de priorité des ressources
    protected $prioriteRessources = [2, 3, 4, 1]; // Coris, Bon, Sika, KOBO 

    public function handle()
    {
        $this->info('Début du renouvellement automatique des abonnements...');
        
        // Récupérer les abonnements arrivés à échéance (aujourd'hui ou expirés)
        $abonnementsARenouveler = Abonnement::where('date_fin', '<=', now())
            ->where('etat', true)
            ->where('statut', 'paye')
            ->with(['entreprise', 'abonnementtype', 'entreprise.entreprisesmembres.membre'])
            ->get();

        $this->info("{$abonnementsARenouveler->count()} abonnement(s) à renouveler.");

        $succes = 0;
        $echecs = 0;

        foreach ($abonnementsARenouveler as $abonnement) {
            $this->line("Traitement de l'abonnement #{$abonnement->id} - {$abonnement->entreprise->nom}");

            if ($this->renouvelerAbonnement($abonnement)) {
                $succes++;
                $this->info("  -> Renouvelé avec succès");
            } else {
                $echecs++;
                $this->error("  -> Échec du renouvellement");
            }
        }

        $this->info("Renouvellement terminé : {$succes} succès, {$echecs} échecs");
        return 0;
    }

    private function renouvelerAbonnement(Abonnement $ancienAbonnement): bool
    {
        $entreprise = $ancienAbonnement->entreprise;
        $abonnementType = $ancienAbonnement->abonnementtype;
        
        // Récupérer tous les comptes ressources disponibles pour l'entreprise et ses membres
        $membreIds = $entreprise->entreprisesmembres()->pluck('membre_id')->toArray();
        
        $comptesDisponibles = Ressourcecompte::where(function($query) use ($entreprise, $membreIds) {
                $query->where('entreprise_id', $entreprise->id)
                      ->orWhereIn('membre_id', $membreIds);
            })
            ->where('etat', 1)
            ->where('solde', '>=', $abonnementType->montant)
            ->whereIn('ressourcetype_id', $this->prioriteRessources)
            ->orderByRaw('FIELD(ressourcetype_id, ' . implode(',', $this->prioriteRessources) . ')')
            ->get();

        // Essayer chaque ressource selon la priorité
        foreach ($comptesDisponibles as $compte) {
            if ($this->verifierCompatibiliteRessource($compte, $abonnementType)) {
                return $this->effectuerRenouvellement($ancienAbonnement, $compte);
            }
        }

        // Marquer l'abonnement comme en retard si aucun paiement n'est possible
        $ancienAbonnement->update([
            'statut' => 'retard',
            'est_a_jour' => false,
            'nombre_rappels' => $ancienAbonnement->nombre_rappels + 1
        ]);

        return false;
    }

    private function verifierCompatibiliteRessource(Ressourcecompte $compte, Abonnementtype $type): bool
    {
        return Ressourcetypeoffretype::where('ressourcetype_id', $compte->ressourcetype_id)
            ->where('offretype_id', 6) // 6 = abonnements
            ->exists();
    }

    private function effectuerRenouvellement(Abonnement $ancienAbonnement, Ressourcecompte $compte): bool
    {
        $abonnementType = $ancienAbonnement->abonnementtype;
        $entreprise = $ancienAbonnement->entreprise;

        try {
            // Créer le nouvel abonnement
            $dateDebut = now();
            $dateFin = now()->copy()->addDays($abonnementType->nombre_jours);
            $dateEcheance = now()->copy()->addDays($abonnementType->nombre_jours);
            
            $reference = 'ABO-' . strtoupper(Str::random(8));

            $nouvelAbonnement = Abonnement::create([
                'entreprise_id' => $entreprise->id,
                'abonnementtype_id' => $abonnementType->id,
                'montant' => $abonnementType->montant,
                'montant_paye' => $abonnementType->montant,
                'montant_restant' => 0,
                'devise' => 'XOF',
                'date_debut' => $dateDebut,
                'date_fin' => $dateFin,
                'date_echeance' => $dateEcheance,
                'date_paiement' => now(),
                'statut' => 'paye',
                'est_a_jour' => true,
                'nombre_rappels' => 0,
                'mode_paiement' => 'Ressource ' . $compte->ressourcetype->titre,
                'commentaires' => 'Renouvellement automatique depuis l\'abonnement #' . $ancienAbonnement->id,
                'etat' => true,
            ]);

            // Créer la transaction de débit
            $transaction = Ressourcetransaction::create([
                'montant' => -$abonnementType->montant,
                'reference' => $reference,
                'ressourcecompte_id' => $compte->id,
                'datetransaction' => now(),
                'operationtype_id' => 2, // Débit
                'spotlight' => true,
                'etat' => 1,
            ]);

            // Mettre à jour le solde
            $compte->decrement('solde', $abonnementType->montant);

            // Créer l'enregistrement abonnementressource
            Abonnementressource::create([
                'montant' => $abonnementType->montant,
                'reference' => $reference,
                'ressourcecompte_id' => $compte->id,
                'abonnement_id' => $nouvelAbonnement->id,
                'membre_id' => $compte->membre_id,
                'entreprise_id' => $entreprise->id,
                'paiementstatut_id' => 1, // Payé
                'spotlight' => true,
                'etat' => true,
            ]);

            // Attribuer récompense si applicable
            if ($compte->membre) {
                $recompenseService = app(RecompenseService::class);
                $recompenseService->attribuerRecompense('PAIEMENT_ABONNEMENT', $compte->membre, $entreprise, $nouvelAbonnement->id, $abonnementType->montant);
            }

            // Archiver l'ancien abonnement
            $ancienAbonnement->update([
                'commentaires' => $ancienAbonnement->commentaires . ' | Renouvelé automatiquement vers #' . $nouvelAbonnement->id
            ]);

            return true;

        } catch (\Exception $e) {
            $this->error("Erreur lors du renouvellement : " . $e->getMessage());
            return false;
        }
    }
}
