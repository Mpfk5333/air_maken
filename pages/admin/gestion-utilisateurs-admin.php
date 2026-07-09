<?php
// gestion-utilisateurs-admin.php - Comptes admin et agents (Super Admin only)
$admin_page_title = 'AIR MAKEN Admin - Gestion des Administrateurs';
require_once dirname(dirname(dirname(__FILE__))) . '/templates/header-admin.php';
requireAdmin();

// Sécurité supplémentaire : Réservé uniquement aux super_admin
if (!isSuperAdmin()) {
    setFlashMessage('danger', "Accès réservé aux Super Administrateurs.");
    header("Location: " . SITE_URL . "pages/admin/tableau-de-bord.php");
    exit;
}

require_once dirname(dirname(dirname(__FILE__))) . '/traitements/config/connexion.php';

// Récupérer tous les administrateurs
$stmt = $pdo->query("SELECT * FROM administrateurs ORDER BY role DESC, nom ASC");
$admins = $stmt->fetchAll();

$csrf_token = generateCsrfToken();
?>

<?php require_once dirname(dirname(dirname(__FILE__))) . '/templates/sidebar-admin.php'; ?>

<div class="admin-main">
    <?php require_once dirname(dirname(dirname(__FILE__))) . '/templates/navbar-admin.php'; ?>
    <div class="admin-content">
        <?php require_once dirname(dirname(dirname(__FILE__))) . '/templates/alertes.php'; ?>

        <div class="flex-between" style="margin-bottom:var(--spacing-md);">
            <h2 style="font-family:var(--font-title);">
                <i class="fa-solid fa-user-shield" style="color:var(--secondary);"></i> Équipe d'Administration
                <span class="badge badge-primary" style="margin-left:.5rem;"><?php echo count($admins); ?></span>
            </h2>
        </div>

        <div class="detail-grid">
            <!-- Liste des administrateurs -->
            <div class="card">
                <div class="admin-card-header" style="border-bottom:1px solid var(--border-color); padding-bottom:var(--spacing-xs); margin-bottom:var(--spacing-sm);">
                    <h3 style="font-family:var(--font-title);">Membres de l'équipe</h3>
                </div>
                <div class="reservation-table-wrapper">
                    <table class="reservation-table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Nom</th>
                                <th>Email</th>
                                <th>Rôle</th>
                                <th>Créé le</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($admins as $adm): ?>
                            <tr>
                                <td>#<?php echo $adm['id']; ?></td>
                                <td><strong><?php echo escape($adm['nom']); ?></strong></td>
                                <td><?php echo escape($adm['email']); ?></td>
                                <td>
                                    <?php if ($adm['role'] === 'super_admin'): ?>
                                        <span class="badge badge-danger">Super Admin</span>
                                    <?php else: ?>
                                        <span class="badge badge-info">Agent</span>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo formatDate($adm['date_creation']); ?></td>
                                <td>
                                    <?php if ($adm['id'] !== $_SESSION['admin_id']): ?>
                                        <form action="<?php echo SITE_URL; ?>traitements/admin/traitement_gestion_utilisateurs_admin.php" method="POST" style="display:inline;"
                                              onsubmit="return confirm('Voulez-vous vraiment supprimer cet administrateur ?');">
                                            <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                                            <input type="hidden" name="admin_id" value="<?php echo $adm['id']; ?>">
                                            <input type="hidden" name="action" value="supprimer">
                                            <button type="submit" class="btn btn-danger btn-sm" title="Supprimer">
                                                <i class="fa-solid fa-trash"></i>
                                            </button>
                                        </form>
                                    <?php else: ?>
                                        <span class="text-muted" style="font-size:0.8rem; font-style:italic;">Vous-même</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Ajouter un administrateur -->
            <div class="card">
                <div class="admin-card-header" style="border-bottom:1px solid var(--border-color); padding-bottom:var(--spacing-xs); margin-bottom:var(--spacing-sm);">
                    <h3 style="font-family:var(--font-title);"><i class="fa-solid fa-user-plus" style="color:var(--secondary);"></i> Ajouter un membre</h3>
                </div>

                <form action="<?php echo SITE_URL; ?>traitements/admin/traitement_gestion_utilisateurs_admin.php" method="POST">
                    <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                    <input type="hidden" name="action" value="ajouter">

                    <div class="form-group">
                        <label for="nom" class="form-label">Nom complet <span style="color:var(--danger);">*</span></label>
                        <input type="text" name="nom" id="nom" class="form-control" placeholder="Ex: Jean Maken" required>
                    </div>

                    <div class="form-group">
                        <label for="email" class="form-label">Adresse E-mail <span style="color:var(--danger);">*</span></label>
                        <input type="email" name="email" id="email" class="form-control" placeholder="Ex: jean@airmaken.com" required>
                    </div>

                    <div class="form-group">
                        <label for="role" class="form-label">Rôle <span style="color:var(--danger);">*</span></label>
                        <select name="role" id="role" class="form-control" required>
                            <option value="agent">Agent d'administration</option>
                            <option value="super_admin">Super Administrateur</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="mot_de_passe" class="form-label">Mot de passe <span style="color:var(--danger);">*</span></label>
                        <input type="password" name="mot_de_passe" id="mot_de_passe" class="form-control" required>
                    </div>

                    <button type="submit" class="btn btn-secondary" style="width:100%; margin-top:var(--spacing-sm);">
                        <i class="fa-solid fa-floppy-disk"></i> Enregistrer le compte
                    </button>
                </form>
            </div>
        </div>
    </div>


<?php require_once dirname(dirname(dirname(__FILE__))) . '/templates/footer-admin.php'; ?>
