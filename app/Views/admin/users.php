<?php
// app/Views/admin/users.php
declare(strict_types=1);
$pageTitle = 'Manage Users';
require __DIR__ . '/../layout/header.php';
?>

<div class="page-header">
    <div>
        <h1 class="page-title">Users Directory</h1>
        <p class="page-subtitle">Total Registered Users: <strong><?= count($users) ?></strong> (Role-based access & status controls)</p>
    </div>
    <div>
        <a href="<?= BASE_URL ?>/admin/dashboard" class="btn btn-outline">
            &larr; Admin HQ
        </a>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h2 class="card-title">Registered Accounts</h2>
        <span style="font-size: 0.85rem; color: var(--color-text-muted);">
            Deactivating an account prevents sign-in without breaking historical records.
        </span>
    </div>
    <div class="card-body" style="padding: 0;">
        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th scope="col">ID</th>
                        <th scope="col">User / Contact</th>
                        <th scope="col">Email Address</th>
                        <th scope="col">Role</th>
                        <th scope="col">Organization</th>
                        <th scope="col">Status</th>
                        <th scope="col" style="text-align: right;">Moderation Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $u): ?>
                        <tr>
                            <td data-label="ID">#<?= (int)$u['id'] ?></td>

                            <td data-label="User / Contact">
                                <strong><?= e($u['name']) ?></strong>
                                <?php if (!empty($u['phone'])): ?>
                                    <div style="font-size: 0.8rem; color: var(--color-text-muted);">📞 <?= e($u['phone']) ?></div>
                                <?php endif; ?>
                            </td>

                            <td data-label="Email Address">
                                <?= e($u['email']) ?>
                            </td>

                            <td data-label="Role">
                                <span class="badge <?= $u['role'] === ROLE_ADMIN ? 'badge-rejected' : ($u['role'] === ROLE_DONOR ? 'badge-available' : 'badge-reserved') ?>">
                                    <?= e(strtoupper($u['role'])) ?>
                                </span>
                            </td>

                            <td data-label="Organization">
                                <?= !empty($u['organization']) ? e($u['organization']) : '-' ?>
                            </td>

                            <td data-label="Status">
                                <?= (int)$u['is_active'] === 1 ? status_badge('active') : status_badge('inactive') ?>
                            </td>

                            <td data-label="Moderation Action" style="text-align: right;">
                                <?php if ($u['role'] === ROLE_ADMIN): ?>
                                    <span style="font-size: 0.8rem; color: var(--color-text-muted); font-style: italic;">Protected Admin</span>
                                <?php else: ?>
                                    <form action="<?= BASE_URL ?>/admin/users/<?= (int)$u['id'] ?>/toggle" method="POST" style="display:inline;">
                                        <?= csrf_field() ?>
                                        <?php if ((int)$u['is_active'] === 1): ?>
                                            <button type="submit" class="btn btn-outline btn-sm" style="color: var(--color-error); border-color: var(--color-error-border);"
                                                    data-confirm="Are you sure you want to deactivate user '<?= e($u['name']) ?>' (<?= e($u['email']) ?>)? This will prevent them from signing in.">
                                                ✖ Deactivate
                                            </button>
                                        <?php else: ?>
                                            <button type="submit" class="btn btn-primary btn-sm"
                                                    data-confirm="Reactivate account for '<?= e($u['name']) ?>'?">
                                                ✔ Activate
                                            </button>
                                        <?php endif; ?>
                                    </form>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../layout/footer.php'; ?>
