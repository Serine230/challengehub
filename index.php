<?php

if (!defined('BASE_PATH')) {
    define('BASE_PATH', __DIR__);
}

require_once BASE_PATH . '/config/app.php';
require_once BASE_PATH . '/config/database.php';

startSecureSession();

$page = $_GET['page'] ?? 'home';

// Load controllers
require_once BASE_PATH . '/app/controllers/AuthController.php';
require_once BASE_PATH . '/app/controllers/ChallengeController.php';
require_once BASE_PATH . '/app/controllers/SubmissionController.php';
require_once BASE_PATH . '/app/controllers/ProfileController.php';

$method = $_SERVER['REQUEST_METHOD'];

// ─── Router ───────────────────────────────────────────────────────────────────

switch ($page) {

    // Auth
    case 'login':
        $ctrl = new AuthController();
        $method === 'POST' ? $ctrl->login() : $ctrl->showLogin();
        break;

    case 'register':
        $ctrl = new AuthController();
        $method === 'POST' ? $ctrl->register() : $ctrl->showRegister();
        break;

    case 'logout':
        (new AuthController())->logout();
        break;

    // Home
    case 'home':
    default:
        (new ChallengeController())->home();
        break;

    // Challenges
    case 'challenges':
        (new ChallengeController())->index();
        break;

    case 'challenge':
        $id = (int)($_GET['id'] ?? 0);
        if (!$id) { redirect('?page=challenges'); }
        (new ChallengeController())->show($id);
        break;

    case 'challenge-create':
        $ctrl = new ChallengeController();
        $method === 'POST' ? $ctrl->store() : $ctrl->create();
        break;

    case 'challenge-edit':
        $id   = (int)($_GET['id'] ?? 0);
        $ctrl = new ChallengeController();
        $method === 'POST' ? $ctrl->update($id) : $ctrl->edit($id);
        break;

    case 'challenge-delete':
        $id = (int)($_GET['id'] ?? 0);
        (new ChallengeController())->delete($id);
        break;

    // Submissions
    case 'submission':
        $id = (int)($_GET['id'] ?? 0);
        if (!$id) { redirect('?page=challenges'); }
        (new SubmissionController())->show($id);
        break;

    case 'submission-store':
        $challengeId = (int)($_GET['challenge_id'] ?? 0);
        (new SubmissionController())->store($challengeId);
        break;

    case 'submission-edit':
        $id   = (int)($_GET['id'] ?? 0);
        $ctrl = new SubmissionController();
        $method === 'POST' ? $ctrl->update($id) : $ctrl->edit($id);
        break;

    case 'submission-delete':
        $id = (int)($_GET['id'] ?? 0);
        (new SubmissionController())->delete($id);
        break;

    case 'vote':
        $id = (int)($_GET['submission_id'] ?? 0);
        (new SubmissionController())->vote($id);
        break;

    case 'comment':
        $submissionId = (int)($_GET['submission_id'] ?? 0);
        (new SubmissionController())->comment($submissionId);
        break;

    case 'comment-delete':
        $id = (int)($_GET['id'] ?? 0);
        (new SubmissionController())->deleteComment($id);
        break;

    // Profile
    case 'profile':
        (new ProfileController())->show();
        break;

    case 'profile-edit':
        $ctrl = new ProfileController();
        $method === 'POST' ? $ctrl->update() : $ctrl->edit();
        break;

    case 'profile-delete':
        (new ProfileController())->delete();
        break;

    // Leaderboard
    case 'leaderboard':
        (new ChallengeController())->leaderboard();
        break;
}
