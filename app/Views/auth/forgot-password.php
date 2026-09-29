<?php
// app/Views/auth/forgot-password.php
declare(strict_types=1);
$pageTitle = 'Reset Password';
require __DIR__ . '/../layout/header.php';
?>

<div style="max-width: 440px; margin: 2rem auto;">
    <div class="card">
        <div class="card-header" style="text-align: center; justify-content: center; flex-direction: column;">
            <div class="brand-icon" style="margin-bottom: 0.5rem;" aria-hidden="true">🔑</div>
            <h1 class="card-title" style="font-size: 1.5rem;">Password Recovery</h1>
            <p style="font-size: 0.85rem; color: var(--color-text-muted);">Enter your email to receive recovery instructions</p>
        </div>
        <div class="card-body">
            <form action="<?= BASE_URL ?>/forgot-password" method="POST" novalidate>
                <?= csrf_field() ?>

                <div class="form-group">
                    <label for="email" class="form-label required">Email Address</label>
                    <input type="email" id="email" name="email" class="form-control" required placeholder="your.email@example.org" autofocus>
                    <small class="form-help">In this MVP, instructions or default recovery details will be confirmed directly on submission.</small>
                </div>

                <button type="submit" class="btn btn-secondary btn-block btn-lg" style="margin-top: 1rem;">
                    Send Instructions
                </button>
            </form>

            <div style="margin-top: 1.5rem; text-align: center; font-size: 0.9rem; border-top: 1px solid var(--color-border-light); padding-top: 1rem;">
                Remembered your password? <a href="<?= BASE_URL ?>/login">Return to Sign In</a>
            </div>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../layout/footer.php'; ?>
