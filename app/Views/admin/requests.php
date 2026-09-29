<?php
// app/Views/admin/requests.php
declare(strict_types=1);
$pageTitle = 'All Food Requests';
require __DIR__ . '/../layout/header.php';
?>

<div class="page-header">
    <div>
        <h1 class="page-title">Platform Food Requests</h1>
        <p class="page-subtitle">Total Requests Logged: <strong><?= count($requests) ?></strong> (Auditing donor-recipient exchanges)</p>
    </div>
    <div>
        <a href="<?= BASE_URL ?>/admin/dashboard" class="btn btn-outline">
            &larr; Admin HQ
        </a>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h2 class="card-title">Food Request Records</h2>
        <span style="font-size: 0.85rem; color: var(--color-text-muted);">
            All historical and active request transitions
        </span>
    </div>
    <div class="card-body" style="padding: 0;">
        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th scope="col">ID</th>
                        <th scope="col">Listing Title</th>
                        <th scope="col">Quantity</th>
                        <th scope="col">Donor</th>
                        <th scope="col">Recipient</th>
                        <th scope="col">Date Logged</th>
                        <th scope="col">Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($requests as $r): ?>
                        <tr>
                            <td data-label="ID">#<?= (int)$r['id'] ?></td>

                            <td data-label="Listing Title">
                                <strong><a href="<?= BASE_URL ?>/listings/<?= (int)$r['listing_id'] ?>"><?= e($r['listing_title']) ?></a></strong>
                            </td>

                            <td data-label="Quantity">
                                <span class="food-quantity-badge"><?= e((string)$r['quantity']) ?> <?= e($r['unit']) ?></span>
                            </td>

                            <td data-label="Donor">
                                <?= e($r['donor_name']) ?>
                            </td>

                            <td data-label="Recipient">
                                <strong><?= e($r['recipient_name']) ?></strong>
                                <div style="font-size: 0.8rem; color: var(--color-text-muted);">✉ <?= e($r['recipient_email']) ?></div>
                            </td>

                            <td data-label="Date Logged">
                                <?= format_dt($r['created_at']) ?>
                            </td>

                            <td data-label="Status">
                                <?= status_badge($r['status']) ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../layout/footer.php'; ?>
