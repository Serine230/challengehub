<?php
session_start();
require '../config/db.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'Non connecté']);
    exit;
}

$user_id = $_SESSION['user_id'];
$action = $_GET['action'] ?? '';

if ($action === 'get') {
    $stmt = $pdo->prepare("
        SELECT * FROM notifications 
        WHERE user_id = ? 
        ORDER BY date_creation DESC 
        LIMIT 10
    ");
    $stmt->execute([$user_id]);
    $notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $stmt2 = $pdo->prepare("SELECT COUNT(*) as total FROM notifications WHERE user_id = ? AND lu = 0");
    $stmt2->execute([$user_id]);
    $nb = $stmt2->fetch()['total'];

    echo json_encode(['notifications' => $notifications, 'nb' => $nb]);
}

if ($action === 'lire') {
    $stmt = $pdo->prepare("UPDATE notifications SET lu = 1 WHERE user_id = ?");
    $stmt->execute([$user_id]);
    echo json_encode(['success' => true]);
}
?>