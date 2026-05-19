<?php include __DIR__ . '/../Controller/scout_auth.php'; ?>
<!DOCTYPE html>
<html>
<head>
    <title>Scout Dashboard</title>
    <link rel="stylesheet" href="../Public/CSS/scout_style.css">
</head>
<body>
<?php include __DIR__ . '/scout_nav.php'; ?>
<main class="page">
    <?= flash_message(); ?>
    <section class="hero-panel">
        <h1>Welcome, <?= e($_SESSION['name']); ?></h1>
        <p>Submit travel place information, track admin review status, and request changes for approved posts.</p>
        <div class="action-row">
            <a class="btn primary" href="scout_request_form.php">Create Post Request</a>
            <a class="btn" href="scout_requests.php">View My Requests</a>
        </div>
    </section>
</main>
</body>
</html>
