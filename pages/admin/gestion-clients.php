<?php
// gestion-clients.php - Liste des clients inscrits (admin)
$admin_page_title = 'AIR MAKEN Admin - Gestion des Clients';
require_once dirname(dirname(dirname(__FILE__))) . '/templates/header-admin.php';
requireAdmin();
require_once dirname(dirname(dirname(__FILE__))) . '/traitements/config/connexion.php';

$recherche = trim($_GET['q'] ?? '');
$filtre_statut = $_GET['statut'] ?? '';

$where  = [];
$params = [];

if (!empty($recherche)) {
    $where[]  = "(u.nom LIKE :q OR u.prenom LIKE :q OR u.email LIKE :q)";
    $params['q'] = '%' . $recherche . '%';
}
if (!empty($filtre_statut) && in_array($filtre_statut, ['actif', 'bloque'])) {
    $where[]  = "u.statut = :statut";
    $params['statut'] = $filtre_statut;
}

$sql = "SELECT u.*, COUNT(r.id) AS nb_reservations
        FROM utilisateurs u
        LEFT JOIN reservations r ON r.id_utilisateur = u.id"
     . (!empty($where) ? ' WHERE ' . implode(' AND ', $where) : '')
     . " GROUP BY u.id ORDER BY u.date_inscription DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$clients = $stmt->fetchAll();

$csrf_token = generateCsrfToken();
?>

<?php require_once dirname(dirname(dirname(__FILE__))) . '/templates/sidebar-admin.php'; ?>

<div class="admin-main">
    <?php require_once dirname(dirname(dirname(__FILE__))) . '/templates/navbar-admin.php'; ?>
    <div class="admin-content">
        <?php require_once dirname(dirname(dirname(__FILE__))) . '/templates/alertes.php'; ?>

        <div class="flex-between" style="margin-bottom:var(--spacing-md);">
            <h2 style="font-family:var(--font-title);">
                <i class="fa-solid fa-users" style="color:var(--secondary);"></i> Clients inscrits
                <span class="badge badge-primary" style="margin-left:.5rem;"><?php echo count($clients); ?></span>
            </h2>
        </div>

        <!-- Barre de recherche / filtre -->
        <div class="table-filter-bar">
            <form method="GET" action="">
                <input type="text" name="q" class="form-control" placeholder="Rechercher un client..." value="<?php echo escape($recherche); ?>" style="max-width:260px;">
                <select name="statut" class="form-control" style="max-width:160px;">
                    <option value="">Tous les statuts</option>
                    <option value="actif" <?php echo ($filtre_statut === 'actif') ? 'selected' : ''; ?>>Actifs</option>
                    <option value="bloque" <?php echo ($filtre_statut === 'bloque') ? 'selected' : ''; ?>>Bloqués</option>
                </select>
                <button type="submit" class="btn btn-primary btn-sm"><i class="fa-solid fa-filter"></i> Filtrer</button>
                <a href="<?php echo SITE_URL; ?>pages/admin/gestion-clients.php" class="btn btn-outline btn-sm">Réinitialiser</a>
            </form>
        </div>

        <div class="card">
            <?php if (empty($clients)): ?>
                <p class="text-muted py-md">Aucun client ne correspond.</p>
            <?php else: ?>
                <div class="reservation-table-wrapper">
                    <table class="reservation-table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Nom complet</th>
                                <th>Email</th>
                                <th>Téléphone</th>
                                <th>Réservations</th>
                                <th>Inscription</th>
                                <th>Statut</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($clients as $client): ?>
                            <tr>
                                <td>#<?php echo $client['id']; ?></td>
                                <td><strong><?php echo escape($client['prenom'] . ' ' . $client['nom']); ?></strong></td>
                                <td><?php echo escape($client['email']); ?></td>
                                <td><?php echo escape($client['telephone'] ?? '-'); ?></td>
                                <td style="text-align:center;"><?php echo $client['nb_reservations']; ?></td>
                                <td><?php echo formatDate($client['date_inscription']); ?></td>
                                <td>
                                    <?php if ($client['statut'] === 'bloque'): ?>
                                        <span class="badge badge-danger">Bloqué</span>
                                    <?php else: ?>
                                        <span class="badge badge-success">Actif</span>
                                    <?php endif; ?>
                                </td>
                                <td style="white-space:nowrap;">
                                    <a href="<?php echo SITE_URL; ?>pages/admin/detail-client.php?id=<?php echo $client['id']; ?>" class="btn btn-primary btn-sm" title="Voir le profil">
                                        <i class="fa-solid fa-eye"></i>
                                    </a>
                                    <!-- Blocage / Déblocage -->
                                    <form action="<?php echo SITE_URL; ?>traitements/clients/traitement_blocage_client.php" method="POST" style="display:inline;"
                                          onsubmit="return confirm('<?php echo ($client['statut'] === 'bloque') ? 'Débloquer' : 'Bloquer'; ?> ce client ?');">
                                        <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                                        <input type="hidden" name="client_id" value="<?php echo $client['id']; ?>">
                                        <input type="hidden" name="action" value="<?php echo ($client['statut'] === 'bloque') ? 'debloquer' : 'bloquer'; ?>">
                                        <button type="submit" class="btn btn-sm <?php echo ($client['statut'] === 'bloque') ? 'btn-success' : 'btn-warning'; ?>" title="<?php echo ($client['statut'] === 'bloque') ? 'Débloquer' : 'Bloquer'; ?>">
                                            <i class="fa-solid <?php echo ($client['statut'] === 'bloque') ? 'fa-lock-open' : 'fa-lock'; ?>"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>

<?php require_once dirname(dirname(dirname(__FILE__))) . '/templates/footer-admin.php'; ?>
