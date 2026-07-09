<?php
// reservations.php - Formulaire et demandes de réservations
$page_title = 'AIR MAKEN - Réservation';
$extra_css = ['reservations.css'];
require_once dirname(dirname(dirname(__FILE__))) . '/templates/header.php';
require_once dirname(dirname(dirname(__FILE__))) . '/templates/navbar.php';

// Protection de la page
requireLogin();

// Charger les services de la base de données
require_once dirname(dirname(dirname(__FILE__))) . '/traitements/config/connexion.php';
try {
    $stmt = $pdo->prepare("SELECT * FROM services WHERE statut = 'actif'");
    $stmt->execute();
    $all_services = $stmt->fetchAll();
} catch (\PDOException $e) {
    $all_services = [];
}

// Récupération des paramètres GET
$preselected_cat = $_GET['categorie'] ?? '';
$preselected_service_id = $_GET['service_id'] ?? '';

if ($preselected_service_id !== '' && $preselected_cat === '') {
    foreach ($all_services as $srv) {
        if ($srv['id'] == $preselected_service_id) {
            $preselected_cat = $srv['categorie'];
            break;
        }
    }
}

$categories = [
    'billetterie' => 'Billetterie Aérienne',
    'hotel' => 'Réservation d\'Hôtel',
    'vehicule' => 'Location de Véhicule',
    'visa' => 'Assistance Visa',
    'assurance' => 'Assurance Voyage',
    'voyage' => 'Voyage Organisé'
];

if (!array_key_exists($preselected_cat, $categories)) {
    $preselected_cat = 'billetterie';
}

