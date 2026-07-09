<?php
// traitement_modification_statut.php - Changement de statut de réservation par l'admin
require_once dirname(dirname(__FILE__)) . '/config/constantes.php';
require_once dirname(dirname(__FILE__)) . '/config/connexion.php';
require_once dirname(dirname(__FILE__)) . '/fonctions/fonctions_securite.php';
require_once dirname(dirname(__FILE__)) . '/fonctions/fonctions_utils.php';
require_once dirname(dirname(__FILE__)) . '/fonctions/fonctions_session.php';

// Protection : Administrateur uniquement
requireAdmin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: " . SITE_URL . "pages/admin/gestion-reservations.php");
    exit;
}

// 1. Validation CSRF
if (!isset($_POST['csrf_token']) || !verifyCsrfToken($_POST['csrf_token'])) {
    setFlashMessage('danger', "Jeton de sécurité CSRF invalide.");
    header("Location: " . SITE_URL . "pages/admin/gestion-reservations.php");
    exit;
}

$reservation_id = intval($_POST['reservation_id'] ?? 0);
$action         = $_POST['action'] ?? '';
$motif_refus    = trim($_POST['motif_refus'] ?? '');

$redirect_detail = SITE_URL . "pages/admin/detail-reservation.php?id=" . $reservation_id;

if ($reservation_id <= 0 || !in_array($action, ['confirmer', 'refuser'])) {
    setFlashMessage('danger', "Requête invalide.");
    header("Location: " . SITE_URL . "pages/admin/gestion-reservations.php");
    exit;
}

try {
    // 2. Charger la réservation
    $stmt = $pdo->prepare("SELECT id, statut, id_utilisateur FROM reservations WHERE id = :id");
    $stmt->execute(['id' => $reservation_id]);
    $res = $stmt->fetch();

    if (!$res) {
        setFlashMessage('danger', "Réservation introuvable.");
        header("Location: " . SITE_URL . "pages/admin/gestion-reservations.php");
        exit;
    }

    // 3. Traitement selon l'action
    if ($action === 'confirmer') {
        // Marquer comme confirmée
        $upd = $pdo->prepare("UPDATE reservations SET statut = 'confirmee', motif_refus = NULL WHERE id = :id");
        $upd->execute(['id' => $reservation_id]);
        setFlashMessage('success', "La réservation #" . $reservation_id . " a été confirmée.");

    } elseif ($action === 'refuser') {
        // Motif obligatoire pour un refus
        if (empty($motif_refus)) {
            setFlashMessage('danger', "Le motif de refus est obligatoire.");
            header("Location: " . $redirect_detail);
            exit;
        }

        $upd = $pdo->prepare("UPDATE reservations SET statut = 'refusee', motif_refus = :motif WHERE id = :id");
        $upd->execute(['motif' => $motif_refus, 'id' => $reservation_id]);
        setFlashMessage('success', "La réservation #" . $reservation_id . " a été refusée.");
    }

    header("Location: " . $redirect_detail);
    exit;

} catch (\PDOException $e) {
    setFlashMessage('danger', "Erreur serveur lors du changement de statut.");
    header("Location: " . $redirect_detail);
    exit;
}
?>
