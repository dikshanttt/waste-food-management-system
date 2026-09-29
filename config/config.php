<?php
// config/config.php - Global Application Settings & Helper Functions

declare(strict_types=1);

// Application Configuration
define('APP_NAME', 'Waste Food Management System');
define('APP_VERSION', '1.0 MVP');
define('APP_TIMEZONE', 'Asia/Kathmandu');
date_default_timezone_set(APP_TIMEZONE);

// Base URL detection
$scriptDir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? ''));
$baseUrl = ($scriptDir === '/' || $scriptDir === '') ? '' : rtrim($scriptDir, '/');
define('BASE_URL', $baseUrl);

// Status Enums & Labels
define('ROLE_DONOR', 'donor');
define('ROLE_RECIPIENT', 'recipient');
define('ROLE_ADMIN', 'admin');

define('STATUS_AVAILABLE', 'available');
define('STATUS_RESERVED', 'reserved');
define('STATUS_UNAVAILABLE', 'unavailable');
define('STATUS_EXPIRED', 'expired');

define('REQ_PENDING', 'pending');
define('REQ_APPROVED', 'approved');
define('REQ_REJECTED', 'rejected');
define('REQ_CANCELLED', 'cancelled');

// Initialize Session securely
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.cookie_httponly', '1');
    ini_set('session.use_only_cookies', '1');
    ini_set('session.cookie_samesite', 'Lax');
    session_start();
}

// Generate / Get CSRF Token
function csrf_token(): string {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrf_field(): string {
    return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8') . '">';
}

function verify_csrf_token(?string $token): bool {
    if (empty($token) || empty($_SESSION['csrf_token'])) {
        return false;
    }
    return hash_equals($_SESSION['csrf_token'], $token);
}

// Flash Message Helpers
function set_flash(string $type, string $message): void {
    $_SESSION['flash'] = [
        'type' => $type, // 'success', 'error', 'info', 'warning'
        'message' => $message
    ];
}

function get_flash(): ?array {
    if (!empty($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}

// Auth Helper Functions
function is_logged_in(): bool {
    return !empty($_SESSION['user_id']) && !empty($_SESSION['user_role']);
}

function current_user(): ?array {
    if (!is_logged_in()) {
        return null;
    }
    return [
        'id' => (int)$_SESSION['user_id'],
        'name' => $_SESSION['user_name'] ?? '',
        'email' => $_SESSION['user_email'] ?? '',
        'role' => $_SESSION['user_role'] ?? '',
        'organization' => $_SESSION['user_org'] ?? ''
    ];
}

function require_auth(?string $allowedRole = null): void {
    if (!is_logged_in()) {
        set_flash('error', 'Please sign in to access that page.');
        header('Location: ' . BASE_URL . '/login');
        exit;
    }

    if ($allowedRole !== null && $_SESSION['user_role'] !== $allowedRole && $_SESSION['user_role'] !== ROLE_ADMIN) {
        http_response_code(403);
        require_once __DIR__ . '/../app/Views/errors/403.php';
        exit;
    }
}

function require_role(string $role): void {
    if (!is_logged_in()) {
        set_flash('error', 'Please sign in to access that page.');
        header('Location: ' . BASE_URL . '/login');
        exit;
    }
    if ($_SESSION['user_role'] !== $role) {
        http_response_code(403);
        require_once __DIR__ . '/../app/Views/errors/403.php';
        exit;
    }
}

// HTML Escaping Helper
function e(?string $string): string {
    return htmlspecialchars((string)($string ?? ''), ENT_QUOTES, 'UTF-8');
}

// Badge Formatter matching Figma brief badges
// Badges: ⏳ Pending, ✔ Approved, ✖ Rejected, ⛔ Cancelled, 🔒 Reserved
function status_badge(string $status): string {
    $normalized = strtolower(trim($status));
    return match($normalized) {
        'pending' => '<span class="badge badge-pending" aria-label="Status: Pending"><span class="badge-icon">⏳</span> Pending</span>',
        'approved' => '<span class="badge badge-approved" aria-label="Status: Approved"><span class="badge-icon">✔</span> Approved</span>',
        'rejected' => '<span class="badge badge-rejected" aria-label="Status: Rejected"><span class="badge-icon">✖</span> Rejected</span>',
        'cancelled' => '<span class="badge badge-cancelled" aria-label="Status: Cancelled"><span class="badge-icon">⛔</span> Cancelled</span>',
        'available' => '<span class="badge badge-available" aria-label="Status: Available"><span class="badge-icon">🌿</span> Available</span>',
        'reserved' => '<span class="badge badge-reserved" aria-label="Status: Reserved"><span class="badge-icon">🔒</span> Reserved</span>',
        'unavailable' => '<span class="badge badge-unavailable" aria-label="Status: Unavailable"><span class="badge-icon">⛔</span> Unavailable</span>',
        'expired' => '<span class="badge badge-expired" aria-label="Status: Expired"><span class="badge-icon">⌛</span> Expired</span>',
        'active' => '<span class="badge badge-approved" aria-label="Status: Active"><span class="badge-icon">✔</span> Active</span>',
        'inactive' => '<span class="badge badge-rejected" aria-label="Status: Deactivated"><span class="badge-icon">✖</span> Inactive</span>',
        default => '<span class="badge badge-default">' . e(ucfirst($status)) . '</span>'
    };
}

// Format date nicely
function format_dt(?string $datetime, bool $includeTime = true): string {
    if (empty($datetime)) return '-';
    $timestamp = strtotime($datetime);
    if (!$timestamp) return '-';
    
    // Relative human touch for today/yesterday
    $datePart = date('Y-m-d', $timestamp);
    $today = date('Y-m-d');
    $yesterday = date('Y-m-d', strtotime('-1 day'));

    if ($datePart === $today) {
        $prefix = 'Today';
    } elseif ($datePart === $yesterday) {
        $prefix = 'Yesterday';
    } else {
        $prefix = date('M j, Y', $timestamp);
    }

    if ($includeTime) {
        return $prefix . ' at ' . date('g:i A', $timestamp);
    }
    return $prefix;
}
