<?php
$pageTitle = 'New Challenge';
require BASE_PATH . '/app/views/partials/header.php';

$categories = [
  'Product Launch', 'UX Design', 'Marketing', 'Branding',
  'E-commerce Strategy', 'Social Media', 'SEO', 'Analytics',
  'Customer Retention', 'Conversion Optimization'
];
?>

<div class="page-wrapper">
  <div class="container" style="max-width:760px;">

    <!-- Breadcrumb -->
    <nav style="margin-bottom:24px;font-size:0.85rem;color:var(--text-muted);">
      <a href="<?= BASE_URL ?>/?page=challenges" style="color:var(--text-muted);">Challenges</a>
      <span style="margin:0 8px;opacity:0.4;">/</span>
      <span style="color:var(--text-primary);">New Challenge</span>
    </nav>

    <div class="section-header animate-in">
      <div>
        <h1 class="section-title">🎯 Create a Challenge</h1>
        <p class="section-subtitle">Design an e-commerce challenge for the community</p>
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
        <form method="POST" action="<?= BASE_URL ?>/?page=challenge-create" enctype="multipart/form-data">
          <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">

          <div class="form-group">
            <label class="form-label">Challenge Title *</label>
            <input type="text" name="title" class="form-control"
                   placeholder="e.g. Design the perfect product page for a luxury brand"
                   value="<?= e($_POST['title'] ?? '') ?>" required maxlength="200">
            <div style="font-size:0.75rem;color:var(--text-muted);margin-top:4px;">5–200 characters</div>
          </div>

          <div class="form-group">
            <label class="form-label">Description *</label>
            <textarea name="description" class="form-control" rows="5"
                      placeholder="Describe the challenge in detail: context, goals, constraints, what you're looking for in submissions..."
                      required><?= e($_POST['description'] ?? '') ?></textarea>
            <div style="font-size:0.75rem;color:var(--text-muted);margin-top:4px;">Min. 20 characters</div>
          </div>

          <div class="row g-3">
            <div class="col-md-6">
              <div class="form-group">
                <label class="form-label">Category *</label>
                <select name="category" class="form-control" required>
                  <option value="">Select a category…</option>
                  <?php foreach ($categories as $cat): ?>
                    <option value="<?= e($cat) ?>" <?= ($_POST['category'] ?? '') === $cat ? 'selected' : '' ?>>
                      <?= e($cat) ?>
                    </option>
                  <?php endforeach; ?>
                </select>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label class="form-label">Deadline *</label>
                <input type="date" name="deadline" class="form-control"
                       value="<?= e($_POST['deadline'] ?? '') ?>"
                       min="<?= date('Y-m-d', strtotime('+1 day')) ?>" required>
              </div>
            </div>
          </div>

          <div class="form-group">
            <label class="form-label">Cover Image (Optional)</label>
            <div class="image-upload-area">
              <input type="file" name="image" accept="image/*" onchange="previewImage(this, 'createPreview')">
              <div id="createPreview">
                <i class="bi bi-image" style="font-size:2rem;color:var(--text-muted);display:block;margin-bottom:8px;"></i>
                <p style="color:var(--text-muted);font-size:0.85rem;margin:0;">JPG, PNG, GIF, WebP — Max 5MB</p>
              </div>
            </div>
          </div>

          <!-- Info box -->
          <div style="padding:16px;background:rgba(0,212,255,0.06);border:1px solid rgba(0,212,255,0.15);border-radius:var(--radius-sm);margin-bottom:24px;">
            <div style="display:flex;gap:10px;align-items:flex-start;">
              <i class="bi bi-info-circle-fill" style="color:var(--accent-cyan);margin-top:2px;"></i>
              <div style="font-size:0.82rem;color:var(--text-secondary);">
                <strong style="color:var(--text-primary);">Important:</strong>
                Challenges are limited to <strong><?= MAX_SUBMISSIONS_PER_CHALLENGE ?> participants</strong>.
                Challenges must be related to e-commerce topics. You cannot participate in your own challenge.
              </div>
            </div>
          </div>

          <div style="display:flex;gap:12px;">
            <button type="submit" class="btn btn-primary btn-lg">
              <i class="bi bi-rocket-takeoff"></i> Publish Challenge
            </button>
            <a href="<?= BASE_URL ?>/?page=challenges" class="btn btn-ghost btn-lg">Cancel</a>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<script>
function previewImage(input, previewId) {
  if (input.files && input.files[0]) {
    const reader = new FileReader();
    reader.onload = function(e) {
      document.getElementById(previewId).innerHTML =
        `<img src="${e.target.result}" style="max-height:150px;border-radius:8px;">`;
    };
    reader.readAsDataURL(input.files[0]);
  }
}
</script>

<?php require BASE_PATH . '/app/views/partials/footer.php'; ?>
