<?php

require_once __DIR__ . '/AdminBaseController.php';

class AdminDashboardController extends AdminBaseController
{
    public function index()
    {
        $pageTitle = 'Dashboard';
        $activePage = 'dashboard';
        $csrfToken = $this->csrfToken();
        $flash = $this->consumeFlash();
        $stats = $this->model->getDashboardStats();

        $this->view('dashboard.php', compact('pageTitle', 'activePage', 'csrfToken', 'flash', 'stats'));
    }
}
