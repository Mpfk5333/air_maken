<?php
// traitement_envoi_message.php - Réception et enregistrement message contact
require_once dirname(dirname(__FILE__)) . '/config/constantes.php';
require_once dirname(dirname(__FILE__)) . '/config/connexion.php';
require_once dirname(dirname(__FILE__)) . '/fonctions/fonctions_securite.php';
require_once dirname(dirname(__FILE__)) . '/fonctions/fonctions_utils.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: " . SITE_URL . "pages/public/contact.php");
    exit;
}

// Vérifier CSRF
if (!isset($_POST['csrf_token']) || !verifyCsrfToken($_POST['csrf_token'])) {
    setFlashMessage('danger', "Jeton CSRF invalide ou expiré.");
    header("Location: " . SITE_URL . "pages/public/contact.php");
    exit;
}

// Extraire et nettoyer
$nom       = trim($_POST['nom'] ?? '');
$email      = trim($_POST['email'] ?? '');
$telephone  = trim($_POST['telephone'] ?? '');
$sujet      = trim($_POST['sujet'] ?? '');
$message    = trim($_POST['message'] ?? '');

// Validation
if (empty($nom) || empty($email) || empty($sujet) || empty($message)) {
    setFlashMessage('danger', "Veuillez remplir tous les champs obligatoires (*).");
    header("Location: " . SITE_URL . "pages/public/contact.php");
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    setFlashMessage('danger', "L'adresse email saisie est incorrecte.");
    header("Location: " . SITE_URL . "pages/public/contact.php");
    exit;
}

// Insertion en base de données
try {
    $stmt = $pdo->prepare("INSERT INTO messages_contact (nom, email, telephone, sujet, message) VALUES (:nom, :email, :tel, :sujet, :msg)");
    $stmt->execute([
        'nom'   => $nom,
        'email'  => $email,
        'tel'    => !empty($telephone) ? $telephone : null,
        'sujet'  => $sujet,
        'msg'    => $message
    ]);

    setFlashMessage('success', "Votre message a été envoyé avec succès. Notre équipe vous contactera rapidement.");
    header("Location: " . SITE_URL . "pages/public/contact.php");
    exit;
} catch (\PDOException $e) {
    setFlashMessage('danger', "Une erreur technique s'est produite lors de l'envoi du message.");
    header("Location: " . SITE_URL . "pages/public/contact.php");
    exit;
}
?>
