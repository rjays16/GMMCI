<?php
require './roots.php';
require_once $root_path.'include/care_api_classes/dashboard/Dashlet.php';
require_once $root_path.'gui/smarty_template/smarty_care.class.php';

/**
* Dashlet for Prescriptions
*/
class BlankDashlet extends Dashlet {

	protected static $name = 'Blank Dashlet';
	protected static $icon = 'add.png';


	/**
	* Constructor
	*
	*/
	public function __construct( $id=null ) {

		parent::__construct( $id );
	}


	public function init()
	{
		parent::init( Array(
			'contentHeight' => 'auto',
			// Maximum number of recently encoded prescriptions to show on the Dashlet
			'setting'			=> 1,
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

			$response->alert("Saved!");
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
			return '<div>This is view mode! <button onclick="Dashboard.dashlets.sendAction(\''.$this->getId().'\', \'save\', [] ); return false;">Save</button></div>';
		}
		elseif ($mode->is(DashletMode::EDIT_MODE))
		{
			return '<div>This is edit mode! <button onclick="Dashboard.dashlets.sendAction(\''.$this->getId().'\', \'setMode\', [\'view\'] ); return false;">View Mode</button></div>';
		}
		else
		{
			return parent::render($renderParams);
		}
	}

}
