<?php
require_once '../config/config.php';
unset($_SESSION['client_id'], $_SESSION['client_nom']);
session_destroy();
header('Location: ' . SITE_URL . '/login.php');
exit;
