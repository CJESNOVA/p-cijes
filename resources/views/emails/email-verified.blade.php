<x-emails-layout :subject="$subject">
    <h1>✅ Votre email a été confirmé !</h1>
    
    <p>Félicitations {{ $userName }} ! 🎉</p>
    
    <p>Votre adresse email a été vérifiée avec succès. Votre compte est maintenant entièrement activé.</p>
    
    <div style="text-align: center;">
        <a href="{{ $dashboardUrl }}" class="button">Commencer maintenant</a>
    </div>
    
    <h3>🎯 Prochaines étapes</h3>
    <p>Maintenant que votre compte est activé, vous pouvez :</p>
    <ul>
        <li>Accéder à votre tableau de bord personnel</li>
        <li>Compléter votre profil pour maximiser votre visibilité</li>
        <li>Découvrir nos opportunités de financement</li>
        <li>Rejoindre des événements et formations</li>
    </ul>
    
    <p>Nous sommes là pour vous accompagner dans votre parcours entrepreneurial.</p>
    
    <p>Bienvenue dans l'aventure CJES Africa !</p>
    
    <p>Cordialement,<br>
    L'équipe CJES Africa</p>
</x-emails-layout>
