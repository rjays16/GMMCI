CREATE TABLE `gmmci041518`.`seg_cf4_h`(
  `id` CHAR(36) NOT NULL,
  `entry_date` DATETIME,
  `pid` VARCHAR(12),
  `encounter_nr` VARCHAR(15),
  `modify_time` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `is_deleted` TINYINT(1) DEFAULT 0,
  `old_pid` VARCHAR(12),
  `is_uploaded` TINYINT(1) DEFAULT 0,
  `encoder` VARCHAR(100),
  `created_at` DATETIME,
  `modified_at` DATETIME,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`pid`) REFERENCES `gmmci041518`.`care_person`(`pid`),
  FOREIGN KEY (`encounter_nr`) REFERENCES `gmmci041518`.`care_encounter`(`encounter_nr`)
);
