<?php
// traitement_nouvelle_reservation.php - Traitement d'une nouvelle réservation
require_once dirname(dirname(__FILE__)) . '/config/constantes.php';
require_once dirname(dirname(__FILE__)) . '/config/connexion.php';
require_once dirname(dirname(__FILE__)) . '/fonctions/fonctions_securite.php';
require_once dirname(dirname(__FILE__)) . '/fonctions/fonctions_validation.php';
require_once dirname(dirname(__FILE__)) . '/fonctions/fonctions_utils.php';
require_once dirname(dirname(__FILE__)) . '/fonctions/fonctions_session.php';

// Protection
requireLogin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: " . SITE_URL . "pages/public/reservations.php");
    exit;
}

// 1. Validation CSRF
if (!isset($_POST['csrf_token']) || !verifyCsrfToken($_POST['csrf_token'])) {
    setFlashMessage('danger', "Session de sécurité invalide.");
    header("Location: " . SITE_URL . "pages/public/reservations.php");
    exit;
}

$type_service = $_POST['type_service'] ?? '';
$remarques = trim($_POST['remarques'] ?? '');

$date_debut = '';
$date_fin = null;
$id_service = null;
$montant = null;
$details = [];

try {
    if ($type_service === 'billetterie') {
        $ville_depart = trim($_POST['ville_depart'] ?? '');
        $ville_arrivee = trim($_POST['ville_arrivee'] ?? '');
        $date_debut = $_POST['date_debut_vol'] ?? '';
        $date_fin = $_POST['date_fin_vol'] ?? null;
        $classe_voyage = $_POST['classe_voyage'] ?? '';
        $voyageurs = intval($_POST['voyageurs_vol'] ?? 1);
        
        if (empty($ville_depart) || empty($ville_arrivee) || empty($date_debut) || empty($classe_voyage)) {
            setFlashMessage('danger', "Veuillez remplir tous les champs obligatoires pour le vol.");
            header("Location: " . SITE_URL . "pages/public/reservations.php?categorie=billetterie");
            exit;
        }
        
        $details = [
            'ville_depart' => $ville_depart,
            'ville_arrivee' => $ville_arrivee,
            'classe_voyage' => $classe_voyage,
            'voyageurs' => $voyageurs,
            'remarques' => $remarques
        ];
        
    } elseif ($type_service === 'hotel') {
        $nom_hotel = trim($_POST['nom_hotel'] ?? '');
        $date_debut = $_POST['date_debut_hotel'] ?? '';
        $date_fin = $_POST['date_fin_hotel'] ?? '';
        $type_chambre = $_POST['type_chambre'] ?? '';
        $voyageurs = intval($_POST['voyageurs_hotel'] ?? 1);
        
        if (empty($date_debut) || empty($date_fin) || empty($type_chambre)) {
            setFlashMessage('danger', "Veuillez remplir tous les champs d'hôtel requis.");
            header("Location: " . SITE_URL . "pages/public/reservations.php?categorie=hotel");
            exit;
        }
        
        $details = [
            'nom_hotel' => $nom_hotel,
            'type_chambre' => $type_chambre,
            'voyageurs' => $voyageurs,
            'remarques' => $remarques
        ];
        
    } elseif ($type_service === 'vehicule') {
        $categorie_vehicule = $_POST['categorie_vehicule'] ?? '';
        $option_chauffeur = $_POST['option_chauffeur'] ?? 'non';
        $date_debut = $_POST['date_debut_vehicule'] ?? '';
        $date_fin = $_POST['date_fin_vehicule'] ?? '';
        
        if (empty($categorie_vehicule) || empty($date_debut) || empty($date_fin)) {
            setFlashMessage('danger', "Veuillez remplir tous les champs requis pour le véhicule.");
            header("Location: " . SITE_URL . "pages/public/reservations.php?categorie=vehicule");
            exit;
        }
        
        $details = [
            'categorie_vehicule' => $categorie_vehicule,
            'option_chauffeur' => $option_chauffeur,
            'remarques' => $remarques
        ];
        
        // Calcul montant indicatif basé sur le nombre de jours (par ex. 50 000 FCFA/jour)
        $diff = strtotime($date_fin) - strtotime($date_debut);
        $days = max(1, ceil($diff / (60 * 60 * 24)));
        $montant = $days * 50000.00;
        
    } elseif ($type_service === 'visa') {
        $pays_visa = trim($_POST['pays_visa'] ?? '');
        $type_visa = $_POST['type_visa'] ?? '';
        $date_debut = $_POST['date_debut_visa'] ?? '';
        
        if (empty($pays_visa) || empty($type_visa) || empty($date_debut)) {
            setFlashMessage('danger', "Veuillez spécifier le pays de destination et le type de visa.");
            header("Location: " . SITE_URL . "pages/public/reservations.php?categorie=visa");
            exit;
        }
        
        $details = [
            'pays_visa' => $pays_visa,
            'type_visa' => $type_visa,
            'remarques' => $remarques
        ];
        
        $montant = 75000.00; // Tarif de base d'assistance visa
        
    } elseif ($type_service === 'assurance') {
        $zone_assurance = $_POST['zone_assurance'] ?? '';
        $date_debut = $_POST['date_debut_assurance'] ?? '';
        $date_fin = $_POST['date_fin_assurance'] ?? '';
        
        if (empty($zone_assurance) || empty($date_debut) || empty($date_fin)) {
            setFlashMessage('danger', "Veuillez remplir les dates et la zone pour l'assurance.");
            header("Location: " . SITE_URL . "pages/public/reservations.php?categorie=assurance");
            exit;
        }
        
        $details = [
            'zone_assurance' => $zone_assurance,
            'remarques' => $remarques
        ];
        
        $montant = 25000.00; // Tarif indicatif
        
    } elseif ($type_service === 'voyage') {
        $id_service = intval($_POST['service_id_voyage'] ?? 0);
        $date_debut = $_POST['date_debut_voyage'] ?? '';
        $voyageurs = intval($_POST['voyageurs_voyage'] ?? 1);
        
        if (empty($id_service) || empty($date_debut)) {
            setFlashMessage('danger', "Veuillez choisir un package de voyage organisé.");
            header("Location: " . SITE_URL . "pages/public/reservations.php?categorie=voyage");
            exit;
        }
        
        // Charger le service pour récupérer les infos
        $srv_stmt = $pdo->prepare("SELECT * FROM services WHERE id = :id AND categorie = 'voyage'");
        $srv_stmt->execute(['id' => $id_service]);
        $service = $srv_stmt->fetch();
        
        if (!$service) {
            setFlashMessage('danger', "Le package de voyage sélectionné n'existe pas.");
            header("Location: " . SITE_URL . "pages/public/reservations.php?categorie=voyage");
            exit;
        }
        
        $details = [
            'nom_voyage' => $service['nom'],
            'voyageurs' => $voyageurs,
            'remarques' => $remarques
        ];
        
        if ($service['prix_indicatif'] !== null) {
            $montant = $service['prix_indicatif'] * $voyageurs;
        }
    } else {
        setFlashMessage('danger', "Catégorie de service invalide.");
        header("Location: " . SITE_URL . "pages/public/reservations.php");
        exit;
    }
    
    // 5. Validation des dates commune
    $date_val = validateDates($date_debut, $date_fin);
    if ($date_val !== true) {
        setFlashMessage('danger', $date_val);
        header("Location: " . SITE_URL . "pages/public/reservations.php?categorie=" . urlencode($type_service));
        exit;
    }
    
    // 6. Enregistrement en base de données
    $json_details = json_encode($details, JSON_UNESCAPED_UNICODE);
    
    $ins_stmt = $pdo->prepare("INSERT INTO reservations (id_utilisateur, id_service, type_service, details, date_debut, date_fin, statut, montant) VALUES (:id_user, :id_service, :type_service, :details, :date_debut, :date_fin, 'en_attente', :montant)");
    $ins_stmt->execute([
        'id_user' => $_SESSION['user_id'],
        'id_service' => $id_service,
        'type_service' => $type_service,
        'details' => $json_details,
        'date_debut' => $date_debut,
        'date_fin' => !empty($date_fin) ? $date_fin : null,
        'montant' => $montant
    ]);
    
    setFlashMessage('success', "Votre demande de réservation a été enregistrée avec succès. Notre équipe vous recontactera sous 24h.");
    header("Location: " . SITE_URL . "pages/public/mes-reservations.php");
    exit;
    
} catch (\PDOException $e) {
    setFlashMessage('danger', "Erreur technique lors de l'enregistrement de la réservation.");
    header("Location: " . SITE_URL . "pages/public/reservations.php?categorie=" . urlencode($type_service));
    exit;
}
?>
