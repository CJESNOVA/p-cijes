<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

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

        return (new MailMessage)
            ->subject($subject)
            ->view('emails.welcome', [
                'subject' => $subject,
                'userName' => $this->userName,
                'dashboardUrl' => $dashboardUrl,
            ]);
    }
}
