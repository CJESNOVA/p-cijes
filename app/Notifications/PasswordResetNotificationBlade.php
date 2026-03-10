<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class PasswordResetNotificationBlade extends Notification implements ShouldQueue
{
    use Queueable;

    public $resetToken;
    public $userName;

    public function __construct($resetToken, $userName)
    {
        $this->resetToken = $resetToken;
        $this->userName = $userName;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $subject = '🔐 Réinitialisation de votre mot de passe';
        $resetUrl = route('resetPasswordView', ['token' => $this->resetToken]);

        return (new MailMessage)
            ->subject($subject)
            ->view('emails.password-reset', [
                'subject' => $subject,
                'userName' => $this->userName,
                'resetUrl' => $resetUrl,
            ]);
    }
}
