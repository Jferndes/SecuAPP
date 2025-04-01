<?php
// Définir le fuseau horaire dès le début du script
date_default_timezone_set('Europe/Paris'); // À adapter selon votre localisation

require_once 'includes/header.php';

// Cette page devrait être protégée par une authentification d'administrateur
// Pour simplifier, on va juste vérifier si l'utilisateur est connecté

if(!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 20;
$offset = ($page - 1) * $limit;

// Récupération des logs
$logs = $logger->getLogs($limit, $offset);
?>

<div class="row">
    <div class="col-md-12">
        <h1 class="text-center mb-4">Logs d'activité</h1>
        
        <div class="card">
            <div class="card-header">
                <h3 class="mb-0">Historique des actions</h3>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Utilisateur</th>
                                <th>Action</th>
                                <th>Adresse IP</th>
                                <th>Détails</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(empty($logs)): ?>
                                <tr>
                                    <td colspan="6" class="text-center">Aucun log disponible</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach($logs as $log): ?>
                                    <tr>
                                        <td><?php echo $log['id']; ?></td>
                                        <td><?php echo $log['username'] ? htmlspecialchars($log['username']) : 'Non connecté'; ?></td>
                                        <td><?php echo htmlspecialchars($log['action']); ?></td>
                                        <td><?php echo htmlspecialchars($log['ip_address']); ?></td>
                                        <td><?php echo htmlspecialchars($log['details']); ?></td>
                                        <td>
                                            <?php 
                                            // Création d'un objet DateTime pour respecter le fuseau horaire
                                            $date = new DateTime($log['created_at']);
                                            echo $date->format('d/m/Y H:i:s'); 
                                            ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination simple -->
                <nav aria-label="Pagination des logs">
                    <ul class="pagination justify-content-center">
                        <?php if($page > 1): ?>
                            <li class="page-item">
                                <a class="page-link" href="?page=<?php echo $page - 1; ?>">Précédent</a>
                            </li>
                        <?php endif; ?>
                        
                        <li class="page-item active">
                            <span class="page-link"><?php echo $page; ?></span>
                        </li>
                        
                        <li class="page-item">
                            <a class="page-link" href="?page=<?php echo $page + 1; ?>">Suivant</a>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>
        
        <div class="text-center mt-3">
            <a href="welcome.php" class="btn btn-primary">Retour à l'accueil</a>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>