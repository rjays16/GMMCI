// cloud_storage_filename for phic cloud storage
ALTER TABLE seg_eclaims_claim_attachment ADD COLUMN `cloud_storage_filename` VARCHAR(155) NULL AFTER `is_added`
