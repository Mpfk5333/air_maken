<?php
// ajouter-service.php - Formulaire d'ajout d'un service (admin)
$admin_page_title = 'AIR MAKEN Admin - Ajouter un Service';
require_once dirname(dirname(dirname(__FILE__))) . '/templates/header-admin.php';
requireAdmin();

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
            <strong>Ajouter un service</strong>
        </div>

        <div class="card" style="max-width:760px;">
            <div class="admin-card-header">
                <h3 style="font-family:var(--font-title);">
                    <i class="fa-solid fa-plus-circle" style="color:var(--secondary);"></i> Nouveau service
                </h3>
            </div>

            <form action="<?php echo SITE_URL; ?>traitements/services/traitement_ajout_service.php" method="POST">
                <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">

                <div class="form-group">
                    <label for="nom" class="form-label">Nom du service <span style="color:var(--danger);">*</span></label>
                    <input type="text" name="nom" id="nom" class="form-control" placeholder="Ex: Vol Malabo → Madrid" required>
                </div>

                <div class="form-group">
                    <label for="categorie" class="form-label">Catégorie <span style="color:var(--danger);">*</span></label>
                    <select name="categorie" id="categorie" class="form-control" required>
                        <option value="">-- Choisir --</option>
                        <?php foreach ($categories as $val => $label): ?>
                            <option value="<?php echo $val; ?>"><?php echo $label; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="description" class="form-label">Description</label>
                    <textarea name="description" id="description" class="form-control" rows="4" placeholder="Décrivez le service..."></textarea>
                </div>

                <div class="form-group">
                    <label for="prix_indicatif" class="form-label">Prix indicatif (FCFA) <small class="text-muted">— laisser vide pour "Sur devis"</small></label>
                    <input type="number" name="prix_indicatif" id="prix_indicatif" class="form-control" placeholder="Ex: 150000" min="0" step="500">
                </div>

                <div style="display:flex; gap:var(--spacing-sm); margin-top:var(--spacing-md);">
                    <button type="submit" class="btn btn-secondary btn-lg"><i class="fa-solid fa-floppy-disk"></i> Enregistrer</button>
                    <a href="<?php echo SITE_URL; ?>pages/admin/gestion-services.php" class="btn btn-outline btn-lg">Annuler</a>
                </div>
            </form>
        </div>
    </div>

<?php require_once dirname(dirname(dirname(__FILE__))) . '/templates/footer-admin.php'; ?>
