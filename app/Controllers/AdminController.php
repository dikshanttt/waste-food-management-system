<?php
// app/Controllers/AdminController.php

declare(strict_types=1);

require_once __DIR__ . '/../Models/User.php';
require_once __DIR__ . '/../Models/FoodListing.php';
require_once __DIR__ . '/../Models/FoodRequest.php';
require_once __DIR__ . '/../../config/config.php';

class AdminController {
    private User $userModel;
    private FoodListing $listingModel;
    private FoodRequest $requestModel;

    public function __construct() {
        require_role(ROLE_ADMIN);
        $this->userModel = new User();
        $this->listingModel = new FoodListing();
        $this->requestModel = new FoodRequest();
    }

    public function dashboard(): void {
        $totalUsers = $this->userModel->countAll();
        $totalListings = $this->listingModel->countAll();
        $totalRequests = $this->requestModel->countAll();

        $recentListings = $this->listingModel->getAllForAdmin(5);

        require __DIR__ . '/../Views/admin/dashboard.php';
    }

    public function users(): void {
        $users = $this->userModel->getAll(200);
        require __DIR__ . '/../Views/admin/users.php';
    }

    public function toggleUser(int $id): void {
        if (!verify_csrf_token($_POST['csrf_token'] ?? null)) {
            set_flash('error', 'Session token expired.');
            header('Location: ' . BASE_URL . '/admin/users');
            exit;
        }

        // Prevent admin deactivating self
        if ($id === (int)$_SESSION['user_id']) {
            set_flash('error', 'You cannot deactivate your own administrative account.');
            header('Location: ' . BASE_URL . '/admin/users');
            exit;
        }

        $this->userModel->toggleActive($id);
        set_flash('success', 'User account status updated.');
        header('Location: ' . BASE_URL . '/admin/users');
        exit;
    }

    public function listings(): void {
        $listings = $this->listingModel->getAllForAdmin(200);
        require __DIR__ . '/../Views/admin/listings.php';
    }

    public function removeListing(int $id): void {
        if (!verify_csrf_token($_POST['csrf_token'] ?? null)) {
            set_flash('error', 'Session token expired.');
            header('Location: ' . BASE_URL . '/admin/listings');
            exit;
        }

        $this->listingModel->adminRemoveOrDeactivate($id);
        set_flash('success', "Listing #{$id} has been moderated (removed or set unavailable).");
        header('Location: ' . BASE_URL . '/admin/listings');
        exit;
    }

    public function requests(): void {
        $requests = $this->requestModel->getAllForAdmin(200);
        require __DIR__ . '/../Views/admin/requests.php';
    }

    public function reports(): void {
        $stats = [
            'users_total' => $this->userModel->countAll(),
            'users_donors' => $this->userModel->countByRole(ROLE_DONOR),
            'users_recipients' => $this->userModel->countByRole(ROLE_RECIPIENT),
            'users_admins' => $this->userModel->countByRole(ROLE_ADMIN),

            'listings_total' => $this->listingModel->countAll(),
            'listings_available' => $this->listingModel->countByStatus(STATUS_AVAILABLE),
            'listings_reserved' => $this->listingModel->countByStatus(STATUS_RESERVED),
            'listings_unavailable' => $this->listingModel->countByStatus(STATUS_UNAVAILABLE),
            'listings_expired' => $this->listingModel->countByStatus(STATUS_EXPIRED),

            'requests_total' => $this->requestModel->countAll(),
            'requests_pending' => $this->requestModel->countByStatus(REQ_PENDING),
            'requests_approved' => $this->requestModel->countByStatus(REQ_APPROVED),
            'requests_rejected' => $this->requestModel->countByStatus(REQ_REJECTED),
            'requests_cancelled' => $this->requestModel->countByStatus(REQ_CANCELLED),
        ];

        require __DIR__ . '/../Views/admin/reports.php';
    }
}
