<?php
require './roots.php';
require_once $root_path.'include/care_api_classes/dashboard/Dashlet.php';
require_once $root_path.'include/care_api_classes/dashboard/DashletSession.php';
require_once $root_path.'gui/smarty_template/smarty_care.class.php';
require_once($root_path.'include/care_api_classes/class_department.php');
require_once($root_path.'include/care_api_classes/class_encounter.php');    


/**
* Dashlet for Prescriptions
*/
class PatientInformation extends Dashlet {

	protected static $name 	= 'Patient Information';
	protected static $icon 	= 'info.png';
	protected static $group = 'PatientFile';

	/**
	* Constructor
	*
	*/
	public function __construct( $id=null )
	{
		parent::__construct( $id );
	}


	public function init()
	{
		parent::init(Array(
			'contentHeight' => 'auto'
		));
	}


	/**
	* Processes an Action sent by the client
	*
	*/
	public function processAction( DashletAction $action )
	{
		global $db;
		$response = new DashletResponse;
		if ($action->is('save'))
		{
			$data = (array) $action->getParameter('data');
			foreach ($data as $i=>$item)
			{
				if ($item['name'] == 'pageSize')
				{
					$pageSize = $item['value'];
				}
			}
			$this->preferences->set('pageSize', $pageSize);
			$this->setMode(DashletMode::getViewMode());
			$updateOk = $this->update();

			if (false !== $updateOk)
			{
				$response->call("Dashboard.dashlets.refresh", $this->getId());
			}
			else
			{
				$response->alert('Error saving: '.$query);
			}
		}
		elseif($action->is('setDoctors'))
		{
			global $db;
			$response = new DashletResponse;

			$sql = "SELECT personell_nr FROM care_users WHERE login_id=".$db->qstr($_SESSION["sess_temp_userid"]);
			$personell_nr = $db->GetOne($sql);
			$userid = $_SESSION["sess_temp_userid"];
			$encounter_nr = $action->getParameter('data');
			$or = $action->getParameter('or');

			$index="encounter_nr, or_no, doctor_nr, create_id, create_time,history";
			$values ="'$encounter_nr','$or','$personell_nr','$userid',NOW(),CONCAT('Create: ',NOW(),' [$userid]\\n')";
 
			$sql2 = "INSERT INTO seg_doctors_co_manage ($index) VALUES ($values)";
			if($db->Execute($sql2)){
				$response->call("Dashboard.dashlets.refresh", $this->getId());
			}else{ 
				$response->alert('Error saving: '.$sql2);
			}
		}
		elseif($action->is('unsetDoctors'))
		{
			global $db;
			$response = new DashletResponse;

			$sql = "SELECT personell_nr FROM care_users WHERE login_id=".$db->qstr($_SESSION["sess_temp_userid"]);
			$personell_nr = $db->GetOne($sql);
			$userid = $_SESSION["sess_temp_userid"];
			$encounter_nr = $action->getParameter('data');

			$sql2 = "UPDATE seg_doctors_co_manage SET
					is_deleted = 1 ,
					or_no = '',
					modify_id = '$userid',
					history = CONCAT(history,'UPDATE: ',NOW(),' [$userid]\\n')
					WHERE is_deleted=0 AND doctor_nr='$personell_nr' AND encounter_nr=".$db->qstr($encounter_nr);

			if($db->Execute($sql2)){
				$response->call("Dashboard.dashlets.refresh", $this->getId());
			}else{ 
				$response->alert('Error saving: '.$sql2);
			}
		}
		elseif($action->is('referPatient'))
		{
			global $db;
			$enc_obj=new Encounter();
			$response = new DashletResponse;

			$pid = $action->getParameter('pid');
			$enc =	$action->getParameter('enc');
			$sql = "SELECT referral_nr FROM seg_referral WHERE encounter_nr='$enc' ORDER BY create_time DESC";
			$res = $db->Execute($sql);
			if($res && $row = $res->FetchRow()){
				$referral_nr = $db->GetOne($sql)+1;
			}else{
				$referral_nr = $pid.'1';
			}	
			
			$ReferArray['referral_nr'] = $referral_nr;
			$ReferArray['encounter_nr'] = $enc;
			$ReferArray['referrer_dr'] = $action->getParameter('doc_nr');
			$ReferArray['referrer_dept'] = $action->getParameter('dept');
			$ReferArray['reason_referral_nr'] = $action->getParameter('reason');
			$ReferArray['userid'] = $_SESSION["sess_temp_userid"];

			$ok = $enc_obj->saveReferral(&$ReferArray);

			if($ok){
				$response->alert('Successfully Saved The Referral');
				
				$response->call("Dashboard.dashlets.refresh", $this->getId());
				$response->call("refreshReferral");
			}else{ 
				$response->alert('Referral Not Saved');
			}
		}
		elseif($action->is('undoReferPatient'))
		{
			global $db;
			$response = new DashletResponse;
			$ref = $action->getParameter('ref');
			
			$sql = "DELETE FROM seg_referral WHERE referral_nr='$ref'";
			
			if($db->Execute($sql)){
				$response->call("Dashboard.dashlets.refresh", $this->getId());
				$response->call("refreshReferral");
			}else{ 
				$response->alert('Error saving: '.$sql);
			}
		}
		elseif($action->is('updateSmokerDrinkerData'))
		{
			global $db;
			$enc_obj=new Encounter();
			$response = new DashletResponse;

			$userid = $_SESSION["sess_temp_userid"];
			$encounter_nr = $action->getParameter('enc');
			$smoker = $action->getParameter('smoker');
			$drinker = $action->getParameter('drinker');

			$sql2 = "UPDATE care_encounter SET
					smoker_history = '$smoker',
					drinker_history = '$drinker',
					history = CONCAT('UPDATE: ',NOW(),' [$userid]\\n')
					WHERE encounter_nr=".$db->qstr($encounter_nr);

			if($db->Execute($sql2)){
				$response->alert('Successfully Saved');
				$response->call("Dashboard.dashlets.refresh", $this->getId());
			}else{ 
				$response->alert('Error saving: '.$sql2);
			}
		}	
		else {
			$response->extend( parent::processAction($action) );
		}

		return $response;
	}



