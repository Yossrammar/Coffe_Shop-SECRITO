<?php
$page     = $page ?? '';
$showCart = in_array($page, ['index', 'menu']);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Secrito<?php echo $pageTitle ? ' — ' . htmlspecialchars($pageTitle) : ''; ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>/css/style.css">
</head>
<body>

<div class="image-preview" id="imagePreview">
    <img id="previewImg" src="" alt="Preview">
</div>

<header class="header">
    <a href="<?php echo SITE_URL; ?>/index.php" class="logo">
        <img src="<?php echo SITE_URL; ?>/images/secrito_logo.png" alt="Secrito Logo">
    </a>
    <nav class="navbar">
        <a href="<?php echo SITE_URL; ?>/index.php"   <?php echo $page==='index'   ? 'class="active-nav"':''; ?>>Accueil</a>
        <a href="<?php echo SITE_URL; ?>/menu.php"    <?php echo $page==='menu'    ? 'class="active-nav"':''; ?>>Menu</a>
        <a href="<?php echo SITE_URL; ?>/contact.php" <?php echo $page==='contact' ? 'class="active-nav"':''; ?>>Contact</a>
    </nav>

    <div class="icon">
        <?php if (clientConnecte()): ?>
            <?php if ($page === 'profil'): ?>
                <a href="<?php echo SITE_URL; ?>/client/profil.php" class="icon-circle" title="Mon compte">
                    <i class="fas fa-user"></i>
                </a>
                <a href="<?php echo SITE_URL; ?>/client/logout.php" class="icon-circle" title="Déconnexion">
                    <i class="fas fa-sign-out-alt"></i>
                </a>
            <?php elseif ($showCart): ?>
                <a href="<?php echo SITE_URL; ?>/client/profil.php" class="icon-circle" title="Mon compte">
                    <i class="fas fa-user"></i>
                </a>
                <div class="fas fa-shopping-cart" id="cart-btn"></div>
            <?php else: ?>
                <a href="<?php echo SITE_URL; ?>/client/profil.php" class="icon-circle" title="Mon compte">
                    <i class="fas fa-user"></i>
                </a>
            <?php endif; ?>
        <?php else: ?>
            <a href="<?php echo SITE_URL; ?>/login.php" class="icon-circle" title="Se connecter">
                <i class="fas fa-user"></i>
            </a>
        <?php endif; ?>
    </div>
</header>