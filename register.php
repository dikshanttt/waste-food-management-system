<?php
// Save a new account in the database.
require_once __DIR__ . '/database/db.php';

$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $role = $_POST['role'] ?? '';

    if ($name === '' || $email === '' || $password === '') {
        $message = 'Please fill in every box.';
        $messageType = 'error';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = 'Please enter a valid email address.';
        $messageType = 'error';
    } elseif (strlen($password) < 8) {
        $message = 'Choose a password with at least 8 characters.';
        $messageType = 'error';
    } elseif ($role !== 'donor' && $role !== 'recipient') {
        $message = 'Please choose whether you want to share or receive food.';
        $messageType = 'error';
    } else {
        try {
            $database = connect_to_database();

            $checkEmail = $database->prepare('SELECT id FROM users WHERE email = ?');
            $checkEmail->execute([$email]);

            if ($checkEmail->fetch()) {
                $message = 'An account already uses that email address.';
                $messageType = 'error';
            } else {
                $passwordHash = password_hash($password, PASSWORD_DEFAULT);
                $saveUser = $database->prepare(
                    'INSERT INTO users (name, email, password_hash, role) VALUES (?, ?, ?, ?)'
                );
                $saveUser->execute([$name, $email, $passwordHash, $role]);

                $message = 'Your account was created. You can now log in.';
                $messageType = 'success';
            }
        } catch (PDOException $error) {
            $message = 'The database is not ready yet. Check the setup steps in README.md.';
            $messageType = 'error';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Create an account | FoodShare</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <header class="top-bar">
        <a class="logo" href="index.php">FoodShare</a>
        <nav>
            <a href="index.php">Home</a>
            <a href="login.php">Log in</a>
        </nav>
    </header>

    <main class="form-page">
        <section class="form-card">
            <p class="eyebrow">JOIN THE COMMUNITY</p>
            <h1>Create an account</h1>
            <p class="muted">Tell us a little about yourself to get started.</p>

            <?php if ($message !== ''): ?>
                <p class="message <?= htmlspecialchars($messageType) ?>" role="status">
                    <?= htmlspecialchars($message) ?>
                </p>
            <?php endif; ?>

            <form method="post" action="register.php">
                <label for="name">Your name</label>
                <input id="name" name="name" type="text" required maxlength="100" autocomplete="name">

                <label for="email">Email address</label>
                <input id="email" name="email" type="email" required maxlength="150" autocomplete="email">

                <label for="role">I want to</label>
                <select id="role" name="role" required>
                    <option value="">Choose one</option>
                    <option value="donor">Share extra food</option>
                    <option value="recipient">Find food for my organization</option>
                </select>

                <label for="password">Create a password</label>
                <div class="password-row">
                    <input id="password" name="password" type="password" required minlength="8" autocomplete="new-password">
                    <button class="show-password" type="button" data-password-toggle="password">Show</button>
                </div>

                <button class="button full-width" type="submit">Create account</button>
            </form>

            <p class="form-bottom">Already registered? <a href="login.php">Log in</a></p>
        </section>
    </main>

    <script src="assets/js/main.js"></script>
</body>
</html>