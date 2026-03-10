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

    public function __construct(public string $actionTitre, public int $points, public string $lien)
    {
    }

    public function via($notifiable)
    {
        return ['mail', 'database']; // mail + base de données
    }

    public function toMail($notifiable)
    {
        // Utiliser Mail::raw directement comme dans MailTestController
        try {
            $subject = '🎁 Nouvelle récompense obtenue !';
            
            $content = "Félicitations 🎉\n\n";
            $content .= "Vous venez de gagner **{$this->points} points** pour l'action : **{$this->actionTitre}**.\n\n";
            $content .= "Voir vos récompenses : " . $this->lien . "\n\n";
            $content .= "Continuez à participer pour gagner encore plus de récompenses !\n\n";
            $content .= "L'équipe CJES Africa";

            Mail::raw($content, function ($message) use ($notifiable, $subject) {
                $message->to($notifiable->email)
                    ->subject($subject);
            });

        } catch (\Exception $e) {
            // En cas d'erreur, logger mais ne pas faire de fallback
            \Log::error('Erreur Mail::raw dans RecompenseNotification: ' . $e->getMessage());
            // Ne rien retourner - Laravel gérera l'erreur
        }
    }

    public function toArray($notifiable)
    {
        return [
            'titre' => $this->actionTitre,
            'points' => $this->points,
            'lien' => $this->lien,
        ];
    }
}
