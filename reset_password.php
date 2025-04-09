<?php
require_once 'includes/header.php';

$error = "";
$success = "";
$user_id = null;
$token = filter_input(INPUT_GET, 'token', FILTER_SANITIZE_SPECIAL_CHARS) ?? '';

// Vérification du jeton
if(empty($token)) {
    header("Location: login.php");
    exit;
}

// Validation du format du jeton (doit être une chaîne hexadécimale de 64 caractères)
if(!preg_match('/^[a-f0-9]{64}$/', $token)) {
    $error = "Format de jeton invalide.";
} else {
    $user_id = $user->verifyResetToken($token);
    
    if(!$user_id) {
        $error = "Le lien de réinitialisation est invalide ou a expiré.";
    }
}

if($_SERVER['REQUEST_METHOD'] === 'POST' && $user_id) {
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    
    if(empty($new_password) || empty($confirm_password)) {
        $error = "Tous les champs sont obligatoires.";
    } elseif($new_password !== $confirm_password) {
        $error = "Les mots de passe ne correspondent pas.";
    } elseif(strlen($new_password) < 8) {
        $error = "Le mot de passe doit contenir au moins 8 caractères.";
    } else {
        // Mise à jour du mot de passe
        if($user->updatePassword($user_id, $new_password)) {
            // Journalisation
            $logger->log($user_id, "Réinitialisation de mot de passe", "Mot de passe modifié avec succès");
            
            $success = "Votre mot de passe a été réinitialisé avec succès. Vous pouvez maintenant vous connecter avec votre nouveau mot de passe.";
        } else {
            $error = "Une erreur est survenue lors de la réinitialisation du mot de passe.";
        }
    }
}
?>

<div class="row">
    <div class="col-md-8 offset-md-2">
        <h1 class="text-center mb-4">Réinitialisation du mot de passe</h1>
        
        <?php if(!empty($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <?php if(!empty($success)): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
            <div class="text-center mt-3">
                <a href="login.php" class="btn btn-primary">Se connecter</a>
            </div>
        <?php elseif($user_id): ?>
            <div class="form-container">
                <form method="POST" action="">
                    <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
                    
                    <div class="mb-3">
                        <label for="new_password" class="form-label">Nouveau mot de passe</label>
                        <input type="password" class="form-control" id="new_password" name="new_password" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="confirm_password" class="form-label">Confirmer le mot de passe</label>
                        <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                    </div>
                    
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary">Réinitialiser le mot de passe</button>
                    </div>
                </form>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>