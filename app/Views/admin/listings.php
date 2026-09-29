<?php
// app/Views/admin/listings.php
declare(strict_types=1);
$pageTitle = 'Manage Food Listings';
require __DIR__ . '/../layout/header.php';
?>

<div class="page-header">
    <div>
        <h1 class="page-title">Food Listings Moderation</h1>
        <p class="page-subtitle">Total Listings Recorded: <strong><?= count($listings) ?></strong> (Review and moderate surplus food offers)</p>
    </div>
    <div>
        <a href="<?= BASE_URL ?>/admin/dashboard" class="btn btn-outline">
            &larr; Admin HQ
        </a>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h2 class="card-title">All Food Listings</h2>
        <span style="font-size: 0.85rem; color: var(--color-text-muted);">
            Removing invalid listings protects recipient safety and system integrity.
        </span>
    </div>
    <div class="card-body" style="padding: 0;">
        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th scope="col">ID</th>
                        <th scope="col">Food Title</th>
                        <th scope="col">Donor</th>
                        <th scope="col">Quantity</th>
                        <th scope="col">Pickup Area</th>
                        <th scope="col">Expiry Deadline</th>
                        <th scope="col">Status</th>
                        <th scope="col" style="text-align: right;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($listings as $l): ?>
                        <tr>
                            <td data-label="ID">#<?= (int)$l['id'] ?></td>

                            <td data-label="Food Title">
                                <strong><a href="<?= BASE_URL ?>/listings/<?= (int)$l['id'] ?>"><?= e($l['title']) ?></a></strong>
                                <?php if (!empty($l['total_requests']) && (int)$l['total_requests'] > 0): ?>
                                    <div style="font-size: 0.8rem; color: var(--color-text-muted);">
                                        <?= (int)$l['total_requests'] ?> total request(s)
                                    </div>
                                <?php endif; ?>
                            </td>

                            <td data-label="Donor">
                                <strong><?= e($l['donor_name']) ?></strong>
                                <div style="font-size: 0.8rem; color: var(--color-text-muted);">✉ <?= e($l['donor_email']) ?></div>
                            </td>

                            <td data-label="Quantity">
                                <span class="food-quantity-badge"><?= e((string)$l['quantity']) ?> <?= e($l['unit']) ?></span>
                            </td>

                            <td data-label="Pickup Area">
                                <?= e($l['pickup_location']) ?>
                            </td>

                            <td data-label="Expiry Deadline">
                                <?= format_dt($l['available_until']) ?>
                            </td>

                            <td data-label="Status">
                                <?= status_badge($l['status']) ?>
                            </td>

                            <td data-label="Action" style="text-align: right;">
                                <div style="display: inline-flex; gap: 0.4rem; justify-content: flex-end;">
                                    <a href="<?= BASE_URL ?>/listings/<?= (int)$l['id'] ?>" class="btn btn-outline btn-sm">
                                        View
                                    </a>
                                    <?php if ($l['status'] !== STATUS_UNAVAILABLE): ?>
                                        <form action="<?= BASE_URL ?>/admin/listings/<?= (int)$l['id'] ?>/remove" method="POST" style="display:inline;">
                                            <?= csrf_field() ?>
                                            <button type="submit" class="btn btn-danger btn-sm"
                                                    data-confirm="Are you sure you want to remove or mark listing #<?= (int)$l['id'] ?> ('<?= e($l['title']) ?>') as unavailable?">
                                                Remove
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../layout/footer.php'; ?>
