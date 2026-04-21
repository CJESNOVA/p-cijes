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
     * @param float|null $montant  // montant pour calcul en pourcentage (optionnel)
     * @return Recompense|false
     */
    public function attribuerRecompense(string $actionCode, $membre, $entreprise = null, $source = null, $montant = null)
    {
        $action = Action::where('code', $actionCode)->first();
        if (! $action) {
            return false;
        }

        // 🎯 CALCUL DES POINTS - Montant fixe ou pourcentage
        $points = (int) ($action->point ?? 0);
        
        // 📊 GESTION DU SEUIL EN POURCENTAGE
        if ($action->seuil && str_contains($action->seuil, '%') && $montant) {
            $pourcentage = (float) str_replace('%', '', $action->seuil);
            
            // 💡 Utiliser directement le montant fourni
            $points = (int) ($montant * ($pourcentage / 100));
            
            \Log::info('Calcul points par pourcentage', [
                'action_code' => $actionCode,
                'pourcentage' => $pourcentage,
                'montant_fourni' => $montant,
                'points_calculés' => $points
            ]);
        }

        DB::beginTransaction();

        try {
            // 1) 🚦 VÉRIFIER LIMITE D'ATTRIBUTION
            if ($action->limite) {
                // 📊 Compter les récompenses existantes pour cette action
                $query = Recompense::where('action_id', $action->id);
                
                // 🎯 Logique de propriété : entreprise优先 si existe
                if ($entreprise) {
                    // Récompense d'entreprise : vérifier par entreprise
                    $query->where('entreprise_id', $entreprise->id);
                    \Log::info('Vérification limite - Récompense entreprise', [
                        'entreprise_id' => $entreprise->id,
                        'action_code' => $actionCode,
                        'limite' => $action->limite
                    ]);
                } else {
                    // Récompense personnelle : vérifier par membre
                    $query->where('membre_id', $membre->id)
                          ->whereNull('entreprise_id');
                    \Log::info('Vérification limite - Récompense personnelle', [
                        'membre_id' => $membre->id,
                        'action_code' => $actionCode,
                        'limite' => $action->limite
                    ]);
                }
                
                $nbRecompenses = $query->count();

                if ($nbRecompenses >= $action->limite) {
                    \Log::warning('Limite de récompense atteinte', [
                        'action_code' => $actionCode,
                        'nb_recompenses' => $nbRecompenses,
                        'limite' => $action->limite,
                        'entreprise_id' => $entreprise->id ?? null,
                        'membre_id' => $membre->id
                    ]);
                    DB::rollBack();
                    return false;
                }
            }

            // 2) 🏦 CRÉER/RÉCUPÉRER COMPTE RESSOURCE
            $ressourceCompte = null;
            if ($action->ressourcetype_id) {
                $criteria = ['ressourcetype_id' => $action->ressourcetype_id];

                // 🎯 Logique de propriété de la récompense
                if ($entreprise) {
                    // 💼 RÉCOMPENSE D'ENTREPRISE - appartient à l'entreprise
                    $criteria['entreprise_id'] = $entreprise->id;
                    $defaults = [
                        'membre_id' => $membre->id,        // Membre déclencheur
                        'solde' => 0, 
                        'etat' => 1, 
                        'spotlight' => 0
                    ];
                    \Log::info('Création compte ressource - Entreprise', [
                        'entreprise_id' => $entreprise->id,
                        'membre_id' => $membre->id,
                        'ressourcetype_id' => $action->ressourcetype_id
                    ]);
                } else {
                    // 👤 RÉCOMPENSE PERSONNELLE - appartient au membre
                    $criteria['membre_id'] = $membre->id;
                    $defaults = [
                        'entreprise_id' => null,           // Pas d'entreprise
                        'solde' => 0, 
                        'etat' => 1, 
                        'spotlight' => 0
                    ];
                    \Log::info('Création compte ressource - Personnel', [
                        'membre_id' => $membre->id,
                        'ressourcetype_id' => $action->ressourcetype_id
                    ]);
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
                    'description' => "Récompense pour l’action : " . $action->titre,
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
            $notifTarget = null;

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
            }

            DB::commit();

$returnRecompense = $recompense;

        try { 
            Mail::send('emails.recompense', [
                'membre' => $membre,
                'action' => $action,
                'recompense' => $recompense,
            ], function ($message) use ($membre) {
                $message->to($membre->email ?? 'yokamly@gmail.com')
                        ->subject('🎁 Nouvelle récompense obtenue - CJES Africa');
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
                'montant' => $montant,
            ]);
            //return $e->getMessage();
            return false;
        }
    }
}
