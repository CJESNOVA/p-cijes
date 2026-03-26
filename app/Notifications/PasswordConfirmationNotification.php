<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class PasswordConfirmationNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public string $userName)
    {
        $this->onQueue('emails');
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('✅ Confirmation de modification de mot de passe')
            ->view('emails.password-confirmation', [
                'user' => $notifiable,
                'userName' => $this->userName,
            ]);
    }
}
