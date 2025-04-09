<?php
/**
 * Classe pour l'envoi d'emails utilisant PHPMailer avec Mailtrap
 */

// Importer les classes PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

// Assurez-vous que ces chemins correspondent à votre structure de dossiers
require_once 'PHPMailer/src/Exception.php';
require_once 'PHPMailer/src/PHPMailer.php';
require_once 'PHPMailer/src/SMTP.php';

class Mailer {
    private $from = "noreply@monsite.com";
    private $fromName = "B3-SecuAPP";
    
    // Configuration SMTP Google                          Configuration SMTP mailtrap
    private $smtpHost = "smtp.gmail.com";                //"sandbox.smtp.mailtrap.io";
    private $smtpPort = 587;
    private $smtpUsername = "ferndesjojo@gmail.com";     //"8183e6bf8cfd4a";
    private $smtpPassword = "wyhg jfwf phnz fgfz";       //"496f0fad345508";

    /**
     * Envoie un email avec PHPMailer
     * @param string $to Adresse email du destinataire
     * @param string $subject Sujet de l'email
     * @param string $message Contenu de l'email
     * @return bool Succès de l'envoi
     */
    public function send($to, $subject, $message) {
        // Créer une nouvelle instance de PHPMailer
        $mail = new PHPMailer(true);
        
        try {
            // Configuration du serveur
            $mail->isSMTP();
            $mail->Host       = $this->smtpHost;
            $mail->SMTPAuth   = true;
            $mail->Username   = $this->smtpUsername;
            $mail->Password   = $this->smtpPassword;
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = $this->smtpPort;
            $mail->CharSet    = 'UTF-8';
            
            // Expéditeur et destinataires
            $mail->setFrom($this->from, $this->fromName);
            $mail->addAddress($to);
            $mail->addReplyTo($this->from, $this->fromName);
            
            // Contenu
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body    = $message;
            $mail->AltBody = strip_tags($message);
            
            // Envoi du message
            $mail->send();
            return true;
        } catch (Exception $e) {
            // Log l'erreur ou gérez-la selon vos besoins
            error_log("Erreur d'envoi d'email: " . $mail->ErrorInfo);
            
            // Mode de développement/débogage : afficher l'email qui aurait été envoyé
            if (isset($_SERVER['SERVER_NAME']) && ($_SERVER['SERVER_NAME'] === 'localhost' || strpos($_SERVER['SERVER_NAME'], 'dev.') === 0)) {
                echo "<div style='border: 1px solid #ccc; padding: 10px; margin: 10px;'>";
                echo "<h3>Email simulé (PHPMailer a échoué)</h3>";
                echo "<p><strong>Erreur:</strong> " . $mail->ErrorInfo . "</p>";
                echo "<p><strong>À:</strong> $to</p>";
                echo "<p><strong>Sujet:</strong> $subject</p>";
                echo "<p><strong>Message:</strong></p>";
                echo "<div>$message</div>";
                echo "</div>";
                return true; // Retourne true en mode développement
            }
            
            return false;
        }
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
        $reset_link = "http://" . $_SERVER['HTTP_HOST'] . "/SecuAPP/reset_password.php?token=" . $token;
        
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
    
    /**
     * Configure les paramètres SMTP
     * @param string $host Hôte SMTP
     * @param int $port Port SMTP
     * @param string $username Nom d'utilisateur SMTP
     * @param string $password Mot de passe SMTP
     */
    public function configureSMTP($host, $port, $username, $password) {
        $this->smtpHost = $host;
        $this->smtpPort = $port;
        $this->smtpUsername = $username;
        $this->smtpPassword = $password;
    }
    
    /**
     * Configure l'expéditeur
     * @param string $email Email de l'expéditeur
     * @param string $name Nom de l'expéditeur
     */
    public function setFrom($email, $name = '') {
        $this->from = $email;
        if (!empty($name)) {
            $this->fromName = $name;
        }
    }
}
?>