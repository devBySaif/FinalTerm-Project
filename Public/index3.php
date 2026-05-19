<?php

session_start();
include __DIR__ . '/../Model/Database.php';

if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
    header('Location: ../admin.php?page=dashboard');
    exit;
}

$error = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if (!filter_var($email, FILTER_VALIDATE_EMAIL) || $password === '') {
        $error = 'Please enter a valid email and password.';
    } else {
        try {
            $db = Database::getConnection();
            $stmt = $db->prepare(
                "SELECT id, name, email, password_hash, role, is_verified
                 FROM users
                 WHERE email = ?
                 LIMIT 1"
            );
            $stmt->execute([$email]);
            $user = $stmt->fetch();

            if (!$user || !password_verify($password, $user['password_hash'])) {
                $error = 'Invalid email or password.';
            } elseif ($user['role'] !== 'admin') {
                $error = 'Only admin users can access this dashboard.';
            } else {
                session_regenerate_id(true);
                $_SESSION['user_id'] = (int) $user['id'];
                $_SESSION['name'] = $user['name'];
                $_SESSION['role'] = $user['role'];
                $_SESSION['is_verified'] = (int) $user['is_verified'];

                header('Location: ../admin.php?page=dashboard');
                exit;
            }
        } catch (Throwable $exception) {
            $error = 'Database connection failed. Please check travel_guide database and tables.';
        }
    }
}

function e($value)
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login | Travel Guide</title>
    <link rel="stylesheet" href="CSS/admin-style.css?v=3">
</head>
<body class="login-page">
    <main class="login-card">
        <div class="brand-block login-brand">
            <span class="brand-mark">TG</span>
            <div>
                <strong>Travel Guide</strong>
                <span>Admin Login</span>
            </div>
        </div>

        <?php if ($error !== ''): ?>
            <div class="flash flash-error"><?= e($error) ?></div>
        <?php endif; ?>

        <form class="admin-form login-form" method="post" action="index3.php" id="loginForm" novalidate>
            <label>
                <span>Email</span>
                <input type="email" name="email" value="<?= e($email) ?>" required>
                <small class="js-error-message" id="loginEmailError"></small>
            </label>

            <label>
                <span>Password</span>
                <input type="password" name="password" required>
                <small class="js-error-message" id="loginPasswordError"></small>
            </label>

            <button class="button primary" type="submit">Login</button>
        </form>
    </main>
    <script src="JS/login-validation.js?v=3"></script>
</body>
</html>
