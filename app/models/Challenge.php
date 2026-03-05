<?php

require_once BASE_PATH . '/config/database.php';

class Challenge {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function getAll(string $search = '', string $category = '', string $sort = 'date'): array {
        $where  = [];
        $params = [];

        if ($search) {
            $where[]  = '(c.title LIKE ? OR c.description LIKE ?)';
            $params[] = "%$search%";
            $params[] = "%$search%";
        }
        if ($category) {
            $where[]  = 'c.category = ?';
            $params[] = $category;
        }

        $whereClause = $where ? 'WHERE ' . implode(' AND ', $where) : '';

        $orderBy = match($sort) {
            'popular' => 'submission_count DESC',
            'date'    => 'c.created_at DESC',
            default   => 'c.created_at DESC',
        };

        $sql = "SELECT c.*, u.name AS author_name,
                       COUNT(DISTINCT s.id) AS submission_count
                FROM challenges c
                JOIN users u ON c.user_id = u.id
                LEFT JOIN submissions s ON s.challenge_id = c.id
                $whereClause
                GROUP BY c.id
                ORDER BY $orderBy";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function getById(int $id): ?array {
        $stmt = $this->db->prepare(
            'SELECT c.*, u.name AS author_name,
                    COUNT(DISTINCT s.id) AS submission_count
             FROM challenges c
             JOIN users u ON c.user_id = u.id
             LEFT JOIN submissions s ON s.challenge_id = c.id
             WHERE c.id = ?
             GROUP BY c.id'
        );
        $stmt->execute([$id]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    public function getByUser(int $userId): array {
        $stmt = $this->db->prepare(
            'SELECT c.*, COUNT(DISTINCT s.id) AS submission_count
             FROM challenges c
             LEFT JOIN submissions s ON s.challenge_id = c.id
             WHERE c.user_id = ?
             GROUP BY c.id
             ORDER BY c.created_at DESC'
        );
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

    public function create(int $userId, string $title, string $description, string $category, string $deadline, ?string $image): int {
        $stmt = $this->db->prepare(
            'INSERT INTO challenges (user_id, title, description, category, deadline, image)
             VALUES (?, ?, ?, ?, ?, ?)'
        );
        $stmt->execute([$userId, $title, $description, $category, $deadline, $image]);
        return (int)$this->db->lastInsertId();
    }

    public function update(int $id, string $title, string $description, string $category, string $deadline, ?string $image): bool {
        if ($image) {
            $stmt = $this->db->prepare(
                'UPDATE challenges SET title=?, description=?, category=?, deadline=?, image=? WHERE id=?'
            );
            return $stmt->execute([$title, $description, $category, $deadline, $image, $id]);
        }
        $stmt = $this->db->prepare(
            'UPDATE challenges SET title=?, description=?, category=?, deadline=? WHERE id=?'
        );
        return $stmt->execute([$title, $description, $category, $deadline, $id]);
    }

    public function delete(int $id): bool {
        $stmt = $this->db->prepare('DELETE FROM challenges WHERE id = ?');
        return $stmt->execute([$id]);
    }

    public function isOwner(int $challengeId, int $userId): bool {
        $stmt = $this->db->prepare('SELECT id FROM challenges WHERE id = ? AND user_id = ?');
        $stmt->execute([$challengeId, $userId]);
        return (bool)$stmt->fetch();
    }

    public function getCategories(): array {
        $stmt = $this->db->query('SELECT DISTINCT category FROM challenges ORDER BY category');
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    public function isFull(int $challengeId): bool {
        $stmt = $this->db->prepare('SELECT COUNT(*) FROM submissions WHERE challenge_id = ?');
        $stmt->execute([$challengeId]);
        return (int)$stmt->fetchColumn() >= MAX_SUBMISSIONS_PER_CHALLENGE;
    }

    public function getFeatured(int $limit = 3): array {
        $stmt = $this->db->prepare(
            'SELECT c.*, u.name AS author_name,
                    COUNT(DISTINCT s.id) AS submission_count
             FROM challenges c
             JOIN users u ON c.user_id = u.id
             LEFT JOIN submissions s ON s.challenge_id = c.id
             GROUP BY c.id
             ORDER BY submission_count DESC, c.created_at DESC
             LIMIT ?'
        );
        $stmt->execute([$limit]);
        return $stmt->fetchAll();
    }
}
