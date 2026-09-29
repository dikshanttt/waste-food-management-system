<?php
// public/index.php - Front Controller & Application Router

declare(strict_types=1);

// Error reporting for development
ini_set('display_errors', '1');
error_reporting(E_ALL);

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';

// Controllers
require_once __DIR__ . '/../app/Controllers/AuthController.php';
require_once __DIR__ . '/../app/Controllers/DonorController.php';
require_once __DIR__ . '/../app/Controllers/RecipientController.php';
require_once __DIR__ . '/../app/Controllers/AdminController.php';

// Parse URI and Method
$requestUri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
$method = strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');

// Handle built-in server serving static files directly
if (php_sapi_name() === 'cli-server') {
    $filePath = __DIR__ . $requestUri;
    if ($requestUri !== '/' && file_exists($filePath) && !is_dir($filePath)) {
        return false;
    }
}

// Strip BASE_URL from request path if deployed in subdirectory
$path = $requestUri;
if (!empty(BASE_URL) && str_starts_with($path, BASE_URL)) {
    $path = substr($path, strlen(BASE_URL));
}
$path = '/' . trim($path, '/');
if ($path === '//') $path = '/';

// Router Dispatcher
try {
    // 1. Home / Root Route
    if ($path === '/' && $method === 'GET') {
        if (is_logged_in()) {
            $role = $_SESSION['user_role'];
            if ($role === ROLE_ADMIN) {
                header('Location: ' . BASE_URL . '/admin/dashboard');
                exit;
            } elseif ($role === ROLE_DONOR) {
                header('Location: ' . BASE_URL . '/donor/dashboard');
                exit;
            }
        }
        (new RecipientController())->browse();
        exit;
    }

    // 2. Auth Routes
    if ($path === '/login') {
        $c = new AuthController();
        $method === 'POST' ? $c->login() : $c->showLogin();
        exit;
    }

    if ($path === '/logout') {
        (new AuthController())->logout();
        exit;
    }

    if ($path === '/register') {
        $c = new AuthController();
        $method === 'POST' ? $c->register() : $c->showRegister();
        exit;
    }

    if ($path === '/forgot-password') {
        $c = new AuthController();
        $method === 'POST' ? $c->handleForgotPassword() : $c->showForgotPassword();
        exit;
    }

    if ($path === '/profile') {
        $c = new AuthController();
        $method === 'POST' ? $c->updateProfile() : $c->showProfile();
        exit;
    }

    // 3. Recipient & Public Listings Routes
    if ($path === '/listings' && $method === 'GET') {
        (new RecipientController())->browse();
        exit;
    }

    if (preg_match('#^/listings/(\d+)$#', $path, $matches) && $method === 'GET') {
        (new RecipientController())->detail((int)$matches[1]);
        exit;
    }

    if (preg_match('#^/listings/(\d+)/request$#', $path, $matches) && $method === 'POST') {
        (new RecipientController())->submitRequest((int)$matches[1]);
        exit;
    }

    if ($path === '/recipient/requests' && $method === 'GET') {
        (new RecipientController())->requests();
        exit;
    }

    if (preg_match('#^/recipient/requests/(\d+)/cancel$#', $path, $matches) && $method === 'POST') {
        (new RecipientController())->cancelRequest((int)$matches[1]);
        exit;
    }

    // 4. Donor Routes
    if ($path === '/donor/dashboard' && $method === 'GET') {
        (new DonorController())->dashboard();
        exit;
    }

    if ($path === '/donor/listings/new') {
        $c = new DonorController();
        $method === 'POST' ? $c->createListing() : $c->showCreateListing();
        exit;
    }

    if (preg_match('#^/donor/listings/(\d+)/edit$#', $path, $matches)) {
        $c = new DonorController();
        $method === 'POST' ? $c->updateListing((int)$matches[1]) : $c->showEditListing((int)$matches[1]);
        exit;
    }

    if (preg_match('#^/donor/listings/(\d+)/delete$#', $path, $matches) && $method === 'POST') {
        (new DonorController())->deleteListing((int)$matches[1]);
        exit;
    }

    if ($path === '/donor/requests' && $method === 'GET') {
        (new DonorController())->requests();
        exit;
    }

    if (preg_match('#^/donor/requests/(\d+)/approve$#', $path, $matches) && $method === 'POST') {
        (new DonorController())->approveRequest((int)$matches[1]);
        exit;
    }

    if (preg_match('#^/donor/requests/(\d+)/reject$#', $path, $matches) && $method === 'POST') {
        (new DonorController())->rejectRequest((int)$matches[1]);
        exit;
    }

    // 5. Admin Routes
    if ($path === '/admin/dashboard' && $method === 'GET') {
        (new AdminController())->dashboard();
        exit;
    }

    if ($path === '/admin/users' && $method === 'GET') {
        (new AdminController())->users();
        exit;
    }

    if (preg_match('#^/admin/users/(\d+)/toggle$#', $path, $matches) && $method === 'POST') {
        (new AdminController())->toggleUser((int)$matches[1]);
        exit;
    }

    if ($path === '/admin/listings' && $method === 'GET') {
        (new AdminController())->listings();
        exit;
    }

    if (preg_match('#^/admin/listings/(\d+)/remove$#', $path, $matches) && $method === 'POST') {
        (new AdminController())->removeListing((int)$matches[1]);
        exit;
    }

    if ($path === '/admin/requests' && $method === 'GET') {
        (new AdminController())->requests();
        exit;
    }

    if ($path === '/admin/reports' && $method === 'GET') {
        (new AdminController())->reports();
        exit;
    }

    // 404 Route Fallback
    http_response_code(404);
    require __DIR__ . '/../app/Views/errors/404.php';
    exit;

} catch (Throwable $e) {
    error_log("Unhandled Application Exception: " . $e->getMessage() . " at " . $e->getFile() . ":" . $e->getLine());
    http_response_code(500);
    echo "<!DOCTYPE html><html><head><title>System Error</title><link rel='stylesheet' href='" . BASE_URL . "/css/style.css'></head><body>";
    echo "<div class='container' style='margin-top: 3rem;'><div class='alert alert-error'>";
    echo "<h3>System Error Encountered</h3>";
    echo "<p>" . htmlspecialchars($e->getMessage()) . "</p>";
    echo "</div><p><a href='" . BASE_URL . "/' class='btn btn-primary'>Return Home</a></p></div></body></html>";
    exit;
}
