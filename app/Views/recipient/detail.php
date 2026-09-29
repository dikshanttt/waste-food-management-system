<?php
// app/Views/recipient/detail.php
declare(strict_types=1);
$pageTitle = $listing['title'];
require __DIR__ . '/../layout/header.php';
$user = current_user();
?>

<div style="margin-bottom: 1.5rem;">
    <a href="<?= BASE_URL ?>/listings" class="btn btn-outline btn-sm">
        &larr; Back to Food Listings
    </a>
</div>

<article class="detail-card">
    <header class="detail-header">
        <div style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 1rem;">
            <div>
                <span class="badge badge-default" style="margin-bottom: 0.5rem;">
                    🍴 Food Donation Offer
                </span>
                <h1 class="page-title" style="font-size: 2rem;"><?= e($listing['title']) ?></h1>
                <div style="font-size: 1.1rem; color: var(--color-primary-dark); font-weight: 700; margin-top: 0.25rem;">
                    👨‍🍳 <?= e($listing['organization_name'] ?: $listing['donor_org'] ?: $listing['donor_name']) ?>
                </div>
            </div>
            <div>
                <?= status_badge($listing['status']) ?>
            </div>
        </div>
    </header>

    <div class="detail-body">
        <!-- Highlights Grid -->
        <div class="detail-grid">
            <div>
                <div class="detail-item-label">Quantity Offered</div>
                <div class="detail-item-value" style="color: var(--color-primary-dark);">
                    <?= e((string)$listing['quantity']) ?> <?= e($listing['unit']) ?>
                </div>
            </div>

            <div>
                <div class="detail-item-label">Available Until</div>
                <div class="detail-item-value">
                    <?= format_dt($listing['available_until']) ?>
                </div>
            </div>

            <div>
                <div class="detail-item-label">Pickup Location</div>
                <div class="detail-item-value">
                    📍 <?= e($listing['pickup_location']) ?>
                </div>
            </div>
        </div>

        <!-- Description -->
        <div>
            <h2 class="detail-section-title">Description & Preparation</h2>
            <div style="font-size: 1rem; line-height: 1.7; color: var(--color-text); background: #FAF9F5; padding: 1.25rem; border-radius: var(--radius-sm); border: 1px solid var(--color-border-light);">
                <?= nl2br(e($listing['description'])) ?>
            </div>
        </div>

        <!-- Pickup Instructions -->
        <?php if (!empty($listing['pickup_instructions'])): ?>
            <div>
                <h2 class="detail-section-title">Pickup Instructions</h2>
                <div style="font-size: 0.95rem; line-height: 1.6; color: var(--color-text); background: var(--color-secondary-light); padding: 1rem 1.25rem; border-radius: var(--radius-sm); border: 1px solid var(--color-warning-border);">
                    ℹ️ <?= nl2br(e($listing['pickup_instructions'])) ?>
                </div>
            </div>
        <?php endif; ?>

        <!-- Request Submission Section -->
        <div style="border-top: 1px solid var(--color-border); padding-top: 1.5rem; margin-top: 0.5rem;">
            <?php if (!$user): ?>
                <div class="alert alert-info" style="align-items: center; justify-content: space-between;">
                    <div>
                        <strong>Interested in this food offer?</strong> Sign in as a recipient to request this food.
                    </div>
                    <a href="<?= BASE_URL ?>/login" class="btn btn-primary btn-sm">Sign In</a>
                </div>
            <?php elseif ($user['id'] === (int)$listing['donor_id']): ?>
                <div class="alert alert-info">
                    <span>👑 You are the donor of this food listing. You can manage incoming requests from your dashboard.</span>
                </div>
                <div style="display: flex; gap: 0.75rem;">
                    <?php if ($listing['status'] === STATUS_AVAILABLE): ?>
                        <a href="<?= BASE_URL ?>/donor/listings/<?= (int)$listing['id'] ?>/edit" class="btn btn-primary">
                            ✏ Edit Listing
                        </a>
                    <?php endif; ?>
                    <a href="<?= BASE_URL ?>/donor/requests" class="btn btn-secondary">
                        📥 Manage Requests
                    </a>
                </div>
            <?php elseif ($hasExistingRequest): ?>
                <div class="alert alert-success" style="flex-direction: column; align-items: flex-start;">
                    <div>
                        <strong>You have already requested this listing.</strong>
                    </div>
                    <div style="margin-top: 0.5rem;">
                        <a href="<?= BASE_URL ?>/recipient/requests" class="btn btn-outline btn-sm">
                            📋 Check Request Status
                        </a>
                    </div>
                </div>
            <?php elseif ($listing['status'] !== STATUS_AVAILABLE || strtotime($listing['available_until']) <= time()): ?>
                <div class="alert alert-warning">
                    <span>⛔ This food offer is no longer available or has passed its availability deadline.</span>
                </div>
            <?php elseif ($user['role'] !== ROLE_RECIPIENT): ?>
                <div class="alert alert-info">
                    <span>You are signed in as an <strong><?= e($user['role']) ?></strong>. Food requests are placed by Recipient accounts.</span>
                </div>
            <?php else: ?>
                <!-- Recipient Request Form -->
                <div class="card" style="border: 2px solid var(--color-primary); background-color: var(--color-primary-light);">
                    <div class="card-body">
                        <h2 style="font-size: 1.25rem; font-weight: 700; color: var(--color-primary-dark); margin-bottom: 0.5rem;">
                            Request This Food
                        </h2>
                        <p style="font-size: 0.9rem; color: var(--color-text); margin-bottom: 1rem;">
                            Notify <strong><?= e($listing['organization_name'] ?: $listing['donor_name']) ?></strong> that you can collect this food before the deadline.
                        </p>

                        <form action="<?= BASE_URL ?>/listings/<?= (int)$listing['id'] ?>/request" method="POST">
                            <?= csrf_field() ?>

                            <div class="form-group">
                                <label for="message" class="form-label">Message to Donor (Optional)</label>
                                <textarea id="message" name="message" class="form-textarea" rows="2"
                                          placeholder="e.g. We can pick this up today at 5:30 PM with our van. Thank you!"></textarea>
                            </div>

                            <button type="submit" class="btn btn-primary btn-lg">
                                🍲 Request Food
                            </button>
                        </form>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</article>

<?php require __DIR__ . '/../layout/footer.php'; ?>
