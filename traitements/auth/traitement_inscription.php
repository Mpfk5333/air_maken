<?php
// traitement_inscription.php - Logique d'inscription client
require_once dirname(dirname(__FILE__)) . '/config/constantes.php';
require_once dirname(dirname(__FILE__)) . '/config/connexion.php';
require_once dirname(dirname(__FILE__)) . '/fonctions/fonctions_securite.php';
require_once dirname(dirname(__FILE__)) . '/fonctions/fonctions_validation.php';
require_once dirname(dirname(__FILE__)) . '/fonctions/fonctions_utils.php';
require_once dirname(dirname(__FILE__)) . '/fonctions/fonctions_session.php';

initSecureSession();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: " . SITE_URL . "pages/public/inscription.php");
    exit;
}

// 1. Validation CSRF
if (!isset($_POST['csrf_token']) || !verifyCsrfToken($_POST['csrf_token'])) {
    setFlashMessage('danger', "Session de sécurité invalide. Veuillez réessayer.");
    header("Location: " . SITE_URL . "pages/public/inscription.php");
    exit;
}

// 2. Récupération des données
$nom = trim($_POST['nom'] ?? '');
$prenom = trim($_POST['prenom'] ?? '');
$email = trim($_POST['email'] ?? '');
$telephone = trim($_POST['telephone'] ?? '');
$mot_de_passe = $_POST['mot_de_passe'] ?? '';
$confirm_mot_de_passe = $_POST['confirm_mot_de_passe'] ?? '';

// 3. Validation des champs requis
$required = ['nom', 'prenom', 'email', 'telephone', 'mot_de_passe', 'confirm_mot_de_passe'];
$missing = validateRequiredFields($required, $_POST);
if (!empty($missing)) {
    setFlashMessage('danger', "Veuillez remplir tous les champs obligatoires.");
    header("Location: " . SITE_URL . "pages/public/inscription.php");
    exit;
}

// 4. Validations de formats et contraintes de sécurité
if (!validateEmail($email)) {
    setFlashMessage('danger', "L'adresse email saisie est incorrecte.");
    header("Location: " . SITE_URL . "pages/public/inscription.php");
    exit;
}

if (!validatePhone($telephone)) {
    setFlashMessage('danger', "Le format du numéro de téléphone est incorrect.");
    header("Location: " . SITE_URL . "pages/public/inscription.php");
    exit;
}

if (strlen($mot_de_passe) < 8) {
    setFlashMessage('danger', "Le mot de passe doit faire au moins 8 caractères.");
    header("Location: " . SITE_URL . "pages/public/inscription.php");
    exit;
}

if ($mot_de_passe !== $confirm_mot_de_passe) {
    setFlashMessage('danger', "Les mots de passe saisis ne correspondent pas.");
    header("Location: " . SITE_URL . "pages/public/inscription.php");
    exit;
}

try {
    // 5. Vérification de l'existence de l'email
    $stmt = $pdo->prepare("SELECT id FROM utilisateurs WHERE email = :email");
    $stmt->execute(['email' => $email]);
    if ($stmt->fetch()) {
        setFlashMessage('danger', "Cette adresse email est déjà utilisée.");
        header("Location: " . SITE_URL . "pages/public/inscription.php");
        exit;
    }

    // 6. Hachage du mot de passe et création du compte
    $hashed_pass = hashPassword($mot_de_passe);
    $stmt = $pdo->prepare("INSERT INTO utilisateurs (nom, prenom, email, telephone, mot_de_passe, statut) VALUES (:nom, :prenom, :email, :telephone, :mot_de_passe, 'actif')");
    $stmt->execute([
        'nom' => $nom,
        'prenom' => $prenom,
        'email' => $email,
        'telephone' => $telephone,
        'mot_de_passe' => $hashed_pass
    ]);

    setFlashMessage('success', "Inscription réussie ! Vous pouvez maintenant vous connecter.");
    header("Location: " . SITE_URL . "pages/public/connexion.php");
    exit;
    
} catch (\PDOException $e) {
    setFlashMessage('danger', "Une erreur technique s'est produite. Veuillez réessayer.");
    header("Location: " . SITE_URL . "pages/public/inscription.php");
    exit;
}
?>
