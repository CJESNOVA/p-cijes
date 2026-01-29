<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

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
        return (new MailMessage)
            ->subject('✅ Votre email a été confirmé !')
            ->greeting('Félicitations ' . $this->userName . ' ! 🎉')
            ->line('Votre adresse email a été vérifiée avec succès.')
            ->line('Votre compte est maintenant entièrement activé et vous pouvez profiter de toutes les fonctionnalités de CJES Africa.')
            ->line('Voici ce que vous pouvez faire maintenant :')
            ->line('🚀 Compléter votre profil membre')
            ->line('📊 Explorer votre tableau de bord')
            ->line('🏢 Ajouter vos entreprises')
            ->line('💰 Gérer vos cotisations')
            ->action('Commencer maintenant', route('dashboard'))
            ->line('Nous sommes là pour vous accompagner dans votre parcours entrepreneurial.')
            ->salutation('Bienvenue dans l\'aventure CJES Africa !')
            ->salutation('L\'équipe CJES Africa');
    }
}
