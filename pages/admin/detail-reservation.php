<?php
// detail-reservation.php - Fiche complète d'une réservation (admin)
$admin_page_title = 'AIR MAKEN Admin - Détail Réservation';
require_once dirname(dirname(dirname(__FILE__))) . '/templates/header-admin.php';
requireAdmin();
require_once dirname(dirname(dirname(__FILE__))) . '/traitements/config/connexion.php';

$id = intval($_GET['id'] ?? 0);
if ($id <= 0) {
    header("Location: " . SITE_URL . "pages/admin/gestion-reservations.php");
    exit;
}

// ── Charger la réservation ────────────────────────────────────────────────────
$stmt = $pdo->prepare(
    "SELECT r.*, s.nom AS nom_service,
            u.nom AS client_nom, u.prenom AS client_prenom,
            u.email AS client_email, u.telephone AS client_tel
     FROM reservations r
     LEFT JOIN services s ON r.id_service = s.id
     JOIN utilisateurs u ON r.id_utilisateur = u.id
     WHERE r.id = :id"
);
$stmt->execute(['id' => $id]);
$res = $stmt->fetch();

if (!$res) {
    setFlashMessage('danger', "Réservation introuvable.");
    header("Location: " . SITE_URL . "pages/admin/gestion-reservations.php");
    exit;
}

$details = json_decode($res['details'], true) ?? [];
$csrf_token = generateCsrfToken();

$type_labels = [
    'billetterie' => 'Billetterie Aérienne',
    'hotel'       => 'Hôtel',
    'vehicule'    => 'Location de Véhicule',
    'visa'        => 'Assistance Visa',
    'assurance'   => 'Assurance Voyage',
    'voyage'      => 'Voyage Organisé',
];
$statut_cfg = [
    'en_attente' => ['badge-warning',  'En attente'],
    'confirmee'  => ['badge-success',  'Confirmée'],
    'refusee'    => ['badge-danger',   'Refusée'],
    'annulee'    => ['badge-primary',  'Annulée'],
];
[$badge, $statut_label] = $statut_cfg[$res['statut']] ?? ['badge-primary', $res['statut']];
?>

<?php require_once dirname(dirname(dirname(__FILE__))) . '/templates/sidebar-admin.php'; ?>

