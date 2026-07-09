<?php
// navbar.php - Barre de navigation publique
require_once dirname(dirname(__FILE__)) . '/traitements/config/constantes.php';
require_once dirname(dirname(__FILE__)) . '/traitements/fonctions/fonctions_session.php';
?>
<header class="main-header">
    <div class="container navbar-container">
        <!-- Logo -->
        <a href="<?php echo SITE_URL; ?>index.php" class="logo">
            <i class="fa-solid fa-plane-departure logo-icon"></i>
            <span class="logo-text">AIR<span class="logo-highlight">MAKEN</span></span>
        </a>

        <!-- Liens de Navigation -->
        <nav class="nav-menu" id="navMenu">
            <ul class="nav-list">
                <li><a href="<?php echo SITE_URL; ?>pages/public/accueil.php" class="nav-link">Accueil</a></li>
                <li><a href="<?php echo SITE_URL; ?>pages/public/services.php" class="nav-link">Services</a></li>
                <li><a href="<?php echo SITE_URL; ?>pages/public/reservations.php" class="nav-link">Réservations</a></li>
                <li><a href="<?php echo SITE_URL; ?>pages/public/apropos.php" class="nav-link">À propos</a></li>
                <li><a href="<?php echo SITE_URL; ?>pages/public/contact.php" class="nav-link">Contact</a></li>
            </ul>
        </nav>

        <!-- Actions Auth / Admin -->
        <div class="nav-actions">
            <?php if (isLoggedIn()): ?>
                <div class="user-logged flex-align-center">
                    <span class="user-welcome"><i class="fa-regular fa-user"></i> Hello, <strong><?php echo escape($_SESSION['user_prenom']); ?></strong></span>
                    <a href="<?php echo SITE_URL; ?>pages/public/mon-compte.php" class="btn btn-outline btn-sm">Mon Compte</a>
                    <a href="<?php echo SITE_URL; ?>pages/public/deconnexion.php" class="btn btn-danger btn-sm" title="Déconnexion"><i class="fa-solid fa-arrow-right-from-bracket"></i></a>
                </div>
            <?php else: ?>
                <a href="<?php echo SITE_URL; ?>pages/public/connexion.php" class="btn btn-outline btn-sm">Connexion</a>
                <a href="<?php echo SITE_URL; ?>pages/public/inscription.php" class="btn btn-primary btn-sm">S'inscrire</a>
            <?php endif; ?>
            
            <?php if (isAdmin()): ?>
                <a href="<?php echo SITE_URL; ?>pages/admin/tableau-de-bord.php" class="btn btn-secondary btn-sm admin-btn" title="Back-Office"><i class="fa-solid fa-lock-open"></i> Admin</a>
            <?php endif; ?>

            <!-- Bouton Thème -->
            <button class="btn btn-outline btn-sm" id="themeToggle" aria-label="Basculer le thème" title="Changer le thème" style="width:40px; height:40px; padding:0;">
                <i class="fa-solid fa-moon"></i>
            </button>

            <!-- Bouton Burger (mobile) -->
            <button class="burger-toggle" id="burgerToggle" aria-label="Toggle Menu" aria-expanded="false">
                <i class="fa-solid fa-bars"></i>
            </button>
        </div>
    </div>
</header>

<!-- Bouton Retour en Haut -->
<button id="scrollTopBtn" aria-label="Retour en haut">
    <i class="fa-solid fa-arrow-up"></i>
</button>
