<?php
require('./roots.php');
require_once($root_path.'include/inc_environment_global.php');
include_once($root_path.'include/inc_date_format_functions.php');
require($root_path."modules/repgen/ajax/repgen_common_ajx.php");

function ProcessReportType($reptype){
		global $db;
		$objResponse = new xajaxResponse();

		$sql = "SELECT rep_nr, rep_name, rep_script, rep_dept_nr, rep_type FROM seg_reptbl
						WHERE rep_type = '".$reptype."' ORDER by rep_name ASC;";
		 //$objResponse->alert("sql= ".$sql);
		 if($result = $db->Execute($sql)){
				$objResponse->clear('report_nr','innerHTML');
				$string="<option value='0'>-Select Report-</option>";
				while($row = $result->FetchRow()){
						$string .= '<option value="'.$row['rep_nr'].'">'.$row['rep_name'].'</option>';
				}


				$objResponse->assign("report_nr", "innerHTML", $string);

		 }else{
				 $objResponse->alert("No record exists.");
		 }

		return $objResponse;
}

#---added by CHA 09-01-09
function setCodes($code_type)
{
		global $db;
		$objResponse = new xajaxResponse();

		if($code_type=='ICD10')
		{
			 $sql="SELECT DISTINCT d.diagnosis_code, d.description, IF(instr(d.diagnosis_code,'.'),substr(d.diagnosis_code,1,IF(instr(d.diagnosis_code,'.'),instr(d.diagnosis_code,'.')-1,0)),
					d.diagnosis_code) AS code_parent,(SELECT description FROM care_icd10_en AS i WHERE i.diagnosis_code=(IF(instr(d.diagnosis_code,'.'),
					substr(d.diagnosis_code,1,IF(instr(d.diagnosis_code,'.'),instr(d.diagnosis_code,'.')-1,0)), d.diagnosis_code) )) AS description2
					FROM care_icd10_en as d WHERE IF(instr(d.diagnosis_code,'.'), substr(d.diagnosis_code,1,IF(instr(d.diagnosis_code,'.'),instr(d.diagnosis_code,'.')-1,0)),
					d.diagnosis_code) <> '' ORDER BY d.diagnosis_code";
					if($result = $db->Execute($sql)){
						$objResponse->clear('icd_code','innerHTML');
						$string="<option value='all'>-All-</option>";
						while($row = $result->FetchRow())
						{
								$string .= '<option value="'.$row['diagnosis_code'].'" onmouseover="mouseOver(this,\''.$row['diagnosis_code'].'\');" onMouseout="return nd();">'.$row['diagnosis_code'].'</option>';
								//echo '<input type="hidden" id="code'.$row["diagnosis_code"].'" name="code'.$row["diagnosis_code"].'" value="'.$row["description"].'">';
						}
						$objResponse->assign("icd_code", "innerHTML", $string);

				 }else{
						 $objResponse->alert("No record exists.");
				 }
		}
		else if($code_type=='ICP')
		{
			 $sql="SELECT DISTINCT d.code, d.description, IF(instr(d.code,'.'),substr(d.code,1,IF(instr(d.code,'.'),instr(d.code,'.')-1,0)),
					d.code) AS code_parent,(SELECT description FROM care_ops301_en AS i WHERE i.code=(IF(instr(d.code,'.'),
					substr(d.code,1,IF(instr(d.code,'.'),instr(d.code,'.')-1,0)), d.code) )) AS description2 FROM care_ops301_en as d
					WHERE IF(instr(d.code,'.'), substr(d.code,1,IF(instr(d.code,'.'),instr(d.code,'.')-1,0)),
					d.code) <> '' ORDER BY d.code";
					if($result = $db->Execute($sql)){
						$objResponse->clear('icp_code','innerHTML');
						$string="<option value='all'>-All-</option>";
						while($row = $result->FetchRow())
						{
								$string .= '<option value="'.$row['code'].'" onmouseover="mouseOver(this,\''.$row['code'].'\');" onMouseout="return nd();">'.$row['code'].'</option>';
								//echo '<input type="hidden" id="code'.$row["code"].'" name="code'.$row["code"].'" value="'.$row["description"].'">';
						}
						$objResponse->assign("icp_code", "innerHTML", $string);

				 }else{
						 $objResponse->alert("No record exists.");
				 }
		}

		return $objResponse;
}

function getICD($icd_nr)
{
				global $db;

				$objResponse = new xajaxResponse();

				$strSQL = "SELECT DISTINCT d.diagnosis_code, d.description
										FROM care_icd10_en as d
										WHERE IF(instr(d.diagnosis_code,'.'), substr(d.diagnosis_code,1,IF(instr(d.diagnosis_code,'.'),instr(d.diagnosis_code,'.')-1,0)),
										d.diagnosis_code) <> ''
										and d.diagnosis_code= $icd_nr";

				if ($result = $db->Execute($strSQL)) {
						if ($row = $result->FetchRow()) {

								$objResponse->call("setICDCode", (is_null($row['diagnosis_code']) ? 0 : $row['diagnosis_code']), (is_null($row['description']) ? '' : $row['description']));
						}
				}

				return $objResponse;
}

function getICP($icp_nr)
{
				global $db;

				$objResponse = new xajaxResponse();

				$strSQL = "SELECT DISTINCT d.code, d.description
										FROM care_ops301_en as d
										WHERE IF(instr(d.code,'.'), substr(d.code,1,IF(instr(d.code,'.'),instr(d.code,'.')-1,0)),
										d.code) <> ''
										and d.code= $icp_nr";

				if ($result = $db->Execute($strSQL)) {
						if ($row = $result->FetchRow()) {

								$objResponse->call("setICPCode", (is_null($row['code']) ? 0 : $row['code']), (is_null($row['description']) ? '' : $row['description']));
						}
				}

				return $objResponse;
}
#---end cha

	#added by VAN 09-14-2010
	function getListReport($category_code, $dept_nr){
		 global $db;
		 $objResponse = new xajaxResponse();

		 if ($category_code)
			$cond = " AND rep_category='".$category_code."' ";
		 #$objResponse->alert('sss= '.$category_code.' - '.$dept_nr);
		 $sql = "SELECT rep_nr,rep_name,rep_script,rep_dept_nr
									 FROM seg_reptbl
									 WHERE rep_dept_nr IN (".$dept_nr.")
									 $cond
									 ORDER BY rep_name";
		 #$objResponse->alert($sql);
		 if($result = $db->Execute($sql)){
				$objResponse->clear('report_nr','innerHTML');
				$string="<option value='0'>-Select Report-</option>";
				while($row = $result->FetchRow()){
					$string .= '<option value="'.$row['rep_nr'].'" >'.$row['rep_name'].'</option>';
				}
				$objResponse->assign("report_nr", "innerHTML", $string);

		 }else{
				$objResponse->alert("No record exists.");
		 }

		 return $objResponse;
	}
	#-------------

 $xajax->processRequest();

?>