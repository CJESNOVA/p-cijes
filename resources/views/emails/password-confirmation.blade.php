<!DOCTYPE html>
<html lang="fr" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Confirmation de modification de mot de passe</title>
    <style>
        /* CONFIGURATION GÉNÉRALE */
        body {
            margin: 0;
            padding: 0;
            width: 100% !important;
            height: 100% !important;
            background-color: #f4f7f6;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            -webkit-font-smoothing: antialiased;
        }
        table {
            border-collapse: collapse;
            border-spacing: 0;
        }
        img {
            border: 0;
            outline: none;
            text-decoration: none;
            -ms-interpolation-mode: bicubic;
        }
        
        /* STYLES PRINCIPAUX */
        .email-container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        
        .header {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            padding: 40px 30px;
            text-align: center;
        }
        
        .header h1 {
            color: #ffffff;
            margin: 0;
            font-size: 28px;
            font-weight: 600;
        }
        
        .content {
            padding: 40px 30px;
        }
        
        .content h2 {
            color: #333333;
            font-size: 24px;
            margin-bottom: 20px;
            font-weight: 600;
        }
        
        .content p {
            color: #666666;
            font-size: 16px;
            line-height: 1.6;
            margin-bottom: 20px;
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
        
        .footer {
            background-color: #f8f9fa;
            padding: 30px;
            text-align: center;
            border-top: 1px solid #e9ecef;
        }
        
        .footer p {
            color: #6c757d;
            font-size: 14px;
            margin: 5px 0;
        }
        
        @media only screen and (max-width: 600px) {
            .email-container {
                margin: 10px;
                border-radius: 0;
            }
            
            .header, .content, .footer {
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <table cellpadding="0" cellspacing="0" border="0" width="100%">
        <tr>
            <td align="center" style="background-color: #f4f7f6; padding: 40px 20px;">
                <table cellpadding="0" cellspacing="0" border="0" width="600" class="email-container">
                    <!-- HEADER -->
                    <tr>
                        <td class="header">
                            <h1>✅ Confirmation de modification de mot de passe</h1>
                        </td>
                    </tr>
                    
                    <!-- CONTENT -->
                    <tr>
                        <td class="content">
                            <h2>Bonjour {{ $userName ?? $user->name }} 👋</h2>
                            
                            <p>Nous vous confirmons que votre mot de passe a été modifié avec succès pour votre compte CJES Africa.</p>
                            
                            <div class="success-box">
                                <h3>🎉 Modification réussie !</h3>
                                <p>Votre mot de passe a été mis à jour et votre compte est maintenant sécurisé avec vos nouveaux identifiants.</p>
                            </div>
                            
                            <h3>📋 Détails de la modification :</h3>
                            <ul style="color: #666666; line-height: 1.6;">
                                <li><strong>Date :</strong> {{ now()->format('d/m/Y à H:i') }}</li>
                                <li><strong>Adresse IP :</strong> {{ request()->ip() ?? 'Non disponible' }}</li>
                                <li><strong>Navigateur :</strong> {{ request()->userAgent() ?? 'Non disponible' }}</li>
                            </ul>
                            
                            <div class="security-tips">
                                <h3>🔒 Conseils de sécurité</h3>
                                <ul style="color: #666666; line-height: 1.6;">
                                    <li>Ne partagez jamais votre mot de passe avec qui que ce soit</li>
                                    <li>Utilisez des mots de passe différents pour chaque service</li>
                                    <li>Activez l'authentification à deux facteurs si disponible</li>
                                    <li>Surveillez les activités suspectes sur votre compte</li>
                                </ul>
                            </div>
                            
                            <p style="font-weight: 600; color: #dc3545;">
                                ⚠️ Si vous n'êtes pas à l'origine de cette modification, contactez-nous immédiatement à support@cjes.africa
                            </p>
                        </td>
                    </tr>
                    
                    <!-- FOOTER -->
                    <tr>
                        <td class="footer">
                            <p><strong>Votre sécurité est notre priorité !</strong></p>
                            <p>L'équipe CJES Africa</p>
                            <p style="font-size: 12px;">
                                📧 Contact : support@cjes.africa<br>
                                🌐 Site : {{ config('app.url') }}
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
