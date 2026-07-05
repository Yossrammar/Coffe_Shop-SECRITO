<?php
require_once 'config/config.php';
if (clientConnecte()) { header('Location: client/profil.php'); exit; }
$page = ''; $pageTitle = 'Connexion';
$mode = $_GET['mode'] ?? 'login';
require_once 'includes/header.php';
?>

<section class="contact" style="min-height:100vh;padding-top:12rem;">
    <div class="contact-box" style="max-width:460px;">

        <?php if (isset($_SESSION['auth_error'])): ?>
            <p style="color:#fca5a5;font-size:1.4rem;margin-bottom:1.5rem;"><?php echo clean($_SESSION['auth_error']); unset($_SESSION['auth_error']); ?></p>
        <?php endif; ?>
        <?php if (isset($_SESSION['auth_success'])): ?>
            <p style="color:#86efac;font-size:1.4rem;margin-bottom:1.5rem;"><?php echo clean($_SESSION['auth_success']); unset($_SESSION['auth_success']); ?></p>
        <?php endif; ?>

        <?php if ($mode === 'register'): ?>
            <h2 style="font-size:3.5rem;color:#ebebdc;padding:2rem 0 3rem;">Créer un compte</h2>
            <form action="auth_handler.php" method="POST">
                <input type="hidden" name="action" value="register">
                <input type="text"  name="prenom"    placeholder="Prénom"   required value="<?php echo clean($_SESSION['old']['prenom']  ?? ''); ?>">
                <input type="text"  name="nom"       placeholder="Nom"      required value="<?php echo clean($_SESSION['old']['nom']     ?? ''); ?>">
                <input type="email" name="email"     placeholder="Email"    required value="<?php echo clean($_SESSION['old']['email']   ?? ''); ?>">
                <input type="tel"   name="telephone" placeholder="Téléphone"         value="<?php echo clean($_SESSION['old']['telephone'] ?? ''); ?>">
                <input type="text"  name="adresse"   placeholder="Adresse de livraison" value="<?php echo clean($_SESSION['old']['adresse'] ?? ''); ?>">
                <input type="password" name="password"  placeholder="Mot de passe" required>
                <input type="password" name="password2" placeholder="Confirmer le mot de passe" required>
                <?php unset($_SESSION['old']); ?>
                <button type="submit" class="cart-btn primary" style="margin-top:2rem;">S'inscrire</button>
            </form>
            <p style="margin-top:2rem;font-size:1.4rem;color:#e6e6d8;">Déjà un compte ? <a href="login.php" style="color:#94b4ac;">Se connecter</a></p>

        <?php else: ?>
            <h2 style="font-size:3.5rem;color:#ebebdc;padding:2rem 0 3rem;">Connexion</h2>
            <form action="auth_handler.php" method="POST">
                <input type="hidden" name="action" value="login">
                <input type="email" name="email"    placeholder="Email"         required>
                <input type="password" name="password" placeholder="Mot de passe" required>
                <button type="submit" class="cart-btn primary" style="margin-top:2rem;">Se connecter</button>
            </form>
            <p style="margin-top:2rem;font-size:1.4rem;color:#e6e6d8;">Pas de compte ? <a href="login.php?mode=register" style="color:#94b4ac;">S'inscrire</a></p>
        <?php endif; ?>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
<script src="<?php echo SITE_URL; ?>/js/main.js"></script>
</body>
</html>
