<?php
require_once 'includes/header.php';

// Vérification si l'utilisateur est en attente de vérification A2F
if(!isset($_SESSION['temp_user_id'])) {
    header("Location: login.php");
    exit;
}

$error = "";

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $code = trim(filter_input(INPUT_POST, 'code', FILTER_SANITIZE_SPECIAL_CHARS) ?? '');
    
    if(empty($code)) {
        $error = "Veuillez entrer le code d'authentification.";
    } elseif(!preg_match('/^\d{6}$/', $code)) {
        $error = "Le code d'authentification doit contenir 6 chiffres.";
    } else {
        // Vérification du code
        if($user->verifyAuthCode($_SESSION['temp_user_id'], $code)) {
            // Récupération des informations utilisateur
            $userData = $user->findById($_SESSION['temp_user_id']);
            
            // Création de la session utilisateur
            $_SESSION['user_id'] = $userData['id'];
            $_SESSION['username'] = $userData['username'];
            
            // Suppression de la session temporaire
            unset($_SESSION['temp_user_id']);
            
            // Journalisation
            $logger->log($userData['id'], "Connexion", "Étape 2: A2F validé");
            
            // Redirection vers la page d'accueil
            header("Location: welcome.php");
            exit;
        } else {
            $error = "Code d'authentification invalide ou expiré.";
            // Journalisation
            $logger->log($_SESSION['temp_user_id'], "A2F échoué", "Code invalide");
        }
    }
}
?>

<div class="row">
    <div class="col-md-8 offset-md-2">
        <h1 class="text-center mb-4">Authentification à deux facteurs</h1>
        
        <?php if(!empty($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <div class="form-container">
            <p class="mb-4">Un code d'authentification à 6 chiffres a été envoyé à votre adresse email. Veuillez l'entrer ci-dessous pour continuer.</p>
            
            <form method="POST" action="">
                <div class="mb-3">
                    <label for="code" class="form-label">Code d'authentification</label>
                    <input type="text" class="form-control" id="code" name="code" maxlength="6" inputmode="numeric" pattern="\d{6}" required>
                </div>
                
                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary">Vérifier</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>