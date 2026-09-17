-- ============================================================
-- UCC Community Extension Services (CES) Management System
-- Database Schema
-- Version: 1.0.0
-- ============================================================

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+08:00";

CREATE DATABASE IF NOT EXISTS `ucc_ces_db`
  DEFAULT CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE `ucc_ces_db`;

-- ============================================================
-- TABLE: programs
-- UCC Academic Programs (Nursing, IT, Education, etc.)
-- ============================================================
CREATE TABLE `programs` (
  `id`         INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `code`       VARCHAR(20)  NOT NULL COMMENT 'e.g. BSN, BSIT, BSED',
  `name`       VARCHAR(100) NOT NULL COMMENT 'e.g. Bachelor of Science in Nursing',
  `department` VARCHAR(100) DEFAULT NULL,
  `is_active`  TINYINT(1)  NOT NULL DEFAULT 1,
  `created_at` TIMESTAMP   NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP   NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_programs_code` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE: users
-- All system users (admin, ces_head, program_head, faculty, student)
-- ============================================================
CREATE TABLE `users` (
  `id`           INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `program_id`   INT UNSIGNED DEFAULT NULL,
  `employee_id`  VARCHAR(50)  DEFAULT NULL COMMENT 'Employee or student ID number',
  `first_name`   VARCHAR(100) NOT NULL,
  `last_name`    VARCHAR(100) NOT NULL,
  `email`        VARCHAR(150) NOT NULL,
  `password`     VARCHAR(255) NOT NULL COMMENT 'bcrypt hashed',
  `role`         ENUM('admin','ces_head','program_head','faculty','student') NOT NULL DEFAULT 'student',
  `contact_no`   VARCHAR(20)  DEFAULT NULL,
  `avatar`       VARCHAR(255) DEFAULT NULL,
  `is_active`    TINYINT(1)  NOT NULL DEFAULT 1,
  `last_login`   TIMESTAMP   NULL DEFAULT NULL,
  `created_at`   TIMESTAMP   NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`   TIMESTAMP   NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_users_email` (`email`),
  KEY `fk_users_program` (`program_id`),
  CONSTRAINT `fk_users_program` FOREIGN KEY (`program_id`) REFERENCES `programs` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE: barangays
-- Adopted communities / barangays
-- ============================================================
CREATE TABLE `barangays` (
  `id`          INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name`        VARCHAR(100) NOT NULL COMMENT 'e.g. Barangay 96',
  `city`        VARCHAR(100) NOT NULL DEFAULT 'Manila',
  `contact_person` VARCHAR(150) DEFAULT NULL,
  `contact_no`  VARCHAR(20)  DEFAULT NULL,
  `address`     TEXT         DEFAULT NULL,
  `is_adopted`  TINYINT(1)  NOT NULL DEFAULT 1,
  `created_at`  TIMESTAMP   NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`  TIMESTAMP   NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE: community_needs_assessments
-- Community Needs Analysis conducted per barangay
-- ============================================================
CREATE TABLE `community_needs_assessments` (
  `id`             INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `barangay_id`    INT UNSIGNED NOT NULL,
  `conducted_by`   INT UNSIGNED NOT NULL COMMENT 'user_id of the one who conducted',
  `assessment_date` DATE        NOT NULL,
  `title`          VARCHAR(200) NOT NULL,
  `description`    TEXT         DEFAULT NULL,
  `status`         ENUM('draft','submitted','approved') NOT NULL DEFAULT 'draft',
  `approved_by`    INT UNSIGNED DEFAULT NULL,
  `approved_at`    TIMESTAMP   NULL DEFAULT NULL,
  `created_at`     TIMESTAMP   NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`     TIMESTAMP   NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_cna_barangay`    (`barangay_id`),
  KEY `fk_cna_conducted`   (`conducted_by`),
  KEY `fk_cna_approved`    (`approved_by`),
  CONSTRAINT `fk_cna_barangay`  FOREIGN KEY (`barangay_id`)  REFERENCES `barangays` (`id`),
  CONSTRAINT `fk_cna_conducted` FOREIGN KEY (`conducted_by`) REFERENCES `users` (`id`),
  CONSTRAINT `fk_cna_approved`  FOREIGN KEY (`approved_by`)  REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE: community_needs_items
-- Individual needs/problems identified per assessment
-- ============================================================
CREATE TABLE `community_needs_items` (
  `id`              INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `assessment_id`   INT UNSIGNED NOT NULL,
  `area`            ENUM('Education','Technology','Health','Sanitation','Livelihood','Environment','Other') NOT NULL,
  `problem`         TEXT         NOT NULL,
  `severity`        ENUM('low','medium','high','critical') NOT NULL DEFAULT 'medium',
  `recommended_action` TEXT      DEFAULT NULL,
  `created_at`      TIMESTAMP   NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_cni_assessment` (`assessment_id`),
  CONSTRAINT `fk_cni_assessment` FOREIGN KEY (`assessment_id`) REFERENCES `community_needs_assessments` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE: project_proposals
-- CES Project Proposals submitted by programs
-- ============================================================
CREATE TABLE `project_proposals` (
  `id`              INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `assessment_id`   INT UNSIGNED DEFAULT NULL COMMENT 'Based on which CNA',
  `barangay_id`     INT UNSIGNED NOT NULL,
  `program_id`      INT UNSIGNED NOT NULL COMMENT 'Implementing program',
  `submitted_by`    INT UNSIGNED NOT NULL,
  `title`           VARCHAR(255) NOT NULL,
  `description`     TEXT         NOT NULL,
  `objectives`      TEXT         DEFAULT NULL,
  `target_area`     ENUM('Education','Technology','Health','Sanitation','Livelihood','Environment','Other') NOT NULL,
  `target_beneficiaries` TEXT    DEFAULT NULL,
  `expected_output` TEXT         DEFAULT NULL,
  `start_date`      DATE         DEFAULT NULL,
  `end_date`        DATE         DEFAULT NULL,
  `budget_requested` DECIMAL(12,2) DEFAULT 0.00,
  `attachment`      VARCHAR(255) DEFAULT NULL COMMENT 'File path of uploaded document',
  `status`          ENUM('draft','pending','approved','rejected','ongoing','completed') NOT NULL DEFAULT 'draft',
  `rejection_reason` TEXT        DEFAULT NULL,
  `approved_by`     INT UNSIGNED DEFAULT NULL,
  `approved_at`     TIMESTAMP   NULL DEFAULT NULL,
  `created_at`      TIMESTAMP   NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`      TIMESTAMP   NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_pp_assessment`  (`assessment_id`),
  KEY `fk_pp_barangay`    (`barangay_id`),
  KEY `fk_pp_program`     (`program_id`),
  KEY `fk_pp_submitted`   (`submitted_by`),
  KEY `fk_pp_approved`    (`approved_by`),
  CONSTRAINT `fk_pp_assessment` FOREIGN KEY (`assessment_id`) REFERENCES `community_needs_assessments` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_pp_barangay`   FOREIGN KEY (`barangay_id`)   REFERENCES `barangays` (`id`),
  CONSTRAINT `fk_pp_program`    FOREIGN KEY (`program_id`)    REFERENCES `programs` (`id`),
  CONSTRAINT `fk_pp_submitted`  FOREIGN KEY (`submitted_by`)  REFERENCES `users` (`id`),
  CONSTRAINT `fk_pp_approved`   FOREIGN KEY (`approved_by`)   REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE: proposal_approvals
-- Approval workflow log (VP, Dean, CES Head chain)
-- ============================================================
CREATE TABLE `proposal_approvals` (
  `id`           INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `proposal_id`  INT UNSIGNED NOT NULL,
  `approver_id`  INT UNSIGNED NOT NULL,
  `approver_role` VARCHAR(50) NOT NULL COMMENT 'Role at time of action',
  `action`       ENUM('approved','rejected','returned') NOT NULL,
  `remarks`      TEXT         DEFAULT NULL,
  `acted_at`     TIMESTAMP   NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_pa_proposal`  (`proposal_id`),
  KEY `fk_pa_approver`  (`approver_id`),
  CONSTRAINT `fk_pa_proposal` FOREIGN KEY (`proposal_id`) REFERENCES `project_proposals` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_pa_approver` FOREIGN KEY (`approver_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE: activities
-- Individual activities/events under a proposal
-- ============================================================
CREATE TABLE `activities` (
  `id`           INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `proposal_id`  INT UNSIGNED NOT NULL,
  `title`        VARCHAR(200) NOT NULL,
  `description`  TEXT         DEFAULT NULL,
  `venue`        VARCHAR(200) DEFAULT NULL,
  `activity_date` DATE        NOT NULL,
  `start_time`   TIME         DEFAULT NULL,
  `end_time`     TIME         DEFAULT NULL,
  `status`       ENUM('scheduled','ongoing','completed','cancelled') NOT NULL DEFAULT 'scheduled',
  `created_by`   INT UNSIGNED NOT NULL,
  `created_at`   TIMESTAMP   NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`   TIMESTAMP   NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_act_proposal`   (`proposal_id`),
  KEY `fk_act_created_by` (`created_by`),
  CONSTRAINT `fk_act_proposal`   FOREIGN KEY (`proposal_id`) REFERENCES `project_proposals` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_act_created_by` FOREIGN KEY (`created_by`)  REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE: participants
-- Community beneficiary / participant records
-- ============================================================
CREATE TABLE `participants` (
  `id`             INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `barangay_id`    INT UNSIGNED NOT NULL,
  `first_name`     VARCHAR(100) NOT NULL,
  `last_name`      VARCHAR(100) NOT NULL,
  `birthdate`      DATE         DEFAULT NULL,
  `gender`         ENUM('male','female','other') DEFAULT NULL,
  `civil_status`   ENUM('single','married','widowed','separated') DEFAULT NULL,
  `address`        TEXT         DEFAULT NULL,
  `contact_no`     VARCHAR(20)  DEFAULT NULL,
  `category`       ENUM('student','youth','senior','pwd','parent','other') NOT NULL DEFAULT 'other',
  `is_active`      TINYINT(1)  NOT NULL DEFAULT 1,
  `created_at`     TIMESTAMP   NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`     TIMESTAMP   NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_part_barangay` (`barangay_id`),
  CONSTRAINT `fk_part_barangay` FOREIGN KEY (`barangay_id`) REFERENCES `barangays` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE: attendance
-- Attendance records per activity (participants + UCC volunteers)
-- ============================================================
CREATE TABLE `attendance` (
  `id`              INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `activity_id`     INT UNSIGNED NOT NULL,
  `attendee_type`   ENUM('participant','user') NOT NULL COMMENT 'community participant or UCC user',
  `participant_id`  INT UNSIGNED DEFAULT NULL,
  `user_id`         INT UNSIGNED DEFAULT NULL,
  `time_in`         TIME         DEFAULT NULL,
  `time_out`        TIME         DEFAULT NULL,
  `remarks`         VARCHAR(255) DEFAULT NULL,
  `recorded_by`     INT UNSIGNED NOT NULL,
  `created_at`      TIMESTAMP   NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_att_activity`    (`activity_id`),
  KEY `fk_att_participant` (`participant_id`),
  KEY `fk_att_user`        (`user_id`),
  KEY `fk_att_recorded`    (`recorded_by`),
  CONSTRAINT `fk_att_activity`    FOREIGN KEY (`activity_id`)    REFERENCES `activities` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_att_participant` FOREIGN KEY (`participant_id`) REFERENCES `participants` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_att_user`        FOREIGN KEY (`user_id`)        REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_att_recorded`    FOREIGN KEY (`recorded_by`)    REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE: certificates
-- Auto-generated certificate records
-- ============================================================
CREATE TABLE `certificates` (
  `id`              INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `activity_id`     INT UNSIGNED NOT NULL,
  `attendee_type`   ENUM('participant','user') NOT NULL,
  `participant_id`  INT UNSIGNED DEFAULT NULL,
  `user_id`         INT UNSIGNED DEFAULT NULL,
  `certificate_type` ENUM('participation','completion','volunteer','recognition') NOT NULL DEFAULT 'participation',
  `issued_at`       TIMESTAMP   NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `file_path`       VARCHAR(255) DEFAULT NULL COMMENT 'Generated PDF path',
  `is_released`     TINYINT(1)  NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `fk_cert_activity`    (`activity_id`),
  KEY `fk_cert_participant` (`participant_id`),
  KEY `fk_cert_user`        (`user_id`),
  CONSTRAINT `fk_cert_activity`    FOREIGN KEY (`activity_id`)    REFERENCES `activities` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_cert_participant` FOREIGN KEY (`participant_id`) REFERENCES `participants` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_cert_user`        FOREIGN KEY (`user_id`)        REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE: evaluations
-- Post-activity evaluations from participants and UCC members
-- ============================================================
CREATE TABLE `evaluations` (
  `id`              INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `activity_id`     INT UNSIGNED NOT NULL,
  `evaluator_type`  ENUM('participant','user') NOT NULL,
  `participant_id`  INT UNSIGNED DEFAULT NULL,
  `user_id`         INT UNSIGNED DEFAULT NULL,
  `rating_overall`  TINYINT UNSIGNED DEFAULT NULL COMMENT '1–5 scale',
  `rating_relevance` TINYINT UNSIGNED DEFAULT NULL,
  `rating_facilitation` TINYINT UNSIGNED DEFAULT NULL,
  `rating_venue`    TINYINT UNSIGNED DEFAULT NULL,
  `comments`        TEXT         DEFAULT NULL,
  `suggestions`     TEXT         DEFAULT NULL,
  `submitted_at`    TIMESTAMP   NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_eval_activity`    (`activity_id`),
  KEY `fk_eval_participant` (`participant_id`),
  KEY `fk_eval_user`        (`user_id`),
  CONSTRAINT `fk_eval_activity`    FOREIGN KEY (`activity_id`)    REFERENCES `activities` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_eval_participant` FOREIGN KEY (`participant_id`) REFERENCES `participants` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_eval_user`        FOREIGN KEY (`user_id`)        REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE: financial_records
-- Budget and expense tracking per proposal
-- ============================================================
CREATE TABLE `financial_records` (
  `id`             INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `proposal_id`    INT UNSIGNED NOT NULL,
  `activity_id`    INT UNSIGNED DEFAULT NULL,
  `type`           ENUM('budget','expense','donation','linkage_support') NOT NULL,
  `description`    VARCHAR(255) NOT NULL,
  `amount`         DECIMAL(12,2) NOT NULL DEFAULT 0.00,
  `source`         VARCHAR(150) DEFAULT NULL COMMENT 'UCC Admin, Partner, Donor, etc.',
  `receipt_no`     VARCHAR(100) DEFAULT NULL,
  `transaction_date` DATE       NOT NULL,
  `recorded_by`    INT UNSIGNED NOT NULL,
  `created_at`     TIMESTAMP   NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_fin_proposal`    (`proposal_id`),
  KEY `fk_fin_activity`    (`activity_id`),
  KEY `fk_fin_recorded`    (`recorded_by`),
  CONSTRAINT `fk_fin_proposal` FOREIGN KEY (`proposal_id`) REFERENCES `project_proposals` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_fin_activity` FOREIGN KEY (`activity_id`) REFERENCES `activities` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_fin_recorded` FOREIGN KEY (`recorded_by`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE: monitoring_logs
-- Sustainability & progress tracking entries per proposal
-- ============================================================
CREATE TABLE `monitoring_logs` (
  `id`              INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `proposal_id`     INT UNSIGNED NOT NULL,
  `participant_id`  INT UNSIGNED DEFAULT NULL COMMENT 'Specific beneficiary being monitored',
  `log_date`        DATE         NOT NULL,
  `indicator`       VARCHAR(200) NOT NULL COMMENT 'e.g. Reading level improved, Blood pressure normalized',
  `initial_status`  TEXT         DEFAULT NULL,
  `current_status`  TEXT         DEFAULT NULL,
  `improvement`     ENUM('none','minimal','moderate','significant') DEFAULT NULL,
  `notes`           TEXT         DEFAULT NULL,
  `logged_by`       INT UNSIGNED NOT NULL,
  `created_at`      TIMESTAMP   NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_mon_proposal`    (`proposal_id`),
  KEY `fk_mon_participant` (`participant_id`),
  KEY `fk_mon_logged`      (`logged_by`),
  CONSTRAINT `fk_mon_proposal`    FOREIGN KEY (`proposal_id`)    REFERENCES `project_proposals` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_mon_participant` FOREIGN KEY (`participant_id`) REFERENCES `participants` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_mon_logged`      FOREIGN KEY (`logged_by`)      REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE: linkages
-- MOA partners and external linkages
-- ============================================================
CREATE TABLE `linkages` (
  `id`             INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name`           VARCHAR(200) NOT NULL,
  `type`           ENUM('government','ngo','private','academic','other') NOT NULL DEFAULT 'other',
  `contact_person` VARCHAR(150) DEFAULT NULL,
  `contact_email`  VARCHAR(150) DEFAULT NULL,
  `contact_no`     VARCHAR(20)  DEFAULT NULL,
  `address`        TEXT         DEFAULT NULL,
  `moa_date`       DATE         DEFAULT NULL COMMENT 'Date MOA was signed',
  `moa_expiry`     DATE         DEFAULT NULL,
  `notes`          TEXT         DEFAULT NULL,
  `is_active`      TINYINT(1)  NOT NULL DEFAULT 1,
  `created_at`     TIMESTAMP   NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`     TIMESTAMP   NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE: proposal_linkages
-- Pivot: which linkages are tied to which proposals
-- ============================================================
CREATE TABLE `proposal_linkages` (
  `proposal_id`  INT UNSIGNED NOT NULL,
  `linkage_id`   INT UNSIGNED NOT NULL,
  `support_type` VARCHAR(100) DEFAULT NULL COMMENT 'e.g. funding, manpower, materials',
  `created_at`   TIMESTAMP   NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`proposal_id`, `linkage_id`),
  KEY `fk_pl_linkage` (`linkage_id`),
  CONSTRAINT `fk_pl_proposal` FOREIGN KEY (`proposal_id`) REFERENCES `project_proposals` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_pl_linkage`  FOREIGN KEY (`linkage_id`)  REFERENCES `linkages` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE: audit_logs
-- System-wide activity log
-- ============================================================
CREATE TABLE `audit_logs` (
  `id`          BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id`     INT UNSIGNED    DEFAULT NULL,
  `action`      VARCHAR(100)    NOT NULL COMMENT 'e.g. login, create_proposal, approve_proposal',
  `module`      VARCHAR(50)     DEFAULT NULL,
  `record_id`   INT UNSIGNED    DEFAULT NULL,
  `description` TEXT            DEFAULT NULL,
  `ip_address`  VARCHAR(45)     DEFAULT NULL,
  `created_at`  TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_audit_user` (`user_id`),
  CONSTRAINT `fk_audit_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- SEED DATA
-- ============================================================

-- Programs
INSERT INTO `programs` (`code`, `name`, `department`) VALUES
('BSN',   'Bachelor of Science in Nursing',          'College of Nursing'),
('BSIT',  'Bachelor of Science in Information Technology', 'College of Computing'),
('BSED',  'Bachelor of Secondary Education',         'College of Education'),
('BSBA',  'Bachelor of Science in Business Administration', 'College of Business'),
('BSM',   'Bachelor of Science in Midwifery',        'College of Nursing');

-- Default Barangay
INSERT INTO `barangays` (`name`, `city`, `contact_person`, `address`) VALUES
('Barangay 96', 'Manila', 'Barangay Captain', 'Barangay 96, Manila City');

-- Default Admin User (password: Admin@123)
INSERT INTO `users` (`program_id`, `employee_id`, `first_name`, `last_name`, `email`, `password`, `role`) VALUES
(NULL, 'ADM-001', 'System', 'Administrator', 'admin@ucc.edu.ph',
 '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');

-- CES Head (password: Admin@123)
INSERT INTO `users` (`program_id`, `employee_id`, `first_name`, `last_name`, `email`, `password`, `role`) VALUES
(NULL, 'CES-001', 'Janet', 'Cruz', 'janet.cruz@ucc.edu.ph',
 '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'ces_head');

COMMIT;
