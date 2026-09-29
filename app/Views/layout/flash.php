<?php
// app/Views/layout/flash.php
declare(strict_types=1);
require_once __DIR__ . '/../../../config/config.php';
$flash = get_flash();
?>
<?php if ($flash): ?>
    <div class="alert alert-<?= e($flash['type'] === 'error' ? 'error' : ($flash['type'] === 'warning' ? 'warning' : 'success')) ?>" role="alert" tabindex="-1">
        <span class="badge-icon" aria-hidden="true">
            <?= $flash['type'] === 'error' ? '⛔' : ($flash['type'] === 'warning' ? '⚠️' : '✔') ?>
        </span>
        <div><?= e($flash['message']) ?></div>
    </div>
<?php endif; ?>
