<x-emails-layout :subject="$subject">
    <div class="welcome-header">
        <h1>� Bienvenue chez CJES Africa !</h1>
    </div>
    
    <h2>Bonjour {{ $userName ?? $user->name }} !</h2>
    
    <p>Nous sommes ravis de vous accueillir dans la communauté CJES Africa ! Votre inscription a été réussie et vous êtes maintenant prêt à commencer votre voyage avec nous.</p>
    
    <div class="welcome-box">
        <h3>🌟 Votre aventure commence maintenant !</h3>
        <p>Rejoignez une communauté dynamique dédiée à l'excellence et au développement.</p>
    </div>
    
    <div class="cta-container">
        <a href="{{ $dashboardUrl ?? route('dashboard') }}" class="button">Commencer maintenant</a>
    </div>
    
    <h3>🚀 Ce qui vous attend chez CJES Africa :</h3>
    <div class="features-grid">
        <div class="feature-item">
            <h4>📚 Formation</h4>
            <p>Accédez à des ressources de formation de qualité</p>
        </div>
        <div class="feature-item">
            <h4>🤝 Communauté</h4>
            <p>Connectez-vous avec des professionnels passionnés</p>
        </div>
        <div class="feature-item">
            <h4>🏆 Récompenses</h4>
            <p>Gagnez des badges et récompenses pour vos accomplissements</p>
        </div>
        <div class="feature-item">
            <h4>📈 Développement</h4>
            <p>Suivez votre progression et atteignez vos objectifs</p>
        </div>
    </div>
    
    <h3>📋 Vos prochaines étapes :</h3>
    <ol class="steps-list">
        <li><strong>Vérifiez votre email</strong> si ce n'est pas déjà fait</li>
        <li><strong>Complétez votre profil</strong> pour personnaliser votre expérience</li>
        <li><strong>Explorez le tableau de bord</strong> pour découvrir toutes les fonctionnalités</li>
        <li><strong>Rejoignez les discussions</strong> et participez à la communauté</li>
    </ol>
    
    <div class="astuce-box">
        <strong>💡 Astuce :</strong> Consultez régulièrement votre tableau de bord pour ne manquer aucune opportunité et rester connecté avec la communauté.
    </div>
</x-emails-layout>

<style>
.welcome-header h1 {
    background: linear-gradient(135deg, #ff6b6b 0%, #ee5a24 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    color: #ff6b6b;
}

.welcome-box {
    background: linear-gradient(135deg, #ffeaa7 0%, #fdcb6e 100%);
    border-left: 4px solid #ff6b6b;
    padding: 20px;
    margin: 30px 0;
    border-radius: 4px;
    text-align: center;
}

.welcome-box h3 {
    color: #2d3436;
    margin-top: 0;
    font-size: 20px;
}

.features-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
    margin: 30px 0;
}

.feature-item {
    background-color: #f8f9fa;
    padding: 20px;
    border-radius: 6px;
    text-align: center;
}

.feature-item h4 {
    color: #ff6b6b;
    margin-top: 0;
    font-size: 18px;
}

.cta-container {
    text-align: center;
    margin: 30px 0;
}

.steps-list {
    color: #666666;
    line-height: 1.6;
    padding-left: 20px;
}

.steps-list li {
    margin-bottom: 10px;
}

.astuce-box {
    background-color: #e3f2fd;
    padding: 15px;
    border-radius: 6px;
    border-left: 4px solid #2196f3;
    margin: 20px 0;
}

@media only screen and (max-width: 600px) {
    .features-grid {
        grid-template-columns: 1fr;
    }
}
</style>
