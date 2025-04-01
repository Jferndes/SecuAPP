<?php
require_once 'includes/header.php';

$error = "";
$success = "";

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Vérification des données
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    
    if(empty($username) || empty($email) || empty($password) || empty($confirm_password)) {
        $error = "Tous les champs sont obligatoires.";
    } elseif($password !== $confirm_password) {
        $error = "Les mots de passe ne correspondent pas.";
    } elseif(strlen($password) < 8) {
        $error = "Le mot de passe doit contenir au moins 8 caractères.";
    } elseif(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "L'adresse email n'est pas valide.";
    } else {
        // Vérification si l'utilisateur existe déjà
        if($user->findUser($username) || $user->findUser($email)) {
            $error = "Ce nom d'utilisateur ou cette adresse email est déjà utilisé(e).";
        } else {
            // Création de l'utilisateur
            $user->username = $username;
            $user->email = $email;
            $user->password = $password;
            
            if($user->create()) {
                $logger->log(null, "Inscription", "Nouvel utilisateur: $username");
                $success = "Votre compte a été créé avec succès. Vous pouvez maintenant vous connecter.";
            } else {
                $error = "Une erreur est survenue lors de la création du compte.";
            }
        }
    }
}
?>

<div class="row">
    <div class="col-md-8 offset-md-2">
        <h1 class="text-center mb-4">Inscription</h1>
        
        <?php if(!empty($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <?php if(!empty($success)): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>
        
        <div class="form-container">
            <form method="POST" action="">
                <div class="mb-3">
                    <label for="username" class="form-label">Nom d'utilisateur</label>
                    <input type="text" class="form-control" id="username" name="username" required>
                </div>
                
                <div class="mb-3">
                    <label for="email" class="form-label">Adresse email</label>
                    <input type="email" class="form-control" id="email" name="email" required>
                </div>
                
                <div class="mb-3">
                    <label for="password" class="form-label">Mot de passe</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                </div>
                
                <div class="mb-3">
                    <label for="confirm_password" class="form-label">Confirmer le mot de passe</label>
                    <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                </div>
                
                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary">S'inscrire</button>
                </div>
            </form>
            
            <div class="text-center mt-3">
                <p>Déjà inscrit? <a href="login.php">Connectez-vous</a></p>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>