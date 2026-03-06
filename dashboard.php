<?php
session_start();
require '../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../app/views/auth/login.php');
    exit;
}

// Supprimer utilisateur
if (isset($_GET['action']) && $_GET['action'] === 'supprimer' && isset($_GET['id'])) {
    $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
    $stmt->execute([$_GET['id']]);
    header('Location: dashboard.php');
    exit;
}

// Stats
$stats = [
    'users'       => $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn(),
    'challenges'  => $pdo->query("SELECT COUNT(*) FROM challenges")->fetchColumn(),
    'submissions' => $pdo->query("SELECT COUNT(*) FROM submissions")->fetchColumn(),
    'comments'    => $pdo->query("SELECT COUNT(*) FROM comments")->fetchColumn(),
    'votes'       => $pdo->query("SELECT COUNT(*) FROM votes")->fetchColumn(),
];

// Pagination utilisateurs
$parPage = 5;
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$offset = ($page - 1) * $parPage;
$totalUsers = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
$totalPages = ceil($totalUsers / $parPage);
$users = $pdo->query("SELECT * FROM users ORDER BY id DESC LIMIT $parPage OFFSET $offset")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Admin - ChallengeHub</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; background: #f0f2f5; display: flex; }

        .sidebar {
            width: 240px; min-height: 100vh;
            background: #1a1a2e; padding: 25px 15px;
            position: fixed; color: white;
        }
        .sidebar h2 { color: #e94560; margin-bottom: 30px; font-size: 1.3em; }
        .sidebar a {
            display: block; color: #ccc; text-decoration: none;
            padding: 12px 15px; border-radius: 8px; margin: 4px 0;
            transition: all 0.2s;
        }
        .sidebar a:hover { background: #e94560; color: white; }

        .main { margin-left: 240px; padding: 30px; width: 100%; }
        .main h1 { margin-bottom: 25px; color: #1a1a2e; }

        .stats-grid {
            display: grid; grid-template-columns: repeat}
