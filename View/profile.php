<?php
require_once __DIR__ . '/../Controller/session.php';
$user = require_login();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile | Travel Guide</title>
    <link rel="stylesheet" href="../Public/CSS/style.css">
</head>
<body>
    <?php include __DIR__ . '/partials_nav.php'; ?>

    <main class="form-container wide">
        <h2>My Profile</h2>
        <?php if ($success = flash('success')): ?>
            <p class="form-message success"><?= e($success) ?></p>
        <?php endif; ?>
        <?php if ($error = flash('error')): ?>
            <p class="form-message"><?= e($error) ?></p>
        <?php endif; ?>
        <?php if ((int) $user['is_verified'] !== 1): ?>
            <div class="notice inline">Your account is pending admin approval.</div>
        <?php endif; ?>

        <?php if (!empty($user['profile_picture'])): ?>
            <img class="avatar" src="../Public/Uploads/<?= e($user['profile_picture']) ?>" alt="Profile picture">
        <?php endif; ?>

        <form method="POST" action="../Controller/ProfileController.php" enctype="multipart/form-data" onsubmit="return validateProfile()">
            <label for="profile_name">Name</label>
            <input id="profile_name" name="name" type="text" value="<?= e($user['name']) ?>">

            <label for="profile_email">Email</label>
            <input id="profile_email" name="email" type="email" value="<?= e($user['email']) ?>">

            <label for="profile_picture">Profile Picture</label>
            <input id="profile_picture" name="profile_picture" type="file" accept="image/jpeg,image/png,image/webp">

            <label for="current_password">Current Password</label>
            <input id="current_password" name="current_password" type="password" autocomplete="current-password">

            <label for="new_password">New Password</label>
            <input id="new_password" name="new_password" type="password" autocomplete="new-password">

            <label for="profile_confirm_password">Confirm New Password</label>
            <input id="profile_confirm_password" name="confirm_password" type="password" autocomplete="new-password">

            <button type="submit">Save Profile</button>
        </form>
    </main>

    <script src="../Public/JS/script.js"></script>
</body>
</html>
