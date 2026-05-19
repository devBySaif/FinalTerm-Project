<?php require __DIR__ . '/layout_header.php'; ?>

<?php
$roleCounts = ['admin' => 0, 'scout' => 0, 'user' => 0];
foreach ($stats['users_by_role'] as $roleRow) {
    $roleCounts[$roleRow['role']] = (int) $roleRow['total'];
}
?>

<section class="summary-grid">
    <article class="summary-card">
        <span class="summary-label">Admins</span>
        <strong><?= e($roleCounts['admin']) ?></strong>
    </article>
    <article class="summary-card">
        <span class="summary-label">Scouts</span>
        <strong><?= e($roleCounts['scout']) ?></strong>
    </article>
    <article class="summary-card">
        <span class="summary-label">General Users</span>
        <strong><?= e($roleCounts['user']) ?></strong>
    </article>
    <article class="summary-card accent">
        <span class="summary-label">Pending Requests</span>
        <strong><?= e($stats['pending_requests']) ?></strong>
    </article>
    <article class="summary-card">
        <span class="summary-label">Total Posts</span>
        <strong><?= e($stats['total_posts']) ?></strong>
    </article>
    <article class="summary-card">
        <span class="summary-label">Total Comments</span>
        <strong><?= e($stats['total_comments']) ?></strong>
    </article>
</section>

<section class="panel">
    <div class="panel-header">
        <div>
            <h2>Quick Actions</h2>
            <p>Manage the areas that usually need admin review.</p>
        </div>
    </div>
    <div class="quick-actions">
        <a class="button primary" href="admin.php?page=add_user">Add User</a>
        <a class="button" href="admin.php?page=posts">Review Posts</a>
        <a class="button" href="admin.php?page=comments">Moderate Comments</a>
    </div>
</section>

<?php require __DIR__ . '/layout_footer.php'; ?>
