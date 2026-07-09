<?php
// detail-client.php - Profil complet d'un client + ses réservations (admin)
$admin_page_title = 'AIR MAKEN Admin - Profil Client';
require_once dirname(dirname(dirname(__FILE__))) . '/templates/header-admin.php';
requireAdmin();
require_once dirname(dirname(dirname(__FILE__))) . '/traitements/config/connexion.php';

$id = intval($_GET['id'] ?? 0);
if ($id <= 0) {
    header("Location: " . SITE_URL . "pages/admin/gestion-clients.php");
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM utilisateurs WHERE id = :id");
$stmt->execute(['id' => $id]);
$client = $stmt->fetch();

if (!$client) {
    setFlashMessage('danger', "Client introuvable.");
    header("Location: " . SITE_URL . "pages/admin/gestion-clients.php");
    exit;
}

// Réservations du client
$res_stmt = $pdo->prepare("SELECT r.*, s.nom as nom_service FROM reservations r LEFT JOIN services s ON r.id_service = s.id WHERE r.id_utilisateur = :id ORDER BY r.date_demande DESC");
$res_stmt->execute(['id' => $id]);
$reservations = $res_stmt->fetchAll();

$type_labels = [
    'billetterie' => 'Billetterie',
    'hotel'       => 'Hôtel',
    'vehicule'    => 'Véhicule',
    'visa'        => 'Visa',
    'assurance'   => 'Assurance',
    'voyage'      => 'Voyage',
];
$statut_cfg = [
    'en_attente' => ['badge-warning',  'En attente'],
    'confirmee'  => ['badge-success',  'Confirmée'],
    'refusee'    => ['badge-danger',   'Refusée'],
    'annulee'    => ['badge-primary',  'Annulée'],
];
$csrf_token = generateCsrfToken();
?>

<?php require_once dirname(dirname(dirname(__FILE__))) . '/templates/sidebar-admin.php'; ?>

<div class="admin-main">
    <?php require_once dirname(dirname(dirname(__FILE__))) . '/templates/navbar-admin.php'; ?>
    <div class="admin-content">
        <?php require_once dirname(dirname(dirname(__FILE__))) . '/templates/alertes.php'; ?>

        <div style="margin-bottom:var(--spacing-sm); font-size:.9rem; color:var(--text-muted);">
            <a href="<?php echo SITE_URL; ?>pages/admin/gestion-clients.php">Clients</a>
            <i class="fa-solid fa-chevron-right" style="margin:0 .4rem; font-size:.7rem;"></i>
            <strong><?php echo escape($client['prenom'] . ' ' . $client['nom']); ?></strong>
        </div>

        <div class="detail-grid">
            <!-- Infos Client -->
            <div>
                <div class="card" style="margin-bottom:var(--spacing-md);">
                    <div class="admin-card-header">
                        <h3 style="font-family:var(--font-title);">
                            <i class="fa-solid fa-user" style="color:var(--secondary);"></i> Informations personnelles
                        </h3>
                    </div>
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:var(--spacing-sm);">
                        <?php foreach (['prenom' => 'Prénom', 'nom' => 'Nom', 'email' => 'Email', 'telephone' => 'Téléphone'] as $field => $label): ?>
                        <div>
                            <p style="font-size:.75rem;color:var(--text-muted);font-weight:700;text-transform:uppercase;"><?php echo $label; ?></p>
                            <p><?php echo escape($client[$field] ?? '-'); ?></p>
                        </div>
                        <?php endforeach; ?>
                        <div>
                            <p style="font-size:.75rem;color:var(--text-muted);font-weight:700;text-transform:uppercase;">Inscrit le</p>
                            <p><?php echo formatDate($client['date_inscription']); ?></p>
                        </div>
                        <div>
                            <p style="font-size:.75rem;color:var(--text-muted);font-weight:700;text-transform:uppercase;">Statut</p>
                            <p>
                                <?php if ($client['statut'] === 'bloque'): ?>
                                    <span class="badge badge-danger">Bloqué</span>
                                <?php else: ?>
                                    <span class="badge badge-success">Actif</span>
                                <?php endif; ?>
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Réservations du client -->
                <div class="card">
                    <div class="admin-card-header">
                        <h3 style="font-family:var(--font-title);">
                            <i class="fa-solid fa-ticket" style="color:var(--secondary);"></i> Historique des réservations (<?php echo count($reservations); ?>)
                        </h3>
                    </div>
                    <?php if (empty($reservations)): ?>
                        <p class="text-muted">Aucune réservation pour ce client.</p>
                    <?php else: ?>
                        <div class="reservation-table-wrapper">
                            <table class="reservation-table" style="font-size:.85rem;">
                                <thead>
                                    <tr><th>#</th><th>Service</th><th>Départ</th><th>Tarif</th><th>Statut</th><th></th></tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($reservations as $res): ?>
                                        <?php [$badge, $label] = $statut_cfg[$res['statut']] ?? ['badge-primary', $res['statut']]; ?>
                                        <tr>
                                            <td>#<?php echo $res['id']; ?></td>
                                            <td><?php echo escape($type_labels[$res['type_service']] ?? ucfirst($res['type_service'])); ?></td>
                                            <td><?php echo formatDate($res['date_debut']); ?></td>
                                            <td><?php echo formatPrice($res['montant']); ?></td>
                                            <td><span class="badge <?php echo $badge; ?>"><?php echo $label; ?></span></td>
                                            <td><a href="<?php echo SITE_URL; ?>pages/admin/detail-reservation.php?id=<?php echo $res['id']; ?>" class="btn btn-primary btn-sm"><i class="fa-solid fa-eye"></i></a></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Actions -->
            <div>
                <div class="card">
                    <div class="admin-card-header">
                        <h3 style="font-family:var(--font-title);">
                            <i class="fa-solid fa-sliders" style="color:var(--secondary);"></i> Actions
                        </h3>
                    </div>

                    <!-- Blocage -->
                    <form action="<?php echo SITE_URL; ?>traitements/clients/traitement_blocage_client.php" method="POST" style="margin-bottom:var(--spacing-sm);"
                          onsubmit="return confirm('<?php echo ($client['statut'] === 'bloque') ? 'Débloquer' : 'Bloquer'; ?> ce client ?');">
                        <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                        <input type="hidden" name="client_id" value="<?php echo $client['id']; ?>">
                        <input type="hidden" name="action" value="<?php echo ($client['statut'] === 'bloque') ? 'debloquer' : 'bloquer'; ?>">
                        <button type="submit" class="btn btn-lg <?php echo ($client['statut'] === 'bloque') ? 'btn-success' : 'btn-warning'; ?>" style="width:100%;">
                            <i class="fa-solid <?php echo ($client['statut'] === 'bloque') ? 'fa-lock-open' : 'fa-lock'; ?>"></i>
                            <?php echo ($client['statut'] === 'bloque') ? 'Débloquer le compte' : 'Bloquer le compte'; ?>
                        </button>
                    </form>

                    <!-- Suppression (Super Admin seulement) -->
                    <?php if (isSuperAdmin()): ?>
                    <form action="<?php echo SITE_URL; ?>traitements/clients/traitement_suppression_client.php" method="POST"
                          onsubmit="return confirm('ATTENTION : supprimer définitivement ce client et toutes ses données ?');">
                        <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                        <input type="hidden" name="client_id" value="<?php echo $client['id']; ?>">
                        <button type="submit" class="btn btn-danger btn-lg" style="width:100%;">
                            <i class="fa-solid fa-user-slash"></i> Supprimer le compte
                        </button>
                    </form>
                    <?php endif; ?>

                    <div style="margin-top:var(--spacing-md);padding-top:var(--spacing-sm);border-top:1px solid var(--border-color);">
                        <a href="<?php echo SITE_URL; ?>pages/admin/gestion-clients.php" class="btn btn-outline btn-sm" style="width:100%;">
                            <i class="fa-solid fa-arrow-left"></i> Retour à la liste
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

<?php require_once dirname(dirname(dirname(__FILE__))) . '/templates/footer-admin.php'; ?>
