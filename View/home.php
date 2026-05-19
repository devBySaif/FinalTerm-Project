<?php
require_once __DIR__ . '/../Controller/session.php';
require_once __DIR__ . '/../Model/Post.php';

$user = current_user();
$posts = [];

if ($user && (int) $user['is_verified'] === 1) {
    $posts = (new Post(Database::connect()))->latestApproved();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Travel Guide</title>
    <link rel="stylesheet" href="../Public/CSS/style.css">
</head>
<body>
    <?php include __DIR__ . '/partials_nav.php'; ?>

    <?php if ($message = flash('notice')): ?>
        <div class="notice"><?= e($message) ?></div>
    <?php endif; ?>

    <?php if (!$user): ?>
        <section class="hero">
            <h1>Travel Guide</h1>
            <p>Find beautiful places, travel costs, and country-wise suggestions from verified scouts.</p>
            <div class="actions">
                <a class="btn" href="register.php">Create Account</a>
                <a class="btn secondary" href="login.php">Login</a>
            </div>
        </section>
    <?php elseif ((int) $user['is_verified'] !== 1): ?>
        <section class="hero compact">
            <h1>Hello, <?= e($user['name']) ?></h1>
            <p>Your account is pending admin approval.</p>
        </section>
    <?php else: ?>
        <section class="page-title">
            <h1>Latest Approved Places</h1>
            <p>Welcome back, <?= e($user['name']) ?>.</p>
        </section>

        <section class="cards">
            <?php if (!$posts): ?>
                <div class="notice">No approved posts are available yet.</div>
            <?php endif; ?>

            <?php foreach ($posts as $post): ?>
                <article class="card">
                    <h3><?= e($post['title']) ?></h3>
                    <p><strong>Country:</strong> <?= e($post['country']) ?></p>
                    <p><strong>Genre:</strong> <?= e($post['genre']) ?></p>
                    <p><strong>Cost:</strong> <?= e(ucfirst($post['cost_level'])) ?></p>
                    <p><?= e(mb_strimwidth($post['short_history'], 0, 130, '...')) ?></p>
                    <?php if ($user['role'] === 'user'): ?>
                        <button type="button" onclick="addToWishlist(<?= (int) $post['id'] ?>)">Add to Wishlist</button>
                    <?php endif; ?>
                </article>
            <?php endforeach; ?>
        </section>
    <?php endif; ?>

    <script src="../Public/JS/script.js"></script>
</body>
</html>
