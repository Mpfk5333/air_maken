<?php
// accueil.php - Page d'accueil publique
$page_title = 'AIR MAKEN - Votre destination commence avec nous';
$extra_css = ['accueil.css'];
require_once dirname(dirname(dirname(__FILE__))) . '/templates/header.php';
require_once dirname(dirname(dirname(__FILE__))) . '/traitements/config/connexion.php';

// Récupération des contenus pour l'accueil
$stmt = $pdo->prepare("SELECT section, contenu, image FROM contenus WHERE page = 'accueil'");
$stmt->execute();
$db_contents = $stmt->fetchAll(PDO::FETCH_ASSOC);

$contents = [];
foreach ($db_contents as $row) {
    $contents[$row['section']] = [
        'text' => $row['contenu'],
        'image' => $row['image']
    ];
}

// Valeurs par défaut si non trouvé
$hero_title = $contents['hero_title']['text'] ?? 'Votre destination commence avec nous';
$hero_subtitle = $contents['hero_subtitle']['text'] ?? 'Voyagez l\'esprit léger avec AIR MAKEN, votre agence de voyage de confiance.';
$presentation = $contents['presentation']['text'] ?? 'AIR MAKEN est une agence de voyages et de services touristiques basée en Guinée Équatoriale...';
$presentation_img = $contents['presentation']['image'] ?? 'about-preview.jpg';

// Récupérer les stats
$stats_json = $contents['stats']['text'] ?? '{"clients": "15k+", "experience": "10+", "destinations": "50+", "support": "24/7"}';
$stats = json_decode($stats_json, true) ?: [
    'clients' => '15k+',
    'experience' => '10+',
    'destinations' => '50+',
    'support' => '24/7'
];

// Récupérer 3 services actifs phares
$services_stmt = $pdo->query("SELECT * FROM services WHERE statut = 'actif' LIMIT 3");
$services = $services_stmt->fetchAll();

// Mapping des icônes de catégorie
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

<!-- Section Hero -->
<section class="hero-section" style="background: linear-gradient(135deg, rgba(10, 25, 47, 0.95), rgba(16, 44, 87, 0.85)), url('<?php echo SITE_URL; ?>images/<?php echo escape($contents['hero_title']['image'] ?? 'hero-bg.jpg'); ?>') no-repeat center center/cover;">
    <div class="container hero-container">
        <div class="hero-content">
            <h1 class="hero-title"><?php echo escape($hero_title); ?></h1>
            <p class="hero-subtitle"><?php echo escape($hero_subtitle); ?></p>
            <div class="hero-actions">
                <a href="<?php echo SITE_URL; ?>pages/public/reservations.php" class="btn btn-secondary btn-lg">
                    <i class="fa-solid fa-calendar-days"></i> Réserver un service
                </a>
                <a href="<?php echo SITE_URL; ?>pages/public/services.php" class="btn btn-outline btn-lg" style="border-color:white; color:white;">
                    <i class="fa-solid fa-circle-info"></i> Découvrir nos services
                </a>
            </div>
        </div>
    </div>
</section>

<!-- Section Stats -->
<section class="stats-section">
    <div class="container stats-grid">
        <div class="stat-card">
            <div class="stat-icon"><i class="fa-solid fa-users-viewfinder"></i></div>
            <h3 class="stat-number"><?php echo escape($stats['clients']); ?></h3>
            <p class="stat-label">Clients Satisfaits</p>
        </div>
        <div class="stat-card">
            <div class="stat-icon"><i class="fa-solid fa-award"></i></div>
            <h3 class="stat-number"><?php echo escape($stats['experience']); ?> ans</h3>
            <p class="stat-label">D'Expérience</p>
        </div>
        <div class="stat-card">
            <div class="stat-icon"><i class="fa-solid fa-earth-africa"></i></div>
            <h3 class="stat-number"><?php echo escape($stats['destinations']); ?></h3>
            <p class="stat-label">Destinations Globales</p>
        </div>
        <div class="stat-card">
            <div class="stat-icon"><i class="fa-solid fa-headset"></i></div>
            <h3 class="stat-number"><?php echo escape($stats['support']); ?></h3>
            <p class="stat-label">Support Client</p>
        </div>
    </div>
