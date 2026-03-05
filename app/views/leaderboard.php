<?php
$pageTitle = 'Leaderboard';
require BASE_PATH . '/app/views/partials/header.php';
?>

<div class="page-wrapper">
  <div class="container" style="max-width:800px;">

    <div class="section-header animate-in">
      <div>
        <h1 class="section-title">🏅 Leaderboard</h1>
        <p class="section-subtitle">Top-rated submissions across all e-commerce challenges</p>
      </div>
    </div>

    <!-- Top 3 Podium -->
    <?php if (count($leaderboard) >= 3): ?>
    <div class="row g-3 mb-5 animate-in" style="align-items:flex-end;">

      <!-- 2nd Place -->
      <div class="col-4">
        <div class="card" style="text-align:center;padding:20px 16px;border-color:rgba(148,163,184,0.4);">
          <div style="font-size:2.5rem;margin-bottom:8px;">🥈</div>
          <div class="avatar" style="margin:0 auto 8px;"><?= strtoupper(substr($leaderboard[1]['author_name'],0,1)) ?></div>
          <div style="font-weight:700;font-size:0.875rem;"><?= e($leaderboard[1]['author_name']) ?></div>
          <div style="font-size:0.75rem;color:var(--text-muted);margin:4px 0 8px;">
            <?= e(substr($leaderboard[1]['challenge_title'],0,30)) ?>…
          </div>
          <div style="color:#94a3b8;font-weight:800;font-family:var(--font-display);font-size:1.3rem;">
            ⭐ <?= $leaderboard[1]['vote_count'] ?>
          </div>
        </div>
      </div>

      <!-- 1st Place -->
      <div class="col-4">
        <div class="card" style="text-align:center;padding:28px 16px;border-color:rgba(255,179,0,0.5);box-shadow:0 0 30px rgba(255,179,0,0.1);">
          <div style="font-size:3rem;margin-bottom:8px;">🥇</div>
          <div class="avatar avatar-lg" style="margin:0 auto 10px;background:linear-gradient(135deg,var(--accent-amber),#ff9500);">
            <?= strtoupper(substr($leaderboard[0]['author_name'],0,1)) ?>
          </div>
          <div style="font-weight:700;"><?= e($leaderboard[0]['author_name']) ?></div>
          <div style="font-size:0.75rem;color:var(--text-muted);margin:4px 0 10px;">
            <?= e(substr($leaderboard[0]['challenge_title'],0,30)) ?>…
          </div>
          <div style="color:var(--accent-amber);font-weight:800;font-family:var(--font-display);font-size:1.6rem;">
            ⭐ <?= $leaderboard[0]['vote_count'] ?>
          </div>
        </div>
      </div>

      <!-- 3rd Place -->
      <div class="col-4">
        <div class="card" style="text-align:center;padding:16px;border-color:rgba(205,127,50,0.4);">
          <div style="font-size:2rem;margin-bottom:8px;">🥉</div>
          <div class="avatar" style="margin:0 auto 8px;"><?= strtoupper(substr($leaderboard[2]['author_name'],0,1)) ?></div>
          <div style="font-weight:700;font-size:0.875rem;"><?= e($leaderboard[2]['author_name']) ?></div>
          <div style="font-size:0.75rem;color:var(--text-muted);margin:4px 0 8px;">
            <?= e(substr($leaderboard[2]['challenge_title'],0,30)) ?>…
          </div>
          <div style="color:#cd7f32;font-weight:800;font-family:var(--font-display);font-size:1.3rem;">
            ⭐ <?= $leaderboard[2]['vote_count'] ?>
          </div>
        </div>
      </div>
    </div>
    <?php endif; ?>

    <!-- Full List -->
    <div class="animate-in">
      <h2 style="font-size:1rem;font-weight:700;color:var(--text-muted);text-transform:uppercase;letter-spacing:0.08em;margin-bottom:16px;">
        All Submissions
      </h2>

      <?php if (empty($leaderboard)): ?>
        <div class="empty-state">
          <div class="empty-state-icon">🏅</div>
          <h3>No submissions yet</h3>
          <p>Participate in challenges to appear on the leaderboard!</p>
          <a href="<?= BASE_URL ?>/?page=challenges" class="btn btn-primary">Browse Challenges</a>
        </div>
      <?php else: ?>
        <?php foreach ($leaderboard as $i => $sub): ?>
        <div class="leaderboard-row animate-in">
          <div class="leaderboard-rank rank-<?= $i < 3 ? ($i+1) : 'other' ?>">
            <?= $i === 0 ? '🥇' : ($i === 1 ? '🥈' : ($i === 2 ? '🥉' : '#'.($i+1))) ?>
          </div>

          <div class="avatar"><?= strtoupper(substr($sub['author_name'],0,1)) ?></div>

          <div style="flex:1;min-width:0;">
            <div style="font-weight:600;font-size:0.9rem;color:var(--text-primary);">
              <a href="<?= BASE_URL ?>/?page=submission&id=<?= $sub['id'] ?>"
                 style="color:inherit;">
                <?= e(substr($sub['description'],0,90)) ?>…
              </a>
            </div>
            <div style="font-size:0.75rem;color:var(--text-muted);margin-top:2px;">
              <span><?= e($sub['author_name']) ?></span>
              <span style="margin:0 6px;opacity:0.4;">·</span>
              <span><i class="bi bi-trophy" style="color:var(--accent-cyan);"></i> <?= e($sub['challenge_title']) ?></span>
              <?php if (!empty($sub['category'])): ?>
              <span style="margin:0 6px;opacity:0.4;">·</span>
              <span class="badge badge-cyan" style="font-size:0.68rem;"><?= e($sub['category']) ?></span>
              <?php endif; ?>
            </div>
          </div>

          <div style="display:flex;align-items:center;gap:6px;color:var(--accent-amber);font-weight:800;font-family:var(--font-display);font-size:1.1rem;white-space:nowrap;">
            ⭐ <?= $sub['vote_count'] ?>
          </div>

          <a href="<?= BASE_URL ?>/?page=submission&id=<?= $sub['id'] ?>" class="btn btn-ghost btn-sm">
            View <i class="bi bi-arrow-right"></i>
          </a>
        </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>

  </div>
</div>

<?php require BASE_PATH . '/app/views/partials/footer.php'; ?>
