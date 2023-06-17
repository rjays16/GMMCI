<?php

require("./roots.php");
require_once($root_path.'include/care_api_classes/class_core.php');
require_once($root_path.'include/care_api_classes/class_globalconfig.php');

/**
 * Class that handles emr integration
 * @author Vanessa A. Saren
 */
class EMR extends Core {

	var $sql;

	function consumeWRITEmethod($data, $url, $method){
		
		$data = array_map('utf8_encode', $data);
		$data_string = json_encode($data);
		
	   	$client = curl_init($url);                                                                      
	    curl_setopt($client, CURLOPT_CUSTOMREQUEST, $method);                                                                     
	    curl_setopt($client, CURLOPT_POSTFIELDS, $data_string);                                                                  
	    curl_setopt($client, CURLOPT_RETURNTRANSFER, true);                                                                      
	    curl_setopt($client, CURLOPT_HTTPHEADER, array(                                                                          
	        'Content-Type: application/json',                                                                                
	        'Content-Length: ' . strlen($data_string))                                                                       
	    );                                                                                                                   
	     
	    $result = curl_exec($client);
	    curl_close($client);

	    return $result;
	}

	function consumeREADmethod($url){
		$client = curl_init($url);
		curl_setopt($client, CURLOPT_RETURNTRANSFER, true);

		$result = curl_exec($client);
		#$info = curl_getinfo($client);
		curl_close($client);

		return $result;
	}

	function consumeWRITEmethodnoDATA($url, $method){
		$client = curl_init($url);                                                                      
	    curl_setopt($client, CURLOPT_URL, $url);
		#curl_setopt($client, CURLOPT_CUSTOMREQUEST, $method);
		curl_setopt($client, CURLOPT_PUT, true);
		curl_setopt($client, CURLOPT_RETURNTRANSFER, true);
	     
	    $result = curl_exec($client);
	    curl_close($client);

	    return $result;
	}

	function getAddressInfo($brgy_nr, $mun_nr){
		global $db;

		$this->sql = "SELECT sb.brgy_name, sm.mun_name,
				sp.prov_name,
				sc.country_name,
				sm.zipcode
				FROM seg_barangays AS sb
				LEFT JOIN seg_municity AS sm ON sm.mun_nr=sb.mun_nr 
				LEFT JOIN seg_provinces AS sp ON sp.prov_nr=sm.prov_nr 
				LEFT JOIN seg_regions AS sr ON sr.region_nr=sp.region_nr
				LEFT JOIN seg_country AS sc ON sc.country_code=".$db->qstr($citizenship)."
				WHERE sb.brgy_nr=".$db->qstr($brgy_nr)." 
				AND sm.mun_nr=".$db->qstr($mun_nr);
		
		$row = $db->GetRow($this->sql);	

		return $row;
	}

	function getDoctorInfo($personell_nr){
		global $db;

		$this->sql = "SELECT ps.pid, ps.nr, ps.job_function_title,
				ps.job_position,ps.license_nr,ps.tin, p.name_last, p.name_first, 
				p.name_middle, p.date_birth, p.sex, p.street_name, 
				sm.mun_name, sp.prov_name, sc.country_name, sm.zipcode,
				p.phone_1_nr,p.phone_2_nr,p.fax,p.cellphone_1_nr,p.email,
				p.brgy_nr, p.mun_nr
				FROM care_personell ps
				INNER JOIN care_person AS p ON p.pid=ps.pid
				LEFT JOIN seg_barangays AS sb ON sb.brgy_nr=p.brgy_nr
				LEFT JOIN seg_municity AS sm ON sm.mun_nr=p.mun_nr 
				LEFT JOIN seg_provinces AS sp ON sp.prov_nr=sm.prov_nr 
				LEFT JOIN seg_regions AS sr ON sr.region_nr=sp.region_nr
				LEFT JOIN seg_country AS sc ON sc.country_code=p.citizenship
				WHERE ps.nr=".$db->qstr($personell_nr);
		
		$row = $db->GetRow($this->sql);	

		return $row;
	}

	function isDoctor($personell_nr){
		global $db;

		$this->sql = "SELECT IF(SUBSTR(short_id,1,1)='D',1,0) AS isdoctor 
				FROM care_personell p 
				WHERE nr=".$db->qstr($personell_nr);
		
		$row = $db->GetRow($this->sql);	

		return $row['isdoctor'];
	}

