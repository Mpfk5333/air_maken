<?php
// traitement_deconnexion.php - Logique de déconnexion client
require_once dirname(dirname(__FILE__)) . '/config/constantes.php';
require_once dirname(dirname(__FILE__)) . '/fonctions/fonctions_session.php';
require_once dirname(dirname(__FILE__)) . '/fonctions/fonctions_utils.php';

initSecureSession();

// 1. Vider le tableau de session
$_SESSION = array();

// 2. Supprimer le cookie de session sur le navigateur
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// 3. Détruire la session
session_destroy();

// 4. Initialiser une nouvelle session temporaire pour le message de confirmation
session_start();
setFlashMessage('success', "Vous avez été déconnecté de votre espace.");
header("Location: " . SITE_URL . "pages/public/connexion.php");
exit;
?>
