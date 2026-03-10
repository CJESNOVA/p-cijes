<x-emails-layout :subject="$subject">
    <h1>🔐 Réinitialisation de votre mot de passe</h1>
    
    <p>Bonjour {{ $userName }},</p>
    
    <p>Vous avez demandé la réinitialisation de votre mot de passe pour votre compte CJES Africa.</p>
    
    <p>Cliquez sur le bouton ci-dessous pour définir un nouveau mot de passe :</p>
    
    <div style="text-align: center;">
        <a href="{{ $resetUrl }}" class="button">Réinitialiser mon mot de passe</a>
    </div>
    
    <div class="security-info">
        <p>⚠️ <strong>Important :</strong> Ce lien expirera dans 60 minutes pour des raisons de sécurité.</p>
    </div>
    
    <p>Si vous n'avez pas demandé cette réinitialisation, vous pouvez ignorer cet email en toute sécurité.</p>
    
    <h3>💡 Conseils de sécurité</h3>
    <p>Pour sécuriser votre compte, choisissez un mot de passe contenant :</p>
    <ul>
        <li>Au moins 8 caractères</li>
        <li>Une lettre majuscule et une minuscule</li>
        <li>Un chiffre et un caractère spécial (@$!%*?&)</li>
    </ul>
    
    <p>Cordialement,<br>
    L'équipe CJES Africa</p>
</x-emails-layout>
