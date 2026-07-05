<?php
require_once '../config/config.php';

if (adminConnecte()) {
    header('Location: index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $pass  = $_POST['password'] ?? '';

    $pdo  = getDB();
    $stmt = $pdo->prepare("SELECT * FROM admins WHERE email = :e");
    $stmt->execute([':e' => $email]);
    $admin = $stmt->fetch();

    // ✅ Vérification SANS hash
    if ($admin && $pass === $admin['mot_de_passe']) {
        $_SESSION['admin_id']  = $admin['id'];
        $_SESSION['admin_nom'] = $admin['nom'];
        header('Location: index.php');
        exit;
    }

    $error = 'Identifiants incorrects.';
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Admin — Secrito</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="../css/style.css">

    <style>
        body {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            background: var(--color-primary);
        }

        .login-box {
            background: #fff;
            padding: 4rem;
            border-radius: 2rem;
            width: 400px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
            text-align: center;
        }

        .login-box h1 {
            font-size: 3rem;
            color: var(--color-primary);
            margin-bottom: 0.5rem;
        }

        .login-box p {
            font-size: 1.4rem;
            color: var(--text-secondary);
            margin-bottom: 3rem;
        }

        .login-box input {
            width: 100%;
            padding: 1.2rem 1.5rem;
            border: 1px solid #ddd;
            border-radius: 1rem;
            font-size: 1.4rem;
            margin-bottom: 1.5rem;
            display: block;
        }

        .login-box button {
            width: 100%;
            padding: 1.3rem;
            background: var(--color-primary);
            color: #fff;
            border: none;
            border-radius: 2rem;
            font-size: 1.5rem;
            cursor: pointer;
        }

        .login-box button:hover {
            background: var(--color-accent);
        }

        .err {
            color: #ef4444;
            font-size: 1.3rem;
            margin-bottom: 1.5rem;
        }
    </style>
</head>

<body>

<div class="login-box">
    <h1>Secrito</h1>
    <p>Espace administrateur</p>

    <?php if (!empty($error)): ?>
        <p class="err"><?php echo clean($error); ?></p>
    <?php endif; ?>

    <form method="POST">
        <input type="email" name="email" placeholder="Email admin" required>
        <input type="password" name="password" placeholder="Mot de passe" required>
        <button type="submit">Se connecter</button>
    </form>
</div>

</body>
</html>