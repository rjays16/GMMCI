CREATE TABLE `gmmci041518`.`seg_cf4_medicine`(
  `id` VARCHAR(36) NOT NULL,
  `entry_id` VARCHAR(36),
  `drug_code` VARCHAR(40),
  `generic` TEXT,
  `quantity` VARCHAR(12),
  `route` VARCHAR(12),
  `frequency` VARCHAR(12),
  `cost` VARCHAR(12),
  `is_pndf` TINYINT(1) DEFAULT 1,
  `is_deleted` TINYINT(1) DEFAULT 0,
  `created_at` DATETIME,
  `modified_at` DATETIME,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`entry_id`) REFERENCES `gmmci041518`.`seg_cf4_h`(`id`),
  FOREIGN KEY (`drug_code`) REFERENCES `gmmci041518`.`seg_phil_medicine`(`drug_code`)
);

ALTER TABLE `gmmci041518`.`seg_cf4_medicine`
  CHANGE `created_at` `created_at` TIMESTAMP NULL,
  ADD COLUMN `modify` DATETIME NULL AFTER `created_at`,
  CHANGE `modified_at` `modified_by` VARCHAR(50) NULL,
  ADD COLUMN `deleted_by` VARCHAR(50) NULL AFTER `modified_by`,
  ADD COLUMN `deleted_at` DATETIME NULL AFTER `deleted_by`,
  CHANGE `is_deleted` `is_deleted` TINYINT(1) DEFAULT 0  NULL  AFTER `deleted_at`,
  ADD FOREIGN KEY (`drug_code`) REFERENCES `gmmci041518`.`seg_phil_medicine`(`drug_code`);

ALTER TABLE `gmmci041518`.`seg_cf4_medicine`
  CHANGE `created_at` `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP   NULL,
  CHANGE `modify` `modify` DATETIME DEFAULT 0   NULL,
  CHANGE `deleted_at` `deleted_at` DATETIME DEFAULT 0   NULL;

ALTER TABLE `gmmci041518`.`seg_cf4_medicine`
  CHANGE `route` `route` TEXT CHARSET utf8 COLLATE utf8_unicode_ci NULL,
  CHANGE `frequency` `frequency` TEXT CHARSET utf8 COLLATE utf8_unicode_ci NULL;

ALTER TABLE `gmmci041518`.`seg_cf4_medicine`
  CHANGE `route` `route` TEXT(500) CHARSET utf8 COLLATE utf8_unicode_ci NULL,
  CHANGE `frequency` `frequency` VARCHAR(50) CHARSET utf8 COLLATE utf8_unicode_ci NULL;
