<?php require __DIR__ . '/layout_header.php'; ?>

<section class="panel">
    <div class="panel-header">
        <div>
            <h2>Comments</h2>
            <p>Remove comments that should not remain visible on published posts.</p>
        </div>
    </div>

    <div class="table-wrap">
        <table class="admin-table">
            <thead>
            <tr>
                <th>Post</th>
                <th>Commenter</th>
                <th>Comment</th>
                <th>Date</th>
                <th class="actions-col">Actions</th>
            </tr>
            </thead>
            <tbody>
            <?php if (empty($comments)): ?>
                <tr>
                    <td colspan="5" class="empty-cell">No comments found.</td>
                </tr>
            <?php endif; ?>

            <?php foreach ($comments as $comment): ?>
                <tr id="comment-row-<?= e($comment['id']) ?>">
                    <td><?= e($comment['post_title']) ?></td>
                    <td>
                        <strong><?= e($comment['commenter_name']) ?></strong>
                        <p class="muted-cell"><?= e($comment['commenter_email']) ?></p>
                    </td>
                    <td class="comment-content"><?= e($comment['content']) ?></td>
                    <td><?= e($comment['created_at']) ?></td>
                    <td class="actions-cell">
                        <button class="button small danger js-delete-comment" type="button" data-comment-id="<?= e($comment['id']) ?>">Delete</button>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</section>

<script>
document.querySelectorAll('.js-delete-comment').forEach(function (button) {
    button.addEventListener('click', function () {
        if (!confirm('Delete this comment?')) {
            return;
        }

        var formData = new URLSearchParams();
        formData.append('csrf_token', document.querySelector('meta[name="csrf-token"]').content);
        formData.append('comment_id', button.dataset.commentId);

        button.disabled = true;

        fetch('admin.php?action=delete_comment', {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: formData
        })
            .then(function (response) { return response.json(); })
            .then(function (data) {
                if (!data.success) {
                    alert(data.message || 'Comment delete failed.');
                    return;
                }

                var row = document.getElementById('comment-row-' + button.dataset.commentId);
                if (row) {
                    row.remove();
                }
            })
            .catch(function () {
                alert('Network error while deleting comment.');
            })
            .finally(function () {
                button.disabled = false;
            });
    });
});
</script>

<?php require __DIR__ . '/layout_footer.php'; ?>
