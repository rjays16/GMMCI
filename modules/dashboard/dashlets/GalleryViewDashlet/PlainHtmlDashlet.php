<?php
require './roots.php';
require_once $root_path.'include/care_api_classes/dashboard/Dashlet.php';
require_once $root_path.'gui/smarty_template/smarty_care.class.php';

/**
* The Plain HTML Dashlet allows simple adding of HTML
*/
class PlainHtmlDashlet extends Dashlet {

	const PREF_HEIGHT 		= 'content.height';
	const PREF_MAXLENGTH 	= 'maxLength';
	const PREF_CONTENT 		= 'content';


	/**
	* Constructor
	*/
	public function __construct( $id=null, $preferences=null ) {
		$this->fqcn = __CLASS__;

		// generate id for this Dashlet
		if ($id)
		{
			$this->id = $id;
		}
		else
		{
			$this->id = create_guid();
		}

		// setup Session object
		$this->session = new DashletSession($this);

		// setup Preferences object
		$this->preferences = new DashletPreferences($this, Array(
			// Default dashlet settings
			'title' 				=> $this->name,
			'contentHeight' => 'auto',

			// Default settings specifcally for this Dashlet
			'maxLength' 		=> 255,
			'content' 			=> 'Plain old HTML'
		));


		// set Dashlet mode to VIEW
		$this->mode = DashletMode::getViewMode();
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
			//$response->alert("Saving:".print_r($action->getParameters(), true));
			$data = (array) $action->getParameter('data');

			foreach ($data as $i=>$item)
			{
				if ($item['name'] == 'content')
				{
					$content = $item['value'];
				}
			}

			$this->preferences->set('content', $content);

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
		return $response;
	}



	/**
	* put your comment there...
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
			$mode = $this->mode;
		}


		if ($mode->is(DashletMode::VIEW_MODE))
		{
			return $this->preferences->get('content');
		}
		elseif ($mode->is(DashletMode::EDIT_MODE))
		{
			$smarty = new smarty_care('common');

			$dashletSmarty = array(
				'id' => $this->id
			);

			$preferencesSmarty = Array(
				'content' => $this->preferences->get('content')
			);

			$smarty->assign('dashlet', $dashletSmarty);
			$smarty->assign('preferences', $preferencesSmarty);

			return $smarty->fetch($root_path.'modules/dashboard/dashlets/PlainHtmlDashlet/templates/config.tpl');
		}
		else
		{
			return 'Mode not supported';
		}
	}

}
