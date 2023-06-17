DELIMITER $$

USE `gmmci`$$

DROP FUNCTION IF EXISTS `fn_get_complete_address`$$

CREATE DEFINER=`root`@`localhost` FUNCTION `fn_get_complete_address`(pid VARCHAR(50)) RETURNS VARCHAR(500) CHARSET latin1
    DETERMINISTIC
BEGIN
	DECLARE s_full_address VARCHAR(250) DEFAULT '';
	DECLARE s_street VARCHAR(100) DEFAULT '';
	DECLARE s_brgy VARCHAR(80) DEFAULT '';
	DECLARE s_municity VARCHAR(80) DEFAULT '';
	DECLARE s_province VARCHAR(80) DEFAULT '';
	DECLARE s_zipcode VARCHAR(10) DEFAULT '';
	SELECT 
		cp.street_name, b.brgy_name, m.mun_name, m.zipcode, p.prov_name
			INTO s_street, s_brgy, s_municity, s_zipcode, s_province	
		FROM care_person cp
			LEFT JOIN seg_barangays b ON b.brgy_nr=cp.brgy_nr
			LEFT JOIN seg_municity m ON m.mun_nr=cp.mun_nr
			LEFT JOIN seg_provinces p ON p.prov_nr=m.prov_nr
		WHERE cp.pid=pid;
	SET s_full_address := 
		CONCAT(
			IF(IFNULL(s_street,'') = '', '' , CONCAT(s_street,', ')),
			IF(s_street IS NULL OR NOT s_street,'', ', '),
			IFNULL(s_brgy,''),
			', ',
			IFNULL(TRIM(s_municity),''),
			' ',
			IFNULL(s_zipcode,''),
			' ',
			IFNULL(s_province,'')
		);
	RETURN s_full_address;
END$$

DELIMITER ;