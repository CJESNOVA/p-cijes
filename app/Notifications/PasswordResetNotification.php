<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

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
        $resetUrl = route('resetPasswordView', ['token' => $this->resetToken]);
        
        return (new MailMessage)
            ->subject('🔐 Réinitialisation de votre mot de passe')
            ->greeting('Bonjour ' . $this->userName . ' 👋')
            ->line('Vous avez demandé la réinitialisation de votre mot de passe.')
            ->line('Cliquez sur le bouton ci-dessous pour définir un nouveau mot de passe :')
            ->action('Réinitialiser mon mot de passe', $resetUrl)
            ->line('⚠️ Ce lien expirera dans 60 minutes pour des raisons de sécurité.')
            ->line('Si vous n\'avez pas demandé cette réinitialisation, vous pouvez ignorer cet email.')
            ->line('Pour sécuriser votre compte, choisissez un mot de passe contenant :')
            ->line('• Au moins 8 caractères')
            ->line('• Une lettre majuscule et une minuscule')
            ->line('• Un chiffre et un caractère spécial (@$!%*?&)')
            ->salutation('Sécurité avant tout !')
            ->salutation('L\'équipe CJES Africa');
    }
}
