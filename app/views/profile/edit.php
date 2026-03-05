<?php
$pageTitle = 'Edit Profile';
require BASE_PATH . '/app/views/partials/header.php';
?>

<div class="page-wrapper">
  <div class="container" style="max-width:560px;">

    <nav style="margin-bottom:24px;font-size:0.85rem;color:var(--text-muted);">
      <a href="<?= BASE_URL ?>/?page=profile" style="color:var(--text-muted);">Profile</a>
      <span style="margin:0 8px;opacity:0.4;">/</span>
      <span style="color:var(--text-primary);">Edit</span>
    </nav>

    <div class="section-header animate-in">
      <div>
        <h1 class="section-title">⚙️ Edit Profile</h1>
        <p class="section-subtitle">Update your account information</p>
      </div>
    </div>

    <?php if (!empty($flash)): ?>
    <div class="alert alert-<?= e($flash['type']) ?>">
      <i class="bi bi-<?= $flash['type'] === 'success' ? 'check-circle' : 'exclamation-triangle' ?>"></i>
      <?= e($flash['message']) ?>
    </div>
    <?php endif; ?>

    <div class="card animate-in">
      <div class="card-body" style="padding:32px;">
        <form method="POST" action="<?= BASE_URL ?>/?page=profile-edit">
          <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">

          <div class="form-group">
            <label class="form-label">Full Name *</label>
            <input type="text" name="name" class="form-control"
                   value="<?= e($_POST['name'] ?? $user['name']) ?>"
                   required autocomplete="name">
          </div>

          <div class="form-group">
            <label class="form-label">Email Address *</label>
            <input type="email" name="email" class="form-control"
                   value="<?= e($_POST['email'] ?? $user['email']) ?>"
                   required autocomplete="email">
          </div>

          <div class="divider"></div>

          <div style="margin-bottom:20px;">
            <h4 style="font-size:0.875rem;font-weight:700;color:var(--text-secondary);margin-bottom:4px;">
              Change Password
            </h4>
            <p style="font-size:0.8rem;color:var(--text-muted);">Leave blank to keep your current password</p>
          </div>

          <div class="form-group">
            <label class="form-label">New Password</label>
            <input type="password" name="password" class="form-control"
                   placeholder="Min. 8 characters" autocomplete="new-password">
          </div>

          <div class="form-group">
            <label class="form-label">Confirm New Password</label>
            <input type="password" name="password_confirm" class="form-control"
                   placeholder="Repeat new password" autocomplete="new-password">
          </div>

          <div style="display:flex;gap:12px;margin-top:24px;">
            <button type="submit" class="btn btn-primary btn-lg">
              <i class="bi bi-check-lg"></i> Save Changes
            </button>
            <a href="<?= BASE_URL ?>/?page=profile" class="btn btn-ghost btn-lg">Cancel</a>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<?php require BASE_PATH . '/app/views/partials/footer.php'; ?>
