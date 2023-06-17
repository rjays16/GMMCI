<?php
require './roots.php';
require_once $root_path.'include/care_api_classes/class_core.php';
require_once $root_path.'include/care_api_classes/dashboard/Dashlet.php';
require_once $root_path.'include/care_api_classes/dashboard/DashletSession.php';
require_once $root_path.'gui/smarty_template/smarty_care.class.php';

/**
* Dashlet for Doctors Notes
*/
class DoctorsNotes extends Dashlet {

	protected static $name 	= 'Notes';
	protected static $icon 	= 'forums.gif';
	protected static $group = '';

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
			'contentHeight' => 'auto',
			'pageSize'			=> 10
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
		if ($action->is("saveDrNote"))
		{
			$core = new Core();
			$core->setTable("seg_doctors_notes",TRUE);

			//prepare data array
			$data=(array)$action->getParameter("data");
			$saveData = array();
			foreach($data as $i=>$item)
			{
					$saveData[$item["name"]]=$item["value"];
			}

			$sql = "SELECT personell_nr FROM care_users WHERE login_id=".$db->qstr($_SESSION["sess_temp_userid"]);
			$personell_nr = $db->GetOne($sql);
			$saveData["personell_nr"] = $personell_nr;

			$session = DashletSession::getInstance(DashletSession::SCOPE_DASHBOARD, $_SESSION['activeDashboard']);
			$encounter_nr = $session->get('ActivePatientFile');
			$saveData["encounter_nr"] = $encounter_nr;

			//$response->alert(print_r($saveData,true));
			$saveok = $core->save($saveData);
			if($saveok===FALSE) {
					$response->alert("Error::".$core->getErrorMsg()."Query::".$core->getQuery());
			}

		}
		else if($action->is("saveDrDiagnosis")) {
			 $core = new Core();
			$core->setTable("seg_doctors_diagnosis",TRUE);

			//prepare data array
			$saveData = array();
			$data = $action->getParameter("data");
			$saveData["icd_code"] = $data;

			$sql = "SELECT personell_nr FROM care_users WHERE login_id=".$db->qstr($_SESSION["sess_temp_userid"]);
			$personell_nr = $db->GetOne($sql);
			$saveData["personell_nr"] = $personell_nr;

			$session = DashletSession::getInstance(DashletSession::SCOPE_DASHBOARD, $_SESSION['activeDashboard']);
			$encounter_nr = $session->get('ActivePatientFile');
			$saveData["encounter_nr"] = $encounter_nr;

			//$response->alert(print_r($saveData,true));
			$saveok = $core->save($saveData);
			if($saveok===FALSE) {
					$response->alert("Error::".$core->getErrorMsg()."Query::".$core->getQuery());
			} else {
				$response->call("DoctorsNotes_refreshIcdList");
			}
		}
		else if($action->is("deleteDrDiagnosis")) {
			 $core = new Core();
			$core->setTable("seg_doctors_diagnosis",TRUE);

			//prepare data array
			$pkArray = array();
			$icd_code = $action->getParameter("data");
			$pkArray["icd_code"] = $icd_code;

			$sql = "SELECT personell_nr FROM care_users WHERE login_id=".$db->qstr($_SESSION["sess_temp_userid"]);
			$personell_nr = $db->GetOne($sql);
			$pkArray["personell_nr"] = $personell_nr;

			$session = DashletSession::getInstance(DashletSession::SCOPE_DASHBOARD, $_SESSION['activeDashboard']);
			$encounter_nr = $session->get('ActivePatientFile');
			$pkArray["encounter_nr"] = $encounter_nr;

			//$response->alert(print_r($saveData,true));
			$deleteok = $core->delete($pkArray);
			if($deleteok===FALSE) {
					$response->alert("Error::".$core->getErrorMsg()."Query::".$core->getQuery());
			} else {
				$response->call("DoctorsNotes_refreshIcdList");
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
		if ( $renderParams['mode'] )
		{
			$mode = $renderParams['mode'];
		}
		else
		{
			$mode = $this->getMode();
		}
		if ($mode->is(DashletMode::VIEW_MODE))
		{
			$core = new Core();
			$core->setTable("seg_doctors_notes",TRUE);

			$smarty = new smarty_care('common');
			$dashletSmarty = Array(
				'id' => $this->getId()
			);
			$smarty->assign('dashlet', $dashletSmarty);
			$preferencesSmarty = Array(
				'pageSize' => $this->preferences->get('pageSize')
			);
			$smarty->assign('settings', $preferencesSmarty);

			$sql = "SELECT personell_nr FROM care_users WHERE login_id=".$db->qstr($_SESSION["sess_temp_userid"]);
			$personell_nr = $db->GetOne($sql);
			$session = DashletSession::getInstance(DashletSession::SCOPE_DASHBOARD, $_SESSION['activeDashboard']);
			$encounter_nr = $session->get('ActivePatientFile');
			$data = $core->fetch(array( 'personell_nr'=> $personell_nr, 'encounter_nr'=> $encounter_nr));
			$smarty->assign('data',$data);

			if($encounter_nr!==NULL) {
				return $smarty->fetch($root_path.'modules/dashboard/dashlets/DoctorsNotes/templates/View.tpl');
			} else {
				return $smarty->fetch($root_path.'modules/dashboard/dashlets/DoctorsNotes/templates/NoView.tpl');
			}

		}
		elseif ($this->getMode()->is(DashletMode::EDIT_MODE))
		{
			$smarty = new smarty_care('common');
			$dashletSmarty = array(
				'id' => $this->getId()
			);
			$smarty->assign('dashlet', $dashletSmarty);
			return $smarty->fetch($root_path.'modules/dashboard/dashlets/JotPad/templates/noEdit.tpl');
		}
		else
		{
			return parent::render($renderParams);
		}
	}

}
