<?php
// app/Views/donor/dashboard.php
declare(strict_types=1);
$pageTitle = 'Donor Dashboard';
require __DIR__ . '/../layout/header.php';
?>

<div class="page-header">
    <div>
        <h1 class="page-title">Donor Dashboard</h1>
        <p class="page-subtitle">Track surplus food listings and respond to community requests</p>
    </div>
    <div style="display: flex; gap: 0.75rem;">
        <a href="<?= BASE_URL ?>/donor/listings/new" class="btn btn-primary">
            ➕ Add Food Listing
        </a>
        <a href="<?= BASE_URL ?>/donor/requests" class="btn btn-secondary">
            📥 Review Requests (<?= e((string)$pendingRequestsCount) ?>)
        </a>
    </div>
</div>

<!-- Metrics Cards (Active Listings 8, Pending Requests 3) -->
<div class="metrics-grid">
    <div class="metric-card">
        <div class="metric-icon" aria-hidden="true">🍲</div>
        <div class="metric-data">
            <span class="metric-value"><?= e((string)$activeListingsCount) ?></span>
            <span class="metric-label">Active Listings</span>
        </div>
    </div>

    <div class="metric-card accent-secondary">
        <div class="metric-icon" aria-hidden="true">⏳</div>
        <div class="metric-data">
            <span class="metric-value"><?= e((string)$pendingRequestsCount) ?></span>
            <span class="metric-label">Pending Requests</span>
        </div>
    </div>

    <div class="metric-card accent-info">
        <div class="metric-icon" aria-hidden="true">📦</div>
        <div class="metric-data">
            <span class="metric-value"><?= e((string)$totalListingsCount) ?></span>
            <span class="metric-label">Total Listings Posted</span>
        </div>
    </div>
</div>

<!-- Listings Table Section -->
<div class="card">
    <div class="card-header">
        <h2 class="card-title">My Food Listings</h2>
        <span style="font-size: 0.85rem; color: var(--color-text-muted); font-weight: 500;">
            Showing <?= count($listings) ?> listings
        </span>
    </div>
    <div class="card-body" style="padding: 0;">
        <?php if (empty($listings)): ?>
            <div class="empty-state" style="margin: 2rem;">
                <span class="empty-state-icon" aria-hidden="true">🍽️</span>
                <h3 class="empty-state-title">No listings yet</h3>
                <p class="empty-state-text">Add your first food listing to share surplus food with community shelters and families in need.</p>
                <a href="<?= BASE_URL ?>/donor/listings/new" class="btn btn-primary">
                    ➕ Add Food Listing
                </a>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th scope="col">Food Item</th>
                            <th scope="col">Quantity</th>
                            <th scope="col">Available Until</th>
                            <th scope="col">Pickup Location</th>
                            <th scope="col">Status</th>
                            <th scope="col" style="text-align: right;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($listings as $item): ?>
                            <tr>
                                <td data-label="Food Item">
                                    <strong><a href="<?= BASE_URL ?>/listings/<?= (int)$item['id'] ?>"><?= e($item['title']) ?></a></strong>
                                    <?php if (!empty($item['pending_requests_count']) && (int)$item['pending_requests_count'] > 0): ?>
                                        <div style="font-size: 0.8rem; margin-top: 0.2rem;">
                                            <a href="<?= BASE_URL ?>/donor/requests" style="color: var(--color-warning); font-weight: 600;">
                                                ⏳ <?= (int)$item['pending_requests_count'] ?> pending request(s)
                                            </a>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td data-label="Quantity">
                                    <span class="food-quantity-badge"><?= e((string)$item['quantity']) ?> <?= e($item['unit']) ?></span>
                                </td>
                                <td data-label="Available Until">
                                    <?= format_dt($item['available_until']) ?>
                                </td>
                                <td data-label="Pickup Location">
                                    <?= e($item['pickup_location']) ?>
                                </td>
                                <td data-label="Status">
                                    <?= status_badge($item['status']) ?>
                                </td>
                                <td data-label="Actions" style="text-align: right;">
                                    <div style="display: inline-flex; gap: 0.4rem; justify-content: flex-end;">
                                        <a href="<?= BASE_URL ?>/listings/<?= (int)$item['id'] ?>" class="btn btn-outline btn-sm" title="View details">
                                            View
                                        </a>
                                        <?php if ($item['status'] === STATUS_AVAILABLE): ?>
                                            <a href="<?= BASE_URL ?>/donor/listings/<?= (int)$item['id'] ?>/edit" class="btn btn-outline btn-sm" title="Edit listing">
                                                Edit
                                            </a>
                                            <form action="<?= BASE_URL ?>/donor/listings/<?= (int)$item['id'] ?>/delete" method="POST" style="display:inline;">
                                                <?= csrf_field() ?>
                                                <button type="submit" class="btn btn-danger btn-sm"
                                                        data-confirm="Are you sure you want to remove or make this listing unavailable?">
                                                    Remove
                                                </button>
                                            </form>
                                        <?php else: ?>
                                            <span style="font-size: 0.8rem; color: var(--color-text-muted); font-style: italic; padding: 0.4rem;">
                                                Locked
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require __DIR__ . '/../layout/footer.php'; ?>
