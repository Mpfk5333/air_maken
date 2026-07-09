<?php
// mon-compte.php - Espace client : profil personnel
$page_title = 'AIR MAKEN - Mon Profil';
require_once dirname(dirname(dirname(__FILE__))) . '/templates/header.php';
require_once dirname(dirname(dirname(__FILE__))) . '/templates/navbar.php';

// Protection de la page
requireLogin();

// Récupération des informations fraîches de la base de données
require_once dirname(dirname(dirname(__FILE__))) . '/traitements/config/connexion.php';
$stmt = $pdo->prepare("SELECT * FROM utilisateurs WHERE id = :id");
$stmt->execute(['id' => $_SESSION['user_id']]);
$user = $stmt->fetch();

$csrf_token = generateCsrfToken();
?>
<div class="container py-lg">
    <div class="account-layout">
        <!-- Sidebar Espace Client -->
        <aside class="account-sidebar">
            <div class="user-avatar-section text-center">
                <div class="user-avatar">
                    <i class="fa-solid fa-user-ninja"></i>
                </div>
                <h3><?php echo escape($user['prenom'] . ' ' . $user['nom']); ?></h3>
                <p><?php echo escape($user['email']); ?></p>
                <span class="badge badge-success">Compte Actif</span>
            </div>
            <ul class="account-menu">
                <li><a href="<?php echo SITE_URL; ?>pages/public/mon-compte.php" class="active"><i class="fa-solid fa-address-card"></i> Mon Profil</a></li>
                <li><a href="<?php echo SITE_URL; ?>pages/public/mes-reservations.php"><i class="fa-solid fa-plane-arrival"></i> Mes Réservations</a></li>
                <li><a href="<?php echo SITE_URL; ?>pages/public/deconnexion.php" style="color: var(--danger);"><i class="fa-solid fa-arrow-right-from-bracket"></i> Déconnexion</a></li>
            </ul>
        </aside>

        <!-- Contenu Principal -->
        <main class="account-content">
            <div class="card">
                <h2 style="font-family: var(--font-title); margin-bottom: 0.25rem;">Informations Personnelles</h2>
                <p class="text-muted mb-md">Consultez et modifiez les détails de votre profil client.</p>

                <?php require_once dirname(dirname(dirname(__FILE__))) . '/templates/alertes.php'; ?>

                <!-- Formulaire Profil -->
                <form action="<?php echo SITE_URL; ?>traitements/clients/traitement_modification_profil.php" method="POST" class="mb-lg">
                    <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                    <input type="hidden" name="action" value="update_profile">
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Nom</label>
                            <input type="text" class="form-control" value="<?php echo escape($user['nom']); ?>" style="opacity: 0.7; cursor: not-allowed;" disabled>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Prénom</label>
                            <input type="text" class="form-control" value="<?php echo escape($user['prenom']); ?>" style="opacity: 0.7; cursor: not-allowed;" disabled>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Adresse email (Non modifiable)</label>
                        <input type="email" class="form-control" value="<?php echo escape($user['email']); ?>" style="opacity: 0.7; cursor: not-allowed;" disabled>
                    </div>

                    <div class="form-group">
                        <label for="telephone" class="form-label">Numéro de téléphone <span style="color: var(--danger);">*</span></label>
                        <input type="tel" name="telephone" id="telephone" class="form-control" value="<?php echo escape($user['telephone']); ?>" required>
                    </div>

                    <button type="submit" class="btn btn-primary" style="margin-top: 0.5rem;">Enregistrer les modifications</button>
                </form>

                <hr style="border: 0; border-top: 1px solid var(--border-color); margin: var(--spacing-lg) 0;">

                <!-- Formulaire Mot de Passe -->
                <form action="<?php echo SITE_URL; ?>traitements/clients/traitement_modification_profil.php" method="POST">
                    <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                    <input type="hidden" name="action" value="update_password">

                    <h3 style="font-family: var(--font-title); margin-bottom: 0.25rem;">Sécurité du compte</h3>
                    <p class="text-muted mb-md">Modifiez votre mot de passe pour maintenir la sécurité de votre compte.</p>

                    <div class="form-group">
                        <label for="mot_de_passe_actuel" class="form-label">Mot de passe actuel <span style="color: var(--danger);">*</span></label>
                        <input type="password" name="mot_de_passe_actuel" id="mot_de_passe_actuel" class="form-control" placeholder="Entrez votre mot de passe actuel" required>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="nouveau_mot_de_passe" class="form-label">Nouveau mot de passe <span style="color: var(--danger);">*</span></label>
                            <input type="password" name="nouveau_mot_de_passe" id="nouveau_mot_de_passe" class="form-control" placeholder="Min. 8 caractères" required>
                        </div>
                        <div class="form-group">
                            <label for="confirm_mot_de_passe" class="form-label">Confirmation du nouveau mot de passe <span style="color: var(--danger);">*</span></label>
                            <input type="password" name="confirm_mot_de_passe" id="confirm_mot_de_passe" class="form-control" placeholder="Confirmer" required>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-secondary" style="margin-top: 0.5rem;">Mettre à jour le mot de passe</button>
                </form>
            </div>
        </main>
    </div>
</div>
<?php
require_once dirname(dirname(dirname(__FILE__))) . '/templates/footer.php';
?>
