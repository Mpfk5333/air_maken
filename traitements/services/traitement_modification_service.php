<?php
// traitement_modification_service.php
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
$nom        = trim($_POST['nom'] ?? '');
$categorie  = $_POST['categorie'] ?? '';
$desc       = trim($_POST['description'] ?? '');
$prix       = $_POST['prix_indicatif'] ?? '';

$categories_valides = ['billetterie','hotel','vehicule','visa','assurance','voyage'];
$redirect = SITE_URL . "pages/admin/modifier-service.php?id=" . $service_id;

if ($service_id <= 0 || empty($nom) || !in_array($categorie, $categories_valides)) {
    setFlashMessage('danger', "Données invalides.");
    header("Location: " . $redirect);
    exit;
}

$prix_val = ($prix !== '' && is_numeric($prix) && $prix >= 0) ? floatval($prix) : null;

try {
    $stmt = $pdo->prepare("UPDATE services SET nom = :nom, categorie = :cat, description = :desc, prix_indicatif = :prix WHERE id = :id");
    $stmt->execute(['nom' => $nom, 'cat' => $categorie, 'desc' => $desc, 'prix' => $prix_val, 'id' => $service_id]);
    setFlashMessage('success', "Service mis à jour avec succès.");
    header("Location: " . SITE_URL . "pages/admin/gestion-services.php");
    exit;
} catch (\PDOException $e) {
    setFlashMessage('danger', "Erreur technique lors de la mise à jour.");
    header("Location: " . $redirect);
    exit;
}
?>
