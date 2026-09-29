<?php
// app/Controllers/DonorController.php

declare(strict_types=1);

require_once __DIR__ . '/../Models/FoodListing.php';
require_once __DIR__ . '/../Models/FoodRequest.php';
require_once __DIR__ . '/../../config/config.php';

class DonorController {
    private FoodListing $listingModel;
    private FoodRequest $requestModel;

    public function __construct() {
        require_role(ROLE_DONOR);
        $this->listingModel = new FoodListing();
        $this->requestModel = new FoodRequest();
    }

    public function dashboard(): void {
        $donorId = (int)$_SESSION['user_id'];
        $listings = $this->listingModel->findByDonor($donorId);

        $activeListingsCount = $this->listingModel->countByDonor($donorId, STATUS_AVAILABLE);
        $pendingRequestsCount = $this->requestModel->countPendingForDonor($donorId);
        $totalListingsCount = count($listings);

        require __DIR__ . '/../Views/donor/dashboard.php';
    }

    public function showCreateListing(): void {
        $listing = null;
        $errors = $_SESSION['form_errors'] ?? [];
        $old = $_SESSION['form_old'] ?? [];
        unset($_SESSION['form_errors'], $_SESSION['form_old']);

        require __DIR__ . '/../Views/donor/listing_form.php';
    }

