<?php

require_once __DIR__ . '/AdminBaseController.php';

class AdminPostController extends AdminBaseController
{
    public function index()
    {
        $pageTitle = 'Post Moderation';
        $activePage = 'posts';
        $csrfToken = $this->csrfToken();
        $flash = $this->consumeFlash();
        $pendingRequests = $this->decoratePostRequests($this->model->getPendingPostRequests());
        $posts = $this->model->getAllPosts();

        $this->view('posts.php', compact('pageTitle', 'activePage', 'csrfToken', 'flash', 'pendingRequests', 'posts'));
    }

    public function edit(array $errors = [])
    {
        $postId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        if (!$postId) {
            $this->setFlash('error', 'Invalid post selected.');
            $this->redirect('posts');
        }

        $post = $this->model->getPostById($postId);
        if (!$post) {
            $this->setFlash('error', 'Post not found.');
            $this->redirect('posts');
        }

        $pageTitle = 'Edit Post';
        $activePage = 'posts';
        $csrfToken = $this->csrfToken();
        $flash = $this->consumeFlash();

        $this->view('edit_post.php', compact('pageTitle', 'activePage', 'csrfToken', 'flash', 'post', 'errors'));
    }

    public function approveRequest()
    {
        $requestId = filter_input(INPUT_POST, 'request_id', FILTER_VALIDATE_INT);
        if (!$requestId) {
            $this->json(['success' => false, 'message' => 'Invalid request selected.'], 422);
        }

        $result = $this->model->approvePostRequest($requestId);
        $this->json($result, $result['success'] ? 200 : 422);
    }

    public function rejectRequest()
    {
        $requestId = filter_input(INPUT_POST, 'request_id', FILTER_VALIDATE_INT);
        if (!$requestId) {
            $this->setFlash('error', 'Invalid request selected.');
            $this->redirect('posts');
        }

        $rejected = $this->model->rejectPostRequest($requestId);
        $this->setFlash($rejected ? 'success' : 'error', $rejected ? 'Request rejected.' : 'Pending request was not found.');
        $this->redirect('posts');
    }

    public function update()
    {
        $postId = filter_input(INPUT_POST, 'post_id', FILTER_VALIDATE_INT);
        if (!$postId) {
            $this->setFlash('error', 'Invalid post selected.');
            $this->redirect('posts');
        }

        $data = [
            'title' => trim($_POST['title'] ?? ''),
            'short_history' => trim($_POST['short_history'] ?? ''),
            'country' => trim($_POST['country'] ?? ''),
            'genre' => trim($_POST['genre'] ?? ''),
            'cost_level' => trim($_POST['cost_level'] ?? ''),
            'travel_medium_info' => trim($_POST['travel_medium_info'] ?? ''),
            'status' => trim($_POST['status'] ?? ''),
        ];

        $errors = $this->validatePostInput($data);
        if (!empty($errors)) {
            $_GET['id'] = $postId;
            $post = array_merge($this->model->getPostById($postId) ?: [], $data);
            $pageTitle = 'Edit Post';
            $activePage = 'posts';
            $csrfToken = $this->csrfToken();
            $flash = $this->consumeFlash();
            $this->view('edit_post.php', compact('pageTitle', 'activePage', 'csrfToken', 'flash', 'post', 'errors'));
            return;
        }

        $this->model->updatePost($postId, $data);
        $this->setFlash('success', 'Post updated successfully.');
        $this->redirect('posts');
    }

    public function destroy()
    {
        $postId = filter_input(INPUT_POST, 'post_id', FILTER_VALIDATE_INT);
        if (!$postId) {
            $this->setFlash('error', 'Invalid post selected.');
            $this->redirect('posts');
        }

        $deleted = $this->model->deletePost($postId);
        $this->setFlash($deleted ? 'success' : 'error', $deleted ? 'Post deleted.' : 'Post was not found.');
        $this->redirect('posts');
    }

    private function validatePostInput(array $data)
    {
        $errors = [];

        foreach (['title', 'short_history', 'country', 'genre', 'travel_medium_info'] as $field) {
            if ($data[$field] === '') {
                $errors[$field] = 'This field is required.';
            }
        }

        if (!in_array($data['cost_level'], ['low', 'medium', 'high'], true)) {
            $errors['cost_level'] = 'Please select a valid cost level.';
        }

        if (!in_array($data['status'], ['pending', 'approved', 'rejected'], true)) {
            $errors['status'] = 'Please select a valid status.';
        }

        return $errors;
    }

    private function decoratePostRequests(array $requests)
    {
        foreach ($requests as &$request) {
            $decoded = json_decode($request['post_data'], true);
            $request['decoded_post_data'] = is_array($decoded) ? $decoded : [];
        }

        return $requests;
    }
}
