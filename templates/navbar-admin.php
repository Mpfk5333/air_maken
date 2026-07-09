<?php
// navbar-admin.php - Barre supérieure du back-office
require_once dirname(dirname(__FILE__)) . '/traitements/config/constantes.php';
require_once dirname(dirname(__FILE__)) . '/traitements/fonctions/fonctions_session.php';
?>
<nav class="admin-navbar">
    <div class="flex-align-center" style="gap:var(--spacing-sm);">
        <!-- Bouton burger admin (mobile) -->
        <button id="adminSidebarToggle" class="btn btn-outline btn-sm" style="width:40px; height:40px; padding:0; display:none;" aria-label="Toggle Sidebar">
            <i class="fa-solid fa-bars"></i>
        </button>
        <span style="font-family:var(--font-title); font-weight:700; font-size:1.05rem; color:var(--text-main);">
            Espace Administration
        </span>
    </div>
    <div class="admin-user-info">
        <i class="fa-solid fa-user-shield" style="color:var(--secondary);"></i>
        <span><strong><?php echo escape($_SESSION['admin_nom'] ?? 'Administrateur'); ?></strong></span>
        <span class="badge <?php echo (($_SESSION['admin_role'] ?? '') === 'super_admin') ? 'badge-danger' : 'badge-info'; ?>">
            <?php echo (($_SESSION['admin_role'] ?? '') === 'super_admin') ? 'Super Admin' : 'Agent'; ?>
        </span>
        <a href="<?php echo SITE_URL; ?>pages/public/accueil.php" class="btn btn-outline btn-sm" title="Voir le site" style="margin-left:var(--spacing-sm);">
            <i class="fa-solid fa-arrow-up-right-from-square"></i> Site
        </a>
        <a href="<?php echo SITE_URL; ?>pages/public/deconnexion.php" class="btn btn-danger btn-sm" title="Déconnexion">
            <i class="fa-solid fa-arrow-right-from-bracket"></i> Déconnexion
        </a>
    </div>
</nav>
<script>
// Afficher le bouton burger admin sur mobile
(function() {
    var btn = document.getElementById('adminSidebarToggle');
    if (btn && window.innerWidth <= 992) { btn.style.display = 'flex'; }
    window.addEventListener('resize', function() {
        if (btn) btn.style.display = window.innerWidth <= 992 ? 'flex' : 'none';
    });
})();
</script>
