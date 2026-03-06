<?php
class Badge {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function verifierEtAttribuer($user_id) {
        // Compter soumissions
        $stmt = $this->pdo->prepare("SELECT COUNT(*) as total FROM submissions WHERE user_id = ?");
        $stmt->execute([$user_id]);
        $nbSubmissions = $stmt->fetch()['total'];

        // Compter votes
        $stmt = $this->pdo->prepare("SELECT COUNT(*) as total FROM votes WHERE user_id = ?");
        $stmt->execute([$user_id]);
        $nbVotes = $stmt->fetch()['total'];

        $regles = [
            ['condition' => $nbSubmissions >= 1,  'badge_id' => 1],
            ['condition' => $nbSubmissions >= 5,  'badge_id' => 2],
            ['condition' => $nbSubmissions >= 10, 'badge_id' => 3],
            ['condition' => $nbVotes >= 10,       'badge_id' => 4],
        ];

        foreach ($regles as $regle) {
            if ($regle['condition']) {
                $stmt = $this->pdo->prepare("SELECT id FROM user_badges WHERE user_id = ? AND badge_id = ?");
                $stmt->execute([$user_id, $regle['badge_id']]);

                if (!$stmt->fetch()) {
                    $stmt = $this->pdo->prepare("INSERT INTO user_badges (user_id, badge_id) VALUES (?, ?)");
                    $stmt->execute([$user_id, $regle['badge_id']]);

                    // Notification automatique
                    $notif = new Notification($this->pdo);
                    $stmt2 = $this->pdo->prepare("SELECT nom, icone FROM badges WHERE id = ?");
                    $stmt2->execute([$regle['badge_id']]);
                    $badge = $stmt2->fetch();
                    $notif->ajouter($user_id, "🎉 Badge obtenu : " . $badge['icone'] . " " . $badge['nom']);
                }
            }
        }
    }

    public function getBadgesUser($user_id) {
        $stmt = $this->pdo->prepare("
            SELECT b.nom, b.icone, b.description, ub.date_obtenu
            FROM user_badges ub
            JOIN badges b ON ub.badge_id = b.id
            WHERE ub.user_id = ?
            ORDER BY ub.date_obtenu DESC
        ");
        $stmt->execute([$user_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>