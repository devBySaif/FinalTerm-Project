<?php
include __DIR__ . '/../Controller/scout_auth.php';
include __DIR__ . '/../Model/ScoutRequestModel.php';

$requests = (new ScoutRequestModel())->getRequestsByScout($_SESSION['user_id']);
?>
<!DOCTYPE html>
<html>
<head>
    <title>My Scout Requests</title>
    <meta name="csrf-token" content="<?= e($_SESSION['csrf_token']); ?>">
    <link rel="stylesheet" href="../Public/CSS/scout_style.css">
</head>
<body>
<?php include __DIR__ . '/scout_nav.php'; ?>
<main class="page">
    <?= flash_message(); ?>
    <div class="title-row">
        <h1>My Requests</h1>
        <a class="btn primary" href="scout_request_form.php">New Request</a>
    </div>

    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Country</th>
                    <th>Genre</th>
                    <th>Status</th>
                    <th>Requested</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($requests as $request): $data = $request['post_data_array']; ?>
                    <tr id="request-<?= (int)$request['id']; ?>">
                        <td><?= e($data['title'] ?? 'Untitled'); ?></td>
                        <td><?= e($data['country'] ?? ''); ?></td>
                        <td><?= e($data['genre'] ?? ''); ?></td>
                        <td><span class="status <?= e($request['status']); ?>"><?= e($request['status']); ?></span></td>
                        <td><?= e($request['requested_at']); ?></td>
                        <td>
                            <?php if ($request['status'] === 'pending'): ?>
                                <a class="btn small" href="scout_request_form.php?id=<?= (int)$request['id']; ?>">Edit</a>
                                <button class="btn small danger delete-request" data-id="<?= (int)$request['id']; ?>">Delete</button>
                            <?php else: ?>
                                <span class="muted">Locked</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (!$requests): ?>
                    <tr><td colspan="6" class="empty">No requests submitted yet.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</main>
<script src="../Public/JS/scout.js"></script>
</body>
</html>
