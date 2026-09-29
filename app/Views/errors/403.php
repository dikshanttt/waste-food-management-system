<?php
// app/Views/errors/403.php
declare(strict_types=1);
$pageTitle = '403 Forbidden';
require __DIR__ . '/../layout/header.php';
?>
<div class="empty-state" style="max-width: 600px; margin: 3rem auto;">
    <span class="empty-state-icon" style="color: var(--color-error);" aria-hidden="true">⛔</span>
    <h1 class="empty-state-title">Access Denied (403)</h1>
    <p class="empty-state-text">
        You do not have the required permissions to access this page or modify this record.
    </p>
    <a href="<?= BASE_URL ?>/" class="btn btn-primary">
        Return to Home
    </a>
</div>
<?php require __DIR__ . '/../layout/footer.php'; ?>
