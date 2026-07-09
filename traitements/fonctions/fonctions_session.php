<?php
// fonctions_session.php - Contrôles de session et droits d'accès

if (count(get_included_files()) == 1) {
    exit("Accès direct non autorisé.");
}

/**
 * Initialise une session sécurisée
 */
function initSecureSession() {
    if (session_status() === PHP_SESSION_NONE) {
        // Session cookies sécurisés
        ini_set('session.cookie_httponly', 1);
        ini_set('session.use_only_cookies', 1);
        
        $secure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443);
        if ($secure) {
            ini_set('session.cookie_secure', 1);
        }
        
        session_start();
    }
}

/**
 * Vérifie si un utilisateur (client) est connecté
 * @return bool
 */
function isLoggedIn() {
    initSecureSession();
    return isset($_SESSION['user_id']) && isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'client';
}

/**
 * Vérifie si un administrateur ou agent est connecté
 * @return bool
 */
function isAdmin() {
    initSecureSession();
    return isset($_SESSION['admin_id']) && isset($_SESSION['admin_role']) && in_array($_SESSION['admin_role'], ['super_admin', 'agent']);
}

/**
 * Vérifie si le compte connecté est Super Admin
 * @return bool
 */
function isSuperAdmin() {
    initSecureSession();
    return isset($_SESSION['admin_id']) && isset($_SESSION['admin_role']) && $_SESSION['admin_role'] === 'super_admin';
}

/**
 * Vérifie si le compte connecté est Agent
 * @return bool
 */
function isAgent() {
    initSecureSession();
    return isset($_SESSION['admin_id']) && isset($_SESSION['admin_role']) && $_SESSION['admin_role'] === 'agent';
}

/**
 * Restreint l'accès aux clients connectés. Redirige sinon.
 */
function requireLogin() {
    if (!isLoggedIn()) {
        initSecureSession();
        // Sauvegarde de l'URL pour redirection future
        $_SESSION['redirect_url'] = $_SERVER['REQUEST_URI'];
        header("Location: " . SITE_URL . "pages/public/connexion.php");
        exit;
    }
}

/**
 * Restreint l'accès aux administrateurs connectés. Redirige sinon.
 */
function requireAdmin() {
    if (!isAdmin()) {
        header("Location: " . SITE_URL . "pages/admin/connexion-admin.php");
        exit;
    }
}
?>
