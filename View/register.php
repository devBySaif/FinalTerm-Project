<?php require_once __DIR__ . '/../Controller/session.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register | Travel Guide</title>
    <link rel="stylesheet" href="../Public/CSS/style.css">
</head>
<body>
    <?php include __DIR__ . '/partials_nav.php'; ?>

    <main class="form-container">
        <h2>Create Account</h2>
        <?php if ($error = flash('error')): ?>
            <p class="form-message"><?= e($error) ?></p>
        <?php endif; ?>
        <form method="POST" action="../Controller/AuthController.php" onsubmit="return validateRegister()">
            <input type="hidden" name="action" value="register">
            <label for="name">Name</label>
            <input id="name" name="name" type="text" autocomplete="name">

            <label for="email">Email</label>
            <input id="email" name="email" type="email" autocomplete="email">

            <label for="password">Password</label>
            <input id="password" name="password" type="password" autocomplete="new-password">

            <label for="confirm_password">Confirm Password</label>
            <input id="confirm_password" name="confirm_password" type="password" autocomplete="new-password">

            <label for="role">Role</label>
            <select id="role" name="role">
                <option value="">Select role</option>
                <option value="user">General User</option>
                <option value="scout">Scout</option>
                <option value="admin">Admin</option>
            </select>

            <button type="submit">Register</button>
        </form>
        <p>Already registered? <a href="login.php">Login</a></p>
    </main>

    <script src="../Public/JS/script.js"></script>
</body>
</html>
