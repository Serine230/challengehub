<?php

require_once BASE_PATH . '/app/models/Challenge.php';
require_once BASE_PATH . '/app/models/Submission.php';

class ChallengeController {
    private Challenge $challengeModel;
    private Submission $submissionModel;

    public function __construct() {
        $this->challengeModel  = new Challenge();
        $this->submissionModel = new Submission();
    }

    public function index(): void {
        $search   = trim($_GET['search'] ?? '');
        $category = trim($_GET['category'] ?? '');
        $sort     = in_array($_GET['sort'] ?? '', ['date', 'popular']) ? $_GET['sort'] : 'date';

        $challenges  = $this->challengeModel->getAll($search, $category, $sort);
        $categories  = $this->challengeModel->getCategories();
        $flash       = getFlash();
        $currentUser = currentUser();

        require BASE_PATH . '/app/views/challenges/index.php';
    }

    public function show(int $id): void {
        $challenge = $this->challengeModel->getById($id);
        if (!$challenge) {
            setFlash('error', 'Challenge not found.');
            redirect('?page=challenges');
        }

        $currentUser    = currentUser();
        $userId         = $currentUser ? $currentUser['id'] : null;
        $submissions    = $this->submissionModel->getByChallenge($id, $userId);
        $hasParticipated = $userId ? $this->submissionModel->hasParticipated($id, $userId) : false;
        $isFull         = $this->challengeModel->isFull($id);
        $flash          = getFlash();

        require BASE_PATH . '/app/views/challenges/show.php';
    }

    public function create(): void {
        requireAuth();
        $flash = getFlash();
        require BASE_PATH . '/app/views/challenges/create.php';
    }

    public function store(): void {
        requireAuth();
        if (!validateCsrfToken($_POST['csrf_token'] ?? '')) {
            setFlash('error', 'Invalid security token.');
            redirect('?page=challenge-create');
        }

        $user    = currentUser();
        $title   = trim($_POST['title'] ?? '');
        $desc    = trim($_POST['description'] ?? '');
        $cat     = trim($_POST['category'] ?? '');
        $dl      = trim($_POST['deadline'] ?? '');
        $errors  = [];

        if (strlen($title) < 5 || strlen($title) > 200) $errors[] = 'Title must be between 5 and 200 characters.';
        if (strlen($desc) < 20)                          $errors[] = 'Description must be at least 20 characters.';
        if (empty($cat))                                  $errors[] = 'Category is required.';
        if (empty($dl) || strtotime($dl) < time())       $errors[] = 'Deadline must be a future date.';

        $image = null;
        if (!empty($_FILES['image']['name'])) {
            $image = uploadImage($_FILES['image'], 'challenge');
            if (!$image) $errors[] = 'Invalid image format or size (max 5MB).';
        }

        if (!$errors) {
            $id = $this->challengeModel->create($user['id'], $title, $desc, $cat, $dl, $image);
            setFlash('success', 'Challenge created successfully!');
            redirect('?page=challenge&id=' . $id);
        }

        $flash = ['type' => 'error', 'message' => implode(' ', $errors)];
        require BASE_PATH . '/app/views/challenges/create.php';
    }

    public function edit(int $id): void {
        requireAuth();
        $challenge = $this->challengeModel->getById($id);
        $user      = currentUser();

        if (!$challenge || !$this->challengeModel->isOwner($id, $user['id'])) {
            setFlash('error', 'Access denied.');
            redirect('?page=challenges');
        }

        $flash = getFlash();
        require BASE_PATH . '/app/views/challenges/edit.php';
    }

    public function update(int $id): void {
        requireAuth();
        if (!validateCsrfToken($_POST['csrf_token'] ?? '')) {
            setFlash('error', 'Invalid security token.');
            redirect('?page=challenge-edit&id=' . $id);
        }

        $user = currentUser();
        if (!$this->challengeModel->isOwner($id, $user['id'])) {
            setFlash('error', 'Access denied.');
            redirect('?page=challenges');
        }

        $title  = trim($_POST['title'] ?? '');
        $desc   = trim($_POST['description'] ?? '');
        $cat    = trim($_POST['category'] ?? '');
        $dl     = trim($_POST['deadline'] ?? '');
        $errors = [];

        if (strlen($title) < 5 || strlen($title) > 200) $errors[] = 'Title must be between 5 and 200 characters.';
        if (strlen($desc) < 20)                          $errors[] = 'Description must be at least 20 characters.';
        if (empty($cat))                                  $errors[] = 'Category is required.';
        if (empty($dl))                                   $errors[] = 'Deadline is required.';

        $image = null;
        if (!empty($_FILES['image']['name'])) {
            $image = uploadImage($_FILES['image'], 'challenge');
            if (!$image) $errors[] = 'Invalid image format or size.';
        }

        if (!$errors) {
            $this->challengeModel->update($id, $title, $desc, $cat, $dl, $image);
            setFlash('success', 'Challenge updated successfully!');
            redirect('?page=challenge&id=' . $id);
        }

        $challenge = $this->challengeModel->getById($id);
        $flash = ['type' => 'error', 'message' => implode(' ', $errors)];
        require BASE_PATH . '/app/views/challenges/edit.php';
    }

    public function delete(int $id): void {
        requireAuth();
        $user = currentUser();

        if ($this->challengeModel->isOwner($id, $user['id'])) {
            $this->challengeModel->delete($id);
            setFlash('success', 'Challenge deleted.');
        } else {
            setFlash('error', 'Access denied.');
        }
        redirect('?page=challenges');
    }

    public function home(): void {
        $featured    = $this->challengeModel->getFeatured(3);
        $leaderboard = $this->submissionModel->getLeaderboard(5);
        $categories  = $this->challengeModel->getCategories();
        $flash       = getFlash();
        $currentUser = currentUser();

        require BASE_PATH . '/app/views/home.php';
    }

    public function leaderboard(): void {
        $leaderboard = $this->submissionModel->getLeaderboard(20);
        $currentUser = currentUser();
        require BASE_PATH . '/app/views/leaderboard.php';
    }
}
