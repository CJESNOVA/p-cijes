<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;

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

        // Version simple avec Mail::raw (comme les autres notifications)
        $content = "Bonjour {$this->userName},\n\n";
        $content .= "Vous avez demandé la réinitialisation de votre mot de passe.\n\n";
        $content .= "Cliquez sur le lien ci-dessous pour définir un nouveau mot de passe :\n";
        $content .= $resetUrl . "\n\n";
        $content .= "⚠️ Ce lien expirera dans 60 minutes pour des raisons de sécurité.\n\n";
        $content .= "Si vous n'avez pas demandé cette réinitialisation, vous pouvez ignorer cet email.\n\n";
        $content .= "Cordialement,\n";
        $content .= "L'équipe CJES Africa";

        Mail::raw($content, function ($message) use ($notifiable, $subject) {
            $message->to($notifiable->email)
                ->subject($subject);
        });
    }
}
