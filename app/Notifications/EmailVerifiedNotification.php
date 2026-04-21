<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Mail;

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
            ->subject('🎉 Email vérifié avec succès')
            ->view('emails.email-verified', [
                'user' => $notifiable,
                'userName' => $this->userName,
            ]);
    }
}
