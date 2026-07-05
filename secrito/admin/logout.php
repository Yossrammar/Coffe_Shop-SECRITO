<?php
require_once '../config/config.php';
unset($_SESSION['admin_id'], $_SESSION['admin_nom']);
session_destroy();
header('Location: login.php');
exit;
