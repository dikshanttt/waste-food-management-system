<?php
// app/Views/layout/header.php
declare(strict_types=1);
require_once __DIR__ . '/../../../config/config.php';
$pageTitle = $pageTitle ?? APP_NAME;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle) ?> | <?= APP_NAME ?></title>
    <!-- Fonts: Inter & Poppins -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Poppins:wght@500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/style.css">
    <style>
        .skip-link {
            position: absolute;
            top: -40px;
            left: 0;
            background: var(--color-primary);
            color: #fff;
            padding: 8px 16px;
            z-index: 999;
            transition: top 0.2s;
        }
        .skip-link:focus {
            top: 0;
        }
    </style>
</head>
<body>
    <a href="#main-content" class="skip-link">Skip to main content</a>
    <?php require __DIR__ . '/navbar.php'; ?>
    <main id="main-content" class="main-content">
        <div class="container">
            <?php require __DIR__ . '/flash.php'; ?>
