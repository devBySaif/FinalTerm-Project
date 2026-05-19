<?php require_once __DIR__ . '/../Controller/session.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Travel Guide</title>
    <link rel="stylesheet" href="../Public/CSS/style.css">
</head>
<body>
    <?php include __DIR__ . '/partials_nav.php'; ?>

    <main class="form-container">
        <h2>Login</h2>
        <?php if ($success = flash('success')): ?>
            <p class="form-message success"><?= e($success) ?></p>
        <?php endif; ?>
        <?php if ($error = flash('error')): ?>
            <p class="form-message"><?= e($error) ?></p>
        <?php endif; ?>
        <form method="POST" action="../Controller/AuthController.php" onsubmit="return validateLogin()">
            <input type="hidden" name="action" value="login">

            <label for="login_email">Email</label>
            <input id="login_email" name="email" type="email" autocomplete="email">

            <label for="login_password">Password</label>
            <input id="login_password" name="password" type="password" autocomplete="current-password">

            <label class="remember">
                <input type="checkbox" name="remember" value="1"> Remember me
            </label>

            <button type="submit">Login</button>
        </form>
        <p>Need an account? <a href="register.php">Register</a></p>
    </main>

    <script src="../Public/JS/script.js"></script>
</body>
</html>
