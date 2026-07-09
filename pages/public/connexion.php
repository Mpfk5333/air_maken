<?php
// connexion.php - Formulaire de connexion client
$page_title = 'AIR MAKEN - Connexion';
$extra_css = ['auth.css'];
require_once dirname(dirname(dirname(__FILE__))) . '/templates/header.php';
require_once dirname(dirname(dirname(__FILE__))) . '/templates/navbar.php';

// Redirection si déjà connecté
if (isLoggedIn()) {
    header("Location: " . SITE_URL . "pages/public/mon-compte.php");
    exit;
}

$csrf_token = generateCsrfToken();
?>
<main class="auth-wrapper">
    <div class="auth-card">
        <div class="auth-header">
            <div class="auth-logo">
                <i class="fa-solid fa-plane-departure logo-icon"></i>
                <span class="logo-text">AIR<span class="logo-highlight">MAKEN</span></span>
            </div>
            <h1 class="auth-title">Connexion</h1>
            <p class="auth-subtitle">Accédez à votre espace pour gérer vos réservations.</p>
        </div>

        <?php require_once dirname(dirname(dirname(__FILE__))) . '/templates/alertes.php'; ?>

        <form action="<?php echo SITE_URL; ?>traitements/auth/traitement_connexion.php" method="POST" id="loginForm">
            <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
            
            <div class="form-group">
                <label for="email" class="form-label">Adresse email <span style="color: var(--danger);">*</span></label>
                <input type="email" name="email" id="email" class="form-control" placeholder="nom@exemple.com" required>
            </div>

            <div class="form-group">
                <label for="mot_de_passe" class="form-label">Mot de passe <span style="color: var(--danger);">*</span></label>
                <input type="password" name="mot_de_passe" id="mot_de_passe" class="form-control" placeholder="Votre mot de passe" required>
            </div>

            <div class="auth-forgot">
                <a href="<?php echo SITE_URL; ?>pages/public/mot-de-passe-oublie.php">Mot de passe oublié ?</a>
            </div>

            <button type="submit" class="btn btn-primary btn-lg" style="width: 100%;">Se connecter</button>
        </form>

        <div class="auth-footer">
            Pas encore de compte ? <a href="<?php echo SITE_URL; ?>pages/public/inscription.php">Créer un compte</a>
        </div>
    </div>
</main>
<?php
$extra_js = ['auth.js'];
require_once dirname(dirname(dirname(__FILE__))) . '/templates/footer.php';
?>
