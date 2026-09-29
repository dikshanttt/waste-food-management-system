<?php
// app/Controllers/AuthController.php

declare(strict_types=1);

require_once __DIR__ . '/../Models/User.php';
require_once __DIR__ . '/../../config/config.php';

class AuthController {
    private User $userModel;

    public function __construct() {
        $this->userModel = new User();
    }

    public function showLogin(): void {
        if (is_logged_in()) {
            $this->redirectByRole($_SESSION['user_role']);
            return;
        }
        $oldEmail = $_SESSION['old_email'] ?? '';
        unset($_SESSION['old_email']);
        require __DIR__ . '/../Views/auth/login.php';
    }

    public function login(): void {
        if (!verify_csrf_token($_POST['csrf_token'] ?? null)) {
            set_flash('error', 'Session token expired. Please try again.');
            header('Location: ' . BASE_URL . '/login');
            exit;
        }

        $email = trim($_POST['email'] ?? '');
        $password = (string)($_POST['password'] ?? '');

        if (empty($email) || empty($password)) {
            set_flash('error', 'Please provide both email and password.');
            $_SESSION['old_email'] = $email;
            header('Location: ' . BASE_URL . '/login');
            exit;
        }

        $user = $this->userModel->findByEmail($email);

        if (!$user || !password_verify($password, $user['password_hash'])) {
            set_flash('error', 'Invalid email address or password.');
            $_SESSION['old_email'] = $email;
            header('Location: ' . BASE_URL . '/login');
            exit;
        }

        if ((int)$user['is_active'] !== 1) {
            set_flash('error', 'Your account has been deactivated. Please contact support.');
            header('Location: ' . BASE_URL . '/login');
            exit;
        }

        // Regenerate session ID upon successful login
        session_regenerate_id(true);

        $_SESSION['user_id'] = (int)$user['id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_role'] = $user['role'];
        $_SESSION['user_org'] = $user['organization'] ?? '';

        set_flash('success', "Welcome back, {$user['name']}!");
        $this->redirectByRole($user['role']);
    }

    public function logout(): void {
        $_SESSION = [];
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        session_destroy();
        session_start();
        set_flash('success', 'You have been signed out successfully.');
        header('Location: ' . BASE_URL . '/login');
        exit;
    }

    public function showRegister(): void {
        if (is_logged_in()) {
            $this->redirectByRole($_SESSION['user_role']);
            return;
        }
        $oldName = $_SESSION['old_name'] ?? '';
        $oldEmail = $_SESSION['old_email'] ?? '';
        $oldRole = $_SESSION['old_role'] ?? ROLE_RECIPIENT;
        $oldOrg = $_SESSION['old_org'] ?? '';
        $oldPhone = $_SESSION['old_phone'] ?? '';
        unset($_SESSION['old_name'], $_SESSION['old_email'], $_SESSION['old_role'], $_SESSION['old_org'], $_SESSION['old_phone']);
        require __DIR__ . '/../Views/auth/register.php';
    }

    public function register(): void {
        if (!verify_csrf_token($_POST['csrf_token'] ?? null)) {
            set_flash('error', 'Session token expired. Please try again.');
            header('Location: ' . BASE_URL . '/register');
            exit;
        }

        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = (string)($_POST['password'] ?? '');
        $role = trim($_POST['role'] ?? ROLE_RECIPIENT);
        $org = trim($_POST['organization'] ?? '');
        $phone = trim($_POST['phone'] ?? '');

        // Prevent public admin creation (FR-06)
        if ($role === ROLE_ADMIN) {
            $role = ROLE_RECIPIENT;
        }

        if (!in_array($role, [ROLE_DONOR, ROLE_RECIPIENT], true)) {
            $role = ROLE_RECIPIENT;
        }

        if (empty($name) || empty($email) || empty($password)) {
            set_flash('error', 'Please fill in all required fields.');
            $this->preserveRegisterInput($name, $email, $role, $org, $phone);
            header('Location: ' . BASE_URL . '/register');
            exit;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            set_flash('error', 'Please enter a valid email address.');
            $this->preserveRegisterInput($name, $email, $role, $org, $phone);
            header('Location: ' . BASE_URL . '/register');
            exit;
        }

        if (strlen($password) < 6) {
            set_flash('error', 'Password must be at least 6 characters long.');
            $this->preserveRegisterInput($name, $email, $role, $org, $phone);
            header('Location: ' . BASE_URL . '/register');
            exit;
        }

        if ($this->userModel->findByEmail($email)) {
            set_flash('error', 'An account with that email address already exists. Please sign in instead.');
            $this->preserveRegisterInput($name, $email, $role, $org, $phone);
            header('Location: ' . BASE_URL . '/register');
            exit;
        }

        $newId = $this->userModel->create($name, $email, $password, $role, $org ?: null, $phone ?: null);

        // Auto sign-in
        session_regenerate_id(true);
        $_SESSION['user_id'] = $newId;
        $_SESSION['user_name'] = $name;
        $_SESSION['user_email'] = $email;
        $_SESSION['user_role'] = $role;
        $_SESSION['user_org'] = $org;

        set_flash('success', "Your account has been created successfully! Welcome to FoodRescue.");
        $this->redirectByRole($role);
    }

