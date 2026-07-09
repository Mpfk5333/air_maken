<?php
// parametres.php - Paramètres généraux de l'application (Super Admin)
$admin_page_title = 'AIR MAKEN Admin - Paramètres';
require_once dirname(dirname(dirname(__FILE__))) . '/templates/header-admin.php';
requireAdmin();

// Réservé uniquement aux super_admin
if (!isSuperAdmin()) {
    setFlashMessage('danger', "Accès réservé aux Super Administrateurs.");
    header("Location: " . SITE_URL . "pages/admin/tableau-de-bord.php");
    exit;
}

require_once dirname(dirname(dirname(__FILE__))) . '/traitements/config/connexion.php';

$csrf_token = generateCsrfToken();

// Informations de la session admin actuelle
$admin_nom   = escape($_SESSION['admin_nom'] ?? 'Administrateur');
$admin_role  = $_SESSION['admin_role'] ?? 'agent';
$admin_email = escape($_SESSION['admin_email'] ?? '');
?>

<?php require_once dirname(dirname(dirname(__FILE__))) . '/templates/sidebar-admin.php'; ?>

<div class="admin-main">
    <?php require_once dirname(dirname(dirname(__FILE__))) . '/templates/navbar-admin.php'; ?>
    <div class="admin-content">
        <?php require_once dirname(dirname(dirname(__FILE__))) . '/templates/alertes.php'; ?>

        <div class="flex-between" style="margin-bottom:var(--spacing-md);">
            <h2 style="font-family:var(--font-title);">
                <i class="fa-solid fa-sliders" style="color:var(--secondary);"></i> Paramètres de l'Application
            </h2>
        </div>

        <div class="detail-grid">
            <!-- Modifier le mot de passe admin -->
            <div class="card">
                <div class="admin-card-header" style="border-bottom:1px solid var(--border-color); padding-bottom:var(--spacing-xs); margin-bottom:var(--spacing-md);">
                    <h3 style="font-family:var(--font-title);">
                        <i class="fa-solid fa-key" style="color:var(--secondary);"></i> Changer mon mot de passe
                    </h3>
                </div>

                <p class="text-muted" style="margin-bottom:var(--spacing-md); font-size:0.9rem;">
                    Connecté en tant que <strong><?php echo $admin_nom; ?></strong>
                    <span class="badge <?php echo ($admin_role === 'super_admin') ? 'badge-danger' : 'badge-info'; ?>" style="margin-left:.5rem;">
                        <?php echo ($admin_role === 'super_admin') ? 'Super Admin' : 'Agent'; ?>
                    </span>
                </p>

                <form action="<?php echo SITE_URL; ?>traitements/admin/traitement_changement_mdp_admin.php" method="POST">
                    <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">

                    <div class="form-group">
                        <label for="ancien_mdp" class="form-label">Mot de passe actuel <span style="color:var(--danger);">*</span></label>
                        <input type="password" name="ancien_mdp" id="ancien_mdp" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label for="nouveau_mdp" class="form-label">Nouveau mot de passe <span style="color:var(--danger);">*</span></label>
                        <input type="password" name="nouveau_mdp" id="nouveau_mdp" class="form-control" required minlength="8">
                    </div>

                    <div class="form-group">
                        <label for="confirm_mdp" class="form-label">Confirmer le nouveau mot de passe <span style="color:var(--danger);">*</span></label>
                        <input type="password" name="confirm_mdp" id="confirm_mdp" class="form-control" required minlength="8">
                    </div>

                    <button type="submit" class="btn btn-primary btn-lg" style="width:100%;">
                        <i class="fa-solid fa-lock"></i> Mettre à jour le mot de passe
                    </button>
                </form>
            </div>

            <!-- Informations Système -->
            <div>
                <!-- Infos de connexion -->
                <div class="card" style="margin-bottom:var(--spacing-md);">
                    <div class="admin-card-header" style="border-bottom:1px solid var(--border-color); padding-bottom:var(--spacing-xs); margin-bottom:var(--spacing-sm);">
                        <h3 style="font-family:var(--font-title);">
                            <i class="fa-solid fa-circle-info" style="color:var(--secondary);"></i> Informations Système
                        </h3>
                    </div>

                    <div style="display:grid; grid-template-columns:1fr 1fr; gap:var(--spacing-sm);">
                        <div>
                            <p style="font-size:.75rem;color:var(--text-muted);font-weight:700;text-transform:uppercase;">Version PHP</p>
                            <p><strong><?php echo PHP_VERSION; ?></strong></p>
                        </div>
                        <div>
                            <p style="font-size:.75rem;color:var(--text-muted);font-weight:700;text-transform:uppercase;">URL du Site</p>
                            <p style="word-break:break-all;"><strong><?php echo escape(SITE_URL); ?></strong></p>
                        </div>
                        <div>
                            <p style="font-size:.75rem;color:var(--text-muted);font-weight:700;text-transform:uppercase;">Répertoire Uploads</p>
                            <p><strong><?php echo escape(UPLOAD_DIR); ?></strong></p>
                        </div>
                        <div>
                            <p style="font-size:.75rem;color:var(--text-muted);font-weight:700;text-transform:uppercase;">Date/Heure Serveur</p>
                            <p><strong><?php echo date('d/m/Y H:i:s'); ?></strong></p>
                        </div>
                    </div>

                    <div style="margin-top:var(--spacing-md); padding:var(--spacing-sm); background:var(--bg-main); border-radius:var(--radius-md); border-left:4px solid var(--success);">
                        <p style="font-size:0.85rem; color:var(--success); font-weight:700;">
                            <i class="fa-solid fa-circle-check"></i> Base de données MySQL connectée avec succès via PDO.
                        </p>
                    </div>
                </div>

                <!-- Statistiques rapides -->
                <div class="card">
                    <div class="admin-card-header" style="border-bottom:1px solid var(--border-color); padding-bottom:var(--spacing-xs); margin-bottom:var(--spacing-sm);">
                        <h3 style="font-family:var(--font-title);">
                            <i class="fa-solid fa-database" style="color:var(--secondary);"></i> État de la Base de données
                        </h3>
                    </div>
                    <?php
                    $tables = [
                        'utilisateurs'     => ['fa-users', 'Clients inscrits'],
                        'administrateurs'  => ['fa-user-shield', 'Administrateurs'],
                        'services'         => ['fa-cubes', 'Services actifs'],
                        'reservations'     => ['fa-ticket', 'Réservations'],
                        'messages_contact' => ['fa-envelope', 'Messages reçus'],
                        'contenus'         => ['fa-file-lines', 'Blocs de contenu'],
                    ];
                    ?>
                    <div style="display:flex; flex-direction:column; gap:var(--spacing-xs);">
                        <?php foreach ($tables as $table => $info): ?>
                            <?php
                            try {
                                $c = $pdo->query("SELECT COUNT(*) FROM `$table`")->fetchColumn();
                            } catch (\Exception $e) {
                                $c = '?';
                            }
                            ?>
                            <div class="flex-between" style="padding:0.6rem var(--spacing-sm); background:var(--bg-main); border-radius:var(--radius-sm);">
                                <span style="font-size:0.9rem;">
                                    <i class="fa-solid <?php echo $info[0]; ?>" style="width:20px; color:var(--primary);"></i>
                                    <?php echo $info[1]; ?>
                                </span>
                                <strong><?php echo $c; ?></strong>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

<?php require_once dirname(dirname(dirname(__FILE__))) . '/templates/footer-admin.php'; ?>
