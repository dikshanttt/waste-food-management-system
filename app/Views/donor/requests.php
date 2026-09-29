<?php
// app/Views/donor/requests.php
declare(strict_types=1);
$pageTitle = 'Manage Food Requests';
require __DIR__ . '/../layout/header.php';
?>

<div class="page-header">
    <div>
        <h1 class="page-title">Manage Food Requests</h1>
        <p class="page-subtitle">Review incoming recipient requests and approve donations for collection</p>
    </div>
    <div>
        <a href="<?= BASE_URL ?>/donor/dashboard" class="btn btn-outline">
            &larr; Donor Dashboard
        </a>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h2 class="card-title">Incoming Requests (<?= count($requests) ?>)</h2>
        <span style="font-size: 0.85rem; color: var(--color-text-muted);">
            Approving a request reserves the food listing and disables further approvals.
        </span>
    </div>
    <div class="card-body" style="padding: 0;">
        <?php if (empty($requests)): ?>
            <div class="empty-state" style="margin: 2rem;">
                <span class="empty-state-icon" aria-hidden="true">📥</span>
                <h3 class="empty-state-title">No requests received yet</h3>
                <p class="empty-state-text">When recipients request your food listings, their requests will appear here for your approval.</p>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th scope="col">Recipient</th>
                            <th scope="col">Requested Listing</th>
                            <th scope="col">Date Requested</th>
                            <th scope="col">Message / Notes</th>
                            <th scope="col">Status</th>
                            <th scope="col" style="text-align: right;">Decision</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($requests as $req): ?>
                            <tr>
                                <td data-label="Recipient">
                                    <strong><?= e($req['recipient_name']) ?></strong>
                                    <?php if (!empty($req['recipient_org'])): ?>
                                        <div style="font-size: 0.8rem; color: var(--color-text-muted);">
                                            <?= e($req['recipient_org']) ?>
                                        </div>
                                    <?php endif; ?>
                                    <div style="font-size: 0.8rem; color: var(--color-text-muted);">
                                        ✉ <?= e($req['recipient_email']) ?>
                                        <?php if (!empty($req['recipient_phone'])): ?>
                                            &bull; 📞 <?= e($req['recipient_phone']) ?>
                                        <?php endif; ?>
                                    </div>
                                </td>

                                <td data-label="Requested Listing">
                                    <strong><a href="<?= BASE_URL ?>/listings/<?= (int)$req['listing_id'] ?>"><?= e($req['listing_title']) ?></a></strong>
                                    <div>
                                        <span class="food-quantity-badge"><?= e((string)$req['quantity']) ?> <?= e($req['unit']) ?></span>
                                    </div>
                                </td>

                                <td data-label="Date Requested">
                                    <?= format_dt($req['created_at']) ?>
                                </td>

                                <td data-label="Message / Notes" style="max-width: 250px;">
                                    <?= !empty($req['message']) ? e($req['message']) : '<span style="color: var(--color-text-muted); font-style: italic;">No message provided</span>' ?>
                                </td>

                                <td data-label="Status">
                                    <?= status_badge($req['status']) ?>
                                    <?php if ($req['status'] === REQ_APPROVED): ?>
                                        <div style="margin-top: 0.25rem;">
                                            <?= status_badge('reserved') ?>
                                        </div>
                                    <?php endif; ?>
                                </td>

                                <td data-label="Decision" style="text-align: right;">
                                    <?php if ($req['status'] === REQ_PENDING): ?>
                                        <div style="display: inline-flex; gap: 0.4rem; justify-content: flex-end;">
                                            <!-- Approve Form -->
                                            <form action="<?= BASE_URL ?>/donor/requests/<?= (int)$req['id'] ?>/approve" method="POST" style="display:inline;">
                                                <?= csrf_field() ?>
                                                <button type="submit" class="btn btn-primary btn-sm"
                                                        data-confirm="Approve request from <?= e($req['recipient_name']) ?>? This will reserve the food listing and decline competing pending requests.">
                                                    ✔ Approve
                                                </button>
                                            </form>

                                            <!-- Reject Form -->
                                            <form action="<?= BASE_URL ?>/donor/requests/<?= (int)$req['id'] ?>/reject" method="POST" style="display:inline;">
                                                <?= csrf_field() ?>
                                                <button type="submit" class="btn btn-outline btn-sm" style="color: var(--color-error); border-color: var(--color-error-border);"
                                                        data-confirm="Reject this request?">
                                                    ✖ Reject
                                                </button>
                                            </form>
                                        </div>
                                    <?php elseif ($req['status'] === REQ_APPROVED): ?>
                                        <span style="font-size: 0.85rem; color: var(--color-success); font-weight: 600;">
                                            ✔ Reserved for pickup
                                        </span>
                                    <?php elseif ($req['status'] === REQ_REJECTED): ?>
                                        <span style="font-size: 0.85rem; color: var(--color-error); font-weight: 500;">
                                            Rejected
                                        </span>
                                    <?php elseif ($req['status'] === REQ_CANCELLED): ?>
                                        <span style="font-size: 0.85rem; color: var(--color-text-muted); font-style: italic;">
                                            Cancelled by recipient
                                        </span>
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
