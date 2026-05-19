<?php require __DIR__ . '/layout_header.php'; ?>

<section class="panel">
    <div class="panel-header">
        <div>
            <h2>Pending Post Requests</h2>
            <p>Approve valid scout submissions or reject requests that need rework.</p>
        </div>
    </div>

    <div class="table-wrap">
        <table class="admin-table">
            <thead>
            <tr>
                <th>Place</th>
                <th>Scout</th>
                <th>Country</th>
                <th>Genre</th>
                <th>Cost</th>
                <th>Requested</th>
                <th class="actions-col">Actions</th>
            </tr>
            </thead>
            <tbody>
            <?php if (empty($pendingRequests)): ?>
                <tr>
                    <td colspan="7" class="empty-cell">There are no pending post requests.</td>
                </tr>
            <?php endif; ?>

            <?php foreach ($pendingRequests as $request): ?>
                <?php $data = $request['decoded_post_data']; ?>
                <tr id="request-row-<?= e($request['id']) ?>">
                    <td>
                        <strong><?= e($data['title'] ?? 'Untitled request') ?></strong>
                        <p class="muted-cell"><?= e(substr($data['short_history'] ?? ($data['history'] ?? ''), 0, 90)) ?></p>
                    </td>
                    <td><?= e($request['scout_name']) ?></td>
                    <td><?= e($data['country'] ?? ($data['country_representation'] ?? '')) ?></td>
                    <td><?= e($data['genre'] ?? '') ?></td>
                    <td><?= e($data['cost_level'] ?? '') ?></td>
                    <td><?= e($request['requested_at']) ?></td>
                    <td class="actions-cell">
                        <button class="button small primary js-approve-request" type="button" data-request-id="<?= e($request['id']) ?>">Approve</button>
                        <form method="post" action="admin.php?action=reject_request" class="inline-form" onsubmit="return confirm('Reject this request?');">
                            <input type="hidden" name="csrf_token" value="<?= e($csrfToken) ?>">
                            <input type="hidden" name="request_id" value="<?= e($request['id']) ?>">
                            <button class="button small danger" type="submit">Reject</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</section>

<section class="panel">
    <div class="panel-header">
        <div>
            <h2>Published Posts</h2>
            <p>Edit approved content, change moderation status, or delete posts.</p>
        </div>
    </div>

    <div class="table-wrap">
        <table class="admin-table">
            <thead>
            <tr>
                <th>Title</th>
                <th>Scout</th>
                <th>Country</th>
                <th>Genre</th>
                <th>Cost</th>
                <th>Status</th>
                <th class="actions-col">Actions</th>
            </tr>
            </thead>
            <tbody>
            <?php if (empty($posts)): ?>
                <tr>
                    <td colspan="7" class="empty-cell">No posts have been published yet.</td>
                </tr>
            <?php endif; ?>

            <?php foreach ($posts as $post): ?>
                <tr>
                    <td>
                        <strong><?= e($post['title']) ?></strong>
                        <p class="muted-cell"><?= e(substr($post['short_history'], 0, 90)) ?></p>
                    </td>
                    <td><?= e($post['scout_name'] ?? 'Unknown') ?></td>
                    <td><?= e($post['country']) ?></td>
                    <td><?= e($post['genre']) ?></td>
                    <td><?= e($post['cost_level']) ?></td>
                    <td><span class="status-chip status-<?= e($post['status']) ?>"><?= e($post['status']) ?></span></td>
                    <td class="actions-cell">
                        <a class="button small" href="admin.php?page=edit_post&id=<?= e($post['id']) ?>">Edit</a>
                        <form method="post" action="admin.php?action=delete_post" class="inline-form" onsubmit="return confirm('Delete this post and its related comments/wishlist entries?');">
                            <input type="hidden" name="csrf_token" value="<?= e($csrfToken) ?>">
                            <input type="hidden" name="post_id" value="<?= e($post['id']) ?>">
                            <button class="button small danger" type="submit">Delete</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</section>

<script>
document.querySelectorAll('.js-approve-request').forEach(function (button) {
    button.addEventListener('click', function () {
        if (!confirm('Approve this post request?')) {
            return;
        }

        var formData = new URLSearchParams();
        formData.append('csrf_token', document.querySelector('meta[name="csrf-token"]').content);
        formData.append('request_id', button.dataset.requestId);

        button.disabled = true;

        fetch('admin.php?action=approve_request', {
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
                    alert(data.message || 'Request approval failed.');
                    return;
                }

                var row = document.getElementById('request-row-' + button.dataset.requestId);
                if (row) {
                    row.remove();
                }
                alert(data.message);
            })
            .catch(function () {
                alert('Network error while approving request.');
            })
            .finally(function () {
                button.disabled = false;
            });
    });
});
</script>

<?php require __DIR__ . '/layout_footer.php'; ?>
