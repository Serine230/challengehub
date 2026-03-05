<?php
$pageTitle = e($challenge['title']);
require BASE_PATH . '/app/views/partials/header.php';

$submissionCount = count($submissions);
$pct    = min(100, ($submissionCount / MAX_SUBMISSIONS_PER_CHALLENGE) * 100);
$userId = $currentUser ? $currentUser['id'] : null;
$isOwner = $userId && $challenge['user_id'] == $userId;
$daysLeft = ceil((strtotime($challenge['deadline']) - time()) / 86400);
$dlClass  = $daysLeft < 3 ? 'urgent' : ($daysLeft < 7 ? 'soon' : 'ok');
$canSubmit = $userId && !$hasParticipated && !$isFull && !$isOwner && $daysLeft > 0;
?>

<div class="page-wrapper">
  <div class="container">

    <!-- ── Breadcrumb ────────────────────────────────────────── -->
    <nav style="margin-bottom:24px;font-size:0.85rem;color:var(--text-muted);">
      <a href="<?= BASE_URL ?>/?page=challenges" style="color:var(--text-muted);">Challenges</a>
      <span style="margin:0 8px;opacity:0.4;">/</span>
      <span style="color:var(--text-primary);"><?= e($challenge['title']) ?></span>
    </nav>

    <div class="row g-4">

      <!-- ── Left: Challenge Details ───────────────────────── -->
      <div class="col-lg-4">
        <div class="card" style="position:sticky;top:90px;">

          <?php if ($challenge['image']): ?>
            <img src="<?= UPLOAD_URL . e($challenge['image']) ?>" alt="<?= e($challenge['title']) ?>"
                 style="width:100%;height:220px;object-fit:cover;">
          <?php else: ?>
            <div style="width:100%;height:180px;background:linear-gradient(135deg,var(--bg-elevated),var(--bg-hover));display:flex;align-items:center;justify-content:center;font-size:4rem;">🏆</div>
          <?php endif; ?>

          <div class="card-body">
            <div style="display:flex;gap:8px;flex-wrap:wrap;margin-bottom:12px;">
              <span class="badge badge-cyan"><?= e($challenge['category']) ?></span>
              <?php if ($isFull): ?>
                <span class="badge badge-red"><i class="bi bi-lock-fill"></i> Full</span>
              <?php endif; ?>
            </div>

            <h1 style="font-size:1.3rem;margin-bottom:10px;"><?= e($challenge['title']) ?></h1>
            <p style="font-size:0.875rem;color:var(--text-secondary);line-height:1.7;margin-bottom:20px;">
              <?= nl2br(e($challenge['description'])) ?>
            </p>

            <div style="display:flex;flex-direction:column;gap:10px;padding:16px;background:var(--bg-deep);border-radius:var(--radius-sm);margin-bottom:20px;">
              <div style="display:flex;justify-content:space-between;font-size:0.82rem;">
                <span style="color:var(--text-muted);display:flex;align-items:center;gap:5px;">
                  <i class="bi bi-person"></i> Created by
                </span>
                <span style="color:var(--text-primary);font-weight:600;"><?= e($challenge['author_name']) ?></span>
              </div>
              <div style="display:flex;justify-content:space-between;font-size:0.82rem;">
                <span style="color:var(--text-muted);display:flex;align-items:center;gap:5px;">
                  <i class="bi bi-clock"></i> Deadline
                </span>
                <span class="deadline <?= $dlClass ?>" style="font-weight:600;">
                  <?= date('M d, Y', strtotime($challenge['deadline'])) ?>
                  (<?= $daysLeft > 0 ? $daysLeft . 'd left' : 'Ended' ?>)
                </span>
              </div>
              <div style="display:flex;justify-content:space-between;font-size:0.82rem;">
                <span style="color:var(--text-muted);display:flex;align-items:center;gap:5px;">
                  <i class="bi bi-people"></i> Participants
                </span>
                <span style="color:var(--accent-cyan);font-weight:600;">
                  <?= $submissionCount ?>/<?= MAX_SUBMISSIONS_PER_CHALLENGE ?>
                </span>
              </div>
            </div>

            <!-- Progress -->
            <div class="progress-bar-wrap" style="margin-bottom:20px;">
              <div class="progress-bar-fill <?= $pct >= 100 ? 'full' : '' ?>" style="width:<?= $pct ?>%"></div>
            </div>

            <!-- Actions -->
            <?php if ($isOwner): ?>
              <div style="display:flex;flex-direction:column;gap:8px;">
                <a href="<?= BASE_URL ?>/?page=challenge-edit&id=<?= $challenge['id'] ?>" class="btn btn-secondary">
                  <i class="bi bi-pencil"></i> Edit Challenge
                </a>
                <form method="POST" action="<?= BASE_URL ?>/?page=challenge-delete&id=<?= $challenge['id'] ?>"
                      onsubmit="return confirm('Delete this challenge and all its submissions?')">
                  <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">
                  <button type="submit" class="btn btn-danger w-100">
                    <i class="bi bi-trash"></i> Delete Challenge
                  </button>
                </form>
              </div>
            <?php elseif (!$currentUser): ?>
              <a href="<?= BASE_URL ?>/?page=login" class="btn btn-primary w-100">
                <i class="bi bi-box-arrow-in-right"></i> Sign in to Participate
              </a>
            <?php elseif ($hasParticipated): ?>
              <div class="btn btn-success w-100" style="cursor:default;">
                <i class="bi bi-check-circle-fill"></i> You've submitted!
              </div>
            <?php elseif ($isFull): ?>
              <div class="btn w-100" style="background:var(--bg-elevated);color:var(--text-muted);cursor:not-allowed;">
                <i class="bi bi-lock"></i> Challenge is Full
              </div>
            <?php elseif ($daysLeft <= 0): ?>
              <div class="btn w-100" style="background:var(--bg-elevated);color:var(--text-muted);cursor:not-allowed;">
                <i class="bi bi-calendar-x"></i> Challenge Ended
              </div>
            <?php endif; ?>
          </div>
        </div>
      </div>

      <!-- ── Right: Submissions ───────────────────────────── -->
      <div class="col-lg-8">

        <!-- Submit form -->
        <?php if ($canSubmit): ?>
        <div class="card mb-4 animate-in">
          <div class="card-header">
            <h2 style="font-size:1.1rem;margin:0;">
              <i class="bi bi-send" style="color:var(--accent-cyan);"></i>
              Submit Your Entry
            </h2>
          </div>
          <div class="card-body">
            <form method="POST" action="<?= BASE_URL ?>/?page=submission-store&challenge_id=<?= $challenge['id'] ?>"
                  enctype="multipart/form-data">
              <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">

              <div class="form-group">
                <label class="form-label">Your Solution / Description *</label>
                <textarea name="description" class="form-control" rows="4"
                          placeholder="Describe your creative solution in detail (min. 20 characters)..."
                          required></textarea>
              </div>

              <div class="form-group">
                <label class="form-label">Upload Image (Optional)</label>
                <div class="image-upload-area">
                  <input type="file" name="image" accept="image/*" onchange="previewImage(this)">
                  <div id="uploadPreview">
                    <i class="bi bi-cloud-upload" style="font-size:2rem;color:var(--text-muted);display:block;margin-bottom:8px;"></i>
                    <p style="color:var(--text-muted);font-size:0.85rem;margin:0;">Click or drag to upload (max 5MB)</p>
                  </div>
                </div>
              </div>

              <button type="submit" class="btn btn-primary">
                <i class="bi bi-send-fill"></i> Submit Entry
              </button>
            </form>
          </div>
        </div>
        <?php endif; ?>

        <!-- Submissions List -->
        <div class="section-header" style="margin-bottom:20px;">
          <h2 style="font-size:1.2rem;margin:0;">
            <i class="bi bi-collection" style="color:var(--accent-cyan);"></i>
            Submissions
            <span style="color:var(--text-muted);font-size:0.875rem;font-weight:400;">(<?= $submissionCount ?>)</span>
          </h2>
        </div>

        <?php if (empty($submissions)): ?>
          <div class="empty-state">
            <div class="empty-state-icon">📝</div>
            <h3>No submissions yet</h3>
            <p>Be the first to submit your solution!</p>
          </div>
        <?php else: ?>
          <?php foreach ($submissions as $i => $sub): ?>
          <div class="submission-card mb-3 animate-in <?= $i < 3 ? 'ranked-'.($i+1) : '' ?>">
            <div style="display:flex;align-items:flex-start;gap:14px;margin-bottom:12px;">
              <div style="font-family:var(--font-display);font-size:1.4rem;font-weight:800;min-width:40px;text-align:center;color:<?= $i===0 ? 'var(--accent-amber)' : ($i===1 ? '#94a3b8' : ($i===2 ? '#cd7f32' : 'var(--text-muted)')) ?>">
                <?= $i === 0 ? '🥇' : ($i === 1 ? '🥈' : ($i === 2 ? '🥉' : '#'.($i+1))) ?>
              </div>
              <div class="avatar"><?= strtoupper(substr($sub['author_name'],0,1)) ?></div>
              <div style="flex:1;">
                <div style="font-weight:600;font-size:0.9rem;"><?= e($sub['author_name']) ?></div>
                <div style="font-size:0.75rem;color:var(--text-muted);"><?= timeAgo($sub['created_at']) ?></div>
              </div>
              <div style="display:flex;align-items:center;gap:8px;">
                <!-- Vote -->
                <?php if ($currentUser && $sub['user_id'] != $userId): ?>
                <a href="<?= BASE_URL ?>/?page=vote&submission_id=<?= $sub['id'] ?>"
                   class="vote-btn <?= !empty($sub['user_voted']) ? 'voted' : '' ?>"
                   id="vote-<?= $sub['id'] ?>"
                   onclick="return handleVote(event, <?= $sub['id'] ?>)">
                  <i class="bi bi-star<?= !empty($sub['user_voted']) ? '-fill' : '' ?>"></i>
                  <span id="vote-count-<?= $sub['id'] ?>"><?= $sub['vote_count'] ?></span>
                </a>
                <?php else: ?>
                <span style="display:inline-flex;align-items:center;gap:5px;font-size:0.85rem;color:var(--accent-amber);font-weight:600;">
                  <i class="bi bi-star-fill"></i> <?= $sub['vote_count'] ?>
                </span>
                <?php endif; ?>

                <!-- Edit/Delete if owner -->
                <?php if ($userId && $sub['user_id'] == $userId): ?>
                <a href="<?= BASE_URL ?>/?page=submission-edit&id=<?= $sub['id'] ?>" class="btn btn-ghost btn-sm">
                  <i class="bi bi-pencil"></i>
                </a>
                <form method="POST" action="<?= BASE_URL ?>/?page=submission-delete&id=<?= $sub['id'] ?>"
                      onsubmit="return confirm('Delete this submission?')" style="display:inline;">
                  <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">
                  <button class="btn btn-danger btn-sm"><i class="bi bi-trash"></i></button>
                </form>
                <?php endif; ?>
              </div>
            </div>

            <p style="font-size:0.9rem;color:var(--text-secondary);line-height:1.7;margin-bottom:12px;padding-left:54px;">
              <?= nl2br(e($sub['description'])) ?>
            </p>

            <?php if ($sub['image']): ?>
            <div style="padding-left:54px;margin-bottom:12px;">
              <img src="<?= UPLOAD_URL . e($sub['image']) ?>" alt="Submission image"
                   style="max-width:100%;max-height:300px;border-radius:var(--radius-sm);border:1px solid var(--border);">
            </div>
            <?php endif; ?>

            <div style="padding-left:54px;">
              <a href="<?= BASE_URL ?>/?page=submission&id=<?= $sub['id'] ?>"
                 class="btn btn-ghost btn-sm">
                <i class="bi bi-chat"></i>
                <?= $sub['comment_count'] ?> comment<?= $sub['comment_count'] != 1 ? 's' : '' ?>
              </a>
            </div>
          </div>
          <?php endforeach; ?>
        <?php endif; ?>

      </div>
    </div>
  </div>
</div>

<script>
function previewImage(input) {
  if (input.files && input.files[0]) {
    const reader = new FileReader();
    reader.onload = function(e) {
      document.getElementById('uploadPreview').innerHTML =
        `<img src="${e.target.result}" style="max-height:120px;border-radius:8px;">`;
    };
    reader.readAsDataURL(input.files[0]);
  }
}

async function handleVote(e, id) {
  e.preventDefault();
  const btn   = document.getElementById('vote-' + id);
  const count = document.getElementById('vote-count-' + id);
  try {
    const res  = await fetch('<?= BASE_URL ?>/?page=vote&submission_id=' + id, {
      method: 'GET',
      headers: { 'X-Requested-With': 'XMLHttpRequest' }
    });
    const data = await res.json();
    count.textContent = data.count;
    if (data.action === 'added') {
      btn.classList.add('voted');
      btn.querySelector('i').className = 'bi bi-star-fill';
    } else {
      btn.classList.remove('voted');
      btn.querySelector('i').className = 'bi bi-star';
    }
  } catch(err) { window.location = btn.href; }
  return false;
}
</script>

<?php require BASE_PATH . '/app/views/partials/footer.php'; ?>
