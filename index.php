<?php
session_start();

// Redirection vers la page d'accueil si l'utilisateur est connecté
if(isset($_SESSION['user_id'])) {
    header("Location: pageAccueil.php");
    exit;
}

// Sinon, redirection vers la page de connexion
header("Location: connexion.php");
exit;
?>