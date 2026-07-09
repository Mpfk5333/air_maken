<?php
// traitement_statistiques.php - Exportation CSV des réservations
require_once dirname(dirname(__FILE__)) . '/config/constantes.php';
require_once dirname(dirname(__FILE__)) . '/config/connexion.php';
require_once dirname(dirname(__FILE__)) . '/fonctions/fonctions_securite.php';
require_once dirname(dirname(__FILE__)) . '/fonctions/fonctions_session.php';

requireAdmin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: " . SITE_URL . "pages/admin/statistiques.php");
    exit;
}

// Vérifier CSRF
if (!isset($_POST['csrf_token']) || !verifyCsrfToken($_POST['csrf_token'])) {
    require_once dirname(dirname(__FILE__)) . '/fonctions/fonctions_utils.php';
    setFlashMessage('danger', "Jeton CSRF invalide.");
    header("Location: " . SITE_URL . "pages/admin/statistiques.php");
    exit;
}

try {
    // Récupérer toutes les réservations avec les informations client associées
    $stmt = $pdo->query("
        SELECT r.id, r.date_demande, r.date_debut, r.date_fin, r.type_service, r.statut, r.montant,
               u.nom as client_nom, u.prenom as client_prenom, u.email as client_email
        FROM reservations r
        INNER JOIN utilisateurs u ON r.id_utilisateur = u.id
        ORDER BY r.date_demande DESC
    ");
    $reservations = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Configuration des en-têtes HTTP pour le téléchargement du fichier
    $filename = "reservations_airmaken_" . date('Y-m-d_H-i') . ".csv";
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Pragma: no-cache');
    header('Expires: 0');

    // Ouverture du flux de sortie standard PHP
    $output = fopen('php://output', 'w');

    // Insertion du BOM UTF-8 pour Excel
    fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

    // En-têtes des colonnes CSV
    fputcsv($output, [
        'ID Réservation',
        'Date Demande',
        'Client',
        'Email Client',
        'Service',
        'Date Début',
        'Date Fin',
        'Statut',
        'Montant (FCFA)'
    ], ';');

    // Ajout des lignes
    $cat_labels = [
        'billetterie' => 'Billetterie Aérienne',
        'hotel'       => 'Hôtel',
        'vehicule'    => 'Location Véhicule',
        'visa'        => 'Assistance Visa',
        'assurance'   => 'Assurance Voyage',
        'voyage'      => 'Voyage Organisé',
    ];

    $statut_labels = [
        'en_attente' => 'En attente',
        'confirmee'  => 'Confirmée',
        'refusee'    => 'Refusée',
        'annulee'    => 'Annulée',
    ];

    foreach ($reservations as $row) {
        fputcsv($output, [
            $row['id'],
            $row['date_demande'],
            $row['client_prenom'] . ' ' . $row['client_nom'],
            $row['client_email'],
            $cat_labels[$row['type_service']] ?? ucfirst($row['type_service']),
            $row['date_debut'],
            $row['date_fin'] ?? '-',
            $statut_labels[$row['statut']] ?? $row['statut'],
            $row['montant'] !== null ? number_format($row['montant'], 0, ',', '') : 'Sur devis'
        ], ';');
    }

    fclose($output);
    exit;

} catch (\PDOException $e) {
    require_once dirname(dirname(__FILE__)) . '/fonctions/fonctions_utils.php';
    setFlashMessage('danger', "Erreur technique lors de la génération de l'export.");
    header("Location: " . SITE_URL . "pages/admin/statistiques.php");
    exit;
}
?>
