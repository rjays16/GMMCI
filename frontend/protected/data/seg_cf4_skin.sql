CREATE TABLE `gmmci041518`.`seg_cf4_skin`(
  `id` VARCHAR(36) NOT NULL,
  `entry_id` VARCHAR(36),
  `encounter_nr` VARCHAR(15),
  `skin_id` SMALLINT(5),
  `remarks` TEXT,
  `created_at` TIMESTAMP,
  `modify` DATETIME,
  `modified_by` VARCHAR(50),
  `deleted_by` VARCHAR(50),
  `is_deleted` TINYINT(1),
  PRIMARY KEY (`id`),
  FOREIGN KEY (`entry_id`) REFERENCES `gmmci041518`.`seg_cf4_h`(`entry_id`),
  FOREIGN KEY (`encounter_nr`) REFERENCES `gmmci041518`.`care_encounter`(`encounter_nr`),
  FOREIGN KEY (`skin_id`) REFERENCES `gmmci041518`.`seg_cf4_lib_skin`(`id`)
);
