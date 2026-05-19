<?php
class MyDB {

    function createConn(){
        $DBHOST = "localhost";
        $DBUSER = "root";
        $DBPASS = "";
        $DBNAME = "travel_guide";
        $conn = new mysqli($DBHOST, $DBUSER, $DBPASS, $DBNAME);
        return $conn;
    } 
    
    function createUser($username, $email, $password, $file, $conn){
        $sql = "INSERT INTO users (username, email, password, file) VALUES ('$username', '$email', '$password', '$file')";
        return $conn->query($sql);
    }
    
    function getUser($username, $conn){
        $sql = "SELECT * FROM users WHERE username='$username'";
        return $conn->query($sql);
    }

    function updateUser($username, $email, $password, $file, $conn){
        $sql = "UPDATE users SET email='$email', password='$password', file='$file' WHERE username='$username'";
        return $conn->query($sql);
    }

    function searchUser($username, $conn){
        $sql = "SELECT * FROM users WHERE username='$username'";
        return $conn->query($sql);
    }
    
    // ===== NEW FUNCTIONS FOR TASK 4 =====
    
    // Get all approved posts
    function getApprovedPosts($conn){
        $sql = "SELECT * FROM posts WHERE status='approved' ORDER BY created_at DESC";
        return $conn->query($sql);
    }
    
    // Get single post by ID
    function getPostById($id, $conn){
        $sql = "SELECT * FROM posts WHERE id='$id' AND status='approved'";
        return $conn->query($sql);
    }
    
    // Search posts by title or country
    function searchPosts($query, $conn){
        $searchTerm = "%$query%";
        $sql = "SELECT * FROM posts WHERE status='approved' AND (title LIKE '$searchTerm' OR country LIKE '$searchTerm')";
        return $conn->query($sql);
    }
    
    // Filter posts by country, genre, cost_level
    function filterPosts($country, $genre, $costLevel, $conn){
        $sql = "SELECT * FROM posts WHERE status='approved'";
        $conditions = [];
        
        if(!empty($country)){
            $conditions[] = "country='$country'";
        }
        if(!empty($genre)){
            $conditions[] = "genre='$genre'";
        }
        if(!empty($costLevel)){
            $conditions[] = "cost_level='$costLevel'";
        }
        
        if(count($conditions) > 0){
            $sql .= " AND " . implode(" AND ", $conditions);
        }
        
        return $conn->query($sql);
    }
    
    // Get comments for a post
    function getCommentsByPost($postId, $conn){
        $sql = "SELECT c.*, u.name FROM comments c 
                JOIN users u ON c.user_id = u.id 
                WHERE c.post_id='$postId' 
                ORDER BY c.created_at DESC";
        return $conn->query($sql);
    }
    
    // Add comment
    function addComment($postId, $userId, $content, $conn){
        $sql = "INSERT INTO comments (post_id, user_id, content, created_at) 
                VALUES ('$postId', '$userId', '$content', NOW())";
        return $conn->query($sql);
    }
    
    // Delete comment (user can only delete their own)
    function deleteComment($commentId, $userId, $conn){
        $sql = "DELETE FROM comments WHERE id='$commentId' AND user_id='$userId'";
        return $conn->query($sql);
    }
    
    // Get cost estimate for a post
    function getCostEstimate($postId, $conn){
        $sql = "SELECT * FROM cost_estimates WHERE post_id='$postId'";
        return $conn->query($sql);
    }
    
    // Get base cost by cost level mapping
    function getBaseCostByLevel($costLevel){
        $costMapping = [
            'low' => 500,
            'medium' => 1500,
            'high' => 3000
        ];
        return isset($costMapping[$costLevel]) ? $costMapping[$costLevel] : 1500;
    }

    function closeConn($conn){
        $conn->close();
    }
}
?>