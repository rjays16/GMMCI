<?php
require './roots.php';
require_once $root_path.'include/care_api_classes/dashboard/Dashlet.php';
require_once $root_path.'include/care_api_classes/dashboard/DashletSession.php';
require_once $root_path.'gui/smarty_template/smarty_care.class.php';

/**
* Dashlet for Patient Radiology Results
*/
class PatientRadioResults extends Dashlet {

	protected static $name 	= 'Radiology Results';
	protected static $icon 	= 'film.png';
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
			'pageSize'			=> 5
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
			$smarty = new smarty_care('common');
			$dashletSmarty = Array(
				'id' => $this->getId()
			);
			$smarty->assign('dashlet', $dashletSmarty);
			$preferencesSmarty = Array(
				'pageSize' => $this->preferences->get('pageSize')
			);
			$smarty->assign('settings', $preferencesSmarty);
			return $smarty->fetch($root_path.'modules/dashboard/dashlets/PatientRadioResults/templates/ListView.tpl');
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
			return $smarty->fetch($root_path.'modules/dashboard/dashlets/PatientRadioResults/templates/Config.tpl');
		}
		else
		{
			return parent::render($renderParams);
		}
	}

}
