<?php
// database/seed.php - Initial Admin Provisioning Only

declare(strict_types=1);

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/config.php';

echo "Initializing Database and resetting tables...\n";

$db = Database::getConnection();

// Execute schema to reset tables
$schemaSql = file_get_contents(__DIR__ . '/schema.sql');
$db->exec($schemaSql);
echo "Schema applied successfully.\n";

echo "Provisioning initial Administrator account...\n";

$adminPassword = 'password123';
$adminHash = password_hash($adminPassword, PASSWORD_BCRYPT);

$userStmt = $db->prepare("
    INSERT INTO users (name, email, password_hash, role, organization, phone, is_active, created_at)
    VALUES (:name, :email, :password_hash, :role, :organization, :phone, 1, NOW())
");

$userStmt->execute([
    ':name' => 'System Administrator',
    ':email' => 'admin@wastefood.org',
    ':password_hash' => $adminHash,
    ':role' => ROLE_ADMIN,
    ':organization' => 'Waste Food HQ',
    ':phone' => '+977-9801000000'
]);

echo "Administrator account created successfully.\n";
echo "=============================================\n";
echo "Initial Administrator Credentials:\n";
echo "Email:    admin@wastefood.org\n";
echo "Password: {$adminPassword}\n";
echo "=============================================\n";
echo "Database is ready for manual testing and live user/listing additions.\n";
