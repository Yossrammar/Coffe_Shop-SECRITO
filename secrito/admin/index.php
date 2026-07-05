<?php
require_once '../config/config.php';
requireAdmin();
$pdo = getDB();

$stats = [
    'commandes'  => $pdo->query("SELECT COUNT(*) FROM commandes")->fetchColumn(),
    'en_attente' => $pdo->query("SELECT COUNT(*) FROM commandes WHERE statut='en_attente'")->fetchColumn(),
    'clients'    => $pdo->query("SELECT COUNT(*) FROM clients")->fetchColumn(),
    'messages'   => $pdo->query("SELECT COUNT(*) FROM messages WHERE lu=0")->fetchColumn(),
    'produits'   => $pdo->query("SELECT COUNT(*) FROM produits WHERE actif=1")->fetchColumn(),
];

$dernieres = $pdo->query("SELECT * FROM commandes ORDER BY created_at DESC LIMIT 5")->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Admin — Secrito</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="admin.css">
</head>
<body class="admin-body">
<?php include 'sidebar.php'; ?>
<main class="admin-main">
    <div class="admin-topbar">
        <h1>Tableau de bord</h1>
        <span>Bienvenue, <?php echo clean($_SESSION['admin_nom']); ?></span>
    </div>

    <div class="stats-grid">
        <div class="stat-card"><i class="fas fa-shopping-bag"></i><div><strong><?php echo $stats['commandes']; ?></strong><span>Commandes totales</span></div></div>
        <div class="stat-card orange"><i class="fas fa-clock"></i><div><strong><?php echo $stats['en_attente']; ?></strong><span>En attente</span></div></div>
        <div class="stat-card green"><i class="fas fa-users"></i><div><strong><?php echo $stats['clients']; ?></strong><span>Clients</span></div></div>
        <div class="stat-card purple"><i class="fas fa-envelope"></i><div><strong><?php echo $stats['messages']; ?></strong><span>Messages non lus</span></div></div>
        <div class="stat-card blue"><i class="fas fa-utensils"></i><div><strong><?php echo $stats['produits']; ?></strong><span>Produits actifs</span></div></div>
    </div>

    <div class="admin-card">
        <h2>Dernières commandes</h2>
        <table class="admin-table">
            <thead><tr><th>#</th><th>Client</th><th>Total</th><th>Statut</th><th>Date</th><th>Action</th></tr></thead>
            <tbody>
            <?php foreach ($dernieres as $c): ?>
                <?php
                $colors=['en_attente'=>'#f59e0b','confirmee'=>'#3b82f6','en_livraison'=>'#8b5cf6','livree'=>'#10b981','annulee'=>'#ef4444'];
                $labels=['en_attente'=>'En attente','confirmee'=>'Confirmée','en_livraison'=>'En livraison','livree'=>'Livrée','annulee'=>'Annulée'];
                ?>
                <tr>
                    <td>#<?php echo $c['id']; ?></td>
                    <td><?php echo clean($c['nom_client']); ?></td>
                    <td><?php echo number_format($c['total']+$c['livraison'],2,',',' '); ?> DT</td>
                    <td><span class="badge" style="background:<?php echo $colors[$c['statut']]??'#888'; ?>"><?php echo $labels[$c['statut']]??$c['statut']; ?></span></td>
                    <td><?php echo date('d/m/Y H:i',strtotime($c['created_at'])); ?></td>
                    <td><a href="commandes.php?id=<?php echo $c['id']; ?>" class="btn-sm">Gérer</a></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</main>
</body>
</html>
