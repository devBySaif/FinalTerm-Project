<?php require __DIR__ . '/layout_header.php'; ?>

<section class="panel form-panel">
    <div class="panel-header">
        <div>
            <h2>Add New User</h2>
            <p>Create an admin, scout, or general user account manually.</p>
        </div>
        <a class="button" href="admin.php?page=users">Back to Users</a>
    </div>

    <form class="admin-form" method="post" action="admin.php?action=add_user" id="addUserForm" novalidate>
        <input type="hidden" name="csrf_token" value="<?= e($csrfToken) ?>">

        <label>
            <span>Name</span>
            <input type="text" name="name" value="<?= e($old['name'] ?? '') ?>" required minlength="2">
            <?php if (!empty($errors['name'])): ?><small class="field-error"><?= e($errors['name']) ?></small><?php endif; ?>
            <small class="js-error-message" id="addUserNameError"></small>
        </label>

        <label>
            <span>Email</span>
            <input type="email" name="email" value="<?= e($old['email'] ?? '') ?>" required>
            <?php if (!empty($errors['email'])): ?><small class="field-error"><?= e($errors['email']) ?></small><?php endif; ?>
            <small class="js-error-message" id="addUserEmailError"></small>
        </label>

        <label>
            <span>Password</span>
            <input type="password" name="password" required minlength="8">
            <?php if (!empty($errors['password'])): ?><small class="field-error"><?= e($errors['password']) ?></small><?php endif; ?>
            <small class="js-error-message" id="addUserPasswordError"></small>
        </label>

        <label>
            <span>Role</span>
            <select name="role" required>
                <?php $selectedRole = $old['role'] ?? 'user'; ?>
                <option value="user" <?= $selectedRole === 'user' ? 'selected' : '' ?>>General User</option>
                <option value="scout" <?= $selectedRole === 'scout' ? 'selected' : '' ?>>Scout</option>
                <option value="admin" <?= $selectedRole === 'admin' ? 'selected' : '' ?>>Admin</option>
            </select>
            <?php if (!empty($errors['role'])): ?><small class="field-error"><?= e($errors['role']) ?></small><?php endif; ?>
            <small class="js-error-message" id="addUserRoleError"></small>
        </label>

        <label>
            <span>Verification Status</span>
            <?php $selectedVerified = (int) ($old['is_verified'] ?? 1); ?>
            <select name="is_verified" required>
                <option value="1" <?= $selectedVerified === 1 ? 'selected' : '' ?>>Verified</option>
                <option value="0" <?= $selectedVerified === 0 ? 'selected' : '' ?>>Pending</option>
            </select>
            <?php if (!empty($errors['is_verified'])): ?><small class="field-error"><?= e($errors['is_verified']) ?></small><?php endif; ?>
            <small class="js-error-message" id="addUserVerifiedError"></small>
        </label>

        <div class="form-actions">
            <button class="button primary" type="submit">Create User</button>
            <a class="button" href="admin.php?page=users">Cancel</a>
        </div>
    </form>
</section>

<script src="Public/JS/add-user-validation.js?v=3"></script>

<?php require __DIR__ . '/layout_footer.php'; ?>
