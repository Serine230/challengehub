<?php

require_once BASE_PATH . '/config/database.php';

class Submission {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function getByChallenge(int $challengeId, ?int $currentUserId = null): array {
        $stmt = $this->db->prepare(
            'SELECT s.*, u.name AS author_name,
                    COUNT(DISTINCT v.id) AS vote_count,
                    COUNT(DISTINCT c.id) AS comment_count
             FROM submissions s
             JOIN users u ON s.user_id = u.id
             LEFT JOIN votes v ON v.submission_id = s.id
             LEFT JOIN comments c ON c.submission_id = s.id
             WHERE s.challenge_id = ?
             GROUP BY s.id
             ORDER BY vote_count DESC, s.created_at DESC'
        );
        $stmt->execute([$challengeId]);
        $submissions = $stmt->fetchAll();

        if ($currentUserId) {
            foreach ($submissions as &$sub) {
                $sub['user_voted'] = $this->hasVoted($sub['id'], $currentUserId);
            }
        }
        return $submissions;
    }

    public function getById(int $id): ?array {
        $stmt = $this->db->prepare(
            'SELECT s.*, u.name AS author_name,
                    ch.title AS challenge_title,
                    COUNT(DISTINCT v.id) AS vote_count
             FROM submissions s
             JOIN users u ON s.user_id = u.id
             JOIN challenges ch ON s.challenge_id = ch.id
             LEFT JOIN votes v ON v.submission_id = s.id
             WHERE s.id = ?
             GROUP BY s.id'
        );
        $stmt->execute([$id]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    public function getByUser(int $userId): array {
        $stmt = $this->db->prepare(
            'SELECT s.*, ch.title AS challenge_title,
                    COUNT(DISTINCT v.id) AS vote_count
             FROM submissions s
             JOIN challenges ch ON s.challenge_id = ch.id
             LEFT JOIN votes v ON v.submission_id = s.id
             WHERE s.user_id = ?
             GROUP BY s.id
             ORDER BY s.created_at DESC'
        );
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

    public function create(int $challengeId, int $userId, string $description, ?string $image): int {
        $stmt = $this->db->prepare(
            'INSERT INTO submissions (challenge_id, user_id, description, image)
             VALUES (?, ?, ?, ?)'
        );
        $stmt->execute([$challengeId, $userId, $description, $image]);
        return (int)$this->db->lastInsertId();
    }

    public function update(int $id, string $description, ?string $image): bool {
        if ($image) {
            $stmt = $this->db->prepare('UPDATE submissions SET description=?, image=? WHERE id=?');
            return $stmt->execute([$description, $image, $id]);
        }
        $stmt = $this->db->prepare('UPDATE submissions SET description=? WHERE id=?');
        return $stmt->execute([$description, $id]);
    }

    public function delete(int $id): bool {
        $stmt = $this->db->prepare('DELETE FROM submissions WHERE id = ?');
        return $stmt->execute([$id]);
    }

    public function isOwner(int $submissionId, int $userId): bool {
        $stmt = $this->db->prepare('SELECT id FROM submissions WHERE id = ? AND user_id = ?');
        $stmt->execute([$submissionId, $userId]);
        return (bool)$stmt->fetch();
    }

    public function hasParticipated(int $challengeId, int $userId): bool {
        $stmt = $this->db->prepare('SELECT id FROM submissions WHERE challenge_id = ? AND user_id = ?');
        $stmt->execute([$challengeId, $userId]);
        return (bool)$stmt->fetch();
    }

    public function hasVoted(int $submissionId, int $userId): bool {
        $stmt = $this->db->prepare('SELECT id FROM votes WHERE submission_id = ? AND user_id = ?');
        $stmt->execute([$submissionId, $userId]);
        return (bool)$stmt->fetch();
    }

    public function getLeaderboard(int $limit = 10): array {
        $stmt = $this->db->prepare(
            'SELECT s.*, u.name AS author_name,
                    ch.title AS challenge_title,
                    ch.category,
                    COUNT(DISTINCT v.id) AS vote_count
             FROM submissions s
             JOIN users u ON s.user_id = u.id
             JOIN challenges ch ON s.challenge_id = ch.id
             LEFT JOIN votes v ON v.submission_id = s.id
             GROUP BY s.id
             ORDER BY vote_count DESC
             LIMIT ?'
        );
        $stmt->execute([$limit]);
        return $stmt->fetchAll();
    }

    public function countByChallenge(int $challengeId): int {
        $stmt = $this->db->prepare('SELECT COUNT(*) FROM submissions WHERE challenge_id = ?');
        $stmt->execute([$challengeId]);
        return (int)$stmt->fetchColumn();
    }
}
