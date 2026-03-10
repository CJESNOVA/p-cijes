<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Mail;

class PasswordResetConfirmationNotification extends Notification implements ShouldQueue
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
            $subject = '✅ Votre mot de passe a été modifié';
            
            $content = "Bonjour " . $this->userName . " 👋\n\n";
            $content .= "Votre mot de passe a été modifié avec succès.\n\n";
            $content .= "Cette modification a été effectuée récemment sur votre compte CJES Africa.\n\n";
            $content .= "Si vous êtes à l'origine de cette modification, tout est en ordre.\n\n";
            $content .= "Si vous n'avez pas demandé cette modification, veuillez :\n";
            $content .= "🔐 Changer immédiatement votre mot de passe\n";
            $content .= "📧 Nous contacter à support@cjes.africa\n";
            $content .= "🔒 Vérifier l'activité de votre compte\n\n";
            $content .= "Accédez à votre compte : " . route('dashboard') . "\n\n";
            $content .= "Conseils de sécurité :\n";
            $content .= "• Utilisez un mot de passe unique et complexe\n";
            $content .= "• Ne partagez jamais vos identifiants\n";
            $content .= "• Activez l'authentification à deux facteurs si disponible\n\n";
            $content .= "Votre sécurité est notre priorité\n";
            $content .= "L'équipe CJES Africa";

            Mail::raw($content, function ($message) use ($notifiable, $subject) {
                $message->to($notifiable->email)
                    ->subject($subject);
            });

        } catch (\Exception $e) {
            // En cas d'erreur, retourner un MailMessage basique
            return (new MailMessage)
                ->subject('✅ Votre mot de passe a été modifié')
                ->greeting('Bonjour ' . $this->userName . ' 👋')
                ->line('Votre mot de passe a été modifié avec succès.')
                ->line('Si vous n\'avez pas demandé cette modification, veuillez nous contacter.')
                ->action('Accéder à mon compte', route('dashboard'))
                ->salutation('L\'équipe CJES Africa');
        }
    }
}
