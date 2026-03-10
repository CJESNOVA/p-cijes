<x-emails-layout :subject="$subject">
    <h1>� Nouvelle récompense obtenue !</h1>
    
    <p>Félicitations 🎉</p>
    
    <p>Vous venez de gagner <strong>{{ $points }} points</strong> pour l'action : <strong>{{ $actionTitre }}</strong>.</p>
    
    <div style="text-align: center; background-color: #fef3c7; padding: 20px; border-radius: 8px; margin: 20px 0;">
        <h2 style="color: #92400e; margin: 0;">+{{ $points }} points</h2>
        <p style="color: #92400e; margin: 5px 0;">{{ $actionTitre }}</p>
    </div>
    
    <div style="text-align: center;">
        <a href="{{ $lien }}" class="button">Voir mes récompenses</a>
    </div>
    
    <h3>🏆 Vos récompenses</h3>
    <p>Continuez à participer activement pour :</p>
    <ul>
        <li>Gagner encore plus de points</li>
        <li>Débloquer des badges exclusifs</li>
        <li>Accéder à des opportunités premium</li>
        <li>Améliorer votre visibilité dans la communauté</li>
    </ul>
    
    <p>Chaque action compte dans votre parcours entrepreneurial avec CJES Africa !</p>
    
    <p>Cordialement,<br>
    L'équipe CJES Africa</p>
</x-emails-layout>
