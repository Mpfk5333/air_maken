<?php
// sidebar-admin.php - Menu latéral du back-office
require_once dirname(dirname(__FILE__)) . '/traitements/config/constantes.php';
require_once dirname(dirname(__FILE__)) . '/traitements/fonctions/fonctions_session.php';

$current_page = basename($_SERVER['SCRIPT_NAME']);
?>
<aside class="admin-sidebar">
    <div class="admin-sidebar-logo">
        <i class="fa-solid fa-plane-departure logo-icon" style="color: var(--secondary); transform: rotate(-15deg);"></i>
        <span class="logo-text">AIR<span class="logo-highlight">MAKEN</span></span>
    </div>
    
    <nav class="admin-menu">
        <a href="<?php echo SITE_URL; ?>pages/admin/tableau-de-bord.php" class="<?php echo ($current_page === 'tableau-de-bord.php') ? 'active' : ''; ?>">
            <i class="fa-solid fa-gauge-high"></i> Dashboard
        </a>
        <a href="<?php echo SITE_URL; ?>pages/admin/gestion-reservations.php" class="<?php echo ($current_page === 'gestion-reservations.php' || $current_page === 'detail-reservation.php') ? 'active' : ''; ?>">
            <i class="fa-solid fa-ticket"></i> Réservations
        </a>
        <a href="<?php echo SITE_URL; ?>pages/admin/gestion-services.php" class="<?php echo ($current_page === 'gestion-services.php' || $current_page === 'ajouter-service.php' || $current_page === 'modifier-service.php') ? 'active' : ''; ?>">
            <i class="fa-solid fa-cubes"></i> Services (Catalogue)
        </a>
        <a href="<?php echo SITE_URL; ?>pages/admin/gestion-clients.php" class="<?php echo ($current_page === 'gestion-clients.php' || $current_page === 'detail-client.php') ? 'active' : ''; ?>">
            <i class="fa-solid fa-users"></i> Clients (Comptes)
        </a>
        <a href="<?php echo SITE_URL; ?>pages/admin/gestion-contenu.php" class="<?php echo ($current_page === 'gestion-contenu.php') ? 'active' : ''; ?>">
            <i class="fa-solid fa-file-signature"></i> Gestion Contenu
        </a>
        <a href="<?php echo SITE_URL; ?>pages/admin/gestion-messages.php" class="<?php echo ($current_page === 'gestion-messages.php') ? 'active' : ''; ?>">
            <i class="fa-solid fa-envelope"></i> Messages Reçus
        </a>
        <a href="<?php echo SITE_URL; ?>pages/admin/statistiques.php" class="<?php echo ($current_page === 'statistiques.php') ? 'active' : ''; ?>">
            <i class="fa-solid fa-chart-pie"></i> Statistiques
        </a>
        
        <?php if (isSuperAdmin()): ?>
            <!-- Liens exclusifs Super Admin -->
            <a href="<?php echo SITE_URL; ?>pages/admin/gestion-utilisateurs-admin.php" class="<?php echo ($current_page === 'gestion-utilisateurs-admin.php') ? 'active' : ''; ?>">
                <i class="fa-solid fa-user-shield"></i> Rôles & Admins
            </a>
            <a href="<?php echo SITE_URL; ?>pages/admin/parametres.php" class="<?php echo ($current_page === 'parametres.php') ? 'active' : ''; ?>">
                <i class="fa-solid fa-sliders"></i> Paramètres
            </a>
        <?php endif; ?>
    </nav>
</aside>
