<?php
require_once __DIR__ . '/config/db.php';

/** Transforme "Système d'exploitation" en "systeme-d-exploitation" pour data-matiere */
function slugify(string $text): string
{
    $text = iconv('UTF-8', 'ASCII//TRANSLIT', $text) ?: $text;
    $text = strtolower($text);
    $text = preg_replace('/[^a-z0-9]+/', '-', $text);
    return trim($text, '-');
}

// 1. Récupération des matières (pour les chips de filtre)
$matieres = $pdo->query('SELECT id_mat, nom FROM matiere ORDER BY nom')->fetchAll();

// 2. Récupération des documents avec le nom de leur matière (jointure)
$documents = $pdo->query(
    'SELECT d.id_doc, d.titre, d.type, d.chemin_ou_url, d.compteur_vues, m.nom AS matiere_nom
     FROM document d
     JOIN matiere m ON m.id_mat = d.id_mat
     ORDER BY d.id_doc DESC'
)->fetchAll();

// 3. Petites aides d'affichage par type de ressource
$typeIcons = ['Cours' => '📄', 'TD' => '✏️', 'TP' => '🖥️', 'Video' => '▶'];
$typeTagClass = ['Cours' => 'type-tag--cours', 'TD' => 'type-tag--td', 'TP' => 'type-tag--tp', 'Video' => 'type-tag--video'];
$pageTitle = 'DevShareCampus';
require __DIR__ . '/partials/header.php';
?>
        <section class="hero">
            <p class="hero-eyebrow">Message à la nouvelle promo</p>
            <h2 class="hero-title">Bienvenue, futurs ingénieurs.</h2>
            <p class="hero-text">
                Tous les cours, TD, TP et vidéos de l'année précédente, structurés par matière.
                Consultez, téléchargez, révisez — au même endroit.
            </p>

            <div class="hero-actions">
                <button class="btn btn-primary">Explorer les matières</button>
                <button class="btn btn-secondary">Voir les nouveautés</button>
            </div>
        </section>

        <section class="bibliotheque">
            <p class="section-label">Bibliothèque</p>
        </section>

        <section class="matieres">
            <h2 class="section-title">Matières du semestre</h2>
            <p class="section-text">Cliquez pour filtrer les ressources ci-dessous.</p>

            <div class="matiere-list">
                <?php if (empty($matieres)): ?>
                    <p class="section-text">Aucune matière enregistrée pour le moment.</p>
                <?php else: ?>
                    <?php foreach ($matieres as $matiere): ?>
                        <button class="matiere-chip" data-matiere="<?= slugify($matiere['nom']) ?>">
                            <?= htmlspecialchars($matiere['nom']) ?>
                        </button>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </section>

        <section class="ressources">
            <div class="search-bar">
                <img class="search-icon" src="" alt="">
                <label for="search-input" class="sr-only">Rechercher une ressource</label>
                <input id="search-input" class="search-input" type="text" placeholder="Rechercher par mot-clé, matière...">
            </div>

            <div class="filter-bar">
                <button class="filter-chip filter-chip--active" data-type="tous">Tout</button>
                <button class="filter-chip" data-type="Cours">Cours</button>
                <button class="filter-chip" data-type="TD">TD</button>
                <button class="filter-chip" data-type="TP">TP</button>
                <button class="filter-chip" data-type="Video">Vidéo YouTube</button>
            </div>

            <div class="resource-grid">
                <?php foreach ($documents as $doc): ?>
                    <?php
                        $isVideo   = $doc['type'] === 'Video';
                        $isYoutube = $isVideo && str_contains($doc['chemin_ou_url'], 'youtube');
                        $tagLabel  = $isVideo ? ($isYoutube ? 'Vidéo YouTube' : 'Vidéo locale') : $doc['type'];
                        $action    = $isVideo ? 'Voir' : 'Ouvrir';
                    ?>
                    <article class="resource-card"
                             data-type="<?= htmlspecialchars($doc['type']) ?>"
                             data-matiere="<?= slugify($doc['matiere_nom']) ?>"
                             data-title="<?= htmlspecialchars(mb_strtolower($doc['titre'])) ?>">
                        <div class="resource-card__top">
                            <span class="resource-icon" aria-hidden="true"><?= $typeIcons[$doc['type']] ?? '📄' ?></span>
                            <span class="type-tag <?= $typeTagClass[$doc['type']] ?? 'type-tag--cours' ?>">
                                <?= htmlspecialchars($tagLabel) ?>
                            </span>
                        </div>
                        <h3 class="resource-title"><?= htmlspecialchars($doc['titre']) ?></h3>
                        <p class="resource-path"><?= htmlspecialchars($doc['chemin_ou_url']) ?></p>
                        <div class="resource-meta">
                            <span class="resource-matiere"><?= htmlspecialchars($doc['matiere_nom']) ?></span>
                            <a class="resource-action" href="<?= htmlspecialchars($doc['chemin_ou_url']) ?>" target="_blank" rel="noopener">
                                <?= $action ?> →
                            </a>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>

            <p class="resource-empty" <?= empty($documents) ? '' : 'hidden' ?>>
                Aucune ressource n'est disponible pour le moment.
            </p>
        </section>
<?php require __DIR__ . '/partials/footer.php'; ?>
