-- ═══════════════════════════════════════════════════════════
-- ChallengeHub — Database Script
-- Database: challengehub2
-- ═══════════════════════════════════════════════════════════

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

-- ── Table: users ──────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `users` (
  `id`         INT            NOT NULL AUTO_INCREMENT,
  `name`       VARCHAR(100)   NOT NULL,
  `email`      VARCHAR(150)   NOT NULL,
  `password`   VARCHAR(255)   NOT NULL,
  `created_at` DATETIME       NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── Table: challenges ────────────────────────────────────
CREATE TABLE IF NOT EXISTS `challenges` (
  `id`          INT           NOT NULL AUTO_INCREMENT,
  `user_id`     INT           NOT NULL,
  `title`       VARCHAR(200)  NOT NULL,
  `description` TEXT          NOT NULL,
  `category`    VARCHAR(100)  NOT NULL,
  `deadline`    DATE          NOT NULL,
  `image`       VARCHAR(255)  NULL DEFAULT NULL,
  `created_at`  DATETIME      NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── Table: submissions ───────────────────────────────────
CREATE TABLE IF NOT EXISTS `submissions` (
  `id`           INT          NOT NULL AUTO_INCREMENT,
  `challenge_id` INT          NOT NULL,
  `user_id`      INT          NOT NULL,
  `description`  TEXT         NOT NULL,
  `image`        VARCHAR(255) NULL DEFAULT NULL,
  `created_at`   DATETIME     NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `challenge_id` (`challenge_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── Table: comments ──────────────────────────────────────
CREATE TABLE IF NOT EXISTS `comments` (
  `id`            INT  NOT NULL AUTO_INCREMENT,
  `submission_id` INT  NOT NULL,
  `user_id`       INT  NOT NULL,
  `content`       TEXT NOT NULL,
  `created_at`    DATETIME NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `submission_id` (`submission_id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── Table: votes ──────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `votes` (
  `id`            INT      NOT NULL AUTO_INCREMENT,
  `submission_id` INT      NOT NULL,
  `user_id`       INT      NOT NULL,
  `created_at`    DATETIME NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_vote` (`submission_id`, `user_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
-- ==================== TABLE BADGES ====================

CREATE TABLE badges (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    description VARCHAR(255) NOT NULL,
    icone VARCHAR(10) NOT NULL,
    condition_type VARCHAR(50) NOT NULL
);

-- ==================== TABLE USER_BADGES ====================

CREATE TABLE user_badges (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    badge_id INT NOT NULL,
    date_obtenu DATETIME DEFAULT NOW(),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (badge_id) REFERENCES badges(id) ON DELETE CASCADE,
    UNIQUE KEY unique_user_badge (user_id, badge_id)
);

-- ==================== TABLE NOTIFICATIONS ====================

CREATE TABLE notifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    message VARCHAR(255) NOT NULL,
    lu TINYINT(1) DEFAULT 0,
    date_creation DATETIME DEFAULT NOW(),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- ==================== INSERTION DES BADGES ====================

INSERT INTO badges (nom, description, icone, condition_type) VALUES
('Débutant',  'Première soumission effectuée',     '🥉', 'submissions'),
('Actif',     '5 soumissions effectuées',           '🥈', 'submissions'),
('Expert',    '10 soumissions effectuées',          '🥇', 'submissions'),
('Votant',    '10 votes donnés à la communauté',    '👍', 'votes'),
('Champion',  'Gagner un challenge (top vote)',     '🏆', 'challenges');

-- ════════════════════════════════════════════════════════════
-- SEED DATA — 3 E-Commerce Challenges (as required)
-- ════════════════════════════════════════════════════════════

-- Demo admin user (password: Admin1234)
INSERT INTO `users` (`name`, `email`, `password`) VALUES
('Admin Hub', 'admin@challengehub.com',
 '$2y$12$eImiTXuWVxfM37uY4JANjQ.k9.NqW0JK1cAq2CnJ9DBZR9XN9pK4e');

-- ── Challenge 1: Product Page Design ────────────────────
INSERT INTO `challenges` (`user_id`, `title`, `description`, `category`, `deadline`) VALUES
(1,
 'Design the Ultimate Product Page for a Luxury Skincare Brand',
 'Your mission: design or describe in detail the ideal product page layout for a high-end skincare brand. Consider visual hierarchy, trust signals, conversion elements, social proof placement, and mobile experience. What makes a luxury product page convert while maintaining exclusivity?',
 'UX Design',
 DATE_ADD(CURDATE(), INTERVAL 14 DAY)
);

-- ── Challenge 2: Cart Abandonment Strategy ───────────────
INSERT INTO `challenges` (`user_id`, `title`, `description`, `category`, `deadline`) VALUES
(1,
 'Build the Perfect Cart Abandonment Recovery Strategy',
 'E-commerce stores lose an average of 70% of potential sales to cart abandonment. Your challenge: propose a complete, multi-channel cart abandonment recovery strategy for a mid-size fashion e-commerce brand. Include email sequences, retargeting tactics, on-site recovery tools, and timing recommendations. Be specific and data-driven.',
 'Marketing',
 DATE_ADD(CURDATE(), INTERVAL 21 DAY)
);

-- ── Challenge 3: E-Commerce Branding ────────────────────
INSERT INTO `challenges` (`user_id`, `title`, `description`, `category`, `deadline`) VALUES
(1,
 'Rebrand a Generic Online Store into a Memorable D2C Brand',
 'Take a fictional generic online electronics store called "TechStore24" and transform it into a compelling Direct-to-Consumer brand. Define the brand identity: name, positioning, tone of voice, visual direction, target audience, and a tagline. Explain how the new brand identity would be reflected across the website, packaging, and social media.',
 'Branding',
 DATE_ADD(CURDATE(), INTERVAL 30 DAY)
);
