<?php
// header.php - Balise <head>, liens CSS/JS communs
require_once dirname(dirname(__FILE__)) . '/traitements/config/constantes.php';
require_once dirname(dirname(__FILE__)) . '/traitements/fonctions/fonctions_session.php';
require_once dirname(dirname(__FILE__)) . '/traitements/fonctions/fonctions_securite.php';
require_once dirname(dirname(__FILE__)) . '/traitements/fonctions/fonctions_utils.php';

initSecureSession();
$page_title = $page_title ?? 'AIR MAKEN - Votre destination commence avec nous';
?>
<!DOCTYPE html>
<html lang="fr" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo escape($page_title); ?></title>
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="<?php echo SITE_URL; ?>images/logo/favicon.png">
    
    <!-- CSS Common Files -->
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>css/variables.css">
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>css/reset.css">
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>css/layout.css">
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>css/navbar.css">
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>css/footer.css">
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>css/components.css">
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>css/responsive.css">
    
    <!-- Page Specific CSS -->
    <?php if (isset($extra_css) && is_array($extra_css)): ?>
        <?php foreach ($extra_css as $css_file): ?>
            <link rel="stylesheet" href="<?php echo SITE_URL; ?>css/<?php echo $css_file; ?>?v=<?php echo time(); ?>">
        <?php endforeach; ?>
    <?php endif; ?>
    
    <!-- FontAwesome Icon Library -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
