<?php
// gestion-messages.php - Messagerie et traitement des contacts (admin)
$admin_page_title = 'AIR MAKEN Admin - Messagerie';
require_once dirname(dirname(dirname(__FILE__))) . '/templates/header-admin.php';
requireAdmin();
require_once dirname(dirname(dirname(__FILE__))) . '/traitements/config/connexion.php';

$filtre_statut = $_GET['statut'] ?? '';
$where = [];
$params = [];

if (in_array($filtre_statut, ['non_lu', 'lu', 'traite'])) {
    $where[] = "statut = :statut";
    $params['statut'] = $filtre_statut;
}

$sql = "SELECT * FROM messages_contact"
     . (!empty($where) ? " WHERE " . implode(" AND ", $where) : "")
     . " ORDER BY date_envoi DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$messages = $stmt->fetchAll();

$csrf_token = generateCsrfToken();

$status_badges = [
    'non_lu' => ['badge-danger', 'Non lu'],
    'lu'     => ['badge-warning', 'Lu'],
    'traite' => ['badge-success', 'Traité'],
];
?>

<?php require_once dirname(dirname(dirname(__FILE__))) . '/templates/sidebar-admin.php'; ?>

<div class="admin-main">
    <?php require_once dirname(dirname(dirname(__FILE__))) . '/templates/navbar-admin.php'; ?>
    <div class="admin-content">
        <?php require_once dirname(dirname(dirname(__FILE__))) . '/templates/alertes.php'; ?>

        <div class="flex-between" style="margin-bottom:var(--spacing-md);">
            <h2 style="font-family:var(--font-title);">
                <i class="fa-solid fa-envelope-open-text" style="color:var(--secondary);"></i> Messages de contact
                <span class="badge badge-primary" style="margin-left:.5rem;"><?php echo count($messages); ?></span>
            </h2>
        </div>

        <!-- Filtrage par statut -->
        <div class="table-filter-bar" style="margin-bottom:var(--spacing-md);">
            <form method="GET" action="" style="display:flex; gap:var(--spacing-xs); align-items:center;">
                <label for="statut" class="form-label" style="margin-bottom:0;">Filtrer :</label>
                <select name="statut" id="statut" class="form-control" style="max-width:180px; padding:0.5rem 1rem;">
                    <option value="">Tous les messages</option>
                    <option value="non_lu" <?php echo ($filtre_statut === 'non_lu') ? 'selected' : ''; ?>>Non lus</option>
                    <option value="lu" <?php echo ($filtre_statut === 'lu') ? 'selected' : ''; ?>>Lus</option>
                    <option value="traite" <?php echo ($filtre_statut === 'traite') ? 'selected' : ''; ?>>Traités</option>
                </select>
                <button type="submit" class="btn btn-primary btn-sm"><i class="fa-solid fa-filter"></i></button>
                <a href="<?php echo SITE_URL; ?>pages/admin/gestion-messages.php" class="btn btn-outline btn-sm">Réinitialiser</a>
            </form>
        </div>

        <div class="detail-grid">
            <!-- Liste des messages -->
            <div class="card">
                <?php if (empty($messages)): ?>
                    <p class="text-muted">Aucun message de contact reçu.</p>
                <?php else: ?>
                    <div class="reservation-table-wrapper" style="max-height: 550px; overflow-y: auto;">
                        <table class="reservation-table">
                            <thead>
                                <tr>
                                    <th>Expéditeur</th>
                                    <th>Sujet</th>
                                    <th>Date d'envoi</th>
                                    <th>Statut</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($messages as $msg): ?>
                                    <tr class="message-row <?php echo ($msg['statut'] === 'non_lu') ? 'font-weight-bold' : ''; ?>" 
                                        style="cursor:pointer;" 
                                        onclick="viewMessage(<?php echo htmlspecialchars(json_encode($msg)); ?>)">
                                        <td>
                                            <strong><?php echo escape($msg['nom']); ?></strong><br>
                                            <small class="text-muted"><?php echo escape($msg['email']); ?></small>
                                        </td>
                                        <td><?php echo escape($msg['sujet']); ?></td>
                                        <td><?php echo formatDateTime($msg['date_envoi']); ?></td>
                                        <td>
                                            <?php [$badge, $label] = $status_badges[$msg['statut']] ?? ['badge-primary', $msg['statut']]; ?>
                                            <span class="badge <?php echo $badge; ?>"><?php echo $label; ?></span>
                                        </td>
                                        <td>
                                            <button class="btn btn-primary btn-sm" onclick="event.stopPropagation(); viewMessage(<?php echo htmlspecialchars(json_encode($msg)); ?>)">
                                                <i class="fa-solid fa-eye"></i>
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Fiche de détail du message sélectionné -->
            <div class="card" id="messageDetailsCard" style="display:none;">
                <div class="admin-card-header" style="border-bottom:1px solid var(--border-color); padding-bottom:var(--spacing-xs); margin-bottom:var(--spacing-sm);">
                    <h3 style="font-family:var(--font-title);">Détails du Message</h3>
                </div>
                
                <div style="margin-bottom:var(--spacing-md);">
                    <p><strong>De:</strong> <span id="detailNom"></span> (<span id="detailEmail" style="color:var(--primary);"></span>)</p>
                    <p><strong>Téléphone:</strong> <span id="detailTel"></span></p>
                    <p><strong>Envoyé le:</strong> <span id="detailDate"></span></p>
                    <p><strong>Sujet:</strong> <span id="detailSujet"></span></p>
                    <p><strong>Statut:</strong> <span id="detailStatut"></span></p>
                </div>

                <div style="background-color:var(--bg-main); padding:var(--spacing-sm); border-radius:var(--radius-md); font-family:var(--font-body); font-size:0.95rem; line-height:1.6; margin-bottom:var(--spacing-md); border-left:4px solid var(--secondary);">
                    <p id="detailTexte" style="white-space:pre-wrap;"></p>
                </div>

                <!-- Changement de statut / Réponse -->
                <div style="border-top:1px solid var(--border-color); padding-top:var(--spacing-sm);">
                    <div style="display:flex; gap:var(--spacing-xs); margin-bottom:var(--spacing-md);">
                        <form action="<?php echo SITE_URL; ?>traitements/contact/traitement_action_message.php" method="POST" style="display:inline;">
                            <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                            <input type="hidden" name="message_id" id="statusMsgId1" value="">
                            <input type="hidden" name="action" value="lu">
                            <button type="submit" class="btn btn-outline btn-sm"><i class="fa-solid fa-envelope-open"></i> Marquer comme Lu</button>
                        </form>
                        <form action="<?php echo SITE_URL; ?>traitements/contact/traitement_action_message.php" method="POST" style="display:inline;">
                            <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                            <input type="hidden" name="message_id" id="statusMsgId2" value="">
                            <input type="hidden" name="action" value="traite">
                            <button type="submit" class="btn btn-secondary btn-sm"><i class="fa-solid fa-circle-check"></i> Résoudre / Traité</button>
                        </form>
                    </div>

                    <form action="<?php echo SITE_URL; ?>traitements/contact/traitement_action_message.php" method="POST" onsubmit="return confirm('Envoyer cette réponse ? (Simulé)');">
                        <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                        <input type="hidden" name="message_id" id="statusMsgId3" value="">
                        <input type="hidden" name="action" value="repondre">
                        <div class="form-group">
                            <label for="reponse" class="form-label">Répondre par e-mail <small class="text-muted">(Simulation)</small></label>
                            <textarea name="reponse" id="reponse" class="form-control" rows="4" placeholder="Tapez votre réponse ici..." required></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary btn-sm"><i class="fa-solid fa-paper-plane"></i> Envoyer la réponse</button>
                    </form>
                </div>
            </div>
        </div>
    </div>


<script>
function viewMessage(msg) {
    document.getElementById('messageDetailsCard').style.display = 'block';
    document.getElementById('detailNom').innerText = msg.nom;
    document.getElementById('detailEmail').innerText = msg.email;
    document.getElementById('detailTel').innerText = msg.telephone ? msg.telephone : '-';
    document.getElementById('detailDate').innerText = msg.date_envoi;
    document.getElementById('detailSujet').innerText = msg.sujet;
    document.getElementById('detailTexte').innerText = msg.message;

    // Statut
    var statutHtml = '';
    if (msg.statut === 'non_lu') {
        statutHtml = '<span class="badge badge-danger">Non lu</span>';
    } else if (msg.statut === 'lu') {
        statutHtml = '<span class="badge badge-warning">Lu</span>';
    } else {
        statutHtml = '<span class="badge badge-success">Traité</span>';
    }
    document.getElementById('detailStatut').innerHTML = statutHtml;

    // IDs formulaires
    document.getElementById('statusMsgId1').value = msg.id;
    document.getElementById('statusMsgId2').value = msg.id;
    document.getElementById('statusMsgId3').value = msg.id;
}
</script>
<?php require_once dirname(dirname(dirname(__FILE__))) . '/templates/footer-admin.php'; ?>
