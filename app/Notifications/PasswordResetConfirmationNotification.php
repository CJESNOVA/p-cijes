<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

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
        return (new MailMessage)
            ->subject('✅ Votre mot de passe a été modifié')
            ->greeting('Bonjour ' . $this->userName . ' 👋')
            ->line('Votre mot de passe a été modifié avec succès.')
            ->line('Cette modification a été effectuée récemment sur votre compte CJES Africa.')
            ->line('Si vous êtes à l\'origine de cette modification, tout est en ordre.')
            ->line('Si vous n\'avez pas demandé cette modification, veuillez :')
            ->line('🔐 Changer immédiatement votre mot de passe')
            ->line('📧 Nous contacter à support@cjes.africa')
            ->line('🔒 Vérifier l\'activité de votre compte')
            ->action('Accéder à mon compte', route('dashboard'))
            ->line('Conseils de sécurité :')
            ->line('• Utilisez un mot de passe unique et complexe')
            ->line('• Ne partagez jamais vos identifiants')
            ->line('• Activez l\'authentification à deux facteurs si disponible')
            ->salutation('Votre sécurité est notre priorité')
            ->salutation('L\'équipe CJES Africa');
    }
}