<div class="admin-main">
    <?php require_once dirname(dirname(dirname(__FILE__))) . '/templates/navbar-admin.php'; ?>

    <div class="admin-content">
        <?php require_once dirname(dirname(dirname(__FILE__))) . '/templates/alertes.php'; ?>

        <!-- Breadcrumb -->
        <div style="margin-bottom:var(--spacing-sm); font-size:.9rem; color:var(--text-muted);">
            <a href="<?php echo SITE_URL; ?>pages/admin/gestion-reservations.php">Réservations</a>
            <i class="fa-solid fa-chevron-right" style="margin:0 .4rem; font-size:.7rem;"></i>
            <strong>Réservation #<?php echo $res['id']; ?></strong>
        </div>

        <div class="detail-grid">
            <!-- ── Colonne principale ── -->
            <div>
                <!-- Coordonnées Client -->
                <div class="card" style="margin-bottom:var(--spacing-md);">
                    <div class="admin-card-header">
                        <h3 style="font-family:var(--font-title);">
                            <i class="fa-solid fa-user" style="color:var(--secondary);"></i> Client
                        </h3>
                    </div>
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:var(--spacing-sm);">
                        <div>
                            <p style="font-size:.75rem;color:var(--text-muted);font-weight:700;text-transform:uppercase;">Nom complet</p>
                            <p><strong><?php echo escape($res['client_prenom'] . ' ' . $res['client_nom']); ?></strong></p>
                        </div>
                        <div>
                            <p style="font-size:.75rem;color:var(--text-muted);font-weight:700;text-transform:uppercase;">Email</p>
                            <p><?php echo escape($res['client_email']); ?></p>
                        </div>
                        <div>
                            <p style="font-size:.75rem;color:var(--text-muted);font-weight:700;text-transform:uppercase;">Téléphone</p>
                            <p><?php echo escape($res['client_tel'] ?? '-'); ?></p>
                        </div>
                        <div>
                            <p style="font-size:.75rem;color:var(--text-muted);font-weight:700;text-transform:uppercase;">ID Client</p>
                            <p>#<?php echo $res['id_utilisateur']; ?></p>
                        </div>
                    </div>
                </div>

                <!-- Détails de la réservation -->
                <div class="card" style="margin-bottom:var(--spacing-md);">
                    <div class="admin-card-header">
                        <h3 style="font-family:var(--font-title);">
                            <i class="fa-solid fa-ticket" style="color:var(--secondary);"></i>
                            <?php echo escape($type_labels[$res['type_service']] ?? ucfirst($res['type_service'])); ?>
                        </h3>
                    </div>

                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:var(--spacing-sm); margin-bottom:var(--spacing-sm);">
                        <div>
                            <p style="font-size:.75rem;color:var(--text-muted);font-weight:700;text-transform:uppercase;">Date de départ</p>
                            <p><?php echo formatDate($res['date_debut']); ?></p>
                        </div>
                        <?php if (!empty($res['date_fin'])): ?>
                        <div>
                            <p style="font-size:.75rem;color:var(--text-muted);font-weight:700;text-transform:uppercase;">Date de retour</p>
                            <p><?php echo formatDate($res['date_fin']); ?></p>
                        </div>
                        <?php endif; ?>
                        <div>
                            <p style="font-size:.75rem;color:var(--text-muted);font-weight:700;text-transform:uppercase;">Demande soumise le</p>
                            <p><?php echo formatDateTime($res['date_demande']); ?></p>
                        </div>
                        <div>
                            <p style="font-size:.75rem;color:var(--text-muted);font-weight:700;text-transform:uppercase;">Tarif indicatif</p>
                            <p style="font-weight:700;color:var(--primary);"><?php echo formatPrice($res['montant']); ?></p>
                        </div>
                    </div>

                    <!-- Champs spécifiques au service -->
                    <?php if (!empty($details)): ?>
                        <hr style="border-color:var(--border-color);margin:var(--spacing-sm) 0;">
                        <h4 style="font-size:.85rem;font-weight:700;color:var(--text-muted);text-transform:uppercase;margin-bottom:var(--spacing-xs);">
                            Informations spécifiques
                        </h4>
                        <ul class="details-list">
                            <?php foreach ($details as $key => $value): ?>
                                <?php if ($key === 'remarques') continue; ?>
                                <li><strong><?php echo escape(ucwords(str_replace('_', ' ', $key))); ?> :</strong> <?php echo escape(is_array($value) ? implode(', ', $value) : $value); ?></li>
                            <?php endforeach; ?>
                            <?php if (!empty($details['remarques'])): ?>
                                <li style="margin-top:.5rem;border-top:1px dashed var(--border-color);padding-top:.5rem;">
                                    <strong><i class="fa-regular fa-comment-dots"></i> Remarques :</strong>
                                    <em>"<?php echo escape($details['remarques']); ?>"</em>
                                </li>
                            <?php endif; ?>
                        </ul>
                    <?php endif; ?>

                    <!-- Motif de refus si applicable -->
                    <?php if (!empty($res['motif_refus'])): ?>
                        <div class="alert alert-danger" style="margin-top:var(--spacing-sm);">
                            <i class="fa-solid fa-circle-exclamation"></i>
                            <strong>Motif de refus :</strong> <?php echo escape($res['motif_refus']); ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- ── Colonne latérale Actions ── -->
            <div>
                <div class="card">
                    <div class="admin-card-header">
                        <h3 style="font-family:var(--font-title);">
                            <i class="fa-solid fa-sliders" style="color:var(--secondary);"></i> Actions
                        </h3>
                    </div>

                    <!-- Statut actuel -->
                    <div style="text-align:center;margin-bottom:var(--spacing-md);">
                        <p style="font-size:.75rem;color:var(--text-muted);font-weight:700;text-transform:uppercase;margin-bottom:.3rem;">Statut actuel</p>
                        <span class="badge <?php echo $badge; ?>" style="font-size:1rem;padding:.5rem 1.25rem;">
                            <?php echo $statut_label; ?>
                        </span>
                    </div>

                    <?php if ($res['statut'] === 'en_attente'): ?>
                        <!-- Confirmer -->
                        <form action="<?php echo SITE_URL; ?>traitements/reservations/traitement_modification_statut.php" method="POST" style="margin-bottom:var(--spacing-sm);">
                            <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                            <input type="hidden" name="reservation_id" value="<?php echo $res['id']; ?>">
                            <input type="hidden" name="action" value="confirmer">
                            <button type="submit" class="btn btn-success btn-lg" style="width:100%;"
                                    onclick="return confirm('Confirmer cette réservation ?');">
                                <i class="fa-solid fa-circle-check"></i> Confirmer la réservation
                            </button>
                        </form>

                        <!-- Refuser -->
                        <form action="<?php echo SITE_URL; ?>traitements/reservations/traitement_modification_statut.php" method="POST" id="refusForm">
                            <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                            <input type="hidden" name="reservation_id" value="<?php echo $res['id']; ?>">
                            <input type="hidden" name="action" value="refuser">
                            <div class="form-group">
                                <label for="motif_refus" class="form-label">
                                    Motif de refus <span style="color:var(--danger);">*</span>
                                </label>
                                <textarea name="motif_refus" id="motif_refus" class="form-control" rows="3"
                                          placeholder="Expliquez la raison du refus..."></textarea>
                            </div>
                            <button type="submit" class="btn btn-danger btn-lg" style="width:100%;"
                                    onclick="return confirmRefus();">
                                <i class="fa-solid fa-circle-xmark"></i> Refuser la réservation
                            </button>
                        </form>

                    <?php elseif ($res['statut'] === 'confirmee'): ?>
                        <!-- Possibilité de repasser en refus -->
                        <form action="<?php echo SITE_URL; ?>traitements/reservations/traitement_modification_statut.php" method="POST" id="refusForm2">
                            <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                            <input type="hidden" name="reservation_id" value="<?php echo $res['id']; ?>">
                            <input type="hidden" name="action" value="refuser">
                            <div class="form-group">
                                <label for="motif_refus2" class="form-label">Motif de refus <span style="color:var(--danger);">*</span></label>
                                <textarea name="motif_refus" id="motif_refus2" class="form-control" rows="3"
                                          placeholder="Motif obligatoire..."></textarea>
                            </div>
                            <button type="submit" class="btn btn-danger" style="width:100%;"
                                    onclick="return confirmRefus2();">
                                <i class="fa-solid fa-circle-xmark"></i> Annuler / Refuser
                            </button>
                        </form>
                    <?php else: ?>
                        <p class="text-muted" style="text-align:center;">Cette réservation a été traitée définitivement.</p>
                    <?php endif; ?>

                    <div style="margin-top:var(--spacing-md); padding-top:var(--spacing-sm);border-top:1px solid var(--border-color);">
                        <a href="<?php echo SITE_URL; ?>pages/admin/gestion-reservations.php" class="btn btn-outline btn-sm" style="width:100%;">
                            <i class="fa-solid fa-arrow-left"></i> Retour à la liste
                        </a>
                    </div>
                </div>
            </div><!-- /col actions -->
        </div><!-- /detail-grid -->
    </div><!-- /admin-content -->

<script>
function confirmRefus() {
    const motif = document.getElementById('motif_refus').value.trim();
    if (!motif) {
        alert("Veuillez saisir un motif de refus obligatoire.");
        return false;
    }
    return confirm("Refuser cette réservation ?");
}
function confirmRefus2() {
    const motif = document.getElementById('motif_refus2').value.trim();
    if (!motif) {
        alert("Veuillez saisir un motif de refus obligatoire.");
        return false;
    }
    return confirm("Annuler/Refuser cette réservation confirmée ?");
}
</script>

<?php require_once dirname(dirname(dirname(__FILE__))) . '/templates/footer-admin.php'; ?>
