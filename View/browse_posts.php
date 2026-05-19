<?php
include '../Controller/browse_posts_process.php'; 
?>

<!DOCTYPE html>
<html>
<head>
    <title>Browse Travel Posts</title>
    <link rel="stylesheet" type="text/css" href="../Public/CSS/browse_posts.css" /> 
</head>
<body>
    <h1>Browse Travel Destinations</h1>
    <p>Welcome, <?php echo $_SESSION['name']; ?>!</p>
    
    <div class="controls">
        <!-- Search Box -->
        <div class="search-section">
            <label for="search">Search:</label>
            <input type="text" id="search" name="search" onkeyup="searchPosts()" placeholder="Search by title or country...">
        </div>
        
        <!-- Filters -->
        <div class="filter-section">
            <label for="countryFilter">Country:</label>
            <select id="countryFilter" onchange="applyFilters()">
                <option value="">All Countries</option>
                <?php foreach($countries as $country): ?>
                    <option value="<?php echo $country; ?>"><?php echo $country; ?></option>
                <?php endforeach; ?>
            </select>
            
            <label for="genreFilter">Genre:</label>
            <select id="genreFilter" onchange="applyFilters()">
                <option value="">All Genres</option>
                <?php foreach($genres as $genre): ?>
                    <option value="<?php echo $genre; ?>"><?php echo $genre; ?></option>
                <?php endforeach; ?>
            </select>
            
            <label>Cost Level:</label>
            <input type="radio" name="costFilter" value="" checked onchange="applyFilters()"> All
            <input type="radio" name="costFilter" value="low" onchange="applyFilters()"> Low
            <input type="radio" name="costFilter" value="medium" onchange="applyFilters()"> Medium
            <input type="radio" name="costFilter" value="high" onchange="applyFilters()"> High
        </div>
    </div>
    
    <!-- Posts Grid -->
    <div id="postsGrid">
        <?php if($posts->num_rows > 0): ?>
            <?php foreach($posts as $post): ?>
                <div class="post-card">
                    <h3><?php echo $post['title']; ?></h3>
                    <p><strong>Country:</strong> <?php echo $post['country']; ?></p>
                    <p><strong>Genre:</strong> <?php echo $post['genre']; ?></p>
                    <p><strong>Cost Level:</strong> <?php echo ucfirst($post['cost_level']); ?></p>
                    <p><?php echo substr($post['short_history'], 0, 150); ?>...</p>
                    <a href="../View/post_detail.php?id=<?php echo $post['id']; ?>" class="read-more">Read more</a> 
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No posts available.</p>
        <?php endif; ?>
    </div>
    
    <script src="../Public/JS/browse_posts.js"></script>
</body>
</html>