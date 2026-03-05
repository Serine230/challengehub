<?php
$pageTitle = 'Submission';
require BASE_PATH . '/app/views/partials/header.php';
$userId = $currentUser ? $currentUser['id'] : null;
$isOwner = $userId && $submission['user_id'] == $userId;
?>

<div class="page-wrapper">
  <div class="container" style="max-width:860px;">

    <!-- Breadcrumb -->
    <nav style="margin-bottom:24px;font-size:0.85rem;color:var(--text-muted);">
      <a href="<?= BASE_URL ?>/?page=challenges" style="color:var(--text-muted);">Challenges</a>
      <span style="margin:0 8px;opacity:0.4;">/</span>
      <a href="<?= BASE_URL ?>/?page=challenge&id=<?= $submission['challenge_id'] ?>" style="color:var(--text-muted);">
        <?= e($submission['challenge_title']) ?>
      </a>
      <span style="margin:0 8px;opacity:0.4;">/</span>
      <span style="color:var(--text-primary);">Submission</span>
    </nav>

    <div class="row g-4">

      <!-- ── Main Content ───────────────────────────────────── -->
      <div class="col-lg-8">
        <div class="card animate-in">
          <div class="card-header" style="display:flex;align-items:center;justify-content:space-between;">
            <div style="display:flex;align-items:center;gap:12px;">
              <div class="avatar"><?= strtoupper(substr($submission['author_name'],0,1)) ?></div>
              <div>
                <div style="font-weight:600;"><?= e($submission['author_name']) ?></div>
                <div style="font-size:0.75rem;color:var(--text-muted);"><?= timeAgo($submission['created_at']) ?></div>
              </div>
            </div>
            <div style="display:flex;align-items:center;gap:8px;">
              <?php if ($isOwner): ?>
                <a href="<?= BASE_URL ?>/?page=submission-edit&id=<?= $submission['id'] ?>" class="btn btn-ghost btn-sm">
                  <i class="bi bi-pencil"></i> Edit
                </a>
                <form method="POST" action="<?= BASE_URL ?>/?page=submission-delete&id=<?= $submission['id'] ?>"
                      onsubmit="return confirm('Delete this submission?')" style="display:inline;">
                  <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">
                  <button class="btn btn-danger btn-sm"><i class="bi bi-trash"></i></button>
                </form>
              <?php endif; ?>
            </div>
          </div>

          <div class="card-body">
            <p style="font-size:1rem;color:var(--text-secondary);line-height:1.8;margin-bottom:20px;">
              <?= nl2br(e($submission['description'])) ?>
            </p>

            <?php if ($submission['image']): ?>
            <div style="margin-bottom:24px;">
              <img src="<?= UPLOAD_URL . e($submission['image']) ?>" alt="Submission"
                   style="max-width:100%;border-radius:var(--radius-md);border:1px solid var(--border);">
            </div>
            <?php endif; ?>

            <!-- Vote Area -->
            <div style="display:flex;align-items:center;gap:16px;padding-top:16px;border-top:1px solid var(--border);">
              <?php if ($currentUser && !$isOwner): ?>
                <a href="<?= BASE_URL ?>/?page=vote&submission_id=<?= $submission['id'] ?>"
                   class="vote-btn <?= $hasVoted ? 'voted' : '' ?>"
                   id="vote-btn"
                   onclick="return handleVote(event)">
                  <i class="bi bi-star<?= $hasVoted ? '-fill' : '' ?>"></i>
                  <span id="vote-count"><?= $submission['vote_count'] ?></span>
                  <?= $hasVoted ? 'Voted' : 'Vote' ?>
                </a>
              <?php else: ?>
                <div style="display:flex;align-items:center;gap:6px;color:var(--accent-amber);font-weight:700;">
                  <i class="bi bi-star-fill"></i>
                  <span><?= $submission['vote_count'] ?></span> votes
                </div>
              <?php endif; ?>
              <span style="font-size:0.82rem;color:var(--text-muted);">
                <i class="bi bi-chat"></i> <?= count($comments) ?> comments
              </span>
            </div>
          </div>
        </div>

        <!-- ── Comments ──────────────────────────────────────── -->
        <div class="mt-4 animate-in">
          <h3 style="font-size:1rem;font-weight:700;margin-bottom:20px;">
            <i class="bi bi-chat-square-text" style="color:var(--accent-cyan);"></i>
            Comments (<?= count($comments) ?>)
          </h3>

          <?php if (empty($comments)): ?>
            <div style="text-align:center;padding:32px;color:var(--text-muted);font-size:0.875rem;">
              <i class="bi bi-chat" style="font-size:2rem;display:block;margin-bottom:8px;opacity:0.5;"></i>
              No comments yet. Be the first to comment!
            </div>
          <?php else: ?>
            <?php foreach ($comments as $c): ?>
            <div class="comment-item">
              <div class="comment-header">
                <div style="display:flex;align-items:center;gap:8px;">
                  <div class="avatar" style="width:30px;height:30px;font-size:0.75rem;">
                    <?= strtoupper(substr($c['author_name'],0,1)) ?>
                  </div>
                  <span class="comment-author"><?= e($c['author_name']) ?></span>
                  <span class="comment-time"><?= timeAgo($c['created_at']) ?></span>
                </div>
                <?php if ($userId && $c['user_id'] == $userId): ?>
                <form method="POST" action="<?= BASE_URL ?>/?page=comment-delete&id=<?= $c['id'] ?>"
                      onsubmit="return confirm('Delete comment?')" style="display:inline;">
                  <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">
                  <button class="btn btn-danger btn-sm" style="padding:3px 8px;font-size:0.75rem;">
                    <i class="bi bi-trash"></i>
                  </button>
                </form>
                <?php endif; ?>
              </div>
              <div class="comment-content"><?= nl2br(e($c['content'])) ?></div>
            </div>
            <?php endforeach; ?>
          <?php endif; ?>

          <!-- Add Comment -->
          <?php if ($currentUser): ?>
          <div class="card mt-3">
            <div class="card-body">
              <h4 style="font-size:0.9rem;font-weight:700;margin-bottom:14px;">Add a Comment</h4>
              <form method="POST" action="<?= BASE_URL ?>/?page=comment&submission_id=<?= $submission['id'] ?>">
                <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">
                <div class="form-group" style="margin-bottom:12px;">
                  <textarea name="content" class="form-control" rows="3"
                            placeholder="Share your thoughts..." required></textarea>
                </div>
                <button type="submit" class="btn btn-primary btn-sm">
                  <i class="bi bi-send"></i> Post Comment
                </button>
              </form>
            </div>
          </div>
          <?php else: ?>
          <div style="text-align:center;padding:20px;color:var(--text-muted);font-size:0.875rem;">
            <a href="<?= BASE_URL ?>/?page=login">Sign in</a> to leave a comment.
          </div>
          <?php endif; ?>
        </div>
      </div>

      <!-- ── Sidebar ────────────────────────────────────────── -->
      <div class="col-lg-4">
        <div class="card animate-in" style="position:sticky;top:90px;">
          <div class="card-body">
            <h4 style="font-size:0.875rem;text-transform:uppercase;letter-spacing:0.08em;color:var(--text-muted);margin-bottom:16px;">Challenge</h4>
            <a href="<?= BASE_URL ?>/?page=challenge&id=<?= $submission['challenge_id'] ?>"
               style="font-size:1rem;font-weight:700;color:var(--text-primary);display:block;margin-bottom:16px;line-height:1.4;">
              <?= e($submission['challenge_title']) ?>
            </a>
            <a href="<?= BASE_URL ?>/?page=challenge&id=<?= $submission['challenge_id'] ?>"
               class="btn btn-ghost w-100 btn-sm">
              <i class="bi bi-arrow-left"></i> Back to Challenge
            </a>
          </div>
        </div>
      </div>

    </div>
  </div>
</div>

<script>
async function handleVote(e) {
  e.preventDefault();
  const btn   = document.getElementById('vote-btn');
  const count = document.getElementById('vote-count');
  try {
    const res  = await fetch('<?= BASE_URL ?>/?page=vote&submission_id=<?= $submission['id'] ?>', {
      headers: { 'X-Requested-With': 'XMLHttpRequest' }
    });
    const data = await res.json();
    count.textContent = data.count;
    if (data.action === 'added') {
      btn.classList.add('voted');
      btn.querySelector('i').className = 'bi bi-star-fill';
      btn.lastChild.textContent = ' Voted';
    } else {
      btn.classList.remove('voted');
      btn.querySelector('i').className = 'bi bi-star';
      btn.lastChild.textContent = ' Vote';
    }
  } catch(err) { window.location = btn.href; }
  return false;
}
</script>

<?php require BASE_PATH . '/app/views/partials/footer.php'; ?>
