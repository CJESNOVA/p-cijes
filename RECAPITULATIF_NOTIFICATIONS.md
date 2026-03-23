# RÉCAPITULATIF DES NOTIFICATIONS EMAIL - CJES Africa

Ce fichier contient tous les textes et contenus des notifications email envoyées par l'application CJES Africa.

---

## 1. WelcomeNotification (Notification de bienvenue)

**Fichier :** `app/Notifications/WelcomeNotification.php`

**Sujet :** 🎉 Bienvenue sur CJES Africa !

**Contenu :**
```
Bonjour [userName] 👋

Bienvenue dans la communauté CJES Africa ! Nous sommes ravis de vous compter parmi nous.

Votre compte a été créé avec succès. Vous pouvez maintenant :
📊 Accéder à votre tableau de bord
🏢 Gérer vos entreprises
💰 Suivre vos cotisations
🎯 Participer aux diagnostics

Accédez à votre tableau de bord : [URL du dashboard]

Si vous avez des questions, n'hésitez pas à nous contacter.

Cordialement,
L'équipe CJES Africa
```

---

## 2. WelcomeNotificationBlade (Version alternative de bienvenue)

**Fichier :** `app/Notifications/WelcomeNotificationBlade.php`

**Sujet :** 🎉 Bienvenue sur CJES Africa !

**Contenu :**
```
Bonjour [userName],

Bienvenue dans la communauté CJES Africa !

Accédez à votre tableau de bord : [URL du dashboard]

Si vous avez des questions, n'hésitez pas à nous contacter.

Cordialement,
L'équipe CJES Africa
```

---

## 3. EmailVerifiedNotification (Confirmation de vérification d'email)

**Fichier :** `app/Notifications/EmailVerifiedNotification.php`

**Sujet :** ✅ Votre email a été confirmé !

**Contenu :**
```
Félicitations [userName] ! 🎉

Votre adresse email a été vérifiée avec succès.

Votre compte est maintenant entièrement activé et vous pouvez profiter de toutes les fonctionnalités de CJES Africa.

Voici ce que vous pouvez faire maintenant :
🚀 Compléter votre profil membre
📊 Explorer votre tableau de bord
🏢 Ajouter vos entreprises
💰 Gérer vos cotisations

Commencez maintenant : [URL du dashboard]

Nous sommes là pour vous accompagner dans votre parcours entrepreneurial.

Bienvenue dans l'aventure CJES Africa !
L'équipe CJES Africa
```

---

## 4. PasswordResetNotification (Réinitialisation de mot de passe)

**Fichier :** `app/Notifications/PasswordResetNotification.php`

**Sujet :** 🔐 Réinitialisation de votre mot de passe

**Contenu :**
```
Bonjour [userName] 👋

Vous avez demandé la réinitialisation de votre mot de passe.

Cliquez sur le lien ci-dessous pour définir un nouveau mot de passe :
[URL de réinitialisation]

⚠️ Ce lien expirera dans 60 minutes pour des raisons de sécurité.

Si vous n'avez pas demandé cette réinitialisation, vous pouvez ignorer cet email.

Pour sécuriser votre compte, choisissez un mot de passe contenant :
• Au moins 8 caractères
• Une lettre majuscule et une minuscule
• Un chiffre et un caractère spécial (@$!%*?&)

📧 Contactez-nous à support@cjes.africa si vous avez des questions.

Sécurité avant tout !
L'équipe CJES Africa
```

---

## 5. PasswordResetNotificationBlade (Version alternative de réinitialisation)

**Fichier :** `app/Notifications/PasswordResetNotificationBlade.php`

**Sujet :** 🔐 Réinitialisation de votre mot de passe

**Contenu :**
```
Bonjour [userName],

Vous avez demandé la réinitialisation de votre mot de passe.

Cliquez sur le lien ci-dessous pour définir un nouveau mot de passe :
[URL de réinitialisation]

⚠️ Ce lien expirera dans 60 minutes pour des raisons de sécurité.

Si vous n'avez pas demandé cette réinitialisation, vous pouvez ignorer cet email.

Cordialement,
L'équipe CJES Africa
```

---

## 6. PasswordResetConfirmationNotification (Confirmation de modification de mot de passe)

**Fichier :** `app/Notifications/PasswordResetConfirmationNotification.php`

**Sujet :** ✅ Votre mot de passe a été modifié

**Contenu :**
```
Bonjour [userName] 👋

Votre mot de passe a été modifié avec succès.

Cette modification a été effectuée récemment sur votre compte CJES Africa.

Si vous êtes à l'origine de cette modification, tout est en ordre.

Si vous n'avez pas demandé cette modification, veuillez :
🔐 Changer immédiatement votre mot de passe
📧 Nous contacter à support@cjes.africa
🔒 Vérifier l'activité de votre compte

Accédez à votre compte : [URL du dashboard]

Conseils de sécurité :
• Utilisez un mot de passe unique et complexe
• Ne partagez jamais vos identifiants
• Activez l'authentification à deux facteurs si disponible

Votre sécurité est notre priorité
L'équipe CJES Africa
```

---

## 7. RecompenseNotification (Notification de récompense)

**Fichier :** `app/Notifications/RecompenseNotification.php`

**Sujet :** 🎁 Nouvelle récompense obtenue !

**Contenu :**
```
Félicitations 🎉

Vous venez de gagner **[points] points** pour l'action : **[actionTitre]**.

Voir vos récompenses : [lien vers récompenses]

Continuez à participer pour gagner encore plus de récompenses !

L'équipe CJES Africa
```

---

## INFORMATIONS TECHNIQUES

- **Langue :** Français
- **Format :** Texte brut (Mail::raw)
- **Variables dynamiques :** [userName], [URL], [points], [actionTitre], etc.
- **Support de queue :** Toutes les notifications implémentent ShouldQueue
- **Gestion d'erreurs :** Try-catch avec logging dans toutes les notifications

## NOTES

- Il existe deux versions pour certaines notifications (Welcome et PasswordReset) : une version standard et une version "Blade"
- La notification Recompense utilise également le canal database en plus du mail
- Les emails utilisent des émojis pour rendre le contenu plus visuel et amical
- L'expéditeur est toujours "L'équipe CJES Africa"
- L'email de support mentionné est : support@cjes.africa

---

**Date de génération :** 18 mars 2026  
**Projet :** CJES Africa - p-cijes  
**Total de notifications :** 7
