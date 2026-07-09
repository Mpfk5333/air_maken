<?php
// traitement_mdp_oublie.php - Logique de réinitialisation mdp
require_once dirname(dirname(__FILE__)) . '/config/constantes.php';
require_once dirname(dirname(__FILE__)) . '/config/connexion.php';
require_once dirname(dirname(__FILE__)) . '/fonctions/fonctions_securite.php';
require_once dirname(dirname(__FILE__)) . '/fonctions/fonctions_validation.php';
require_once dirname(dirname(__FILE__)) . '/fonctions/fonctions_utils.php';
require_once dirname(dirname(__FILE__)) . '/fonctions/fonctions_session.php';

initSecureSession();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: " . SITE_URL . "pages/public/mot-de-passe-oublie.php");
    exit;
}

// 1. Validation CSRF
if (!isset($_POST['csrf_token']) || !verifyCsrfToken($_POST['csrf_token'])) {
    setFlashMessage('danger', "Session de sécurité invalide. Veuillez réessayer.");
    header("Location: " . SITE_URL . "pages/public/mot-de-passe-oublie.php");
    exit;
}

// 2. Traiter la demande de lien (Étape 1)
if (isset($_POST['action_request'])) {
    $email = trim($_POST['email'] ?? '');

    if (!validateEmail($email)) {
        setFlashMessage('danger', "Veuillez saisir une adresse email valide.");
        header("Location: " . SITE_URL . "pages/public/mot-de-passe-oublie.php");
        exit;
    }

    try {
        // Vérifier si le compte existe
        $stmt = $pdo->prepare("SELECT id, nom, prenom FROM utilisateurs WHERE email = :email");
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch();

        if ($user) {
            // Génération du token de réinitialisation
            $token = bin2hex(random_bytes(32));
            
            // Sauvegarde temporaire en session pour simulation
            $_SESSION['reset_token'] = $token;
            $_SESSION['reset_email'] = $email;
            $_SESSION['reset_token_expiry'] = time() + 1800; // Valide 30 minutes

            // Message contenant le lien simulé (XAMPP local simulation)
            $reset_link = SITE_URL . "pages/public/mot-de-passe-oublie.php?token=" . $token;
            setFlashMessage('success', "Un email de réinitialisation a été simulé. Cliquez sur ce lien pour changer votre mot de passe : <a href='" . $reset_link . "' style='font-weight:bold;text-decoration:underline;'>Changer mon mot de passe</a>");
        } else {
            // Pour des raisons de sécurité, on peut renvoyer le même message ou être précis selon les besoins.
            // L'analyse dit : "Email inexistant -> message Aucun compte n'est associé à cette adresse email"
            setFlashMessage('danger', "Aucun compte n'est associé à cette adresse email.");
        }
        
        header("Location: " . SITE_URL . "pages/public/mot-de-passe-oublie.php");
        exit;

    } catch (\PDOException $e) {
        setFlashMessage('danger', "Une erreur technique s'est produite. Veuillez réessayer.");
        header("Location: " . SITE_URL . "pages/public/mot-de-passe-oublie.php");
        exit;
    }
}

// 3. Traiter le changement de mot de passe (Étape 2)
if (isset($_POST['action_reset'])) {
    $token = $_POST['token'] ?? '';
    $nouveau_mdp = $_POST['nouveau_mdp'] ?? '';
    $confirm_mdp = $_POST['confirm_mdp'] ?? '';

    // Vérifier la validité du token
    if (!isset($_SESSION['reset_token']) || $_SESSION['reset_token'] !== $token || !isset($_SESSION['reset_token_expiry']) || $_SESSION['reset_token_expiry'] < time()) {
        setFlashMessage('danger', "Ce lien de réinitialisation n'est plus valide ou a expiré.");
        header("Location: " . SITE_URL . "pages/public/mot-de-passe-oublie.php");
        exit;
    }

    if (strlen($nouveau_mdp) < 8) {
        setFlashMessage('danger', "Le mot de passe doit faire au moins 8 caractères.");
        header("Location: " . SITE_URL . "pages/public/mot-de-passe-oublie.php?token=" . urlencode($token));
        exit;
    }

    if ($nouveau_mdp !== $confirm_mdp) {
        setFlashMessage('danger', "Les mots de passe saisis ne correspondent pas.");
        header("Location: " . SITE_URL . "pages/public/mot-de-passe-oublie.php?token=" . urlencode($token));
        exit;
    }

    try {
        $email = $_SESSION['reset_email'];
        $hashed_pass = hashPassword($nouveau_mdp);

        // Mettre à jour en BDD
        $stmt = $pdo->prepare("UPDATE utilisateurs SET mot_de_passe = :pass WHERE email = :email");
        $stmt->execute([
            'pass' => $hashed_pass,
            'email' => $email
        ]);

        // Nettoyer la session
        unset($_SESSION['reset_token']);
        unset($_SESSION['reset_email']);
        unset($_SESSION['reset_token_expiry']);

        setFlashMessage('success', "Votre mot de passe a été réinitialisé avec succès ! Connectez-vous.");
        header("Location: " . SITE_URL . "pages/public/connexion.php");
        exit;

    } catch (\PDOException $e) {
        setFlashMessage('danger', "Une erreur technique s'est produite. Veuillez réessayer.");
        header("Location: " . SITE_URL . "pages/public/mot-de-passe-oublie.php?token=" . urlencode($token));
        exit;
    }
}
?>
