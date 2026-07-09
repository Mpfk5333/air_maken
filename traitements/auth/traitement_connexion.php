<?php
// traitement_connexion.php - Logique de connexion client ET administrateur
require_once dirname(dirname(__FILE__)) . '/config/constantes.php';
require_once dirname(dirname(__FILE__)) . '/config/connexion.php';
require_once dirname(dirname(__FILE__)) . '/fonctions/fonctions_securite.php';
require_once dirname(dirname(__FILE__)) . '/fonctions/fonctions_validation.php';
require_once dirname(dirname(__FILE__)) . '/fonctions/fonctions_utils.php';
require_once dirname(dirname(__FILE__)) . '/fonctions/fonctions_session.php';

initSecureSession();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: " . SITE_URL . "pages/public/connexion.php");
    exit;
}

// 1. Validation CSRF
if (!isset($_POST['csrf_token']) || !verifyCsrfToken($_POST['csrf_token'])) {
    setFlashMessage('danger', "Session de sécurité invalide. Veuillez réessayer.");
    header("Location: " . SITE_URL . "pages/public/connexion.php");
    exit;
}

$login_type = $_POST['login_type'] ?? 'client';
$email      = trim($_POST['email'] ?? '');
$mot_de_passe = $_POST['mot_de_passe'] ?? '';

if (empty($email) || empty($mot_de_passe)) {
    $redirect = ($login_type === 'admin')
        ? "pages/admin/connexion-admin.php"
        : "pages/public/connexion.php";
    setFlashMessage('danger', "Veuillez remplir tous les champs obligatoires.");
    header("Location: " . SITE_URL . $redirect);
    exit;
}

/* ============================================================
   A) CONNEXION ADMINISTRATEUR / AGENT
   ============================================================ */
if ($login_type === 'admin') {
    $redirect_fail = SITE_URL . "pages/admin/connexion-admin.php";

    // Protection brute-force : compteur de tentatives en session
    if (!isset($_SESSION['admin_login_attempts'])) {
        $_SESSION['admin_login_attempts'] = 0;
        $_SESSION['admin_login_last']     = time();
    }

    // Réinitialiser le compteur si la fenêtre de 15 min est dépassée
    if (time() - $_SESSION['admin_login_last'] > 900) {
        $_SESSION['admin_login_attempts'] = 0;
        $_SESSION['admin_login_last']     = time();
    }

    if ($_SESSION['admin_login_attempts'] >= 5) {
        $remaining = 15 - round((time() - $_SESSION['admin_login_last']) / 60);
        setFlashMessage('danger', "Trop de tentatives échouées. Veuillez réessayer dans environ {$remaining} minute(s).");
        header("Location: " . $redirect_fail);
        exit;
    }

    try {
        $stmt = $pdo->prepare("SELECT * FROM administrateurs WHERE email = :email");
        $stmt->execute(['email' => $email]);
        $admin = $stmt->fetch();

        if ($admin && verifyPassword($mot_de_passe, $admin['mot_de_passe'])) {
            // Succès — reset compteur
            $_SESSION['admin_login_attempts'] = 0;
            session_regenerate_id(true);

            $_SESSION['admin_id']   = $admin['id'];
            $_SESSION['admin_nom']  = $admin['nom'];
            $_SESSION['admin_email']= $admin['email'];
            $_SESSION['admin_role'] = $admin['role'];

            setFlashMessage('success', "Bienvenue, " . $admin['nom'] . " !");
            header("Location: " . SITE_URL . "pages/admin/tableau-de-bord.php");
            exit;
        } else {
            // Échec
            $_SESSION['admin_login_attempts']++;
            $_SESSION['admin_login_last'] = time();
            setFlashMessage('danger', "Identifiants incorrects.");
            header("Location: " . $redirect_fail);
            exit;
        }

    } catch (\PDOException $e) {
        setFlashMessage('danger', "Erreur technique. Veuillez réessayer.");
        header("Location: " . $redirect_fail);
        exit;
    }
}

/* ============================================================
   B) CONNEXION CLIENT
   ============================================================ */
$redirect_fail = SITE_URL . "pages/public/connexion.php";

try {
    $stmt = $pdo->prepare("SELECT * FROM utilisateurs WHERE email = :email");
    $stmt->execute(['email' => $email]);
    $user = $stmt->fetch();

    if ($user && verifyPassword($mot_de_passe, $user['mot_de_passe'])) {
        if ($user['statut'] === 'bloque') {
            setFlashMessage('danger', "Votre compte est actuellement suspendu, veuillez contacter l'agence.");
            header("Location: " . $redirect_fail);
            exit;
        }

        session_regenerate_id(true);
        $_SESSION['user_id']     = $user['id'];
        $_SESSION['user_nom']    = $user['nom'];
        $_SESSION['user_prenom'] = $user['prenom'];
        $_SESSION['user_email']  = $user['email'];
        $_SESSION['user_type']   = 'client';

        $redirect = SITE_URL . "pages/public/mon-compte.php";
        if (!empty($_SESSION['redirect_url'])) {
            $redirect = $_SESSION['redirect_url'];
            unset($_SESSION['redirect_url']);
        }

        setFlashMessage('success', "Ravi de vous revoir, " . $user['prenom'] . " !");
        header("Location: " . $redirect);
        exit;
    } else {
        setFlashMessage('danger', "Email ou mot de passe incorrect.");
        header("Location: " . $redirect_fail);
        exit;
    }

} catch (\PDOException $e) {
    setFlashMessage('danger', "Une erreur technique s'est produite. Veuillez réessayer.");
    header("Location: " . $redirect_fail);
    exit;
}
?>
