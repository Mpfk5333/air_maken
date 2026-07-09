<?php
// deconnexion.php - Script de déconnexion sécurisée client
require_once dirname(dirname(dirname(__FILE__))) . '/traitements/config/constantes.php';
header("Location: " . SITE_URL . "traitements/auth/traitement_deconnexion.php");
exit;
?>
