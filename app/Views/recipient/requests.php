<?php
// app/Views/recipient/requests.php
declare(strict_types=1);
$pageTitle = 'My Food Requests & Status';
require __DIR__ . '/../layout/header.php';
?>

<div class="page-header">
    <div>
        <h1 class="page-title">My Food Requests</h1>
        <p class="page-subtitle">Track the status of your food requests and coordinate pickup</p>
    </div>
    <div>
        <a href="<?= BASE_URL ?>/listings" class="btn btn-primary">
            🔍 Browse More Food
        </a>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h2 class="card-title">Submitted Requests (<?= count($requests) ?>)</h2>
        <span style="font-size: 0.85rem; color: var(--color-text-muted);">
            Pending requests can be cancelled prior to donor decision.
        </span>
    </div>
    <div class="card-body" style="padding: 0;">
        <?php if (empty($requests)): ?>
            <div class="empty-state" style="margin: 2rem;">
                <span class="empty-state-icon" aria-hidden="true">🍲</span>
                <h3 class="empty-state-title">No food requests yet</h3>
                <p class="empty-state-text">Browse available surplus food offers in your area and submit a request to support your community.</p>
                <a href="<?= BASE_URL ?>/listings" class="btn btn-primary">
                    🔍 Browse Available Food
                </a>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th scope="col">Food Listing</th>
                            <th scope="col">Quantity</th>
                            <th scope="col">Donor & Pickup Area</th>
                            <th scope="col">Date Requested</th>
                            <th scope="col">Status</th>
                            <th scope="col" style="text-align: right;">Action / Details</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($requests as $item): ?>
                            <tr>
                                <td data-label="Food Listing">
                                    <strong><a href="<?= BASE_URL ?>/listings/<?= (int)$item['listing_id'] ?>"><?= e($item['listing_title']) ?></a></strong>
                                    <?php if (!empty($item['message'])): ?>
                                        <div style="font-size: 0.8rem; color: var(--color-text-muted); margin-top: 0.2rem;">
                                            Note: "<?= e($item['message']) ?>"
                                        </div>
                                    <?php endif; ?>
                                </td>

                                <td data-label="Quantity">
                                    <span class="food-quantity-badge"><?= e((string)$item['quantity']) ?> <?= e($item['unit']) ?></span>
                                </td>

                                <td data-label="Donor & Area">
                                    <strong><?= e($item['organization_name'] ?: $item['donor_name']) ?></strong>
                                    <div style="font-size: 0.8rem; color: var(--color-text-muted);">
                                        📍 <?= e($item['pickup_location']) ?>
                                    </div>
                                    <?php if ($item['status'] === REQ_APPROVED): ?>
                                        <div style="margin-top: 0.35rem; font-size: 0.82rem; background: var(--color-success-light); padding: 0.25rem 0.5rem; border-radius: 4px; color: var(--color-success);">
                                            📞 Donor: <?= e($item['donor_phone'] ?: $item['donor_email']) ?>
                                        </div>
                                    <?php endif; ?>
                                </td>

                                <td data-label="Date Requested">
                                    <?= format_dt($item['created_at']) ?>
                                </td>

                                <td data-label="Status">
                                    <?= status_badge($item['status']) ?>
                                    <?php if ($item['status'] === REQ_APPROVED): ?>
                                        <div style="font-size: 0.78rem; color: var(--color-success); font-weight: 600; margin-top: 0.2rem;">
                                            Approved for pickup!
                                        </div>
                                    <?php endif; ?>
                                </td>

                                <td data-label="Action / Details" style="text-align: right;">
                                    <?php if ($item['status'] === REQ_PENDING): ?>
                                        <form action="<?= BASE_URL ?>/recipient/requests/<?= (int)$item['id'] ?>/cancel" method="POST" style="display:inline;">
                                            <?= csrf_field() ?>
                                            <button type="submit" class="btn btn-outline btn-sm" style="color: var(--color-error); border-color: var(--color-error-border);"
                                                    data-confirm="Are you sure you want to cancel this pending request?">
                                                ⛔ Cancel Request
                                            </button>
                                        </form>
                                    <?php elseif ($item['status'] === REQ_APPROVED): ?>
                                        <a href="<?= BASE_URL ?>/listings/<?= (int)$item['listing_id'] ?>" class="btn btn-primary btn-sm">
                                            View Pickup Info
                                        </a>
                                    <?php else: ?>
                                        <a href="<?= BASE_URL ?>/listings/<?= (int)$item['listing_id'] ?>" class="btn btn-outline btn-sm">
                                            View Offer
                                        </a>
                                    <?php endif; ?>
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
