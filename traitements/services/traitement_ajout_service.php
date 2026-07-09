<?php
// traitement_ajout_service.php
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
    header("Location: " . SITE_URL . "pages/admin/ajouter-service.php");
    exit;
}

$nom        = trim($_POST['nom'] ?? '');
$categorie  = $_POST['categorie'] ?? '';
$desc       = trim($_POST['description'] ?? '');
$prix       = $_POST['prix_indicatif'] ?? '';

$categories_valides = ['billetterie','hotel','vehicule','visa','assurance','voyage'];

if (empty($nom) || !in_array($categorie, $categories_valides)) {
    setFlashMessage('danger', "Le nom et la catégorie sont obligatoires.");
    header("Location: " . SITE_URL . "pages/admin/ajouter-service.php");
    exit;
}

$prix_val = ($prix !== '' && is_numeric($prix) && $prix >= 0) ? floatval($prix) : null;

try {
    $stmt = $pdo->prepare("INSERT INTO services (nom, categorie, description, prix_indicatif) VALUES (:nom, :cat, :desc, :prix)");
    $stmt->execute(['nom' => $nom, 'cat' => $categorie, 'desc' => $desc, 'prix' => $prix_val]);
    setFlashMessage('success', "Service « $nom » ajouté avec succès.");
    header("Location: " . SITE_URL . "pages/admin/gestion-services.php");
    exit;
} catch (\PDOException $e) {
    setFlashMessage('danger', "Erreur technique lors de l'ajout.");
    header("Location: " . SITE_URL . "pages/admin/ajouter-service.php");
    exit;
}
?>
