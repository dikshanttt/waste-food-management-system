<?php
// app/Models/FoodListing.php

declare(strict_types=1);

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/config.php';

class FoodListing {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getConnection();
        $this->autoExpirePastListings();
    }

    public function autoExpirePastListings(): void {
        // Automatically transition available listings whose availability end time has passed
        $stmt = $this->db->prepare("
            UPDATE food_listings
            SET status = 'expired', updated_at = NOW()
            WHERE status = 'available' AND available_until < NOW()
        ");
        $stmt->execute();
    }

    public function create(
        int $donorId,
        string $title,
        string $description,
        int $quantity,
        string $unit,
        string $availableUntil,
        string $pickupLocation,
        ?string $pickupInstructions = null,
        ?string $organizationName = null
    ): int {
        $stmt = $this->db->prepare("
            INSERT INTO food_listings (
                donor_id, title, description, quantity, unit,
                available_until, pickup_location, pickup_instructions,
                organization_name, status, created_at
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'available', NOW())
        ");
        $stmt->execute([
            $donorId,
            trim($title),
            trim($description),
            $quantity,
            trim($unit),
            $availableUntil,
            trim($pickupLocation),
            $pickupInstructions ? trim($pickupInstructions) : null,
            $organizationName ? trim($organizationName) : null
        ]);
        return (int)$this->db->lastInsertId();
    }

    public function update(
        int $id,
        int $donorId,
        string $title,
        string $description,
        int $quantity,
        string $unit,
        string $availableUntil,
        string $pickupLocation,
        ?string $pickupInstructions = null,
        ?string $organizationName = null
    ): bool {
        // Only allow donor owner to update while available
        $stmt = $this->db->prepare("
            UPDATE food_listings
            SET title = ?, description = ?, quantity = ?, unit = ?,
                available_until = ?, pickup_location = ?, pickup_instructions = ?,
                organization_name = ?, updated_at = NOW()
            WHERE id = ? AND donor_id = ? AND status = 'available'
        ");
        return $stmt->execute([
            trim($title),
            trim($description),
            $quantity,
            trim($unit),
            $availableUntil,
            trim($pickupLocation),
            $pickupInstructions ? trim($pickupInstructions) : null,
            $organizationName ? trim($organizationName) : null,
            $id,
            $donorId
        ]);
    }

    public function findById(int $id): ?array {
        $stmt = $this->db->prepare("
            SELECT l.*, u.name AS donor_name, u.email AS donor_email, u.phone AS donor_phone,
                   u.organization AS donor_org,
                   (SELECT COUNT(*) FROM food_requests r WHERE r.listing_id = l.id) AS total_requests,
                   (SELECT COUNT(*) FROM food_requests r WHERE r.listing_id = l.id AND r.status = 'pending') AS pending_requests
            FROM food_listings l
            JOIN users u ON l.donor_id = u.id
            WHERE l.id = ?
            LIMIT 1
        ");
        $stmt->execute([$id]);
        $listing = $stmt->fetch();
        return $listing ?: null;
    }

    public function findByDonor(int $donorId): array {
        $stmt = $this->db->prepare("
            SELECT l.*,
                   (SELECT COUNT(*) FROM food_requests r WHERE r.listing_id = l.id AND r.status = 'pending') AS pending_requests_count
            FROM food_listings l
            WHERE l.donor_id = ?
            ORDER BY l.created_at DESC
        ");
        $stmt->execute([$donorId]);
        return $stmt->fetchAll();
    }

    public function countByDonor(int $donorId, ?string $status = null): int {
        if ($status !== null) {
            $stmt = $this->db->prepare("SELECT COUNT(*) FROM food_listings WHERE donor_id = ? AND status = ?");
            $stmt->execute([$donorId, $status]);
        } else {
            $stmt = $this->db->prepare("SELECT COUNT(*) FROM food_listings WHERE donor_id = ?");
            $stmt->execute([$donorId]);
        }
        return (int)$stmt->fetchColumn();
    }

    public function getAvailable(string $search = '', string $unit = '', int $limit = 50, int $offset = 0): array {
        $sql = "
            SELECT l.*, u.name AS donor_name, u.organization AS donor_org
            FROM food_listings l
            JOIN users u ON l.donor_id = u.id
            WHERE l.status = 'available'
              AND l.available_until > NOW()
              AND u.is_active = 1
        ";
        $params = [];

        if (!empty($search)) {
            $sql .= " AND (l.title LIKE ? OR l.description LIKE ? OR l.pickup_location LIKE ? OR l.organization_name LIKE ?)";
            $term = '%' . trim($search) . '%';
            $params[] = $term;
            $params[] = $term;
            $params[] = $term;
            $params[] = $term;
        }

        if (!empty($unit)) {
            $sql .= " AND l.unit = ?";
            $params[] = trim($unit);
        }

        $sql .= " ORDER BY l.available_until ASC LIMIT ? OFFSET ?";
        
        $stmt = $this->db->prepare($sql);
        $paramIdx = 1;
        foreach ($params as $val) {
            $stmt->bindValue($paramIdx++, $val, PDO::PARAM_STR);
        }
        $stmt->bindValue($paramIdx++, $limit, PDO::PARAM_INT);
        $stmt->bindValue($paramIdx++, $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function setStatus(int $id, string $status): bool {
        $stmt = $this->db->prepare("UPDATE food_listings SET status = ?, updated_at = NOW() WHERE id = ?");
        return $stmt->execute([$status, $id]);
    }

    public function deleteOrMarkUnavailable(int $id, int $donorId): bool {
        // Check if there are request records
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM food_requests WHERE listing_id = ?");
        $stmt->execute([$id]);
        $hasRequests = ((int)$stmt->fetchColumn()) > 0;

        if ($hasRequests) {
            // Soft-retire to preserve audit history as per FR-11
            $upd = $this->db->prepare("UPDATE food_listings SET status = 'unavailable', updated_at = NOW() WHERE id = ? AND donor_id = ?");
            return $upd->execute([$id, $donorId]);
        } else {
            // Safe to delete if no requests exist
            $del = $this->db->prepare("DELETE FROM food_listings WHERE id = ? AND donor_id = ?");
            return $del->execute([$id, $donorId]);
        }
    }

    public function adminRemoveOrDeactivate(int $id): bool {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM food_requests WHERE listing_id = ?");
        $stmt->execute([$id]);
        $hasRequests = ((int)$stmt->fetchColumn()) > 0;

        if ($hasRequests) {
            $upd = $this->db->prepare("UPDATE food_listings SET status = 'unavailable', updated_at = NOW() WHERE id = ?");
            return $upd->execute([$id]);
        } else {
            $del = $this->db->prepare("DELETE FROM food_listings WHERE id = ?");
            return $del->execute([$id]);
        }
    }

    public function countAll(): int {
        $stmt = $this->db->query("SELECT COUNT(*) FROM food_listings");
        return (int)$stmt->fetchColumn();
    }

    public function countByStatus(string $status): int {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM food_listings WHERE status = ?");
        $stmt->execute([$status]);
        return (int)$stmt->fetchColumn();
    }

    public function getAllForAdmin(int $limit = 100, int $offset = 0): array {
        $stmt = $this->db->prepare("
            SELECT l.*, u.name AS donor_name, u.email AS donor_email,
                   (SELECT COUNT(*) FROM food_requests r WHERE r.listing_id = l.id) AS total_requests
            FROM food_listings l
            JOIN users u ON l.donor_id = u.id
            ORDER BY l.created_at DESC
            LIMIT ? OFFSET ?
        ");
        $stmt->bindValue(1, $limit, PDO::PARAM_INT);
        $stmt->bindValue(2, $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
