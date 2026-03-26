<x-emails-layout :subject="$subject">
    <div class="verified-header">
        <h1>🎉 Email vérifié avec succès</h1>
    </div>
    
    <h2>Félicitations {{ $userName ?? $user->name }} !</h2>
    
    <p>Votre adresse email a été vérifiée avec succès. Votre compte CJES Africa est maintenant entièrement activé et prêt à être utilisé.</p>
    
    <div class="verified-box">
        <h3>✅ Vérification réussie !</h3>
        <p>Vous pouvez maintenant profiter de toutes les fonctionnalités de la plateforme CJES Africa en toute sécurité.</p>
    </div>
    
    <div class="cta-container">
        <a href="{{ route('dashboard') }}" class="button">Accéder à mon tableau de bord</a>
    </div>
    
    <div class="next-steps">
        <h3>🚀 Prochaines étapes</h3>
        <ul>
            <li><strong>Complétez votre profil</strong> pour une meilleure expérience</li>
            <li><strong>Découvrez nos fonctionnalités</strong> dans votre tableau de bord</li>
            <li><strong>Rejoignez la communauté</strong> et connectez-vous avec d'autres membres</li>
            <li><strong>Explorez les ressources</strong> disponibles pour vous</li>
        </ul>
    </div>
    
    <h3>📊 Votre compte est maintenant :</h3>
    <div class="account-status">
        <div class="status-item">
            <span class="status-icon">✅</span>
            <span>Vérifié et sécurisé</span>
        </div>
        <div class="status-item">
            <span class="status-icon">✅</span>
            <span>Actif et fonctionnel</span>
        </div>
        <div class="status-item">
            <span class="status-icon">✅</span>
            <span>Prêt pour toutes les fonctionnalités</span>
        </div>
        <div class="status-item">
            <span class="status-icon">✅</span>
            <span>Éligible aux récompenses et badges</span>
        </div>
    </div>
    
    <div class="message-box">
        <p style="font-style: italic; color: #6c757d; margin: 0;">
            Merci de faire confiance à CJES Africa pour votre développement professionnel et personnel.
        </p>
    </div>
</x-emails-layout>

<style>
.verified-header h1 {
    background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    color: #007bff;
}

.verified-box {
    background-color: #d1ecf1;
    border-left: 4px solid #007bff;
    padding: 20px;
    margin: 30px 0;
    border-radius: 4px;
    text-align: center;
}

.verified-box h3 {
    color: #004085;
    margin-top: 0;
    font-size: 20px;
}

.next-steps {
    background-color: #f8f9fa;
    border-left: 4px solid #28a745;
    padding: 20px;
    margin: 30px 0;
    border-radius: 4px;
}

.next-steps h3 {
    color: #155724;
    margin-top: 0;
    font-size: 18px;
}

.account-status {
    background-color: #f8f9fa;
    padding: 20px;
    border-radius: 8px;
    margin: 20px 0;
}

.status-item {
    display: flex;
    align-items: center;
    margin-bottom: 10px;
    color: #666666;
}

.status-icon {
    margin-right: 10px;
    font-size: 16px;
}

.cta-container {
    text-align: center;
    margin: 30px 0;
}

.message-box {
    background-color: #f8f9fa;
    padding: 20px;
    border-radius: 6px;
    text-align: center;
    margin: 20px 0;
    border-top: 2px solid #007bff;
}
</style>
<p>Cordialement,<br>
    L'équipe CJES Africa</p>
