<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Mail;

class WelcomeNotification extends Notification implements ShouldQueue
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
            $subject = '🎉 Bienvenue sur CJES Africa !';
            $content = "Bonjour " . $this->userName . " 👋\n\n";
            $content .= "Bienvenue dans la communauté CJES Africa ! Nous sommes ravis de vous compter parmi nous.\n\n";
            $content .= "Votre compte a été créé avec succès. Vous pouvez maintenant :\n";
            $content .= "📊 Accéder à votre tableau de bord\n";
            $content .= "🏢 Gérer vos entreprises\n";
            $content .= "💰 Suivre vos cotisations\n";
            $content .= "🎯 Participer aux diagnostics\n\n";
            $content .= "Accédez à votre tableau de bord : " . route('dashboard') . "\n\n";
            $content .= "Si vous avez des questions, n'hésitez pas à nous contacter.\n\n";
            $content .= "Cordialement,\n";
            $content .= "L'équipe CJES Africa";

            Mail::raw($content, function ($message) use ($notifiable, $subject) {
                $message->to($notifiable->email)
                    ->subject($subject);
            });

        } catch (\Exception $e) {
            // En cas d'erreur, logger mais ne pas faire de fallback
            \Log::error('Erreur Mail::raw dans WelcomeNotification: ' . $e->getMessage());
            // Ne rien retourner - Laravel gérera l'erreur
        }
    }
}
