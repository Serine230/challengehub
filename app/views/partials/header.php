<?php
$user  = currentUser();
$flash = $flash ?? getFlash();
$activePage = $_GET['page'] ?? 'home';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= e($pageTitle ?? 'ChallengeHub') ?> — Creative E-Commerce Challenges</title>
  <meta name="description" content="ChallengeHub — Collaborative platform for e-commerce creative challenges.">

  <!-- Bootstrap 5 CDN (single link as required) -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

  <!-- Google Fonts + Custom CSS -->
  <link rel="stylesheet" href="<?= BASE_URL ?>/css/main.css">

  <!-- Bootstrap Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>

<!-- ── Navbar ──────────────────────────────────────────────── -->
<nav class="navbar">
  <div class="container">

    <a href="<?= BASE_URL ?>/?page=home" class="navbar-brand">
      <div class="logo-icon">⚡</div>
      Challenge<span>Hub</span>
    </a>

    <ul class="navbar-nav">
      <li>
        <a href="<?= BASE_URL ?>/?page=home"
           class="<?= $activePage === 'home' ? 'active' : '' ?>">
          <i class="bi bi-house"></i> Home
        </a>
      </li>
      <li>
        <a href="<?= BASE_URL ?>/?page=challenges"
           class="<?= in_array($activePage, ['challenges','challenge']) ? 'active' : '' ?>">
          <i class="bi bi-trophy"></i> Challenges
        </a>
      </li>
      <li>
        <a href="<?= BASE_URL ?>/?page=leaderboard"
           class="<?= $activePage === 'leaderboard' ? 'active' : '' ?>">
          <i class="bi bi-bar-chart-line"></i> Leaderboard
        </a>
      </li>
    </ul>

    <div class="navbar-actions">
      <?php if ($user): ?>
        <a href="<?= BASE_URL ?>/?page=challenge-create" class="btn btn-primary btn-sm">
          <i class="bi bi-plus-lg"></i> New Challenge
        </a>
        <div class="dropdown" style="position:relative;">
          <a href="<?= BASE_URL ?>/?page=profile"
             class="btn btn-ghost btn-sm d-flex align-center gap-1">
            <div class="avatar" style="width:28px;height:28px;font-size:0.75rem;">
              <?= strtoupper(substr($user['name'],0,1)) ?>
            </div>
            <?= e($user['name']) ?>
          </a>
        </div>
        <a href="<?= BASE_URL ?>/?page=logout" class="btn btn-ghost btn-sm">
          <i class="bi bi-box-arrow-right"></i>
        </a>
      <?php else: ?>
        <a href="<?= BASE_URL ?>/?page=login"    class="btn btn-ghost btn-sm">Sign In</a>
        <a href="<?= BASE_URL ?>/?page=register" class="btn btn-primary btn-sm">Get Started</a>
      <?php endif; ?>
    </div>

  </div>
</nav>

<!-- ── Flash Message ───────────────────────────────────────── -->
<?php if ($flash): ?>
<div style="position:fixed;top:80px;right:24px;z-index:9999;max-width:380px;animation:fadeInUp 0.3s ease;">
  <div class="alert alert-<?= e($flash['type']) ?>">
    <i class="bi bi-<?= $flash['type'] === 'success' ? 'check-circle' : 'exclamation-triangle' ?>"></i>
    <?= e($flash['message']) ?>
  </div>
</div>
<script>
  setTimeout(() => {
    const alert = document.querySelector('[style*="fixed"]');
    if (alert) { alert.style.opacity='0'; alert.style.transition='opacity 0.5s'; setTimeout(()=>alert.remove(),500); }
  }, 4000);
</script>
<?php endif; ?>
