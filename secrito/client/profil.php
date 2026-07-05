<?php
require_once '../config/config.php';
requireClient();
$page = 'profil'; $pageTitle = 'Mon compte';

$pdo    = getDB();
$client = $pdo->prepare("SELECT * FROM clients WHERE id = :id");
$client->execute([':id' => $_SESSION['client_id']]);
$client = $client->fetch();

$commandes = $pdo->prepare("SELECT * FROM commandes WHERE client_id = :id ORDER BY created_at DESC");
$commandes->execute([':id' => $_SESSION['client_id']]);
$commandes = $commandes->fetchAll();


$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
    $nom       = trim($_POST['nom']       ?? '');
    $prenom    = trim($_POST['prenom']    ?? '');
    $telephone = trim($_POST['telephone'] ?? '');
    $adresse   = trim($_POST['adresse']   ?? '');

    if (!empty($nom) && !empty($prenom)) {
        $upd = $pdo->prepare("UPDATE clients SET nom=:n,prenom=:p,telephone=:t,adresse=:a WHERE id=:id");
        $upd->execute([':n'=>$nom,':p'=>$prenom,':t'=>$telephone,':a'=>$adresse,':id'=>$_SESSION['client_id']]);
        $_SESSION['client_nom'] = $prenom.' '.$nom;
        $msg = 'Profil mis à jour !';
        $client['nom']=$nom; $client['prenom']=$prenom;
        $client['telephone']=$telephone; $client['adresse']=$adresse;
    }

    if (!empty($_POST['new_pass']) && !empty($_POST['old_pass'])) {
        if (password_verify($_POST['old_pass'], $client['mot_de_passe'])) {
            if (strlen($_POST['new_pass']) >= 6) {
                $hash = password_hash($_POST['new_pass'], PASSWORD_BCRYPT);
                $pdo->prepare("UPDATE clients SET mot_de_passe=:h WHERE id=:id")->execute([':h'=>$hash,':id'=>$_SESSION['client_id']]);
                $msg .= ' Mot de passe changé.';
            } else { $msg = 'Nouveau mot de passe trop court (6 caractères min).'; }
        } else { $msg = 'Ancien mot de passe incorrect.'; }
    }
}

require_once '../includes/header.php';
?>

