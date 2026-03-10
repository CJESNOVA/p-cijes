<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Mail;

class PasswordResetNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public string $resetToken, public string $userName)
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
            $resetUrl = route('resetPasswordView', ['token' => $this->resetToken]);
            $subject = '🔐 Réinitialisation de votre mot de passe';
            
            $content = "Bonjour " . $this->userName . " 👋\n\n";
            $content .= "Vous avez demandé la réinitialisation de votre mot de passe.\n\n";
            $content .= "Cliquez sur le lien ci-dessous pour définir un nouveau mot de passe :\n";
            $content .= $resetUrl . "\n\n";
            $content .= "⚠️ Ce lien expirera dans 60 minutes pour des raisons de sécurité.\n\n";
            $content .= "Si vous n'avez pas demandé cette réinitialisation, vous pouvez ignorer cet email.\n\n";
            $content .= "Pour sécuriser votre compte, choisissez un mot de passe contenant :\n";
            $content .= "• Au moins 8 caractères\n";
            $content .= "• Une lettre majuscule et une minuscule\n";
            $content .= "• Un chiffre et un caractère spécial (@$!%*?&)\n\n";
            $content .= "📧 Contactez-nous à support@cjes.africa si vous avez des questions.\n\n";
            $content .= "Sécurité avant tout !\n";
            $content .= "L'équipe CJES Africa";

            Mail::raw($content, function ($message) use ($notifiable, $subject) {
                $message->to($notifiable->email)
                    ->subject($subject);
            });

            // Retourner un MailMessage vide pour éviter les erreurs
            return (new MailMessage)->subject($subject);
        } catch (\Exception $e) {
            // En cas d'erreur, retourner un MailMessage basique
            $resetUrl = route('resetPasswordView', ['token' => $this->resetToken]);
            return (new MailMessage)
                ->subject('🔐 Réinitialisation de votre mot de passe')
                ->greeting('Bonjour ' . $this->userName . ' 👋')
                ->line('Vous avez demandé la réinitialisation de votre mot de passe.')
                ->line('Cliquez sur le bouton ci-dessous pour définir un nouveau mot de passe :')
                ->action('Réinitialiser mon mot de passe', $resetUrl)
                ->line('⚠️ Ce lien expirera dans 60 minutes pour des raisons de sécurité.')
                ->line('Si vous n\'avez pas demandé cette réinitialisation, vous pouvez ignorer cet email.')
                ->salutation('L\'équipe CJES Africa');
        }
    }
}
