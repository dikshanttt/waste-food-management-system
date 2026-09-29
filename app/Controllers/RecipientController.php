<?php
// app/Controllers/RecipientController.php

declare(strict_types=1);

require_once __DIR__ . '/../Models/FoodListing.php';
require_once __DIR__ . '/../Models/FoodRequest.php';
require_once __DIR__ . '/../../config/config.php';

class RecipientController {
    private FoodListing $listingModel;
    private FoodRequest $requestModel;

    public function __construct() {
        $this->listingModel = new FoodListing();
        $this->requestModel = new FoodRequest();
    }

    public function browse(): void {
        $search = trim($_GET['search'] ?? '');
        $unit = trim($_GET['unit'] ?? '');

        // Only active, unexpired food listings
        $listings = $this->listingModel->getAvailable($search, $unit);

        require __DIR__ . '/../Views/recipient/browse.php';
    }

    public function detail(int $id): void {
        $listing = $this->listingModel->findById($id);
        if (!$listing) {
            http_response_code(404);
            require __DIR__ . '/../Views/errors/404.php';
            exit;
        }

        $hasExistingRequest = false;
        if (is_logged_in() && current_user()['role'] === ROLE_RECIPIENT) {
            $hasExistingRequest = $this->requestModel->hasActiveRequest($id, (int)$_SESSION['user_id']);
        }

        require __DIR__ . '/../Views/recipient/detail.php';
    }

    public function submitRequest(int $id): void {
        require_role(ROLE_RECIPIENT);

        if (!verify_csrf_token($_POST['csrf_token'] ?? null)) {
            set_flash('error', 'Session token expired.');
            header('Location: ' . BASE_URL . "/listings/{$id}");
            exit;
        }

        $recipientId = (int)$_SESSION['user_id'];
        $message = trim($_POST['message'] ?? '');

        $result = $this->requestModel->create($id, $recipientId, $message ?: null);

        if ($result['success']) {
            // Exact Figma brief success text:
            set_flash('success', 'Request submitted successfully. Status: Pending.');
            header('Location: ' . BASE_URL . '/recipient/requests');
            exit;
        } else {
            set_flash('error', $result['error'] ?? 'Could not submit request.');
            header('Location: ' . BASE_URL . "/listings/{$id}");
            exit;
        }
    }

    public function requests(): void {
        require_role(ROLE_RECIPIENT);
        $recipientId = (int)$_SESSION['user_id'];
        $requests = $this->requestModel->findByRecipient($recipientId);

        require __DIR__ . '/../Views/recipient/requests.php';
    }

    public function cancelRequest(int $requestId): void {
        require_role(ROLE_RECIPIENT);

        if (!verify_csrf_token($_POST['csrf_token'] ?? null)) {
            set_flash('error', 'Session token expired.');
            header('Location: ' . BASE_URL . '/recipient/requests');
            exit;
        }

        $recipientId = (int)$_SESSION['user_id'];
        $res = $this->requestModel->cancel($requestId, $recipientId);

        if ($res['success']) {
            set_flash('info', 'Your food request has been cancelled.');
        } else {
            set_flash('error', $res['error'] ?? 'Failed to cancel request.');
        }

        header('Location: ' . BASE_URL . '/recipient/requests');
        exit;
    }
}
