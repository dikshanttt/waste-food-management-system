<?php
// Check the sign-in details, then show a message on this page.
require_once __DIR__ . '/database/db.php';

$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($email === '' || $password === '') {
        $message = 'Please enter your email and password.';
        $messageType = 'error';
    } else {
        try {
            $database = connect_to_database();
            $query = $database->prepare(
                'SELECT name, password_hash, is_active FROM users WHERE email = ?'
            );
            $query->execute([$email]);
            $user = $query->fetch();

            if ($user && password_verify($password, $user['password_hash']) && $user['is_active']) {
                $message = 'Your sign-in details are correct. This version ends at the login screen.';
                $messageType = 'success';
            } else {
                $message = 'The email or password is incorrect.';
                $messageType = 'error';
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
    <title>Log in | FoodShare</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <header class="top-bar">
        <a class="logo" href="index.php">FoodShare</a>
        <nav>
            <a href="index.php">Home</a>
            <a class="button small" href="register.php">Create an account</a>
        </nav>
    </header>

    <main class="form-page">
        <section class="form-card">
            <p class="eyebrow">WELCOME BACK</p>
            <h1>Log in</h1>
            <p class="muted">Enter the email and password you used to register.</p>

            <?php if ($message !== ''): ?>
                <p class="message <?= htmlspecialchars($messageType) ?>" role="status">
                    <?= htmlspecialchars($message) ?>
                </p>
            <?php endif; ?>

            <form method="post" action="login.php">
                <label for="email">Email address</label>
                <input id="email" name="email" type="email" required autocomplete="email">

                <label for="password">Password</label>
                <div class="password-row">
                    <input id="password" name="password" type="password" required autocomplete="current-password">
                    <button class="show-password" type="button" data-password-toggle="password">Show</button>
                </div>

                <button class="button full-width" type="submit">Log in</button>
            </form>

            <p class="form-bottom">New to FoodShare? <a href="register.php">Create an account</a></p>
        </section>
    </main>

    <script src="assets/js/main.js"></script>
</body>
</html>