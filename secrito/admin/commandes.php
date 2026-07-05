<?php
require_once '../config/config.php';
requireAdmin();
$pdo = getDB();

// Mise à jour statut
if ($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['statut'], $_POST['commande_id'])) {
    $statuts = ['en_attente','confirmee','en_livraison','livree','annulee'];
    if (in_array($_POST['statut'], $statuts)) {
        $pdo->prepare("UPDATE commandes SET statut=:s WHERE id=:id")
            ->execute([':s'=>$_POST['statut'],':id'=>(int)$_POST['commande_id']]);
    }
    header('Location: commandes.php'); exit;
}

// Filtre statut
$filtre = $_GET['statut'] ?? 'tous';
$valid  = ['tous','en_attente','confirmee','en_livraison','livree','annulee'];
if (!in_array($filtre,$valid)) $filtre='tous';

$sql = "SELECT * FROM commandes";
if ($filtre!=='tous') $sql .= " WHERE statut='".$filtre."'";
$sql .= " ORDER BY created_at DESC";
$commandes = $pdo->query($sql)->fetchAll();

// Détail commande
$detail = null;
if (isset($_GET['id'])) {
    $detail = $pdo->prepare("SELECT * FROM commandes WHERE id=:id");
    $detail->execute([':id'=>(int)$_GET['id']]);
    $detail = $detail->fetch();
    if ($detail) {
        $detail['items'] = $pdo->prepare("SELECT * FROM commande_items WHERE commande_id=:id");
        $detail['items']->execute([':id'=>$detail['id']]);
        $detail['items'] = $detail['items']->fetchAll();
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Commandes — Admin Secrito</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="admin.css">
</head>
<body class="admin-body">
<?php include 'sidebar.php'; ?>
<main class="admin-main">
    <div class="admin-topbar">
        <h1>Commandes</h1>
    </div>

    <?php if ($detail): ?>
    <!-- Détail commande -->
    <div class="admin-card" style="margin-bottom:2rem;">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:2rem;flex-wrap:wrap;gap:1rem;">
            <h2>Commande #<?php echo $detail['id']; ?></h2>
            <a href="commandes.php" style="font-size:1.4rem;color:var(--color-accent);">← Retour à la liste</a>
        </div>
        <p style="font-size:1.4rem;margin-bottom:0.5rem;"><strong>Client :</strong> <?php echo clean($detail['nom_client']); ?></p>
        <p style="font-size:1.4rem;margin-bottom:0.5rem;"><strong>Tél :</strong> <?php echo clean($detail['telephone']); ?></p>
        <?php if ($detail['adresse']): ?>
        <p style="font-size:1.4rem;margin-bottom:0.5rem;"><strong>Adresse :</strong> <?php echo clean($detail['adresse']); ?></p>
        <?php endif; ?>
        <p style="font-size:1.4rem;margin-bottom:1.5rem;"><strong>Date :</strong> <?php echo date('d/m/Y H:i',strtotime($detail['created_at'])); ?></p>

        <table class="admin-table" style="margin-bottom:2rem;">
            <thead><tr><th>Produit</th><th>Qté</th><th>Prix unit.</th><th>Sous-total</th></tr></thead>
            <tbody>
            <?php foreach ($detail['items'] as $item): ?>
            <tr>
                <td><?php echo clean($item['nom_produit']); ?></td>
                <td><?php echo $item['quantite']; ?></td>
                <td><?php echo number_format($item['prix_unit'],2,',',' '); ?> DT</td>
                <td><?php echo number_format($item['prix_unit']*$item['quantite'],2,',',' '); ?> DT</td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <p style="text-align:right;font-size:1.5rem;"><strong>Sous-total : <?php echo number_format($detail['total'],2,',',' '); ?> DT</strong></p>
        <p style="text-align:right;font-size:1.5rem;">Livraison : <?php echo number_format($detail['livraison'],2,',',' '); ?> DT</p>
        <p style="text-align:right;font-size:2rem;font-weight:700;color:var(--color-primary);margin-top:0.5rem;">Total : <?php echo number_format($detail['total']+$detail['livraison'],2,',',' '); ?> DT</p>

        <hr style="margin:2rem 0;border-color:#eee;">
        <h3 style="font-size:1.6rem;margin-bottom:1.5rem;">Changer le statut</h3>
        <form method="POST" style="display:flex;gap:1.2rem;flex-wrap:wrap;">
            <input type="hidden" name="commande_id" value="<?php echo $detail['id']; ?>">
            <?php
            $statuts = ['en_attente'=>'En attente','confirmee'=>'Confirmée','en_livraison'=>'En livraison','livree'=>'Livrée','annulee'=>'Annulée'];
            $colors  = ['en_attente'=>'#f59e0b','confirmee'=>'#3b82f6','en_livraison'=>'#8b5cf6','livree'=>'#10b981','annulee'=>'#ef4444'];
            foreach ($statuts as $val=>$lab):
            ?>
            <button type="submit" name="statut" value="<?php echo $val; ?>"
                style="padding:0.8rem 1.8rem;border:2px solid <?php echo $colors[$val]; ?>;background:<?php echo $detail['statut']===$val ? $colors[$val] : '#fff'; ?>;color:<?php echo $detail['statut']===$val ? '#fff' : $colors[$val]; ?>;border-radius:2rem;font-size:1.3rem;cursor:pointer;font-weight:600;">
                <?php echo $lab; ?>
            </button>
            <?php endforeach; ?>
        </form>
    </div>

    <?php else: ?>
    <!-- Liste commandes -->
    <div style="display:flex;gap:1rem;margin-bottom:2rem;flex-wrap:wrap;">
        <?php
        $filtres = ['tous'=>'Toutes','en_attente'=>'En attente','confirmee'=>'Confirmées','en_livraison'=>'En livraison','livree'=>'Livrées','annulee'=>'Annulées'];
        foreach ($filtres as $val=>$lab):
        ?>
        <a href="?statut=<?php echo $val; ?>" style="padding:0.8rem 2rem;border-radius:2rem;background:<?php echo $filtre===$val?'var(--color-primary)':'#eee'; ?>;color:<?php echo $filtre===$val?'#fff':'var(--color-primary)'; ?>;font-size:1.3rem;"><?php echo $lab; ?></a>
        <?php endforeach; ?>
    </div>

    <div class="admin-card">
        <table class="admin-table">
            <thead><tr><th>#</th><th>Client</th><th>Tél</th><th>Total</th><th>Statut</th><th>Date</th><th></th></tr></thead>
            <tbody>
            <?php if (empty($commandes)): ?>
                <tr><td colspan="7" style="text-align:center;color:#888;padding:3rem;">Aucune commande.</td></tr>
            <?php endif; ?>
            <?php foreach ($commandes as $c):
                $col = $colors[$c['statut']]??'#888';
                $lab2= $statuts[$c['statut']]??$c['statut'];
            ?>
            <tr>
                <td>#<?php echo $c['id']; ?></td>
                <td><?php echo clean($c['nom_client']); ?></td>
                <td><?php echo clean($c['telephone']); ?></td>
                <td><?php echo number_format($c['total']+$c['livraison'],2,',',' '); ?> DT</td>
                <td><span class="badge" style="background:<?php echo $col; ?>"><?php echo $lab2; ?></span></td>
                <td><?php echo date('d/m/Y H:i',strtotime($c['created_at'])); ?></td>
                <td><a href="?id=<?php echo $c['id']; ?>" class="btn-sm">Détail</a></td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>
</main>
</body>
</html>
