<?php $pageTitle = 'Create Account'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Create Account — ChallengeHub</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
  <link rel="stylesheet" href="<?= BASE_URL ?>/css/main.css">
</head>
<body>
<div class="auth-wrapper">
  <div style="position:absolute;inset:0;pointer-events:none;overflow:hidden;">
    <div style="position:absolute;top:5%;right:10%;width:350px;height:350px;border-radius:50%;background:radial-gradient(circle,rgba(0,229,160,0.05) 0%,transparent 70%);"></div>
    <div style="position:absolute;bottom:5%;left:5%;width:300px;height:300px;border-radius:50%;background:radial-gradient(circle,rgba(0,212,255,0.05) 0%,transparent 70%);"></div>
  </div>

  <div class="auth-card animate-in" style="max-width:480px;">
    <div class="auth-logo">
      <div style="width:56px;height:56px;background:linear-gradient(135deg,var(--accent-green),var(--accent-cyan));border-radius:16px;display:flex;align-items:center;justify-content:center;font-size:1.8rem;margin:0 auto 16px;box-shadow:0 0 30px rgba(0,229,160,0.3);">🚀</div>
      <h1 class="auth-title">Join ChallengeHub</h1>
      <p class="auth-subtitle">Create your free account and start competing</p>
    </div>

    <?php if (!empty($flash)): ?>
    <div class="alert alert-<?= e($flash['type']) ?>">
      <i class="bi bi-<?= $flash['type'] === 'success' ? 'check-circle' : 'exclamation-triangle' ?>"></i>
      <?= e($flash['message']) ?>
    </div>
    <?php endif; ?>

    <form method="POST" action="<?= BASE_URL ?>/?page=register" novalidate>
      <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">

      <div class="form-group">
        <label class="form-label">Full Name</label>
        <input type="text" name="name" class="form-control"
               placeholder="John Doe"
               value="<?= e($_POST['name'] ?? '') ?>"
               autocomplete="name" required>
      </div>

      <div class="form-group">
        <label class="form-label">Email Address</label>
        <input type="email" name="email" class="form-control"
               placeholder="you@example.com"
               value="<?= e($_POST['email'] ?? '') ?>"
               autocomplete="email" required>
      </div>

      <div class="row g-3">
        <div class="col-6">
          <div class="form-group">
            <label class="form-label">Password</label>
            <input type="password" name="password" class="form-control"
                   placeholder="Min. 8 chars" autocomplete="new-password" required>
          </div>
        </div>
        <div class="col-6">
          <div class="form-group">
            <label class="form-label">Confirm Password</label>
            <input type="password" name="password_confirm" class="form-control"
                   placeholder="Repeat password" autocomplete="new-password" required>
          </div>
        </div>
      </div>

      <button type="submit" class="btn btn-primary w-100" style="padding:13px;">
        <i class="bi bi-person-plus"></i> Create Account
      </button>
    </form>

    <div class="divider"></div>

    <p style="text-align:center;font-size:0.875rem;color:var(--text-muted);">
      Already have an account?
      <a href="<?= BASE_URL ?>/?page=login" style="font-weight:600;">Sign in</a>
    </p>
  </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
