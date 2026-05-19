<?php
include __DIR__ . '/../Controller/scout_auth.php';
include __DIR__ . '/../Model/ScoutRequestModel.php';

$model = new ScoutRequestModel();
$request = null;
$data = [];
$action = '../Controller/ScoutCrudController.php?action=store';
$heading = 'Create Post Request';
$originalPostId = isset($_GET['original_post_id']) ? (int)$_GET['original_post_id'] : null;

if (isset($_GET['id'])) {
    $request = $model->getRequest((int)$_GET['id'], $_SESSION['user_id']);
    if (!$request || $request['status'] !== 'pending') {
        $_SESSION['flash'] = ['type' => 'error', 'message' => 'Only pending own requests can be edited.'];
        header('Location: scout_requests.php');
        exit;
    }

    $data = $request['post_data_array'];
    $action = '../Controller/ScoutCrudController.php?action=update';
    $heading = 'Edit Post Request';
}
?>
<!DOCTYPE html>
<html>
<head>
    <title><?= e($heading); ?></title>
    <link rel="stylesheet" href="../Public/CSS/scout_style.css">
</head>
<body>
<?php include __DIR__ . '/scout_nav.php'; ?>
<main class="page narrow">
    <?= flash_message(); ?>
    <h1><?= e($heading); ?></h1>
    <form class="scout-form" id="requestForm" action="<?= e($action); ?>" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="csrf_token" value="<?= e($_SESSION['csrf_token']); ?>">
        <?php if ($request): ?>
            <input type="hidden" name="request_id" value="<?= (int)$request['id']; ?>">
            <input type="hidden" name="existing_image" value="<?= e($data['image'] ?? ''); ?>">
        <?php endif; ?>
        <?php if ($originalPostId): ?>
            <input type="hidden" name="original_post_id" value="<?= (int)$originalPostId; ?>">
            <div class="alert alert-info">This will be submitted as a change request for approved post #<?= (int)$originalPostId; ?>.</div>
        <?php endif; ?>

        <label>Title</label>
        <input type="text" name="title" value="<?= e($data['title'] ?? ''); ?>" maxlength="255" required>

        <label>Short History</label>
        <textarea name="short_history" rows="5" required><?= e($data['short_history'] ?? ''); ?></textarea>

        <label>Country</label>
        <input type="text" name="country" value="<?= e($data['country'] ?? ''); ?>" maxlength="100" required>

        <label>Country Representation</label>
        <textarea name="country_representation" rows="4" required><?= e($data['country_representation'] ?? ''); ?></textarea>

        <div class="grid-2">
            <div>
                <label>Genre</label>
                <select name="genre" required>
                    <?php foreach (['beach', 'mountain', 'city', 'historical', 'forest', 'religious', 'adventure'] as $genre): ?>
                        <option value="<?= e($genre); ?>" <?= (($data['genre'] ?? '') === $genre) ? 'selected' : ''; ?>><?= e(ucfirst($genre)); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label>Cost Level</label>
                <select name="cost_level" required>
                    <?php foreach (['low', 'medium', 'high'] as $cost): ?>
                        <option value="<?= e($cost); ?>" <?= (($data['cost_level'] ?? '') === $cost) ? 'selected' : ''; ?>><?= e(ucfirst($cost)); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <label>Travel Medium Info</label>
        <input type="text" name="travel_medium_info" value="<?= e($data['travel_medium_info'] ?? ''); ?>" required>

        <label>Image</label>
        <input type="file" name="image" id="imageInput" accept="image/jpeg,image/png,image/webp">
        <?php if (!empty($data['image'])): ?>
            <p class="muted">Current image: <?= e($data['image']); ?></p>
        <?php endif; ?>

        <div id="formErrors" class="alert alert-error hidden"></div>
        <button class="btn primary" type="submit">Save Request</button>
    </form>
</main>
<script src="../Public/JS/scout.js"></script>
</body>
</html>
