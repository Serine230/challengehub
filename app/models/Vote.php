<?php

require_once BASE_PATH . '/config/database.php';

class Vote {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function toggle(int $submissionId, int $userId): array {
        $stmt = $this->db->prepare('SELECT id FROM votes WHERE submission_id = ? AND user_id = ?');
        $stmt->execute([$submissionId, $userId]);
        $existing = $stmt->fetch();

        if ($existing) {
            $del = $this->db->prepare('DELETE FROM votes WHERE submission_id = ? AND user_id = ?');
            $del->execute([$submissionId, $userId]);
            $action = 'removed';
        } else {
            $ins = $this->db->prepare('INSERT INTO votes (submission_id, user_id) VALUES (?, ?)');
            $ins->execute([$submissionId, $userId]);
            $action = 'added';
        }

        $count = $this->db->prepare('SELECT COUNT(*) FROM votes WHERE submission_id = ?');
        $count->execute([$submissionId]);

        return [
            'action' => $action,
            'count'  => (int)$count->fetchColumn(),
        ];
    }

    public function getCount(int $submissionId): int {
        $stmt = $this->db->prepare('SELECT COUNT(*) FROM votes WHERE submission_id = ?');
        $stmt->execute([$submissionId]);
        return (int)$stmt->fetchColumn();
    }

    public function hasVoted(int $submissionId, int $userId): bool {
        $stmt = $this->db->prepare('SELECT id FROM votes WHERE submission_id = ? AND user_id = ?');
        $stmt->execute([$submissionId, $userId]);
        return (bool)$stmt->fetch();
    }
}
