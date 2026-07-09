<?php
// fonctions_utils.php - Fonctions utilitaires diverses

if (count(get_included_files()) == 1) {
    exit("Accès direct non autorisé.");
}

/**
 * Formate une date en format français (JJ/MM/AAAA)
 * @param string $date
 * @return string
 */
function formatDate($date) {
    if (empty($date)) return '';
    return date('d/m/Y', strtotime($date));
}

/**
 * Formate une date et une heure
 * @param string $datetime
 * @return string
 */
function formatDateTime($datetime) {
    if (empty($datetime)) return '';
    return date('d/m/Y H:i', strtotime($datetime));
}

/**
 * Formate un prix en FCFA (XAF)
 * @param float|int|null $price
 * @return string
 */
function formatPrice($price) {
    if ($price === null) return 'Sur devis';
    return number_format($price, 0, ',', ' ') . ' FCFA';
}

/**
 * Enregistre un message flash en session
 * @param string $type (success, error, warning, info)
 * @param string $message
 */
function setFlashMessage($type, $message) {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    $_SESSION['flash'][$type] = $message;
}

/**
 * Récupère et efface un message flash de la session
 * @param string $type
 * @return string|null
 */
function getFlashMessage($type) {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    if (isset($_SESSION['flash'][$type])) {
        $message = $_SESSION['flash'][$type];
        unset($_SESSION['flash'][$type]);
        return $message;
    }
    return null;
}

/**
 * Vérifie si un message flash existe
 * @param string $type
 * @return bool
 */
function hasFlashMessage($type) {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    return isset($_SESSION['flash'][$type]);
}
?>
