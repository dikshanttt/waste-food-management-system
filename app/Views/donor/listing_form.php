<?php
// app/Views/donor/listing_form.php
declare(strict_types=1);
$isEdit = !empty($listing['id']);
$pageTitle = $isEdit ? 'Edit Food Listing' : 'Add Food Listing';
require __DIR__ . '/../layout/header.php';

$titleVal = $listing['title'] ?? $old['title'] ?? '';
$descVal = $listing['description'] ?? $old['description'] ?? '';
$qtyVal = $listing['quantity'] ?? $old['quantity'] ?? 10;
$unitVal = $listing['unit'] ?? $old['unit'] ?? 'Meals';
$availVal = '';
if (!empty($listing['available_until'])) {
    $availVal = date('Y-m-d\TH:i', strtotime($listing['available_until']));
} elseif (!empty($old['available_until'])) {
    $availVal = $old['available_until'];
} else {
    // Default tomorrow 19:00 (7 PM)
    $availVal = date('Y-m-d\T19:00', strtotime('+1 day'));
}
$locVal = $listing['pickup_location'] ?? $old['pickup_location'] ?? 'Main Street Community Hall';
$instrVal = $listing['pickup_instructions'] ?? $old['pickup_instructions'] ?? '';
$orgVal = $listing['organization_name'] ?? $old['organization_name'] ?? (current_user()['organization'] ?? 'Green Kitchen');
?>

<div class="page-header">
    <div>
        <h1 class="page-title"><?= e($pageTitle) ?></h1>
        <p class="page-subtitle"><?= $isEdit ? 'Update details for this food offer' : 'Provide food information, quantities, and pickup times' ?></p>
    </div>
    <div>
        <a href="<?= BASE_URL ?>/donor/dashboard" class="btn btn-outline">
            &larr; Back to Dashboard
        </a>
    </div>
</div>

<div style="max-width: 760px; margin: 0 auto;">
    <div class="card">
        <div class="card-header">
            <h2 class="card-title"><?= $isEdit ? 'Edit Listing Details' : 'Food Offer Details' ?></h2>
        </div>
        <div class="card-body">
            <form action="<?= $isEdit ? BASE_URL . '/donor/listings/' . (int)$listing['id'] . '/edit' : BASE_URL . '/donor/listings/new' ?>"
                  method="POST" novalidate>
                <?= csrf_field() ?>

                <!-- Title -->
                <div class="form-group">
                    <label for="title" class="form-label required">Food Title / Item Name</label>
                    <input type="text" id="title" name="title" class="form-control"
                           value="<?= e((string)$titleVal) ?>" required maxlength="150"
                           placeholder="e.g. Vegetable Curry, Bread Packets, Fresh Apples">
                    <?php if (!empty($errors['title'])): ?>
                        <span class="form-error"><?= e($errors['title']) ?></span>
                    <?php endif; ?>
                </div>

                <!-- Description -->
                <div class="form-group">
                    <label for="description" class="form-label required">Description & Preparation Details</label>
                    <textarea id="description" name="description" class="form-textarea" rows="4"
                              required placeholder="Describe the food, packaging, dietary info (vegetarian/vegan/allergens), preparation time, etc."><?= e((string)$descVal) ?></textarea>
                    <?php if (!empty($errors['description'])): ?>
                        <span class="form-error"><?= e($errors['description']) ?></span>
                    <?php endif; ?>
                </div>

                <!-- Quantity and Unit -->
                <div class="form-grid-2">
                    <div class="form-group">
                        <label for="quantity" class="form-label required">Quantity</label>
                        <input type="number" id="quantity" name="quantity" class="form-control"
                               min="1" max="9999" value="<?= e((string)$qtyVal) ?>" required>
                        <?php if (!empty($errors['quantity'])): ?>
                            <span class="form-error"><?= e($errors['quantity']) ?></span>
                        <?php endif; ?>
                    </div>

                    <div class="form-group">
                        <label for="unit" class="form-label required">Unit of Measure</label>
                        <select id="unit" name="unit" class="form-select" required>
                            <?php foreach (['Meals', 'Packs', 'Boxes', 'Portions', 'Kg', 'Liters'] as $u): ?>
                                <option value="<?= $u ?>" <?= $unitVal === $u ? 'selected' : '' ?>><?= $u ?></option>
                            <?php endforeach; ?>
                        </select>
                        <?php if (!empty($errors['unit'])): ?>
                            <span class="form-error"><?= e($errors['unit']) ?></span>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Available Until Date/Time -->
                <div class="form-group">
                    <label for="available_until" class="form-label required">Available Until (Expiry / Pickup Deadline)</label>
                    <input type="datetime-local" id="available_until" name="available_until" class="form-control"
                           value="<?= e((string)$availVal) ?>" required>
                    <small class="form-help">Listings will automatically expire and stop accepting requests past this time.</small>
                    <?php if (!empty($errors['available_until'])): ?>
                        <span class="form-error"><?= e($errors['available_until']) ?></span>
                    <?php endif; ?>
                </div>

                <!-- Pickup Location -->
                <div class="form-group">
                    <label for="pickup_location" class="form-label required">Pickup Location / Area</label>
                    <input type="text" id="pickup_location" name="pickup_location" class="form-control"
                           value="<?= e((string)$locVal) ?>" required maxlength="255"
                           placeholder="e.g. Main Street Community Hall, Ward 4">
                    <?php if (!empty($errors['pickup_location'])): ?>
                        <span class="form-error"><?= e($errors['pickup_location']) ?></span>
                    <?php endif; ?>
                </div>

                <!-- Pickup Instructions -->
                <div class="form-group">
                    <label for="pickup_instructions" class="form-label">Pickup Instructions (Optional)</label>
                    <textarea id="pickup_instructions" name="pickup_instructions" class="form-textarea" rows="2"
                              placeholder="e.g. Ask for Chef at side door, bring own containers, parking available."><?= e((string)$instrVal) ?></textarea>
                </div>

                <!-- Organization Name -->
                <div class="form-group">
                    <label for="organization_name" class="form-label">Donor Name / Kitchen Banner</label>
                    <input type="text" id="organization_name" name="organization_name" class="form-control"
                           value="<?= e((string)$orgVal) ?>" placeholder="e.g. Green Kitchen">
                </div>

                <!-- Actions: Save / Cancel -->
                <div style="display: flex; gap: 1rem; align-items: center; margin-top: 2rem; border-top: 1px solid var(--color-border-light); padding-top: 1.5rem;">
                    <button type="submit" class="btn btn-primary btn-lg">
                        <?= $isEdit ? '✔ Save Changes' : '➕ Create Listing' ?>
                    </button>
                    <a href="<?= BASE_URL ?>/donor/dashboard" class="btn btn-outline btn-lg">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../layout/footer.php'; ?>
