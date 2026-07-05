<?php
require_once '../config/config.php';
requireAdmin();
$pdo = getDB();

if (isset($_GET['lu'])) {
    $pdo->prepare("UPDATE messages SET lu=1 WHERE id=:id")->execute([':id'=>(int)$_GET['lu']]);
    header('Location: messages.php'); exit;
}
if (isset($_GET['delete'])) {
    $pdo->prepare("DELETE FROM messages WHERE id=:id")->execute([':id'=>(int)$_GET['delete']]);
    header('Location: messages.php'); exit;
}

$messages = $pdo->query("SELECT * FROM messages ORDER BY created_at DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Messages — Admin Secrito</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="admin.css">
</head>
<body class="admin-body">
<?php include 'sidebar.php'; ?>
<main class="admin-main">
    <div class="admin-topbar"><h1>Messages</h1></div>
    <div class="admin-card">
        <?php foreach ($messages as $m): ?>
        <div style="border:1px solid #eee;border-radius:1.2rem;padding:2rem;margin-bottom:1.5rem;background:<?php echo $m['lu']?'#fff':'#f0fdf4'; ?>;">
            <div style="display:flex;justify-content:space-between;flex-wrap:wrap;gap:1rem;margin-bottom:1rem;">
                <div>
                    <strong style="font-size:1.5rem;"><?php echo clean($m['nom']); ?></strong>
                    <span style="font-size:1.3rem;color:#888;margin-left:1rem;"><?php echo clean($m['email']); ?></span>
                    <?php if ($m['telephone']): ?><span style="font-size:1.3rem;color:#888;margin-left:1rem;"><?php echo clean($m['telephone']); ?></span><?php endif; ?>
                </div>
                <div style="display:flex;gap:1rem;align-items:center;">
                    <span style="font-size:1.2rem;color:#888;"><?php echo date('d/m/Y H:i',strtotime($m['created_at'])); ?></span>
                    <?php if (!$m['lu']): ?>
                        <a href="?lu=<?php echo $m['id']; ?>" class="btn-sm">✓ Lu</a>
                    <?php endif; ?>
                    <a href="?delete=<?php echo $m['id']; ?>" class="btn-sm" style="background:#ef4444;" onclick="return confirm('Supprimer ?')">🗑️</a>
                </div>
            </div>
            <p style="font-size:1.4rem;color:var(--text-main);line-height:1.8;"><?php echo nl2br(clean($m['message'])); ?></p>
        </div>
        <?php endforeach; ?>
        <?php if (empty($messages)): ?>
            <p style="text-align:center;color:#888;padding:3rem;font-size:1.5rem;">Aucun message.</p>
        <?php endif; ?>
    </div>
</main>
</body>
</html>
