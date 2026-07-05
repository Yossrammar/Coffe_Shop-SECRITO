<?php
require_once 'config/config.php';
$page      = 'index';
$pageTitle = null;
require_once 'includes/header.php';
?>

<!-- BANNER SLIDER -->
<section class="banner" id="B-banner">
    <div class="slides">
        <img src="images/banner1.png" class="slide active" alt="banner 1">
        <img src="images/banner2.png" class="slide" alt="banner 2">
        <img src="images/banner3.png" class="slide" alt="banner 3">
        <img src="images/banner4.png" class="slide" alt="banner 4">
        <img src="images/banner5.png" class="slide" alt="banner 5">
        <img src="images/banner6.png" class="slide" alt="banner 6">
        <img src="images/banner7.png" class="slide" alt="banner 7">
    </div>
    <div class="banner-content">
        <h1>Bienvenue Chez Secrito</h1>
        <p><b>Ce n'est pas ce que vous croyez, c'est un univers où le café, l'ambiance et les instants se
            rencontrent <br> SECRITO, Ce n'est pas ce que vous croyez</b></p>
    </div>
    <span class="arrow left">&#10094;</span>
    <span class="arrow right">&#10095;</span>
</section>

<!-- ABOUT OVERLAY -->
<section class="about-overlay" id="apropos">
    <div class="about-overlay-content">
        <h2>À Propos</h2>
        <p><b>Secrito est un café moderne imaginé comme un espace de vie, où chaque détail contribue à une
            expérience unique. Entre cafés soigneusement sélectionnés, ambiance raffinée et instants de partage,
            Secrito invite à ralentir et à savourer chaque moment.</b></p>
        <div class="about-images">
            <img src="images/local5.png" alt="Espace café Secrito">
            <img src="images/local1.png" alt="Intérieur du café Secrito">
            <img src="images/local2.png" alt="Ambiance du café Secrito">
            <img src="images/local3.png" alt="Espace café Secrito">
            <img src="images/local4.png" alt="Espace café Secrito">
        </div>
    </div>
</section>

<!-- MENU TEASER -->
<section class="menu-teaser">
    <div class="menu-teaser-content">
        <h2>Découvrez notre menu</h2>
        <p>Une sélection soigneusement préparée pour tous les goûts.</p>
        <a href="menu.php" class="menu-btn">Voir le menu</a>
    </div>
</section>

<!-- AVIS CLIENTS -->
<?php
// Récupération des avis depuis la BD (table optionnelle, fallback statique)
$avis = [];
try {
    $pdo = getDB();
    // Vérifie si la table avis existe
    $check = $pdo->query("SHOW TABLES LIKE 'avis'")->fetchColumn();
    if ($check) {
        $avis = $pdo->query("SELECT * FROM avis WHERE actif = 1 ORDER BY id DESC")->fetchAll();
    }
} catch (PDOException $e) {
    // Silencieux : on utilise les avis statiques
}

// Avis statiques de fallback
if (empty($avis)) {
    $avis = [
        ['texte' => 'Parfait pour se détendre ou travailler tranquillement.', 'auteur' => 'Amine B.'],
        ['texte' => 'Secrito est devenu mon café préféré.', 'auteur' => 'Sara M.'],
        ['texte' => 'TOP TOP TOP 👏🏻❤️✨ Un lieu élégant avec une vraie identité.', 'auteur' => 'Maryem.G'],
        ['texte' => '100% J\'adore tous ❤️❤️✨', 'auteur' => 'Asma.H'],
        ['texte' => 'L\'accueil est chaleureux et l\'atmosphère est vraiment unique.', 'auteur' => 'Youssef K.'],
        ['texte' => 'magnifique ✅❤️', 'auteur' => 'SAMEH.J'],
    ];
}
?>
<section class="reviews" id="avis">
    <h1 class="section-title">Avis De Nos Clients</h1>
    <div class="reviews-container">
        <?php foreach ($avis as $a): ?>
        <div class="review-card">
            <p class="review-text"><?php echo htmlspecialchars($a['texte']); ?></p>
            <h4 class="review-author"><?php echo htmlspecialchars($a['auteur']); ?></h4>
        </div>
        <?php endforeach; ?>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
<?php require_once 'includes/cart.php'; ?>

<script src="js/main.js"></script>
</body>
</html>
