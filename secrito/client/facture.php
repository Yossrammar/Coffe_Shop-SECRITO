<?php
require_once '../config/config.php';
requireClient();

$id  = (int)($_GET['id'] ?? 0);
$pdo = getDB();

$cmd = $pdo->prepare("SELECT * FROM commandes WHERE id=:id AND client_id=:cid");
$cmd->execute([':id'=>$id,':cid'=>$_SESSION['client_id']]);
$cmd = $cmd->fetch();

if (!$cmd) { echo 'Commande introuvable.'; exit; }

$items = $pdo->prepare("SELECT * FROM commande_items WHERE commande_id=:id");
$items->execute([':id'=>$id]);
$items = $items->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Facture #<?php echo $cmd['id']; ?> — Secrito</title>
    <style>
        * { margin:0;padding:0;box-sizing:border-box;font-family:'Georgia',serif; }
        body { background:#f5f5f0;padding:4rem;color:#264755; }
        .facture { max-width:700px;margin:0 auto;background:#fff;padding:4rem;border-radius:1.5rem;box-shadow:0 4px 20px rgba(0,0,0,0.1); }
        .header-fact { display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:3rem;padding-bottom:2rem;border-bottom:2px solid #ebebdc; }
        .logo-text { font-size:3rem;font-weight:700;color:#264755;letter-spacing:2px; }
        .logo-text span { color:#84a49c; }
        .fact-info { text-align:right;font-size:1.4rem;color:#60646c; }
        h2 { font-size:2.2rem;margin-bottom:2rem; }
        .client-box { background:#f9f9f6;padding:1.5rem;border-radius:1rem;margin-bottom:2.5rem;font-size:1.4rem;line-height:1.8; }
        table { width:100%;border-collapse:collapse;margin-bottom:2rem; }
        th { background:#264755;color:#fff;padding:1rem 1.2rem;text-align:left;font-size:1.3rem; }
        td { padding:1rem 1.2rem;font-size:1.4rem;border-bottom:1px solid #eee; }
        tr:last-child td { border-bottom:none; }
        .totaux { text-align:right;font-size:1.5rem;padding-top:1rem; }
        .totaux p { margin-bottom:0.6rem; }
        .total-final { font-size:2rem;font-weight:700;color:#264755;margin-top:1rem;padding-top:1rem;border-top:2px solid #264755; }
        .statut-badge { display:inline-block;padding:0.5rem 1.5rem;border-radius:2rem;font-size:1.3rem;font-weight:600;margin-top:0.5rem; }
        .footer-fact { margin-top:3rem;padding-top:2rem;border-top:1px solid #eee;text-align:center;font-size:1.3rem;color:#888; }
        @media print {
            body { background:#fff;padding:0; }
            .facture { box-shadow:none;border-radius:0; }
            .no-print { display:none !important; }
        }
    </style>
</head>
<body>
<div class="facture">
    <div class="header-fact">
        <div>
            <div class="logo-text">SEC<span>RITO</span></div>
            <p style="font-size:1.3rem;color:#888;margin-top:0.5rem;">4 Rue Tachkent, Ariana 2037</p>
            <p style="font-size:1.3rem;color:#888;">+216 24 880 880</p>
        </div>
        <div class="fact-info">
            <strong style="font-size:1.8rem;">FACTURE</strong>
            <p>N° <?php echo str_pad($cmd['id'],5,'0',STR_PAD_LEFT); ?></p>
            <p><?php echo date('d/m/Y', strtotime($cmd['created_at'])); ?></p>
            <?php
            $colors = ['en_attente'=>'#f59e0b','confirmee'=>'#3b82f6','en_livraison'=>'#8b5cf6','livree'=>'#10b981','annulee'=>'#ef4444'];
            $labels = ['en_attente'=>'En attente','confirmee'=>'Confirmée','en_livraison'=>'En livraison','livree'=>'Livrée','annulee'=>'Annulée'];
            ?>
            <span class="statut-badge" style="background:<?php echo $colors[$cmd['statut']]??'#888'; ?>;color:#fff;">
                <?php echo $labels[$cmd['statut']] ?? $cmd['statut']; ?>
            </span>
        </div>
    </div>

    <div class="client-box">
        <strong>Client :</strong> <?php echo clean($cmd['nom_client']); ?><br>
        <?php if ($cmd['telephone']): ?><strong>Tél :</strong> <?php echo clean($cmd['telephone']); ?><br><?php endif; ?>
        <?php if ($cmd['adresse']): ?><strong>Adresse :</strong> <?php echo clean($cmd['adresse']); ?><?php endif; ?>
    </div>

    <table>
        <thead>
            <tr><th>Produit</th><th>Qté</th><th>Prix unit.</th><th>Sous-total</th></tr>
        </thead>
        <tbody>
            <?php foreach ($items as $item): ?>
            <tr>
                <td><?php echo clean($item['nom_produit']); ?></td>
                <td><?php echo $item['quantite']; ?></td>
                <td><?php echo number_format($item['prix_unit'],2,',',' '); ?> DT</td>
                <td><?php echo number_format($item['prix_unit']*$item['quantite'],2,',',' '); ?> DT</td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div class="totaux">
        <p>Sous-total : <strong><?php echo number_format($cmd['total'],2,',',' '); ?> DT</strong></p>
        <p>Frais de livraison : <strong><?php echo number_format($cmd['livraison'],2,',',' '); ?> DT</strong></p>
        <p class="total-final">Total TTC : <?php echo number_format($cmd['total']+$cmd['livraison'],2,',',' '); ?> DT</p>
    </div>

    <div class="footer-fact">
        <p>Merci pour votre confiance ! ☕</p>
        <p style="margin-top:0.5rem;">Secrito — contact@secrito.tn — @secrito_brunch</p>
    </div>

    <div class="no-print" style="text-align:center;margin-top:2.5rem;">
        <button onclick="window.print()" style="padding:1rem 3rem;background:#264755;color:#fff;border:none;border-radius:2rem;font-size:1.5rem;cursor:pointer;margin-right:1rem;">🖨️ Imprimer / PDF</button>
        <button onclick="history.back()" style="padding:1rem 3rem;background:#ebebdc;color:#264755;border:none;border-radius:2rem;font-size:1.5rem;cursor:pointer;">← Retour</button>
    </div>
</div>
</body>
</html>
