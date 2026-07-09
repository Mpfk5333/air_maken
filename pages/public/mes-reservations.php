<?php
// mes-reservations.php - Espace client : historique des réservations
$page_title = 'AIR MAKEN - Mes Réservations';
$extra_css = ['reservations.css'];
require_once dirname(dirname(dirname(__FILE__))) . '/templates/header.php';
require_once dirname(dirname(dirname(__FILE__))) . '/templates/navbar.php';

// Protection de la page
requireLogin();

// Récupération des infos utilisateur
require_once dirname(dirname(dirname(__FILE__))) . '/traitements/config/connexion.php';
$stmt = $pdo->prepare("SELECT * FROM utilisateurs WHERE id = :id");
$stmt->execute(['id' => $_SESSION['user_id']]);
$user = $stmt->fetch();

// Récupérer l'historique des réservations de cet utilisateur
$res_stmt = $pdo->prepare("SELECT r.*, s.nom as nom_service FROM reservations r LEFT JOIN services s ON r.id_service = s.id WHERE r.id_utilisateur = :id ORDER BY r.date_demande DESC");
$res_stmt->execute(['id' => $_SESSION['user_id']]);
$reservations = $res_stmt->fetchAll();

$categories = [
    'billetterie' => 'Billetterie Aérienne',
    'hotel' => 'Réservation d\'Hôtel',
    'vehicule' => 'Location de Véhicule',
    'visa' => 'Assistance Visa',
    'assurance' => 'Assurance Voyage',
    'voyage' => 'Voyage Organisé'
];

