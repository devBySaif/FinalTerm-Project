<?php

require_once __DIR__ . '/AdminBaseController.php';
require_once __DIR__ . '/AdminDashboardController.php';
require_once __DIR__ . '/AdminUserController.php';
require_once __DIR__ . '/AdminPostController.php';
require_once __DIR__ . '/AdminCommentController.php';

class AdminController extends AdminBaseController
{
    public function handle()
    {
        $action = $_GET['action'] ?? '';

        if ($action !== '') {
            $this->handleAction($action);
            return;
        }

        $this->renderPage($_GET['page'] ?? 'dashboard');
    }

    private function handleAction($action)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->json(['success' => false, 'message' => 'Invalid request method.'], 405);
        }

        $this->verifyCsrfToken();

        switch ($action) {
            case 'add_user':
                (new AdminUserController())->store();
                break;

            case 'toggle_user_verification':
                (new AdminUserController())->toggleVerification();
                break;

            case 'delete_user':
                (new AdminUserController())->destroy();
                break;

            case 'approve_request':
                (new AdminPostController())->approveRequest();
                break;

            case 'reject_request':
                (new AdminPostController())->rejectRequest();
                break;

            case 'update_post':
                (new AdminPostController())->update();
                break;

            case 'delete_post':
                (new AdminPostController())->destroy();
                break;

            case 'delete_comment':
                (new AdminCommentController())->destroy();
                break;

            default:
                $this->json(['success' => false, 'message' => 'Unknown admin action.'], 404);
        }
    }

    private function renderPage($page)
    {
        switch ($page) {
            case 'dashboard':
                (new AdminDashboardController())->index();
                break;

            case 'users':
                (new AdminUserController())->index();
                break;

            case 'add_user':
                (new AdminUserController())->create();
                break;

            case 'posts':
                (new AdminPostController())->index();
                break;

            case 'edit_post':
                (new AdminPostController())->edit();
                break;

            case 'comments':
                (new AdminCommentController())->index();
                break;

            default:
                http_response_code(404);
                echo 'Admin page not found.';
        }
    }
}









