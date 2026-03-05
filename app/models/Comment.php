<?php

require_once BASE_PATH . '/config/database.php';

class Comment {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function getBySubmission(int $submissionId): array {
        $stmt = $this->db->prepare(
            'SELECT c.*, u.name AS author_name
             FROM comments c
             JOIN users u ON c.user_id = u.id
             WHERE c.submission_id = ?
             ORDER BY c.created_at ASC'
        );
        $stmt->execute([$submissionId]);
        return $stmt->fetchAll();
    }

    public function create(int $submissionId, int $userId, string $content): int {
        $stmt = $this->db->prepare(
            'INSERT INTO comments (submission_id, user_id, content) VALUES (?, ?, ?)'
        );
        $stmt->execute([$submissionId, $userId, $content]);
        return (int)$this->db->lastInsertId();
    }

    public function delete(int $id): bool {
        $stmt = $this->db->prepare('DELETE FROM comments WHERE id = ?');
        return $stmt->execute([$id]);
    }

    public function isOwner(int $commentId, int $userId): bool {
        $stmt = $this->db->prepare('SELECT id FROM comments WHERE id = ? AND user_id = ?');
        $stmt->execute([$commentId, $userId]);
        return (bool)$stmt->fetch();
    }
}
