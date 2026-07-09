<?php
// traitement_blocage_client.php - Bloquer/Débloquer un compte client
require_once dirname(dirname(__FILE__)) . '/config/constantes.php';
require_once dirname(dirname(__FILE__)) . '/config/connexion.php';
require_once dirname(dirname(__FILE__)) . '/fonctions/fonctions_securite.php';
require_once dirname(dirname(__FILE__)) . '/fonctions/fonctions_utils.php';
require_once dirname(dirname(__FILE__)) . '/fonctions/fonctions_session.php';

requireAdmin();

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
$action    = $_POST['action'] ?? '';

if ($client_id <= 0 || !in_array($action, ['bloquer', 'debloquer'])) {
    setFlashMessage('danger', "Requête invalide.");
    header("Location: " . SITE_URL . "pages/admin/gestion-clients.php");
    exit;
}

$nouveau_statut = ($action === 'bloquer') ? 'bloque' : 'actif';

try {
    $stmt = $pdo->prepare("UPDATE utilisateurs SET statut = :statut WHERE id = :id");
    $stmt->execute(['statut' => $nouveau_statut, 'id' => $client_id]);

    $msg = ($action === 'bloquer') ? "Compte client bloqué." : "Compte client réactivé.";
    setFlashMessage('success', $msg);
    header("Location: " . SITE_URL . "pages/admin/detail-client.php?id=" . $client_id);
    exit;
} catch (\PDOException $e) {
    setFlashMessage('danger', "Erreur technique lors du changement de statut.");
    header("Location: " . SITE_URL . "pages/admin/gestion-clients.php");
    exit;
}
?>
