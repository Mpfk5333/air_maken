<?php
// traitement_changement_mdp_admin.php - Changement de mot de passe administrateur
require_once dirname(dirname(__FILE__)) . '/config/constantes.php';
require_once dirname(dirname(__FILE__)) . '/config/connexion.php';
require_once dirname(dirname(__FILE__)) . '/fonctions/fonctions_securite.php';
require_once dirname(dirname(__FILE__)) . '/fonctions/fonctions_utils.php';
require_once dirname(dirname(__FILE__)) . '/fonctions/fonctions_session.php';

requireAdmin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: " . SITE_URL . "pages/admin/parametres.php");
    exit;
}

// Vérifier CSRF
if (!isset($_POST['csrf_token']) || !verifyCsrfToken($_POST['csrf_token'])) {
    setFlashMessage('danger', "Jeton CSRF invalide.");
    header("Location: " . SITE_URL . "pages/admin/parametres.php");
    exit;
}

$ancien_mdp  = $_POST['ancien_mdp'] ?? '';
$nouveau_mdp = $_POST['nouveau_mdp'] ?? '';
$confirm_mdp = $_POST['confirm_mdp'] ?? '';

// Validation de base
if (empty($ancien_mdp) || empty($nouveau_mdp) || empty($confirm_mdp)) {
    setFlashMessage('danger', "Veuillez remplir tous les champs.");
    header("Location: " . SITE_URL . "pages/admin/parametres.php");
    exit;
}

if ($nouveau_mdp !== $confirm_mdp) {
    setFlashMessage('danger', "Les deux nouveaux mots de passe ne correspondent pas.");
    header("Location: " . SITE_URL . "pages/admin/parametres.php");
    exit;
}

if (strlen($nouveau_mdp) < 8) {
    setFlashMessage('danger', "Le nouveau mot de passe doit comporter au moins 8 caractères.");
    header("Location: " . SITE_URL . "pages/admin/parametres.php");
    exit;
}

// Récupérer le hash actuel en base de données
$admin_id = intval($_SESSION['admin_id']);
$stmt = $pdo->prepare("SELECT mot_de_passe FROM administrateurs WHERE id = :id");
$stmt->execute(['id' => $admin_id]);
$admin = $stmt->fetch();

if (!$admin) {
    setFlashMessage('danger', "Compte introuvable.");
    header("Location: " . SITE_URL . "pages/admin/parametres.php");
    exit;
}

// Vérifier que l'ancien mot de passe est correct
if (!verifyPassword($ancien_mdp, $admin['mot_de_passe'])) {
    setFlashMessage('danger', "L'ancien mot de passe saisi est incorrect.");
    header("Location: " . SITE_URL . "pages/admin/parametres.php");
    exit;
}

// Hacher et enregistrer le nouveau mot de passe
$nouveau_hash = hashPassword($nouveau_mdp);

try {
    $upd = $pdo->prepare("UPDATE administrateurs SET mot_de_passe = :pwd WHERE id = :id");
    $upd->execute(['pwd' => $nouveau_hash, 'id' => $admin_id]);
    setFlashMessage('success', "Votre mot de passe a été mis à jour avec succès.");
    header("Location: " . SITE_URL . "pages/admin/parametres.php");
    exit;
} catch (\PDOException $e) {
    setFlashMessage('danger', "Erreur technique lors de la mise à jour du mot de passe.");
    header("Location: " . SITE_URL . "pages/admin/parametres.php");
    exit;
}
?>
