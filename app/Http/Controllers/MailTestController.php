<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Notifications\WelcomeNotification;

class MailTestController extends Controller
{
    public function testMail()
    {
        try {
            // Test simple avec Mail::raw
            Mail::raw('Ceci est un test email depuis CJES Africa', function ($message) {
                $message->to('test@example.com')
                    ->subject('📧 Test Email CJES Africa');
            });
            
            return response()->json([
                'status' => 'success',
                'message' => 'Email de test envoyé avec succès !'
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
}
