<?php
// footer-admin.php - Pied de page back-office
?>
</div><!-- end .admin-main -->
</div><!-- end .admin-layout -->

<script src="<?php echo SITE_URL; ?>js/main.js"></script>
<?php if (isset($extra_js_admin) && is_array($extra_js_admin)): ?>
    <?php foreach ($extra_js_admin as $js_file): ?>
        <script src="<?php echo SITE_URL; ?>js/admin/<?php echo $js_file; ?>?v=<?php echo time(); ?>"></script>
    <?php endforeach; ?>
<?php endif; ?>
</body>
</html>
