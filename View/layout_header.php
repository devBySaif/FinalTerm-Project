<?php
if (!function_exists('e')) {
    function e($value)
    {
        return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
    }
}

$directViewPages = [
    'dashboard.php' => 'dashboard',
    'users.php' => 'users',
    'add_user.php' => 'add_user',
    'posts.php' => 'posts',
    'edit_post.php' => 'posts',
    'comments.php' => 'comments',
];

$currentScript = basename($_SERVER['SCRIPT_NAME'] ?? '');
if ($currentScript !== 'admin.php' && isset($directViewPages[$currentScript])) {
    header('Location: ../admin.php?page=' . $directViewPages[$currentScript]);
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?= e($csrfToken ?? '') ?>">
    <title><?= e($pageTitle ?? 'Admin') ?> | Travel Guide</title>
    <link rel="stylesheet" href="Public/CSS/admin-style.css?v=3">
</head>
<body>
<div class="admin-shell">
    <aside class="admin-sidebar">
        <div class="brand-block">
            <span class="brand-mark">TG</span>
            <div>
                <strong>Travel Guide</strong>
                <span>Admin Panel</span>
            </div>
        </div>

        <nav class="admin-nav" aria-label="Admin navigation">
            <a class="<?= ($activePage ?? '') === 'dashboard' ? 'active' : '' ?>" href="admin.php?page=dashboard">Dashboard</a>
            <a class="<?= ($activePage ?? '') === 'users' ? 'active' : '' ?>" href="admin.php?page=users">Users</a>
            <a class="<?= ($activePage ?? '') === 'posts' ? 'active' : '' ?>" href="admin.php?page=posts">Posts</a>
            <a class="<?= ($activePage ?? '') === 'comments' ? 'active' : '' ?>" href="admin.php?page=comments">Comments</a>
        </nav>
    </aside>

    <main class="admin-main">
        <header class="topbar">
            <div>
                <p class="eyebrow">Administration</p>
                <h1><?= e($pageTitle ?? 'Dashboard') ?></h1>
            </div>
            <div class="topbar-actions">
                <div class="admin-user">
                    <span><?= e($_SESSION['name'] ?? 'Admin') ?></span>
                    <small><?= e($_SESSION['role'] ?? 'admin') ?></small>
                </div>
                <a class="button danger" href="Controller/AuthController.php?action=logout">Logout</a>
            </div>
        </header>

        <?php if (!empty($flash)): ?>
            <div class="flash flash-<?= e($flash['type']) ?>"><?= e($flash['message']) ?></div>
        <?php endif; ?>
