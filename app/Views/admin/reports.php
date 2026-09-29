<?php
// app/Views/admin/reports.php
declare(strict_types=1);
$pageTitle = 'Platform Activity Reports';
require __DIR__ . '/../layout/header.php';
?>

<div class="page-header">
    <div>
        <h1 class="page-title">Activity Reports & Metrics</h1>
        <p class="page-subtitle">Derivation of platform-wide surplus food management performance</p>
    </div>
    <div>
        <a href="<?= BASE_URL ?>/admin/dashboard" class="btn btn-outline">
            &larr; Admin HQ
        </a>
    </div>
</div>

<!-- Primary Count Metrics (derived live from DB) -->
<div class="metrics-grid">
    <div class="metric-card">
        <div class="metric-icon" aria-hidden="true">👥</div>
        <div class="metric-data">
            <span class="metric-value"><?= (int)$stats['users_total'] ?></span>
            <span class="metric-label">Users Registered</span>
        </div>
    </div>

    <div class="metric-card accent-secondary">
        <div class="metric-icon" aria-hidden="true">🍲</div>
        <div class="metric-data">
            <span class="metric-value"><?= (int)$stats['listings_total'] ?></span>
            <span class="metric-label">Food Listings Posted</span>
        </div>
    </div>

    <div class="metric-card accent-info">
        <div class="metric-icon" aria-hidden="true">📥</div>
        <div class="metric-data">
            <span class="metric-value"><?= (int)$stats['requests_total'] ?></span>
            <span class="metric-label">Total Requests Handled</span>
        </div>
    </div>
</div>

<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 1.5rem; margin-bottom: 2rem;">
    <!-- Listing Status Breakdown -->
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">Listings by Status</h2>
        </div>
        <div class="card-body">
            <table class="data-table">
                <thead>
                    <tr>
                        <th scope="col">Status</th>
                        <th scope="col" style="text-align: right;">Count</th>
                        <th scope="col" style="text-align: right;">Share</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $listingStatusCounts = [
                        'Available' => $stats['listings_available'],
                        'Reserved' => $stats['listings_reserved'],
                        'Unavailable' => $stats['listings_unavailable'],
                        'Expired' => $stats['listings_expired'],
                    ];
                    foreach ($listingStatusCounts as $st => $cnt): 
                        $pct = $stats['listings_total'] > 0 ? round(($cnt / $stats['listings_total']) * 100, 1) : 0;
                    ?>
                        <tr>
                            <td><?= status_badge(strtolower($st)) ?></td>
                            <td style="text-align: right; font-weight: 700;"><?= $cnt ?></td>
                            <td style="text-align: right; color: var(--color-text-muted);"><?= $pct ?>%</td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Request Status Breakdown -->
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">Requests by Status</h2>
        </div>
        <div class="card-body">
            <table class="data-table">
                <thead>
                    <tr>
                        <th scope="col">Status</th>
                        <th scope="col" style="text-align: right;">Count</th>
                        <th scope="col" style="text-align: right;">Share</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $reqStatusCounts = [
                        'Pending' => $stats['requests_pending'],
                        'Approved' => $stats['requests_approved'],
                        'Rejected' => $stats['requests_rejected'],
                        'Cancelled' => $stats['requests_cancelled'],
                    ];
                    foreach ($reqStatusCounts as $st => $cnt): 
                        $pct = $stats['requests_total'] > 0 ? round(($cnt / $stats['requests_total']) * 100, 1) : 0;
                    ?>
                        <tr>
                            <td><?= status_badge(strtolower($st)) ?></td>
                            <td style="text-align: right; font-weight: 700;"><?= $cnt ?></td>
                            <td style="text-align: right; color: var(--color-text-muted);"><?= $pct ?>%</td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- User Community Breakdown -->
<div class="card">
    <div class="card-header">
        <h2 class="card-title">User Community Role Distribution</h2>
    </div>
    <div class="card-body">
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem;">
            <div style="background-color: var(--color-surface-alt); padding: 1.25rem; border-radius: var(--radius-md); border: 1px solid var(--color-border-light);">
                <div style="font-size: 0.85rem; color: var(--color-text-muted); font-weight: 600;">👨‍🍳 FOOD DONORS</div>
                <div style="font-size: 1.75rem; font-weight: 800; color: var(--color-primary-dark); margin-top: 0.25rem;">
                    <?= (int)$stats['users_donors'] ?>
                </div>
                <div style="font-size: 0.8rem; color: var(--color-text-muted);">Restaurants, caterers, groceries</div>
            </div>

            <div style="background-color: var(--color-surface-alt); padding: 1.25rem; border-radius: var(--radius-md); border: 1px solid var(--color-border-light);">
                <div style="font-size: 0.85rem; color: var(--color-text-muted); font-weight: 600;">🍲 FOOD RECIPIENTS</div>
                <div style="font-size: 1.75rem; font-weight: 800; color: var(--color-secondary-dark); margin-top: 0.25rem;">
                    <?= (int)$stats['users_recipients'] ?>
                </div>
                <div style="font-size: 0.8rem; color: var(--color-text-muted);">Shelters, outreach groups, individuals</div>
            </div>

            <div style="background-color: var(--color-surface-alt); padding: 1.25rem; border-radius: var(--radius-md); border: 1px solid var(--color-border-light);">
                <div style="font-size: 0.85rem; color: var(--color-text-muted); font-weight: 600;">👑 ADMINISTRATORS</div>
                <div style="font-size: 1.75rem; font-weight: 800; color: var(--color-text); margin-top: 0.25rem;">
                    <?= (int)$stats['users_admins'] ?>
                </div>
                <div style="font-size: 0.8rem; color: var(--color-text-muted);">Staff overseeing operations</div>
            </div>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../layout/footer.php'; ?>
