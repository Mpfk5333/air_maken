<?php
// traitement_suppression_service.php
require_once dirname(dirname(__FILE__)) . '/config/constantes.php';
require_once dirname(dirname(__FILE__)) . '/config/connexion.php';
require_once dirname(dirname(__FILE__)) . '/fonctions/fonctions_securite.php';
require_once dirname(dirname(__FILE__)) . '/fonctions/fonctions_utils.php';
require_once dirname(dirname(__FILE__)) . '/fonctions/fonctions_session.php';

requireAdmin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: " . SITE_URL . "pages/admin/gestion-services.php");
    exit;
}

if (!isset($_POST['csrf_token']) || !verifyCsrfToken($_POST['csrf_token'])) {
    setFlashMessage('danger', "Jeton CSRF invalide.");
    header("Location: " . SITE_URL . "pages/admin/gestion-services.php");
    exit;
}

$service_id = intval($_POST['service_id'] ?? 0);

if ($service_id <= 0) {
    setFlashMessage('danger', "Service introuvable.");
    header("Location: " . SITE_URL . "pages/admin/gestion-services.php");
    exit;
}

try {
    // Vérifier s'il y a des réservations liées (ne pas supprimer en cascade)
    $check = $pdo->prepare("SELECT COUNT(*) FROM reservations WHERE id_service = :id");
    $check->execute(['id' => $service_id]);
    if ($check->fetchColumn() > 0) {
        setFlashMessage('danger', "Impossible de supprimer ce service car des réservations y sont associées.");
        header("Location: " . SITE_URL . "pages/admin/gestion-services.php");
        exit;
    }

    $stmt = $pdo->prepare("DELETE FROM services WHERE id = :id");
    $stmt->execute(['id' => $service_id]);
    setFlashMessage('success', "Service supprimé avec succès.");
    header("Location: " . SITE_URL . "pages/admin/gestion-services.php");
    exit;
} catch (\PDOException $e) {
    setFlashMessage('danger', "Erreur technique lors de la suppression.");
    header("Location: " . SITE_URL . "pages/admin/gestion-services.php");
    exit;
}
?>
