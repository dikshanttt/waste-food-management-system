<?php
// app/Views/auth/profile.php
declare(strict_types=1);
$pageTitle = 'Account Profile';
require __DIR__ . '/../layout/header.php';
?>

<div class="page-header">
    <div>
        <h1 class="page-title">User Profile</h1>
        <p class="page-subtitle">Manage your personal information and account security</p>
    </div>
</div>

<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 2rem;">
    <!-- Profile Info Form -->
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">Profile Information</h2>
        </div>
        <div class="card-body">
            <form action="<?= BASE_URL ?>/profile" method="POST">
                <?= csrf_field() ?>
                <input type="hidden" name="action" value="update_profile">

                <div class="form-group">
                    <label class="form-label">Email Address (Read-only)</label>
                    <input type="email" class="form-control" value="<?= e($userRecord['email']) ?>" disabled>
                    <small class="form-help">Email cannot be modified once registered.</small>
                </div>

                <div class="form-group">
                    <label class="form-label">Account Role</label>
                    <div><?= status_badge($userRecord['role']) ?></div>
                </div>

                <div class="form-group">
                    <label for="name" class="form-label required">Display / Contact Name</label>
                    <input type="text" id="name" name="name" class="form-control"
                           value="<?= e($userRecord['name']) ?>" required>
                </div>

                <div class="form-group">
                    <label for="organization" class="form-label">Organization Name</label>
                    <input type="text" id="organization" name="organization" class="form-control"
                           value="<?= e($userRecord['organization'] ?? '') ?>" placeholder="e.g. Green Kitchen">
                </div>

                <div class="form-group">
                    <label for="phone" class="form-label">Phone Number</label>
                    <input type="tel" id="phone" name="phone" class="form-control"
                           value="<?= e($userRecord['phone'] ?? '') ?>" placeholder="e.g. +977-9841000000">
                </div>

                <button type="submit" class="btn btn-primary">
                    Save Changes
                </button>
            </form>
        </div>
    </div>

    <!-- Password Change Form -->
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">Change Password</h2>
        </div>
        <div class="card-body">
            <form action="<?= BASE_URL ?>/profile" method="POST">
                <?= csrf_field() ?>
                <input type="hidden" name="action" value="update_password">

                <div class="form-group">
                    <label for="new_password" class="form-label required">New Password</label>
                    <input type="password" id="new_password" name="new_password" class="form-control"
                           required minlength="6" placeholder="Enter new password">
                </div>

                <div class="form-group">
                    <label for="confirm_password" class="form-label required">Confirm New Password</label>
                    <input type="password" id="confirm_password" name="confirm_password" class="form-control"
                           required minlength="6" placeholder="Confirm new password">
                </div>

                <button type="submit" class="btn btn-secondary">
                    Update Password
                </button>
            </form>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../layout/footer.php'; ?>
