<?php
// traitement_suppression_client.php - Suppression définitive d'un client (Super Admin only)
require_once dirname(dirname(__FILE__)) . '/config/constantes.php';
require_once dirname(dirname(__FILE__)) . '/config/connexion.php';
require_once dirname(dirname(__FILE__)) . '/fonctions/fonctions_securite.php';
require_once dirname(dirname(__FILE__)) . '/fonctions/fonctions_utils.php';
require_once dirname(dirname(__FILE__)) . '/fonctions/fonctions_session.php';

requireAdmin();

// Super Admin uniquement
if (!isSuperAdmin()) {
    setFlashMessage('danger', "Action réservée aux Super Administrateurs.");
    header("Location: " . SITE_URL . "pages/admin/gestion-clients.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: " . SITE_URL . "pages/admin/gestion-clients.php");
    exit;
}

if (!isset($_POST['csrf_token']) || !verifyCsrfToken($_POST['csrf_token'])) {
    setFlashMessage('danger', "Jeton CSRF invalide.");
    header("Location: " . SITE_URL . "pages/admin/gestion-clients.php");
    exit;
}

$client_id = intval($_POST['client_id'] ?? 0);

if ($client_id <= 0) {
    setFlashMessage('danger', "Client introuvable.");
    header("Location: " . SITE_URL . "pages/admin/gestion-clients.php");
    exit;
}

try {
    // Supprimer d'abord les réservations liées
    $del_res = $pdo->prepare("DELETE FROM reservations WHERE id_utilisateur = :id");
    $del_res->execute(['id' => $client_id]);

    // Supprimer le compte client
    $del_usr = $pdo->prepare("DELETE FROM utilisateurs WHERE id = :id");
    $del_usr->execute(['id' => $client_id]);

    setFlashMessage('success', "Le compte client et toutes ses données ont été supprimés définitivement.");
    header("Location: " . SITE_URL . "pages/admin/gestion-clients.php");
    exit;
} catch (\PDOException $e) {
    setFlashMessage('danger', "Erreur technique lors de la suppression du compte.");
    header("Location: " . SITE_URL . "pages/admin/gestion-clients.php");
    exit;
}
?>
