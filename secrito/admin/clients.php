<?php
require_once '../config/config.php';
requireAdmin();
$pdo = getDB();

if (isset($_GET['delete'])) {
    $pdo->prepare("DELETE FROM clients WHERE id=:id")->execute([':id'=>(int)$_GET['delete']]);
    header('Location: clients.php'); exit;
}

$clients = $pdo->query("SELECT c.*, COUNT(cmd.id) as nb_commandes FROM clients c LEFT JOIN commandes cmd ON cmd.client_id=c.id GROUP BY c.id ORDER BY c.created_at DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Clients — Admin Secrito</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="admin.css">
</head>
<body class="admin-body">
<?php include 'sidebar.php'; ?>
<main class="admin-main">
    <div class="admin-topbar"><h1>Clients (<?php echo count($clients); ?>)</h1></div>
    <div class="admin-card">
        <table class="admin-table">
            <thead><tr><th>#</th><th>Nom</th><th>Email</th><th>Tél</th><th>Commandes</th><th>Inscrit le</th><th></th></tr></thead>
            <tbody>
            <?php foreach ($clients as $c): ?>
            <tr>
                <td><?php echo $c['id']; ?></td>
                <td><?php echo clean($c['prenom'].' '.$c['nom']); ?></td>
                <td><?php echo clean($c['email']); ?></td>
                <td><?php echo clean($c['telephone']); ?></td>
                <td><?php echo $c['nb_commandes']; ?></td>
                <td><?php echo date('d/m/Y',strtotime($c['created_at'])); ?></td>
                <td><a href="?delete=<?php echo $c['id']; ?>" class="btn-sm" style="background:#ef4444;" onclick="return confirm('Supprimer ce client ?')">🗑️</a></td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</main>
</body>
</html>
