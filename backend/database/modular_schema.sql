-- LCBAnian Modular Database Schema (MySQL 8+)
-- Import this file in MySQL Workbench or via CLI
-- Compatible with existing Laravel models

SET NAMES utf8mb4;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;

CREATE DATABASE IF NOT EXISTS `lcbaian_db` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `lcbaian_db`;

-- Drop existing tables if present (order matters when FKs exist)
DROP TABLE IF EXISTS `admin_logs`;
DROP TABLE IF EXISTS `applications`;
DROP TABLE IF EXISTS `job_posts`;
DROP TABLE IF EXISTS `likes`;
DROP TABLE IF EXISTS `comments`;
DROP TABLE IF EXISTS `posts`;
DROP TABLE IF EXISTS `alumni_badges`;
DROP TABLE IF EXISTS `badges`;
DROP TABLE IF EXISTS `messages`;
DROP TABLE IF EXISTS `community_members`;
DROP TABLE IF EXISTS `communities`;
DROP TABLE IF EXISTS `mentorships`;
DROP TABLE IF EXISTS `events`;
DROP TABLE IF EXISTS `users`;

SET foreign_key_checks = 1;

-- 1. Users Table (Alumni, Admins, Mentors)
CREATE TABLE `users` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `first_name` VARCHAR(50) NOT NULL,
  `last_name` VARCHAR(50) NOT NULL,
  `email` VARCHAR(100) NOT NULL,
  `password` VARCHAR(255) NOT NULL,
  `role` ENUM('alumni', 'mentor', 'admin') NOT NULL DEFAULT 'alumni',
  `profile_picture` VARCHAR(255) NULL,
  `headline` VARCHAR(100) NULL,
  `bio` TEXT NULL,
  `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`),
  KEY `users_role_idx` (`role`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. Events Table
CREATE TABLE `events` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` VARCHAR(100) NOT NULL,
  `description` TEXT NOT NULL,
  `start_date` DATETIME NOT NULL,
  `end_date` DATETIME NOT NULL,
  `location` VARCHAR(255) NOT NULL,
  `created_by` BIGINT UNSIGNED NOT NULL,
  `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `events_created_by_idx` (`created_by`),
  KEY `events_start_date_idx` (`start_date`),
  CONSTRAINT `events_created_by_fk` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. Mentorship Table
CREATE TABLE `mentorships` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `mentor_id` BIGINT UNSIGNED NOT NULL,
  `mentee_id` BIGINT UNSIGNED NOT NULL,
  `goal` VARCHAR(255) NULL,
  `preferred_slot` DATETIME NULL,
  `status` ENUM('pending', 'approved', 'declined', 'completed') NOT NULL DEFAULT 'pending',
  `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `mentorships_mentor_idx` (`mentor_id`),
  KEY `mentorships_mentee_idx` (`mentee_id`),
  KEY `mentorships_status_idx` (`status`),
  CONSTRAINT `mentorships_mentor_fk` FOREIGN KEY (`mentor_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `mentorships_mentee_fk` FOREIGN KEY (`mentee_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4. Communities Table
CREATE TABLE `communities` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(100) NOT NULL,
  `description` TEXT NOT NULL,
  `created_by` BIGINT UNSIGNED NOT NULL,
  `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `communities_created_by_idx` (`created_by`),
  CONSTRAINT `communities_created_by_fk` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 5. Community Members Table (Many-to-Many)
CREATE TABLE `community_members` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `community_id` BIGINT UNSIGNED NOT NULL,
  `user_id` BIGINT UNSIGNED NOT NULL,
  `joined_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `community_members_unique` (`community_id`, `user_id`),
  KEY `community_members_user_idx` (`user_id`),
  CONSTRAINT `community_members_community_fk` FOREIGN KEY (`community_id`) REFERENCES `communities` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `community_members_user_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 6. Messages Table
CREATE TABLE `messages` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `sender_id` BIGINT UNSIGNED NOT NULL,
  `receiver_id` BIGINT UNSIGNED NOT NULL,
  `content` TEXT NOT NULL,
  `is_read` BOOLEAN NOT NULL DEFAULT FALSE,
  `sent_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `messages_sender_idx` (`sender_id`),
  KEY `messages_receiver_idx` (`receiver_id`),
  KEY `messages_sent_at_idx` (`sent_at`),
  CONSTRAINT `messages_sender_fk` FOREIGN KEY (`sender_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `messages_receiver_fk` FOREIGN KEY (`receiver_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 7. Badges Table
CREATE TABLE `badges` (
  `badge_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(100) NOT NULL,
  `description` TEXT NULL,
  `icon_path` VARCHAR(255) NULL,
  `criteria` VARCHAR(255) NULL,
  PRIMARY KEY (`badge_id`),
  UNIQUE KEY `badges_name_unique` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 8. Alumni Badges Table
CREATE TABLE `alumni_badges` (
  `alumni_badge_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` BIGINT UNSIGNED NOT NULL,
  `badge_id` INT UNSIGNED NOT NULL,
  `date_awarded` DATETIME NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`alumni_badge_id`),
  UNIQUE KEY `alumni_badges_unique` (`user_id`, `badge_id`),
  KEY `alumni_badges_badge_idx` (`badge_id`),
  CONSTRAINT `alumni_badges_user_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `alumni_badges_badge_fk` FOREIGN KEY (`badge_id`) REFERENCES `badges` (`badge_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 9. Posts Table
CREATE TABLE `posts` (
  `post_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` BIGINT UNSIGNED NOT NULL,
  `content` TEXT NOT NULL,
  `image_path` VARCHAR(255) NULL,
  `created_at` DATETIME NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`post_id`),
  KEY `posts_user_idx` (`user_id`),
  KEY `posts_created_at_idx` (`created_at`),
  CONSTRAINT `posts_user_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 10. Comments Table
CREATE TABLE `comments` (
  `comment_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `post_id` INT UNSIGNED NOT NULL,
  `user_id` BIGINT UNSIGNED NOT NULL,
  `content` TEXT NOT NULL,
  `created_at` DATETIME NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`comment_id`),
  KEY `comments_post_idx` (`post_id`),
  KEY `comments_user_idx` (`user_id`),
  KEY `comments_created_at_idx` (`created_at`),
  CONSTRAINT `comments_post_fk` FOREIGN KEY (`post_id`) REFERENCES `posts` (`post_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `comments_user_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 11. Likes Table
CREATE TABLE `likes` (
  `like_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `post_id` INT UNSIGNED NOT NULL,
  `user_id` BIGINT UNSIGNED NOT NULL,
  `created_at` DATETIME NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`like_id`),
  UNIQUE KEY `likes_unique` (`post_id`, `user_id`),
  KEY `likes_user_idx` (`user_id`),
  CONSTRAINT `likes_post_fk` FOREIGN KEY (`post_id`) REFERENCES `posts` (`post_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `likes_user_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 12. Job Posts Table
CREATE TABLE `job_posts` (
  `job_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `posted_by_admin` BOOLEAN NOT NULL DEFAULT FALSE,
  `user_id` BIGINT UNSIGNED NULL,
  `title` VARCHAR(200) NOT NULL,
  `description` TEXT NOT NULL,
  `company_name` VARCHAR(150) NULL,
  `location` VARCHAR(150) NULL,
  `created_at` DATETIME NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`job_id`),
  KEY `job_posts_user_idx` (`user_id`),
  KEY `job_posts_created_at_idx` (`created_at`),
  CONSTRAINT `job_posts_user_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 13. Applications Table
CREATE TABLE `applications` (
  `application_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `job_id` INT UNSIGNED NOT NULL,
  `user_id` BIGINT UNSIGNED NOT NULL,
  `resume_path` VARCHAR(255) NULL,
  `status` ENUM('pending', 'reviewed', 'accepted', 'rejected') NOT NULL DEFAULT 'pending',
  `applied_at` DATETIME NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`application_id`),
  UNIQUE KEY `applications_unique` (`job_id`, `user_id`),
  KEY `applications_user_idx` (`user_id`),
  KEY `applications_status_idx` (`status`),
  CONSTRAINT `applications_job_fk` FOREIGN KEY (`job_id`) REFERENCES `job_posts` (`job_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `applications_user_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 14. Admin Logs Table
CREATE TABLE `admin_logs` (
  `log_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` BIGINT UNSIGNED NOT NULL,
  `action` VARCHAR(255) NOT NULL,
  `created_at` DATETIME NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`log_id`),
  KEY `admin_logs_user_idx` (`user_id`),
  KEY `admin_logs_created_at_idx` (`created_at`),
  CONSTRAINT `admin_logs_user_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Enforce no self-mentorship via triggers (avoids MySQL 3823 limitation with CHECK)
DELIMITER $$
DROP TRIGGER IF EXISTS `mentorships_no_self_insert` $$
CREATE TRIGGER `mentorships_no_self_insert` BEFORE INSERT ON `mentorships`
FOR EACH ROW
BEGIN
  IF NEW.`mentor_id` = NEW.`mentee_id` THEN
    SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'mentor_id cannot equal mentee_id';
  END IF;
END $$

DROP TRIGGER IF EXISTS `mentorships_no_self_update` $$
CREATE TRIGGER `mentorships_no_self_update` BEFORE UPDATE ON `mentorships`
FOR EACH ROW
BEGIN
  IF NEW.`mentor_id` = NEW.`mentee_id` THEN
    SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'mentor_id cannot equal mentee_id';
  END IF;
END $$
DELIMITER ;

-- Insert sample data for testing
INSERT INTO `users` (`first_name`, `last_name`, `email`, `password`, `role`, `headline`, `bio`) VALUES
('John', 'Doe', 'admin@lcbaian.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 'LCBA Administrator', 'System administrator for LCBAian platform'),
('Jane', 'Smith', 'mentor@lcbaian.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'mentor', 'Senior Software Engineer', 'Experienced mentor in software development'),
('Bob', 'Johnson', 'alumni@lcbaian.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'alumni', 'Recent Graduate', 'Computer Science graduate looking for opportunities');

INSERT INTO `badges` (`name`, `description`, `criteria`) VALUES
('Early Adopter', 'Joined the platform in its early stages', 'Registered within first 100 users'),
('Top Mentor', 'Highly rated mentor by mentees', 'Average rating above 4.5 stars'),
('Active Job Seeker', 'Actively applying to job opportunities', 'Applied to 10+ jobs'),
('Community Contributor', 'Active participant in community discussions', 'Posted 20+ meaningful posts');

INSERT INTO `communities` (`name`, `description`, `created_by`) VALUES
('Software Engineering', 'Discussion group for software engineering topics', 1),
('Career Development', 'Tips and advice for career growth', 1),
('Alumni Network', 'General alumni networking and updates', 1);

INSERT INTO `job_posts` (`title`, `description`, `company_name`, `location`, `posted_by_admin`, `user_id`) VALUES
('Software Developer', 'Looking for a talented software developer to join our team', 'Tech Corp', 'Remote', TRUE, NULL),
('Data Analyst', 'Analyze data and provide insights for business decisions', 'Data Inc', 'New York', FALSE, 1);

-- Insert sample community memberships
INSERT INTO `community_members` (`community_id`, `user_id`) VALUES
(1, 2), (1, 3), (2, 2), (2, 3), (3, 1), (3, 2), (3, 3);
