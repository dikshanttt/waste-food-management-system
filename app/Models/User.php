<?php
// app/Models/User.php

declare(strict_types=1);

require_once __DIR__ . '/../../config/database.php';

class User {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    public function findByEmail(string $email): ?array {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE email = ? LIMIT 1");
        $stmt->execute([trim(strtolower($email))]);
        $user = $stmt->fetch();
        return $user ?: null;
    }

    public function findById(int $id): ?array {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE id = ? LIMIT 1");
        $stmt->execute([$id]);
        $user = $stmt->fetch();
        return $user ?: null;
    }

    public function create(
        string $name,
        string $email,
        string $password,
        string $role,
        ?string $organization = null,
        ?string $phone = null
    ): int {
        // Enforce role safety: public cannot register admin directly
        if (!in_array($role, [ROLE_DONOR, ROLE_RECIPIENT, ROLE_ADMIN], true)) {
            $role = ROLE_RECIPIENT;
        }

        $hash = password_hash($password, PASSWORD_BCRYPT);
        $stmt = $this->db->prepare("
            INSERT INTO users (name, email, password_hash, role, organization, phone, is_active, created_at)
            VALUES (?, ?, ?, ?, ?, ?, 1, NOW())
        ");
        $stmt->execute([
            trim($name),
            trim(strtolower($email)),
            $hash,
            $role,
            $organization ? trim($organization) : null,
            $phone ? trim($phone) : null
        ]);

        return (int)$this->db->lastInsertId();
    }

    public function updateProfile(int $id, string $name, ?string $organization, ?string $phone): bool {
        $stmt = $this->db->prepare("
            UPDATE users
            SET name = ?, organization = ?, phone = ?, updated_at = NOW()
            WHERE id = ?
        ");
        return $stmt->execute([
            trim($name),
            $organization ? trim($organization) : null,
            $phone ? trim($phone) : null,
            $id
        ]);
    }

    public function updatePassword(int $id, string $newPassword): bool {
        $hash = password_hash($newPassword, PASSWORD_BCRYPT);
        $stmt = $this->db->prepare("
            UPDATE users
            SET password_hash = ?, updated_at = NOW()
            WHERE id = ?
        ");
        return $stmt->execute([$hash, $id]);
    }

    public function toggleActive(int $id): bool {
        $stmt = $this->db->prepare("
            UPDATE users
            SET is_active = IF(is_active = 1, 0, 1), updated_at = NOW()
            WHERE id = ?
        ");
        return $stmt->execute([$id]);
    }

    public function countAll(): int {
        $stmt = $this->db->query("SELECT COUNT(*) FROM users");
        return (int)$stmt->fetchColumn();
    }

    public function countByRole(string $role): int {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM users WHERE role = ?");
        $stmt->execute([$role]);
        return (int)$stmt->fetchColumn();
    }

    public function getAll(int $limit = 100, int $offset = 0): array {
        $stmt = $this->db->prepare("
            SELECT id, name, email, role, organization, phone, is_active, created_at
            FROM users
            ORDER BY created_at DESC
            LIMIT ? OFFSET ?
        ");
        $stmt->bindValue(1, $limit, PDO::PARAM_INT);
        $stmt->bindValue(2, $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
