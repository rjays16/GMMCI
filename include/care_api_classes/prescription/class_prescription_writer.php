<?php
	require('./roots.php');
	require_once($root_path.'include/care_api_classes/class_core.php');

	class SegPrescription extends Core {
		var $tb_prescription = "seg_prescription";
		var $tb_prescription_items = "seg_prescription_items";
		var $tb_template = "seg_prescription_template";
		var $tb_template_items = "seg_prescription_template_items";
		var $fld_prescription =
			array(
				'id',
				'encounter_nr',
				'prescription_date',
				'instructions',
                'clinical_impression',
				'is_deleted',
				'reason_for_deletion',
				'history',
				'create_id',
				'create_time',
				'modify_id',
				'modify_time'
			);
		var $fld_prescription_items =
			array(
				'prescription_id',
				'item_code',
				'item_name',
				'quantity',
				'unit',
				'dosage',
				'period_count',
				'period_interval'
			);
		var $fld_template =
			array(
				'id',
				'name',
				'owner',
				'is_deleted',
				'history',
				'create_id',
				'create_time',
				'modify_id',
				'modify_time'
			);
		var $fld_template_items =
			array(
				'template_id',
				'item_code',
				'item_name',
				'quantity',
				'unit',
				'dosage',
				'period_count',
				'period_interval'
			);

		function SegPrescription()
		{

		}

		function usePrescription()
		{
			$this->coretable = $this->tb_prescription;
			$this->ref_array = $this->fld_prescription;
		}

		function usePrescriptionItems()
		{
			$this->coretable = $this->tb_prescription_items;
			$this->ref_array = $this->fld_prescription_items;
		}

		function useTemplates()
		{
			$this->coretable = $this->tb_template;
			$this->ref_array = $this->fld_template;
		}

		function useTemplateItems()
		{
			$this->coretable = $this->tb_template_items;
			$this->ref_array = $this->fld_template_items;
		}
        
        
        /**
        * put your comment there...
        * 
        * @param mixed $encounter
        * @param mixed $checkId
        */
        function getLatestClinicalImpression($encounter, $checkId = true) {
            global $db;
            
            $sessionUserId = $_SESSION['sess_temp_userid'];
            $this->sql = "SELECT clinical_impression FROM seg_prescription WHERE encounter_nr=" . $db->qstr($encounter) ."\n";
            if ($checkid) {
                $this->sql .= "AND create_id=" . $db->qstr($sessionUserId) . "\n";
            }
            $this->sql .= "ORDER BY modify_time DESC";
            
            $this->result = $db->GetOne($this->sql);
            if ($this->result === false) {
                echo $db->ErrorMsg();
                echo "<pre>" . $ths->sql . "</pre>";
            }
            
            return $this->result;
        }
        
        

		function getRecentMeds($item_code, $offset, $maxcount)
		{
			global $db;

			if (!$offset) $offset = 0;
			if (!$maxcount) $maxcount = 10;

			$this->sql = "(SELECT SQL_CALC_FOUND_ROWS DISTINCT ph.generic,pi.item_name, pi.item_code, pi.quantity, \n".
										"pi.dosage, pi.period_count, pi.period_interval, 'Available' AS `availability` \n".
									"FROM seg_prescription_items AS pi \n".
									"INNER JOIN seg_prescription AS p ON pi.prescription_id=p.id \n".
									"INNER JOIN care_pharma_products_main AS ph ON ph.bestellnum=pi.item_code \n".
									"WHERE pi.item_code=".$db->qstr($item_code)." ORDER BY p.modify_time DESC)\n".
									"UNION\n".
									"(SELECT DISTINCT ph.generic, ti.item_name, ti.item_code, ti.quantity, ti.dosage, \n".
										"ti.period_count, ti.period_interval, 'Available' AS `availability` \n".
									"FROM seg_prescription_template_items AS ti \n".
									"INNER JOIN seg_prescription_template AS t ON ti.template_id=t.id \n".
									"INNER JOIN care_pharma_products_main AS ph ON ph.bestellnum=ti.item_code \n".
									"WHERE ti.item_code=".$db->qstr($item_code)."\n".
									"ORDER BY t.modify_time DESC )LIMIT $offset, $maxcount";
			if($this->result=$db->Execute($this->sql)) {
				return $this->result;
			} else { return false; }
		}

		function getTemplates($name, $offset, $maxcount, $sort)
		{
			global $db;
			if (!$offset || !is_numeric($offset))
                $offset = 0;
			if (!$maxcount || !is_numeric($maxcount))
                $maxcount = 10;

            
            if (!in_array($sort, array('item_name', 'name'))) {
                $sort = 'name';
            }
            
			$this->sql = "SELECT SQL_CALC_FOUND_ROWS t.*, t.name, t.owner, ti.item_code, ti.item_name, ti.dosage, \n".
			    "ti.quantity, ti.period_count, ti.period_interval, 'Available' AS `availability`, \n".
			    "(SELECT ph.generic FROM care_pharma_products_main AS ph WHERE ph.bestellnum=ti.item_code) AS `generic`, \n".
			    "(SELECT ph.is_restricted FROM care_pharma_products_main AS ph WHERE ph.bestellnum=ti.item_code) AS `is_restricted` \n".
			    "FROM seg_prescription_template AS t \n".
			    "INNER JOIN seg_prescription_template_items AS ti ON ti.template_id=t.id \n".
			    //"WHERE t.is_deleted=0 AND t.name like '$name%' \n".
                "WHERE t.is_deleted=0 AND create_id=" . $db->qstr($_SESSION['sess_temp_userid']) . " AND t.name LIKE " . $db->qstr($name . '%') . "\n".
			    "ORDER BY {$sort} LIMIT $offset, $maxcount";
			if($this->result=$db->Execute($this->sql)) {
				return $this->result;
			} else { return false; }
		}

		function savePrescription($data)
		{
			global $db;
			$id = create_guid();
			$data['id'] = $id;
			$this->usePrescription();
			$this->setDataArray($data);
			if($this->insertDataFromInternalArray() !== FALSE) {
				return $id;
			} else {
				$this->error_msg = $db->ErrorMsg();
				return FALSE;
			}
		}

		function savePrescriptionItems($id, $itemsArray)
		{
			global $db;
			$id = $db->qstr($id);
			$this->sql = "INSERT INTO $this->tb_prescription_items (prescription_id,item_code,item_name,quantity,dosage,\n".
									"period_count,period_interval) VALUES($id,?,?,?,?,?,?)";
			if($buf=$db->Execute($this->sql,$itemsArray)) {
				return TRUE;
			} else { $this->error_msg = $db->ErrorMsg(); return FALSE; }
		}

		function saveTemplate($data)
		{
			global $db;
			$id = create_guid();
			$data['id'] = $id;
			$this->useTemplates();
			$this->setDataArray($data);
			if($this->insertDataFromInternalArray() !== FALSE) {
				return $id;
			} else {
				$this->error_msg = $db->ErrorMsg();
				return FALSE;
			}
		}

		function saveTemplateItems($id, $itemsArray)
		{
			global $db;
			$id = $db->qstr($id);
			$this->sql = "INSERT INTO $this->tb_template_items (template_id,item_code,item_name,quantity,dosage,\n".
									"period_count,period_interval) VALUES($id,?,?,?,?,?,?)";
			if($buf=$db->Execute($this->sql,$itemsArray)) {
				return TRUE;
			} else { $this->error_msg = $db->ErrorMsg();  return FALSE; }
		}

		function listTemplates($name, $sort, $offset, $maxcount)
		{
			global $db;
			if (!$offset) $offset = 0;
			if (!$maxcount) $maxcount = 10;

			$this->sql = "SELECT SQL_CALC_FOUND_ROWS fn_get_person_name(cp.pid) AS `owner_name`,t.* \n".
									"FROM seg_prescription_template AS t \n".
									"LEFT JOIN care_users AS cu ON t.owner=cu.login_id \n".
									"LEFT JOIN care_personell AS p ON p.nr=cu.personell_nr \n".
									"LEFT JOIN care_person AS cp ON cp.pid=p.pid \n".
									"WHERE t.is_deleted=0 AND t.name like '$name%' \n".
									"ORDER BY {$sort} LIMIT $offset, $maxcount";
			if($this->result=$db->Execute($this->sql)) {
				return $this->result;
			} else { $this->error_msg = $db->ErrorMsg(); return false; }
		}

		function getTemplateItems($id)
		{
			global $db;

			$this->sql = "SELECT SQL_CALC_FOUND_ROWS ti.*, ph.generic \n".
									"FROM seg_prescription_template_items AS ti \n".
									"INNER JOIN seg_prescription_template AS t ON t.id=ti.template_id \n".
									"LEFT JOIN care_pharma_products_main AS ph ON ph.bestellnum=ti.item_code \n".
									"WHERE t.is_deleted=0 AND t.id='$id' \n".
									"ORDER BY ti.item_name ASC";
			if($this->result=$db->Execute($this->sql)) {
				return $this->result;
			} else { $this->error_msg = $db->ErrorMsg(); return false; }
		}

		function deleteTemplate($id)
		{
			global $db;
			$this->sql = "UPDATE $this->tb_template SET is_deleted=1, \n".
						"history=CONCAT(history,'\nDeleted: ".date('Y-m-d H:i:s')." [".addslashes($_SESSION['sess_temp_userid'])."]'), \n".
						"modify_time=".$db->qstr(date('Y-m-d H:i:s')).", modify_id=".$db->qstr($_SESSION['sess_temp_userid'])." \n".
						"WHERE id=".$db->qstr($id);
			if($this->result=$db->Execute($this->sql)) {
				if($db->Affected_Rows()) {
					return TRUE;
				}
			}else {
				$this->error_msg = $db->ErrorMsg();
				return FALSE;
			}
		}

		function clearTemplateItems($id)
		{
			global $db;
			$this->sql = "DELETE FROM $this->tb_template_items WHERE template_id=".$db->qstr($id);
			if($this->result=$db->Execute($this->sql)) {
				return TRUE;
			} else {
				$this->error_msg = $db->ErrorMsg();
				return FALSE;
			}
		}

		function updateTemplate($id, $name)
		{
			global $db;
			$this->sql = "UPDATE $this->tb_template SET name=".$db->qstr($name).", \n".
								"history=CONCAT(history,'\nUpdated: ".date('Y-m-d H:i:s')." [".addslashes($_SESSION['sess_temp_userid'])."]'), \n".
								"modify_time=".$db->qstr(date('Y-m-d H:i:s')).", modify_id=".$db->qstr($_SESSION['sess_temp_userid'])." \n".
								"WHERE id=".$db->qstr($id);
			if($this->result=$db->Execute($this->sql)) {
				return TRUE;
			} else {
				$this->error_msg = $db->ErrorMsg();
				return FALSE;
			}
		}
        
        
        function getPrescriptionInfo($prescription_id) {
            global $db;
            $this->sql = "SELECT id, encounter_nr, prescription_date, instructions,\n" .
                "clinical_impression\n" . 
                "FROM seg_prescription\n" .
                "WHERE id=" . $db->qstr($prescription_id);
            return $db->GetRow($this->sql);
        }
        

		function getPrescription($encounter_nr, $prescription_id)
		{
			global $db;
			$this->sql = "SELECT SQL_CALC_FOUND_ROWS\n".
                    "product.artikelname, product.generic,\n".
                    "pi.item_name, pi.quantity, pi.dosage, pi.period_count, \n".
			        "pi.period_interval\n".
                    //"fn_get_person_name_first_mi_last(cp.pid) AS `writer` ,\n".
                    //"cp.license_nr, cp.prescription_license_nr\n".
			    "FROM seg_prescription_items AS pi \n".
			        //"INNER JOIN seg_prescription AS p ON pi.prescription_id=p.id \n".
                    "LEFT JOIN care_pharma_products_main product ON product.bestellnum=pi.item_code\n".
			        //"LEFT JOIN care_users AS u ON p.create_id=u.login_id \n".
			        //"LEFT JOIN care_personell AS cp ON u.personell_nr=cp.nr \n".
			    //"WHERE p.encounter_nr=".$db->qstr($encounter_nr)." AND p.prescription_date=DATE(NOW()) ORDER BY item_name ASC";
			    "WHERE pi.prescription_id=".$db->qstr($prescription_id)." ORDER BY item_name ASC";
			if($this->result=$db->Execute($this->sql)) {
				return $this->result;
			}else {
				$this->error_msg = $db->ErrorMsg();
				return FALSE;
			}
		}
        
        
        function getPrescriberInfo($prescriptionId) {
            global $db;
        /*
		edited by Nick, 11/18/2013 4:07 PM
		added a return field name_formal to identify the department
		of the doctor
        */    
            $this->sql = "SELECT\n" .
                    "fn_get_person_name_first_mi_last(personnel.pid) AS `name`,\n".
                    "personnel.license_nr, personnel.prescription_license_nr, personnel.ptr_nr, personnel.s2_nr,dept.`name_formal` \n".
                "FROM seg_prescription prescription\n".
                    "INNER JOIN care_users user ON prescription.create_id=user.login_id \n".
                    "INNER JOIN care_personell personnel ON user.personell_nr=personnel.nr \n".
                    "INNER JOIN care_personell_assignment cpa ON cpa.`personell_nr`=user.`personell_nr` \n".
                    "INNER JOIN care_department dept ON dept.nr = cpa.`location_nr` \n".
                "WHERE prescription.id=" . $db->qstr($prescriptionId);
        /*
        end Nick
        */
            return $db->GetRow($this->sql);
        }
        

		function isLicensedPersonell()
		{
			global $db;
			$this->sql = "SELECT EXISTS( SELECT cp.prescription_license_nr \n".
								"FROM care_personell AS cp \n".
								"LEFT JOIN care_users AS cu ON cp.nr=cu.personell_nr \n".
								"WHERE cu.login_id=".$db->qstr($_SESSION['sess_temp_userid'])." \n".
								"AND (!ISNULL(cp.prescription_license_nr) OR cp.prescription_license_nr!='') \n".
								") AS `has_license`";
			if($has_license=$db->GetOne($this->sql)) {
				return $has_license;
			} else {
				$this->error_msg = $db->ErrorMsg();
				return FALSE;
			}
		}
        
        
		function getPrescriptionLicense()
		{
			global $db;
			$this->sql = "SELECT cp.prescription_license_nr FROM care_personell AS cp \n".
				"LEFT JOIN care_users AS cu ON cp.nr=cu.personell_nr \n".
				"WHERE cu.login_id=".$db->qstr($_SESSION['sess_temp_userid']);
			if($license=$db->GetOne($this->sql)) {
				return $license;
			} else {
				$this->error_msg = $db->ErrorMsg();
				return FALSE;
			}
		}

	}//end of class prescription writer
?>
