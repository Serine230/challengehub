<?php

require_once BASE_PATH . '/app/models/Submission.php';
require_once BASE_PATH . '/app/models/Challenge.php';
require_once BASE_PATH . '/app/models/Comment.php';
require_once BASE_PATH . '/app/models/Vote.php';

class SubmissionController {
    private Submission $submissionModel;
    private Challenge  $challengeModel;
    private Comment    $commentModel;
    private Vote       $voteModel;

    public function __construct() {
        $this->submissionModel = new Submission();
        $this->challengeModel  = new Challenge();
        $this->commentModel    = new Comment();
        $this->voteModel       = new Vote();
    }

    public function show(int $id): void {
        $submission = $this->submissionModel->getById($id);
        if (!$submission) {
            setFlash('error', 'Submission not found.');
            redirect('?page=challenges');
        }

        $currentUser = currentUser();
        $userId      = $currentUser ? $currentUser['id'] : null;
        $comments    = $this->commentModel->getBySubmission($id);
        $hasVoted    = $userId ? $this->voteModel->hasVoted($id, $userId) : false;
        $flash       = getFlash();

        require BASE_PATH . '/app/views/submissions/show.php';
    }

    public function store(int $challengeId): void {
        requireAuth();
        if (!validateCsrfToken($_POST['csrf_token'] ?? '')) {
            setFlash('error', 'Invalid security token.');
            redirect('?page=challenge&id=' . $challengeId);
        }

        $user      = currentUser();
        $challenge = $this->challengeModel->getById($challengeId);

        if (!$challenge) {
            redirect('?page=challenges');
        }

        if ($this->challengeModel->isFull($challengeId)) {
            setFlash('error', 'This challenge has reached the maximum number of participants (5).');
            redirect('?page=challenge&id=' . $challengeId);
        }

        if ($this->submissionModel->hasParticipated($challengeId, $user['id'])) {
            setFlash('error', 'You have already submitted to this challenge.');
            redirect('?page=challenge&id=' . $challengeId);
        }

        if ($challenge['user_id'] == $user['id']) {
            setFlash('error', 'You cannot participate in your own challenge.');
            redirect('?page=challenge&id=' . $challengeId);
        }

        $desc   = trim($_POST['description'] ?? '');
        $errors = [];

        if (strlen($desc) < 20) $errors[] = 'Description must be at least 20 characters.';

        $image = null;
        if (!empty($_FILES['image']['name'])) {
            $image = uploadImage($_FILES['image'], 'submission');
            if (!$image) $errors[] = 'Invalid image format or size.';
        }

        if (!$errors) {
            $id = $this->submissionModel->create($challengeId, $user['id'], $desc, $image);
            setFlash('success', 'Your submission has been posted!');
            redirect('?page=submission&id=' . $id);
        }

        setFlash('error', implode(' ', $errors));
        redirect('?page=challenge&id=' . $challengeId);
    }

    public function edit(int $id): void {
        requireAuth();
        $submission = $this->submissionModel->getById($id);
        $user       = currentUser();

        if (!$submission || !$this->submissionModel->isOwner($id, $user['id'])) {
            setFlash('error', 'Access denied.');
            redirect('?page=challenges');
        }

        $flash = getFlash();
        require BASE_PATH . '/app/views/submissions/edit.php';
    }

    public function update(int $id): void {
        requireAuth();
        if (!validateCsrfToken($_POST['csrf_token'] ?? '')) {
            setFlash('error', 'Invalid security token.');
            redirect('?page=submission-edit&id=' . $id);
        }

        $user = currentUser();
        if (!$this->submissionModel->isOwner($id, $user['id'])) {
            setFlash('error', 'Access denied.');
            redirect('?page=challenges');
        }

        $desc   = trim($_POST['description'] ?? '');
        $errors = [];

        if (strlen($desc) < 20) $errors[] = 'Description must be at least 20 characters.';

        $image = null;
        if (!empty($_FILES['image']['name'])) {
            $image = uploadImage($_FILES['image'], 'submission');
            if (!$image) $errors[] = 'Invalid image format or size.';
        }

        if (!$errors) {
            $this->submissionModel->update($id, $desc, $image);
            setFlash('success', 'Submission updated!');
            redirect('?page=submission&id=' . $id);
        }

        $submission = $this->submissionModel->getById($id);
        $flash = ['type' => 'error', 'message' => implode(' ', $errors)];
        require BASE_PATH . '/app/views/submissions/edit.php';
    }

    public function delete(int $id): void {
        requireAuth();
        $submission = $this->submissionModel->getById($id);
        $user       = currentUser();

        if ($submission && $this->submissionModel->isOwner($id, $user['id'])) {
            $challengeId = $submission['challenge_id'];
            $this->submissionModel->delete($id);
            setFlash('success', 'Submission deleted.');
            redirect('?page=challenge&id=' . $challengeId);
        }

        setFlash('error', 'Access denied.');
        redirect('?page=challenges');
    }

    public function vote(int $submissionId): void {
        requireAuth();
        $user   = currentUser();
        $result = $this->voteModel->toggle($submissionId, $user['id']);

        if (isset($_SERVER['HTTP_X_REQUESTED_WITH'])) {
            header('Content-Type: application/json');
            echo json_encode($result);
            exit;
        }

        $submission = $this->submissionModel->getById($submissionId);
        redirect('?page=submission&id=' . $submissionId);
    }

    public function comment(int $submissionId): void {
        requireAuth();
        if (!validateCsrfToken($_POST['csrf_token'] ?? '')) {
            setFlash('error', 'Invalid security token.');
            redirect('?page=submission&id=' . $submissionId);
        }

        $user    = currentUser();
        $content = trim($_POST['content'] ?? '');

        if (strlen($content) < 2) {
            setFlash('error', 'Comment is too short.');
        } else {
            $this->commentModel->create($submissionId, $user['id'], $content);
            setFlash('success', 'Comment added!');
        }

        redirect('?page=submission&id=' . $submissionId);
    }

    public function deleteComment(int $commentId): void {
        requireAuth();
        $user = currentUser();
        if ($this->commentModel->isOwner($commentId, $user['id'])) {
            $this->commentModel->delete($commentId);
            setFlash('success', 'Comment deleted.');
        }
        $referer = $_SERVER['HTTP_REFERER'] ?? BASE_URL . '/?page=challenges';
        header('Location: ' . $referer);
        exit;
    }
}
