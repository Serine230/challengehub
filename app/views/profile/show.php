<?php
$pageTitle = 'My Profile';
require BASE_PATH . '/app/views/partials/header.php';
?>

<div class="page-wrapper">
  <div class="container">

    <!-- ── Profile Header ─────────────────────────────────── -->
    <div class="profile-header animate-in">
      <div class="avatar avatar-lg">
        <?= strtoupper(substr($user['name'],0,1)) ?>
      </div>
      <div>
        <h1 style="font-size:1.6rem;margin-bottom:4px;"><?= e($user['name']) ?></h1>
        <p style="color:var(--text-muted);font-size:0.875rem;margin:0;">
          <i class="bi bi-envelope"></i> <?= e($user['email']) ?>
        </p>
        <p style="color:var(--text-muted);font-size:0.78rem;margin-top:4px;">
          <i class="bi bi-calendar3"></i> Member since <?= date('M Y', strtotime($user['created_at'])) ?>
        </p>
      </div>

      <div class="profile-stats">
        <div class="stat-item">
          <div class="stat-value"><?= $stats['challenges'] ?></div>
          <div class="stat-label">Challenges</div>
        </div>
        <div class="stat-item">
          <div class="stat-value"><?= $stats['submissions'] ?></div>
          <div class="stat-label">Submissions</div>
        </div>
        <div class="stat-item">
          <div class="stat-value"><?= $stats['votes'] ?></div>
          <div class="stat-label">Votes Earned</div>
        </div>
      </div>

      <div style="margin-left:16px;display:flex;gap:8px;flex-wrap:wrap;">
        <a href="<?= BASE_URL ?>/?page=profile-edit" class="btn btn-secondary btn-sm">
          <i class="bi bi-pencil"></i> Edit Profile
        </a>
      </div>
    </div>

    <!-- ── Tabs ──────────────────────────────────────────── -->
    <div class="tabs animate-in">
      <a href="#challenges-tab" class="tab active" onclick="showTab(event,'challenges-tab')">
        <i class="bi bi-trophy"></i> My Challenges (<?= count($challenges) ?>)
      </a>
      <a href="#submissions-tab" class="tab" onclick="showTab(event,'submissions-tab')">
        <i class="bi bi-send"></i> My Submissions (<?= count($submissions) ?>)
      </a>
      <a href="#settings-tab" class="tab" onclick="showTab(event,'settings-tab')">
        <i class="bi bi-gear"></i> Settings
      </a>
    </div>

    <!-- My Challenges Tab -->
    <div id="challenges-tab" class="tab-content">
      <?php if (empty($challenges)): ?>
        <div class="empty-state">
          <div class="empty-state-icon">🏆</div>
          <h3>No challenges yet</h3>
          <p>Create your first e-commerce challenge!</p>
          <a href="<?= BASE_URL ?>/?page=challenge-create" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Create Challenge
          </a>
        </div>
      <?php else: ?>
        <div class="grid grid-3">
          <?php foreach ($challenges as $ch):
            $sub = (int)$ch['submission_count'];
            $pct = min(100, ($sub / MAX_SUBMISSIONS_PER_CHALLENGE) * 100);
          ?>
          <div class="challenge-card animate-in">
            <div class="challenge-card-image-placeholder" style="height:100px;">🏆</div>
            <div class="challenge-card-body">
              <div class="challenge-card-meta">
                <span class="badge badge-cyan"><?= e($ch['category']) ?></span>
              </div>
              <h3 class="challenge-card-title" style="font-size:0.95rem;">
                <a href="<?= BASE_URL ?>/?page=challenge&id=<?= $ch['id'] ?>" style="color:inherit;">
                  <?= e($ch['title']) ?>
                </a>
              </h3>
              <div style="font-size:0.75rem;color:var(--text-muted);margin-bottom:8px;">
                <?= $sub ?>/<?= MAX_SUBMISSIONS_PER_CHALLENGE ?> participants
              </div>
              <div class="progress-bar-wrap" style="margin-bottom:12px;">
                <div class="progress-bar-fill" style="width:<?= $pct ?>%"></div>
              </div>
              <div style="display:flex;gap:8px;">
                <a href="<?= BASE_URL ?>/?page=challenge&id=<?= $ch['id'] ?>" class="btn btn-ghost btn-sm flex-1">
                  View
                </a>
                <a href="<?= BASE_URL ?>/?page=challenge-edit&id=<?= $ch['id'] ?>" class="btn btn-secondary btn-sm">
                  <i class="bi bi-pencil"></i>
                </a>
                <form method="POST" action="<?= BASE_URL ?>/?page=challenge-delete&id=<?= $ch['id'] ?>"
                      onsubmit="return confirm('Delete this challenge?')" style="display:inline;">
                  <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">
                  <button class="btn btn-danger btn-sm"><i class="bi bi-trash"></i></button>
                </form>
              </div>
            </div>
          </div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </div>

    <!-- My Submissions Tab -->
    <div id="submissions-tab" class="tab-content" style="display:none;">
      <?php if (empty($submissions)): ?>
        <div class="empty-state">
          <div class="empty-state-icon">📝</div>
          <h3>No submissions yet</h3>
          <p>Participate in challenges to see your submissions here.</p>
          <a href="<?= BASE_URL ?>/?page=challenges" class="btn btn-primary">Browse Challenges</a>
        </div>
      <?php else: ?>
        <div style="display:flex;flex-direction:column;gap:12px;">
          <?php foreach ($submissions as $sub): ?>
          <div class="submission-card animate-in" style="display:flex;align-items:center;gap:16px;">
            <div style="flex:1;min-width:0;">
              <div style="font-size:0.78rem;color:var(--text-muted);margin-bottom:4px;">
                <i class="bi bi-trophy" style="color:var(--accent-cyan);"></i>
                <?= e($sub['challenge_title']) ?>
              </div>
              <div style="font-size:0.9rem;color:var(--text-secondary);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                <?= e(substr($sub['description'],0,100)) ?>…
              </div>
            </div>
            <div style="display:flex;align-items:center;gap:6px;color:var(--accent-amber);font-weight:700;white-space:nowrap;">
              <i class="bi bi-star-fill"></i> <?= $sub['vote_count'] ?>
            </div>
            <div style="display:flex;gap:8px;white-space:nowrap;">
              <a href="<?= BASE_URL ?>/?page=submission&id=<?= $sub['id'] ?>" class="btn btn-ghost btn-sm">View</a>
              <a href="<?= BASE_URL ?>/?page=submission-edit&id=<?= $sub['id'] ?>" class="btn btn-secondary btn-sm">
                <i class="bi bi-pencil"></i>
              </a>
              <form method="POST" action="<?= BASE_URL ?>/?page=submission-delete&id=<?= $sub['id'] ?>"
                    onsubmit="return confirm('Delete submission?')" style="display:inline;">
                <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">
                <button class="btn btn-danger btn-sm"><i class="bi bi-trash"></i></button>
              </form>
            </div>
          </div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </div>

    <!-- Settings Tab -->
    <div id="settings-tab" class="tab-content" style="display:none;">
      <div class="card animate-in" style="max-width:560px;">
        <div class="card-body" style="padding:32px;">
          <h3 style="font-size:1rem;margin-bottom:20px;">
            <i class="bi bi-shield-exclamation" style="color:var(--accent-red);"></i>
            Danger Zone
          </h3>
          <p style="font-size:0.875rem;color:var(--text-muted);margin-bottom:20px;">
            Permanently delete your account and all associated data. This action cannot be undone.
          </p>
          <form method="POST" action="<?= BASE_URL ?>/?page=profile-delete"
                onsubmit="return confirm('Are you absolutely sure? This will permanently delete your account.')">
            <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">
            <button type="submit" class="btn btn-danger">
              <i class="bi bi-trash"></i> Delete My Account
            </button>
          </form>
        </div>
      </div>
    </div>

  </div>
</div>

<script>
function showTab(e, tabId) {
  e.preventDefault();
  document.querySelectorAll('.tab-content').forEach(t => t.style.display = 'none');
  document.querySelectorAll('.tab').forEach(t => t.classList.remove('active'));
  document.getElementById(tabId).style.display = 'block';
  e.target.classList.add('active');
}
</script>

<?php require BASE_PATH . '/app/views/partials/footer.php'; ?>
