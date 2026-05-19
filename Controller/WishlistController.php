<?php

require_once __DIR__ . '/session.php';
require_once __DIR__ . '/../Model/Wishlist.php';

header('Content-Type: application/json');

$user = current_user();

if (!$user || $user['role'] !== 'user' || (int) $user['is_verified'] !== 1) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Only verified general users can use wishlist.']);
    exit;
}

$action = $_GET['action'] ?? $_POST['action'] ?? '';
$postId = filter_input(INPUT_POST, 'post_id', FILTER_VALIDATE_INT);

if (!$postId) {
    echo json_encode(['success' => false, 'message' => 'Post ID is required.']);
    exit;
}

$wishlist = new Wishlist(Database::connect());

if ($action === 'add') {
    $result = $wishlist->add((int) $user['id'], $postId);
    echo json_encode([
        'success' => $result,
        'message' => $result ? 'Added to wishlist.' : 'This post is already in your wishlist.',
    ]);
    exit;
}

if ($action === 'remove') {
    $result = $wishlist->remove((int) $user['id'], $postId);
    echo json_encode([
        'success' => $result,
        'message' => $result ? 'Removed from wishlist.' : 'Item was not found in your wishlist.',
    ]);
    exit;
}

http_response_code(400);
echo json_encode(['success' => false, 'message' => 'Invalid wishlist action.']);
