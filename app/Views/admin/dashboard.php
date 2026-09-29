<?php
// app/Views/admin/dashboard.php
declare(strict_types=1);
$pageTitle = 'Administrator Command Center';
require __DIR__ . '/../layout/header.php';
?>

<div class="page-header">
    <div>
        <h1 class="page-title">Administrator HQ</h1>
        <p class="page-subtitle">Platform-wide overview, user access control, and moderation</p>
    </div>
    <div style="display: flex; gap: 0.75rem;">
        <a href="<?= BASE_URL ?>/admin/users" class="btn btn-outline">
            👥 Manage Users
        </a>
        <a href="<?= BASE_URL ?>/admin/reports" class="btn btn-primary">
            📈 View Reports
        </a>
    </div>
</div>

<!-- Admin Metrics (Users 120, Listings 45, Requests 89) -->
<div class="metrics-grid">
    <div class="metric-card">
        <div class="metric-icon" aria-hidden="true">👥</div>
        <div class="metric-data">
            <span class="metric-value"><?= e((string)$totalUsers) ?></span>
            <span class="metric-label">Total Registered Users</span>
        </div>
    </div>

    <div class="metric-card accent-secondary">
        <div class="metric-icon" aria-hidden="true">🍲</div>
        <div class="metric-data">
            <span class="metric-value"><?= e((string)$totalListings) ?></span>
            <span class="metric-label">Food Listings Posted</span>
        </div>
    </div>

    <div class="metric-card accent-info">
        <div class="metric-icon" aria-hidden="true">📥</div>
        <div class="metric-data">
            <span class="metric-value"><?= e((string)$totalRequests) ?></span>
            <span class="metric-label">Total Requests Handled</span>
        </div>
    </div>
</div>

<!-- Quick Navigation Cards -->
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 1.5rem; margin-bottom: 2rem;">
    <div class="card">
        <div class="card-body">
            <h3 style="font-size: 1.15rem; font-weight: 700; margin-bottom: 0.5rem;">👥 User Moderation</h3>
            <p style="font-size: 0.85rem; color: var(--color-text-muted); margin-bottom: 1rem;">
                Review registered donors and recipients, audit account activity, and deactivate violators with confirmation.
            </p>
            <a href="<?= BASE_URL ?>/admin/users" class="btn btn-outline btn-sm btn-block">Go to Users &rarr;</a>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <h3 style="font-size: 1.15rem; font-weight: 700; margin-bottom: 0.5rem;">🍲 Food Listings</h3>
            <p style="font-size: 0.85rem; color: var(--color-text-muted); margin-bottom: 1rem;">
                Audit active, reserved, and expired food listings. Safely remove invalid or improper listings.
            </p>
            <a href="<?= BASE_URL ?>/admin/listings" class="btn btn-outline btn-sm btn-block">Go to Listings &rarr;</a>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <h3 style="font-size: 1.15rem; font-weight: 700; margin-bottom: 0.5rem;">📥 Food Requests</h3>
            <p style="font-size: 0.85rem; color: var(--color-text-muted); margin-bottom: 1rem;">
                Supervise donor-recipient matches, pending requests, reservations, and cancellations.
            </p>
            <a href="<?= BASE_URL ?>/admin/requests" class="btn btn-outline btn-sm btn-block">Go to Requests &rarr;</a>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <h3 style="font-size: 1.15rem; font-weight: 700; margin-bottom: 0.5rem;">📈 Activity Reports</h3>
            <p style="font-size: 0.85rem; color: var(--color-text-muted); margin-bottom: 1rem;">
                Verify totals, breakdown ratios, approval percentages, and data consistency for pilots and demos.
            </p>
            <a href="<?= BASE_URL ?>/admin/reports" class="btn btn-outline btn-sm btn-block">View Reports &rarr;</a>
        </div>
    </div>
</div>

<!-- Recent Listings Preview -->
<div class="card">
    <div class="card-header">
        <h2 class="card-title">Recent Food Listings Overview</h2>
        <a href="<?= BASE_URL ?>/admin/listings" class="btn btn-outline btn-sm">View All Listings</a>
    </div>
    <div class="card-body" style="padding: 0;">
        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th scope="col">Title</th>
                        <th scope="col">Donor</th>
                        <th scope="col">Quantity</th>
                        <th scope="col">Expiry</th>
                        <th scope="col">Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach (array_slice($recentListings, 0, 5) as $l): ?>
                        <tr>
                            <td data-label="Title">
                                <strong><a href="<?= BASE_URL ?>/listings/<?= (int)$l['id'] ?>"><?= e($l['title']) ?></a></strong>
                            </td>
                            <td data-label="Donor"><?= e($l['donor_name']) ?></td>
                            <td data-label="Quantity"><span class="food-quantity-badge"><?= e((string)$l['quantity']) ?> <?= e($l['unit']) ?></span></td>
                            <td data-label="Expiry"><?= format_dt($l['available_until']) ?></td>
                            <td data-label="Status"><?= status_badge($l['status']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../layout/footer.php'; ?>
