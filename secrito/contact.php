<?php
require_once 'config/config.php';
$page      = 'contact';
$pageTitle = 'Contact';
require_once 'includes/header.php';
?>

<!-- CONTACT -->
<section class="contact">
    <div class="contact-box">
        <h2>Contactez-nous</h2>

        <!-- Message de succès après soumission -->
        <?php if (isset($_SESSION['contact_success'])): ?>
        <div id="flashSuccess" style="display:block;">
            <!-- Déclenché via JS pour afficher la modal -->
        </div>
        <script>
            // Afficher la modal côté client si le message vient du serveur
            document.addEventListener("DOMContentLoaded", function() {
                const modal = document.getElementById("thankModal");
                if (modal) {
                    modal.style.display = "flex";
                    // Auto-fermer après 3s
                    setTimeout(() => { modal.style.display = "none"; }, 3000);
                }
            });
        </script>
        <?php unset($_SESSION['contact_success']); ?>
        <?php endif; ?>

        <!-- Affichage des erreurs éventuelles -->
        <?php if (isset($_SESSION['contact_error'])): ?>
        <p style="color:#fca5a5;font-size:1.4rem;margin-bottom:1rem;">
            <?php echo htmlspecialchars($_SESSION['contact_error']); unset($_SESSION['contact_error']); ?>
        </p>
        <?php endif; ?>

        <form id="contactForm" action="contact_handler.php" method="POST">
            <!-- Token CSRF -->
            <?php
            if (empty($_SESSION['csrf_token'])) {
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            }
            ?>
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

            <input type="text"  name="nom"       placeholder="Nom complet"   required
                   value="<?php echo isset($_SESSION['old_nom'])   ? clean($_SESSION['old_nom'])   : ''; ?>">

            <input type="email" name="email"     placeholder="Email"         required
                   value="<?php echo isset($_SESSION['old_email']) ? clean($_SESSION['old_email']) : ''; ?>">

            <input type="tel"   name="telephone" placeholder="Téléphone"
                   value="<?php echo isset($_SESSION['old_tel'])   ? clean($_SESSION['old_tel'])   : ''; ?>">

            <textarea name="message" rows="4" placeholder="Votre message" required><?php
                echo isset($_SESSION['old_message']) ? clean($_SESSION['old_message']) : '';
            ?></textarea>

            <?php
            // Nettoyer les vieilles valeurs
            unset($_SESSION['old_nom'], $_SESSION['old_email'], $_SESSION['old_tel'], $_SESSION['old_message']);
            ?>

            <button type="submit" class="cart-btn primary">Envoyer</button>
        </form>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
<?php require_once 'includes/cart.php'; ?>

<!-- MODAL MERCI CONTACT -->
<div class="thank-modal" id="thankModal">
    <div class="thank-box">
        <p>Merci pour votre message 🙏</p>
    </div>
</div>

<script src="js/main.js"></script>
</body>
</html>
