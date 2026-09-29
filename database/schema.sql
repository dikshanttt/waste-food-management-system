-- Waste Food Management System v1.0 MVP Database Schema
-- Compatible with MySQL 5.7+ / 8.0+ and MariaDB 10.4+

CREATE DATABASE IF NOT EXISTS `waste_food_db` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `waste_food_db`;

-- Drop existing tables in reverse dependency order if resetting
DROP TABLE IF EXISTS `food_requests`;
DROP TABLE IF EXISTS `food_listings`;
DROP TABLE IF EXISTS `users`;

-- Users Table
CREATE TABLE `users` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(100) NOT NULL,
    `email` VARCHAR(150) NOT NULL UNIQUE,
    `password_hash` VARCHAR(255) NOT NULL,
    `role` ENUM('donor', 'recipient', 'admin') NOT NULL DEFAULT 'recipient',
    `organization` VARCHAR(150) NULL,
    `phone` VARCHAR(30) NULL,
    `is_active` TINYINT(1) NOT NULL DEFAULT 1,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_users_role` (`role`),
    INDEX `idx_users_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Food Listings Table
CREATE TABLE `food_listings` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `donor_id` INT NOT NULL,
    `title` VARCHAR(150) NOT NULL,
    `description` TEXT NOT NULL,
    `quantity` INT NOT NULL,
    `unit` VARCHAR(50) NOT NULL,
    `available_until` DATETIME NOT NULL,
    `pickup_location` VARCHAR(255) NOT NULL,
    `pickup_instructions` TEXT NULL,
    `organization_name` VARCHAR(150) NULL,
    `status` ENUM('available', 'reserved', 'unavailable', 'expired') NOT NULL DEFAULT 'available',
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT `chk_quantity_positive` CHECK (`quantity` > 0),
    CONSTRAINT `fk_listings_donor` FOREIGN KEY (`donor_id`) REFERENCES `users` (`id`) ON DELETE RESTRICT,
    INDEX `idx_listings_status_expiry` (`status`, `available_until`),
    INDEX `idx_listings_donor` (`donor_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Food Requests Table
CREATE TABLE `food_requests` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `listing_id` INT NOT NULL,
    `recipient_id` INT NOT NULL,
    `message` TEXT NULL,
    `status` ENUM('pending', 'approved', 'rejected', 'cancelled') NOT NULL DEFAULT 'pending',
    `decided_at` DATETIME NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT `fk_requests_listing` FOREIGN KEY (`listing_id`) REFERENCES `food_listings` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_requests_recipient` FOREIGN KEY (`recipient_id`) REFERENCES `users` (`id`) ON DELETE RESTRICT,
    INDEX `idx_requests_listing_status` (`listing_id`, `status`),
    INDEX `idx_requests_recipient` (`recipient_id`, `created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