    public function showForgotPassword(): void {
        require __DIR__ . '/../Views/auth/forgot-password.php';
    }

    public function handleForgotPassword(): void {
        if (!verify_csrf_token($_POST['csrf_token'] ?? null)) {
            set_flash('error', 'Session token expired.');
            header('Location: ' . BASE_URL . '/forgot-password');
            exit;
        }

        $email = trim($_POST['email'] ?? '');
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            set_flash('error', 'Please provide a valid email address.');
            header('Location: ' . BASE_URL . '/forgot-password');
            exit;
        }

        // For v1 MVP, show helpful confirmation without revealing if email exists
        set_flash('info', "If an account exists for {$email}, password recovery instructions have been prepared. For demo accounts, the default password is 'password123'.");
        header('Location: ' . BASE_URL . '/login');
        exit;
    }

    public function showProfile(): void {
        require_auth();
        $userRecord = $this->userModel->findById((int)$_SESSION['user_id']);
        if (!$userRecord) {
            $this->logout();
            return;
        }
        require __DIR__ . '/../Views/auth/profile.php';
    }

    public function updateProfile(): void {
        require_auth();
        if (!verify_csrf_token($_POST['csrf_token'] ?? null)) {
            set_flash('error', 'Session token expired.');
            header('Location: ' . BASE_URL . '/profile');
            exit;
        }

        $userId = (int)$_SESSION['user_id'];
        $action = $_POST['action'] ?? 'update_profile';

        if ($action === 'update_profile') {
            $name = trim($_POST['name'] ?? '');
            $org = trim($_POST['organization'] ?? '');
            $phone = trim($_POST['phone'] ?? '');

            if (empty($name)) {
                set_flash('error', 'Name cannot be empty.');
                header('Location: ' . BASE_URL . '/profile');
                exit;
            }

            $this->userModel->updateProfile($userId, $name, $org ?: null, $phone ?: null);
            $_SESSION['user_name'] = $name;
            $_SESSION['user_org'] = $org;

            set_flash('success', 'Profile updated successfully.');
            header('Location: ' . BASE_URL . '/profile');
            exit;
        } elseif ($action === 'update_password') {
            $newPass = (string)($_POST['new_password'] ?? '');
            $confirmPass = (string)($_POST['confirm_password'] ?? '');

            if (strlen($newPass) < 6) {
                set_flash('error', 'New password must be at least 6 characters.');
                header('Location: ' . BASE_URL . '/profile');
                exit;
            }

            if ($newPass !== $confirmPass) {
                set_flash('error', 'Passwords do not match.');
                header('Location: ' . BASE_URL . '/profile');
                exit;
            }

            $this->userModel->updatePassword($userId, $newPass);
            set_flash('success', 'Password updated successfully.');
            header('Location: ' . BASE_URL . '/profile');
            exit;
        }
    }

    private function redirectByRole(string $role): void {
        if ($role === ROLE_ADMIN) {
            header('Location: ' . BASE_URL . '/admin/dashboard');
        } elseif ($role === ROLE_DONOR) {
            header('Location: ' . BASE_URL . '/donor/dashboard');
        } else {
            header('Location: ' . BASE_URL . '/listings');
        }
        exit;
    }

    private function preserveRegisterInput(string $name, string $email, string $role, string $org, string $phone): void {
        $_SESSION['old_name'] = $name;
        $_SESSION['old_email'] = $email;
        $_SESSION['old_role'] = $role;
        $_SESSION['old_org'] = $org;
        $_SESSION['old_phone'] = $phone;
    }
}
