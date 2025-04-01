<?php
/**
 * Classe pour l'envoi d'emails
 */
class Mailer {
    private $from = "noreply@monsite.com";
    
    /**
     * Envoie un email
     * @param string $to Adresse email du destinataire
     * @param string $subject Sujet de l'email
     * @param string $message Contenu de l'email
     * @return bool Succès de l'envoi
     */
    public function send($to, $subject, $message) {
        $headers = "From: " . $this->from . "\r\n";
        $headers .= "Reply-To: " . $this->from . "\r\n";
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
        
        // Dans un environnement de production, utilisez la fonction mail()
        // ou un service tiers comme PHPMailer, SendGrid, etc.
        // Pour ce projet, nous allons simuler l'envoi d'email en l'affichant
        
        echo "<div style='border: 1px solid #ccc; padding: 10px; margin: 10px;'>";
        echo "<h3>Email simulé</h3>";
        echo "<p><strong>À:</strong> $to</p>";
        echo "<p><strong>Sujet:</strong> $subject</p>";
        echo "<p><strong>Message:</strong></p>";
        echo "<div>$message</div>";
        echo "</div>";
        
        return true;
        
        // En production, utilisez:
        // return mail($to, $subject, $message, $headers);
    }
    
    /**
     * Envoie un code d'authentification à deux facteurs
     * @param string $to Adresse email du destinataire
     * @param string $code Code A2F
     * @return bool Succès de l'envoi
     */
    public function sendAuthCode($to, $code) {
        $subject = "Votre code d'authentification";
        $message = "
        <html>
        <body>
            <h2>Code d'authentification à deux facteurs</h2>
            <p>Votre code d'authentification est: <strong>$code</strong></p>
            <p>Ce code expirera dans 10 minutes.</p>
            <p>Si vous n'avez pas demandé ce code, veuillez ignorer cet email.</p>
        </body>
        </html>
        ";
        
        return $this->send($to, $subject, $message);
    }
    
    /**
     * Envoie un lien de réinitialisation de mot de passe
     * @param string $to Adresse email du destinataire
     * @param string $token Jeton de réinitialisation
     * @return bool Succès de l'envoi
     */
    public function sendResetLink($to, $token) {
        $reset_link = "http://" . $_SERVER['HTTP_HOST'] . "/reset_password.php?token=" . $token;
        
        $subject = "Réinitialisation de votre mot de passe";
        $message = "
        <html>
        <body>
            <h2>Réinitialisation de mot de passe</h2>
            <p>Vous avez demandé la réinitialisation de votre mot de passe.</p>
            <p>Cliquez sur le lien ci-dessous pour réinitialiser votre mot de passe :</p>
            <p><a href='$reset_link'>Réinitialiser mon mot de passe</a></p>
            <p>Ou copiez ce lien dans votre navigateur : $reset_link</p>
            <p>Ce lien expirera dans 1 heure.</p>
            <p>Si vous n'avez pas demandé cette réinitialisation, veuillez ignorer cet email.</p>
        </body>
        </html>
        ";
        
        return $this->send($to, $subject, $message);
    }
}
?>