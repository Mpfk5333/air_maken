<?php
// gestion-reservations.php - Liste & filtre de toutes les réservations (admin)
$admin_page_title = 'AIR MAKEN Admin - Gestion des réservations';
require_once dirname(dirname(dirname(__FILE__))) . '/templates/header-admin.php';
requireAdmin();
require_once dirname(dirname(dirname(__FILE__))) . '/traitements/config/connexion.php';

// ── Filtres GET ──────────────────────────────────────────────────────────────
$filtre_statut    = $_GET['statut']     ?? '';
$filtre_service   = $_GET['service']    ?? '';
$filtre_recherche = trim($_GET['q']     ?? '');

$type_labels = [
    'billetterie' => 'Billetterie Aérienne',
    'hotel'       => 'Hôtel',
    'vehicule'    => 'Véhicule',
    'visa'        => 'Visa',
    'assurance'   => 'Assurance',
    'voyage'      => 'Voyage Organisé',
];
$statuts_valides  = ['en_attente', 'confirmee', 'refusee', 'annulee'];
$services_valides = array_keys($type_labels);

// ── Requête dynamique ────────────────────────────────────────────────────────
$where  = [];
$params = [];

if (!empty($filtre_statut) && in_array($filtre_statut, $statuts_valides)) {
    $where[]  = "r.statut = :statut";
    $params['statut'] = $filtre_statut;
}
if (!empty($filtre_service) && in_array($filtre_service, $services_valides)) {
    $where[]  = "r.type_service = :service";
    $params['service'] = $filtre_service;
}
if (!empty($filtre_recherche)) {
    $where[]  = "(u.nom LIKE :q OR u.prenom LIKE :q OR u.email LIKE :q)";
    $params['q'] = '%' . $filtre_recherche . '%';
}

$sql = "SELECT r.id, r.type_service, r.statut, r.date_debut, r.date_fin, r.date_demande, r.montant,
               u.nom, u.prenom, u.email
        FROM reservations r
        JOIN utilisateurs u ON r.id_utilisateur = u.id"
     . (!empty($where) ? ' WHERE ' . implode(' AND ', $where) : '')
     . " ORDER BY r.date_demande DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$reservations = $stmt->fetchAll();

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
        <?php require_once dirname(dirname(dirname(__FILE__))) . '/templates/alertes.php'; ?>

        <div class="flex-between" style="margin-bottom:var(--spacing-md);">
            <h2 style="font-family:var(--font-title);">
                <i class="fa-solid fa-ticket" style="color:var(--secondary);"></i> Gestion des réservations
                <span class="badge badge-primary" style="margin-left:.5rem;"><?php echo count($reservations); ?></span>
            </h2>
        </div>

        <!-- ── Barre de filtres ── -->
        <div class="table-filter-bar">
            <form method="GET" action="">
                <input type="text" name="q" class="form-control" placeholder="Rechercher un client..." value="<?php echo escape($filtre_recherche); ?>" style="max-width:220px;">

                <select name="statut" class="form-control" style="max-width:180px;">
                    <option value="">Tous les statuts</option>
                    <?php foreach (['en_attente'=>'En attente','confirmee'=>'Confirmée','refusee'=>'Refusée','annulee'=>'Annulée'] as $v=>$l): ?>
                        <option value="<?php echo $v; ?>" <?php echo ($filtre_statut===$v)?'selected':''; ?>><?php echo $l; ?></option>
                    <?php endforeach; ?>
                </select>

                <select name="service" class="form-control" style="max-width:200px;">
                    <option value="">Tous les services</option>
                    <?php foreach ($type_labels as $v=>$l): ?>
                        <option value="<?php echo $v; ?>" <?php echo ($filtre_service===$v)?'selected':''; ?>><?php echo $l; ?></option>
                    <?php endforeach; ?>
                </select>

                <button type="submit" class="btn btn-primary btn-sm"><i class="fa-solid fa-filter"></i> Filtrer</button>
                <a href="<?php echo SITE_URL; ?>pages/admin/gestion-reservations.php" class="btn btn-outline btn-sm">Réinitialiser</a>
            </form>
        </div>

        <!-- ── Tableau ── -->
        <div class="card">
            <?php if (empty($reservations)): ?>
                <p class="text-muted py-md">Aucune réservation ne correspond aux filtres sélectionnés.</p>
            <?php else: ?>
                <div class="reservation-table-wrapper">
                    <table class="reservation-table">
                        <thead>
                            <tr>
                                <th>#ID</th>
                                <th>Client</th>
                                <th>Service</th>
                                <th>Date de départ</th>
                                <th>Soumis le</th>
                                <th>Tarif</th>
                                <th>Statut</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($reservations as $res): ?>
                                <?php [$badge, $label] = $statut_cfg[$res['statut']] ?? ['badge-primary', $res['statut']]; ?>
                                <tr>
                                    <td>#<?php echo $res['id']; ?></td>
                                    <td>
                                        <strong><?php echo escape($res['prenom'] . ' ' . $res['nom']); ?></strong>
                                        <br><span style="font-size:.8rem;color:var(--text-muted);"><?php echo escape($res['email']); ?></span>
                                    </td>
                                    <td><?php echo escape($type_labels[$res['type_service']] ?? ucfirst($res['type_service'])); ?></td>
                                    <td>
                                        <?php echo formatDate($res['date_debut']); ?>
                                        <?php if ($res['date_fin']): ?>
                                            <br><small>→ <?php echo formatDate($res['date_fin']); ?></small>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo formatDate($res['date_demande']); ?></td>
                                    <td><?php echo formatPrice($res['montant']); ?></td>
                                    <td><span class="badge <?php echo $badge; ?>"><?php echo $label; ?></span></td>
                                    <td>
                                        <a href="<?php echo SITE_URL; ?>pages/admin/detail-reservation.php?id=<?php echo $res['id']; ?>" class="btn btn-primary btn-sm" title="Voir le détail">
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
    </div>

<?php require_once dirname(dirname(dirname(__FILE__))) . '/templates/footer-admin.php'; ?>
