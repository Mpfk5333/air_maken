<?php
// apropos.php - Page à propos de l'agence
$page_title = 'À propos d\'AIR MAKEN - Votre partenaire voyage';
$extra_css = ['apropos.css'];
require_once dirname(dirname(dirname(__FILE__))) . '/templates/header.php';
require_once dirname(dirname(dirname(__FILE__))) . '/traitements/config/connexion.php';

// Récupération des contenus pour À propos
$stmt = $pdo->prepare("SELECT section, contenu, image FROM contenus WHERE page = 'apropos'");
$stmt->execute();
$db_contents = $stmt->fetchAll(PDO::FETCH_ASSOC);

$contents = [];
foreach ($db_contents as $row) {
    $contents[$row['section']] = [
        'text' => $row['contenu'],
        'image' => $row['image']
    ];
}

$histoire = $contents['histoire']['text'] ?? 'AIR MAKEN est née d\'une volonté de simplifier le voyage en Guinée Équatoriale...';
$histoire_img = $contents['histoire']['image'] ?? 'histoire.jpg';
$mission = $contents['mission']['text'] ?? 'Offrir des services d\'excellence alliant confort et tarifs compétitifs...';
$valeurs = $contents['valeurs']['text'] ?? 'Professionnalisme, Réactivité, Écoute client, Excellence.';
?>

<?php require_once dirname(dirname(dirname(__FILE__))) . '/templates/navbar.php'; ?>

<!-- Bannière d'en-tête de page -->
<section class="page-banner" style="background: linear-gradient(135deg, rgba(10, 25, 47, 0.9), rgba(16, 44, 87, 0.8)), url('<?php echo SITE_URL; ?>images/about-banner.jpg') no-repeat center center/cover;">
    <div class="container text-center">
        <h1 class="banner-title">À propos de nous</h1>
        <p class="banner-subtitle">Découvrez l'histoire, la mission et les valeurs de l'agence de voyages de référence</p>
    </div>
</section>

<!-- Section Notre Histoire -->
<section class="about-section section-padding">
    <div class="container about-grid">
        <div class="about-image-block">
            <div class="image-wrapper">
                <img src="<?php echo SITE_URL; ?>images/<?php echo escape($histoire_img); ?>" alt="Notre Histoire AIR MAKEN" class="img-responsive about-img">
            </div>
        </div>
        <div class="about-text-block">
            <span class="section-tag">Notre parcours</span>
            <h2 class="section-title">Notre Histoire</h2>
            <div class="about-desc"><?php echo nl2br(escape($histoire)); ?></div>
        </div>
    </div>
</section>

<!-- Section Mission & Valeurs (Deux Colonnes Modernes) -->
<section class="mission-valeurs-section section-padding bg-light">
    <div class="container mv-grid">
        <div class="mv-card card">
            <div class="mv-icon-wrapper"><i class="fa-solid fa-bullseye"></i></div>
            <h3 class="mv-card-title">Notre Mission</h3>
            <p class="mv-card-text"><?php echo nl2br(escape($mission)); ?></p>
        </div>

        <div class="mv-card card">
            <div class="mv-icon-wrapper"><i class="fa-solid fa-heart"></i></div>
            <h3 class="mv-card-title">Nos Valeurs</h3>
            <p class="mv-card-text"><?php echo nl2br(escape($valeurs)); ?></p>
        </div>
    </div>
</section>

<!-- Section Pourquoi AIR MAKEN? -->
<section class="advantages-section section-padding">
    <div class="container">
        <div class="text-center" style="margin-bottom:var(--spacing-lg);">
            <span class="section-tag">Avantages</span>
            <h2 class="section-title">Pourquoi choisir AIR MAKEN ?</h2>
        </div>

        <div class="advantages-grid">
            <div class="advantage-item">
                <div class="advantage-header">
                    <span class="advantage-num">01</span>
                    <h4>Expertise Locale & Globale</h4>
                </div>
                <p>Une parfaite connaissance du terrain en Guinée Équatoriale associée à des connexions internationales solides.</p>
            </div>
            <div class="advantage-item">
                <div class="advantage-header">
                    <span class="advantage-num">02</span>
                    <h4>Accompagnement Sur Mesure</h4>
                </div>
                <p>Chaque demande est traitée individuellement pour répondre précisément à vos besoins d'itinéraire et de budget.</p>
            </div>
            <div class="advantage-item">
                <div class="advantage-header">
                    <span class="advantage-num">03</span>
                    <h4>Transparence & Fiabilité</h4>
                </div>
                <p>Aucun frais caché. Nos tarifs sont clairs et nous tenons nos promesses pour chaque réservation effectuée.</p>
            </div>
        </div>
    </div>
</section>

<?php require_once dirname(dirname(dirname(__FILE__))) . '/templates/footer.php'; ?>
