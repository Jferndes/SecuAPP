# Application d'Authentification Sécurisée en PHP

Une application d'authentification complète avec des fonctionnalités avancées de sécurité et de gestion des utilisateurs, développée en PHP et MySQL.

## Caractéristiques

- **Système d'authentification complet**
  - Inscription avec validation des données
  - Connexion sécurisée avec validation des entrées
  - Authentification à deux facteurs (A2F) par email
  - Gestion de sessions sécurisées

- **Gestion des mots de passe**
  - Hachage des mots de passe avec BCrypt
  - Fonctionnalité de récupération de mot de passe
  - Mécanisme de jetons sécurisés pour la réinitialisation

- **Sécurité renforcée**
  - Protection contre les injections SQL (requêtes préparées PDO)
  - Validation et sanitization rigoureuses des entrées utilisateur
  - Protection contre les attaques XSS
  - Journalisation des activités suspectes

- **Gestion des utilisateurs**
  - Tableau de bord utilisateur
  - Journal des activités
  - Déconnexion sécurisée

## Technologies utilisées

- PHP 7.4+
- MySQL/MariaDB
- PDO pour les connexions à la base de données
- PHPMailer pour l'envoi d'emails
- Bootstrap 5 pour l'interface utilisateur

## Prérequis

- Serveur web (Apache/Nginx)
- PHP 7.4 ou supérieur
- MySQL 5.7 ou supérieur
- Extensions PHP : PDO, openssl, mbstring

## Installation

1. **Cloner le dépôt**

```bash
git clone https://github.com/Jferndes/SecuAPP.git
```

2. **Configuration de la base de données**

Modifiez les paramètres de connexion dans le fichier `config/database.php` :

```php
private $host = "localhost";
private $db_name = "auth_app";
private $username = "votre_username";
private $password = "votre_password";
```

3. **Créer la base de données et les tables**

Exécuter le script SQL `database.sql` directement depuis votre outil de gestion de base de données préféré (phpMyAdmin, MySQL Workbench, etc.).

4. **Lancer l'application**

Accédez à l'application via votre navigateur :
```
http://localhost/SecuAPP
```

## Structure du projet

```
auth-app/
│
├── config/                # Configuration de l'application
│   ├── database.php       # Configuration de la base de données
│   ├── logger.php         # Système de journalisation
│   └── mailer.php         # Configuration de l'envoi d'emails
│
├── includes/              # Fichiers inclus dans toutes les pages
│   ├── header.php         # En-tête HTML et initialisation
│   └── footer.php         # Pied de page HTML
│
├── models/                # Modèles de données
│   └── user.php           # Modèle utilisateur (création, authentification, etc.)
│
├── PHPMailer/             # Bibliothèque PHPMailer pour l'envoi d'emails
│
├── database.sql           # Script SQL pour la création des tables
├── index.php              # Page d'accueil/redirection
├── connexion.php              # Page de connexion
├── deconnexion.php           # Page d'inscription
├── authDeuxFacteur.php         # Vérification A2F
├── mdpOublie.php    # Demande de réinitialisation de mot de passe
├── mdpReset.php     # Réinitialisation de mot de passe
├── pageAccueil.php            # Page d'accueil après connexion
├── deconnexion.php             # Déconnexion
└── logs.php               # Historique des activités
```

## Fonctionnalités de sécurité

### Protection contre les injections SQL
- Utilisation exclusive de requêtes préparées avec PDO
- Validation stricte et typage des paramètres de requête
- Sanitization des entrées utilisateur

### Sécurité des mots de passe
- Hachage des mots de passe avec BCrypt
- Validation de la complexité des mots de passe
- Jetons de réinitialisation à usage unique et limités dans le temps

### Authentification à deux facteurs
- Codes à 6 chiffres générés aléatoirement
- Expiration automatique après 10 minutes
- Validation stricte du format des codes

### Prévention des attaques XSS
- Échappement systématique des sorties HTML
- Sanitization des entrées avec `filter_input()`
- Validation des formats via des expressions régulières

### Journalisation
- Enregistrement des tentatives de connexion réussies et échouées
- Suivi des activités sensibles (réinitialisation de mot de passe, etc.)
- Stockage des adresses IP et User-Agent pour détecter les activités suspectes

## Bonnes pratiques

- **Ne stockez jamais de mots de passe en clair** - Tous les mots de passe sont hachés avec BCrypt
- **Validez toujours les entrées utilisateur** - Toutes les entrées sont validées et nettoyées
- **Utilisez des sessions sécurisées** - Configuration des sessions conforme aux bonnes pratiques
- **Limitez les informations sensibles retournées** - Messages d'erreur génériques pour éviter la divulgation d'informations

## Mentions spéciales

- [PHPMailer](https://github.com/PHPMailer/PHPMailer) - Pour la gestion de l'envoi d'emails
- [Bootstrap](https://getbootstrap.com/) - Pour l'interface utilisateur