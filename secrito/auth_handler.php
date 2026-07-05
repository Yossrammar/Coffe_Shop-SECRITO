<?php
require_once 'config/config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header('Location: login.php'); exit; }

$action = $_POST['action'] ?? '';

if ($action === 'login') {
    $email = trim($_POST['email'] ?? '');
    $pass  = $_POST['password'] ?? '';

    if (empty($email) || empty($pass)) {
        $_SESSION['auth_error'] = 'Veuillez remplir tous les champs.';
        header('Location: login.php'); exit;
    }

    $pdo  = getDB();
    $stmt = $pdo->prepare("SELECT * FROM clients WHERE email = :email");
    $stmt->execute([':email' => $email]);
    $client = $stmt->fetch();

    if (!$client || !password_verify($pass, $client['mot_de_passe'])) {
        $_SESSION['auth_error'] = 'Email ou mot de passe incorrect.';
        header('Location: login.php'); exit;
    }

    $_SESSION['client_id']  = $client['id'];
    $_SESSION['client_nom'] = $client['prenom'] . ' ' . $client['nom'];
    header('Location: client/profil.php'); exit;
}

if ($action === 'register') {
    $prenom    = trim($_POST['prenom']    ?? '');
    $nom       = trim($_POST['nom']       ?? '');
    $email     = trim($_POST['email']     ?? '');
    $telephone = trim($_POST['telephone'] ?? '');
    $adresse   = trim($_POST['adresse']   ?? '');
    $pass      = $_POST['password']  ?? '';
    $pass2     = $_POST['password2'] ?? '';

    $_SESSION['old'] = compact('prenom','nom','email','telephone','adresse');

    if (empty($prenom)||empty($nom)||empty($email)||empty($pass)) {
        $_SESSION['auth_error'] = 'Veuillez remplir tous les champs obligatoires.';
        header('Location: login.php?mode=register'); exit;
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['auth_error'] = 'Email invalide.';
        header('Location: login.php?mode=register'); exit;
    }
    if (strlen($pass) < 6) {
        $_SESSION['auth_error'] = 'Le mot de passe doit contenir au moins 6 caractères.';
        header('Location: login.php?mode=register'); exit;
    }
    if ($pass !== $pass2) {
        $_SESSION['auth_error'] = 'Les mots de passe ne correspondent pas.';
        header('Location: login.php?mode=register'); exit;
    }

    $pdo = getDB();
    $check = $pdo->prepare("SELECT id FROM clients WHERE email = :email");
    $check->execute([':email' => $email]);
    if ($check->fetch()) {
        $_SESSION['auth_error'] = 'Cet email est déjà utilisé.';
        header('Location: login.php?mode=register'); exit;
    }

    $hash = password_hash($pass, PASSWORD_BCRYPT);
    $stmt = $pdo->prepare("INSERT INTO clients (nom,prenom,email,telephone,adresse,mot_de_passe) VALUES (:nom,:prenom,:email,:tel,:adr,:pass)");
    $stmt->execute([':nom'=>$nom,':prenom'=>$prenom,':email'=>$email,':tel'=>$telephone,':adr'=>$adresse,':pass'=>$hash]);

    $_SESSION['auth_success'] = 'Compte créé avec succès ! Vous pouvez vous connecter.';
    unset($_SESSION['old']);
    header('Location: login.php'); exit;
}

header('Location: login.php'); exit;
