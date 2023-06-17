<?php
require './roots.php';
require_once $root_path.'include/care_api_classes/dashboard/Dashlet.php';
require_once $root_path.'include/care_api_classes/dashboard/DashletSession.php';
require_once $root_path.'gui/smarty_template/smarty_care.class.php';

/**
* Dashlet for Prescriptions
*/
class RxWriter extends Dashlet {

	protected static $name = 'Rx Writer';
	protected static $icon = 'rx.png';

	/**
	* Constructor
	*
	*/
	public function __construct( $id=null ) {

		parent::__construct( $id );
	}


	public function init()
	{
		parent::init(Array(
			'contentHeight' => 'auto',
			// Maximum number of recently encoded prescriptions to show on the Dashlet
			'pageSize'			=> 10,
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

		elseif ($action->is('printRx'))
		{
			$id = (array) $action->getParameter('id');
			$encounter = (array) $action->getParameter('encounter');
            #added by VAN 10-01-2012
            $as_grp = (array) $action->getParameter('as_grp');
			$response->call("Dashboard.openWindow", Array(
				'url'=> "../../modules/prescription/seg-clinic-print-prescription.php",
				'data' => Array('prescription_id' => $id, 'encounter_nr'=>$encounter, 'as_grp'=>$as_grp)
			));
		}

		elseif ($action->is('deleteRx'))
		{
			$id = $action->getParameter('id');
			$ok = $db->Execute("UPDATE seg_prescription SET is_deleted=1 WHERE id=".$db->qstr($id));
			if ($ok)
			{
				$response->alert("Prescription entry successfully deleted...");
				$response->execute('$("rx-list-'.$this->getId().'").list.refresh()');
			}
			else
			{
				$response->alert("Error in attempting to delete entry...");
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
		global $root_path;

		$mode = $this->getMode();

		if ($mode->is(DashletMode::VIEW_MODE))
		{
			$smarty = new smarty_care('common');
			$dashletSmarty = Array(
				'id' => $this->getId()
			);
			$smarty->assign('dashlet', $dashletSmarty);
			$preferencesSmarty = Array(
				'pageSize' => $this->preferences->get('pageSize')
			);
			$smarty->assign('settings', $preferencesSmarty);
			$smarty->assign('URL_APPEND', URL_APPEND);

			$session = DashletSession::getInstance(DashletSession::SCOPE_DASHBOARD, $_SESSION['activeDashboard']);
			$encounter_nr = $session->get('ActivePatientFile');
			if($encounter_nr!==NULL) {
					$smarty->assign('disableRxWriter', '');
			} else {
				$smarty->assign('disableRxWriter', 'disabled="disabled"');
			}
			$smarty->assign('encounterNr', '<input type="hidden" id="encounterNr" name="encounterNr" value="'.$encounter_nr.'"/>');

			return $smarty->fetch($root_path.'modules/dashboard/dashlets/RxWriter/templates/View.tpl');
		}
		elseif ($mode->is(DashletMode::EDIT_MODE))
		{
			$smarty = new smarty_care('common');
			$dashletSmarty = array(
				'id' => $this->getId()
			);
			$smarty->assign('dashlet', $dashletSmarty);
			$preferencesSmarty = Array(
				'pageSize' => $this->preferences->get('pageSize')
			);
			$smarty->assign('settings', $preferencesSmarty);

			return $smarty->fetch($root_path.'modules/dashboard/dashlets/RxWriter/templates/Config.tpl');
		}
		else
		{
			return parent::render($renderParams);
		}
	}

}
