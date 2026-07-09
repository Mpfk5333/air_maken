<?php
// traitement_action_message.php - Actions de messagerie admin
require_once dirname(dirname(__FILE__)) . '/config/constantes.php';
require_once dirname(dirname(__FILE__)) . '/config/connexion.php';
require_once dirname(dirname(__FILE__)) . '/fonctions/fonctions_securite.php';
require_once dirname(dirname(__FILE__)) . '/fonctions/fonctions_utils.php';
require_once dirname(dirname(__FILE__)) . '/fonctions/fonctions_session.php';

requireAdmin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: " . SITE_URL . "pages/admin/gestion-messages.php");
    exit;
}

// Vérifier CSRF
if (!isset($_POST['csrf_token']) || !verifyCsrfToken($_POST['csrf_token'])) {
    setFlashMessage('danger', "Jeton CSRF invalide.");
    header("Location: " . SITE_URL . "pages/admin/gestion-messages.php");
    exit;
}

$msg_id = intval($_POST['message_id'] ?? 0);
$action = $_POST['action'] ?? '';

if ($msg_id <= 0 || !in_array($action, ['lu', 'traite', 'repondre'])) {
    setFlashMessage('danger', "Paramètres de requête incorrects.");
    header("Location: " . SITE_URL . "pages/admin/gestion-messages.php");
    exit;
}

try {
    if ($action === 'lu') {
        $stmt = $pdo->prepare("UPDATE messages_contact SET statut = 'lu' WHERE id = :id");
        $stmt->execute(['id' => $msg_id]);
        setFlashMessage('success', "Message marqué comme lu.");
    } elseif ($action === 'traite') {
        $stmt = $pdo->prepare("UPDATE messages_contact SET statut = 'traite' WHERE id = :id");
        $stmt->execute(['id' => $msg_id]);
        setFlashMessage('success', "Message marqué comme résolu (traité).");
    } elseif ($action === 'repondre') {
        $reponse = trim($_POST['reponse'] ?? '');
        if (empty($reponse)) {
            setFlashMessage('danger', "La réponse ne peut pas être vide.");
            header("Location: " . SITE_URL . "pages/admin/gestion-messages.php");
            exit;
        }

        // Mettre à jour en traité
        $stmt = $pdo->prepare("UPDATE messages_contact SET statut = 'traite' WHERE id = :id");
        $stmt->execute(['id' => $msg_id]);

        // Simuler l'envoi d'email
        setFlashMessage('success', "Réponse envoyée avec succès par e-mail (simulation). Statut mis à jour à Traité.");
    }

    header("Location: " . SITE_URL . "pages/admin/gestion-messages.php");
    exit;
} catch (\PDOException $e) {
    setFlashMessage('danger', "Erreur technique lors du traitement de l'action.");
    header("Location: " . SITE_URL . "pages/admin/gestion-messages.php");
    exit;
}
?>
