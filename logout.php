<?php
require_once 'includes/header.php';

// Vérification si l'utilisateur est connecté
if(isset($_SESSION['user_id'])) {
    // Journalisation
    $logger->log($_SESSION['user_id'], "Déconnexion", "Utilisateur: " . $_SESSION['username']);
    
    // Destruction de la session
    session_unset();
    session_destroy();
}

// Redirection vers la page de connexion
header("Location: login.php");
exit;
?>