<?php
// app/Models/FoodRequest.php

declare(strict_types=1);

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/config.php';

class FoodRequest {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    public function hasActiveRequest(int $listingId, int $recipientId): bool {
        $stmt = $this->db->prepare("
            SELECT COUNT(*) FROM food_requests
            WHERE listing_id = ? AND recipient_id = ? AND status IN ('pending', 'approved')
        ");
        $stmt->execute([$listingId, $recipientId]);
        return ((int)$stmt->fetchColumn()) > 0;
    }

    public function create(int $listingId, int $recipientId, ?string $message = null): array {
        // 1. Fetch listing details to validate state
        $listingStmt = $this->db->prepare("SELECT * FROM food_listings WHERE id = ? FOR UPDATE");
        $this->db->beginTransaction();
        try {
            $listingStmt->execute([$listingId]);
            $listing = $listingStmt->fetch();

            if (!$listing) {
                $this->db->rollBack();
                return ['success' => false, 'error' => 'Food listing not found.'];
            }

            if ($listing['status'] !== STATUS_AVAILABLE) {
                $this->db->rollBack();
                return ['success' => false, 'error' => 'This food offer is no longer available.'];
            }

            if (strtotime($listing['available_until']) <= time()) {
                $this->db->rollBack();
                return ['success' => false, 'error' => 'This food listing has already expired.'];
            }

            if ((int)$listing['donor_id'] === $recipientId) {
                $this->db->rollBack();
                return ['success' => false, 'error' => 'You cannot request your own food listing.'];
            }

            // Check duplicate request
            if ($this->hasActiveRequest($listingId, $recipientId)) {
                $this->db->rollBack();
                return ['success' => false, 'error' => 'You already have an active request for this food listing.'];
            }

            $insStmt = $this->db->prepare("
                INSERT INTO food_requests (listing_id, recipient_id, message, status, created_at)
                VALUES (?, ?, ?, 'pending', NOW())
            ");
            $insStmt->execute([
                $listingId,
                $recipientId,
                $message ? trim($message) : null
            ]);
            $reqId = (int)$this->db->lastInsertId();

            $this->db->commit();
            return ['success' => true, 'id' => $reqId];
        } catch (Exception $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            error_log("Error creating request: " . $e->getMessage());
            return ['success' => false, 'error' => 'An unexpected error occurred while submitting your request.'];
        }
    }

    public function findById(int $id): ?array {
        $stmt = $this->db->prepare("
            SELECT r.*,
                   l.title AS listing_title, l.quantity, l.unit, l.pickup_location,
                   l.pickup_instructions, l.available_until, l.status AS listing_status,
                   l.donor_id,
                   donor.name AS donor_name, donor.email AS donor_email, donor.organization AS donor_org,
                   recip.name AS recipient_name, recip.email AS recipient_email, recip.phone AS recipient_phone
            FROM food_requests r
            JOIN food_listings l ON r.listing_id = l.id
            JOIN users donor ON l.donor_id = donor.id
            JOIN users recip ON r.recipient_id = recip.id
            WHERE r.id = ?
            LIMIT 1
        ");
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function findByRecipient(int $recipientId): array {
        $stmt = $this->db->prepare("
            SELECT r.*,
                   l.title AS listing_title, l.quantity, l.unit, l.pickup_location,
                   l.pickup_instructions, l.available_until, l.status AS listing_status,
                   l.organization_name,
                   donor.name AS donor_name, donor.email AS donor_email, donor.phone AS donor_phone
            FROM food_requests r
            JOIN food_listings l ON r.listing_id = l.id
            JOIN users donor ON l.donor_id = donor.id
            WHERE r.recipient_id = ?
            ORDER BY r.created_at DESC
        ");
        $stmt->execute([$recipientId]);
        return $stmt->fetchAll();
    }

    public function findByDonor(int $donorId): array {
        $stmt = $this->db->prepare("
            SELECT r.*,
                   l.title AS listing_title, l.quantity, l.unit, l.status AS listing_status,
                   recip.name AS recipient_name, recip.email AS recipient_email, recip.phone AS recipient_phone,
                   recip.organization AS recipient_org
            FROM food_requests r
            JOIN food_listings l ON r.listing_id = l.id
            JOIN users recip ON r.recipient_id = recip.id
            WHERE l.donor_id = ?
            ORDER BY r.created_at DESC
        ");
        $stmt->execute([$donorId]);
        return $stmt->fetchAll();
    }

    public function countPendingForDonor(int $donorId): int {
        $stmt = $this->db->prepare("
            SELECT COUNT(*)
            FROM food_requests r
            JOIN food_listings l ON r.listing_id = l.id
            WHERE l.donor_id = ? AND r.status = 'pending'
        ");
        $stmt->execute([$donorId]);
        return (int)$stmt->fetchColumn();
    }

    public function cancel(int $id, int $recipientId): array {
        $stmt = $this->db->prepare("SELECT * FROM food_requests WHERE id = ? AND recipient_id = ?");
        $stmt->execute([$id, $recipientId]);
        $req = $stmt->fetch();

        if (!$req) {
            return ['success' => false, 'error' => 'Request not found or access denied.'];
        }

        if ($req['status'] !== REQ_PENDING) {
            return ['success' => false, 'error' => 'Only pending requests can be cancelled.'];
        }

        $upd = $this->db->prepare("UPDATE food_requests SET status = 'cancelled', updated_at = NOW() WHERE id = ?");
        $upd->execute([$id]);

        return ['success' => true];
    }

    /**
     * Atomically approve request, reserve listing, and reject remaining pending requests
     */
    public function approve(int $requestId, int $donorId): array {
        $this->db->beginTransaction();
        try {
            // Lock request and listing
            $stmt = $this->db->prepare("
                SELECT r.*, l.donor_id, l.status AS listing_status
                FROM food_requests r
                JOIN food_listings l ON r.listing_id = l.id
                WHERE r.id = ?
                FOR UPDATE
            ");
            $stmt->execute([$requestId]);
            $req = $stmt->fetch();

            if (!$req) {
                $this->db->rollBack();
                return ['success' => false, 'error' => 'Request record not found.'];
            }

            if ((int)$req['donor_id'] !== $donorId) {
                $this->db->rollBack();
                return ['success' => false, 'error' => 'Unauthorized: Only the food owner can approve requests.'];
            }

            if ($req['status'] !== REQ_PENDING) {
                $this->db->rollBack();
                return ['success' => false, 'error' => 'This request is no longer pending.'];
            }

            // Verify no other request for this listing was already approved
            $checkApproved = $this->db->prepare("
                SELECT COUNT(*) FROM food_requests
                WHERE listing_id = ? AND status = 'approved' AND id != ?
            ");
            $checkApproved->execute([$req['listing_id'], $requestId]);
            if ((int)$checkApproved->fetchColumn() > 0) {
                $this->db->rollBack();
                return ['success' => false, 'error' => 'This listing already has an approved request.'];
            }

            // 1. Mark this request Approved
            $apprStmt = $this->db->prepare("
                UPDATE food_requests
                SET status = 'approved', decided_at = NOW(), updated_at = NOW()
                WHERE id = ?
            ");
            $apprStmt->execute([$requestId]);

            // 2. Mark the listing Reserved
            $resStmt = $this->db->prepare("
                UPDATE food_listings
                SET status = 'reserved', updated_at = NOW()
                WHERE id = ?
            ");
            $resStmt->execute([$req['listing_id']]);

            // 3. Reject any competing pending requests for this listing
            $rejOthers = $this->db->prepare("
                UPDATE food_requests
                SET status = 'rejected', decided_at = NOW(), updated_at = NOW()
                WHERE listing_id = ? AND id != ? AND status = 'pending'
            ");
            $rejOthers->execute([$req['listing_id'], $requestId]);

            $this->db->commit();
            return ['success' => true];
        } catch (Exception $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            error_log("Approval error: " . $e->getMessage());
            return ['success' => false, 'error' => 'Failed to approve request: ' . $e->getMessage()];
        }
    }

    public function reject(int $requestId, int $donorId): array {
        $stmt = $this->db->prepare("
            SELECT r.*, l.donor_id
            FROM food_requests r
            JOIN food_listings l ON r.listing_id = l.id
            WHERE r.id = ?
        ");
        $stmt->execute([$requestId]);
        $req = $stmt->fetch();

        if (!$req) {
            return ['success' => false, 'error' => 'Request not found.'];
        }

        if ((int)$req['donor_id'] !== $donorId) {
            return ['success' => false, 'error' => 'Unauthorized: Only the food owner can reject requests.'];
        }

        if ($req['status'] !== REQ_PENDING) {
            return ['success' => false, 'error' => 'Only pending requests can be rejected.'];
        }

        $upd = $this->db->prepare("
            UPDATE food_requests
            SET status = 'rejected', decided_at = NOW(), updated_at = NOW()
            WHERE id = ?
        ");
        $upd->execute([$requestId]);

        return ['success' => true];
    }

    public function countAll(): int {
        $stmt = $this->db->query("SELECT COUNT(*) FROM food_requests");
        return (int)$stmt->fetchColumn();
    }

    public function countByStatus(string $status): int {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM food_requests WHERE status = ?");
        $stmt->execute([$status]);
        return (int)$stmt->fetchColumn();
    }

    public function getAllForAdmin(int $limit = 100, int $offset = 0): array {
        $stmt = $this->db->prepare("
            SELECT r.*,
                   l.title AS listing_title, l.quantity, l.unit,
                   donor.name AS donor_name, donor.email AS donor_email,
                   recip.name AS recipient_name, recip.email AS recipient_email
            FROM food_requests r
            JOIN food_listings l ON r.listing_id = l.id
            JOIN users donor ON l.donor_id = donor.id
            JOIN users recip ON r.recipient_id = recip.id
            ORDER BY r.created_at DESC
            LIMIT ? OFFSET ?
        ");
        $stmt->bindValue(1, $limit, PDO::PARAM_INT);
        $stmt->bindValue(2, $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
