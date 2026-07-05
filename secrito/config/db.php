<?php
define('DB_HOST', 'localhost');
define('DB_NAME', 'secrito');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');
define('FRAIS_LIVRAISON', 3.00);

function getDB(): PDO {
    static $pdo = null;
    if ($pdo === null) {
        $dsn = "mysql:host=".DB_HOST.";dbname=".DB_NAME.";charset=".DB_CHARSET;
        try {
            $pdo = new PDO($dsn, DB_USER, DB_PASS, [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ]);
        } catch (PDOException $e) {
            die('<div style="font-family:sans-serif;padding:2rem;background:#fef2f2;color:#991b1b;border:1px solid #fca5a5;border-radius:8px;margin:2rem;">
                <strong>Erreur de connexion à la base de données.</strong><br>
                Vérifiez vos paramètres dans <code>config/db.php</code><br><br>
                <small>'.htmlspecialchars($e->getMessage()).'</small>
            </div>');
        }
    }
    return $pdo;
}
