<?php
// contact.php - Formulaire de contact et informations d'accès
$page_title = 'AIR MAKEN - Contactez-nous';
$extra_css = ['contact.css'];
require_once dirname(dirname(dirname(__FILE__))) . '/templates/header.php';
require_once dirname(dirname(dirname(__FILE__))) . '/traitements/config/connexion.php';

$user_nom = '';
$user_email = '';
$user_phone = '';

if (isLoggedIn()) {
    $stmt = $pdo->prepare("SELECT nom, prenom, email, telephone FROM utilisateurs WHERE id = :id");
    $stmt->execute(['id' => $_SESSION['user_id']]);
    $u = $stmt->fetch();
    if ($u) {
        $user_nom = $u['prenom'] . ' ' . $u['nom'];
        $user_email = $u['email'];
        $user_phone = $u['telephone'];
    }
}

$csrf_token = generateCsrfToken();
?>

<?php require_once dirname(dirname(dirname(__FILE__))) . '/templates/navbar.php'; ?>

<!-- En-tête de page -->
<section class="page-banner" style="background: linear-gradient(135deg, rgba(10, 25, 47, 0.9), rgba(16, 44, 87, 0.8)), url('<?php echo SITE_URL; ?>images/contact-banner.jpg') no-repeat center center/cover;">
    <div class="container text-center">
        <h1 class="banner-title">Contactez-nous</h1>
        <p class="banner-subtitle">Une question ? Une demande spécifique ? Notre équipe est à votre écoute 24/7</p>
    </div>
</section>

<!-- Section Contact -->
<section class="contact-section section-padding">
    <div class="container contact-grid">
        <!-- Informations & Carte -->
        <div class="contact-info-block">
            <h2 class="section-title">Nos Coordonnées</h2>
            <p class="text-muted" style="margin-bottom:var(--spacing-md);">N'hésitez pas à nous rendre visite ou à nous appeler directement.</p>

            <div class="info-items">
                <div class="info-item">
                    <div class="info-icon"><i class="fa-solid fa-location-dot"></i></div>
                    <div>
                        <h4>Adresse Physique</h4>
                        <p>Avenida de la Independencia, Malabo, Guinée Équatoriale</p>
                    </div>
                </div>
                <div class="info-item">
                    <div class="info-icon"><i class="fa-solid fa-phone"></i></div>
                    <div>
                        <h4>Téléphones</h4>
                        <p>+240 333 444 555<br>+240 222 555 888</p>
                    </div>
                </div>
                <div class="info-item">
                    <div class="info-icon"><i class="fa-solid fa-envelope"></i></div>
                    <div>
                        <h4>E-mail</h4>
                        <p>contact@airmaken.com<br>support@airmaken.com</p>
                    </div>
                </div>
                <div class="info-item">
                    <div class="info-icon"><i class="fa-solid fa-clock"></i></div>
                    <div>
                        <h4>Horaires d'ouverture</h4>
                        <p>Lundi - Vendredi : 8h00 - 17h00<br>Samedi : 9h00 - 13h00</p>
                    </div>
                </div>
            </div>

            <!-- Faux Map interactif premium avec CSS -->
            <div class="mock-map-card">
                <div class="map-overlay">
                    <i class="fa-solid fa-location-crosshairs map-pin-pulse"></i>
                    <span class="map-label">Siège AIR MAKEN - Malabo</span>
                </div>
            </div>
        </div>

        <!-- Formulaire de Contact -->
        <div class="contact-form-block card">
            <h2 class="section-title" style="margin-bottom:var(--spacing-xs);">Envoyez un message</h2>
            <p class="text-muted" style="margin-bottom:var(--spacing-md);">Remplissez le formulaire ci-dessous et nous vous répondrons dans les plus brefs délais.</p>

            <?php require_once dirname(dirname(dirname(__FILE__))) . '/templates/alertes.php'; ?>

            <form action="<?php echo SITE_URL; ?>traitements/contact/traitement_envoi_message.php" method="POST" id="contactForm">
                <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">

                <div class="form-group">
                    <label for="nom" class="form-label">Nom Complet <span style="color:var(--danger);">*</span></label>
                    <input type="text" name="nom" id="nom" class="form-control" placeholder="Votre nom" value="<?php echo escape($user_nom); ?>" required>
                </div>

                <div class="form-group">
                    <label for="email" class="form-label">Adresse E-mail <span style="color:var(--danger);">*</span></label>
                    <input type="email" name="email" id="email" class="form-control" placeholder="votre.email@exemple.com" value="<?php echo escape($user_email); ?>" required>
                </div>

                <div class="form-group">
                    <label for="telephone" class="form-label">Téléphone</label>
                    <input type="text" name="telephone" id="telephone" class="form-control" placeholder="Ex: +240 333 444 555" value="<?php echo escape($user_phone); ?>">
                </div>

                <div class="form-group">
                    <label for="sujet" class="form-label">Sujet <span style="color:var(--danger);">*</span></label>
                    <input type="text" name="sujet" id="sujet" class="form-control" placeholder="Objet de votre message" required>
                </div>

                <div class="form-group">
                    <label for="message" class="form-label">Message <span style="color:var(--danger);">*</span></label>
                    <textarea name="message" id="message" class="form-control" rows="5" placeholder="Saisissez votre message ici..." required></textarea>
                </div>

                <button type="submit" class="btn btn-primary btn-lg" style="width:100%; margin-top:var(--spacing-sm);">
                    <i class="fa-solid fa-paper-plane"></i> Envoyer le message
                </button>
            </form>
        </div>
    </div>
</section>

<?php require_once dirname(dirname(dirname(__FILE__))) . '/templates/footer.php'; ?>
