<x-emails-layout :subject="$subject">
    <div class="recompense-header">
        <h1>🏆 Nouvelle récompense obtenue !</h1>
    </div>
    
    <h2>Félicitations {{ $userName ?? $user->name }} !</h2>
    
    <p>Votre engagement et vos accomplissements sur la plateforme CJES Africa ont été remarqués ! Vous venez de débloquer une nouvelle récompense.</p>
    
    <div class="recompense-box">
        <span class="badge-icon">{{ $recompense['icon'] ?? '🏅' }}</span>
        <h3>{{ $recompense['titre'] ?? 'Badge d\'excellence' }}</h3>
        <p>{{ $recompense['description'] ?? 'Félicitations pour cette accomplishment remarquable !' }}</p>
        
        @if(isset($recompense['points']))
        <p class="points-gagnés">+{{ $recompense['points'] }} points gagnés !</p>
        @endif
    </div>
    
    <h3>📊 Vos statistiques actuelles :</h3>
    <div class="stats-grid">
        <div class="stat-item">
            <span class="stat-number">{{ $stats['total_points'] ?? '0' }}</span>
            <span class="stat-label">Points totaux</span>
        </div>
        <div class="stat-item">
            <span class="stat-number">{{ $stats['badges'] ?? '0' }}</span>
            <span class="stat-label">Badges obtenus</span>
        </div>
        <div class="stat-item">
            <span class="stat-number">{{ $stats['niveau'] ?? '1' }}</span>
            <span class="stat-label">Niveau actuel</span>
        </div>
    </div>
    
    @if(isset($recompense['progression']))
    <h3>📈 Progression vers le niveau suivant :</h3>
    <div class="progress-bar">
        <div class="progress-fill" style="width: {{ $recompense['progression'] }}%"></div>
    </div>
    <p class="progression-text">
        {{ $recompense['progression'] }}% vers le niveau {{ ($stats['niveau'] ?? 1) + 1 }}
    </p>
    @endif
    
    <div class="cta-container">
        <a href="{{ $lien }}" class="button">Voir toutes mes récompenses</a>
    </div>
    
    <div class="next-rewards">
        <h3>🎯 Prochaines récompenses à débloquer :</h3>
        <ul>
            @foreach($nextRewards ?? [] as $reward)
            <li><strong>{{ $reward['titre'] }}</strong> - {{ $reward['description'] }} ({{ $reward['points_required'] }} points)</li>
            @endforeach
        </ul>
    </div>
    
    <div class="conseil-box">
        <strong>💡 Continuez comme ça !</strong> Chaque action sur la plateforme vous rapproche de nouvelles récompenses et d'une reconnaissance accrue au sein de la communauté.
    </div>
</x-emails-layout>

<style>
.recompense-header h1 {
    background: linear-gradient(135deg, #ffd700 0%, #ffb347 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    color: #ffd700;
}

.recompense-box {
    background: linear-gradient(135deg, #fff3cd 0%, #ffeaa7 100%);
    border-left: 4px solid #ffd700;
    padding: 25px;
    margin: 30px 0;
    border-radius: 8px;
    text-align: center;
    position: relative;
}

.badge-icon {
    font-size: 48px;
    margin-bottom: 15px;
    display: block;
}

.recompense-box h3 {
    color: #856404;
    margin-top: 0;
    font-size: 22px;
    font-weight: 700;
}

.points-gagnés {
    font-size: 18px;
    font-weight: 600;
    color: #856404;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 15px;
    margin: 30px 0;
}

.stat-item {
    background-color: #f8f9fa;
    padding: 20px;
    border-radius: 8px;
    text-align: center;
    border: 2px solid #e9ecef;
}

.stat-number {
    font-size: 24px;
    font-weight: 700;
    color: #ffd700;
    display: block;
}

.stat-label {
    font-size: 14px;
    color: #666666;
    margin-top: 5px;
}

.progress-bar {
    background-color: #e9ecef;
    border-radius: 10px;
    height: 20px;
    overflow: hidden;
    margin: 20px 0;
}

.progress-fill {
    background: linear-gradient(90deg, #ffd700 0%, #ffb347 100%);
    height: 100%;
    border-radius: 10px;
    transition: width 0.3s ease;
}

.progression-text {
    text-align: center;
    color: #666666;
    margin: 10px 0;
}

.cta-container {
    text-align: center;
    margin: 30px 0;
}

.next-rewards {
    background-color: #f8f9fa;
    border-left: 4px solid #28a745;
    padding: 20px;
    margin: 30px 0;
    border-radius: 4px;
}

.next-rewards h3 {
    color: #155724;
    margin-top: 0;
    font-size: 18px;
}

.conseil-box {
    background-color: #fff3cd;
    padding: 15px;
    border-radius: 6px;
    border-left: 4px solid #ffd700;
    margin: 20px 0;
}

@media only screen and (max-width: 600px) {
    .stats-grid {
        grid-template-columns: 1fr;
    }
}
</style>
