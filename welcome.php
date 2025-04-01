<?php
require_once 'includes/header.php';

// Vérification si l'utilisateur est connecté
if(!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Récupération des informations de l'utilisateur
$userData = $user->findById($_SESSION['user_id']);
?>

<div class="row">
    <div class="col-md-8 offset-md-2">
        <div class="card">
            <div class="card-header">
                <h1 class="text-center mb-0">Bienvenue, <?php echo htmlspecialchars($userData['username']); ?> !</h1>
            </div>
            <div class="card-body">
                <div class="alert alert-success">
                    <p>Vous êtes connecté avec succès à votre compte.</p>
                </div>
                
                <div class="user-info mb-4">
                    <h3>Informations du compte</h3>
                    <p><strong>Nom d'utilisateur:</strong> <?php echo htmlspecialchars($userData['username']); ?></p>
                    <p><strong>Email:</strong> <?php echo htmlspecialchars($userData['email']); ?></p>
                    <p><strong>Compte créé le:</strong> <?php echo date('d/m/Y H:i', strtotime($userData['created_at'])); ?></p>
                </div>
                
                <div class="d-grid gap-2">
                    <a href="logout.php" class="btn btn-danger">Se déconnecter</a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>