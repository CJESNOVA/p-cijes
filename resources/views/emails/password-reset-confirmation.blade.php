<center>
    <table border="0" cellpadding="0" cellspacing="0" width="100%" class="container">
        
        <!-- HEADER -->
        <tr>
            <td class="header" valign="top" style="padding: 10px;; margin:0; line-height:0; font-size:0; background-color:#ffffff;">
                <img src="https://togo.cjes.africa/wp-content/uploads/2026/03/CJES-AFRICA-MAIL-scaled.png"
                     alt="CJES Africa"
                     width="260"
                     style="display:block; margin:0 auto; padding:0; border:0; width:260px; max-width:100%; height:auto;">
            </td>
        </tr>
        
        <!-- CONTENU -->
        <tr>
            <td class="content">
                <h1 class="welcome-title" style="color: #84E2AF; text-align: center; font-size: 24px; margin-bottom: 25px; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;">
                    Mot de passe modifié <span style="font-size: 26px; font-family: Arial, sans-serif; font-weight: bold;">✔</span>
                </h1>                    
                
                <p style="text-align: center; font-size: 16px;">
                    Bonjour <strong>{{ $userName ?? $user->name }}</strong>,
                </p>
                
                <div class="info-box">
                    <p style="margin: 0; color: #2c3e50;">
                        Votre mot de passe a été modifié avec succès. Vous pouvez désormais vous connecter à votre compte en utilisant vos nouveaux identifiants.
                    </p>
                </div>
                
                <div style="text-align: center; margin: 30px 0;">
                    <a href="{{ config('app.url') }}/dashboard" class="cta-button">Se connecter maintenant</a>
                </div>
                
                <p style="text-align: center; font-size: 14px; color: #666; margin-top: 25px;">
                    Si vous n'êtes pas à l'origine de cette modification, veuillez contacter immédiatement notre support technique.
                </p>
                
                <p style="text-align: center; font-weight: bold; margin-top: 30px;">
                    Cordialement,<br>
                    <span style="color: #2c3e50;">L'équipe CJES</span>
                </p>

                <table border="0" cellpadding="0" cellspacing="0" width="100%">
                    <tr>
                        <td align="center">
                            <a href="https://www.facebook.com/EcoCJES" style="display: inline-block; margin: 0 4px;"><img src="https://img.icons8.com/fluent-systems-filled/24/84E2AF/facebook-new.png" alt="Facebook" width="24" height="24"></a>
                            <a href="https://www.instagram.com/eco_cjes/?hl=fr" style="display: inline-block; margin: 0 4px;"><img src="https://img.icons8.com/fluent-systems-filled/24/84E2AF/instagram-new.png" alt="Instagram" width="24" height="24"></a>
                            <a href="mailto:support@cjes.africa" style="display: inline-block; margin: 0 4px;"><img src="https://img.icons8.com/fluent-systems-filled/24/84E2AF/email.png" alt="E-mail" width="24" height="24"></a>
                            <a href="https://cijes.cjes.africa/" style="display: inline-block; margin: 0 4px;"><img src="https://img.icons8.com/fluent-systems-filled/24/84E2AF/globe.png" alt="Web" width="24" height="24"></a>
                            <a href="https://wa.me/0022890700002" style="display: inline-block; margin: 0 4px;"><img src="https://img.icons8.com/fluent-systems-filled/24/84E2AF/whatsapp.png" alt="WhatsApp" width="24" height="24"></a>
                            <a href="https://www.linkedin.com/company/cjes/?viewAsMember=true" style="display: inline-block; margin: 0 4px;"><img src="https://img.icons8.com/fluent-systems-filled/24/84E2AF/linkedin.png" alt="LinkedIn" width="24" height="24"></a>
                            <a href="https://www.youtube.com/@lacjet" style="display: inline-block; margin: 0 4px;"><img src="https://img.icons8.com/fluent-systems-filled/24/84E2AF/youtube-play.png" alt="YouTube" width="24" height="24"></a>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        
        <!-- FOOTER -->
        <tr>
            <td class="footer">
                <p style="margin: 0;">Vous recevez cet email car une demande de sécurité a été initiée.</p>
                <p style="margin: 5px 0 0 0;">Support : <a href="mailto:support@cjes.africa">support@cjes.africa</a></p> 
            </td>
        </tr>
        
    </table>
</center>

<style>
.container {
    max-width: 600px;
    margin: 20px auto;
    background-color: #ffffff;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 4px 15px rgba(0,0,0,0.05);
}

.header {
    padding: 30px 0;
    text-align: center;
}

.content {
    padding: 0 40px 40px 40px;
    color: #333333;
    line-height: 1.6;
}

.footer {
    background-color: #2c3e50;
    color: #ecf0f1;
    padding: 30px 40px;
    text-align: center;
    font-size: 12px;
}

.footer a {
    color: #84E2AF;
    text-decoration: none;
}

.welcome-title {
    color: #84E2AF;
    text-align: center;
    font-size: 24px;
    margin-bottom: 25px;
}

.cta-button {
    display: inline-block;
    background-color: #84E2AF;
    color: #ffffff !important;
    padding: 15px 35px;
    text-decoration: none;
    border-radius: 50px;
    font-weight: bold;
    font-size: 16px;
    animation: pulsate 2s infinite ease-in-out;
    box-shadow: 0 4px 10px rgba(132, 226, 175, 0.3);
}

.info-box {
    background-color: #f0fbf5;
    border-radius: 8px;
    padding: 20px;
    margin: 25px 0;
    text-align: center;
    border: 1px solid #d4f2e1;
}

@keyframes pulsate {
    0% { transform: scale(1); box-shadow: 0 4px 10px rgba(132, 226, 175, 0.3); }
    50% { transform: scale(1.03); box-shadow: 0 6px 15px rgba(132, 226, 175, 0.5); }
    100% { transform: scale(1); box-shadow: 0 4px 10px rgba(132, 226, 175, 0.3); }
}

@media only screen and (max-width: 600px) {
    .container { width: 95% !important; margin: 10px auto !important; }
    .content { padding: 0 20px 30px 20px !important; }
    .cta-button { width: 80% !important; text-align: center; }
}
</style>
