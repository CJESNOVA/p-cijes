<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

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
        return (new MailMessage)
            ->subject('🎉 Bienvenue sur CJES Africa !')
            ->greeting('Bonjour ' . $this->userName . ' 👋')
            ->line('Bienvenue dans la communauté CJES Africa ! Nous sommes ravis de vous compter parmi nous.')
            ->line('Votre compte a été créé avec succès. Vous pouvez maintenant :')
            ->line('📊 Accéder à votre tableau de bord')
            ->line('🏢 Gérer vos entreprises')
            ->line('💰 Suivre vos cotisations')
            ->line('🎯 Participer aux diagnostics')
            ->action('Accéder à mon tableau de bord', route('dashboard'))
            ->line('Si vous avez des questions, n\'hésitez pas à nous contacter.')
            ->salutation('Cordialement,')
            ->salutation('L\'équipe CJES Africa');
    }
}
