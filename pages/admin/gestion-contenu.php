<?php
// gestion-contenu.php - Édition dynamique des pages Accueil / À propos (admin)
$admin_page_title = 'AIR MAKEN Admin - Gestion du Contenu';
require_once dirname(dirname(dirname(__FILE__))) . '/templates/header-admin.php';
requireAdmin();
require_once dirname(dirname(dirname(__FILE__))) . '/traitements/config/connexion.php';

// Récupérer tout le contenu
$stmt = $pdo->query("SELECT * FROM contenus");
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

$contents = [];
foreach ($rows as $row) {
    $contents[$row['page']][$row['section']] = $row['contenu'];
}

// Extraction des statistiques depuis le JSON
$stats_json = $contents['accueil']['stats'] ?? '{"clients": "15k+", "experience": "10+", "destinations": "50+", "support": "24/7"}';
$stats = json_decode($stats_json, true) ?: [
    'clients' => '15k+',
    'experience' => '10+',
    'destinations' => '50+',
    'support' => '24/7'
];

$csrf_token = generateCsrfToken();
?>

<?php require_once dirname(dirname(dirname(__FILE__))) . '/templates/sidebar-admin.php'; ?>

<div class="admin-main">
    <?php require_once dirname(dirname(dirname(__FILE__))) . '/templates/navbar-admin.php'; ?>
    <div class="admin-content">
        <?php require_once dirname(dirname(dirname(__FILE__))) . '/templates/alertes.php'; ?>

        <div class="flex-between" style="margin-bottom:var(--spacing-md);">
            <h2 style="font-family:var(--font-title);">
                <i class="fa-solid fa-file-pen" style="color:var(--secondary);"></i> Édition dynamique du site
            </h2>
        </div>

        <!-- Système d'onglets pour séparer l'accueil et le à propos -->
        <div class="card" style="max-width:900px;">
            <div class="admin-tabs" style="display:flex; gap:1rem; border-bottom:2px solid var(--border-color); margin-bottom:var(--spacing-md); padding-bottom:var(--spacing-xs);">
                <button class="tab-link active" onclick="openTab(event, 'tabAccueil')" style="background:none; border:none; padding:0.5rem 1rem; font-weight:700; cursor:pointer; color:var(--primary); font-family:var(--font-title); font-size:1.05rem;">Page Accueil</button>
                <button class="tab-link" onclick="openTab(event, 'tabApropos')" style="background:none; border:none; padding:0.5rem 1rem; font-weight:700; cursor:pointer; color:var(--text-muted); font-family:var(--font-title); font-size:1.05rem;">Page À propos</button>
            </div>

            <!-- Formulaire Global -->
            <form action="<?php echo SITE_URL; ?>traitements/admin/traitement_gestion_contenu.php" method="POST">
                <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">

                <!-- CONTENU ACCUEIL -->
                <div id="tabAccueil" class="tab-content-block">
                    <h3 style="margin-bottom:var(--spacing-sm); color:var(--primary);">Section Bannière Hero</h3>
                    
                    <div class="form-group">
                        <label for="hero_title" class="form-label">Titre Principal (Hero)</label>
                        <input type="text" name="hero_title" id="hero_title" class="form-control" 
                               value="<?php echo escape($contents['accueil']['hero_title'] ?? ''); ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="hero_subtitle" class="form-label">Sous-titre (Hero)</label>
                        <textarea name="hero_subtitle" id="hero_subtitle" class="form-control" rows="3" required><?php echo escape($contents['accueil']['hero_subtitle'] ?? ''); ?></textarea>
                    </div>

                    <h3 style="margin-top:var(--spacing-lg); margin-bottom:var(--spacing-sm); color:var(--primary);">Section Présentation</h3>

                    <div class="form-group">
                        <label for="presentation" class="form-label">Texte de Présentation</label>
                        <textarea name="presentation" id="presentation" class="form-control" rows="6" required><?php echo escape($contents['accueil']['presentation'] ?? ''); ?></textarea>
                    </div>

                    <h3 style="margin-top:var(--spacing-lg); margin-bottom:var(--spacing-sm); color:var(--primary);">Chiffres clés (Statistiques)</h3>
                    <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap:var(--spacing-sm);">
                        <div class="form-group">
                            <label for="stat_clients" class="form-label">Clients Satisfaits</label>
                            <input type="text" name="stat_clients" id="stat_clients" class="form-control" value="<?php echo escape($stats['clients'] ?? ''); ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="stat_experience" class="form-label">Années d'expérience</label>
                            <input type="text" name="stat_experience" id="stat_experience" class="form-control" value="<?php echo escape($stats['experience'] ?? ''); ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="stat_destinations" class="form-label">Destinations globales</label>
                            <input type="text" name="stat_destinations" id="stat_destinations" class="form-control" value="<?php echo escape($stats['destinations'] ?? ''); ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="stat_support" class="form-label">Disponibilité Support</label>
                            <input type="text" name="stat_support" id="stat_support" class="form-control" value="<?php echo escape($stats['support'] ?? ''); ?>" required>
                        </div>
                    </div>
                </div>

                <!-- CONTENU À PROPOS -->
                <div id="tabApropos" class="tab-content-block" style="display:none;">
                    <h3 style="margin-bottom:var(--spacing-sm); color:var(--primary);">Section Historique</h3>

                    <div class="form-group">
                        <label for="histoire" class="form-label">Notre Histoire</label>
                        <textarea name="histoire" id="histoire" class="form-control" rows="6" required><?php echo escape($contents['apropos']['histoire'] ?? ''); ?></textarea>
                    </div>

                    <h3 style="margin-top:var(--spacing-lg); margin-bottom:var(--spacing-sm); color:var(--primary);">Mission & Valeurs</h3>

                    <div class="form-group">
                        <label for="mission" class="form-label">Notre Mission</label>
                        <textarea name="mission" id="mission" class="form-control" rows="4" required><?php echo escape($contents['apropos']['mission'] ?? ''); ?></textarea>
                    </div>

                    <div class="form-group">
                        <label for="valeurs" class="form-label">Nos Valeurs</label>
                        <textarea name="valeurs" id="valeurs" class="form-control" rows="4" required><?php echo escape($contents['apropos']['valeurs'] ?? ''); ?></textarea>
                    </div>
                </div>

                <div style="margin-top:var(--spacing-lg); border-top:1px solid var(--border-color); padding-top:var(--spacing-md);">
                    <button type="submit" class="btn btn-secondary btn-lg"><i class="fa-solid fa-floppy-disk"></i> Enregistrer les modifications</button>
                </div>
            </form>
        </div>
    </div>


<script>
function openTab(evt, tabName) {
    var i, tabContent, tabLinks;
    tabContent = document.getElementsByClassName("tab-content-block");
    for (i = 0; i < tabContent.length; i++) {
        tabContent[i].style.display = "none";
    }
    tabLinks = document.getElementsByClassName("tab-link");
    for (i = 0; i < tabLinks.length; i++) {
        tabLinks[i].classList.remove("active");
        tabLinks[i].style.color = "var(--text-muted)";
    }
    document.getElementById(tabName).style.display = "block";
    evt.currentTarget.classList.add("active");
    evt.currentTarget.style.color = "var(--primary)";
}
</script>

<?php require_once dirname(dirname(dirname(__FILE__))) . '/templates/footer-admin.php'; ?>
