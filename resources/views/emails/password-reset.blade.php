<x-emails-layout :subject="$subject">
    <div class="reset-header">
        <h1>🔐 Réinitialisation de votre mot de passe</h1>
    </div>
    
    <h2>Bonjour {{ $userName ?? $user->name }} 👋</h2>
    
    <p>Vous avez demandé la réinitialisation de votre mot de passe pour votre compte CJES Africa.</p>
    
    <p>Cliquez sur le bouton ci-dessous pour définir un nouveau mot de passe :</p>
    
    <div class="cta-container">
        <a href="{{ $resetUrl }}" class="button">Réinitialiser mon mot de passe</a>
    </div>
    
    <div class="url-fallback">
        <p style="font-size: 14px; color: #868e96; margin: 0;">
            Ou copiez-collez ce lien dans votre navigateur :
        </p>
        <p style="word-break: break-all; color: #007bff; margin: 5px 0;">
            {{ $resetUrl }}
        </p>
    </div>
    
    <div class="security-info">
        <h3>🔒 Informations de sécurité</h3>
        <p><strong>Ce lien expirera dans 60 minutes</strong> pour des raisons de sécurité.</p>
        <p>Si vous n'avez pas demandé cette réinitialisation, vous pouvez ignorer cet email en toute sécurité.</p>
    </div>
    
    <h3>💡 Conseils pour un mot de passe sécurisé :</h3>
    <div class="password-tips">
        <div class="tip-item">
            <span class="tip-icon">📏</span>
            <span>Au moins 8 caractères</span>
        </div>
        <div class="tip-item">
            <span class="tip-icon">🔤</span>
            <span>Une lettre majuscule et une minuscule</span>
        </div>
        <div class="tip-item">
            <span class="tip-icon">🔢</span>
            <span>Un chiffre et un caractère spécial (@$!%*?&)</span>
        </div>
        <div class="tip-item">
            <span class="tip-icon">🚫</span>
            <span>Évitez les informations personnelles évidentes</span>
        </div>
    </div>
</x-emails-layout>

<style>
.reset-header h1 {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    color: #667eea;
}

.cta-container {
    text-align: center;
    margin: 30px 0;
}

.url-fallback {
    background-color: #f8f9fa;
    padding: 15px;
    border-radius: 6px;
    margin: 20px 0;
    text-align: center;
}

.security-info {
    background-color: #f8f9fa;
    border-left: 4px solid #667eea;
    padding: 20px;
    margin: 30px 0;
    border-radius: 4px;
}

.security-info h3 {
    color: #667eea;
    margin-top: 0;
    font-size: 18px;
}

.password-tips {
    background-color: #f8f9fa;
    padding: 20px;
    border-radius: 8px;
    margin: 20px 0;
}

.tip-item {
    display: flex;
    align-items: center;
    margin-bottom: 12px;
    color: #666666;
}

.tip-item:last-child {
    margin-bottom: 0;
}

.tip-icon {
    margin-right: 12px;
    font-size: 18px;
}
</style>
