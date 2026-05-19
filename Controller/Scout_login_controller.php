<?php
include __DIR__ . '/../Model/ScoutRequestModel.php';

session_start();

$errorMessage = '';

if (isset($_POST['login'])) {
    $model = new ScoutRequestModel();
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if ($email == '' || $password == '') {
        $errorMessage = 'Email and password are required.';
    } else {
        $user = $model->findUserByEmail($email);

        if ($user) {
            $dbPassword = $user['password_hash'];

            if (password_verify($password, $dbPassword)) {
                if ($user['role'] == 'scout' && (int)$user['is_verified'] == 1) {
                    $_SESSION['user_id'] = (int)$user['id'];
                    $_SESSION['name'] = $user['name'];
                    $_SESSION['email'] = $user['email'];
                    $_SESSION['role'] = $user['role'];
                    $_SESSION['is_verified'] = (int)$user['is_verified'];

                    header('Location: ../View/scout_dashboard.php');
                    exit;
                } elseif ($user['role'] != 'scout') {
                    $errorMessage = 'Only scout users can login here.';
                } else {
                    $errorMessage = 'Your scout account is pending admin approval.';
                }
            } else {
                $errorMessage = 'Invalid email or password.';
            }
        } else {
            $errorMessage = 'Invalid email or password.';
        }
    }
}

include __DIR__ . '/../View/Scout_login.php';
