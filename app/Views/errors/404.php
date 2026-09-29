<?php
// app/Views/errors/404.php
declare(strict_types=1);
$pageTitle = '404 Page Not Found';
require __DIR__ . '/../layout/header.php';
?>
<div class="empty-state" style="max-width: 600px; margin: 3rem auto;">
    <span class="empty-state-icon" aria-hidden="true">🔍</span>
    <h1 class="empty-state-title">Page Not Found (404)</h1>
    <p class="empty-state-text">
        The page or food offer you are looking for does not exist or may have been removed.
    </p>
    <a href="<?= BASE_URL ?>/listings" class="btn btn-primary">
        Browse Available Food
    </a>
</div>
<?php require __DIR__ . '/../layout/footer.php'; ?>
