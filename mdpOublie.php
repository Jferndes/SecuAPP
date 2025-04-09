<?php
require_once 'includes/header.php';

$error = "";
$success = "";

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim(filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL) ?? '');
    
    if(empty($email)) {
        $error = "Veuillez entrer votre adresse email.";
    } elseif(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "L'adresse email n'est pas valide.";
    } else {
        // Recherche de l'utilisateur
        $userData = $user->findUser($email);
        
        if($userData) {
            // Génération d'un jeton de réinitialisation
            $token = $user->generateResetToken($userData['id']);
            
            if($token) {
                // Envoi du lien de réinitialisation
                $mailer->sendResetLink($userData['email'], $token);
                
                // Journalisation
                $logger->log($userData['id'], "Demande de réinitialisation", "Email: " . $userData['email']);
                
                $success = "Un lien de réinitialisation a été envoyé à votre adresse email.";
            } else {
                $error = "Erreur lors de la génération du lien de réinitialisation.";
            }
        } else {
            // Pour des raisons de sécurité, ne pas indiquer si l'email existe
            $success = "Si cette adresse email est associée à un compte, un lien de réinitialisation a été envoyé.";
            
            // Journalisation
            $logger->log(null, "Demande de réinitialisation (email inconnu)", "Email: " . htmlspecialchars($email));
        }
    }
}
?>

<div class="row">
    <div class="col-md-8 offset-md-2">
        <h1 class="text-center mb-4">Mot de passe oublié</h1>
        
        <?php if(!empty($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <?php if(!empty($success)): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>
        
        <div class="form-container">
            <p class="mb-4">Entrez votre adresse email ci-dessous pour recevoir un lien de réinitialisation de mot de passe.</p>
            
            <form method="POST" action="">
                <div class="mb-3">
                    <label for="email" class="form-label">Adresse email</label>
                    <input type="email" class="form-control" id="email" name="email" required>
                </div>
                
                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary">Envoyer le lien</button>
                </div>
            </form>
            
            <div class="text-center mt-3">
                <p><a href="connexion.php">Retour à la connexion</a></p>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>