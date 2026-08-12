SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

CREATE TABLE IF NOT EXISTS `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_locks_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint unsigned NOT NULL,
  `reserved_at` int unsigned DEFAULT NULL,
  `available_at` int unsigned NOT NULL,
  `created_at` int unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `failed_jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `fullname` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `accounttype` enum('client','healthcare_worker','admin') NOT NULL,
  `record_id` bigint unsigned DEFAULT NULL,
  `verified` tinyint(1) NOT NULL DEFAULT 0,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `approved` tinyint NOT NULL DEFAULT 0,
  `approved_by` bigint unsigned DEFAULT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`),
  KEY `users_approved_by_foreign` (`approved_by`),
  CONSTRAINT `users_approved_by_foreign` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `clients` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `first_name` varchar(255) NOT NULL,
  `last_name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(255) NOT NULL,
  `date_of_birth` date NOT NULL,
  `address` varchar(255) NOT NULL,
  `city` varchar(255) NOT NULL,
  `state` varchar(255) NOT NULL,
  `zip_code` varchar(255) NOT NULL,
  `country` varchar(255) NOT NULL,
  `alias` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `workers` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `first_name` varchar(255) NOT NULL,
  `last_name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(255) NOT NULL,
  `profession` varchar(255) NOT NULL,
  `specialization` varchar(255) NOT NULL,
  `license_number` varchar(255) NOT NULL,
  `years_of_experience` int NOT NULL,
  `facility_name` varchar(255) NOT NULL,
  `facility_address` varchar(255) NOT NULL,
  `city` varchar(255) NOT NULL,
  `state` varchar(255) NOT NULL,
  `credentials` text NOT NULL,
  `profile_photo` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `healthcare_workers` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `profession` varchar(255) DEFAULT NULL,
  `specialization` varchar(255) DEFAULT NULL,
  `license_number` varchar(255) DEFAULT NULL,
  `experience_years` int DEFAULT NULL,
  `facility_name` varchar(255) DEFAULT NULL,
  `facility_address` varchar(255) DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `credentials` text DEFAULT NULL,
  `profile_photo` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `healthcare_workers_user_id_unique` (`user_id`),
  CONSTRAINT `healthcare_workers_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `job_posting` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `minimum_pay_offer` decimal(10,2) DEFAULT NULL,
  `maximum_pay_offer` decimal(10,2) DEFAULT NULL,
  `client_id` bigint unsigned NOT NULL,
  `location` varchar(255) DEFAULT NULL,
  `employment_type` varchar(255) DEFAULT NULL,
  `experience` varchar(255) DEFAULT NULL,
  `specialty` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `job_posting_client_id_foreign` (`client_id`),
  CONSTRAINT `job_posting_client_id_foreign` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE OR REPLACE VIEW `job_postings` AS SELECT * FROM `job_posting`;

