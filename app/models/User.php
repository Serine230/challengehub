<?php

require_once BASE_PATH . '/config/database.php';

class User {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function findByEmail(string $email): ?array {
        $stmt = $this->db->prepare('SELECT * FROM users WHERE email = ? LIMIT 1');
        $stmt->execute([$email]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    public function findById(int $id): ?array {
        $stmt = $this->db->prepare('SELECT * FROM users WHERE id = ? LIMIT 1');
        $stmt->execute([$id]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    public function create(string $name, string $email, string $password): int|false {
        $stmt = $this->db->prepare(
            'INSERT INTO users (name, email, password) VALUES (?, ?, ?)'
        );
        $hashed = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
        $stmt->execute([$name, $email, $hashed]);
        return (int)$this->db->lastInsertId();
    }

    public function update(int $id, string $name, string $email, ?string $password = null): bool {
        if ($password) {
            $stmt = $this->db->prepare(
                'UPDATE users SET name = ?, email = ?, password = ? WHERE id = ?'
            );
            $hashed = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
            return $stmt->execute([$name, $email, $hashed, $id]);
        }
        $stmt = $this->db->prepare('UPDATE users SET name = ?, email = ? WHERE id = ?');
        return $stmt->execute([$name, $email, $id]);
    }

    public function delete(int $id): bool {
        $stmt = $this->db->prepare('DELETE FROM users WHERE id = ?');
        return $stmt->execute([$id]);
    }

    public function emailExists(string $email, ?int $excludeId = null): bool {
        if ($excludeId) {
            $stmt = $this->db->prepare('SELECT id FROM users WHERE email = ? AND id != ? LIMIT 1');
            $stmt->execute([$email, $excludeId]);
        } else {
            $stmt = $this->db->prepare('SELECT id FROM users WHERE email = ? LIMIT 1');
            $stmt->execute([$email]);
        }
        return (bool)$stmt->fetch();
    }

    public function verifyPassword(string $password, string $hash): bool {
        return password_verify($password, $hash);
    }

    public function getStats(int $userId): array {
        $challenges  = $this->db->prepare('SELECT COUNT(*) FROM challenges WHERE user_id = ?');
        $challenges->execute([$userId]);

        $submissions = $this->db->prepare('SELECT COUNT(*) FROM submissions WHERE user_id = ?');
        $submissions->execute([$userId]);

        $votes = $this->db->prepare(
            'SELECT COUNT(*) FROM votes v
             JOIN submissions s ON v.submission_id = s.id
             WHERE s.user_id = ?'
        );
        $votes->execute([$userId]);

        return [
            'challenges'  => (int)$challenges->fetchColumn(),
            'submissions' => (int)$submissions->fetchColumn(),
            'votes'       => (int)$votes->fetchColumn(),
        ];
    }
}
