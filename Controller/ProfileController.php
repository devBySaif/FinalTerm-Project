<?php

require_once __DIR__ . '/session.php';

$user = require_login();
$userModel = new User(Database::connect());

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../View/profile.php');
    exit;
}

$id = (int) $user['id'];
$name = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? '');
$currentPassword = $_POST['current_password'] ?? '';
$newPassword = $_POST['new_password'] ?? '';
$confirmPassword = $_POST['confirm_password'] ?? '';

if ($name === '' || $email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    flash('error', 'Name and a valid email are required.');
    header('Location: ../View/profile.php');
    exit;
}

if ($userModel->emailExistsForAnotherUser($email, $id)) {
    flash('error', 'That email is already used by another account.');
    header('Location: ../View/profile.php');
    exit;
}

$profilePicture = null;

if (!empty($_FILES['profile_picture']['name'])) {
    $file = $_FILES['profile_picture'];
    $allowedTypes = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
    $mimeType = mime_content_type($file['tmp_name']);

    if (!isset($allowedTypes[$mimeType])) {
        flash('error', 'Only JPG, PNG, and WEBP images are allowed.');
        header('Location: ../View/profile.php');
        exit;
    }

    if ($file['size'] > 2 * 1024 * 1024) {
        flash('error', 'Profile picture must be under 2MB.');
        header('Location: ../View/profile.php');
        exit;
    }

    $profilePicture = 'profile_' . $id . '_' . bin2hex(random_bytes(8)) . '.' . $allowedTypes[$mimeType];
    $target = __DIR__ . '/../Public/Uploads/' . $profilePicture;

    if (!move_uploaded_file($file['tmp_name'], $target)) {
        flash('error', 'Could not upload profile picture.');
        header('Location: ../View/profile.php');
        exit;
    }
}

$userModel->updateProfile($id, $name, $email, $profilePicture);

if ($currentPassword !== '' || $newPassword !== '' || $confirmPassword !== '') {
    $freshUser = $userModel->getById($id);

    if ($currentPassword === '' || !password_verify($currentPassword, $freshUser['password_hash'])) {
        flash('error', 'Current password is incorrect.');
        header('Location: ../View/profile.php');
        exit;
    }

    if (strlen($newPassword) < 8 || $newPassword !== $confirmPassword) {
        flash('error', 'New password must be at least 8 characters and both password fields must match.');
        header('Location: ../View/profile.php');
        exit;
    }

    $userModel->updatePassword($id, $newPassword);
}

$_SESSION['name'] = $name;
$_SESSION['email'] = $email;
flash('success', 'Profile updated successfully.');

header('Location: ../View/profile.php');
exit;