$csrf_token = generateCsrfToken();
?>
<div class="container py-lg">
    <div class="account-layout">
        <!-- Sidebar Espace Client -->
        <aside class="account-sidebar">
            <div class="user-avatar-section text-center">
                <div class="user-avatar">
                    <i class="fa-solid fa-user-ninja"></i>
                </div>
                <h3><?php echo escape($user['prenom'] . ' ' . $user['nom']); ?></h3>
                <p><?php echo escape($user['email']); ?></p>
                <span class="badge badge-success">Compte Actif</span>
            </div>
            <ul class="account-menu">
                <li><a href="<?php echo SITE_URL; ?>pages/public/mon-compte.php"><i class="fa-solid fa-address-card"></i> Profil</a></li>
                <li><a href="<?php echo SITE_URL; ?>pages/public/mes-reservations.php" class="active"><i class="fa-solid fa-plane-arrival"></i> Réservations</a></li>
                <li><a href="<?php echo SITE_URL; ?>pages/public/deconnexion.php" style="color: var(--danger);"><i class="fa-solid fa-arrow-right-from-bracket"></i> Déconnexion</a></li>
            </ul>
        </aside>

        <!-- Contenu Principal -->
        <main class="account-content">
            <div class="card">
                <h2 style="font-family: var(--font-title); margin-bottom: 0.25rem;">Mes demandes de réservation</h2>
                <p class="text-muted mb-md">Consultez et suivez le statut de vos demandes de voyages.</p>

                <?php require_once dirname(dirname(dirname(__FILE__))) . '/templates/alertes.php'; ?>

                <?php if (empty($reservations)): ?>
                    <div class="text-center py-lg" style="border: 2px dashed var(--border-color); border-radius: var(--radius-md); padding: 3rem;">
                        <i class="fa-solid fa-plane-slash" style="font-size: 3rem; color: var(--text-light); margin-bottom: 1.5rem;"></i>
                        <p class="text-muted" style="font-size: 1.05rem;">Vous n'avez pas encore soumis de réservation.</p>
                        <a href="<?php echo SITE_URL; ?>pages/public/reservations.php" class="btn btn-primary" style="margin-top: 1.25rem;">Faire ma première réservation</a>
                    </div>
                <?php else: ?>
                    <div class="reservation-table-wrapper">
                        <table class="reservation-table">
                            <thead>
                                <tr>
                                    <th>Service</th>
                                    <th>Détails</th>
                                    <th>Dates</th>
                                    <th>Tarif indicatif</th>
                                    <th>Statut</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($reservations as $res): ?>
                                    <?php
                                    $details = json_decode($res['details'], true) ?? [];
                                    
                                    // Configuration badges statuts
                                    $statut_class = 'badge-warning';
                                    $statut_label = 'En attente';
                                    
                                    if ($res['statut'] === 'confirmee') {
                                        $statut_class = 'badge-success';
                                        $statut_label = 'Confirmée';
                                    } elseif ($res['statut'] === 'refusee') {
                                        $statut_class = 'badge-danger';
                                        $statut_label = 'Refusée';
                                    } elseif ($res['statut'] === 'annulee') {
                                        $statut_class = 'badge-primary';
                                        $statut_label = 'Annulée';
                                    }
                                    ?>
                                    <tr>
                                        <td>
                                            <strong><?php echo escape($categories[$res['type_service']] ?? ucfirst($res['type_service'])); ?></strong>
                                            <?php if (!empty($res['nom_service'])): ?>
                                                <br><span style="font-size: 0.8rem; color: var(--text-muted);"><?php echo escape($res['nom_service']); ?></span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <ul class="details-list">
                                                <?php if ($res['type_service'] === 'billetterie'): ?>
                                                    <li><i class="fa-solid fa-plane-departure"></i> Départ : <?php echo escape($details['ville_depart']); ?></li>
                                                    <li><i class="fa-solid fa-plane-arrival"></i> Arrivée : <?php echo escape($details['ville_arrivee']); ?></li>
                                                    <li><i class="fa-solid fa-chair"></i> Classe : <?php echo escape(ucfirst($details['classe_voyage'])); ?></li>
                                                    <li><i class="fa-solid fa-users"></i> Passagers : <?php echo escape($details['voyageurs']); ?></li>
                                                
                                                <?php elseif ($res['type_service'] === 'hotel'): ?>
                                                    <?php if (!empty($details['nom_hotel'])): ?>
                                                        <li><i class="fa-solid fa-hotel"></i> Hôtel : <?php echo escape($details['nom_hotel']); ?></li>
                                                    <?php endif; ?>
                                                    <li><i class="fa-solid fa-bed"></i> Chambre : <?php echo escape(ucfirst($details['type_chambre'])); ?></li>
                                                    <li><i class="fa-solid fa-users"></i> Personnes : <?php echo escape($details['voyageurs']); ?></li>
                                                
                                                <?php elseif ($res['type_service'] === 'vehicule'): ?>
                                                    <li><i class="fa-solid fa-car-side"></i> Catégorie : <?php echo escape(ucfirst($details['categorie_vehicule'])); ?></li>
                                                    <li><i class="fa-solid fa-user-tie"></i> Chauffeur : <?php echo ($details['option_chauffeur'] === 'oui') ? 'Avec chauffeur' : 'Sans chauffeur'; ?></li>
                                                
                                                <?php elseif ($res['type_service'] === 'visa'): ?>
                                                    <li><i class="fa-solid fa-passport"></i> Pays : <?php echo escape($details['pays_visa']); ?></li>
                                                    <li><i class="fa-solid fa-paste"></i> Type : <?php echo escape(ucfirst($details['type_visa'])); ?></li>
                                                
                                                <?php elseif ($res['type_service'] === 'assurance'): ?>
                                                    <li><i class="fa-solid fa-shield-halved"></i> Couverture : <?php echo escape(ucfirst($details['zone_assurance'])); ?></li>
                                                
                                                <?php elseif ($res['type_service'] === 'voyage'): ?>
                                                    <li><i class="fa-solid fa-map-location-dot"></i> Circuit : <?php echo escape($details['nom_voyage'] ?? ''); ?></li>
                                                    <li><i class="fa-solid fa-users"></i> Voyageurs : <?php echo escape($details['voyageurs']); ?></li>
                                                <?php endif; ?>

                                                <?php if (!empty($details['remarques'])): ?>
                                                    <li style="margin-top:0.25rem; font-style:italic;"><i class="fa-regular fa-comment-dots"></i> "<?php echo escape($details['remarques']); ?>"</li>
                                                <?php endif; ?>

                                                <?php if (!empty($res['motif_refus'])): ?>
                                                    <li style="color:var(--danger); font-weight:700;"><i class="fa-solid fa-circle-exclamation"></i> Motif de refus : <?php echo escape($res['motif_refus']); ?></li>
                                                <?php endif; ?>
                                            </ul>
                                        </td>
                                        <td>
                                            <strong>Aller :</strong> <?php echo formatDate($res['date_debut']); ?>
                                            <?php if (!empty($res['date_fin'])): ?>
                                                <br><strong>Retour :</strong> <?php echo formatDate($res['date_fin']); ?>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <span style="font-weight: 700; color: var(--primary);"><?php echo formatPrice($res['montant']); ?></span>
                                        </td>
                                        <td>
                                            <span class="badge <?php echo $statut_class; ?>"><?php echo $statut_label; ?></span>
                                        </td>
                                        <td>
                                            <?php if ($res['statut'] === 'en_attente'): ?>
                                                <form action="<?php echo SITE_URL; ?>traitements/reservations/traitement_annulation_reservation.php" method="POST" onsubmit="return confirm('Voulez-vous vraiment annuler cette demande ?');">
                                                    <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                                                    <input type="hidden" name="reservation_id" value="<?php echo $res['id']; ?>">
                                                    <button type="submit" class="btn btn-danger btn-sm" style="padding: 0.35rem 0.75rem;"><i class="fa-solid fa-trash-can"></i></button>
                                                </form>
                                            <?php else: ?>
                                                <span style="font-size: 0.85rem; color: var(--text-light);">-</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </main>
    </div>
</div>
<?php
require_once dirname(dirname(dirname(__FILE__))) . '/templates/footer.php';
?>
