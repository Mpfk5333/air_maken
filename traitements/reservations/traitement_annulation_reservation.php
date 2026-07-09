<?php
// traitement_annulation_reservation.php - Annulation par le client
require_once dirname(dirname(__FILE__)) . '/config/constantes.php';
require_once dirname(dirname(__FILE__)) . '/config/connexion.php';
require_once dirname(dirname(__FILE__)) . '/fonctions/fonctions_securite.php';
require_once dirname(dirname(__FILE__)) . '/fonctions/fonctions_utils.php';
require_once dirname(dirname(__FILE__)) . '/fonctions/fonctions_session.php';

// Protection : Client connecté obligatoire
requireLogin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: " . SITE_URL . "pages/public/mes-reservations.php");
    exit;
}

// 1. Validation CSRF
if (!isset($_POST['csrf_token']) || !verifyCsrfToken($_POST['csrf_token'])) {
    setFlashMessage('danger', "Jeton de sécurité CSRF manquant ou invalide.");
    header("Location: " . SITE_URL . "pages/public/mes-reservations.php");
    exit;
}

$reservation_id = intval($_POST['reservation_id'] ?? 0);

if ($reservation_id <= 0) {
    setFlashMessage('danger', "Demande de réservation incorrecte.");
    header("Location: " . SITE_URL . "pages/public/mes-reservations.php");
    exit;
}

try {
    // 2. Charger la réservation pour contrôle
    $stmt = $pdo->prepare("SELECT id, id_utilisateur, statut FROM reservations WHERE id = :id");
    $stmt->execute(['id' => $reservation_id]);
    $res = $stmt->fetch();

    if ($res) {
        // 3. Vérifier la propriété de la réservation
        if ($res['id_utilisateur'] != $_SESSION['user_id']) {
            setFlashMessage('danger', "Action interdite : cette réservation ne vous appartient pas.");
            header("Location: " . SITE_URL . "pages/public/mes-reservations.php");
            exit;
        }

        // 4. Vérifier que la réservation est bien annulable (statut 'en_attente')
        if ($res['statut'] !== 'en_attente') {
            setFlashMessage('danger', "Cette réservation ne peut plus être annulée car elle a déjà été traitée ou annulée.");
            header("Location: " . SITE_URL . "pages/public/mes-reservations.php");
            exit;
        }

        // 5. Mettre à jour le statut en 'annulee'
        $upd_stmt = $pdo->prepare("UPDATE reservations SET statut = 'annulee' WHERE id = :id");
        $upd_stmt->execute(['id' => $reservation_id]);

        setFlashMessage('success', "Votre réservation a été annulée avec succès.");
    } else {
        setFlashMessage('danger', "Réservation introuvable.");
    }

    header("Location: " . SITE_URL . "pages/public/mes-reservations.php");
    exit;

} catch (\PDOException $e) {
    setFlashMessage('danger', "Une erreur serveur est survenue lors de l'annulation.");
    header("Location: " . SITE_URL . "pages/public/mes-reservations.php");
    exit;
}
?>
