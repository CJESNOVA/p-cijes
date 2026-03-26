<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Mail;

class RecompenseNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public string $actionTitre, 
        public int $points, 
        public string $lien,
        public array $recompense = [],
        public array $stats = [],
        public array $nextRewards = []
    ) {
        $this->onQueue('emails');
    }

    public function via($notifiable)
    {
        return ['mail', 'database']; // mail + base de données
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('� Nouvelle récompense obtenue !')
            ->view('emails.recompense', [
                'user' => $notifiable,
                'userName' => $notifiable->name,
                'actionTitre' => $this->actionTitre,
                'points' => $this->points,
                'lien' => $this->lien,
                'recompense' => $this->recompense,
                'stats' => $this->stats,
                'nextRewards' => $this->nextRewards,
            ]);
    }

    public function toArray($notifiable)
    {
        return [
            'titre' => $this->actionTitre,
            'points' => $this->points,
            'lien' => $this->lien,
            'recompense' => $this->recompense,
            'stats' => $this->stats,
            'nextRewards' => $this->nextRewards,
        ];
    }
}
