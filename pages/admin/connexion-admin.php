<?php
// connexion-admin.php - Formulaire de connexion administrateur / agent
require_once dirname(dirname(dirname(__FILE__))) . '/traitements/config/constantes.php';
require_once dirname(dirname(dirname(__FILE__))) . '/traitements/fonctions/fonctions_session.php';
require_once dirname(dirname(dirname(__FILE__))) . '/traitements/fonctions/fonctions_securite.php';
require_once dirname(dirname(dirname(__FILE__))) . '/traitements/fonctions/fonctions_utils.php';

initSecureSession();

// Déjà connecté en admin → redirection vers dashboard
if (isAdmin()) {
    header("Location: " . SITE_URL . "pages/admin/tableau-de-bord.php");
    exit;
}

$csrf_token = generateCsrfToken();
$admin_page_title = 'AIR MAKEN - Connexion Administration';
?>
<!DOCTYPE html>
<html lang="fr" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo escape($admin_page_title); ?></title>
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>css/variables.css">
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>css/reset.css">
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>css/layout.css">
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>css/components.css">
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>css/auth.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
<main class="auth-wrapper" style="background: linear-gradient(135deg, hsl(215,85%,10%) 0%, hsl(215,85%,20%) 100%); min-height: 100vh;">
    <div class="auth-card">
        <div class="auth-header">
            <div class="auth-logo">
                <i class="fa-solid fa-plane-departure" style="color: var(--secondary); font-size: 1.6rem;"></i>
                <span>AIR<span class="logo-highlight">MAKEN</span></span>
            </div>
            <h1 class="auth-title"><i class="fa-solid fa-user-shield" style="color: var(--secondary);"></i> Espace Administration</h1>
            <p class="auth-subtitle">Accès restreint — Administrateurs et Agents uniquement.</p>
        </div>

        <?php
        // Affichage des messages flash
        require_once dirname(dirname(dirname(__FILE__))) . '/templates/alertes.php';
        ?>

        <form action="<?php echo SITE_URL; ?>traitements/auth/traitement_connexion.php" method="POST" id="adminLoginForm">
            <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
            <input type="hidden" name="login_type" value="admin">

            <div class="form-group">
                <label for="email" class="form-label">Adresse email <span style="color:var(--danger);">*</span></label>
                <input type="email" name="email" id="email" class="form-control" placeholder="admin@airmaken.com" required>
            </div>

            <div class="form-group">
                <label for="mot_de_passe" class="form-label">Mot de passe <span style="color:var(--danger);">*</span></label>
                <input type="password" name="mot_de_passe" id="mot_de_passe" class="form-control" placeholder="Votre mot de passe" required>
            </div>

            <button type="submit" class="btn btn-secondary btn-lg" style="width:100%; margin-top:var(--spacing-sm);">
                <i class="fa-solid fa-lock-open"></i> Se connecter
            </button>
        </form>

        <div class="auth-footer">
            <a href="<?php echo SITE_URL; ?>pages/public/accueil.php"><i class="fa-solid fa-arrow-left"></i> Retour au site public</a>
        </div>
    </div>
</main>
</body>
</html>
