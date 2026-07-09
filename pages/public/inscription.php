<?php
// inscription.php - Formulaire d'inscription visiteur
$page_title = 'AIR MAKEN - Inscription';
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
            <h1 class="auth-title">Créer un compte</h1>
            <p class="auth-subtitle">Rejoignez-nous pour organiser vos réservations en ligne.</p>
        </div>

        <!-- Bloc d'affichage des alertes -->
        <?php require_once dirname(dirname(dirname(__FILE__))) . '/templates/alertes.php'; ?>

        <form action="<?php echo SITE_URL; ?>traitements/auth/traitement_inscription.php" method="POST" id="registerForm">
            <!-- Jeton de sécurité CSRF -->
            <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
            
            <div class="form-row">
                <div class="form-group">
                    <label for="nom" class="form-label">Nom <span style="color: var(--danger);">*</span></label>
                    <input type="text" name="nom" id="nom" class="form-control" placeholder="Ex: NDONG" required>
                </div>
                <div class="form-group">
                    <label for="prenom" class="form-label">Prénom <span style="color: var(--danger);">*</span></label>
                    <input type="text" name="prenom" id="prenom" class="form-control" placeholder="Ex: Juan" required>
                </div>
            </div>

            <div class="form-group">
                <label for="email" class="form-label">Adresse email <span style="color: var(--danger);">*</span></label>
                <input type="email" name="email" id="email" class="form-control" placeholder="nom@exemple.com" required>
            </div>

            <div class="form-group">
                <label for="telephone" class="form-label">Téléphone <span style="color: var(--danger);">*</span></label>
                <input type="tel" name="telephone" id="telephone" class="form-control" placeholder="Ex: +240 222 333 444" required>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="mot_de_passe" class="form-label">Mot de passe <span style="color: var(--danger);">*</span></label>
                    <input type="password" name="mot_de_passe" id="mot_de_passe" class="form-control" placeholder="Min. 8 caractères" required>
                </div>
                <div class="form-group">
                    <label for="confirm_mot_de_passe" class="form-label">Confirmation <span style="color: var(--danger);">*</span></label>
                    <input type="password" name="confirm_mot_de_passe" id="confirm_mot_de_passe" class="form-control" placeholder="Confirmer" required>
                </div>
            </div>

            <button type="submit" class="btn btn-primary btn-lg" style="width: 100%; margin-top: var(--spacing-sm);">S'inscrire</button>
        </form>

        <div class="auth-footer">
            Déjà inscrit ? <a href="<?php echo SITE_URL; ?>pages/public/connexion.php">Se connecter</a>
        </div>
    </div>
</main>
<?php
$extra_js = ['auth.js'];
require_once dirname(dirname(dirname(__FILE__))) . '/templates/footer.php';
?>
