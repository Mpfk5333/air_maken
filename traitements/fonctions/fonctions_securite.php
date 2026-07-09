<?php
// fonctions_securite.php - Fonctions de hash, d'échappement et jetons CSRF

if (count(get_included_files()) == 1) {
    exit("Accès direct non autorisé.");
}

/**
 * Échappe les données pour l'affichage HTML (protection XSS)
 * @param mixed $data
 * @return mixed
 */
function escape($data) {
    if (is_array($data)) {
        return array_map('escape', $data);
    }
    return htmlspecialchars($data ?? '', ENT_QUOTES, 'UTF-8');
}

/**
 * Génère un jeton CSRF et le stocke en session
 * @return string
 */
function generateCsrfToken() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Vérifie un jeton CSRF
 * @param string|null $token
 * @return bool
 */
function verifyCsrfToken($token) {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    if (!empty($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token ?? '')) {
        return true;
    }
    return false;
}

/**
 * Hache un mot de passe avec bcrypt
 * @param string $password
 * @return string
 */
function hashPassword($password) {
    return password_hash($password, PASSWORD_BCRYPT);
}

/**
 * Vérifie un mot de passe par rapport à son hash
 * @param string $password
 * @param string $hash
 * @return bool
 */
function verifyPassword($password, $hash) {
    return password_verify($password, $hash);
}
?>
