<?php
// app/Views/auth/register.php
declare(strict_types=1);
$pageTitle = 'Create Account';
$selectedRole = $_GET['role'] ?? $oldRole ?? ROLE_RECIPIENT;
if (!in_array($selectedRole, [ROLE_DONOR, ROLE_RECIPIENT], true)) {
    $selectedRole = ROLE_RECIPIENT;
}
require __DIR__ . '/../layout/header.php';
?>

<div style="max-width: 520px; margin: 1.5rem auto;">
    <div class="card">
        <div class="card-header" style="text-align: center; justify-content: center; flex-direction: column;">
            <div class="brand-icon" style="margin-bottom: 0.5rem;" aria-hidden="true">🌱</div>
            <h1 class="card-title" style="font-size: 1.5rem;">Join FoodRescue</h1>
            <p style="font-size: 0.85rem; color: var(--color-text-muted);">Connect with your local community to share or receive surplus food</p>
        </div>
        <div class="card-body">
            <form action="<?= BASE_URL ?>/register" method="POST" novalidate>
                <?= csrf_field() ?>

                <div class="form-group">
                    <label class="form-label required">I want to:</label>
                    <div class="role-selector" role="radiogroup" aria-label="Account type selection">
                        <label class="role-option">
                            <input type="radio" name="role" value="recipient" <?= $selectedRole === ROLE_RECIPIENT ? 'checked' : '' ?>>
                            <div class="role-card">
                                <span class="role-icon" aria-hidden="true">🍲</span>
                                <span class="role-title">Receive Food</span>
                                <span class="role-desc">Shelters, charities, community volunteers & individuals</span>
                            </div>
                        </label>
                        <label class="role-option">
                            <input type="radio" name="role" value="donor" <?= $selectedRole === ROLE_DONOR ? 'checked' : '' ?>>
                            <div class="role-card">
                                <span class="role-icon" aria-hidden="true">👨‍🍳</span>
                                <span class="role-title">Donate Food</span>
                                <span class="role-desc">Restaurants, caterers, bakeries & event organizers</span>
                            </div>
                        </label>
                    </div>
                </div>

                <div class="form-group">
                    <label for="name" class="form-label required">Full Name / Contact Person</label>
                    <input type="text" id="name" name="name" class="form-control"
                           value="<?= e($oldName ?? '') ?>" required autocomplete="name"
                           placeholder="e.g. Maya Shrestha">
                </div>

                <div class="form-group">
                    <label for="email" class="form-label required">Email Address</label>
                    <input type="email" id="email" name="email" class="form-control"
                           value="<?= e($oldEmail ?? '') ?>" required autocomplete="email"
                           placeholder="e.g. maya@example.org">
                </div>

                <div class="form-grid-2">
                    <div class="form-group">
                        <label for="organization" class="form-label">Organization / Hall (Optional)</label>
                        <input type="text" id="organization" name="organization" class="form-control"
                               value="<?= e($oldOrg ?? '') ?>" placeholder="e.g. Green Kitchen">
                    </div>
                    <div class="form-group">
                        <label for="phone" class="form-label">Phone Number (Optional)</label>
                        <input type="tel" id="phone" name="phone" class="form-control"
                               value="<?= e($oldPhone ?? '') ?>" placeholder="e.g. +977-9800000000">
                    </div>
                </div>

                <div class="form-group">
                    <label for="password" class="form-label required">Password</label>
                    <input type="password" id="password" name="password" class="form-control"
                           required minlength="6" autocomplete="new-password"
                           placeholder="At least 6 characters">
                    <small class="form-help">Must be at least 6 characters long.</small>
                </div>

                <button type="submit" class="btn btn-primary btn-block btn-lg" style="margin-top: 1.5rem;">
                    Create Account
                </button>
            </form>

            <div style="margin-top: 1.5rem; text-align: center; font-size: 0.9rem; border-top: 1px solid var(--color-border-light); padding-top: 1.25rem;">
                Already have an account? <a href="<?= BASE_URL ?>/login" style="font-weight: 600;">Sign in here</a>
            </div>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../layout/footer.php'; ?>
