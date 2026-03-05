<?php
$pageTitle = 'Edit Submission';
require BASE_PATH . '/app/views/partials/header.php';
?>

<div class="page-wrapper">
  <div class="container" style="max-width:720px;">

    <nav style="margin-bottom:24px;font-size:0.85rem;color:var(--text-muted);">
      <a href="<?= BASE_URL ?>/?page=submission&id=<?= $submission['id'] ?>" style="color:var(--text-muted);">
        Submission
      </a>
      <span style="margin:0 8px;opacity:0.4;">/</span>
      <span style="color:var(--text-primary);">Edit</span>
    </nav>

    <div class="section-header animate-in">
      <div>
        <h1 class="section-title">✏️ Edit Submission</h1>
        <p class="section-subtitle">Update your entry for "<?= e($submission['challenge_title']) ?>"</p>
      </div>
    </div>

    <?php if (!empty($flash)): ?>
    <div class="alert alert-<?= e($flash['type']) ?>">
      <i class="bi bi-exclamation-triangle"></i>
      <?= e($flash['message']) ?>
    </div>
    <?php endif; ?>

    <div class="card animate-in">
      <div class="card-body" style="padding:32px;">
        <form method="POST" action="<?= BASE_URL ?>/?page=submission-edit&id=<?= $submission['id'] ?>"
              enctype="multipart/form-data">
          <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">

          <div class="form-group">
            <label class="form-label">Your Solution *</label>
            <textarea name="description" class="form-control" rows="6" required>
<?= e($_POST['description'] ?? $submission['description']) ?></textarea>
          </div>

          <div class="form-group">
            <label class="form-label">Update Image (Optional)</label>
            <?php if ($submission['image']): ?>
              <div style="margin-bottom:12px;">
                <img src="<?= UPLOAD_URL . e($submission['image']) ?>" alt="Current"
                     style="max-height:140px;border-radius:var(--radius-sm);border:1px solid var(--border);">
                <div style="font-size:0.78rem;color:var(--text-muted);margin-top:6px;">Current image</div>
              </div>
            <?php endif; ?>
            <div class="image-upload-area">
              <input type="file" name="image" accept="image/*" onchange="previewImage(this)">
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
            <a href="<?= BASE_URL ?>/?page=submission&id=<?= $submission['id'] ?>" class="btn btn-ghost btn-lg">
              Cancel
            </a>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<script>
function previewImage(input) {
  if (input.files && input.files[0]) {
    const reader = new FileReader();
    reader.onload = e => {
      document.getElementById('editPreview').innerHTML =
        `<img src="${e.target.result}" style="max-height:120px;border-radius:8px;">`;
    };
    reader.readAsDataURL(input.files[0]);
  }
}
</script>

<?php require BASE_PATH . '/app/views/partials/footer.php'; ?>
