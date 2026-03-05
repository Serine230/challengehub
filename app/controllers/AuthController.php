<?php

require_once BASE_PATH . '/app/models/User.php';

class AuthController {
    private User $userModel;

    public function __construct() {
        $this->userModel = new User();
    }

    public function showLogin(): void {
        $flash = getFlash();
        require BASE_PATH . '/app/views/auth/login.php';
    }

    public function login(): void {
        if (!validateCsrfToken($_POST['csrf_token'] ?? '')) {
            setFlash('error', 'Invalid security token. Please try again.');
            redirect('?page=login');
        }

        $email    = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $errors   = [];

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Please enter a valid email address.';
        }
        if (empty($password)) {
            $errors[] = 'Password is required.';
        }

        if (!$errors) {
            $user = $this->userModel->findByEmail($email);
            if ($user && $this->userModel->verifyPassword($password, $user['password'])) {
                session_regenerate_id(true);
                $_SESSION['user_id']    = $user['id'];
                $_SESSION['user_name']  = $user['name'];
                $_SESSION['user_email'] = $user['email'];
                setFlash('success', 'Welcome back, ' . $user['name'] . '!');
                redirect('?page=home');
            }
            $errors[] = 'Invalid email or password.';
        }

        $flash = ['type' => 'error', 'message' => implode(' ', $errors)];
        require BASE_PATH . '/app/views/auth/login.php';
    }

    public function showRegister(): void {
        $flash = getFlash();
        require BASE_PATH . '/app/views/auth/register.php';
    }

    public function register(): void {
        if (!validateCsrfToken($_POST['csrf_token'] ?? '')) {
            setFlash('error', 'Invalid security token.');
            redirect('?page=register');
        }

        $name     = trim($_POST['name'] ?? '');
        $email    = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirm  = $_POST['password_confirm'] ?? '';
        $errors   = [];

        if (strlen($name) < 2 || strlen($name) > 100) {
            $errors[] = 'Name must be between 2 and 100 characters.';
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Please enter a valid email address.';
        }
        if (strlen($password) < 8) {
            $errors[] = 'Password must be at least 8 characters.';
        }
        if ($password !== $confirm) {
            $errors[] = 'Passwords do not match.';
        }
        if (!$errors && $this->userModel->emailExists($email)) {
            $errors[] = 'This email is already registered.';
        }

        if (!$errors) {
            $id = $this->userModel->create($name, $email, $password);
            session_regenerate_id(true);
            $_SESSION['user_id']    = $id;
            $_SESSION['user_name']  = $name;
            $_SESSION['user_email'] = $email;
            setFlash('success', 'Account created! Welcome to ChallengeHub, ' . $name . '!');
            redirect('?page=home');
        }

        $flash = ['type' => 'error', 'message' => implode(' ', $errors)];
        require BASE_PATH . '/app/views/auth/register.php';
    }

    public function logout(): void {
        session_destroy();
        setcookie(session_name(), '', time() - 3600, '/');
        header('Location: ' . BASE_URL . '/?page=login');
        exit;
    }
}
