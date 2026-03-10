<x-emails-layout :subject="$subject">
    <h1>✅ Votre mot de passe a été modifié</h1>
    
    <p>Bonjour {{ $userName }},</p>
    
    <p>Nous vous confirmons que votre mot de passe a été modifié avec succès.</p>
    
    <div style="text-align: center;">
        <a href="{{ $dashboardUrl }}" class="button">Accéder à mon compte</a>
    </div>
    
    <div class="security-info">
        <p>🔒 <strong>Sécurité :</strong> Si vous n'avez pas demandé cette modification, veuillez nous contacter immédiatement.</p>
    </div>
    
    <h3>🛡️ Conseils de sécurité</h3>
    <p>Pour protéger votre compte, nous vous recommandons de :</p>
    <ul>
        <li>Ne jamais partager vos identifiants</li>
        <li>Utiliser un mot de passe unique et complexe</li>
        <li>Activer l'authentification à deux facteurs si disponible</li>
        <li>Surveiller régulièrement l'activité de votre compte</li>
    </ul>
    
    <p>La sécurité de votre compte est notre priorité absolue.</p>
    
    <p>Cordialement,<br>
    L'équipe CJES Africa</p>
</x-emails-layout>