    public function createListing(): void {
        if (!verify_csrf_token($_POST['csrf_token'] ?? null)) {
            set_flash('error', 'Session token expired. Please try again.');
            header('Location: ' . BASE_URL . '/donor/listings/new');
            exit;
        }

        $donorId = (int)$_SESSION['user_id'];
        $title = trim($_POST['title'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $quantity = (int)($_POST['quantity'] ?? 0);
        $unit = trim($_POST['unit'] ?? 'Meals');
        $availableUntil = trim($_POST['available_until'] ?? '');
        $pickupLocation = trim($_POST['pickup_location'] ?? '');
        $pickupInstructions = trim($_POST['pickup_instructions'] ?? '');
        $organizationName = trim($_POST['organization_name'] ?? '');

        $errors = [];
        if (empty($title)) $errors['title'] = 'Food title is required.';
        if (empty($description)) $errors['description'] = 'Description is required.';
        if ($quantity <= 0) $errors['quantity'] = 'Quantity must be greater than zero.';
        if (empty($unit)) $errors['unit'] = 'Unit of measure is required.';
        if (empty($pickupLocation)) $errors['pickup_location'] = 'Pickup location is required.';

        if (empty($availableUntil)) {
            $errors['available_until'] = 'Availability deadline is required.';
        } else {
            $availTs = strtotime($availableUntil);
            if (!$availTs || $availTs <= time()) {
                $errors['available_until'] = 'Availability deadline must be a future date and time.';
            }
        }

        if (!empty($errors)) {
            $_SESSION['form_errors'] = $errors;
            $_SESSION['form_old'] = $_POST;
            set_flash('error', 'Please correct the errors in the form.');
            header('Location: ' . BASE_URL . '/donor/listings/new');
            exit;
        }

        $newId = $this->listingModel->create(
            $donorId,
            $title,
            $description,
            $quantity,
            $unit,
            date('Y-m-d H:i:s', strtotime($availableUntil)),
            $pickupLocation,
            $pickupInstructions ?: null,
            $organizationName ?: null
        );

        set_flash('success', "Food listing '{$title}' created successfully! Recipients can now request it.");
        header('Location: ' . BASE_URL . '/donor/dashboard');
        exit;
    }

    public function showEditListing(int $id): void {
        $donorId = (int)$_SESSION['user_id'];
        $listing = $this->listingModel->findById($id);

        if (!$listing || (int)$listing['donor_id'] !== $donorId) {
            http_response_code(403);
            require __DIR__ . '/../Views/errors/403.php';
            exit;
        }

        if ($listing['status'] !== STATUS_AVAILABLE) {
            set_flash('error', 'This listing is locked and cannot be edited because it is no longer available.');
            header('Location: ' . BASE_URL . '/donor/dashboard');
            exit;
        }

        $errors = $_SESSION['form_errors'] ?? [];
        $old = $_SESSION['form_old'] ?? [];
        unset($_SESSION['form_errors'], $_SESSION['form_old']);

        require __DIR__ . '/../Views/donor/listing_form.php';
    }

    public function updateListing(int $id): void {
        if (!verify_csrf_token($_POST['csrf_token'] ?? null)) {
            set_flash('error', 'Session token expired.');
            header('Location: ' . BASE_URL . "/donor/listings/{$id}/edit");
            exit;
        }

        $donorId = (int)$_SESSION['user_id'];
        $listing = $this->listingModel->findById($id);

        if (!$listing || (int)$listing['donor_id'] !== $donorId) {
            http_response_code(403);
            require __DIR__ . '/../Views/errors/403.php';
            exit;
        }

        $title = trim($_POST['title'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $quantity = (int)($_POST['quantity'] ?? 0);
        $unit = trim($_POST['unit'] ?? 'Meals');
        $availableUntil = trim($_POST['available_until'] ?? '');
        $pickupLocation = trim($_POST['pickup_location'] ?? '');
        $pickupInstructions = trim($_POST['pickup_instructions'] ?? '');
        $organizationName = trim($_POST['organization_name'] ?? '');

        $errors = [];
        if (empty($title)) $errors['title'] = 'Food title is required.';
        if (empty($description)) $errors['description'] = 'Description is required.';
        if ($quantity <= 0) $errors['quantity'] = 'Quantity must be greater than zero.';
        if (empty($unit)) $errors['unit'] = 'Unit of measure is required.';
        if (empty($pickupLocation)) $errors['pickup_location'] = 'Pickup location is required.';

        if (empty($availableUntil)) {
            $errors['available_until'] = 'Availability deadline is required.';
        } else {
            $availTs = strtotime($availableUntil);
            if (!$availTs || $availTs <= time()) {
                $errors['available_until'] = 'Availability deadline must be a future date and time.';
            }
        }

        if (!empty($errors)) {
            $_SESSION['form_errors'] = $errors;
            $_SESSION['form_old'] = $_POST;
            set_flash('error', 'Please correct the errors in the form.');
            header('Location: ' . BASE_URL . "/donor/listings/{$id}/edit");
            exit;
        }

        $this->listingModel->update(
            $id,
            $donorId,
            $title,
            $description,
            $quantity,
            $unit,
            date('Y-m-d H:i:s', strtotime($availableUntil)),
            $pickupLocation,
            $pickupInstructions ?: null,
            $organizationName ?: null
        );

        set_flash('success', "Listing '{$title}' updated successfully.");
        header('Location: ' . BASE_URL . '/donor/dashboard');
        exit;
    }

    public function deleteListing(int $id): void {
        if (!verify_csrf_token($_POST['csrf_token'] ?? null)) {
            set_flash('error', 'Session token expired.');
            header('Location: ' . BASE_URL . '/donor/dashboard');
            exit;
        }

        $donorId = (int)$_SESSION['user_id'];
        $listing = $this->listingModel->findById($id);

        if (!$listing || (int)$listing['donor_id'] !== $donorId) {
            http_response_code(403);
            require __DIR__ . '/../Views/errors/403.php';
            exit;
        }

        $this->listingModel->deleteOrMarkUnavailable($id, $donorId);
        set_flash('success', 'Listing removed or marked unavailable.');
        header('Location: ' . BASE_URL . '/donor/dashboard');
        exit;
    }

    public function requests(): void {
        $donorId = (int)$_SESSION['user_id'];
        $requests = $this->requestModel->findByDonor($donorId);
        require __DIR__ . '/../Views/donor/requests.php';
    }

    public function approveRequest(int $requestId): void {
        if (!verify_csrf_token($_POST['csrf_token'] ?? null)) {
            set_flash('error', 'Session token expired.');
            header('Location: ' . BASE_URL . '/donor/requests');
            exit;
        }

        $donorId = (int)$_SESSION['user_id'];
        $res = $this->requestModel->approve($requestId, $donorId);

        if ($res['success']) {
            set_flash('success', 'Request approved successfully! The food listing is now Reserved for collection.');
        } else {
            set_flash('error', $res['error'] ?? 'Failed to approve request.');
        }

        header('Location: ' . BASE_URL . '/donor/requests');
        exit;
    }

    public function rejectRequest(int $requestId): void {
        if (!verify_csrf_token($_POST['csrf_token'] ?? null)) {
            set_flash('error', 'Session token expired.');
            header('Location: ' . BASE_URL . '/donor/requests');
            exit;
        }

        $donorId = (int)$_SESSION['user_id'];
        $res = $this->requestModel->reject($requestId, $donorId);

        if ($res['success']) {
            set_flash('success', 'Request rejected.');
        } else {
            set_flash('error', $res['error'] ?? 'Failed to reject request.');
        }

        header('Location: ' . BASE_URL . '/donor/requests');
        exit;
    }
}
