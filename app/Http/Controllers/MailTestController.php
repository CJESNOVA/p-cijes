<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Notifications\WelcomeNotification;
use App\Notifications\PasswordResetNotification;
use App\Notifications\EmailVerifiedNotification;
use App\Notifications\PasswordResetConfirmationNotification;
use App\Notifications\RecompenseNotification;

class MailTestController extends Controller
{
    public function testMail()
    {
        try {
            // Test simple avec Mail::raw
            Mail::raw('Ceci est un test email depuis CJES Africa', function ($message) {
                $message->to('lookyyokamly@yahoo.fr')
                    ->subject('📧 Test Email CJES Africa');
            });
            
            return response()->json([
                'status' => 'success',
                'message' => 'Email de test envoyé avec succès ! lookyyokamly@yahoo.fr'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Erreur lors de l\'envoi: ' . $e->getMessage()
            ]);
        }
    }
    
    public function testNotification()
    {
        try {
            $user = auth()->user();
            if (!$user) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Utilisateur non connecté'
                ]);
            }
            
            $user->notify(new WelcomeNotification($user->name));
            
            return response()->json([
                'status' => 'success',
                'message' => 'Notification de test envoyée avec succès !'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Erreur lors de l\'envoi: ' . $e->getMessage()
            ]);
        }
    }

    public function testPasswordResetNotification()
    {
        try {
            $user = auth()->user();
            if (!$user) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Utilisateur non connecté'
                ]);
            }

            // Simuler un token de réinitialisation
            $resetToken = bin2hex(random_bytes(32));
            
            // Tester la notification de réinitialisation
            $user->notify(new PasswordResetNotification($resetToken, $user->name));
            
            return response()->json([
                'status' => 'success',
                'message' => 'Notification de réinitialisation de mot de passe envoyée avec succès à ' . $user->email,
                'user_email' => $user->email,
                'reset_token' => $resetToken,
                'reset_url' => route('resetPasswordView', ['token' => $resetToken])
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Erreur lors de l\'envoi de la notification de réinitialisation: ' . $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    public function testAllNotifications()
    {
        try {
            $user = auth()->user();
            if (!$user) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Utilisateur non connecté'
                ]);
            }

            $results = [];
            
            // Tester WelcomeNotification
            try {
                $user->notify(new WelcomeNotification($user->name));
                $results['welcome'] = '✅ Success';
            } catch (\Exception $e) {
                $results['welcome'] = '❌ Error: ' . $e->getMessage();
            }

            // Tester PasswordResetNotification
            try {
                $resetToken = bin2hex(random_bytes(32));
                $user->notify(new PasswordResetNotification($resetToken, $user->name));
                $results['password_reset'] = '✅ Success';
            } catch (\Exception $e) {
                $results['password_reset'] = '❌ Error: ' . $e->getMessage();
            }

            // Tester EmailVerifiedNotification
            try {
                $user->notify(new EmailVerifiedNotification($user->name));
                $results['email_verified'] = '✅ Success';
            } catch (\Exception $e) {
                $results['email_verified'] = '❌ Error: ' . $e->getMessage();
            }

            // Tester PasswordResetConfirmationNotification
            try {
                $user->notify(new PasswordResetConfirmationNotification($user->name));
                $results['password_reset_confirmation'] = '✅ Success';
            } catch (\Exception $e) {
                $results['password_reset_confirmation'] = '❌ Error: ' . $e->getMessage();
            }

            // Tester RecompenseNotification
            try {
                $user->notify(new RecompenseNotification('Test action', 50, route('dashboard')));
                $results['recompense'] = '✅ Success';
            } catch (\Exception $e) {
                $results['recompense'] = '❌ Error: ' . $e->getMessage();
            }

            return response()->json([
                'status' => 'completed',
                'message' => 'Test de toutes les notifications terminé',
                'user_email' => $user->email,
                'results' => $results
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Erreur générale: ' . $e->getMessage()
            ]);
        }
    }
}
