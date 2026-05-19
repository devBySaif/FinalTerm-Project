<?php
include '../Model/mydb.php';  // ← Fixed
session_start();

// CREATE FAKE SESSION
$_SESSION["user_id"] = 1;
$_SESSION["name"] = "Test User";
$_SESSION["role"] = "user";
$_SESSION["is_verified"] = 1;

$mydb = new MyDB();
$conn = $mydb->createConn();

// Handle AJAX Add Comment
if(isset($_POST['action']) && $_POST['action'] == 'add_comment'){
    header('Content-Type: application/json');
    
    $postId = isset($_POST['post_id']) ? intval($_POST['post_id']) : 0;
    $content = isset($_POST['content']) ? trim($_POST['content']) : '';
    
    // Validation
    if(empty($content)){
        echo json_encode(['success' => false, 'error' => 'Comment cannot be empty']);
        $mydb->closeConn($conn);
        exit();
    }
    
    if(strlen($content) > 500){
        echo json_encode(['success' => false, 'error' => 'Comment too long (max 500 characters)']);
        $mydb->closeConn($conn);
        exit();
    }
    
    // XSS prevention
    $content = htmlspecialchars($content, ENT_QUOTES, 'UTF-8');
    
    $result = $mydb->addComment($postId, $_SESSION['user_id'], $content, $conn);
    
    if($result){
        $commentsResult = $mydb->getCommentsByPost($postId, $conn);
        $newComment = null;
        
        if($commentsResult->num_rows > 0){
            $newComment = $commentsResult->fetch_assoc();
        }
        
        echo json_encode(['success' => true, 'comment' => $newComment]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Failed to add comment']);
    }
    
    $mydb->closeConn($conn);
    exit();
}

// Handle AJAX Delete Comment
if(isset($_POST['action']) && $_POST['action'] == 'delete_comment'){
    header('Content-Type: application/json');
    
    $commentId = isset($_POST['comment_id']) ? intval($_POST['comment_id']) : 0;
    
    if($commentId == 0){
        echo json_encode(['success' => false, 'error' => 'Invalid comment ID']);
        $mydb->closeConn($conn);
        exit();
    }
    
    $result = $mydb->deleteComment($commentId, $_SESSION['user_id'], $conn);
    
    if($result && $conn->affected_rows > 0){
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Failed to delete comment']);
    }
    
    $mydb->closeConn($conn);
    exit();
}

// Regular page load
if(!isset($_GET['id'])){
    header("Location: ../View/browse_posts.php");  // ← Fixed
    exit();
}

$postId = $_GET['id'];
$postResult = $mydb->getPostById($postId, $conn);

if($postResult->num_rows == 0){
    header("Location: ../View/browse_posts.php");  // ← Fixed
    exit();
}

$post = $postResult->fetch_assoc();
$comments = $mydb->getCommentsByPost($postId, $conn);
$costResult = $mydb->getCostEstimate($postId, $conn);

if($costResult->num_rows > 0){
    $costData = $costResult->fetch_assoc();
    $baseCost = $costData['base_cost'];
} else {
    $baseCost = $mydb->getBaseCostByLevel($post['cost_level']);
}

$canComment = true;
?>