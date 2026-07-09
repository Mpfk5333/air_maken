<?php
// footer.php - Pied de page public
require_once dirname(dirname(__FILE__)) . '/traitements/config/constantes.php';
?>
<footer class="main-footer">
    <div class="container footer-grid">
        <!-- Colonne 1: À propos -->
        <div class="footer-col">
            <a href="<?php echo SITE_URL; ?>index.php" class="footer-logo">
                <i class="fa-solid fa-plane-departure logo-icon"></i>
                <span class="logo-text">AIR<span class="logo-highlight">MAKEN</span></span>
            </a>
            <p class="footer-desc">Votre agence de voyages et de services touristiques basée en Guinée Équatoriale. Votre voyage commence avec nous.</p>
            <div class="footer-socials">
                <a href="#" class="social-link"><i class="fa-brands fa-facebook-f"></i></a>
                <a href="#" class="social-link"><i class="fa-brands fa-instagram"></i></a>
                <a href="#" class="social-link"><i class="fa-brands fa-twitter"></i></a>
                <a href="#" class="social-link"><i class="fa-brands fa-linkedin-in"></i></a>
            </div>
        </div>
        
        <!-- Colonne 2: Services -->
        <div class="footer-col">
            <h4 class="footer-title">Nos Services</h4>
            <ul class="footer-links">
                <li><a href="<?php echo SITE_URL; ?>pages/public/services.php"><i class="fa-solid fa-chevron-right link-arrow"></i> Billetterie Aérienne</a></li>
                <li><a href="<?php echo SITE_URL; ?>pages/public/services.php"><i class="fa-solid fa-chevron-right link-arrow"></i> Assistance Visa</a></li>
                <li><a href="<?php echo SITE_URL; ?>pages/public/services.php"><i class="fa-solid fa-chevron-right link-arrow"></i> Réservation d'Hôtels</a></li>
                <li><a href="<?php echo SITE_URL; ?>pages/public/services.php"><i class="fa-solid fa-chevron-right link-arrow"></i> Location de Véhicules</a></li>
            </ul>
        </div>
        
        <!-- Colonne 3: Liens Rapides -->
        <div class="footer-col">
            <h4 class="footer-title">Liens Rapides</h4>
            <ul class="footer-links">
                <li><a href="<?php echo SITE_URL; ?>pages/public/accueil.php"><i class="fa-solid fa-chevron-right link-arrow"></i> Accueil</a></li>
                <li><a href="<?php echo SITE_URL; ?>pages/public/reservations.php"><i class="fa-solid fa-chevron-right link-arrow"></i> Réservations</a></li>
                <li><a href="<?php echo SITE_URL; ?>pages/public/apropos.php"><i class="fa-solid fa-chevron-right link-arrow"></i> À propos</a></li>
                <li><a href="<?php echo SITE_URL; ?>pages/public/contact.php"><i class="fa-solid fa-chevron-right link-arrow"></i> Contact</a></li>
            </ul>
        </div>
        
        <!-- Colonne 4: Contact -->
        <div class="footer-col">
            <h4 class="footer-title">Coordonnées</h4>
            <ul class="footer-contact">
                <li><i class="fa-solid fa-location-dot contact-icon"></i> Malabo, Guinée Équatoriale</li>
                <li><i class="fa-solid fa-phone contact-icon"></i> +240 333 444 555</li>
                <li><i class="fa-solid fa-envelope contact-icon"></i> contact@airmaken.com</li>
                <li><i class="fa-solid fa-clock contact-icon"></i> Lun - Ven : 8h00 - 17h00</li>
            </ul>
        </div>
    </div>
    
    <!-- Bas de page -->
    <div class="footer-bottom">
        <div class="container flex-between footer-bottom-container">
            <p class="copy-text">&copy; <?php echo date('Y'); ?> <strong>AIR MAKEN</strong>. Tous droits réservés.</p>
            <p class="dev-text">Votre destination commence avec nous.</p>
        </div>
    </div>
</footer>

<!-- JS Common Files -->
<script src="<?php echo SITE_URL; ?>js/main.js"></script>
<script src="<?php echo SITE_URL; ?>js/validation.js"></script>

<!-- Page Specific JS -->
<?php if (isset($extra_js) && is_array($extra_js)): ?>
    <?php foreach ($extra_js as $js_file): ?>
        <script src="<?php echo SITE_URL; ?>js/<?php echo $js_file; ?>?v=<?php echo time(); ?>"></script>
    <?php endforeach; ?>
<?php endif; ?>

</body>
</html>
