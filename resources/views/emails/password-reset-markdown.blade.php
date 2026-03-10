@component('mail::message')
# 🔐 Réinitialisation de votre mot de passe

Bonjour {{ $userName }},

Vous avez demandé la réinitialisation de votre mot de passe pour votre compte CJES Africa.

Cliquez sur le bouton ci-dessous pour définir un nouveau mot de passe :

@component('mail::button', ['url' => $resetUrl])
Réinitialiser mon mot de passe
@endcomponent

> ⚠️ **Important :** Ce lien expirera dans 60 minutes pour des raisons de sécurité.

Si vous n'avez pas demandé cette réinitialisation, vous pouvez ignorer cet email en toute sécurité.

### 💡 Conseils de sécurité

Pour sécuriser votre compte, choisissez un mot de passe contenant :
- Au moins 8 caractères
- Une lettre majuscule et une minuscule
- Un chiffre et un caractère spécial (@$!%*?&)

Cordialement,  
L'équipe CJES Africa

@endcomponent
