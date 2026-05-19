<?php
session_start();

$action = $_GET['action'] ?? '';

if ($action === 'logout') {
    session_destroy();
    header('Location: Scout_login_controller.php');
    exit;
}

header('Location: ../View/scout_dashboard.php');
exit;
