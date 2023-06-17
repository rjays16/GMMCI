CREATE TABLE `gmmci041518`.`seg_cf4_lib_clinical_record`(
  `id` VARCHAR(36) NOT NULL,
  `entry_id` VARCHAR(36),
  `encounter_nr` VARCHAR(15),
  `present_illsent` TEXT,
  `created_at` TIMESTAMP,
  `modify` DATETIME,
  `modified_by` VARCHAR(50),
  `deleted_by` VARCHAR(50),
  `deleted_at` DATETIME,
  `is_deleted` TINYINT(1) DEFAULT 0,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`entry_id`) REFERENCES `gmmci041518`.`seg_cf4_h`(`id`),
  FOREIGN KEY (`encounter_nr`) REFERENCES `gmmci041518`.`care_encounter`(`encounter_nr`)
);