	/**
	* Processes a Render request and returns the output
	*
	*/
	public function render($renderParams=null) {
		global $root_path, $db;
		$mode = $this->getMode();
		$dept_obj=new Department;

		if ($mode->is(DashletMode::VIEW_MODE))
		{
			$smarty = new smarty_care('common');
			$dashletSmarty = Array(
				'id' => $this->getId()
			);
			$smarty->assign('dashlet', $dashletSmarty);


			$session = DashletSession::getInstance(DashletSession::SCOPE_DASHBOARD, $_SESSION['activeDashboard']);
			$file = $session->get('ActivePatientFile');

			$query = "SELECT p.pid, e.encounter_nr, fn_get_person_name(p.pid) `fullname`, p.sex, e.current_att_dr_nr,\n".
					"e.official_receipt_nr `or`, fn_get_complete_address(p.pid) `address`, fn_get_age(DATE(NOW()), date_birth) `age`,\n".
					"e.chief_complaint, e.smoker_history, e.drinker_history, e.encounter_type\n".
				"FROM care_encounter e\n".
					"INNER JOIN care_person p ON p.pid=e.pid\n".
				"WHERE e.encounter_nr=".$db->qstr($file);

			$row = $db->GetRow($query);

			$sql = "SELECT personell_nr FROM care_users WHERE login_id=".$db->qstr($_SESSION["sess_temp_userid"]);
			$personell_nr = $db->GetOne($sql);

			$patientSmarty = Array(
				'pid' => $row['pid'],
				'encounter' => $row['encounter_nr'],
				'fullname' => $row['fullname'],
				'address' => $row['address'],
				'age' => $row['age'],
				'complaint' => $row['chief_complaint'],
				'or' => $row['or'],
				'doc_nr' => $personell_nr
			);
			if (strtoupper($row['sex']) == 'M')
				$patientSmarty['gender'] = 'Male';
			elseif (strtoupper($row['sex']) == 'F')
				$patientSmarty['gender'] = 'Female';

			if($row['smoker_history']=='yes'){
				$smokerYes = 'checked';
				$patientSmarty['smoker'] = 'Yes';
			}elseif($row['smoker_history']=='no'){
				$smokerNo = 'checked';
				$patientSmarty['smoker'] = 'No';
			}elseif($row['smoker_history']=='na'){
				$smokerNa = 'checked';
				$patientSmarty['smoker'] = 'N/A';
			}

			if($row['drinker_history']=='yes'){
				$drinkerYes = 'checked';
				$patientSmarty['drinker'] = 'Yes';
			}elseif($row['drinker_history']=='no'){
				$drinkerNo = 'checked';
				$patientSmarty['drinker'] = 'No';
			}elseif($row['drinker_history']=='na'){
				$drinkerNa = 'checked';
				$patientSmarty['drinker'] = 'N/A';
			}

			$smarty->assign('pat', $patientSmarty);

			if($file!==NULL) {
					$smarty->assign('disable', '');
					$disable = '';
					$show = "display:''";
			} else {
				$smarty->assign('disable', 'disabled="disabled"');
				$disable = 'disabled="disabled"';
				$show = "display:none";
			}

			if($row['encounter_type']==2){
				$show1 = "display:''";
			}else{
				$show =	 "display:none";
				$show1 = "display:none";
			}

			$smarty->assign('encounterNr', '<input type="hidden" id="encounterNr" name="encounterNr" value="'.$file.'"/>');
			$smarty->assign('URL_APPEND', URL_APPEND);
			
			$sql1 = "SELECT * FROM seg_doctors_co_manage WHERE is_deleted=0 AND doctor_nr='$personell_nr' AND encounter_nr=".$db->qstr($file);
			$result = $db->Execute($sql1);	
			if ($result){
			    if($rows = $result->FetchRow()){
				    if(date("m/d/Y",strtotime($rows['create_time']))==date("m/d/Y")){
				    	$smarty->assign('btn_untagDoctor','<button class="button" '.$disable.' style="'.$show1.'" onclick="PatientHistory_unsetDoctors();return false;"><img src="../../gui/img/common/default/forums.gif"/>Undo Tag Patient</button>');
				    }
			    }else{
			    	$smarty->assign('btn_tagDoctor','<button class="button" '.$disable.' style="'.$show1.'" onclick="PatientHistory_setDoctors();return false;"><img src="../../gui/img/common/default/forums.gif"/>Tag My Patient</button>');
			    }
			}

			$sql2 = "SELECT fn_get_personell_name(doctor_nr) `doctor` FROM seg_doctors_co_manage WHERE is_deleted=0 AND encounter_nr=".$db->qstr($file);
			$result2 = $db->Execute($sql2);
			if($result2){
				$doctors ='';
				while($rows2 = $result2->FetchRow()){
					$doctors .="<li><span>Dr. ".$rows2['doctor']."</span></li>";
        		}
			}
			if($doctors!=''){
				$doctor_input = $doctors;
			}else{
				$doctor_input = "<li><span>Don\'t have Doctor yet.</span></li>";
			}
		
			$attributes .= 'onmouseover="return overlib(\''.$doctor_input.'\', CAPTION,\'Doctors\', BORDER,0,TEXTPADDING,5, TEXTFONTCLASS,\'oltxt\', CAPTIONFONTCLASS,\'olcap\',WIDTH,300, FGCLASS,\'olfgPopup\');"'; 
        	        $attributes .= 'onMouseout ="return nd();"';
			
			$smarty->assign('doctors','<span style="font:bold 12px Verdana; '.$show.'"><img class="link" '.$attributes.' src="../../gui/img/common/default/forums.gif">Doctors</span>');
			$dept = array();
			$sqlReferral = "SELECT * FROM seg_referral WHERE encounter_nr='$file' AND referrer_dr='$personell_nr'";
			$res = $db->Execute($sqlReferral);
			while($res && $row = $res->FetchRow()){
				$dept[] = $row['referrer_dept'];
			}
			
			if($dept)
				$smarty->assign('refer','<button class="button" '.$disable.' style="'.$show1.'" onclick="showHistoryReferral('.$personell_nr.');return false;"><img src="../../gui/img/common/default/patient.png"/>Show Referral History</button>');
			else
				$smarty->assign('refer','<button class="button" '.$disable.' style="'.$show1.'" onclick="PatientHistory_referPatient('.$personell_nr.');return false;"><img src="../../gui/img/common/default/patient.png"/>Refer Patient</button>');
			

			$result = $dept_obj->getAllOPDMedicalObject(0);
			$deptlist = "<option value='' selected>-Select a Department-</option>";
			while($result && $row = $result->FetchRow()){
				if (!in_array($row["nr"], $dept)) {
					$deptlist = $deptlist ."<option value='" .$row["nr"]. "' >" .$row["name_formal"]. "</option>";
				}
			}
	
	
			/*$sql = "SELECT * FROM seg_referral_from";
			$res = $db->Execute($sql);
			$hospital = "<option value=0>-Select a Hospital-</option>";
			while($res && $row = $res->FetchRow()){
				$hospital = $hospital ."<option value='" .$row["id"]. "' >" .$row["referral"]. "</option>";
			}*/

			$sqlReason = "SELECT * FROM seg_referral_reason ORDER BY reason";
			$res = $db->Execute($sqlReason);
			$reason = "<option value='' selected>-Select a Reason-</option>";
			while($res && $row = $res->FetchRow()){
				$reason = $reason ."<option value='" .$row["id"]. "' >" .$row["reason"]. "</option>";
			}

			/*$smarty->assign('sReferType', '<select class="segInput" name="ReferType">
											<option value="0">Department</option>
											<option value="1">Hospital-</option>
											</select>');*/
			$smarty->assign('sDept', '<select  name="department" id="department" class="segInput" onchange="assignValue(1,this.value);">'.$deptlist.'</select><input name="dept" id="dept" type="hidden">');
			//$smarty->assign("sHospital", '<select class="segInput" name="hospital" style="display:none">'.$hospital.'</select>');
			$smarty->assign("sReason", '<select class="segInput" name="referral_reason" id="referral_reason" onchange="assignValue(2,this.value);" >'.$reason.'</select><input name="reason" id="reason" type="hidden">');
			
			$smarty->assign('updateSmokerDrinkerData','<button class="button" '.$disable.' onclick="PatientHistory_updateSmokerDrinkerData();return false;"><img src="../../gui/img/common/default/page_edit.png"/>Update Smoker/Drinker Data</button>');
			$smarty->assign('sSmoker','<td class="adm_input" colspan="2">
															<input id="smoker_yes" type="radio" '.$smokerYes.' value="yes" name="smoker">
															YES
															<input id="smoker_no" type="radio" '.$smokerNo.' value="no" name="smoker">
															NO
															<input id="smoker_na" type="radio" '.$smokerNa.' value="na" name="smoker">
															N/A
														</td>');
			$smarty->assign('sDrinker','<td class="adm_input" colspan="2">
											<input id="drinker_yes" type="radio" '.$drinkerYes.' value="yes" name="drinker">
											YES
											<input id="drinker_no" type="radio" '.$drinkerNo.' value="no" name="drinker">
											NO
											<input id="drinker_na" type="radio" '.$drinkerNa.' value="na" name="drinker">
											N/A
										</td>');
			
			return $smarty->fetch($root_path.'modules/dashboard/dashlets/PatientInformation/templates/View.tpl');
		}
//		elseif ($mode->is(DashletMode::EDIT_MODE))
//		{
//			$smarty = new smarty_care('common');
//			$dashletSmarty = array(
//				'id' => $this->getId()
//			);
//			$smarty->assign('dashlet', $dashletSmarty);
//			$preferencesSmarty = Array(
//				'pageSize' => $this->preferences->get('pageSize')
//			);
//			$smarty->assign('settings', $preferencesSmarty);
//			return $smarty->fetch($root_path.'modules/dashboard/dashlets/PatientList/templates/config.tpl');
//		}
//		else
//		{
//			return 'Mode not supported';
//		}
		else
		{
			return parent::render($renderParams);
		}
	}

}
