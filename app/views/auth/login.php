<?php
$pageTitle = 'Sign In';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Sign In — ChallengeHub</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
  <link rel="stylesheet" href="<?= BASE_URL ?>/css/main.css">
</head>
<body>
<div class="auth-wrapper">
  <div style="position:absolute;inset:0;pointer-events:none;overflow:hidden;">
    <div style="position:absolute;top:10%;left:5%;width:300px;height:300px;border-radius:50%;background:radial-gradient(circle,rgba(0,212,255,0.05) 0%,transparent 70%);"></div>
    <div style="position:absolute;bottom:10%;right:5%;width:400px;height:400px;border-radius:50%;background:radial-gradient(circle,rgba(168,85,247,0.05) 0%,transparent 70%);"></div>
  </div>

  <div class="auth-card animate-in">
    <div class="auth-logo">
      <div style="width:56px;height:56px;background:linear-gradient(135deg,var(--accent-cyan),var(--accent-blue));border-radius:16px;display:flex;align-items:center;justify-content:center;font-size:1.8rem;margin:0 auto 16px;box-shadow:0 0 30px rgba(0,212,255,0.3);">⚡</div>
      <h1 class="auth-title">Welcome back</h1>
      <p class="auth-subtitle">Sign in to your ChallengeHub account</p>
    </div>

    <?php if (!empty($flash)): ?>
    <div class="alert alert-<?= e($flash['type']) ?>">
      <i class="bi bi-<?= $flash['type'] === 'success' ? 'check-circle' : 'exclamation-triangle' ?>"></i>
      <?= e($flash['message']) ?>
    </div>
    <?php endif; ?>

    <form method="POST" action="<?= BASE_URL ?>/?page=login" novalidate>
      <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">

      <div class="form-group">
        <label class="form-label">Email Address</label>
        <input type="email" name="email" class="form-control"
               placeholder="you@example.com"
               value="<?= e($_POST['email'] ?? '') ?>"
               autocomplete="email" required>
      </div>

      <div class="form-group">
        <label class="form-label">Password</label>
        <div style="position:relative;">
          <input type="password" name="password" id="pwField" class="form-control"
                 placeholder="••••••••"
                 autocomplete="current-password" required style="padding-right:48px;">
          <button type="button" onclick="togglePw()" style="position:absolute;right:12px;top:50%;transform:translateY(-50%);background:none;border:none;color:var(--text-muted);cursor:pointer;font-size:1rem;">
            <i class="bi bi-eye" id="eyeIcon"></i>
          </button>
        </div>
      </div>

      <button type="submit" class="btn btn-primary w-100" style="margin-top:8px;padding:13px;">
        <i class="bi bi-box-arrow-in-right"></i> Sign In
      </button>
    </form>

    <div class="divider"></div>

    <p style="text-align:center;font-size:0.875rem;color:var(--text-muted);">
      Don't have an account?
      <a href="<?= BASE_URL ?>/?page=register" style="font-weight:600;">Create one free</a>
    </p>
  </div>
</div>
<script>
function togglePw() {
  const f = document.getElementById('pwField');
  const i = document.getElementById('eyeIcon');
  if (f.type === 'password') { f.type = 'text'; i.className = 'bi bi-eye-slash'; }
  else { f.type = 'password'; i.className = 'bi bi-eye'; }
}
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
