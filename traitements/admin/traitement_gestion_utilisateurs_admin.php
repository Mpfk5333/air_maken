<?php
// traitement_gestion_utilisateurs_admin.php - Gestion comptes admin/agents
require_once dirname(dirname(__FILE__)) . '/config/constantes.php';
require_once dirname(dirname(__FILE__)) . '/config/connexion.php';
require_once dirname(dirname(__FILE__)) . '/fonctions/fonctions_securite.php';
require_once dirname(dirname(__FILE__)) . '/fonctions/fonctions_utils.php';
require_once dirname(dirname(__FILE__)) . '/fonctions/fonctions_session.php';

requireAdmin();

// Réservé aux Super Admins uniquement
if (!isSuperAdmin()) {
    setFlashMessage('danger', "Action réservée aux Super Administrateurs.");
    header("Location: " . SITE_URL . "pages/admin/tableau-de-bord.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: " . SITE_URL . "pages/admin/gestion-utilisateurs-admin.php");
    exit;
}

// Vérifier CSRF
if (!isset($_POST['csrf_token']) || !verifyCsrfToken($_POST['csrf_token'])) {
    setFlashMessage('danger', "Jeton CSRF invalide.");
    header("Location: " . SITE_URL . "pages/admin/gestion-utilisateurs-admin.php");
    exit;
}

$action = $_POST['action'] ?? '';

// -------------------------------------------------------
// ACTION : AJOUTER UN ADMINISTRATEUR / AGENT
// -------------------------------------------------------
if ($action === 'ajouter') {
    $nom          = trim($_POST['nom'] ?? '');
    $email        = trim($_POST['email'] ?? '');
    $role         = $_POST['role'] ?? '';
    $mot_de_passe = $_POST['mot_de_passe'] ?? '';

    $roles_valides = ['agent', 'super_admin'];

    // Validation
    if (empty($nom) || empty($email) || empty($mot_de_passe) || !in_array($role, $roles_valides)) {
        setFlashMessage('danger', "Tous les champs sont obligatoires et le rôle doit être valide.");
        header("Location: " . SITE_URL . "pages/admin/gestion-utilisateurs-admin.php");
        exit;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        setFlashMessage('danger', "L'adresse e-mail saisie est invalide.");
        header("Location: " . SITE_URL . "pages/admin/gestion-utilisateurs-admin.php");
        exit;
    }

    if (strlen($mot_de_passe) < 8) {
        setFlashMessage('danger', "Le mot de passe doit comporter au moins 8 caractères.");
        header("Location: " . SITE_URL . "pages/admin/gestion-utilisateurs-admin.php");
        exit;
    }

    // Vérifier que l'email n'existe pas déjà
    $check = $pdo->prepare("SELECT COUNT(*) FROM administrateurs WHERE email = :email");
    $check->execute(['email' => $email]);
    if ($check->fetchColumn() > 0) {
        setFlashMessage('danger', "Un administrateur avec cet e-mail existe déjà.");
        header("Location: " . SITE_URL . "pages/admin/gestion-utilisateurs-admin.php");
        exit;
    }

    $hash = hashPassword($mot_de_passe);

    try {
        $stmt = $pdo->prepare("INSERT INTO administrateurs (nom, email, mot_de_passe, role) VALUES (:nom, :email, :pwd, :role)");
        $stmt->execute(['nom' => $nom, 'email' => $email, 'pwd' => $hash, 'role' => $role]);
        setFlashMessage('success', "Compte administrateur « $nom » créé avec succès.");
        header("Location: " . SITE_URL . "pages/admin/gestion-utilisateurs-admin.php");
        exit;
    } catch (\PDOException $e) {
        setFlashMessage('danger', "Erreur technique lors de la création du compte.");
        header("Location: " . SITE_URL . "pages/admin/gestion-utilisateurs-admin.php");
        exit;
    }
}

// -------------------------------------------------------
// ACTION : SUPPRIMER UN ADMINISTRATEUR / AGENT
// -------------------------------------------------------
if ($action === 'supprimer') {
    $admin_id_cible = intval($_POST['admin_id'] ?? 0);

    if ($admin_id_cible <= 0) {
        setFlashMessage('danger', "Administrateur introuvable.");
        header("Location: " . SITE_URL . "pages/admin/gestion-utilisateurs-admin.php");
        exit;
    }

    // Interdire la suppression de son propre compte
    if ($admin_id_cible === intval($_SESSION['admin_id'])) {
        setFlashMessage('danger', "Vous ne pouvez pas supprimer votre propre compte.");
        header("Location: " . SITE_URL . "pages/admin/gestion-utilisateurs-admin.php");
        exit;
    }

    try {
        $stmt = $pdo->prepare("DELETE FROM administrateurs WHERE id = :id");
        $stmt->execute(['id' => $admin_id_cible]);
        setFlashMessage('success', "Compte administrateur supprimé avec succès.");
        header("Location: " . SITE_URL . "pages/admin/gestion-utilisateurs-admin.php");
        exit;
    } catch (\PDOException $e) {
        setFlashMessage('danger', "Erreur technique lors de la suppression.");
        header("Location: " . SITE_URL . "pages/admin/gestion-utilisateurs-admin.php");
        exit;
    }
}

// Action non reconnue
setFlashMessage('danger', "Action non reconnue.");
header("Location: " . SITE_URL . "pages/admin/gestion-utilisateurs-admin.php");
exit;
?>
