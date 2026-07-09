<?php
// header-admin.php - En-tête du back-office (balise <head> + ouverture layout)
require_once dirname(dirname(__FILE__)) . '/traitements/config/constantes.php';
require_once dirname(dirname(__FILE__)) . '/traitements/fonctions/fonctions_session.php';
require_once dirname(dirname(__FILE__)) . '/traitements/fonctions/fonctions_securite.php';
require_once dirname(dirname(__FILE__)) . '/traitements/fonctions/fonctions_utils.php';

initSecureSession();

// Protection par défaut (chaque page admin peut appeler requireAdmin() avant cet include)
$admin_page_title = $admin_page_title ?? 'AIR MAKEN - Administration';
?>
<!DOCTYPE html>
<html lang="fr" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo escape($admin_page_title); ?></title>
    <link rel="icon" type="image/png" href="<?php echo SITE_URL; ?>images/logo/favicon.png">
    
    <!-- CSS Global -->
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>css/variables.css">
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>css/reset.css">
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>css/layout.css">
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>css/components.css">
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>css/admin.css">
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>css/responsive.css">
    
    <!-- CSS de page spécifique -->
    <?php if (isset($extra_css_admin) && is_array($extra_css_admin)): ?>
        <?php foreach ($extra_css_admin as $css_file): ?>
            <link rel="stylesheet" href="<?php echo SITE_URL; ?>css/<?php echo $css_file; ?>?v=<?php echo time(); ?>">
        <?php endforeach; ?>
    <?php endif; ?>
    
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
<div class="admin-layout">
