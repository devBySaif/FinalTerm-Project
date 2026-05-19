<?php

require_once __DIR__ . '/session.php';

$pdo = Database::connect();
$userModel = new User($pdo);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'register') {
        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';
        $role = $_POST['role'] ?? '';
        $allowedRoles = ['admin', 'scout', 'user'];

        if ($name === '' || $email === '' || $password === '' || $confirmPassword === '' || $role === '') {
            flash('error', 'All fields are required.');
            header('Location: ../View/register.php');
            exit;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            flash('error', 'Enter a valid email address.');
            header('Location: ../View/register.php');
            exit;
        }

        if (!in_array($role, $allowedRoles, true)) {
            flash('error', 'Please select a valid role.');
            header('Location: ../View/register.php');
            exit;
        }

        if (strlen($password) < 8 || $password !== $confirmPassword) {
            flash('error', 'Password must be at least 8 characters and both passwords must match.');
            header('Location: ../View/register.php');
            exit;
        }

        if ($userModel->findByEmail($email)) {
            flash('error', 'This email is already registered.');
            header('Location: ../View/register.php');
            exit;
        }

        $userModel->createUser($name, $email, $password, $role);
        flash('success', 'Registration complete. Please login. Your account is pending admin approval.');
        header('Location: ../View/login.php');
        exit;
    }

    if ($action === 'login') {
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        if ($email === '' || $password === '') {
            flash('error', 'Email and password are required.');
            header('Location: ../View/login.php');
            exit;
        }

        $user = $userModel->authenticate($email, $password);

        if (!$user) {
            flash('error', 'Invalid email or password.');
            header('Location: ../View/login.php');
            exit;
        }

        login_user($user);

        if (!empty($_POST['remember'])) {
            remember_user((int) $user['id']);
        }

        if ((int) $user['is_verified'] !== 1) {
            flash('notice', 'Your account is pending admin approval.');
        }

        header('Location: ../View/home.php');
        exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'GET' && ($_GET['action'] ?? '') === 'logout') {
    if (isset($_SESSION['user_id'])) {
        $userModel->clearRememberToken((int) $_SESSION['user_id']);
    }

    clear_remember_cookie();
    $_SESSION = [];
    session_destroy();
    header('Location: ../View/login.php');
    exit;
}

header('Location: ../View/login.php');
exit;