<section class="contact" style="display:block; padding:12rem 7% 6rem; min-height:100vh;">
    <div style="max-width:900px;margin:0 auto;">

        <h1 style="font-size:3.5rem;color:#94b4ac;margin-bottom:3rem;">
        Bonjour, <?php echo clean($client['prenom']); ?>
        </h1>
        <?php if ($msg): ?>
            <p style="background:#d1fae5;color:#065f46;padding:1.2rem 2rem;border-radius:1rem;margin-bottom:2rem;font-size:1.5rem;"><?php echo clean($msg); ?></p>
        <?php endif; ?>

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:3rem;flex-wrap:wrap;">

            <!-- Modifier profil -->
            <div style="background:#fff;border-radius:2rem;padding:3rem;box-shadow:0 4px 20px rgba(0,0,0,0.08);">
                <h2 style="font-size:2rem;color:var(--color-primary);margin-bottom:2rem;">Mes informations</h2>
                <form method="POST">
                    <input type="hidden" name="update" value="1">
                    <div class="form-group">
                        <label>Prénom</label>
                        <input type="text" name="prenom" value="<?php echo clean($client['prenom']); ?>" required style="width:100%;padding:1rem;border:1px solid #ddd;border-radius:1rem;font-size:1.4rem;margin-bottom:1.2rem;">
                    </div>
                    <div class="form-group">
                        <label style="font-size:1.3rem;color:var(--text-main);display:block;margin-bottom:0.5rem;">Nom</label>
                        <input type="text" name="nom" value="<?php echo clean($client['nom']); ?>" required style="width:100%;padding:1rem;border:1px solid #ddd;border-radius:1rem;font-size:1.4rem;margin-bottom:1.2rem;">
                    </div>
                    <div class="form-group">
                        <label style="font-size:1.3rem;color:var(--text-main);display:block;margin-bottom:0.5rem;">Téléphone</label>
                        <input type="tel" name="telephone" value="<?php echo clean($client['telephone']); ?>" style="width:100%;padding:1rem;border:1px solid #ddd;border-radius:1rem;font-size:1.4rem;margin-bottom:1.2rem;">
                    </div>
                    <div class="form-group">
                        <label style="font-size:1.3rem;color:var(--text-main);display:block;margin-bottom:0.5rem;">Adresse de livraison</label>
                        <textarea name="adresse" rows="2" style="width:100%;padding:1rem;border:1px solid #ddd;border-radius:1rem;font-size:1.4rem;margin-bottom:1.2rem;resize:vertical;"><?php echo clean($client['adresse']); ?></textarea>
                    </div>
                    <hr style="margin:1.5rem 0;border-color:#eee;">
                    <p style="font-size:1.3rem;color:var(--text-secondary);margin-bottom:1rem;">Changer le mot de passe (optionnel)</p>
                    <input type="password" name="old_pass" placeholder="Ancien mot de passe" style="width:100%;padding:1rem;border:1px solid #ddd;border-radius:1rem;font-size:1.4rem;margin-bottom:1.2rem;">
                    <input type="password" name="new_pass" placeholder="Nouveau mot de passe" style="width:100%;padding:1rem;border:1px solid #ddd;border-radius:1rem;font-size:1.4rem;margin-bottom:1.5rem;">
                    <button type="submit" style="width:100%;padding:1.2rem;background:var(--color-primary);color:#fff;border-radius:2rem;font-size:1.5rem;cursor:pointer;border:none;">Enregistrer</button>
                </form>
            </div>

            <!-- Mes commandes -->
            <div style="background:#fff;border-radius:2rem;padding:3rem;box-shadow:0 4px 20px rgba(0,0,0,0.08);">
                <h2 style="font-size:2rem;color:var(--color-primary);margin-bottom:2rem;">Mes commandes</h2>
                <?php if (empty($commandes)): ?>
                    <p style="font-size:1.4rem;color:var(--text-secondary);">Aucune commande pour le moment.</p>
                    <a href="<?php echo SITE_URL; ?>/menu.php" style="display:inline-block;margin-top:1.5rem;padding:1rem 2.5rem;background:var(--color-primary);color:#fff;border-radius:2rem;font-size:1.4rem;">Voir le menu</a>
                <?php else: ?>
                    <div style="max-height:400px;overflow-y:auto;">
                    <?php foreach ($commandes as $cmd): ?>
                        <?php
                        $colors = ['en_attente'=>'#f59e0b','confirmee'=>'#3b82f6','en_livraison'=>'#8b5cf6','livree'=>'#10b981','annulee'=>'#ef4444'];
                        $labels = ['en_attente'=>'En attente','confirmee'=>'Confirmée','en_livraison'=>'En livraison','livree'=>'Livrée','annulee'=>'Annulée'];
                        $col    = $colors[$cmd['statut']] ?? '#888';
                        $lab    = $labels[$cmd['statut']] ?? $cmd['statut'];
                        ?>
                        <div style="border:1px solid #eee;border-radius:1.2rem;padding:1.5rem;margin-bottom:1.5rem;">
                            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:0.8rem;">
                                <strong style="font-size:1.4rem;color:var(--color-primary);">Commande #<?php echo $cmd['id']; ?></strong>
                                <span style="background:<?php echo $col; ?>;color:#fff;padding:0.4rem 1.2rem;border-radius:2rem;font-size:1.2rem;"><?php echo $lab; ?></span>
                            </div>
                            <p style="font-size:1.3rem;color:var(--text-secondary);"><?php echo date('d/m/Y H:i', strtotime($cmd['created_at'])); ?></p>
                            <p style="font-size:1.5rem;font-weight:600;color:var(--color-accent);margin-top:0.5rem;"><?php echo number_format($cmd['total'] + $cmd['livraison'], 2, ',', ' '); ?> DT</p>
                            <?php if ($cmd['statut'] === 'livree' || $cmd['statut'] === 'confirmee'): ?>
                                <a href="<?php echo SITE_URL; ?>/client/facture.php?id=<?php echo $cmd['id']; ?>" target="_blank" style="display:inline-block;margin-top:0.8rem;font-size:1.3rem;color:var(--color-primary);text-decoration:underline;">📄 Voir la facture</a>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

        </div>
    </div>
</section>

<?php require_once '../includes/footer.php'; ?>
<?php require_once '../includes/cart.php'; ?>
<script src="<?php echo SITE_URL; ?>/js/main.js"></script>
</body>
</html>
