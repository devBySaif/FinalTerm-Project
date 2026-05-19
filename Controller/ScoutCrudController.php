<?php
include __DIR__ . '/../Model/ScoutRequestModel.php';

session_start();

$controller = new ScoutCrudController();
$controller->handle();

class ScoutCrudController
{
    private ScoutRequestModel $model;

    public function __construct()
    {
        $this->model = new ScoutRequestModel();
    }

    public function handle(): void
    {
        $this->requireScout();

        $action = $_GET['action'] ?? $_POST['action'] ?? '';

        if ($action === 'store') {
            $this->store();
        } elseif ($action === 'update') {
            $this->update();
        } elseif ($action === 'delete') {
            $this->delete();
        } else {
            header('Location: ../View/scout_dashboard.php');
        }
    }

    private function store(): void
    {
        $this->requireValidCsrf();
        [$data, $errors] = $this->validatedPostData();
        $originalPostId = isset($_POST['original_post_id']) && $_POST['original_post_id'] !== ''
            ? (int)$_POST['original_post_id']
            : null;

        if ($originalPostId !== null && !$this->model->getApprovedPost($originalPostId, $_SESSION['user_id'])) {
            $errors[] = 'Selected approved post was not found.';
        }

        if ($errors) {
            $this->respondError($errors);
            return;
        }

        $ok = $this->model->createRequest($_SESSION['user_id'], $data, $originalPostId);
        $this->respondSuccess($ok, 'Post request submitted successfully.', '../View/scout_requests.php');
    }

    private function update(): void
    {
        $this->requireValidCsrf();
        $requestId = (int)($_POST['request_id'] ?? 0);
        [$data, $errors] = $this->validatedPostData();

        if ($requestId <= 0) {
            $errors[] = 'Invalid request.';
        }

        if ($errors) {
            $this->respondError($errors);
            return;
        }

        $ok = $this->model->updateRequest($requestId, $_SESSION['user_id'], $data);
        $this->respondSuccess($ok, 'Post request updated successfully.', '../View/scout_requests.php');
    }

    private function delete(): void
    {
        $this->requireValidCsrf();
        $requestId = (int)($_POST['request_id'] ?? 0);
        $ok = $requestId > 0 && $this->model->deleteRequest($requestId, $_SESSION['user_id']);

        header('Content-Type: application/json');
        echo json_encode([
            'success' => $ok,
            'message' => $ok ? 'Request deleted.' : 'Only pending own requests can be deleted.'
        ]);
    }

    private function validatedPostData(): array
    {
        $allowedGenres = ['beach', 'mountain', 'city', 'historical', 'forest', 'religious', 'adventure'];
        $allowedCosts = ['low', 'medium', 'high'];
        $data = [
            'title' => trim($_POST['title'] ?? ''),
            'short_history' => trim($_POST['short_history'] ?? ''),
            'country' => trim($_POST['country'] ?? ''),
            'country_representation' => trim($_POST['country_representation'] ?? ''),
            'genre' => trim($_POST['genre'] ?? ''),
            'cost_level' => trim($_POST['cost_level'] ?? ''),
            'travel_medium_info' => trim($_POST['travel_medium_info'] ?? ''),
            'image' => trim($_POST['existing_image'] ?? '')
        ];
        $errors = [];

        if ($data['title'] === '' || strlen($data['title']) > 255) {
            $errors[] = 'Title is required and must be under 255 characters.';
        }
        if ($data['short_history'] === '') {
            $errors[] = 'Short history is required.';
        }
        if ($data['country'] === '' || strlen($data['country']) > 100) {
            $errors[] = 'Country is required and must be under 100 characters.';
        }
        if ($data['country_representation'] === '') {
            $errors[] = 'Country representation is required.';
        }
        if (!in_array($data['genre'], $allowedGenres, true)) {
            $errors[] = 'Please select a valid genre.';
        }
        if (!in_array($data['cost_level'], $allowedCosts, true)) {
            $errors[] = 'Please select a valid cost level.';
        }
        if ($data['travel_medium_info'] === '') {
            $errors[] = 'Travel medium information is required.';
        }

        if (isset($_FILES['image']) && $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE) {
            $upload = $this->handleImageUpload($_FILES['image']);
            if ($upload['error']) {
                $errors[] = $upload['error'];
            } else {
                $data['image'] = $upload['path'];
            }
        }

        return [$data, $errors];
    }

    private function handleImageUpload(array $file): array
    {
        if ($file['error'] !== UPLOAD_ERR_OK) {
            return ['error' => 'Image upload failed.'];
        }

        if ($file['size'] > 2 * 1024 * 1024) {
            return ['error' => 'Image must be 2MB or smaller.'];
        }

        $info = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($info, $file['tmp_name']);
        finfo_close($info);

        $extensions = [
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/webp' => 'webp'
        ];

        if (!isset($extensions[$mime])) {
            return ['error' => 'Only JPG, PNG, or WEBP images are allowed.'];
        }

        $uploadDir = __DIR__ . '/../Public/Uploads/posts/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $filename = 'post_' . $_SESSION['user_id'] . '_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $extensions[$mime];
        $target = $uploadDir . $filename;

        if (!move_uploaded_file($file['tmp_name'], $target)) {
            return ['error' => 'Could not save uploaded image.'];
        }

        return ['error' => null, 'path' => 'Public/Uploads/posts/' . $filename];
    }

    private function requireScout(): void
    {
        $isScout = isset($_SESSION['user_id'], $_SESSION['role'], $_SESSION['is_verified'])
            && $_SESSION['role'] === 'scout'
            && (int)$_SESSION['is_verified'] === 1;

        if (!$isScout) {
            header('Location: Scout_login_controller.php');
            exit;
        }
    }

    private function requireValidCsrf(): void
    {
        $token = $_POST['csrf_token'] ?? '';
        $valid = isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);

        if ($valid) {
            return;
        }

        if ($this->isAjax()) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'errors' => ['Invalid request token.']]);
            exit;
        }

        $this->flash('error', 'Invalid request token.');
        header('Location: ../View/scout_dashboard.php');
        exit;
    }

    private function respondError(array $errors): void
    {
        if ($this->isAjax()) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'errors' => $errors]);
            return;
        }

        $this->flash('error', implode(' ', $errors));
        header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? '../View/scout_request_form.php'));
    }

    private function respondSuccess(bool $ok, string $message, string $redirect): void
    {
        if ($this->isAjax()) {
            header('Content-Type: application/json');
            echo json_encode(['success' => $ok, 'message' => $ok ? $message : 'Action failed.']);
            return;
        }

        $this->flash($ok ? 'success' : 'error', $ok ? $message : 'Action failed.');
        header('Location: ' . $redirect);
    }

    private function isAjax(): bool
    {
        return strtolower($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') === 'xmlhttprequest';
    }

    private function flash(string $type, string $message): void
    {
        $_SESSION['flash'] = ['type' => $type, 'message' => $message];
    }
}
