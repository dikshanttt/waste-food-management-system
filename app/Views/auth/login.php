<?php
// app/Views/auth/login.php
declare(strict_types=1);
$pageTitle = 'Sign In';
require __DIR__ . '/../layout/header.php';
?>

<div style="max-width: 440px; margin: 1.5rem auto;">
    <div class="card">
        <div class="card-header" style="text-align: center; justify-content: center; flex-direction: column;">
            <div class="brand-icon" style="margin-bottom: 0.5rem;" aria-hidden="true">🍲</div>
            <h1 class="card-title" style="font-size: 1.5rem;">Welcome Back</h1>
            <p style="font-size: 0.85rem; color: var(--color-text-muted);">Sign in to access your donations or food requests</p>
        </div>
        <div class="card-body">
            <form action="<?= BASE_URL ?>/login" method="POST" novalidate>
                <?= csrf_field() ?>

                <div class="form-group">
                    <label for="email" class="form-label required">Email Address</label>
                    <input type="email" id="email" name="email" class="form-control"
                           value="<?= e($oldEmail ?? '') ?>" required autocomplete="email" autofocus
                           placeholder="e.g. donor@wastefood.org">
                </div>

                <div class="form-group">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.4rem;">
                        <label for="password" class="form-label required" style="margin-bottom: 0;">Password</label>
                        <a href="<?= BASE_URL ?>/forgot-password" style="font-size: 0.8rem; font-weight: 500;">Forgot Password?</a>
                    </div>
                    <input type="password" id="password" name="password" class="form-control"
                           required autocomplete="current-password" placeholder="Enter your password">
                </div>

                <button type="submit" class="btn btn-primary btn-block btn-lg" style="margin-top: 1.5rem;">
                    Sign In
                </button>
            </form>

            <div style="margin-top: 1.5rem; text-align: center; font-size: 0.9rem; border-top: 1px solid var(--color-border-light); padding-top: 1.25rem;">
                Don't have an account? <br>
                <div style="margin-top: 0.5rem; display: flex; gap: 0.5rem; justify-content: center;">
                    <a href="<?= BASE_URL ?>/register?role=donor" class="btn btn-outline btn-sm">Register as Donor</a>
                    <a href="<?= BASE_URL ?>/register?role=recipient" class="btn btn-outline btn-sm">Register as Recipient</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Administrator Credentials Helper -->
    <div class="card" style="margin-top: 1.5rem; background-color: var(--color-surface-alt); border-style: dashed;">
        <div class="card-body" style="padding: 1rem 1.25rem;">
            <div style="font-size: 0.8rem; font-weight: 700; text-transform: uppercase; color: var(--color-text-muted); margin-bottom: 0.6rem;">
                🔑 Pre-provisioned Administrator Account
            </div>
            <div style="display: flex; flex-direction: column; gap: 0.5rem; font-size: 0.85rem;">
                <button type="button" class="btn btn-outline btn-sm" style="text-align: left; justify-content: flex-start;"
                        onclick="fillLogin('admin@wastefood.org', 'password123')">
                    <strong>System Administrator</strong>: admin@wastefood.org (Click to Fill)
                </button>
                <div style="font-size: 0.8rem; color: var(--color-text-muted); margin-top: 0.25rem;">
                    💡 To test donor or recipient features, use the <strong>Create Account</strong> buttons above to register your own accounts.
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function fillLogin(email, password) {
    document.getElementById('email').value = email;
    document.getElementById('password').value = password;
}
</script>

<?php require __DIR__ . '/../layout/footer.php'; ?>
