<?php
session_start();

// Redirection vers la page d'accueil si l'utilisateur est connecté
if(isset($_SESSION['user_id'])) {
    header("Location: welcome.php");
    exit;
}

// Sinon, redirection vers la page de connexion
header("Location: login.php");
exit;
?>