CREATE TABLE IF NOT EXISTS `job_posting_key_requirements` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `job_posting_id` bigint unsigned NOT NULL,
  `description` text NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `job_posting_key_requirements_job_posting_id_foreign` (`job_posting_id`),
  CONSTRAINT `job_posting_key_requirements_job_posting_id_foreign` FOREIGN KEY (`job_posting_id`) REFERENCES `job_posting` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `job_posting_key_skills` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `job_posting_id` bigint unsigned NOT NULL,
  `skill` varchar(100) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `job_posting_key_skills_job_posting_id_foreign` (`job_posting_id`),
  CONSTRAINT `job_posting_key_skills_job_posting_id_foreign` FOREIGN KEY (`job_posting_id`) REFERENCES `job_posting` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `applications` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `job_posting_id` bigint unsigned NOT NULL,
  `user_applied_id` bigint unsigned NOT NULL,
  `application_details` text DEFAULT NULL,
  `expected_salary` varchar(255) DEFAULT NULL,
  `attachments` text DEFAULT NULL,
  `metric_score` int NOT NULL DEFAULT 0,
  `interview_status` varchar(255) NOT NULL DEFAULT 'pending',
  `interview_date` datetime DEFAULT NULL,
  `interview_location` varchar(255) DEFAULT NULL,
  `interview_notes` text DEFAULT NULL,
  `reschedule_reason` text DEFAULT NULL,
  `additional_notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `applications_job_posting_id_user_applied_id_unique` (`job_posting_id`,`user_applied_id`),
  KEY `applications_user_applied_id_foreign` (`user_applied_id`),
  CONSTRAINT `applications_job_posting_id_foreign` FOREIGN KEY (`job_posting_id`) REFERENCES `job_posting` (`id`) ON DELETE CASCADE,
  CONSTRAINT `applications_user_applied_id_foreign` FOREIGN KEY (`user_applied_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `skills` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `workers_id` bigint unsigned NOT NULL,
  `skill` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `skills_workers_id_foreign` (`workers_id`),
  CONSTRAINT `skills_workers_id_foreign` FOREIGN KEY (`workers_id`) REFERENCES `healthcare_workers` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `employment_history` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `workers_id` bigint unsigned NOT NULL,
  `company_name` varchar(255) NOT NULL,
  `job_position` varchar(255) NOT NULL,
  `summary` text DEFAULT NULL,
  `year_started` year NOT NULL,
  `year_ended` year DEFAULT NULL,
  `is_currently_employed` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `employment_history_workers_id_foreign` (`workers_id`),
  CONSTRAINT `employment_history_workers_id_foreign` FOREIGN KEY (`workers_id`) REFERENCES `healthcare_workers` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `endorsements` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `worker_id` bigint unsigned NOT NULL,
  `job_post_id` bigint unsigned NOT NULL,
  `client_id` bigint unsigned DEFAULT NULL,
  `meet_and_greet_date` timestamp NULL DEFAULT NULL,
  `meet_and_greet_link` varchar(255) DEFAULT NULL,
  `endorsed_by` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `endorsements_worker_id_foreign` (`worker_id`),
  KEY `endorsements_job_post_id_foreign` (`job_post_id`),
  KEY `endorsements_client_id_foreign` (`client_id`),
  KEY `endorsements_endorsed_by_foreign` (`endorsed_by`),
  CONSTRAINT `endorsements_worker_id_foreign` FOREIGN KEY (`worker_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `endorsements_job_post_id_foreign` FOREIGN KEY (`job_post_id`) REFERENCES `job_posting` (`id`) ON DELETE CASCADE,
  CONSTRAINT `endorsements_client_id_foreign` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`) ON DELETE SET NULL,
  CONSTRAINT `endorsements_endorsed_by_foreign` FOREIGN KEY (`endorsed_by`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `ndis_requirements_parameters` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `requirements` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `ndis_requirements_completed` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `worker_id` bigint unsigned NOT NULL,
  `parameter_id` bigint unsigned NOT NULL,
  `document_link` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ndis_requirements_completed_worker_id_parameter_id_unique` (`worker_id`,`parameter_id`),
  KEY `ndis_requirements_completed_parameter_id_foreign` (`parameter_id`),
  CONSTRAINT `ndis_requirements_completed_worker_id_foreign` FOREIGN KEY (`worker_id`) REFERENCES `healthcare_workers` (`id`) ON DELETE CASCADE,
  CONSTRAINT `ndis_requirements_completed_parameter_id_foreign` FOREIGN KEY (`parameter_id`) REFERENCES `ndis_requirements_parameters` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `cms_settings` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `key` varchar(255) NOT NULL,
  `value` longtext DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `cms_settings_key_unique` (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT IGNORE INTO `migrations` (`migration`, `batch`) VALUES
('0001_01_01_000001_create_cache_table', 1),
('0001_01_01_000002_create_jobs_table', 1),
('2026_02_26_100000_create_users_table', 1),
('2026_02_26_100001_create_clients_table', 1),
('2026_02_26_100002_create_workers_table', 1),
('2026_02_26_110000_add_email_verification_to_users', 1),
('2026_02_28_000001_create_healthcare_workers_table', 1),
('2026_03_07_120000_add_phone_to_users_table', 1),
('2026_03_14_000000_create_job_posting_table', 1),
('2026_03_14_000001_create_job_posting_key_requirements_table', 1),
('2026_03_16_000000_create_applications_table', 1),
('2026_03_19_000000_add_interview_fields_to_applications_table', 1),
('2026_03_23_000000_create_skills_table', 1),
('2026_03_23_000001_create_employment_history_table', 1),
('2026_03_23_000002_add_approved_by_to_users_table', 1),
('2026_03_25_000001_add_reschedule_reason_to_applications_table', 1),
('2026_03_25_000002_add_additional_notes_to_applications_table', 1),
('2026_04_07_000000_create_job_posting_key_skills_table', 1),
('2026_04_07_000001_replace_healthcare_worker_city_state_with_location', 1),
('2026_04_12_181900_add_alias_to_clients_table', 1),
('2026_04_24_000000_create_endorsements_table', 1),
('2026_04_24_000001_add_client_id_to_endorsements_table', 1),
('2026_04_24_000002_merge_endorsement_meet_and_greet_columns', 1),
('2026_04_29_000000_create_ndis_requirements_parameters_table', 1),
('2026_04_29_000001_create_ndis_requirements_completed_table', 1),
('2026_04_29_000002_add_profile_photo_to_worker_tables', 1),
('2026_07_31_000000_create_cms_settings_table', 1);

INSERT IGNORE INTO `users`
(`fullname`, `email`, `phone`, `password`, `accounttype`, `record_id`, `verified`, `email_verified_at`, `approved`, `created_at`, `updated_at`)
VALUES
('Admin One', 'admin1@carehub.com', NULL, '$2y$10$D63XgW5YqiOHzDeMweNyg.tQKBtxCIMtOATP7.mABvnQoNSdc0iEy', 'admin', NULL, 1, NOW(), 1, NOW(), NOW()),
('Admin Two', 'admin2@carehub.com', NULL, '$2y$10$D63XgW5YqiOHzDeMweNyg.tQKBtxCIMtOATP7.mABvnQoNSdc0iEy', 'admin', NULL, 1, NOW(), 1, NOW(), NOW()),
('Admin Three', 'admin3@carehub.com', NULL, '$2y$10$D63XgW5YqiOHzDeMweNyg.tQKBtxCIMtOATP7.mABvnQoNSdc0iEy', 'admin', NULL, 1, NOW(), 1, NOW(), NOW());

SET FOREIGN_KEY_CHECKS = 1;
