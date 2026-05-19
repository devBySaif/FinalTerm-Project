<?php
include __DIR__ . '/../Controller/scout_auth.php';
include __DIR__ . '/../Model/ScoutRequestModel.php';

$posts = (new ScoutRequestModel())->getApprovedPostsByScout($_SESSION['user_id']);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Approved Posts</title>
    <link rel="stylesheet" href="../Public/CSS/scout_style.css">
</head>
<body>
<?php include __DIR__ . '/scout_nav.php'; ?>
<main class="page">
    <h1>Approved Posts</h1>
    <div class="post-grid">
        <?php foreach ($posts as $post): ?>
            <article class="post-card">
                <h2><?= e($post['title']); ?></h2>
                <p><?= e(substr($post['short_history'], 0, 140)); ?>...</p>
                <div class="meta">
                    <span><?= e($post['country']); ?></span>
                    <span><?= e($post['genre']); ?></span>
                    <span><?= e($post['cost_level']); ?></span>
                </div>
                <a class="btn small" href="scout_request_form.php?original_post_id=<?= (int)$post['id']; ?>">Request Changes</a>
            </article>
        <?php endforeach; ?>
        <?php if (!$posts): ?>
            <p class="empty">No approved posts found for this scout.</p>
        <?php endif; ?>
    </div>
</main>
</body>
</html>
