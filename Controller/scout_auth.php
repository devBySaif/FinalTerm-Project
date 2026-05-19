<?php
session_start();

if (!isset($_SESSION['user_id'], $_SESSION['role'], $_SESSION['is_verified']) || $_SESSION['role'] !== 'scout' || (int)$_SESSION['is_verified'] !== 1) {
    header('Location: Scout_login_controller.php');
    exit;
}

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

function e(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

function flash_message(): string
{
    if (empty($_SESSION['flash'])) {
        return '';
    }

    $flash = $_SESSION['flash'];
    unset($_SESSION['flash']);
    return '<div class="alert alert-' . e($flash['type']) . '">' . e($flash['message']) . '</div>';
}
