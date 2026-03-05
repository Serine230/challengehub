<?php
$pageTitle = 'Edit Challenge';
require BASE_PATH . '/app/views/partials/header.php';

$categories = [
  'Product Launch', 'UX Design', 'Marketing', 'Branding',
  'E-commerce Strategy', 'Social Media', 'SEO', 'Analytics',
  'Customer Retention', 'Conversion Optimization'
];
?>

<div class="page-wrapper">
  <div class="container" style="max-width:760px;">

    <nav style="margin-bottom:24px;font-size:0.85rem;color:var(--text-muted);">
      <a href="<?= BASE_URL ?>/?page=challenges" style="color:var(--text-muted);">Challenges</a>
      <span style="margin:0 8px;opacity:0.4;">/</span>
      <a href="<?= BASE_URL ?>/?page=challenge&id=<?= $challenge['id'] ?>" style="color:var(--text-muted);">
        <?= e($challenge['title']) ?>
      </a>
      <span style="margin:0 8px;opacity:0.4;">/</span>
      <span style="color:var(--text-primary);">Edit</span>
    </nav>

    <div class="section-header animate-in">
      <div>
        <h1 class="section-title">✏️ Edit Challenge</h1>
        <p class="section-subtitle">Update your challenge details</p>
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
        <form method="POST" action="<?= BASE_URL ?>/?page=challenge-edit&id=<?= $challenge['id'] ?>"
              enctype="multipart/form-data">
          <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">

          <div class="form-group">
            <label class="form-label">Challenge Title *</label>
            <input type="text" name="title" class="form-control"
                   value="<?= e($_POST['title'] ?? $challenge['title']) ?>"
                   required maxlength="200">
          </div>

          <div class="form-group">
            <label class="form-label">Description *</label>
            <textarea name="description" class="form-control" rows="5" required>
<?= e($_POST['description'] ?? $challenge['description']) ?></textarea>
          </div>

          <div class="row g-3">
            <div class="col-md-6">
              <div class="form-group">
                <label class="form-label">Category *</label>
                <select name="category" class="form-control" required>
                  <option value="">Select category…</option>
                  <?php foreach ($categories as $cat):
                    $sel = (($_POST['category'] ?? $challenge['category']) === $cat) ? 'selected' : '';
                  ?>
                    <option value="<?= e($cat) ?>" <?= $sel ?>><?= e($cat) ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label class="form-label">Deadline *</label>
                <input type="date" name="deadline" class="form-control"
                       value="<?= e($_POST['deadline'] ?? $challenge['deadline']) ?>" required>
              </div>
            </div>
          </div>

          <div class="form-group">
            <label class="form-label">Update Cover Image (Optional)</label>
            <?php if ($challenge['image']): ?>
              <div style="margin-bottom:12px;">
                <img src="<?= UPLOAD_URL . e($challenge['image']) ?>" alt="Current"
                     style="max-height:140px;border-radius:var(--radius-sm);border:1px solid var(--border);">
                <div style="font-size:0.78rem;color:var(--text-muted);margin-top:6px;">Current image — upload a new one to replace it</div>
              </div>
            <?php endif; ?>
            <div class="image-upload-area">
              <input type="file" name="image" accept="image/*" onchange="previewImage(this, 'editPreview')">
              <div id="editPreview">
                <i class="bi bi-cloud-upload" style="font-size:1.5rem;color:var(--text-muted);"></i>
                <p style="color:var(--text-muted);font-size:0.82rem;margin:4px 0 0;">Upload new image</p>
              </div>
            </div>
          </div>

          <div style="display:flex;gap:12px;">
            <button type="submit" class="btn btn-primary btn-lg">
              <i class="bi bi-check-lg"></i> Save Changes
            </button>
            <a href="<?= BASE_URL ?>/?page=challenge&id=<?= $challenge['id'] ?>" class="btn btn-ghost btn-lg">
              Cancel
            </a>
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
        `<img src="${e.target.result}" style="max-height:120px;border-radius:8px;">`;
    };
    reader.readAsDataURL(input.files[0]);
  }
}
</script>

<?php require BASE_PATH . '/app/views/partials/footer.php'; ?>
