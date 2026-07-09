<?php
// alertes.php - Bloc générique d'affichage des messages (succès/erreur)
require_once dirname(dirname(__FILE__)) . '/traitements/fonctions/fonctions_utils.php';
require_once dirname(dirname(__FILE__)) . '/traitements/fonctions/fonctions_securite.php';

$alert_types = ['success', 'danger', 'warning', 'info'];

foreach ($alert_types as $type) {
    $msg = getFlashMessage($type);
    if ($msg) {
        $icon = 'fa-circle-info';
        if ($type === 'success') {
            $icon = 'fa-circle-check';
        } elseif ($type === 'danger') {
            $icon = 'fa-circle-xmark';
        } elseif ($type === 'warning') {
            $icon = 'fa-triangle-exclamation';
        }
        
        echo '<div class="alert alert-' . $type . ' flex-align-center" role="alert">';
        echo '  <i class="fa-solid ' . $icon . ' alert-icon"></i>';
        echo '  <span class="alert-message">' . escape($msg) . '</span>';
        echo '</div>';
    }
}
?>
