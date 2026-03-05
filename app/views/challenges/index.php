<?php
$pageTitle = 'Challenges';
require BASE_PATH . '/app/views/partials/header.php';

$ecommerceCategories = [
  'Product Launch', 'UX Design', 'Marketing', 'Branding',
  'E-commerce Strategy', 'Social Media', 'SEO', 'Analytics',
  'Customer Retention', 'Conversion Optimization'
];

$catIcons = [
  'Product Launch' => '🚀', 'UX Design' => '🎨', 'Marketing' => '📣',
  'Branding' => '💎', 'E-commerce Strategy' => '🛒', 'Social Media' => '📱',
  'SEO' => '🔍', 'Analytics' => '📊', 'Customer Retention' => '🤝',
  'Conversion Optimization' => '⚡',
];
?>

<div class="page-wrapper">
  <div class="container">

    <!-- ── Page Header ──────────────────────────────────────── -->
    <div class="section-header animate-in">
      <div>
        <h1 class="section-title">🏆 All Challenges</h1>
        <p class="section-subtitle">
          <?= count($challenges) ?> e-commerce challenge<?= count($challenges) !== 1 ? 's' : '' ?> available
        </p>
      </div>
      <?php if ($currentUser): ?>
      <a href="<?= BASE_URL ?>/?page=challenge-create" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> New Challenge
      </a>
      <?php endif; ?>
    </div>

    <!-- ── Filter Bar ────────────────────────────────────────── -->
    <form method="GET" action="<?= BASE_URL ?>/" class="filter-bar animate-in">
      <input type="hidden" name="page" value="challenges">

      <div style="position:relative;flex:1;min-width:200px;">
        <i class="bi bi-search" style="position:absolute;left:14px;top:50%;transform:translateY(-50%);color:var(--text-muted);font-size:0.9rem;"></i>
        <input type="text" name="search" class="form-control" placeholder="Search challenges..."
               value="<?= e($_GET['search'] ?? '') ?>"
               style="padding-left:40px;">
      </div>

      <select name="category" class="form-control" style="width:200px;">
        <option value="">All Categories</option>
        <?php foreach ($ecommerceCategories as $cat): ?>
          <option value="<?= e($cat) ?>" <?= ($_GET['category'] ?? '') === $cat ? 'selected' : '' ?>>
            <?= e($cat) ?>
          </option>
        <?php endforeach; ?>
      </select>

      <select name="sort" class="form-control" style="width:160px;">
        <option value="date"    <?= ($_GET['sort'] ?? 'date') === 'date'    ? 'selected' : '' ?>>Latest First</option>
        <option value="popular" <?= ($_GET['sort'] ?? '') === 'popular' ? 'selected' : '' ?>>Most Popular</option>
      </select>

      <button type="submit" class="btn btn-primary">
        <i class="bi bi-funnel"></i> Filter
      </button>

      <?php if (!empty($_GET['search']) || !empty($_GET['category'])): ?>
      <a href="<?= BASE_URL ?>/?page=challenges" class="btn btn-ghost">
        <i class="bi bi-x"></i> Clear
      </a>
      <?php endif; ?>
    </form>

    <!-- ── Challenge Grid ────────────────────────────────────── -->
    <?php if (empty($challenges)): ?>
      <div class="empty-state animate-in">
        <div class="empty-state-icon">🔍</div>
        <h3>No challenges found</h3>
        <p>Try adjusting your search or filters, or create a new challenge.</p>
        <?php if ($currentUser): ?>
          <a href="<?= BASE_URL ?>/?page=challenge-create" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Create Challenge
          </a>
        <?php endif; ?>
      </div>
    <?php else: ?>
      <div class="grid grid-3">
        <?php foreach ($challenges as $i => $ch):
          $submissionCount = (int)$ch['submission_count'];
          $pct     = min(100, ($submissionCount / MAX_SUBMISSIONS_PER_CHALLENGE) * 100);
          $daysLeft = ceil((strtotime($ch['deadline']) - time()) / 86400);
          $dlClass  = $daysLeft < 3 ? 'urgent' : ($daysLeft < 7 ? 'soon' : 'ok');
          $icon     = $catIcons[$ch['category']] ?? '🎯';
        ?>
        <div class="challenge-card animate-in">
          <?php if ($ch['image']): ?>
            <img src="<?= UPLOAD_URL . e($ch['image']) ?>" alt="<?= e($ch['title']) ?>" class="challenge-card-image">
          <?php else: ?>
            <div class="challenge-card-image-placeholder"><?= $icon ?></div>
          <?php endif; ?>

          <div class="challenge-card-body">
            <div class="challenge-card-meta">
              <span class="badge badge-cyan"><?= e($ch['category']) ?></span>
              <?php if ($submissionCount >= MAX_SUBMISSIONS_PER_CHALLENGE): ?>
                <span class="badge badge-red"><i class="bi bi-lock-fill"></i> Full</span>
              <?php elseif ($daysLeft <= 0): ?>
                <span class="badge badge-red">Ended</span>
              <?php endif; ?>
            </div>

            <h3 class="challenge-card-title">
              <a href="<?= BASE_URL ?>/?page=challenge&id=<?= $ch['id'] ?>" style="color:inherit;">
                <?= e($ch['title']) ?>
              </a>
            </h3>
            <p class="challenge-card-desc"><?= e($ch['description']) ?></p>

            <!-- Progress -->
            <div style="margin-bottom:12px;">
              <div class="d-flex justify-between" style="font-size:0.75rem;color:var(--text-muted);margin-bottom:4px;">
                <span style="display:flex;align-items:center;gap:4px;">
                  <i class="bi bi-people-fill" style="color:var(--accent-cyan);"></i>
                  <?= $submissionCount ?>/<?= MAX_SUBMISSIONS_PER_CHALLENGE ?> participants
                </span>
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
                Explore <i class="bi bi-arrow-right"></i>
              </a>
            </div>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>

  </div>
</div>

<?php require BASE_PATH . '/app/views/partials/footer.php'; ?>
