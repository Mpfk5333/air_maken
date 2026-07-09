<?php
// gestion-services.php - Catalogue de services (admin)
$admin_page_title = 'AIR MAKEN Admin - Gestion des Services';
require_once dirname(dirname(dirname(__FILE__))) . '/templates/header-admin.php';
requireAdmin();
require_once dirname(dirname(dirname(__FILE__))) . '/traitements/config/connexion.php';

$services = $pdo->query("SELECT * FROM services ORDER BY categorie, nom ASC")->fetchAll();

$categorie_labels = [
    'billetterie' => 'Billetterie Aérienne',
    'hotel'       => 'Hôtel',
    'vehicule'    => 'Location Véhicule',
    'visa'        => 'Assistance Visa',
    'assurance'   => 'Assurance Voyage',
    'voyage'      => 'Voyage Organisé',
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
                <i class="fa-solid fa-cubes" style="color:var(--secondary);"></i> Catalogue de services
                <span class="badge badge-primary" style="margin-left:.5rem;"><?php echo count($services); ?></span>
            </h2>
            <a href="<?php echo SITE_URL; ?>pages/admin/ajouter-service.php" class="btn btn-secondary">
                <i class="fa-solid fa-plus"></i> Ajouter un service
            </a>
        </div>

        <div class="card">
            <?php if (empty($services)): ?>
                <p class="text-muted py-md">Aucun service enregistré pour le moment.</p>
            <?php else: ?>
                <div class="reservation-table-wrapper">
                    <table class="reservation-table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Nom du service</th>
                                <th>Catégorie</th>
                                <th>Prix indicatif</th>
                                <th>Description (extrait)</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($services as $svc): ?>
                            <tr>
                                <td>#<?php echo $svc['id']; ?></td>
                                <td><strong><?php echo escape($svc['nom']); ?></strong></td>
                                <td><?php echo escape($categorie_labels[$svc['categorie']] ?? ucfirst($svc['categorie'])); ?></td>
                                <td><?php echo formatPrice($svc['prix_indicatif']); ?></td>
                                <td style="max-width:250px;"><?php echo escape(mb_strimwidth($svc['description'] ?? '', 0, 100, '…')); ?></td>
                                <td style="white-space:nowrap;">
                                    <a href="<?php echo SITE_URL; ?>pages/admin/modifier-service.php?id=<?php echo $svc['id']; ?>" class="btn btn-primary btn-sm" title="Modifier">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </a>
                                    <form action="<?php echo SITE_URL; ?>traitements/services/traitement_suppression_service.php" method="POST" style="display:inline;" onsubmit="return confirm('Supprimer ce service ?');">
                                        <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                                        <input type="hidden" name="service_id" value="<?php echo $svc['id']; ?>">
                                        <button type="submit" class="btn btn-danger btn-sm" title="Supprimer">
                                            <i class="fa-solid fa-trash"></i>
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
