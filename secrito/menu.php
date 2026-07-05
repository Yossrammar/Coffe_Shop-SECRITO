<?php
require_once 'config/config.php';
$page      = 'menu';
$pageTitle = 'Menu';

// Récupération des produits depuis la BD
$produits   = [];
$categories = ['brunch', 'sucree', 'sale', 'fresh'];

try {
    $pdo     = getDB();
    $stmt    = $pdo->query("SELECT * FROM produits WHERE actif = 1 ORDER BY categorie, id");
    $produits = $stmt->fetchAll();
} catch (PDOException $e) {
    $erreurDB = "Impossible de charger le menu depuis la base de données.";
}

// Regrouper par catégorie
$parCategorie = [];
foreach ($produits as $p) {
    $parCategorie[$p['categorie']][] = $p;
}

require_once 'includes/header.php';
?>

<!-- MENU PAGE -->
<section class="menu-page" id="menu">

    <h1 class="menu-title">Menu</h1>

    <?php if (isset($erreurDB)): ?>
    <div style="text-align:center;color:#ebebdc;font-size:1.6rem;padding:2rem;">
        ⚠️ <?php echo htmlspecialchars($erreurDB); ?>
    </div>
    <?php endif; ?>

    <!-- Filtres catégories -->
    <div class="menu-filters">
        <button data-category="all" class="active">Tous</button>
        <button data-category="sucree">Sucrée</button>
        <button data-category="sale">Salé</button>
        <button data-category="brunch">Brunch</button>
        <button data-category="fresh">Fresh Bar</button>
    </div>

    <!-- Conteneur des produits -->
    <div class="menu-container">

        <?php if (!empty($produits)): ?>
            <?php foreach ($produits as $p): ?>
            <div class="menu-card" data-category="<?php echo htmlspecialchars($p['categorie']); ?>">
                <img src="images/<?php echo htmlspecialchars($p['image']); ?>"
                     alt="<?php echo htmlspecialchars($p['nom']); ?>"
                     onerror="this.style.display='none'">
                <h3><?php echo htmlspecialchars($p['nom']); ?></h3>
                <?php if (!empty($p['description'])): ?>
                <p><?php echo htmlspecialchars($p['description']); ?></p>
                <?php endif; ?>
                <span class="price"><?php echo number_format($p['prix'], 2, ',', ' '); ?> DT</span>
                <button class="add-to-cart"
                        data-id="<?php echo (int)$p['id']; ?>"
                        data-nom="<?php echo htmlspecialchars($p['nom'], ENT_QUOTES); ?>"
                        data-prix="<?php echo (float)$p['prix']; ?>"
                        data-img="images/<?php echo htmlspecialchars($p['image'], ENT_QUOTES); ?>">
                    Commander
                </button>
            </div>
            <?php endforeach; ?>

        <?php else: ?>
            <?php
            $produitsStatiques = [
                // BRUNCH
                ['nom'=>'Douceur quotidienne', 'desc'=>'Un café avec deux mini croissants gourmands.', 'prix'=>22, 'cat'=>'brunch', 'img'=>'Douceur quotidienne.png'],
                ['nom'=>'Arabesque',            'desc'=>'Deux mini brik — Mokli à l\'ancienne — Assida zgougou — Bsissa pistache SECRITO.', 'prix'=>55, 'cat'=>'brunch', 'img'=>'Arabesque.png'],
                ['nom'=>'Gourmand',             'desc'=>'Wrap poulet pané — Mousse de chocolat croquante — Dattes gourmandes.', 'prix'=>47, 'cat'=>'brunch', 'img'=>'Gourmand.png'],
                ['nom'=>'Salé',                 'desc'=>'Oeuf bénédicte saumon — Croissant salé — Salade grecque.', 'prix'=>45, 'cat'=>'brunch', 'img'=>'Sale.png'],
                ['nom'=>'Healthy',              'desc'=>'Toast salé avocat, saumon, oeuf poché — Shot bomb — Salade avec légumes grillés.', 'prix'=>35, 'cat'=>'brunch', 'img'=>'Healthy.png'],
                // SUCRÉE
                ['nom'=>'Fleur de Chocolats',           'desc'=>'2 fleurs de chocolat Secrito.', 'prix'=>14, 'cat'=>'sucree', 'img'=>'Fleur de Chocolats.png'],
                ['nom'=>'Pistache Secrito',              'desc'=>'', 'prix'=>19, 'cat'=>'sucree', 'img'=>'Pistache secrito.png'],
                ['nom'=>'Noisettes Secrito',             'desc'=>'', 'prix'=>18, 'cat'=>'sucree', 'img'=>'Noisettes secrito.png'],
                ['nom'=>'Tiramisu Signature Secrito',    'desc'=>'', 'prix'=>20, 'cat'=>'sucree', 'img'=>'Tiramisu.png'],
                ['nom'=>'Crêpe Caramel Beurre Salé',     'desc'=>'', 'prix'=>15, 'cat'=>'sucree', 'img'=>'Crepe Caramel Beurre Sale.png'],
                ['nom'=>'Crêpe Nutella',                 'desc'=>'', 'prix'=>17, 'cat'=>'sucree', 'img'=>'Crepe Nutella.png'],
                ['nom'=>'Crème brûlée',                  'desc'=>'', 'prix'=>21, 'cat'=>'sucree', 'img'=>'Creme brulee.png'],
                // SALÉ
                ['nom'=>'Wrap Viande Hachée spicy', 'desc'=>'Bœuf haché maison légèrement pimenté.', 'prix'=>20, 'cat'=>'sale', 'img'=>'Wrap Viande Hachee spicy.png'],
                ['nom'=>'Omelette',                 'desc'=>'TARTUFFO : Truffes — LA SPAGNIOLA : Végétarienne — NORVÉGIENNE : Saumon.', 'prix'=>28, 'cat'=>'sale', 'img'=>'omllette.png'],
                ['nom'=>'Crevette Panée',           'desc'=>'Riz Blanc, edamame, sauce soja et mayonnaise épicée.', 'prix'=>28, 'cat'=>'sale', 'img'=>'crevette pane.png'],
                ['nom'=>'Crevette Grillée',         'desc'=>'Riz Blanc, edamame, sauce soja et mayonnaise épicée.', 'prix'=>30, 'cat'=>'sale', 'img'=>'Crevette Grille.png'],
                ['nom'=>'Saumon',                   'desc'=>'Riz Blanc, edamame, sauce soja et mayonnaise épicée.', 'prix'=>28, 'cat'=>'sale', 'img'=>'Saumon.png'],
                // FRESH BAR
                ['nom'=>'Café latte glacé rose',        'desc'=>'', 'prix'=>16, 'cat'=>'fresh', 'img'=>'cafe latte glacee rose.png'],
                ['nom'=>'Café latte glacé pistache',    'desc'=>'', 'prix'=>19, 'cat'=>'fresh', 'img'=>'Cafe latte glacee pistache.png'],
                ['nom'=>'Fraise rose',                  'desc'=>'', 'prix'=>15, 'cat'=>'fresh', 'img'=>'fraise rose.png'],
                ['nom'=>'Mangue Passion',               'desc'=>'', 'prix'=>16, 'cat'=>'fresh', 'img'=>'Mangue Passion.png'],
                ['nom'=>'Jus d\'Orange Pressé Minute',  'desc'=>'', 'prix'=>8,  'cat'=>'fresh', 'img'=>'Jus Orange Presse Minute.png'],
                ['nom'=>'Jus d\'Agrumes Signature',     'desc'=>'Orange, pamplemousse, bergamote.', 'prix'=>13, 'cat'=>'fresh', 'img'=>'Jus Agrumes Signature.png'],
            ];
            foreach ($produitsStatiques as $i => $p):
            ?>
            <div class="menu-card" data-category="<?php echo $p['cat']; ?>">
                <img src="images/<?php echo htmlspecialchars($p['img']); ?>"
                     alt="<?php echo htmlspecialchars($p['nom']); ?>"
                     onerror="this.style.display='none'">
                <h3><?php echo htmlspecialchars($p['nom']); ?></h3>
                <?php if ($p['desc']): ?>
                <p><?php echo htmlspecialchars($p['desc']); ?></p>
                <?php endif; ?>
                <span class="price"><?php echo number_format($p['prix'], 2, ',', ' '); ?> DT</span>
                <button class="add-to-cart"
                        data-id="<?php echo $i + 1; ?>"
                        data-nom="<?php echo htmlspecialchars($p['nom'], ENT_QUOTES); ?>"
                        data-prix="<?php echo $p['prix']; ?>"
                        data-img="images/<?php echo htmlspecialchars($p['img'], ENT_QUOTES); ?>">
                    Commander
                </button>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>

    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
<?php require_once 'includes/cart.php'; ?>

<script src="js/main.js"></script>
</body>
</html>