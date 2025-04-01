<?php
require_once 'includes/header.php';

$error = "";

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if(empty($username) || empty($password)) {
        $error = "Tous les champs sont obligatoires.";
    } else {
        // Recherche de l'utilisateur
        $userData = $user->findUser($username);
        
        if($userData && password_verify($password, $userData['password'])) {
            // Génération d'un code A2F
            $code = $user->generateAuthCode($userData['id']);
            
            if($code) {
                // Envoi du code par email
                $mailer->sendAuthCode($userData['email'], $code);
                
                // Stockage temporaire de l'ID utilisateur
                $_SESSION['temp_user_id'] = $userData['id'];
                
                // Journalisation
                $logger->log($userData['id'], "Connexion", "Étape 1: A2F envoyé");
                
                // Redirection vers la page A2F
                header("Location: two_factor.php");
                exit;
            } else {
                $error = "Erreur lors de la génération du code d'authentification.";
            }
        } else {
            $error = "Nom d'utilisateur ou mot de passe incorrect.";
            // Journalisation des tentatives échouées
            $logger->log(null, "Tentative de connexion échouée", "Utilisateur: $username");
        }
    }
}
?>

<div class="row">
    <div class="col-md-8 offset-md-2">
        <h1 class="text-center mb-4">Connexion</h1>
        
        <?php if(!empty($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <div class="form-container">
            <form method="POST" action="">
                <div class="mb-3">
                    <label for="username" class="form-label">Nom d'utilisateur ou email</label>
                    <input type="text" class="form-control" id="username" name="username" required>
                </div>
                
                <div class="mb-3">
                    <label for="password" class="form-label">Mot de passe</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                </div>
                
                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary">Se connecter</button>
                </div>
                
                <div class="text-center mt-3">
                    <p><a href="forgot_password.php">Mot de passe oublié?</a></p>
                </div>
            </form>
            
            <div class="text-center mt-3">
                <p>Pas encore inscrit? <a href="register.php">Créez un compte</a></p>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>