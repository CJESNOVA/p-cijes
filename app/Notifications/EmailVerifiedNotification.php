<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Mail;

class EmailVerifiedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public string $userName)
    {
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        // Utiliser Mail::raw directement comme dans MailTestController
        try {
            $subject = '✅ Votre email a été confirmé !';
            
            $content = "Félicitations " . $this->userName . " ! 🎉\n\n";
            $content .= "Votre adresse email a été vérifiée avec succès.\n\n";
            $content .= "Votre compte est maintenant entièrement activé et vous pouvez profiter de toutes les fonctionnalités de CJES Africa.\n\n";
            $content .= "Voici ce que vous pouvez faire maintenant :\n";
            $content .= "🚀 Compléter votre profil membre\n";
            $content .= "📊 Explorer votre tableau de bord\n";
            $content .= "🏢 Ajouter vos entreprises\n";
            $content .= "💰 Gérer vos cotisations\n\n";
            $content .= "Commencez maintenant : " . route('dashboard') . "\n\n";
            $content .= "Nous sommes là pour vous accompagner dans votre parcours entrepreneurial.\n\n";
            $content .= "Bienvenue dans l'aventure CJES Africa !\n";
            $content .= "L'équipe CJES Africa";

            Mail::raw($content, function ($message) use ($notifiable, $subject) {
                $message->to($notifiable->email)
                    ->subject($subject);
            });

        } catch (\Exception $e) {
            // En cas d'erreur, logger mais ne pas faire de fallback
            \Log::error('Erreur Mail::raw dans EmailVerifiedNotification: ' . $e->getMessage());
            // Ne rien retourner - Laravel gérera l'erreur
        }
    }
}
