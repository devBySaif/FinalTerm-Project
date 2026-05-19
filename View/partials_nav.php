<?php $navUser = current_user(); ?>
<nav>
    <a class="brand" href="home.php">Travel Guide</a>
    <ul>
        <li><a href="home.php">Home</a></li>
        <?php if (!$navUser): ?>
            <li><a href="login.php">Login</a></li>
            <li><a href="register.php">Register</a></li>
        <?php else: ?>
            <?php if ($navUser['role'] === 'user' && (int) $navUser['is_verified'] === 1): ?>
                <li><a href="wishlist.php">Wishlist</a></li>
            <?php endif; ?>
            <li><a href="profile.php">Profile</a></li>
            <li><a href="../Controller/AuthController.php?action=logout">Logout</a></li>
        <?php endif; ?>
    </ul>
</nav>
