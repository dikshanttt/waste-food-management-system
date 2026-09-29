<?php
// app/Views/layout/navbar.php
declare(strict_types=1);
require_once __DIR__ . '/../../../config/config.php';
$user = current_user();
$currentRoute = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
if (!empty(BASE_URL) && str_starts_with($currentRoute, BASE_URL)) {
    $currentRoute = substr($currentRoute, strlen(BASE_URL));
}
$currentRoute = rtrim($currentRoute, '/') ?: '/';
?>
<header class="navbar">
    <div class="container nav-container">
        <a href="<?= BASE_URL ?>/" class="brand-link" aria-label="Waste Food Management System Home">
            <div class="brand-icon" aria-hidden="true">🍲</div>
            <div class="brand-title">
                <span class="brand-name">FoodRescue</span>
                <span class="brand-tagline">Waste Food Management</span>
            </div>
        </a>

        <button class="mobile-nav-toggle" aria-label="Toggle navigation menu" aria-expanded="false">
            ☰
        </button>

        <nav class="nav-links" id="primary-navigation" aria-label="Primary Navigation">
            <?php if (!$user): ?>
                <div class="nav-item">
                    <a href="<?= BASE_URL ?>/listings" class="<?= $currentRoute === '/listings' ? 'active' : '' ?>">
                        🔍 Browse Food
                    </a>
                </div>
                <div class="nav-item">
                    <a href="<?= BASE_URL ?>/login" class="<?= $currentRoute === '/login' ? 'active' : '' ?>">
                        Sign In
                    </a>
                </div>
                <div class="nav-item">
                    <a href="<?= BASE_URL ?>/register" class="btn btn-primary btn-sm <?= $currentRoute === '/register' ? 'active' : '' ?>">
                        Create Account
                    </a>
                </div>
            <?php elseif ($user['role'] === ROLE_DONOR): ?>
                <div class="nav-item">
                    <a href="<?= BASE_URL ?>/donor/dashboard" class="<?= $currentRoute === '/donor/dashboard' ? 'active' : '' ?>">
                        📊 Dashboard
                    </a>
                </div>
                <div class="nav-item">
                    <a href="<?= BASE_URL ?>/donor/listings/new" class="<?= $currentRoute === '/donor/listings/new' ? 'active' : '' ?>">
                        ➕ Add Listing
                    </a>
                </div>
                <div class="nav-item">
                    <a href="<?= BASE_URL ?>/donor/requests" class="<?= $currentRoute === '/donor/requests' ? 'active' : '' ?>">
                        📥 Food Requests
                    </a>
                </div>
                <div class="nav-item">
                    <a href="<?= BASE_URL ?>/listings">
                        🔍 Browse All
                    </a>
                </div>
                <div class="nav-item">
                    <div class="user-badge">
                        <span>👤 <?= e($user['name']) ?></span>
                        <span class="role-tag">Donor</span>
                    </div>
                </div>
                <div class="nav-item">
                    <a href="<?= BASE_URL ?>/profile" title="My Profile" class="<?= $currentRoute === '/profile' ? 'active' : '' ?>">⚙ Profile</a>
                </div>
                <div class="nav-item">
                    <a href="<?= BASE_URL ?>/logout" class="btn btn-outline btn-sm">Sign Out</a>
                </div>
            <?php elseif ($user['role'] === ROLE_RECIPIENT): ?>
                <div class="nav-item">
                    <a href="<?= BASE_URL ?>/listings" class="<?= in_array($currentRoute, ['/listings', '/']) ? 'active' : '' ?>">
                        🍲 Available Food
                    </a>
                </div>
                <div class="nav-item">
                    <a href="<?= BASE_URL ?>/recipient/requests" class="<?= $currentRoute === '/recipient/requests' ? 'active' : '' ?>">
                        📋 My Requests
                    </a>
                </div>
                <div class="nav-item">
                    <div class="user-badge">
                        <span>👤 <?= e($user['name']) ?></span>
                        <span class="role-tag">Recipient</span>
                    </div>
                </div>
                <div class="nav-item">
                    <a href="<?= BASE_URL ?>/profile" title="My Profile" class="<?= $currentRoute === '/profile' ? 'active' : '' ?>">⚙ Profile</a>
                </div>
                <div class="nav-item">
                    <a href="<?= BASE_URL ?>/logout" class="btn btn-outline btn-sm">Sign Out</a>
                </div>
            <?php elseif ($user['role'] === ROLE_ADMIN): ?>
                <div class="nav-item">
                    <a href="<?= BASE_URL ?>/admin/dashboard" class="<?= $currentRoute === '/admin/dashboard' ? 'active' : '' ?>">
                        ⚡ Admin HQ
                    </a>
                </div>
                <div class="nav-item">
                    <a href="<?= BASE_URL ?>/admin/users" class="<?= $currentRoute === '/admin/users' ? 'active' : '' ?>">
                        👥 Users
                    </a>
                </div>
                <div class="nav-item">
                    <a href="<?= BASE_URL ?>/admin/listings" class="<?= $currentRoute === '/admin/listings' ? 'active' : '' ?>">
                        🍲 Listings
                    </a>
                </div>
                <div class="nav-item">
                    <a href="<?= BASE_URL ?>/admin/requests" class="<?= $currentRoute === '/admin/requests' ? 'active' : '' ?>">
                        📥 Requests
                    </a>
                </div>
                <div class="nav-item">
                    <a href="<?= BASE_URL ?>/admin/reports" class="<?= $currentRoute === '/admin/reports' ? 'active' : '' ?>">
                        📈 Reports
                    </a>
                </div>
                <div class="nav-item">
                    <div class="user-badge" style="background-color: #FFEBEE; border-color: #FFCDD2;">
                        <span>👑 <?= e($user['name']) ?></span>
                        <span class="role-tag" style="background-color: var(--color-error); color: #fff;">Admin</span>
                    </div>
                </div>
                <div class="nav-item">
                    <a href="<?= BASE_URL ?>/logout" class="btn btn-outline btn-sm">Sign Out</a>
                </div>
            <?php endif; ?>
        </nav>
    </div>
</header>
