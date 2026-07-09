<?php
// traitement_modification_profil.php - Mise à jour profil client
require_once dirname(dirname(__FILE__)) . '/config/constantes.php';
require_once dirname(dirname(__FILE__)) . '/config/connexion.php';
require_once dirname(dirname(__FILE__)) . '/fonctions/fonctions_securite.php';
require_once dirname(dirname(__FILE__)) . '/fonctions/fonctions_validation.php';
require_once dirname(dirname(__FILE__)) . '/fonctions/fonctions_utils.php';
require_once dirname(dirname(__FILE__)) . '/fonctions/fonctions_session.php';

// Protection : doit être client connecté
requireLogin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: " . SITE_URL . "pages/public/mon-compte.php");
    exit;
}

// 1. Validation CSRF
if (!isset($_POST['csrf_token']) || !verifyCsrfToken($_POST['csrf_token'])) {
    setFlashMessage('danger', "Session de sécurité expirée. Veuillez rafraîchir.");
    header("Location: " . SITE_URL . "pages/public/mon-compte.php");
    exit;
}

$action = $_POST['action'] ?? '';

// 2. Traiter modification téléphone
if ($action === 'update_profile') {
    $telephone = trim($_POST['telephone'] ?? '');
    
    if (!validatePhone($telephone)) {
        setFlashMessage('danger', "Le format de numéro de téléphone saisi est incorrect.");
        header("Location: " . SITE_URL . "pages/public/mon-compte.php");
        exit;
    }

    try {
        $stmt = $pdo->prepare("UPDATE utilisateurs SET telephone = :tel WHERE id = :id");
        $stmt->execute([
            'tel' => $telephone,
            'id' => $_SESSION['user_id']
        ]);
        
        setFlashMessage('success', "Votre numéro de téléphone a bien été mis à jour.");
    } catch (\PDOException $e) {
        setFlashMessage('danger', "Une erreur est survenue lors de la mise à jour.");
    }
    
    header("Location: " . SITE_URL . "pages/public/mon-compte.php");
    exit;
}

// 3. Traiter modification mot de passe
if ($action === 'update_password') {
    $mot_de_passe_actuel = $_POST['mot_de_passe_actuel'] ?? '';
    $nouveau_mot_de_passe = $_POST['nouveau_mot_de_passe'] ?? '';
    $confirm_mot_de_passe = $_POST['confirm_mot_de_passe'] ?? '';

    $fields = ['mot_de_passe_actuel', 'nouveau_mot_de_passe', 'confirm_mot_de_passe'];
    $missing = validateRequiredFields($fields, $_POST);
    if (!empty($missing)) {
        setFlashMessage('danger', "Veuillez remplir tous les champs du mot de passe.");
        header("Location: " . SITE_URL . "pages/public/mon-compte.php");
        exit;
    }

    if (strlen($nouveau_mot_de_passe) < 8) {
        setFlashMessage('danger', "Le nouveau mot de passe doit faire au moins 8 caractères.");
        header("Location: " . SITE_URL . "pages/public/mon-compte.php");
        exit;
    }

    if ($nouveau_mot_de_passe !== $confirm_mot_de_passe) {
        setFlashMessage('danger', "La confirmation ne correspond pas au nouveau mot de passe.");
        header("Location: " . SITE_URL . "pages/public/mon-compte.php");
        exit;
    }

    try {
        // Obtenir le mot de passe hashé actuel
        $stmt = $pdo->prepare("SELECT mot_de_passe FROM utilisateurs WHERE id = :id");
        $stmt->execute(['id' => $_SESSION['user_id']]);
        $user = $stmt->fetch();

        if ($user && verifyPassword($mot_de_passe_actuel, $user['mot_de_passe'])) {
            // Mettre à jour avec le nouveau hash
            $new_hash = hashPassword($nouveau_mot_de_passe);
            $stmt = $pdo->prepare("UPDATE utilisateurs SET mot_de_passe = :pass WHERE id = :id");
            $stmt->execute([
                'pass' => $new_hash,
                'id' => $_SESSION['user_id']
            ]);
            
            setFlashMessage('success', "Votre mot de passe a été modifié avec succès.");
        } else {
            setFlashMessage('danger', "Le mot de passe actuel saisi est incorrect.");
        }

    } catch (\PDOException $e) {
        setFlashMessage('danger', "Une erreur technique s'est produite.");
    }

    header("Location: " . SITE_URL . "pages/public/mon-compte.php");
    exit;
}

header("Location: " . SITE_URL . "pages/public/mon-compte.php");
exit;
?>
