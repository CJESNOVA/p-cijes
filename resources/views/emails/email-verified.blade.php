<x-emails-layout :subject="$subject">
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
                <h1 class="welcome-title">Votre email a été confirmé !</h1> 
                
                <p style="text-align: center; font-size: 16px;">
                    Félicitations <strong>{{ $userName ?? $user->name }}</strong>
                </p>
                
                <p style="text-align: center;">
                    Votre adresse email a été vérifiée avec succès. Votre compte est maintenant entièrement activé et vous pouvez profiter de toutes les fonctionnalités dans l'écosystème <strong>CJES</strong>. 
                </p>
                
                <div style="text-align: center; margin: 30px 0;">
                    <a href="{{ config('app.url') }}/dashboard" class="cta-button">Commencer maintenant</a>
                </div>
                
                <div class="features-box" style="background-color: #fcfcfc; border: 1px solid #eeeeee; border-radius: 12px; padding: 30px; margin: 30px 0;">
                    <p style="margin-top: 0; font-weight: bold; color: #2c3e50; text-align: center; font-size: 16px; margin-bottom: 25px;">
                        Voici ce que vous pouvez faire maintenant :
                    </p>
                    
                    <table border="0" cellpadding="0" cellspacing="0" width="100%" style="margin-bottom: 18px;">
                        <tr>
                            <td width="40" style="vertical-align: middle;">
                                <img src="https://img.icons8.com/material-rounded/48/84E2AF/user-male-circle.png" alt="Profil" width="22" height="22" style="display: block;">
                            </td>
                            <td style="vertical-align: middle; font-size: 15px; color: #333333;">
                                Compléter votre <strong>Profil</strong> 
                            </td>
                        </tr>
                    </table>

                    <table border="0" cellpadding="0" cellspacing="0" width="100%" style="margin-bottom: 18px;">
                        <tr>
                            <td width="40" style="vertical-align: middle;">
                                <img src="https://img.icons8.com/fluent-systems-filled/32/84E2AF/combo-chart.png" alt="Tableau de bord" width="22" height="22" style="display: block;">
                            </td>
                            <td style="vertical-align: middle; font-size: 15px; color: #333333;">
                                Explorer votre <strong>Tableau de bord</strong>
                            </td>
                        </tr>
                    </table>

                    <table border="0" cellpadding="0" cellspacing="0" width="100%" style="margin-bottom: 18px;">
                        <tr>
                            <td width="40" style="vertical-align: middle;">
                                <img src="https://img.icons8.com/fluent-systems-filled/32/84E2AF/organization.png" alt="Entreprises" width="22" height="22" style="display: block;">
                            </td>
                            <td style="vertical-align: middle; font-size: 15px; color: #333333;">
                                Ajouter vos <strong>Entreprises</strong> 
                            </td>
                        </tr>
                    </table>

                    <table border="0" cellpadding="0" cellspacing="0" width="100%;">
                        <tr>
                            <td width="40" style="vertical-align: middle;">
                                <img src="https://img.icons8.com/fluent-systems-filled/32/84E2AF/identification-documents.png" alt="Membre" width="22" height="22" style="display: block;">
                            </td>
                            <td style="vertical-align: middle; font-size: 15px; color: #333333;">
                                Devenir <strong>Membre</strong>
                            </td>
                        </tr>
                    </table>
                </div>
                
                <p style="text-align: center; font-size: 14px; color: #666;">
                    Nous sommes là pour vous accompagner dans votre parcours entrepreneurial. 
                </p>
                
                <p style="text-align: center; font-weight: bold; margin-top: 20px;">
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
                <p style="margin: 0;">Vous recevez cet email car vous avez créé un compte sur CJES Africa.</p>
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

.features-box {
    background-color: #fcfcfc;
    border: 1px solid #eeeeee;
    border-radius: 8px;
    padding: 25px;
    margin: 30px 0;
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

</x-emails-layout>