	function getPatientdataArray($dataarr){
		
		extract($dataarr);

		$mother = $mother_fname . (isset($mother_maidenname) ? ' '.$mother_maidenname:'').
					(isset($mother_mname) ? ' '.$mother_mname:'').
					(isset($mother_lname) ? ' '.$mother_lname:'');

		$father = $father_fname . (isset($father_mname) ? ' '.$father_mname:'').
					(isset($father_lname) ? ' '.$father_lname:'');
		
		$name_last = $name_last. (isset($suffix) ? ', '.$suffix:'');

		$row_addr = $this->getAddressInfo($brgy_nr, $mun_nr);

		$street_name = $street_name. (isset($row_addr['brgy_name']) ? ', '.$row_addr['brgy_name']:'');

		$data = array(
                    "FirstName" => $name_first,
			        "MiddleName" => $name_middle,
			        "LastName" => $name_last,
			        "Gender" => strtoupper($sex),
			        "MaidenLastName" => ($name_maiden ? $name_maiden :''),
			        "Title" => ($title ? $title :''),
			        "DateOfBirth" => date('m/d/Y',strtotime($date_birth)),
			        "SecurityPin" => "",
			        "RegistrationNotes" => "",
			        "ClinicalNotes" => "",
			        "EmergencyContactPhone" => "",
			        "EmergencyContactName" => "",
			        "SocialSecurityNumber" => "",
			        "HISRegistrationDate" => $date_reg,
			        "HISId" => $pid,
			        "Street1" => ((trim($street_name)) ? trim($street_name) :''),
			        "Street2" => "",
			        "City" => trim($row_addr['mun_name']),
			        "Province" => trim($row_addr['prov_name']),
			        "Country" => trim($row_addr['country_name']),
			        "ZipCode" => trim($row_addr['zipcode']),
			        "Email" => ($email ? $email :''),
			        "MotherName" => ($mother ? $mother :''),
			        "FatherName" => ($father ? $father :''),
			        "SpouseName" => ($spouse_name ? $spouse_name :''),
			        "HomePhone" => ($phone_1_nr ? $phone_1_nr :''),
			        "CellPhone" => ($cellphone_1_nr ? $cellphone_1_nr :''),
			        "WorkPhone" => ($phone_2_nr ? $phone_2_nr :''),
			        "SerialNumber" => "",
			        "CompanyName" => ($employer ? $employer :''),
			        "CompanyAddressLine1" => "",
			        "CompanyAddressLine2" => "",
			        "CompanyCity" => "",
			        "CompanyProvince" => "",
			        "CompanyCountry" => "",
			        "CompanyPostalCode" => "",
			        "CompanyPhoneNumber" => "",
			        "CompanyFaxNumber" => "",
			        "GroupName" => "",
			        "CarrierName" => "",
			        "PlanCode" => "",
			        "Copay" => "",
			        "Status" => "",
			        "GuarantorName" => "",
			        "GuarantorDateOfBirth" => "",
			        "PayerNotes" => "",
                 );
		
		return $data;
	}

	function getDoctordataArray($dataarr){
		
		extract($dataarr);

		$mother = $mother_fname . (isset($mother_maidenname) ? ' '.$mother_maidenname:'').
					(isset($mother_mname) ? ' '.$mother_mname:'').
					(isset($mother_lname) ? ' '.$mother_lname:'');

		$father = $father_fname . (isset($father_mname) ? ' '.$father_mname:'').
					(isset($father_lname) ? ' '.$father_lname:'');
		
		$name_last = $name_last. (isset($suffix) ? ', '.$suffix:'');

		$row_addr = $this->getAddressInfo($brgy_nr, $mun_nr);

		$street_name = $street_name. (isset($row_addr['brgy_name']) ? ', '.$row_addr['brgy_name']:'');

		$data = array(
                    "PhysicianNumber" => $nr,
			        "FirstName" => $name_first,
			        "LastName" => $name_last,
			        "Address1" => ((trim($street_name)) ? trim($street_name) :''),
			        "Address2" => "",
			        "City" => trim($row_addr['mun_name']),
			        "Province" => trim($row_addr['prov_name']),
			        "ZipCode" => trim($row_addr['zipcode']),
			        "MainPhone" => ($phone_1_nr ? $phone_1_nr :''),
			        "PrivatePhone" => ($phone_2_nr ? $phone_2_nr :''),
			        "Fax" => ($fax ? $fax :''),
			        "MobilePhone" => ($cellphone_1_nr ? $cellphone_1_nr :''),
			        "EmailAddress" => ($email ? $email :''),
			        "DepartmentId" => $dept_nr,
			        "DepartmentName" => $dept_name,
                 );
		
		return $data;
	}


	function getEncounterdataArray($dataarr){
		
		extract($dataarr);

		$data = array(
                    "CaseNumber" => $encounter_nr,
        			"PatientId" => $pid,
        			"DepartmentId" => $dept_nr,
                 );
		
		return $data;
	}

}