$csrf_token = generateCsrfToken();
?>
<div class="container py-lg">
    <div class="reservation-container">
        <div class="text-center mb-lg">
            <h1 style="font-family: var(--font-title); font-size: 2.2rem; margin-bottom: 0.5rem;">Faire une Réservation</h1>
            <p class="text-muted">Soumettez votre demande en ligne. Notre équipe vous recontactera sous 24h avec les tarifs précis.</p>
        </div>

        <?php require_once dirname(dirname(dirname(__FILE__))) . '/templates/alertes.php'; ?>

        <!-- Sélecteur Global de Type de Service -->
        <div class="service-type-selector">
            <label for="type_service" class="form-label" style="display: block; margin-bottom: 0.5rem; font-weight:700;">Choisissez le type de service :</label>
            <select id="type_service">
                <?php foreach ($categories as $cat_key => $cat_label): ?>
                    <option value="<?php echo $cat_key; ?>" <?php echo ($preselected_cat === $cat_key) ? 'selected' : ''; ?>>
                        <?php echo $cat_label; ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <!-- Formulaire de réservation unifié -->
        <form action="<?php echo SITE_URL; ?>traitements/reservations/traitement_nouvelle_reservation.php" method="POST" class="card">
            <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
            <input type="hidden" name="type_service" id="form_type_service" value="<?php echo escape($preselected_cat); ?>">

            <!-- ========================================== -->
            <!-- SECTION : BILLETTERIE                      -->
            <!-- ========================================== -->
            <div class="dynamic-fields-section" id="section_billetterie">
                <h3 class="mb-md" style="font-family: var(--font-title); color: var(--primary); border-bottom:1px solid var(--border-color); padding-bottom:0.5rem;"><i class="fa-solid fa-plane"></i> Vols & Billets</h3>
                <div class="form-row">
                    <div class="form-group">
                        <label for="ville_depart" class="form-label">Ville de départ <span style="color: var(--danger);">*</span></label>
                        <input type="text" name="ville_depart" id="ville_depart" class="form-control" placeholder="Ex: Malabo" data-required required>
                    </div>
                    <div class="form-group">
                        <label for="ville_arrivee" class="form-label">Ville d'arrivée <span style="color: var(--danger);">*</span></label>
                        <input type="text" name="ville_arrivee" id="ville_arrivee" class="form-control" placeholder="Ex: Madrid" data-required required>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="date_debut_vol" class="form-label">Date aller <span style="color: var(--danger);">*</span></label>
                        <input type="date" name="date_debut_vol" id="date_debut_vol" class="form-control" data-required required>
                    </div>
                    <div class="form-group">
                        <label for="date_fin_vol" class="form-label">Date retour (Optionnel)</label>
                        <input type="date" name="date_fin_vol" id="date_fin_vol" class="form-control">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="classe_voyage" class="form-label">Classe <span style="color: var(--danger);">*</span></label>
                        <select name="classe_voyage" id="classe_voyage" class="form-control" data-required required>
                            <option value="economique">Économique</option>
                            <option value="affaires">Affaires</option>
                            <option value="premiere">Première classe</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="voyageurs_vol" class="form-label">Nombre de passagers <span style="color: var(--danger);">*</span></label>
                        <input type="number" name="voyageurs_vol" id="voyageurs_vol" class="form-control" min="1" max="9" value="1" data-required required>
                    </div>
                </div>
            </div>

            <!-- ========================================== -->
            <!-- SECTION : HÔTEL                            -->
            <!-- ========================================== -->
            <div class="dynamic-fields-section" id="section_hotel">
                <h3 class="mb-md" style="font-family: var(--font-title); color: var(--primary); border-bottom:1px solid var(--border-color); padding-bottom:0.5rem;"><i class="fa-solid fa-hotel"></i> Hôtels & Séjours</h3>
                <div class="form-group">
                    <label for="nom_hotel" class="form-label">Hôtel préféré ou destination</label>
                    <input type="text" name="nom_hotel" id="nom_hotel" class="form-control" placeholder="Ex: Hilton Malabo (ou vide pour sélection par l'agence)">
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="date_debut_hotel" class="form-label">Date d'arrivée <span style="color: var(--danger);">*</span></label>
                        <input type="date" name="date_debut_hotel" id="date_debut_hotel" class="form-control" data-required required>
                    </div>
                    <div class="form-group">
                        <label for="date_fin_hotel" class="form-label">Date de départ <span style="color: var(--danger);">*</span></label>
                        <input type="date" name="date_fin_hotel" id="date_fin_hotel" class="form-control" data-required required>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="type_chambre" class="form-label">Type de chambre <span style="color: var(--danger);">*</span></label>
                        <select name="type_chambre" id="type_chambre" class="form-control" data-required required>
                            <option value="simple">Simple</option>
                            <option value="double">Double</option>
                            <option value="suite">Suite</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="voyageurs_hotel" class="form-label">Nombre de personnes <span style="color: var(--danger);">*</span></label>
                        <input type="number" name="voyageurs_hotel" id="voyageurs_hotel" class="form-control" min="1" max="10" value="1" data-required required>
                    </div>
                </div>
            </div>

            <!-- ========================================== -->
            <!-- SECTION : VÉHICULE                         -->
            <!-- ========================================== -->
            <div class="dynamic-fields-section" id="section_vehicule">
                <h3 class="mb-md" style="font-family: var(--font-title); color: var(--primary); border-bottom:1px solid var(--border-color); padding-bottom:0.5rem;"><i class="fa-solid fa-car"></i> Véhicules</h3>
                <div class="form-row">
                    <div class="form-group">
                        <label for="categorie_vehicule" class="form-label">Catégorie de véhicule <span style="color: var(--danger);">*</span></label>
                        <select name="categorie_vehicule" id="categorie_vehicule" class="form-control" data-required required>
                            <option value="citadine">Citadine</option>
                            <option value="berline">Berline</option>
                            <option value="suv">SUV / Tout-terrain</option>
                            <option value="utilitaire">Utilitaire</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="option_chauffeur" class="form-label">Avec ou sans chauffeur <span style="color: var(--danger);">*</span></label>
                        <select name="option_chauffeur" id="option_chauffeur" class="form-control" data-required required>
                            <option value="non">Sans chauffeur</option>
                            <option value="oui">Avec chauffeur</option>
                        </select>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="date_debut_vehicule" class="form-label">Date de début <span style="color: var(--danger);">*</span></label>
                        <input type="date" name="date_debut_vehicule" id="date_debut_vehicule" class="form-control" data-required required>
                    </div>
                    <div class="form-group">
                        <label for="date_fin_vehicule" class="form-label">Date de fin <span style="color: var(--danger);">*</span></label>
                        <input type="date" name="date_fin_vehicule" id="date_fin_vehicule" class="form-control" data-required required>
                    </div>
                </div>
            </div>

            <!-- ========================================== -->
            <!-- SECTION : VISA                             -->
            <!-- ========================================== -->
            <div class="dynamic-fields-section" id="section_visa">
                <h3 class="mb-md" style="font-family: var(--font-title); color: var(--primary); border-bottom:1px solid var(--border-color); padding-bottom:0.5rem;"><i class="fa-solid fa-passport"></i> Assistance Visas</h3>
                <div class="form-row">
                    <div class="form-group">
                        <label for="pays_visa" class="form-label">Pays de destination <span style="color: var(--danger);">*</span></label>
                        <input type="text" name="pays_visa" id="pays_visa" class="form-control" placeholder="Ex: Espagne" data-required required>
                    </div>
                    <div class="form-group">
                        <label for="type_visa" class="form-label">Type de visa <span style="color: var(--danger);">*</span></label>
                        <select name="type_visa" id="type_visa" class="form-control" data-required required>
                            <option value="tourisme">Tourisme</option>
                            <option value="affaires">Affaires</option>
                            <option value="etudes">Études</option>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label for="date_debut_visa" class="form-label">Date de départ prévue <span style="color: var(--danger);">*</span></label>
                    <input type="date" name="date_debut_visa" id="date_debut_visa" class="form-control" data-required required>
                </div>
            </div>

            <!-- ========================================== -->
            <!-- SECTION : ASSURANCE                        -->
            <!-- ========================================== -->
            <div class="dynamic-fields-section" id="section_assurance">
                <h3 class="mb-md" style="font-family: var(--font-title); color: var(--primary); border-bottom:1px solid var(--border-color); padding-bottom:0.5rem;"><i class="fa-solid fa-shield-halved"></i> Assurances voyages</h3>
                <div class="form-group">
                    <label for="zone_assurance" class="form-label">Zone de couverture <span style="color: var(--danger);">*</span></label>
                    <select name="zone_assurance" id="zone_assurance" class="form-control" data-required required>
                        <option value="afrique">Afrique</option>
                        <option value="europe">Europe (Espace Schengen)</option>
                        <option value="monde">Monde entier</option>
                    </select>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="date_debut_assurance" class="form-label">Date de début <span style="color: var(--danger);">*</span></label>
                        <input type="date" name="date_debut_assurance" id="date_debut_assurance" class="form-control" data-required required>
                    </div>
                    <div class="form-group">
                        <label for="date_fin_assurance" class="form-label">Date de fin <span style="color: var(--danger);">*</span></label>
                        <input type="date" name="date_fin_assurance" id="date_fin_assurance" class="form-control" data-required required>
                    </div>
                </div>
            </div>

            <!-- ========================================== -->
            <!-- SECTION : VOYAGE                           -->
            <!-- ========================================== -->
            <div class="dynamic-fields-section" id="section_voyage">
                <h3 class="mb-md" style="font-family: var(--font-title); color: var(--primary); border-bottom:1px solid var(--border-color); padding-bottom:0.5rem;"><i class="fa-solid fa-map-location-dot"></i> Voyages Organisés</h3>
                <div class="form-group">
                    <label for="service_id_voyage" class="form-label">Choisissez un package de voyage <span style="color: var(--danger);">*</span></label>
                    <select name="service_id_voyage" id="service_id_voyage" class="form-control" data-required required>
                        <option value="">-- Choisissez un voyage organisé --</option>
                        <?php foreach ($all_services as $srv): ?>
                            <?php if ($srv['categorie'] === 'voyage'): ?>
                                <option value="<?php echo $srv['id']; ?>" <?php echo ($preselected_service_id == $srv['id']) ? 'selected' : ''; ?>>
                                    <?php echo escape($srv['nom']); ?> (<?php echo formatPrice($srv['prix_indicatif']); ?>)
                                </option>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="date_debut_voyage" class="form-label">Date de départ souhaitée <span style="color: var(--danger);">*</span></label>
                        <input type="date" name="date_debut_voyage" id="date_debut_voyage" class="form-control" data-required required>
                    </div>
                    <div class="form-group">
                        <label for="voyageurs_voyage" class="form-label">Nombre de personnes <span style="color: var(--danger);">*</span></label>
                        <input type="number" name="voyageurs_voyage" id="voyageurs_voyage" class="form-control" min="1" max="20" value="1" data-required required>
                    </div>
                </div>
            </div>

            <!-- Remarques Additionnelles communes -->
            <div class="form-group" style="margin-top: var(--spacing-md);">
                <label for="remarques" class="form-label">Demandes particulières / Précisions</label>
                <textarea name="remarques" id="remarques" class="form-control" rows="4" placeholder="Saisissez ici toute remarque utile à nos agents..."></textarea>
            </div>

            <button type="submit" class="btn btn-primary btn-lg" style="width: 100%; margin-top: var(--spacing-sm);">
                Soumettre ma demande
            </button>
        </form>
    </div>
</div>

<script>
// Met à jour la valeur du type de service soumis dans le formulaire
document.getElementById('type_service').addEventListener('change', function() {
    document.getElementById('form_type_service').value = this.value;
});
</script>
<?php
$extra_js = ['reservation.js'];
require_once dirname(dirname(dirname(__FILE__))) . '/templates/footer.php';
?>
