<?php
class Notification {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function ajouter($user_id, $message) {
        $stmt = $this->pdo->prepare("INSERT INTO notifications (user_id, message) VALUES (?, ?)");
        $stmt->execute([$user_id, $message]);
    }

    public function getNonLues($user_id) {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) as total FROM notifications WHERE user_id = ? AND lu = 0");
        $stmt->execute([$user_id]);
        return $stmt->fetch()['total'];
    }

    public function getToutes($user_id) {
        $stmt = $this->pdo->prepare("
            SELECT * FROM notifications 
            WHERE user_id = ? 
            ORDER BY date_creation DESC 
            LIMIT 10
        ");
        $stmt->execute([$user_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>