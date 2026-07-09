<?php
// statistiques.php - Statistiques d'activité et rapports exportables (admin)
$admin_page_title = 'AIR MAKEN Admin - Statistiques';
require_once dirname(dirname(dirname(__FILE__))) . '/templates/header-admin.php';
requireAdmin();
require_once dirname(dirname(dirname(__FILE__))) . '/traitements/config/connexion.php';

// 1. Clients inscrits
$nb_clients = $pdo->query("SELECT COUNT(*) FROM utilisateurs")->fetchColumn();

// 2. Messages
$msg_stmt = $pdo->query("SELECT COUNT(*), SUM(CASE WHEN statut = 'non_lu' THEN 1 ELSE 0 END) FROM messages_contact");
$msg_stats = $msg_stmt->fetch(PDO::FETCH_NUM);
$nb_messages = $msg_stats[0] ?? 0;
$nb_messages_non_lu = $msg_stats[1] ?? 0;

// 3. Réservations globales & Revenus
$res_stmt = $pdo->query("SELECT COUNT(*), SUM(CASE WHEN statut = 'confirmee' THEN montant ELSE 0 END) FROM reservations");
$res_stats = $res_stmt->fetch(PDO::FETCH_NUM);
$nb_reservations = $res_stats[0] ?? 0;
$revenu_total = $res_stats[1] ?? 0;

// 4. Par statut
$status_stmt = $pdo->query("SELECT statut, COUNT(*) as count FROM reservations GROUP BY statut");
$status_counts = $status_stmt->fetchAll(PDO::FETCH_KEY_PAIR);

$statuts_label = [
    'en_attente' => 'En attente',
    'confirmee'  => 'Confirmées',
    'refusee'    => 'Refusées',
    'annulee'    => 'Annulées',
];

// 5. Par catégorie
$cat_stmt = $pdo->query("SELECT type_service, COUNT(*) as count, SUM(montant) as revenue FROM reservations GROUP BY type_service");
$cat_stats = $cat_stmt->fetchAll(PDO::FETCH_ASSOC);

$categories_label = [
    'billetterie' => 'Billetterie Aérienne',
    'hotel'       => 'Hôtels',
    'vehicule'    => 'Location de Véhicules',
    'visa'        => 'Assistance Visa',
    'assurance'   => 'Assurance Voyage',
    'voyage'      => 'Voyages Organisés',
];

$csrf_token = generateCsrfToken();
?>

<?php require_once dirname(dirname(dirname(__FILE__))) . '/templates/sidebar-admin.php'; ?>

