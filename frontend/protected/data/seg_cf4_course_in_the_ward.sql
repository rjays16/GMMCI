CREATE TABLE `gmmci041518`.`seg_cf4_course_in_the_ward`(
  `id` VARCHAR(36) NOT NULL,
  `entry_id` VARCHAR(36),
  `course_in_the_ward` TEXT,
  `is_deleted` TINYINT(1) DEFAULT 0,
  `created_at` DATETIME,
  `modified_at` DATETIME,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`entry_id`) REFERENCES `gmmci041518`.`seg_cf4_h`(`id`)
);

ALTER TABLE `gmmci041518`.`seg_cf4_course_in_the_ward`
  CHANGE `course_in_the_ward` `date_action` DATETIME NULL,
  ADD COLUMN `doctor_action` TEXT NULL AFTER `date_action`,
  CHANGE `created_at` `created_at` TIMESTAMP NULL,
  ADD COLUMN `modify` DATETIME NULL AFTER `created_at`,
  CHANGE `modified_at` `modified_by` VARCHAR(50) NULL,
  ADD COLUMN `deleted_by` VARCHAR(50) NULL AFTER `modified_by`,
  ADD COLUMN `deleted_at` DATETIME NULL AFTER `deleted_by`,
  CHANGE `is_deleted` `is_deleted` TINYINT(1) DEFAULT 0  NULL  AFTER `deleted_at`;
