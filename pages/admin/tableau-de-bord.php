<?php
// tableau-de-bord.php - Tableau de bord principal admin (indicateurs)
$admin_page_title = 'AIR MAKEN Admin - Tableau de bord';
require_once dirname(dirname(dirname(__FILE__))) . '/templates/header-admin.php';
requireAdmin();
require_once dirname(dirname(dirname(__FILE__))) . '/traitements/config/connexion.php';

// ── Indicateurs ──────────────────────────────────────────────────────────────
$stats = [];
$queries = [
    'en_attente'  => "SELECT COUNT(*) FROM reservations WHERE statut = 'en_attente'",
    'confirmees'  => "SELECT COUNT(*) FROM reservations WHERE statut = 'confirmee'",
    'refusees'    => "SELECT COUNT(*) FROM reservations WHERE statut = 'refusee'",
    'messages'    => "SELECT COUNT(*) FROM messages_contact WHERE statut = 'non_lu'",
    'clients'     => "SELECT COUNT(*) FROM utilisateurs",
    'total_res'   => "SELECT COUNT(*) FROM reservations",
];
foreach ($queries as $key => $sql) {
    $stats[$key] = (int) $pdo->query($sql)->fetchColumn();
}

// ── 5 dernières réservations ─────────────────────────────────────────────────
$recentes = $pdo->query(
    "SELECT r.id, r.type_service, r.statut, r.date_demande,
            u.nom, u.prenom
     FROM reservations r
     JOIN utilisateurs u ON r.id_utilisateur = u.id
     ORDER BY r.date_demande DESC LIMIT 5"
)->fetchAll();

$type_labels = [
    'billetterie' => 'Billetterie Aérienne',
    'hotel'       => 'Hôtel',
    'vehicule'    => 'Véhicule',
    'visa'        => 'Visa',
    'assurance'   => 'Assurance',
    'voyage'      => 'Voyage Organisé',
];
$statut_cfg = [
    'en_attente' => ['badge-warning',  'En attente'],
    'confirmee'  => ['badge-success',  'Confirmée'],
    'refusee'    => ['badge-danger',   'Refusée'],
    'annulee'    => ['badge-primary',  'Annulée'],
];
?>

<?php require_once dirname(dirname(dirname(__FILE__))) . '/templates/sidebar-admin.php'; ?>

<div class="admin-main">
    <?php require_once dirname(dirname(dirname(__FILE__))) . '/templates/navbar-admin.php'; ?>

    <div class="admin-content">
        <!-- Alertes flash -->
        <?php require_once dirname(dirname(dirname(__FILE__))) . '/templates/alertes.php'; ?>

        <h2 style="font-family:var(--font-title);margin-bottom:var(--spacing-md);">
            <i class="fa-solid fa-gauge-high" style="color:var(--secondary);"></i> Tableau de bord
        </h2>

        <!-- ── Widgets ── -->
        <div class="dashboard-grid">
            <a href="<?php echo SITE_URL; ?>pages/admin/gestion-reservations.php?statut=en_attente" class="widget-card" style="text-decoration:none;">
                <div class="widget-info">
                    <h3><?php echo $stats['en_attente']; ?></h3>
                    <p>Réservations en attente</p>
                </div>
                <div class="widget-icon pending"><i class="fa-solid fa-hourglass-half"></i></div>
            </a>
            <a href="<?php echo SITE_URL; ?>pages/admin/gestion-reservations.php?statut=confirmee" class="widget-card" style="text-decoration:none;">
                <div class="widget-info">
                    <h3><?php echo $stats['confirmees']; ?></h3>
                    <p>Confirmées</p>
                </div>
                <div class="widget-icon success"><i class="fa-solid fa-circle-check"></i></div>
            </a>
            <a href="<?php echo SITE_URL; ?>pages/admin/gestion-messages.php" class="widget-card" style="text-decoration:none;">
                <div class="widget-info">
                    <h3><?php echo $stats['messages']; ?></h3>
                    <p>Messages non lus</p>
                </div>
                <div class="widget-icon messages"><i class="fa-solid fa-envelope"></i></div>
            </a>
            <a href="<?php echo SITE_URL; ?>pages/admin/gestion-clients.php" class="widget-card" style="text-decoration:none;">
                <div class="widget-info">
                    <h3><?php echo $stats['clients']; ?></h3>
                    <p>Clients inscrits</p>
                </div>
                <div class="widget-icon clients"><i class="fa-solid fa-users"></i></div>
            </a>
        </div>

        <!-- ── 5 dernières réservations ── -->
        <div class="card" style="margin-bottom:0;">
            <div class="flex-between admin-card-header">
                <h3 style="font-family:var(--font-title);">
                    <i class="fa-solid fa-clock-rotate-left" style="color:var(--secondary);"></i>
                    5 dernières réservations
                </h3>
                <a href="<?php echo SITE_URL; ?>pages/admin/gestion-reservations.php" class="btn btn-outline btn-sm">Voir tout</a>
            </div>

            <?php if (empty($recentes)): ?>
                <p class="text-muted" style="padding:var(--spacing-md) 0;">Aucune réservation pour le moment.</p>
            <?php else: ?>
                <div class="reservation-table-wrapper" style="margin-top:var(--spacing-sm);">
                    <table class="reservation-table">
                        <thead>
                            <tr>
                                <th>#ID</th>
                                <th>Client</th>
                                <th>Service</th>
                                <th>Date soumission</th>
                                <th>Statut</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recentes as $res): ?>
                                <?php [$badge, $label] = $statut_cfg[$res['statut']] ?? ['badge-primary', $res['statut']]; ?>
                                <tr>
                                    <td>#<?php echo $res['id']; ?></td>
                                    <td><?php echo escape($res['prenom'] . ' ' . $res['nom']); ?></td>
                                    <td><?php echo escape($type_labels[$res['type_service']] ?? ucfirst($res['type_service'])); ?></td>
                                    <td><?php echo formatDateTime($res['date_demande']); ?></td>
                                    <td><span class="badge <?php echo $badge; ?>"><?php echo $label; ?></span></td>
                                    <td>
                                        <a href="<?php echo SITE_URL; ?>pages/admin/detail-reservation.php?id=<?php echo $res['id']; ?>" class="btn btn-primary btn-sm">
                                            <i class="fa-solid fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div><!-- /.admin-content -->

<?php require_once dirname(dirname(dirname(__FILE__))) . '/templates/footer-admin.php'; ?>
