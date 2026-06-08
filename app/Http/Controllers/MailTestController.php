<?php

namespace App\Http\Controllers;

use App\Models\Membre;
use App\Models\Action;
use App\Models\User;
use App\Notifications\WelcomeNotification;
use App\Notifications\RecompenseNotification;
use App\Notifications\PasswordResetNotification;
use App\Notifications\PasswordResetConfirmationNotification;
use App\Notifications\EmailVerifiedNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Carbon\Carbon;

class MailTestController extends Controller
{
    /**
     * Test d'envoi d'email simple
     */
    public function testMail()
    {
        try {
            Mail::raw('Ceci est un test depuis Laravel local.', function ($message) {
                $message->to('yokamly@gmail.com')
                        ->subject('Test Mail CJES Africa');
            });

            return '✅ Mail envoyé avec succès !';
        } catch (\Exception $e) {
            return '❌ Erreur : ' . $e->getMessage();
        }
    }

    /**
     * Test de notification de bienvenue
     */
    public function testNotification()
    {
        try {
            // Créer un utilisateur de test
            $user = User::first();
            if (!$user) {
                return '❌ Aucun utilisateur trouvé';
            }

            $user->notify(new WelcomeNotification($user->name));
            
            return '✅ Notification de bienvenue envoyée à : ' . $user->email;
        } catch (\Exception $e) {
            return '❌ Erreur : ' . $e->getMessage();
        }
    }

    /**
     * Test de notification de réinitialisation de mot de passe
     */
    public function testPasswordResetNotification()
    {
        try {
            $user = User::first();
            if (!$user) {
                return '❌ Aucun utilisateur trouvé';
            }

            $resetToken = Str::random(32);
            $user->notify(new PasswordResetNotification($resetToken, $user->name));
            
            return '✅ Notification de réinitialisation envoyée à : ' . $user->email;
        } catch (\Exception $e) {
            return '❌ Erreur : ' . $e->getMessage();
        }
    }

    /**
     * Test de toutes les notifications
     */
    public function testAllNotifications()
    {
        $results = [];
        
        // Test 1: Welcome Notification
        try {
            $user = User::first();
            if ($user) {
                $user->notify(new WelcomeNotification($user->name));
                $results[] = '✅ Welcome Notification envoyée à ' . $user->email;
            } else {
                $results[] = '❌ Aucun utilisateur trouvé pour Welcome Notification';
            }
        } catch (\Exception $e) {
            $results[] = '❌ Erreur Welcome Notification: ' . $e->getMessage();
        }

        // Test 2: Email Verified Notification
        try {
            $user = User::first();
            if ($user) {
                $user->notify(new EmailVerifiedNotification($user->name));
                $results[] = '✅ Email Verified Notification envoyée à ' . $user->email;
            } else {
                $results[] = '❌ Aucun utilisateur trouvé pour Email Verified Notification';
            }
        } catch (\Exception $e) {
            $results[] = '❌ Erreur Email Verified Notification: ' . $e->getMessage();
        }

        // Test 3: Password Reset Confirmation
        try {
            $user = User::first();
            if ($user) {
                $user->notify(new PasswordResetConfirmationNotification($user->name));
                $results[] = '✅ Password Reset Confirmation envoyée à ' . $user->email;
            } else {
                $results[] = '❌ Aucun utilisateur trouvé pour Password Reset Confirmation';
            }
        } catch (\Exception $e) {
            $results[] = '❌ Erreur Password Reset Confirmation: ' . $e->getMessage();
        }

        // Test 4: Recompense Notification
        try {
            $membre = Membre::first();
            if ($membre) {
                $membre->notify(new RecompenseNotification('Test Action', 100, 'http://test.com'));
                $results[] = '✅ Recompense Notification envoyée à ' . $membre->email;
            } else {
                $results[] = '❌ Aucun membre trouvé pour Recompense Notification';
            }
        } catch (\Exception $e) {
            $results[] = '❌ Erreur Recompense Notification: ' . $e->getMessage();
        }

        return '<h3>Résultats des tests:</h3><ul><li>' . implode('</li><li>', $results) . '</li></ul>';
    }
}
