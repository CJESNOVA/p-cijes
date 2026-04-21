<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Mail;

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
            ->subject('✅ Confirmation de modification de mot de passe')
            ->view('emails.password-reset-confirmation', [
                'user' => $notifiable,
                'userName' => $this->userName,
                'dashboardUrl' => route('dashboard')
            ]);
    }
}
