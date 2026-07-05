<?php
define('SITE_NAME', 'Secrito');
define('SITE_URL',  'http://localhost:8080/secrito');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/db.php';

function clean(string $val): string {
    return htmlspecialchars(trim($val), ENT_QUOTES, 'UTF-8');
}

function clientConnecte(): bool {
    return isset($_SESSION['client_id']);
}

function adminConnecte(): bool {
    return isset($_SESSION['admin_id']);
}

function requireClient(): void {
    if (!clientConnecte()) {
        header('Location: ' . SITE_URL . '/login.php');
        exit;
    }
}

function requireAdmin(): void {
    if (!adminConnecte()) {
        header('Location: ' . SITE_URL . '/admin/login.php');
        exit;
    }
}
