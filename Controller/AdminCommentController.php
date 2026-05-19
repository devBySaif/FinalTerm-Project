<?php

require_once __DIR__ . '/AdminBaseController.php';

class AdminCommentController extends AdminBaseController
{
    public function index()
    {
        $pageTitle = 'Comment Moderation';
        $activePage = 'comments';
        $csrfToken = $this->csrfToken();
        $flash = $this->consumeFlash();
        $comments = $this->model->getComments();

        $this->view('comments.php', compact('pageTitle', 'activePage', 'csrfToken', 'flash', 'comments'));
    }

    public function destroy()
    {
        $commentId = filter_input(INPUT_POST, 'comment_id', FILTER_VALIDATE_INT);
        if (!$commentId) {
            $this->json(['success' => false, 'message' => 'Invalid comment selected.'], 422);
        }

        $deleted = $this->model->deleteComment($commentId);
        $this->json([
            'success' => $deleted,
            'message' => $deleted ? 'Comment deleted.' : 'Comment was not found.',
        ], $deleted ? 200 : 404);
    }
}
