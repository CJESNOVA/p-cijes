<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;

class WelcomeNotificationBlade extends Notification implements ShouldQueue
{
    use Queueable;

    public $userName;

    public function __construct($userName)
    {
        $this->userName = $userName;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $subject = '🎉 Bienvenue sur CJES Africa !';
        $dashboardUrl = route('dashboard');

        // Version simple avec Mail::raw
        $content = "Bonjour {$this->userName},\n\n";
        $content .= "Bienvenue dans la communauté CJES Africa !\n\n";
        $content .= "Accédez à votre tableau de bord : {$dashboardUrl}\n\n";
        $content .= "Si vous avez des questions, n'hésitez pas à nous contacter.\n\n";
        $content .= "Cordialement,\n";
        $content .= "L'équipe CJES Africa";

        Mail::raw($content, function ($message) use ($notifiable, $subject) {
            $message->to($notifiable->email)
                ->subject($subject);
        });
    }
}