<div class="admin-main">
    <?php require_once dirname(dirname(dirname(__FILE__))) . '/templates/navbar-admin.php'; ?>
    <div class="admin-content">
        <?php require_once dirname(dirname(dirname(__FILE__))) . '/templates/alertes.php'; ?>

        <div class="flex-between" style="margin-bottom:var(--spacing-md);">
            <h2 style="font-family:var(--font-title);">
                <i class="fa-solid fa-chart-line" style="color:var(--secondary);"></i> Rapports & Analyses
            </h2>
            <form action="<?php echo SITE_URL; ?>traitements/admin/traitement_statistiques.php" method="POST">
                <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                <button type="submit" class="btn btn-secondary">
                    <i class="fa-solid fa-file-csv"></i> Exporter les réservations (CSV)
                </button>
            </form>
        </div>

        <!-- Section des widgets clés -->
        <div class="admin-stats-grid" style="display:grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap:var(--spacing-sm); margin-bottom:var(--spacing-md);">
            <div class="card" style="display:flex; align-items:center; gap:var(--spacing-sm);">
                <div style="font-size:2.5rem; color:var(--secondary);"><i class="fa-solid fa-wallet"></i></div>
                <div>
                    <p class="text-muted" style="font-size:0.85rem; text-transform:uppercase; font-weight:700;">Chiffre d'Affaires</p>
                    <h3 style="font-family:var(--font-title); font-size:1.4rem; font-weight:800; color:var(--primary);"><?php echo formatPrice($revenu_total); ?></h3>
                </div>
            </div>
            <div class="card" style="display:flex; align-items:center; gap:var(--spacing-sm);">
                <div style="font-size:2.5rem; color:var(--primary);"><i class="fa-solid fa-receipt"></i></div>
                <div>
                    <p class="text-muted" style="font-size:0.85rem; text-transform:uppercase; font-weight:700;">Réservations</p>
                    <h3 style="font-family:var(--font-title); font-size:1.4rem; font-weight:800; color:var(--primary);"><?php echo $nb_reservations; ?></h3>
                </div>
            </div>
            <div class="card" style="display:flex; align-items:center; gap:var(--spacing-sm);">
                <div style="font-size:2.5rem; color:var(--success);"><i class="fa-solid fa-users"></i></div>
                <div>
                    <p class="text-muted" style="font-size:0.85rem; text-transform:uppercase; font-weight:700;">Clients Actifs</p>
                    <h3 style="font-family:var(--font-title); font-size:1.4rem; font-weight:800; color:var(--primary);"><?php echo $nb_clients; ?></h3>
                </div>
            </div>
            <div class="card" style="display:flex; align-items:center; gap:var(--spacing-sm);">
                <div style="font-size:2.5rem; color:var(--danger);"><i class="fa-solid fa-comments"></i></div>
                <div>
                    <p class="text-muted" style="font-size:0.85rem; text-transform:uppercase; font-weight:700;">Messages Non Lus</p>
                    <h3 style="font-family:var(--font-title); font-size:1.4rem; font-weight:800; color:var(--primary);"><?php echo $nb_messages_non_lu; ?> / <?php echo $nb_messages; ?></h3>
                </div>
            </div>
        </div>

        <div class="detail-grid">
            <!-- Graphiques / Proportions des Réservations -->
            <div class="card">
                <div class="admin-card-header" style="border-bottom:1px solid var(--border-color); padding-bottom:var(--spacing-xs); margin-bottom:var(--spacing-sm);">
                    <h3 style="font-family:var(--font-title);">Répartition des Réservations par Statut</h3>
                </div>

                <div style="display:flex; flex-direction:column; gap:var(--spacing-sm); margin-top:var(--spacing-sm);">
                    <?php
                    $colors = [
                        'en_attente' => 'var(--warning)',
                        'confirmee'  => 'var(--success)',
                        'refusee'    => 'var(--danger)',
                        'annulee'    => 'var(--primary)',
                    ];
                    foreach ($statuts_label as $key => $lbl):
                        $count = $status_counts[$key] ?? 0;
                        $percent = $nb_reservations > 0 ? round(($count / $nb_reservations) * 100) : 0;
                        $color = $colors[$key] ?? 'var(--text-muted)';
                    ?>
                        <div>
                            <div class="flex-between" style="font-size:0.9rem; margin-bottom:0.25rem;">
                                <span><strong><?php echo $lbl; ?></strong> (<?php echo $count; ?>)</span>
                                <span><?php echo $percent; ?>%</span>
                            </div>
                            <div style="width:100%; height:12px; background-color:var(--bg-main); border-radius:var(--radius-full); overflow:hidden;">
                                <div style="width:<?php echo $percent; ?>%; height:100%; background-color:<?php echo $color; ?>; border-radius:var(--radius-full);"></div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Résumé par catégorie de service -->
            <div class="card">
                <div class="admin-card-header" style="border-bottom:1px solid var(--border-color); padding-bottom:var(--spacing-xs); margin-bottom:var(--spacing-sm);">
                    <h3 style="font-family:var(--font-title);">Performance par Catégorie</h3>
                </div>
                <div class="reservation-table-wrapper">
                    <table class="reservation-table" style="font-size:0.85rem;">
                        <thead>
                            <tr>
                                <th>Service</th>
                                <th style="text-align:center;">Volume</th>
                                <th style="text-align:right;">Revenus</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($cat_stats as $row): ?>
                            <tr>
                                <td><strong><?php echo escape($categories_label[$row['type_service']] ?? ucfirst($row['type_service'])); ?></strong></td>
                                <td style="text-align:center;"><?php echo $row['count']; ?></td>
                                <td style="text-align:right; font-weight:700; color:var(--success);"><?php echo formatPrice($row['revenue']); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>


<?php require_once dirname(dirname(dirname(__FILE__))) . '/templates/footer-admin.php'; ?>
