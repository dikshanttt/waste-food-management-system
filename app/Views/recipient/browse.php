<?php
// app/Views/recipient/browse.php
declare(strict_types=1);
$pageTitle = 'Available Surplus Food';
require __DIR__ . '/../layout/header.php';
?>

<div class="page-header">
    <div>
        <h1 class="page-title">Available Surplus Food</h1>
        <p class="page-subtitle">Browse fresh, unexpired surplus meals and produce available for pickup</p>
    </div>
    <?php if (is_logged_in() && current_user()['role'] === ROLE_RECIPIENT): ?>
        <div>
            <a href="<?= BASE_URL ?>/recipient/requests" class="btn btn-secondary">
                📋 View My Requests
            </a>
        </div>
    <?php endif; ?>
</div>

<!-- Search & Filter Bar -->
<div class="search-filter-card">
    <form action="<?= BASE_URL ?>/listings" method="GET" class="search-filter-form">
        <div>
            <label for="search" class="form-label" style="font-size: 0.8rem; text-transform: uppercase;">Search Offers</label>
            <input type="text" id="search" name="search" class="form-control"
                   value="<?= e($search ?? '') ?>" placeholder="Search by food name, location, or donor...">
        </div>

        <div>
            <label for="unit" class="form-label" style="font-size: 0.8rem; text-transform: uppercase;">Unit Type</label>
            <select id="unit" name="unit" class="form-select">
                <option value="">All Units</option>
                <?php foreach (['Meals', 'Packs', 'Boxes', 'Portions', 'Kg', 'Liters'] as $u): ?>
                    <option value="<?= $u ?>" <?= ($unit ?? '') === $u ? 'selected' : '' ?>><?= $u ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div style="align-self: flex-end;">
            <button type="submit" class="btn btn-primary">
                🔍 Filter
            </button>
        </div>

        <?php if (!empty($search) || !empty($unit)): ?>
            <div style="align-self: flex-end;">
                <a href="<?= BASE_URL ?>/listings" class="btn btn-outline">
                    Reset
                </a>
            </div>
        <?php endif; ?>
    </form>
</div>

<!-- Listings Cards Grid -->
<?php if (empty($listings)): ?>
    <div class="empty-state">
        <span class="empty-state-icon" aria-hidden="true">🍲</span>
        <h2 class="empty-state-title">No available food offers right now</h2>
        <p class="empty-state-text">
            <?php if (!empty($search) || !empty($unit)): ?>
                No listings matched your search criteria. Try clearing your filters.
            <?php else: ?>
                Check back shortly! Donors post surplus food throughout the day as meal services conclude.
            <?php endif; ?>
        </p>
        <?php if (!empty($search) || !empty($unit)): ?>
            <a href="<?= BASE_URL ?>/listings" class="btn btn-outline">Clear Filters</a>
        <?php endif; ?>
    </div>
<?php else: ?>
    <div style="margin-bottom: 1rem; color: var(--color-text-muted); font-size: 0.9rem; font-weight: 500;">
        Showing <strong><?= count($listings) ?></strong> unexpired food offers available now
    </div>

    <div class="listing-grid">
        <?php foreach ($listings as $item): ?>
            <article class="food-card">
                <header class="food-card-header">
                    <div>
                        <h2 class="food-card-title">
                            <a href="<?= BASE_URL ?>/listings/<?= (int)$item['id'] ?>"><?= e($item['title']) ?></a>
                        </h2>
                        <div style="font-size: 0.85rem; color: var(--color-primary-dark); font-weight: 600; margin-top: 0.2rem;">
                            <?= e($item['organization_name'] ?: $item['donor_org'] ?: $item['donor_name']) ?>
                        </div>
                    </div>
                    <div>
                        <?= status_badge($item['status']) ?>
                    </div>
                </header>

                <div class="food-card-body">
                    <p style="font-size: 0.9rem; color: var(--color-text); line-height: 1.5;">
                        <?= e(mb_strimwidth($item['description'], 0, 110, '...')) ?>
                    </p>

                    <div class="food-meta-row">
                        <span class="food-meta-icon" aria-hidden="true">📦</span>
                        <div>
                            <strong>Quantity:</strong>
                            <span class="food-quantity-badge"><?= e((string)$item['quantity']) ?> <?= e($item['unit']) ?></span>
                        </div>
                    </div>

                    <div class="food-meta-row">
                        <span class="food-meta-icon" aria-hidden="true">📍</span>
                        <div>
                            <strong>Area:</strong> <?= e($item['pickup_location']) ?>
                        </div>
                    </div>

                    <div class="food-meta-row">
                        <span class="food-meta-icon" aria-hidden="true">⏰</span>
                        <div>
                            <strong>Available until:</strong> <?= format_dt($item['available_until']) ?>
                        </div>
                    </div>
                </div>

                <footer class="food-card-footer">
                    <a href="<?= BASE_URL ?>/listings/<?= (int)$item['id'] ?>" class="btn btn-primary btn-sm btn-block">
                        View Details & Request &rarr;
                    </a>
                </footer>
            </article>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php require __DIR__ . '/../layout/footer.php'; ?>
