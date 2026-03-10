<x-emails-layout :subject="$subject">
    <h1>🎉 Bienvenue sur CJES Africa !</h1>
    
    <p>Bonjour {{ $userName }},</p>
    
    <p>Nous sommes ravis de vous accueillir dans la communauté CJES Africa ! Votre compte a été créé avec succès.</p>
    
    <p>CJES Africa est la plateforme qui accompagne les entrepreneurs et porteurs de projets dans leur développement professionnel.</p>
    
    <div style="text-align: center;">
        <a href="{{ $dashboardUrl }}" class="button">Accéder à mon tableau de bord</a>
    </div>
    
    <h3>🚀 Que pouvez-vous faire maintenant ?</h3>
    <ul>
        <li>Compléter votre profil professionnel</li>
        <li>Découvrir nos services d'accompagnement</li>
        <li>Rejoindre notre communauté d'entrepreneurs</li>
        <li>Accéder à des ressources exclusives</li>
    </ul>
    
    <p>Si vous avez des questions, n'hésitez pas à nous contacter à <a href="mailto:support@cjes.africa">support@cjes.africa</a>.</p>
    
    <p>Nous sommes là pour vous accompagner dans votre parcours entrepreneurial.</p>
    
    <p>Bienvenue dans l'aventure CJES Africa !</p>
    
    <p>Cordialement,<br>
    L'équipe CJES Africa</p>
</x-emails-layout>
