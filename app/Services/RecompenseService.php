<?php

namespace App\Services;

use App\Models\Action;
use App\Models\Recompense;
use App\Models\Alerte;
use App\Models\Ressourcecompte;
use App\Models\Ressourcetransaction;
use App\Notifications\RecompenseNotification;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class RecompenseService
{
    /**
     * Attribuer une récompense, générer la ressource (compte + transaction),
     * créer l'alerte et notifier l'utilisateur (mail + database + broadcast).
     *
     * @param string $actionCode
     * @param \App\Models\Membre $membre
     * @param null|\App\Models\Entreprise $entreprise
     * @param mixed $source  // id ou référence de la source (ex: quiz_id)
     * @return Recompense|false
     */
    public function attribuerRecompense(string $actionCode, $membre, $entreprise = null, $source = null)
    {
        $action = Action::where('code', $actionCode)->first();
        if (! $action) {
            return false;
        }

        $points = (int) ($action->point ?? 0);

        DB::beginTransaction();

        try {
            // 1) Vérifier limite d'attribution
            if ($action->limite) {
                $nbRecompenses = Recompense::where('action_id', $action->id)
                    ->where(function ($q) use ($membre, $entreprise) {
                        $q->where('membre_id', $membre->id);
                        if ($entreprise) {
                            $q->orWhere('entreprise_id', $entreprise->id);
                        }
                    })
                    ->count();

                if ($nbRecompenses >= $action->limite) {
                    DB::rollBack();
                    return false;
                }
            }

            // 2) Créer / récupérer compte ressource
            $ressourceCompte = null;
            if ($action->ressourcetype_id) {
                $criteria = ['ressourcetype_id' => $action->ressourcetype_id];

                if ($entreprise) {
                    $criteria['entreprise_id'] = $entreprise->id;
                    $defaults = ['membre_id' => $membre->id, 'solde' => 0, 'etat' => 1, 'spotlight' => 0];
                } else {
                    $criteria['membre_id'] = $membre->id;
                    $defaults = ['entreprise_id' => null, 'solde' => 0, 'etat' => 1, 'spotlight' => 0];
                }

                $ressourceCompte = Ressourcecompte::firstOrCreate($criteria, $defaults);
            }

            // 3) Créer la récompense
            $recompense = Recompense::create([
                'valeur' => $points,
                'commentaire' => "Récompense pour l’action : " . $action->titre,
                'action_id' => $action->id,
                'ressourcetype_id' => $action->ressourcetype_id,
                'dateattribution' => Carbon::now(),
                'membre_id' => $membre->id,
                'source_id' => $source,
                'spotlight' => 0,
                'etat' => 1,
            ]);

            if ($entreprise) {
                $recompense->entreprise_id = $entreprise->id;
                $recompense->save();
            }

            // 4) Transaction + mise à jour du solde
            if ($ressourceCompte && $points > 0) {
                $reference = 'REC-' . strtoupper(Str::random(8));
                Ressourcetransaction::create([
                    'montant' => $points,
                    'reference' => $reference,
                    'ressourcecompte_id' => $ressourceCompte->id,
                    'datetransaction' => Carbon::now(),
                    'operationtype_id' => 1,
                    'spotlight' => 0,
                    'etat' => 1,
                ]);

                $ressourceCompte->increment('solde', $points);
            }

            // 5) Créer une alerte
            $lien = Route::has('recompense.mesRecompenses')
                ? route('recompense.mesRecompenses')
                : (Route::has('dashboard') ? route('dashboard') : '#');

            $alerte = Alerte::create([
                'titre' => "🎉 Félicitations !",
                'contenu' => "Vous avez gagné {$points} {$action->ressourcetype->titre} pour : {$action->titre}",
                'lienurl' => $lien,
                'langue_id' => 1,
                'alertetype_id' => 1,
                'recompense_id' => $recompense->id,
                'datealerte' => Carbon::now(),
                'membre_id' => $membre->id,
                'lu' => 0,
                'etat' => 1,
            ]);

            // 6) Envoi de notification Laravel (database + mail)
            /*$notifTarget = null;

            if (method_exists($membre, 'notify')) {
                $notifTarget = $membre;
            } elseif (isset($membre->user) && method_exists($membre->user, 'notify')) {
                $notifTarget = $membre->user;
            }

            if ($notifTarget) {
                Notification::send($notifTarget, new RecompenseNotification(
                    $action->titre,
                    $points,
                    $lien
                ));
            } else {
                Log::warning("Aucune cible notifiable trouvée pour membre id={$membre->id} lors de l'attribution d'une récompense.");
            }*/

            DB::commit();

$returnRecompense = $recompense;

        try { 
            Mail::send('emails.recompense', [
                'membre' => $membre,
                'action' => $action,
                'recompense' => $recompense,
            ], function ($message) use ($membre) {
                $message->to($membre->email ?? 'yokamly@gmail.com')
                        ->subject('🎁 Nouvelle récompense obtenue - CIJES Africa');
            });

            } catch (\Exception $e) { 
                \Log::error('Erreur envoi mail récompense : ' . $e->getMessage(), [
                    'membre_id' => $membre->id ?? null,
                    'action' => $action->titre ?? null,
                ]);
            }
            
            return $returnRecompense;

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Erreur attribuerRecompense: ' . $e->getMessage(), [
                'action' => $actionCode,
                'membre_id' => $membre->id ?? null,
                'entreprise_id' => $entreprise->id ?? null,
            ]);
            //return $e->getMessage();
            return false;
        }
    }
}
