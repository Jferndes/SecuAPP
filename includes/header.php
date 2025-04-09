<?php
session_start();

// Inclusion des fichiers nécessaires
require_once 'config/database.php';
require_once 'config/logger.php';
require_once 'config/mailer.php';
require_once 'models/user.php';

// Connexion à la base de données
$database = new Database();
$db = $database->getConnection();

// Initialisation des objets
$logger = new Logger($db);
$mailer = new Mailer();
$user = new User($db);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Application d'authentification</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css">
    <style>
        body {
            padding-top: 50px;
        }
        .form-container {
            max-width: 500px;
            margin: 0 auto;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 5px;
            background-color: #f9f9f9;
        }
    </style>
</head>
<body>
    <div class="container">