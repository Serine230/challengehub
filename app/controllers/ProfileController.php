<?php

require_once BASE_PATH . '/app/models/User.php';
require_once BASE_PATH . '/app/models/Challenge.php';
require_once BASE_PATH . '/app/models/Submission.php';

class ProfileController {
    private User       $userModel;
    private Challenge  $challengeModel;
    private Submission $submissionModel;

    public function __construct() {
        $this->userModel       = new User();
        $this->challengeModel  = new Challenge();
        $this->submissionModel = new Submission();
    }

    public function show(): void {
        requireAuth();
        $currentUser = currentUser();
        $user        = $this->userModel->findById($currentUser['id']);
        $stats       = $this->userModel->getStats($currentUser['id']);
        $challenges  = $this->challengeModel->getByUser($currentUser['id']);
        $submissions = $this->submissionModel->getByUser($currentUser['id']);
        $flash       = getFlash();

        require BASE_PATH . '/app/views/profile/show.php';
    }

    public function edit(): void {
        requireAuth();
        $currentUser = currentUser();
        $user        = $this->userModel->findById($currentUser['id']);
        $flash       = getFlash();

        require BASE_PATH . '/app/views/profile/edit.php';
    }

    public function update(): void {
        requireAuth();
        if (!validateCsrfToken($_POST['csrf_token'] ?? '')) {
            setFlash('error', 'Invalid security token.');
            redirect('?page=profile-edit');
        }

        $currentUser = currentUser();
        $name        = trim($_POST['name'] ?? '');
        $email       = trim($_POST['email'] ?? '');
        $password    = $_POST['password'] ?? '';
        $confirm     = $_POST['password_confirm'] ?? '';
        $errors      = [];

        if (strlen($name) < 2)                         $errors[] = 'Name is too short.';
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Invalid email address.';
        if ($password && strlen($password) < 8)         $errors[] = 'Password must be at least 8 characters.';
        if ($password && $password !== $confirm)         $errors[] = 'Passwords do not match.';
        if (!$errors && $this->userModel->emailExists($email, $currentUser['id'])) {
            $errors[] = 'Email is already in use.';
        }

        if (!$errors) {
            $this->userModel->update($currentUser['id'], $name, $email, $password ?: null);
            $_SESSION['user_name']  = $name;
            $_SESSION['user_email'] = $email;
            setFlash('success', 'Profile updated successfully!');
            redirect('?page=profile');
        }

        $user  = $this->userModel->findById($currentUser['id']);
        $flash = ['type' => 'error', 'message' => implode(' ', $errors)];
        require BASE_PATH . '/app/views/profile/edit.php';
    }

    public function delete(): void {
        requireAuth();
        if (!validateCsrfToken($_POST['csrf_token'] ?? '')) {
            setFlash('error', 'Invalid security token.');
            redirect('?page=profile');
        }

        $currentUser = currentUser();
        $this->userModel->delete($currentUser['id']);
        session_destroy();
        setcookie(session_name(), '', time() - 3600, '/');
        header('Location: ' . BASE_URL . '/?page=home');
        exit;
    }
}
