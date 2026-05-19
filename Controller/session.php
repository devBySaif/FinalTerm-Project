<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../Model/Database.php';
require_once __DIR__ . '/../Model/User.php';

if (!isset($_SESSION['initiated'])) {
    session_regenerate_id(true);
    $_SESSION['initiated'] = true;
}

function e($value): string {
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function flash(string $key, ?string $message = null): ?string {
    if ($message !== null) {
        $_SESSION['flash'][$key] = $message;
        return null;
    }

    if (!isset($_SESSION['flash'][$key])) {
        return null;
    }

    $value = $_SESSION['flash'][$key];
    unset($_SESSION['flash'][$key]);
    return $value;
}

function login_user(array $user): void {
    session_regenerate_id(true);
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['name'] = $user['name'];
    $_SESSION['email'] = $user['email'];
    $_SESSION['role'] = $user['role'];
    $_SESSION['is_verified'] = $user['is_verified'];
}

function clear_remember_cookie(): void {
    setcookie('remember_token', '', [
        'expires' => time() - 3600,
        'path' => '/',
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
}

function remember_user(int $userId): void {
    $validator = bin2hex(random_bytes(32));
    $hashedValidator = hash('sha256', $validator);

    $userModel = new User(Database::connect());
    if (!$userModel->storeRememberToken($userId, $hashedValidator)) {
        return;
    }

    setcookie('remember_token', "{$userId}:{$validator}", [
        'expires' => time() + (30 * 24 * 60 * 60),
        'path' => '/',
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
}

function restore_remembered_user(): void {
    if (empty($_COOKIE['remember_token'])) {
        return;
    }

    $parts = explode(':', $_COOKIE['remember_token'], 2);
    if (count($parts) !== 2) {
        clear_remember_cookie();
        return;
    }

    [$userId, $validator] = $parts;
    if (!ctype_digit($userId)) {
        clear_remember_cookie();
        return;
    }

    $userModel = new User(Database::connect());
    $user = $userModel->getById((int) $userId);

    if (!$user || empty($user['remember_token']) || !hash_equals($user['remember_token'], hash('sha256', $validator))) {
        clear_remember_cookie();
        return;
    }

    login_user($user);
}

function current_user(): ?array {
    if (!isset($_SESSION['user_id'])) {
        restore_remembered_user();
    }

    if (!isset($_SESSION['user_id'])) {
        return null;
    }

    $userModel = new User(Database::connect());
    $user = $userModel->getById((int) $_SESSION['user_id']);
    return $user ?: null;
}

function require_login(): array {
    $user = current_user();
    if (!$user) {
        header('Location: ../View/login.php');
        exit;
    }
    return $user;
}

function require_verified_user_role(): array {
    $user = require_login();
    if ($user['role'] !== 'user' || (int) $user['is_verified'] !== 1) {
        http_response_code(403);
        die('Only verified general users can access this page.');
    }
    return $user;
}
