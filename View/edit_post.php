<?php require __DIR__ . '/layout_header.php'; ?>

<section class="panel form-panel">
    <div class="panel-header">
        <div>
            <h2>Edit Post</h2>
            <p>Update the approved post details directly in the posts table.</p>
        </div>
        <a class="button" href="admin.php?page=posts">Back to Posts</a>
    </div>

    <form class="admin-form" method="post" action="admin.php?action=update_post" id="editPostForm" novalidate>
        <input type="hidden" name="csrf_token" value="<?= e($csrfToken) ?>">
        <input type="hidden" name="post_id" value="<?= e($post['id']) ?>">

        <label>
            <span>Title</span>
            <input type="text" name="title" value="<?= e($post['title'] ?? '') ?>" required>
            <?php if (!empty($errors['title'])): ?><small class="field-error"><?= e($errors['title']) ?></small><?php endif; ?>
        </label>

        <label>
            <span>Short History</span>
            <textarea name="short_history" rows="6" required><?= e($post['short_history'] ?? '') ?></textarea>
            <?php if (!empty($errors['short_history'])): ?><small class="field-error"><?= e($errors['short_history']) ?></small><?php endif; ?>
        </label>

        <div class="form-grid">
            <label>
                <span>Country</span>
                <input type="text" name="country" value="<?= e($post['country'] ?? '') ?>" required>
                <?php if (!empty($errors['country'])): ?><small class="field-error"><?= e($errors['country']) ?></small><?php endif; ?>
            </label>

            <label>
                <span>Genre</span>
                <input type="text" name="genre" value="<?= e($post['genre'] ?? '') ?>" required>
                <?php if (!empty($errors['genre'])): ?><small class="field-error"><?= e($errors['genre']) ?></small><?php endif; ?>
            </label>
        </div>

        <div class="form-grid">
            <label>
                <span>Cost Level</span>
                <select name="cost_level" required>
                    <?php foreach (['low', 'medium', 'high'] as $costLevel): ?>
                        <option value="<?= e($costLevel) ?>" <?= ($post['cost_level'] ?? '') === $costLevel ? 'selected' : '' ?>><?= e(ucfirst($costLevel)) ?></option>
                    <?php endforeach; ?>
                </select>
                <?php if (!empty($errors['cost_level'])): ?><small class="field-error"><?= e($errors['cost_level']) ?></small><?php endif; ?>
            </label>

            <label>
                <span>Status</span>
                <select name="status" required>
                    <?php foreach (['pending', 'approved', 'rejected'] as $status): ?>
                        <option value="<?= e($status) ?>" <?= ($post['status'] ?? '') === $status ? 'selected' : '' ?>><?= e(ucfirst($status)) ?></option>
                    <?php endforeach; ?>
                </select>
                <?php if (!empty($errors['status'])): ?><small class="field-error"><?= e($errors['status']) ?></small><?php endif; ?>
            </label>
        </div>

        <label>
            <span>Travel Medium Info</span>
            <textarea name="travel_medium_info" rows="4" required><?= e($post['travel_medium_info'] ?? '') ?></textarea>
            <?php if (!empty($errors['travel_medium_info'])): ?><small class="field-error"><?= e($errors['travel_medium_info']) ?></small><?php endif; ?>
        </label>

        <div class="form-actions">
            <button class="button primary" type="submit">Save Changes</button>
            <a class="button" href="admin.php?page=posts">Cancel</a>
        </div>
    </form>
</section>

<script>
document.getElementById('editPostForm').addEventListener('submit', function (event) {
    var form = event.currentTarget;
    var requiredFields = ['title', 'short_history', 'country', 'genre', 'travel_medium_info'];
    var missing = requiredFields.some(function (field) {
        return form[field].value.trim() === '';
    });

    if (missing) {
        event.preventDefault();
        alert('Please fill in every required post field.');
    }
});
</script>

<?php require __DIR__ . '/layout_footer.php'; ?>
