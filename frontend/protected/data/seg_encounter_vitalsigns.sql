ALTER TABLE `gmmci041518`.`seg_encounter_vitalsigns` 
  ADD COLUMN `height` DOUBLE NULL AFTER `weight`,
  ADD COLUMN `height_unit` INT NULL AFTER `weight_unit`;