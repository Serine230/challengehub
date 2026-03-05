<?php
$pageTitle = 'Home';
require BASE_PATH . '/app/views/partials/header.php';

// Category color map
$catColors = [
  'Product Launch'    => 'cyan',
  'UX Design'         => 'purple',
  'Marketing'         => 'amber',
  'Branding'          => 'green',
  'E-commerce Strategy' => 'cyan',
  'Social Media'      => 'purple',
];

function getCatBadge(string $cat): string {
  global $catColors;
  $color = $catColors[$cat] ?? 'default';
  return '<span class="badge badge-' . $color . '">' . htmlspecialchars($cat, ENT_QUOTES) . '</span>';
}

function getDeadlineClass(string $deadline): string {
  $days = (strtotime($deadline) - time()) / 86400;
  if ($days < 0)   return 'urgent';
  if ($days < 3)   return 'urgent';
  if ($days < 7)   return 'soon';
  return 'ok';
}
?>

<div class="page-wrapper">
  <div class="container">

    <!-- ── Hero ──────────────────────────────────────────────── -->
    <section class="hero animate-in">
      <div class="row align-items-center">
        <div class="col-lg-7">
          <div class="hero-title">
            Compete. Create.<br>
            <span class="gradient-text">Dominate E-Commerce.</span>
          </div>
          <p class="hero-subtitle">
            ChallengeHub is the collaborative arena where e-commerce professionals
            publish creative challenges, showcase solutions, and vote for the best ideas.
          </p>
          <div class="hero-actions">
            <?php if ($currentUser): ?>
              <a href="<?= BASE_URL ?>/?page=challenge-create" class="btn btn-primary btn-lg">
                <i class="bi bi-plus-circle"></i> Launch a Challenge
              </a>
              <a href="<?= BASE_URL ?>/?page=challenges" class="btn btn-ghost btn-lg">
                Browse All <i class="bi bi-arrow-right"></i>
              </a>
            <?php else: ?>
              <a href="<?= BASE_URL ?>/?page=register" class="btn btn-primary btn-lg">
                <i class="bi bi-rocket-takeoff"></i> Get Started Free
              </a>
              <a href="<?= BASE_URL ?>/?page=challenges" class="btn btn-ghost btn-lg">
                Explore Challenges <i class="bi bi-arrow-right"></i>
              </a>
            <?php endif; ?>
          </div>
        </div>
        <div class="col-lg-5 d-none d-lg-flex justify-content-end">
          <div style="position:relative;">
            <div style="width:280px;height:280px;border-radius:50%;background:radial-gradient(circle,rgba(0,212,255,0.08) 0%,transparent 70%);display:flex;align-items:center;justify-content:center;font-size:8rem;">🏆</div>
            <div style="position:absolute;top:20px;right:-10px;background:var(--bg-card);border:1px solid var(--border);border-radius:12px;padding:12px 16px;font-size:0.78rem;">
              <div style="color:var(--accent-green);font-weight:700;">+24 votes</div>
              <div style="color:var(--text-muted);">Best submission</div>
            </div>
            <div style="position:absolute;bottom:30px;left:-20px;background:var(--bg-card);border:1px solid var(--border);border-radius:12px;padding:12px 16px;font-size:0.78rem;">
              <div style="color:var(--accent-cyan);font-weight:700;">3 Challenges</div>
              <div style="color:var(--text-muted);">Active now</div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- ── Featured Challenges ───────────────────────────────── -->
    <section class="mt-4">
      <div class="section-header animate-in">
        <div>
          <h2 class="section-title">🔥 Featured Challenges</h2>
          <p class="section-subtitle">The most active e-commerce challenges right now</p>
        </div>
        <a href="<?= BASE_URL ?>/?page=challenges" class="btn btn-ghost btn-sm">
          View All <i class="bi bi-arrow-right"></i>
        </a>
      </div>

      <?php if (empty($featured)): ?>
        <div class="empty-state">
          <div class="empty-state-icon">🎯</div>
          <h3>No challenges yet</h3>
          <p>Be the first to create an e-commerce challenge!</p>
          <?php if ($currentUser): ?>
            <a href="<?= BASE_URL ?>/?page=challenge-create" class="btn btn-primary">Create First Challenge</a>
          <?php endif; ?>
        </div>
      <?php else: ?>
      <div class="grid grid-3">
        <?php foreach ($featured as $i => $ch): ?>
        <?php
          $submissionCount = (int)$ch['submission_count'];
          $pct = min(100, ($submissionCount / MAX_SUBMISSIONS_PER_CHALLENGE) * 100);
          $dlClass = getDeadlineClass($ch['deadline']);
          $daysLeft = ceil((strtotime($ch['deadline']) - time()) / 86400);
        ?>
        <div class="challenge-card animate-in">
          <?php if ($ch['image']): ?>
            <img src="<?= UPLOAD_URL . e($ch['image']) ?>" alt="<?= e($ch['title']) ?>" class="challenge-card-image">
          <?php else: ?>
            <div class="challenge-card-image-placeholder">
              <?= ['🛒','📦','🎯','💡','🚀','🛍️'][$i % 6] ?>
            </div>
          <?php endif; ?>

          <div class="challenge-card-body">
            <div class="challenge-card-meta">
              <?= getCatBadge($ch['category']) ?>
              <?php if ($submissionCount >= MAX_SUBMISSIONS_PER_CHALLENGE): ?>
                <span class="badge badge-red">Full</span>
              <?php endif; ?>
            </div>

            <h3 class="challenge-card-title">
              <a href="<?= BASE_URL ?>/?page=challenge&id=<?= $ch['id'] ?>" style="color:inherit;text-decoration:none;">
                <?= e($ch['title']) ?>
              </a>
            </h3>
            <p class="challenge-card-desc"><?= e($ch['description']) ?></p>

            <!-- Participants progress -->
            <div style="margin-bottom:12px;">
              <div class="d-flex justify-between" style="font-size:0.75rem;color:var(--text-muted);margin-bottom:4px;">
                <span><?= $submissionCount ?>/<?= MAX_SUBMISSIONS_PER_CHALLENGE ?> participants</span>
                <span class="deadline <?= $dlClass ?>">
                  <i class="bi bi-clock"></i>
                  <?= $daysLeft > 0 ? $daysLeft . 'd left' : 'Ended' ?>
                </span>
              </div>
              <div class="progress-bar-wrap">
                <div class="progress-bar-fill <?= $pct >= 100 ? 'full' : '' ?>" style="width:<?= $pct ?>%"></div>
              </div>
            </div>

            <div class="challenge-card-footer">
              <div style="display:flex;align-items:center;gap:8px;">
                <div class="avatar" style="width:26px;height:26px;font-size:0.7rem;">
                  <?= strtoupper(substr($ch['author_name'],0,1)) ?>
                </div>
                <span style="font-size:0.8rem;color:var(--text-muted);"><?= e($ch['author_name']) ?></span>
              </div>
              <a href="<?= BASE_URL ?>/?page=challenge&id=<?= $ch['id'] ?>" class="btn btn-primary btn-sm">
                View <i class="bi bi-arrow-right"></i>
              </a>
            </div>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
      <?php endif; ?>
    </section>

    <!-- ── Leaderboard Preview ───────────────────────────────── -->
    <?php if (!empty($leaderboard)): ?>
    <section class="mt-5">
      <div class="section-header animate-in">
        <div>
          <h2 class="section-title">🏅 Top Submissions</h2>
          <p class="section-subtitle">Best-voted submissions across all challenges</p>
        </div>
        <a href="<?= BASE_URL ?>/?page=leaderboard" class="btn btn-ghost btn-sm">
          Full Leaderboard <i class="bi bi-arrow-right"></i>
        </a>
      </div>

      <?php foreach ($leaderboard as $i => $sub): ?>
      <div class="leaderboard-row animate-in">
        <div class="leaderboard-rank rank-<?= $i < 3 ? ($i+1) : 'other' ?>">
          <?= $i === 0 ? '🥇' : ($i === 1 ? '🥈' : ($i === 2 ? '🥉' : '#'.($i+1))) ?>
        </div>
        <div class="avatar"><?= strtoupper(substr($sub['author_name'],0,1)) ?></div>
        <div style="flex:1;min-width:0;">
          <div style="font-weight:600;font-size:0.9rem;color:var(--text-primary);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
            <a href="<?= BASE_URL ?>/?page=submission&id=<?= $sub['id'] ?>" style="color:inherit;">
              <?= e(substr($sub['description'],0,80)) ?>…
            </a>
          </div>
          <div style="font-size:0.78rem;color:var(--text-muted);">
            <?= e($sub['author_name']) ?> · <?= e($sub['challenge_title']) ?>
          </div>
        </div>
        <div style="display:flex;align-items:center;gap:6px;color:var(--accent-amber);font-weight:700;font-size:0.9rem;">
          <i class="bi bi-star-fill"></i> <?= $sub['vote_count'] ?>
        </div>
      </div>
      <?php endforeach; ?>
    </section>
    <?php endif; ?>

    <!-- ── CTA ───────────────────────────────────────────────── -->
    <?php if (!$currentUser): ?>
    <section class="mt-5">
      <div class="card" style="background:linear-gradient(135deg,rgba(0,212,255,0.06),rgba(168,85,247,0.06));border-color:rgba(0,212,255,0.2);text-align:center;padding:48px 24px;">
        <h2 style="font-size:2rem;margin-bottom:12px;">Ready to challenge the best?</h2>
        <p style="color:var(--text-secondary);margin-bottom:24px;max-width:480px;margin-left:auto;margin-right:auto;">
          Join ChallengeHub today. Create your profile, submit your best e-commerce strategies, and earn votes from the community.
        </p>
        <a href="<?= BASE_URL ?>/?page=register" class="btn btn-primary btn-lg" style="margin:auto;">
          <i class="bi bi-rocket-takeoff"></i> Join ChallengeHub
        </a>
      </div>
    </section>
    <?php endif; ?>

  </div>
</div>

<?php require BASE_PATH . '/app/views/partials/footer.php'; ?>
