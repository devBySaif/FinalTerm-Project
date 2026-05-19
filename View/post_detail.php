<?php
include '../Controller/post_detail_process.php';  
?>

<!DOCTYPE html>
<html>
<head>
    <title><?php echo $post['title']; ?></title>
    <link rel="stylesheet" type="text/css" href="../Public/CSS/post_detail.css" />
</head>
<body>
    <h1><?php echo $post['title']; ?></h1>
    
    <!-- Post Details -->
    <div class="post-detail">
        <p><strong>Country:</strong> <?php echo $post['country']; ?></p>
        <p><strong>Genre:</strong> <?php echo $post['genre']; ?></p>
        <p><strong>Cost Level:</strong> <?php echo ucfirst($post['cost_level']); ?></p>
        <p><strong>Travel Medium:</strong> <?php echo $post['travel_medium_info']; ?></p>
        <div class="history">
            <strong>About this place:</strong>
            <p><?php echo $post['short_history']; ?></p>
        </div>
    </div>
    
    <!-- Cost Calculator -->
    <div class="cost-calculator">
        <h3>Probable Cost Calculator</h3>
        <p>Base Cost: $<span id="baseCost"><?php echo $baseCost; ?></span></p>
        
        <label for="travelers">Number of Travelers (1-10):</label>
        <input type="number" id="travelers" min="1" max="10" value="1" onchange="calculateCost()"><br><br>
        
        <label for="days">Number of Days:</label>
        <input type="number" id="days" min="1" value="7" onchange="calculateCost()"><br><br>
        
        <p class="total-cost"><strong>Estimated Total Cost: $<span id="totalCost">0</span></strong></p>
    </div>
    
    <hr>
    
    <!-- Comments Section -->
    <div class="comments-section">
        <h3>Comments</h3>
        
        <div class="comment-form">
            <h4>Leave a Comment</h4>
            <label for="commentName">Name:</label>
            <input type="text" id="commentName" value="<?php echo $_SESSION['name']; ?>" readonly><br><br>
            
            <label for="commentContent">Comment (max 500 characters):</label><br>
            <textarea id="commentContent" maxlength="500" rows="4" cols="50"></textarea><br><br>
            
            <button onclick="addComment(<?php echo $postId; ?>)">Post Comment</button>
        </div>
        
        <hr>
        
        <h4>All Comments:</h4>
        <div id="commentsList">
            <?php if($comments->num_rows > 0): ?>
                <?php foreach($comments as $comment): ?>
                    <div class="comment" id="comment-<?php echo $comment['id']; ?>">
                        <p class="comment-header">
                            <strong><?php echo $comment['name']; ?></strong> 
                            <span class="comment-date">- <?php echo $comment['created_at']; ?></span>
                        </p>
                        <p class="comment-content"><?php echo $comment['content']; ?></p>
                        <?php if($comment['user_id'] == $_SESSION['user_id']): ?>
                            <button class="delete-btn" onclick="deleteComment(<?php echo $comment['id']; ?>)">Delete</button>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No comments yet. Be the first to comment!</p>
            <?php endif; ?>
        </div>
    </div>
    
    <br><br>
    <a href="../View/browse_posts.php">⬅ Back to Browse Posts</a>  <!-- ← Fixed -->
    
    <script src="../Public/JS/post_detail.js"></script>  <!-- ← Fixed -->
    <script src="../Public/JS/cost_calculator.js"></script>  <!-- ← Fixed -->
</body>
</html>