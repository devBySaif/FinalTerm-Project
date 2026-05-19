<?php require __DIR__ . '/layout_header.php'; ?>

<section class="panel">
    <div class="panel-header">
        <div>
            <h2>Users</h2>
            <p>Verify scouts and general users, or remove accounts when needed.</p>
        </div>
        <a class="button primary" href="admin.php?page=add_user">Add New User</a>
    </div>

    <div class="table-wrap">
        <table class="admin-table">
            <thead>
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Role</th>
                <th>Verified</th>
                <th>Joined</th>
                <th class="actions-col">Actions</th>
            </tr>
            </thead>
            <tbody>
            <?php if (empty($users)): ?>
                <tr>
                    <td colspan="6" class="empty-cell">No scout or general user accounts found.</td>
                </tr>
            <?php endif; ?>

            <?php foreach ($users as $user): ?>
                <tr id="user-row-<?= e($user['id']) ?>">
                    <td><?= e($user['name']) ?></td>
                    <td><?= e($user['email']) ?></td>
                    <td><span class="status-chip role-<?= e($user['role']) ?>"><?= e($user['role']) ?></span></td>
                    <td>
                        <span class="status-chip <?= $user['is_verified'] ? 'status-approved' : 'status-pending' ?>" data-user-status="<?= e($user['id']) ?>">
                            <?= $user['is_verified'] ? 'Verified' : 'Pending' ?>
                        </span>
                    </td>
                    <td><?= e($user['created_at']) ?></td>
                    <td class="actions-cell">
                        <button class="button small js-toggle-verify" type="button" data-user-id="<?= e($user['id']) ?>">
                            <?= $user['is_verified'] ? 'Unverify' : 'Verify' ?>
                        </button>
                        <form method="post" action="admin.php?action=delete_user" class="inline-form" onsubmit="return confirm('Delete this user and related data?');">
                            <input type="hidden" name="csrf_token" value="<?= e($csrfToken) ?>">
                            <input type="hidden" name="user_id" value="<?= e($user['id']) ?>">
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
document.querySelectorAll('.js-toggle-verify').forEach(function (button) {
    button.addEventListener('click', function () {
        var formData = new URLSearchParams();
        formData.append('csrf_token', document.querySelector('meta[name="csrf-token"]').content);
        formData.append('user_id', button.dataset.userId);

        button.disabled = true;

        fetch('admin.php?action=toggle_user_verification', {
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
                    alert(data.message || 'Verification update failed.');
                    return;
                }

                var status = document.querySelector('[data-user-status="' + button.dataset.userId + '"]');
                status.textContent = data.is_verified ? 'Verified' : 'Pending';
                status.className = 'status-chip ' + (data.is_verified ? 'status-approved' : 'status-pending');
                button.textContent = data.is_verified ? 'Unverify' : 'Verify';
            })
            .catch(function () {
                alert('Network error while updating verification.');
            })
            .finally(function () {
                button.disabled = false;
            });
    });
});
</script>

<?php require __DIR__ . '/layout_footer.php'; ?>
