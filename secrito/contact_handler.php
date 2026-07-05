<?php
// ============================================================
//  contact_handler.php — Traitement du formulaire de contact
//  Reçoit POST depuis contact.php, sauvegarde en BD, redirige
// ============================================================

require_once 'config/config.php';

// Autoriser uniquement POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: contact.php');
    exit;
}

// ── Vérification CSRF ──────────────────────────────────────
if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== ($_SESSION['csrf_token'] ?? '')) {
    $_SESSION['contact_error'] = 'Requête invalide. Veuillez réessayer.';
    header('Location: contact.php');
    exit;
}
// Renouveler le token
$_SESSION['csrf_token'] = bin2hex(random_bytes(32));

// ── Récupération et nettoyage des données ──────────────────
$nom       = trim($_POST['nom']       ?? '');
$email     = trim($_POST['email']     ?? '');
$telephone = trim($_POST['telephone'] ?? '');
$message   = trim($_POST['message']   ?? '');

// ── Validation ────────────────────────────────────────────
$errors = [];

if (empty($nom) || mb_strlen($nom) < 2) {
    $errors[] = 'Nom invalide (minimum 2 caractères).';
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'Adresse email invalide.';
}

if (empty($message) || mb_strlen($message) < 5) {
    $errors[] = 'Message trop court (minimum 5 caractères).';
}

if (!empty($errors)) {
    // Remettre les valeurs dans la session pour les réafficher
    $_SESSION['contact_error'] = implode(' ', $errors);
    $_SESSION['old_nom']       = $nom;
    $_SESSION['old_email']     = $email;
    $_SESSION['old_tel']       = $telephone;
    $_SESSION['old_message']   = $message;
    header('Location: contact.php');
    exit;
}

// ── Insertion en base de données ──────────────────────────
try {
    $pdo  = getDB();
    $sql  = "INSERT INTO messages (nom, email, telephone, message) VALUES (:nom, :email, :telephone, :message)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':nom'       => htmlspecialchars($nom,       ENT_QUOTES, 'UTF-8'),
        ':email'     => htmlspecialchars($email,     ENT_QUOTES, 'UTF-8'),
        ':telephone' => htmlspecialchars($telephone, ENT_QUOTES, 'UTF-8'),
        ':message'   => htmlspecialchars($message,   ENT_QUOTES, 'UTF-8'),
    ]);

    $_SESSION['contact_success'] = true;

} catch (PDOException $e) {
    // En cas d'erreur BD, on affiche quand même le merci (pas de perte silencieuse)
    error_log('Secrito contact error: ' . $e->getMessage());
    $_SESSION['contact_success'] = true; // UX : l'utilisateur n'est pas responsable
}

header('Location: contact.php');
exit;
