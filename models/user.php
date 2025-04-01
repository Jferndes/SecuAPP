<?php
class User {
    private $conn;
    private $table_name = "users";
    
    public $id;
    public $username;
    public $email;
    public $password;
    
    /**
     * Constructeur de la classe User
     * @param PDO $db Connexion à la base de données
     */
    public function __construct($db) {
        $this->conn = $db;
    }
    
    /**
     * Crée un nouvel utilisateur
     * @return bool Succès de l'opération
     */
    public function create() {
        $query = "INSERT INTO " . $this->table_name . " 
                  (username, email, password) 
                  VALUES (:username, :email, :password)";
        
        $stmt = $this->conn->prepare($query);
        
        // Assainissement des données
        $this->username = htmlspecialchars(strip_tags($this->username));
        $this->email = htmlspecialchars(strip_tags($this->email));
        
        // Hachage du mot de passe
        $password_hash = password_hash($this->password, PASSWORD_BCRYPT);
        
        // Liaison des valeurs
        $stmt->bindParam(':username', $this->username);
        $stmt->bindParam(':email', $this->email);
        $stmt->bindParam(':password', $password_hash);
        
        // Exécution de la requête
        if($stmt->execute()) {
            $this->id = $this->conn->lastInsertId();
            return true;
        }
        
        return false;
    }
    
    /**
     * Vérifie si l'utilisateur existe avec le nom d'utilisateur ou l'email fourni
     * @param string $value Nom d'utilisateur ou email
     * @return bool|array Données utilisateur ou false
     */
    public function findUser($value) {
        $query = "SELECT * FROM " . $this->table_name . " 
                  WHERE username = :value OR email = :value 
                  LIMIT 1";
        
        $stmt = $this->conn->prepare($query);
        
        // Assainissement des données
        $value = htmlspecialchars(strip_tags($value));
        
        // Liaison des valeurs
        $stmt->bindParam(':value', $value);
        
        // Exécution de la requête
        $stmt->execute();
        
        if($stmt->rowCount() > 0) {
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }
        
        return false;
    }
    
    /**
     * Récupère un utilisateur par son ID
     * @param int $id ID de l'utilisateur
     * @return bool|array Données utilisateur ou false
     */
    public function findById($id) {
        $query = "SELECT * FROM " . $this->table_name . " 
                  WHERE id = :id 
                  LIMIT 1";
        
        $stmt = $this->conn->prepare($query);
        
        // Liaison des valeurs
        $stmt->bindParam(':id', $id);
        
        // Exécution de la requête
        $stmt->execute();
        
        if($stmt->rowCount() > 0) {
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }
        
        return false;
    }
    
    /**
     * Génère et sauvegarde un code d'authentification à deux facteurs
     * @param int $user_id ID de l'utilisateur
     * @return string|bool Le code généré ou false
     */
    public function generateAuthCode($user_id) {
        // Suppression des anciens codes
        $query = "DELETE FROM auth_codes WHERE user_id = :user_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        
        // Génération d'un nouveau code à 6 chiffres
        $code = sprintf("%06d", mt_rand(1, 999999));
        
        // Définition de l'expiration (10 minutes)
        $expires_at = date('Y-m-d H:i:s', strtotime('+10 minutes'));
        
        // Insertion du nouveau code
        $query = "INSERT INTO auth_codes (user_id, code, expires_at) 
                  VALUES (:user_id, :code, :expires_at)";
        
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':code', $code);
        $stmt->bindParam(':expires_at', $expires_at);
        
        if($stmt->execute()) {
            return $code;
        }
        
        return false;
    }
    
    /**
     * Vérifie si un code A2F est valide
     * @param int $user_id ID de l'utilisateur
     * @param string $code Code A2F
     * @return bool Validité du code
     */
    public function verifyAuthCode($user_id, $code) {
        $query = "SELECT * FROM auth_codes 
                  WHERE user_id = :user_id 
                  AND code = :code 
                  AND expires_at > NOW() 
                  LIMIT 1";
        
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':code', $code);
        
        $stmt->execute();
        
        if($stmt->rowCount() > 0) {
            // Suppression du code utilisé
            $query = "DELETE FROM auth_codes WHERE user_id = :user_id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':user_id', $user_id);
            $stmt->execute();
            
            return true;
        }
        
        return false;
    }
    
    /**
     * Génère et sauvegarde un jeton de réinitialisation de mot de passe
     * @param int $user_id ID de l'utilisateur
     * @return string|bool Le jeton généré ou false
     */
    public function generateResetToken($user_id) {
        // Suppression des anciens jetons
        $query = "DELETE FROM reset_tokens WHERE user_id = :user_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        
        // Génération d'un nouveau jeton
        $token = bin2hex(random_bytes(32));
        
        // Définition de l'expiration (1 heure)
        $expires_at = date('Y-m-d H:i:s', strtotime('+1 hour'));
        
        // Insertion du nouveau jeton
        $query = "INSERT INTO reset_tokens (user_id, token, expires_at) 
                  VALUES (:user_id, :token, :expires_at)";
        
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':token', $token);
        $stmt->bindParam(':expires_at', $expires_at);
        
        if($stmt->execute()) {
            return $token;
        }
        
        return false;
    }
    
    /**
     * Vérifie si un jeton de réinitialisation est valide
     * @param string $token Jeton de réinitialisation
     * @return int|bool ID de l'utilisateur ou false
     */
    public function verifyResetToken($token) {
        $query = "SELECT user_id FROM reset_tokens 
                  WHERE token = :token 
                  AND expires_at > NOW() 
                  LIMIT 1";
        
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(':token', $token);
        
        $stmt->execute();
        
        if($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row['user_id'];
        }
        
        return false;
    }
    
    /**
     * Met à jour le mot de passe d'un utilisateur
     * @param int $user_id ID de l'utilisateur
     * @param string $new_password Nouveau mot de passe
     * @return bool Succès de l'opération
     */
    public function updatePassword($user_id, $new_password) {
        $query = "UPDATE " . $this->table_name . " 
                  SET password = :password 
                  WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        
        // Hachage du nouveau mot de passe
        $password_hash = password_hash($new_password, PASSWORD_BCRYPT);
        
        // Liaison des valeurs
        $stmt->bindParam(':password', $password_hash);
        $stmt->bindParam(':id', $user_id);
        
        // Exécution de la requête
        if($stmt->execute()) {
            // Suppression des jetons de réinitialisation
            $query = "DELETE FROM reset_tokens WHERE user_id = :user_id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':user_id', $user_id);
            $stmt->execute();
            
            return true;
        }
        
        return false;
    }
}
?>