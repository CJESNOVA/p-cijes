<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Mail;

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
            ->view('emails.password-reset', [
                'user' => $notifiable,
                'userName' => $this->userName,
                'resetUrl' => $resetUrl,
                'token' => $this->resetToken
            ]);
    }
}
