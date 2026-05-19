<?php
require_once __DIR__ . '/../Controller/session.php';
require_once __DIR__ . '/../Model/Wishlist.php';

$user = require_verified_user_role();
$items = (new Wishlist(Database::connect()))->getForUser((int) $user['id']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Wishlist | Travel Guide</title>
    <link rel="stylesheet" href="../Public/CSS/style.css">
</head>
<body>
    <?php include __DIR__ . '/partials_nav.php'; ?>

    <section class="page-title">
        <h1>My Wishlist</h1>
        <p>Saved travel places for later planning.</p>
    </section>

    <section class="cards" id="wishlistGrid">
        <?php if (!$items): ?>
            <div class="notice">Your wishlist is empty.</div>
        <?php endif; ?>

        <?php foreach ($items as $item): ?>
            <article class="card" data-post-id="<?= (int) $item['post_id'] ?>">
                <h3><?= e($item['title']) ?></h3>
                <p><strong>Country:</strong> <?= e($item['country']) ?></p>
                <p><strong>Cost:</strong> <?= e(ucfirst($item['cost_level'])) ?></p>
                <button type="button" onclick="removeItem(this, <?= (int) $item['post_id'] ?>)">Remove</button>
            </article>
        <?php endforeach; ?>
    </section>

    <script src="../Public/JS/script.js"></script>
</body>
</html>
