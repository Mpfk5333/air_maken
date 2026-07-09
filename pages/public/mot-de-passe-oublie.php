<?php
// mot-de-passe-oublie.php - Demande de réinitialisation de mot de passe
$page_title = 'AIR MAKEN - Réinitialisation de mot de passe';
$extra_css = ['auth.css'];
require_once dirname(dirname(dirname(__FILE__))) . '/templates/header.php';
require_once dirname(dirname(dirname(__FILE__))) . '/templates/navbar.php';

// Redirection si déjà connecté
if (isLoggedIn()) {
    header("Location: " . SITE_URL . "pages/public/mon-compte.php");
    exit;
}

$csrf_token = generateCsrfToken();
$token = $_GET['token'] ?? '';
$is_reset_mode = false;

if ($token !== '') {
    // Mode réinitialisation : on vérifie si le jeton existe en session et n'a pas expiré
    if (isset($_SESSION['reset_token']) && $_SESSION['reset_token'] === $token && isset($_SESSION['reset_token_expiry']) && $_SESSION['reset_token_expiry'] > time()) {
        $is_reset_mode = true;
    } else {
        setFlashMessage('danger', "Ce lien de réinitialisation n'est plus valide ou a expiré. Veuillez refaire une demande.");
    }
}
?>
<main class="auth-wrapper">
    <div class="auth-card">
        <div class="auth-header">
            <div class="auth-logo">
                <i class="fa-solid fa-plane-departure logo-icon"></i>
                <span class="logo-text">AIR<span class="logo-highlight">MAKEN</span></span>
            </div>
            <h1 class="auth-title">
                <?php echo $is_reset_mode ? 'Nouveau mot de passe' : 'Mot de passe oublié'; ?>
            </h1>
            <p class="auth-subtitle">
                <?php echo $is_reset_mode ? 'Saisissez votre nouveau mot de passe.' : 'Entrez votre adresse email pour réinitialiser votre compte.'; ?>
            </p>
        </div>

        <?php require_once dirname(dirname(dirname(__FILE__))) . '/templates/alertes.php'; ?>

        <?php if ($is_reset_mode): ?>
            <!-- Formulaire pour changer le mot de passe -->
            <form action="<?php echo SITE_URL; ?>traitements/auth/traitement_mdp_oublie.php" method="POST">
                <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                <input type="hidden" name="token" value="<?php echo escape($token); ?>">
                
                <div class="form-group">
                    <label for="nouveau_mdp" class="form-label">Nouveau mot de passe <span style="color: var(--danger);">*</span></label>
                    <input type="password" name="nouveau_mdp" id="nouveau_mdp" class="form-control" placeholder="Minimum 8 caractères" required>
                </div>

                <div class="form-group">
                    <label for="confirm_mdp" class="form-label">Confirmez le mot de passe <span style="color: var(--danger);">*</span></label>
                    <input type="password" name="confirm_mdp" id="confirm_mdp" class="form-control" placeholder="Confirmer le mot de passe" required>
                </div>

                <button type="submit" name="action_reset" class="btn btn-primary btn-lg" style="width: 100%; margin-top: var(--spacing-sm);">Mettre à jour</button>
            </form>
        <?php else: ?>
            <!-- Formulaire pour demander le lien -->
            <form action="<?php echo SITE_URL; ?>traitements/auth/traitement_mdp_oublie.php" method="POST">
                <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                
                <div class="form-group">
                    <label for="email" class="form-label">Adresse email <span style="color: var(--danger);">*</span></label>
                    <input type="email" name="email" id="email" class="form-control" placeholder="nom@exemple.com" required>
                </div>

                <button type="submit" name="action_request" class="btn btn-primary btn-lg" style="width: 100%; margin-top: var(--spacing-sm);">Demander la réinitialisation</button>
            </form>
        <?php endif; ?>

        <div class="auth-footer">
            Retour à la <a href="<?php echo SITE_URL; ?>pages/public/connexion.php">connexion</a>
        </div>
    </div>
</main>
<?php
require_once dirname(dirname(dirname(__FILE__))) . '/templates/footer.php';
?>
