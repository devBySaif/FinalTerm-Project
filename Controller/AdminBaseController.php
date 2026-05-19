<?php

include __DIR__ . '/../Model/Database.php';
include __DIR__ . '/../Model/AdminModel.php';

class AdminBaseController
{
    protected $model;
    protected $basePath;
    protected $loginPath;

    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $this->basePath = 'admin.php';
        $this->loginPath = 'Public/index3.php';
        $this->enforceAdminGate();
        $this->model = new AdminModel(Database::getConnection());

        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
    }

    protected function enforceAdminGate()
    {
        $isAjax = $this->isAjaxRequest();

        if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
            if ($isAjax) {
                $this->json(['success' => false, 'message' => 'Admin access required.'], 403);
            }

            header('Location: ' . $this->loginPath);
            exit;
        }
    }

    protected function verifyCsrfToken()
    {
        $token = $_POST['csrf_token'] ?? ($_SERVER['HTTP_X_CSRF_TOKEN'] ?? '');

        if (!hash_equals($this->csrfToken(), $token)) {
            if ($this->isAjaxRequest()) {
                $this->json(['success' => false, 'message' => 'Invalid security token.'], 419);
            }

            $this->setFlash('error', 'Invalid security token. Please try again.');
            $this->redirect($_GET['page'] ?? 'dashboard');
        }
    }

    protected function csrfToken()
    {
        return $_SESSION['csrf_token'] ?? '';
    }

    protected function isAjaxRequest()
    {
        return (
            isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
            strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest'
        ) || (
            isset($_SERVER['HTTP_ACCEPT']) &&
            strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false
        );
    }

    protected function json(array $payload, $statusCode = 200)
    {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($payload);
        exit;
    }

    protected function redirect($page, array $params = [])
    {
        $params = array_merge(['page' => $page], $params);
        header('Location: ' . $this->basePath . '?' . http_build_query($params));
        exit;
    }

    protected function setFlash($type, $message)
    {
        $_SESSION['flash'] = [
            'type' => $type,
            'message' => $message,
        ];
    }

    protected function consumeFlash()
    {
        $flash = $_SESSION['flash'] ?? null;
        unset($_SESSION['flash']);

        return $flash;
    }

    protected function view($path, array $data = [])
    {
        extract($data);
        require __DIR__ . '/../View/' . $path;
    }
}
