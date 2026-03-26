<x-emails-layout :subject="$subject">
    <div class="confirmation-header">
        <h1>✅ Confirmation de modification de mot de passe</h1>
    </div>
    
    <h2>Bonjour {{ $userName ?? $user->name }} 👋</h2>
    
    <p>Nous vous confirmons que votre mot de passe a été modifié avec succès pour votre compte CJES Africa.</p>
    
    <div class="success-box">
        <h3>🎉 Modification réussie !</h3>
        <p>Votre mot de passe a été mis à jour et votre compte est maintenant sécurisé avec vos nouveaux identifiants.</p>
    </div>
    
    <h3>📋 Détails de la modification :</h3>
    <div class="details-grid">
        <div class="detail-item">
            <span class="detail-label">Date :</span>
            <span class="detail-value">{{ now()->format('d/m/Y à H:i') }}</span>
        </div>
        <div class="detail-item">
            <span class="detail-label">Navigateur :</span>
            <span class="detail-value">{{ request()->userAgent() ?? 'Non disponible' }}</span>
        </div>
    </div>
    
    <div class="cta-container">
        <a href="{{ $dashboardUrl ?? route('dashboard') }}" class="button">Accéder à mon compte</a>
    </div>
    
    <div class="security-tips">
        <h3>� Conseils de sécurité</h3>
        <div class="tips-list">
            <div class="tip-item">
                <span class="tip-icon">🤫</span>
                <span>Ne partagez jamais votre mot de passe avec qui que ce soit</span>
            </div>
            <div class="tip-item">
                <span class="tip-icon">🔐</span>
                <span>Utilisez des mots de passe différents pour chaque service</span>
            </div>
            <div class="tip-item">
                <span class="tip-icon">📱</span>
                <span>Activez l'authentification à deux facteurs si disponible</span>
            </div>
            <div class="tip-item">
                <span class="tip-icon">👁️</span>
                <span>Surveillez les activités suspectes sur votre compte</span>
            </div>
        </div>
    </div>
    
    <div class="warning-box">
        <p style="font-weight: 600; color: #dc3545; margin: 0;">
            ⚠️ Si vous n'êtes pas à l'origine de cette modification, contactez-nous immédiatement à support@cjes.africa
        </p>
    </div>
</x-emails-layout>

<style>
.confirmation-header h1 {
    background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    color: #28a745;
}

.success-box {
    background-color: #d4edda;
    border-left: 4px solid #28a745;
    padding: 20px;
    margin: 30px 0;
    border-radius: 4px;
    text-align: center;
}

.success-box h3 {
    color: #155724;
    margin-top: 0;
    font-size: 20px;
}

.details-grid {
    background-color: #f8f9fa;
    padding: 20px;
    border-radius: 8px;
    margin: 20px 0;
}

.detail-item {
    display: flex;
    justify-content: space-between;
    margin-bottom: 10px;
    padding-bottom: 10px;
    border-bottom: 1px solid #e9ecef;
}

.detail-item:last-child {
    margin-bottom: 0;
    padding-bottom: 0;
    border-bottom: none;
}

.detail-label {
    font-weight: 600;
    color: #495057;
}

.detail-value {
    color: #6c757d;
}

.cta-container {
    text-align: center;
    margin: 30px 0;
}

.security-tips {
    background-color: #f8f9fa;
    border-left: 4px solid #17a2b8;
    padding: 20px;
    margin: 30px 0;
    border-radius: 4px;
}

.security-tips h3 {
    color: #17a2b8;
    margin-top: 0;
    font-size: 18px;
}

.tips-list {
    margin-top: 15px;
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

.warning-box {
    background-color: #f8d7da;
    border-left: 4px solid #dc3545;
    padding: 15px;
    margin: 20px 0;
    border-radius: 4px;
}
</style>
