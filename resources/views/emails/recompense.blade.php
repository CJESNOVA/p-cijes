<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>🎉 Récompense obtenue</title>
    <style>
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            background-color: #f9fafb;
            color: #333;
            padding: 30px;
        }
        .container {
            max-width: 600px;
            margin: auto;
            background: white;
            border-radius: 8px;
            padding: 25px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        }
        h2 {
            color: #1e3a8a;
        }
        ul {
            list-style: none;
            padding-left: 0;
        }
        li {
            margin-bottom: 5px;
        }
        a {
            color: #0ea5e9;
            text-decoration: none;
        }
        a:hover {
            text-decoration: underline;
        }
        .footer {
            margin-top: 25px;
            font-size: 12px;
            color: #999;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Bonjour {{ $membre->prenom ?? 'Utilisateur' }} {{ $membre->nom ?? 'Utilisateur' }},</h2>

        <p>🎉 Félicitations ! Vous venez de recevoir une nouvelle récompense.</p>

        <ul>
            <li><strong>Action :</strong> {{ $action->titre ?? 'N/A' }}</li>
            <li><strong>Points :</strong> {{ $action->point ?? 0 }}</li>
            <li><strong>Date :</strong> {{ optional($recompense->updated_at)->format('d/m/Y H:i') ?? 'N/A' }}</li>
        </ul>

        <p>👉 Consultez vos récompenses ici :
            <a href="{{ url('/bons/mes-recompenses') }}">Mes Récompenses</a>
        </p>

        <p>Merci pour votre engagement 👏</p>

        <div class="footer">
            — L’équipe CIJES Africa
        </div>
    </div>
</body>
</html>
