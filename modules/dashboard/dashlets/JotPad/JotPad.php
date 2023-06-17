<?php
require './roots.php';
require_once $root_path.'include/care_api_classes/dashboard/Dashlet.php';
require_once $root_path.'gui/smarty_template/smarty_care.class.php';

/**
* JotPad Dashlet
*/
class JotPad extends Dashlet {

	protected static $name = 'Jot Pad';
	protected static $icon = 'pencil_add.png';


	/**
	* Constructor
	*
	* @param mixed $id The id that will be assigned to this dashlet
	* @param DeashletPreferences $preferences The Preferences to be loaded into the Dashlet
	* @return PlainHtmlDashlet
	*/
	public function __construct( $id=null, $preferences=null ) {

		if ( !isset($preferences ) )
		{
			 $preferences = Array(

			 // The title assigned to this Dashlet
				'title' 				=> self::$name,

				// The height of the content area of the dashlet, could have any values that are valid values
				// for the CSS `height` attribute
				'contentHeight' => 'auto',

				// Default settings specifcally used for this Dashlet
				'content' 			=> ''

			);
		}


		parent::__construct( $id, $preferences );
	}


	/**
	* put your comment there...
	*
	* @param DashletAction $action
	* @return DashletResponse
	*/
	public function processAction( DashletAction $action )
	{
		global $db;

		$response = new DashletResponse;
		if ($action->is('save'))
		{
			$data = $action->getParameter('data');
			$this->preferences->set('content', $data);
			$query = "UPDATE seg_dashlets SET preferences=".$db->qstr($this->preferences->pack())." WHERE id=".$db->qstr($this->getId());
			$ok = $db->Execute($query);
			if (false !== $ok)
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
	* Render function
	*
	*
	*/
	public function render($renderParams=null) {
		global $root_path;

		if ($this->getMode()->is(DashletMode::VIEW_MODE))
		{
			$smarty = new smarty_care('common');
			$dashletSmarty = array(
				'id' => $this->getId(),
				'content' => $this->preferences->get('content')
			);
			$smarty->assign('dashlet', $dashletSmarty);
			//return $this->preferences->get('content');
			return $smarty->fetch($root_path.'modules/dashboard/dashlets/JotPad/templates/pad.tpl');
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
			return 'Mode not supported';
		}
	}

}