</section>

<!-- Section Présentation -->
<section class="presentation-section section-padding">
    <div class="container presentation-grid">
        <div class="presentation-text-block">
            <span class="section-tag">Qui sommes-nous ?</span>
            <h2 class="section-title">AIR MAKEN à vos côtés</h2>
            <p class="presentation-desc"><?php echo nl2br(escape($presentation)); ?></p>
            <div style="margin-top:var(--spacing-md);">
                <a href="<?php echo SITE_URL; ?>pages/public/apropos.php" class="btn btn-primary">
                    En savoir plus <i class="fa-solid fa-arrow-right"></i>
                </a>
            </div>
        </div>
        <div class="presentation-image-block">
            <div class="image-wrapper">
                <img src="<?php echo SITE_URL; ?>images/<?php echo escape($presentation_img); ?>" alt="Présentation AIR MAKEN" class="img-responsive presentation-img">
                <div class="image-overlay-box">
                    <i class="fa-solid fa-plane-up"></i>
                    <span>Depuis 2016</span>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Section Services à la Une -->
<section class="featured-services-section section-padding bg-light">
    <div class="container">
        <div class="text-center" style="margin-bottom:var(--spacing-lg);">
            <span class="section-tag">Explorez</span>
            <h2 class="section-title">Services Phares</h2>
            <p class="section-subtitle text-muted">Des solutions sur mesure pour vos déplacements nationaux et internationaux</p>
        </div>

        <div class="services-grid">
            <?php if (empty($services)): ?>
                <p class="text-center text-muted">Aucun service disponible pour le moment.</p>
            <?php else: ?>
                <?php foreach ($services as $svc): ?>
                    <div class="card service-card">
                        <div class="service-card-icon-wrapper">
                            <i class="fa-solid <?php echo $cat_icons[$svc['categorie']] ?? 'fa-cube'; ?> service-icon"></i>
                        </div>
                        <h3 class="service-card-title"><?php echo escape($svc['nom']); ?></h3>
                        <p class="service-card-desc"><?php echo escape(mb_strimwidth($svc['description'], 0, 120, '…')); ?></p>
                        <div class="service-card-footer">
                            <span class="service-card-price"><?php echo formatPrice($svc['prix_indicatif']); ?></span>
                            <a href="<?php echo SITE_URL; ?>pages/public/reservations.php?service_id=<?php echo $svc['id']; ?>" class="btn btn-secondary btn-sm">
                                Réserver <i class="fa-solid fa-angle-right"></i>
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        <div class="text-center" style="margin-top:var(--spacing-lg);">
            <a href="<?php echo SITE_URL; ?>pages/public/services.php" class="btn btn-outline">
                Voir tout le catalogue <i class="fa-solid fa-list"></i>
            </a>
        </div>
    </div>
</section>

<!-- Section Réassurance / Témoignages -->
<section class="reassurance-section section-padding">
    <div class="container">
        <div class="text-center" style="margin-bottom:var(--spacing-lg);">
            <span class="section-tag">Pourquoi nous faire confiance ?</span>
            <h2 class="section-title">La différence AIR MAKEN</h2>
        </div>

        <div class="reassurance-grid">
            <div class="reassurance-card">
                <div class="reassurance-icon"><i class="fa-solid fa-user-tie"></i></div>
                <h4>Conseillers Experts</h4>
                <p>Une équipe qualifiée et passionnée disponible à tout moment pour planifier vos itinéraires.</p>
            </div>
            <div class="reassurance-card">
                <div class="reassurance-icon"><i class="fa-solid fa-clock-rotate-left"></i></div>
                <h4>Disponibilité 24/7</h4>
                <p>Une assistance de tous les instants lors de vos déplacements nationaux ou internationaux.</p>
            </div>
            <div class="reassurance-card">
                <div class="reassurance-icon"><i class="fa-solid fa-handshake"></i></div>
                <h4>Partenaires Mondiaux</h4>
                <p>Des accords exclusifs avec les compagnies aériennes et groupes hôteliers mondiaux.</p>
            </div>
        </div>
    </div>
</section>

<?php require_once dirname(dirname(dirname(__FILE__))) . '/templates/footer.php'; ?>
