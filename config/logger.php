<?php
class Logger {
    private $db;
    
    /**
     * Constructeur de la classe Logger
     * @param PDO $db Connexion à la base de données
     */
    public function __construct($db) {
        $this->db = $db;
    }
    
    /**
     * Enregistre une action dans les logs
     * @param int $user_id ID de l'utilisateur (null si non connecté)
     * @param string $action Action effectuée
     * @param string $details Détails supplémentaires (optionnel)
     * @return bool Succès de l'opération
     */
    public function log($user_id, $action, $details = null) {
        try {
            $query = "INSERT INTO activity_logs (user_id, action, ip_address, user_agent, details)
                      VALUES (:user_id, :action, :ip_address, :user_agent, :details)";
            
            $stmt = $this->db->prepare($query);
            
            $ip_address = $_SERVER['REMOTE_ADDR'] ?? '';
            $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? '';
            
            $stmt->bindParam(':user_id', $user_id);
            $stmt->bindParam(':action', $action);
            $stmt->bindParam(':ip_address', $ip_address);
            $stmt->bindParam(':user_agent', $user_agent);
            $stmt->bindParam(':details', $details);
            
            return $stmt->execute();
        } catch(PDOException $exception) {
            echo "Erreur de journalisation: " . $exception->getMessage();
            return false;
        }
    }
    
    /**
     * Récupère les logs (pour l'administrateur)
     * @param int $limit Nombre de logs à récupérer
     * @param int $offset Décalage pour la pagination
     * @return array Les logs récupérés
     */
    public function getLogs($limit = 50, $offset = 0) {
        try {
            $query = "SELECT l.id, l.user_id, u.username, l.action, l.ip_address, 
                      l.user_agent, l.details, l.created_at 
                      FROM activity_logs l
                      LEFT JOIN users u ON l.user_id = u.id
                      ORDER BY l.created_at DESC
                      LIMIT :limit OFFSET :offset";
            
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $exception) {
            echo "Erreur de récupération des logs: " . $exception->getMessage();
            return [];
        }
    }
}
?>