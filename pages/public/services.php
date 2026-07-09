<?php
// services.php - Page de catalogue des services
$page_title = 'AIR MAKEN - Nos Services Touristiques';
$extra_css = ['services.css'];
require_once dirname(dirname(dirname(__FILE__))) . '/templates/header.php';
require_once dirname(dirname(dirname(__FILE__))) . '/traitements/config/connexion.php';

// Récupérer les services actifs
$stmt = $pdo->query("SELECT * FROM services WHERE statut = 'actif' ORDER BY categorie, nom ASC");
$services = $stmt->fetchAll();

$categories = [
    'all'         => 'Tous les services',
    'billetterie' => 'Billetterie Aérienne',
    'hotel'       => 'Hôtels',
    'vehicule'    => 'Location de Véhicules',
    'visa'        => 'Assistance Visa',
    'assurance'   => 'Assurance Voyage',
    'voyage'      => 'Voyages Organisés',
];

$cat_icons = [
    'billetterie' => 'fa-plane-departure',
    'hotel'       => 'fa-hotel',
    'vehicule'    => 'fa-car',
    'visa'        => 'fa-passport',
    'assurance'   => 'fa-shield-halved',
    'voyage'      => 'fa-map-location-dot',
];
?>

<?php require_once dirname(dirname(dirname(__FILE__))) . '/templates/navbar.php'; ?>

<!-- En-tête de page -->
<section class="page-banner" style="background: linear-gradient(135deg, rgba(10, 25, 47, 0.9), rgba(16, 44, 87, 0.8)), url('<?php echo SITE_URL; ?>images/services-banner.jpg') no-repeat center center/cover;">
    <div class="container text-center">
        <h1 class="banner-title">Notre Catalogue de Services</h1>
        <p class="banner-subtitle">Trouvez et réservez le service adapté à vos besoins pour un voyage sans tracas</p>
    </div>
</section>

<!-- Catalogue de Services -->
<section class="services-catalogue section-padding">
    <div class="container">
        <!-- Barre de filtre par catégorie (Tabs) -->
        <div class="category-tabs">
            <?php foreach ($categories as $key => $label): ?>
                <button class="tab-btn <?php echo $key === 'all' ? 'active' : ''; ?>" data-category="<?php echo $key; ?>">
                    <?php if ($key !== 'all'): ?>
                        <i class="fa-solid <?php echo $cat_icons[$key]; ?>"></i>
                    <?php endif; ?>
                    <?php echo $label; ?>
                </button>
            <?php endforeach; ?>
        </div>

        <!-- Grille de Services -->
        <div class="services-list-grid" id="servicesGrid">
            <?php if (empty($services)): ?>
                <div class="text-center py-lg" style="grid-column: 1 / -1;">
                    <p class="text-muted">Aucun service n'est proposé pour le moment.</p>
                </div>
            <?php else: ?>
                <?php foreach ($services as $svc): ?>
                    <div class="card service-catalog-card" data-category="<?php echo escape($svc['categorie']); ?>">
                        <div class="card-badge">
                            <i class="fa-solid <?php echo $cat_icons[$svc['categorie']] ?? 'fa-cube'; ?>"></i>
                            <?php echo escape($categories[$svc['categorie']]); ?>
                        </div>
                        <div class="service-catalog-body">
                            <h3 class="service-catalog-title"><?php echo escape($svc['nom']); ?></h3>
                            <p class="service-catalog-desc"><?php echo nl2br(escape($svc['description'])); ?></p>
                        </div>
                        <div class="service-catalog-footer">
                            <div class="service-price-box">
                                <span class="price-label">Prix indicatif</span>
                                <span class="price-amount"><?php echo formatPrice($svc['prix_indicatif']); ?></span>
                            </div>
                            <a href="<?php echo SITE_URL; ?>pages/public/reservations.php?service_id=<?php echo $svc['id']; ?>&categorie=<?php echo $svc['categorie']; ?>" class="btn btn-secondary">
                                Réserver <i class="fa-solid fa-calendar-check"></i>
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- Script de filtrage dynamique JS -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const tabs = document.querySelectorAll('.tab-btn');
    const cards = document.querySelectorAll('.service-catalog-card');

    tabs.forEach(tab => {
        tab.addEventListener('click', function() {
            // Activer la bonne tab
            tabs.forEach(t => t.classList.remove('active'));
            this.classList.add('active');

            const selectedCat = this.getAttribute('data-category');

            // Filtrer les cartes
            cards.forEach(card => {
                const cardCat = card.getAttribute('data-category');
                if (selectedCat === 'all' || cardCat === selectedCat) {
                    card.style.display = 'flex';
                    card.style.opacity = '0';
                    setTimeout(() => {
                        card.style.transition = 'opacity 0.4s ease';
                        card.style.opacity = '1';
                    }, 50);
                } else {
                    card.style.display = 'none';
                }
            });
        });
    });
});
</script>

<?php require_once dirname(dirname(dirname(__FILE__))) . '/templates/footer.php'; ?>
