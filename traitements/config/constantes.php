<?php
// constantes.php - Constantes globales du projet

if (count(get_included_files()) == 1) {
    exit("Accès direct non autorisé.");
}

// Détection dynamique de l'URL de base du site
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || ($_SERVER['SERVER_PORT'] ?? 80) == 443) ? "https://" : "http://";
$domainName = $_SERVER['HTTP_HOST'] ?? 'localhost';

// Détermination du chemin relatif du projet par rapport au Document Root
$scriptFilename = str_replace('\\', '/', $_SERVER['SCRIPT_FILENAME'] ?? '');
$rootPath = str_replace('\\', '/', dirname(dirname(dirname(__FILE__))));
$docRoot = str_replace('\\', '/', $_SERVER['DOCUMENT_ROOT'] ?? '');

$baseUri = '';
if (!empty($docRoot) && strpos($rootPath, $docRoot) === 0) {
    $baseUri = substr($rootPath, strlen($docRoot));
}
$baseUri = rtrim(str_replace('\\', '/', $baseUri), '/');

define('SITE_URL', $protocol . $domainName . $baseUri . '/');

// Chemins physiques
define('ROOT_PATH', dirname(dirname(dirname(__FILE__))) . '/');
define('UPLOAD_DIR', ROOT_PATH . 'assets/uploads/');

// Rôles utilisateurs
define('ROLE_SUPER_ADMIN', 'super_admin');
define('ROLE_AGENT', 'agent');

// Statuts des réservations
define('STATUT_EN_ATTENTE', 'en_attente');
define('STATUT_CONFIRMEE', 'confirmee');
define('STATUT_REFUSEE', 'refusee');
define('STATUT_ANNULEE', 'annulee');

// Catégories de services
define('CAT_BILLETTERIE', 'billetterie');
define('CAT_HOTEL', 'hotel');
define('CAT_VEHICULE', 'vehicule');
define('CAT_VISA', 'visa');
define('CAT_ASSURANCE', 'assurance');
define('CAT_VOYAGE', 'voyage');
define('CAT_EVENEMENT', 'evenement');

// Statuts messages de contact
define('MSG_NON_LU', 'non_lu');
define('MSG_LU', 'lu');
define('MSG_TRAITE', 'traite');
?>
