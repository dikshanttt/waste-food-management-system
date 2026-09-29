<?php
// The home page shows a welcome message and links to the account pages.
require_once __DIR__ . '/database/db.php';

$memberCount = 0;

try {
    $database = connect_to_database();
    $memberCount = (int)$database->query('SELECT COUNT(*) FROM users')->fetchColumn();
} catch (PDOException $error) {
    // The home page can still open before the database has been set up.
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>FoodShare | Share extra food</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <header class="top-bar">
        <a class="logo" href="index.php">FoodShare</a>
        <nav>
            <a href="login.php">Log in</a>
            <a class="button small" href="register.php">Create an account</a>
        </nav>
    </header>

    <main>
        <section class="hero">
            <p class="eyebrow">GOOD FOOD SHOULD NOT GO TO WASTE</p>
            <h1>Share extra food with your community.</h1>
            <p class="intro">FoodShare helps people offer extra food and helps local organizations find it.</p>
            <a class="button" href="register.php">Join FoodShare</a>
            <a class="text-link" href="login.php">Already have an account? Log in</a>
        </section>

        <section class="content">
            <h2>A simple way to share extra food</h2>
            <p class="note">
                <?php if ($memberCount > 0): ?>
                    <?= $memberCount ?> people have joined FoodShare.
                <?php else: ?>
                    Create an account to be one of the first people to join FoodShare.
                <?php endif; ?>
            </p>
        </section>

        <section class="content steps">
            <h2>How it works</h2>
            <div class="step-list">
                <article class="step">
                    <span>1</span>
                    <h3>Create an account</h3>
                    <p>Register as someone sharing food or an organization receiving it.</p>
                </article>
                <article class="step">
                    <span>2</span>
                    <h3>Log in</h3>
                    <p>Use your email and password to check your account details.</p>
                </article>
                <article class="step">
                    <span>3</span>
                    <h3>Start here</h3>
                    <p>This student project demonstrates the home, login, and registration pages.</p>
                </article>
            </div>
        </section>
    </main>

    <footer class="footer">
        <p>FoodShare — helping good food reach more people.</p>
    </footer>

    <script src="assets/js/main.js"></script>
</body>
</html>
