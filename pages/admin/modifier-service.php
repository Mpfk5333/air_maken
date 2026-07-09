<?php
// modifier-service.php - Formulaire d'édition d'un service (admin)
$admin_page_title = 'AIR MAKEN Admin - Modifier un Service';
require_once dirname(dirname(dirname(__FILE__))) . '/templates/header-admin.php';
requireAdmin();
require_once dirname(dirname(dirname(__FILE__))) . '/traitements/config/connexion.php';

$id = intval($_GET['id'] ?? 0);
if ($id <= 0) {
    header("Location: " . SITE_URL . "pages/admin/gestion-services.php");
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM services WHERE id = :id");
$stmt->execute(['id' => $id]);
$svc = $stmt->fetch();

if (!$svc) {
    setFlashMessage('danger', "Service introuvable.");
    header("Location: " . SITE_URL . "pages/admin/gestion-services.php");
    exit;
}

$csrf_token = generateCsrfToken();

$categories = [
    'billetterie' => 'Billetterie Aérienne',
    'hotel'       => 'Hôtel',
    'vehicule'    => 'Location Véhicule',
    'visa'        => 'Assistance Visa',
    'assurance'   => 'Assurance Voyage',
    'voyage'      => 'Voyage Organisé',
];
?>

<?php require_once dirname(dirname(dirname(__FILE__))) . '/templates/sidebar-admin.php'; ?>

<div class="admin-main">
    <?php require_once dirname(dirname(dirname(__FILE__))) . '/templates/navbar-admin.php'; ?>
    <div class="admin-content">
        <?php require_once dirname(dirname(dirname(__FILE__))) . '/templates/alertes.php'; ?>

        <div style="margin-bottom:var(--spacing-sm); font-size:.9rem; color:var(--text-muted);">
            <a href="<?php echo SITE_URL; ?>pages/admin/gestion-services.php">Services</a>
            <i class="fa-solid fa-chevron-right" style="margin:0 .4rem; font-size:.7rem;"></i>
            <strong>Modifier : <?php echo escape($svc['nom']); ?></strong>
        </div>

        <div class="card" style="max-width:760px;">
            <div class="admin-card-header">
                <h3 style="font-family:var(--font-title);">
                    <i class="fa-solid fa-pen-to-square" style="color:var(--secondary);"></i> Modifier le service #<?php echo $svc['id']; ?>
                </h3>
            </div>

            <form action="<?php echo SITE_URL; ?>traitements/services/traitement_modification_service.php" method="POST">
                <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                <input type="hidden" name="service_id" value="<?php echo $svc['id']; ?>">

                <div class="form-group">
                    <label for="nom" class="form-label">Nom du service <span style="color:var(--danger);">*</span></label>
                    <input type="text" name="nom" id="nom" class="form-control" value="<?php echo escape($svc['nom']); ?>" required>
                </div>

                <div class="form-group">
                    <label for="categorie" class="form-label">Catégorie <span style="color:var(--danger);">*</span></label>
                    <select name="categorie" id="categorie" class="form-control" required>
                        <?php foreach ($categories as $val => $label): ?>
                            <option value="<?php echo $val; ?>" <?php echo ($svc['categorie'] === $val) ? 'selected' : ''; ?>><?php echo $label; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="description" class="form-label">Description</label>
                    <textarea name="description" id="description" class="form-control" rows="4"><?php echo escape($svc['description'] ?? ''); ?></textarea>
                </div>

                <div class="form-group">
                    <label for="prix_indicatif" class="form-label">Prix indicatif (FCFA) <small class="text-muted">— laisser vide pour "Sur devis"</small></label>
                    <input type="number" name="prix_indicatif" id="prix_indicatif" class="form-control"
                           value="<?php echo !is_null($svc['prix_indicatif']) ? $svc['prix_indicatif'] : ''; ?>" min="0" step="500">
                </div>

                <div style="display:flex; gap:var(--spacing-sm); margin-top:var(--spacing-md);">
                    <button type="submit" class="btn btn-secondary btn-lg"><i class="fa-solid fa-floppy-disk"></i> Enregistrer les modifications</button>
                    <a href="<?php echo SITE_URL; ?>pages/admin/gestion-services.php" class="btn btn-outline btn-lg">Annuler</a>
                </div>
            </form>
        </div>
    </div>

<?php require_once dirname(dirname(dirname(__FILE__))) . '/templates/footer-admin.php'; ?>
