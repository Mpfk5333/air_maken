<?php
// traitement_gestion_contenu.php - Traitement d'édition de contenu
require_once dirname(dirname(__FILE__)) . '/config/constantes.php';
require_once dirname(dirname(__FILE__)) . '/config/connexion.php';
require_once dirname(dirname(__FILE__)) . '/fonctions/fonctions_securite.php';
require_once dirname(dirname(__FILE__)) . '/fonctions/fonctions_utils.php';
require_once dirname(dirname(__FILE__)) . '/fonctions/fonctions_session.php';

requireAdmin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: " . SITE_URL . "pages/admin/gestion-contenu.php");
    exit;
}

// Vérifier CSRF
if (!isset($_POST['csrf_token']) || !verifyCsrfToken($_POST['csrf_token'])) {
    setFlashMessage('danger', "Jeton CSRF invalide.");
    header("Location: " . SITE_URL . "pages/admin/gestion-contenu.php");
    exit;
}

// Récupération et nettoyage des champs accueil
$hero_title    = trim($_POST['hero_title'] ?? '');
$hero_subtitle = trim($_POST['hero_subtitle'] ?? '');
$presentation  = trim($_POST['presentation'] ?? '');

$stat_clients      = trim($_POST['stat_clients'] ?? '');
$stat_experience   = trim($_POST['stat_experience'] ?? '');
$stat_destinations = trim($_POST['stat_destinations'] ?? '');
$stat_support      = trim($_POST['stat_support'] ?? '');

// Reconstruire le JSON stats
$stats_array = [
    'clients' => $stat_clients,
    'experience' => $stat_experience,
    'destinations' => $stat_destinations,
    'support' => $stat_support
];
$stats_json = json_encode($stats_array, JSON_UNESCAPED_UNICODE);

// Récupération et nettoyage des champs apropos
$histoire = trim($_POST['histoire'] ?? '');
$mission  = trim($_POST['mission'] ?? '');
$valeurs  = trim($_POST['valeurs'] ?? '');

if (empty($hero_title) || empty($hero_subtitle) || empty($presentation) || empty($histoire) || empty($mission) || empty($valeurs)) {
    setFlashMessage('danger', "Veuillez remplir tous les champs obligatoires.");
    header("Location: " . SITE_URL . "pages/admin/gestion-contenu.php");
    exit;
}

try {
    $pdo->beginTransaction();

    // Mettre à jour chaque contenu
    $updates = [
        ['accueil', 'hero_title', $hero_title],
        ['accueil', 'hero_subtitle', $hero_subtitle],
        ['accueil', 'presentation', $presentation],
        ['accueil', 'stats', $stats_json],
        ['apropos', 'histoire', $histoire],
        ['apropos', 'mission', $mission],
        ['apropos', 'valeurs', $valeurs]
    ];

    $stmt = $pdo->prepare("UPDATE contenus SET contenu = :val WHERE page = :page AND section = :sect");

    foreach ($updates as $update) {
        $stmt->execute([
            'val'  => $update[2],
            'page' => $update[0],
            'sect' => $update[1]
        ]);
    }

    $pdo->commit();
    setFlashMessage('success', "Le contenu du site a été mis à jour avec succès.");
    header("Location: " . SITE_URL . "pages/admin/gestion-contenu.php");
    exit;
} catch (\PDOException $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    setFlashMessage('danger', "Erreur technique lors de la mise à jour : " . $e->getMessage());
    header("Location: " . SITE_URL . "pages/admin/gestion-contenu.php");
    exit;
}
?>
