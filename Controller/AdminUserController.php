<?php

require_once __DIR__ . '/AdminBaseController.php';

class AdminUserController extends AdminBaseController
{
    public function index(array $errors = [], array $old = [])
    {
        $pageTitle = 'User Management';
        $activePage = 'users';
        $csrfToken = $this->csrfToken();
        $flash = $this->consumeFlash();
        $users = $this->model->getManageableUsers();

        $this->view('users.php', compact('pageTitle', 'activePage', 'csrfToken', 'flash', 'users', 'errors', 'old'));
    }

    public function create(array $errors = [], array $old = [])
    {
        $pageTitle = 'Add User';
        $activePage = 'users';
        $csrfToken = $this->csrfToken();
        $flash = $this->consumeFlash();

        $this->view('add_user.php', compact('pageTitle', 'activePage', 'csrfToken', 'flash', 'errors', 'old'));
    }

    public function store()
    {
        $input = [
            'name' => trim($_POST['name'] ?? ''),
            'email' => trim($_POST['email'] ?? ''),
            'password' => $_POST['password'] ?? '',
            'role' => trim($_POST['role'] ?? ''),
            'is_verified' => isset($_POST['is_verified']) ? (int) $_POST['is_verified'] : 0,
        ];

        $errors = $this->validateUserInput($input);

        if ($this->model->emailExists($input['email'])) {
            $errors['email'] = 'This email is already registered.';
        }

        if (!empty($errors)) {
            $this->create($errors, $input);
            return;
        }

        $this->model->createUser(
            $input['name'],
            strtolower($input['email']),
            password_hash($input['password'], PASSWORD_DEFAULT),
            $input['role'],
            $input['is_verified']
        );

        $this->setFlash('success', 'User created successfully.');
        $this->redirect('users');
    }

    public function toggleVerification()
    {
        $userId = filter_input(INPUT_POST, 'user_id', FILTER_VALIDATE_INT);
        if (!$userId) {
            $this->json(['success' => false, 'message' => 'Invalid user selected.'], 422);
        }

        $newStatus = $this->model->toggleUserVerification($userId);
        if ($newStatus === null) {
            $this->json(['success' => false, 'message' => 'User cannot be updated.'], 404);
        }

        $this->json([
            'success' => true,
            'message' => $newStatus ? 'User verified.' : 'User unverified.',
            'is_verified' => $newStatus,
        ]);
    }

    public function destroy()
    {
        $userId = filter_input(INPUT_POST, 'user_id', FILTER_VALIDATE_INT);
        $currentAdminId = $_SESSION['user_id'] ?? 0;

        if (!$userId) {
            $this->setFlash('error', 'Invalid user selected.');
            $this->redirect('users');
        }

        if ((int) $userId === (int) $currentAdminId) {
            $this->setFlash('error', 'You cannot delete your own admin account.');
            $this->redirect('users');
        }

        $deleted = $this->model->deleteUser($userId, $currentAdminId);
        $this->setFlash($deleted ? 'success' : 'error', $deleted ? 'User deleted.' : 'User was not found.');
        $this->redirect('users');
    }

    private function validateUserInput(array $input)
    {
        $errors = [];

        if ($input['name'] === '' || strlen($input['name']) < 2) {
            $errors['name'] = 'Name must be at least 2 characters.';
        }

        if (!filter_var($input['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'A valid email address is required.';
        }

        if (strlen($input['password']) < 8) {
            $errors['password'] = 'Password must be at least 8 characters.';
        }

        if (!in_array($input['role'], ['admin', 'scout', 'user'], true)) {
            $errors['role'] = 'Please select a valid role.';
        }

        if (!in_array($input['is_verified'], [0, 1], true)) {
            $errors['is_verified'] = 'Verification status is invalid.';
        }

        return $errors;
    }
}
