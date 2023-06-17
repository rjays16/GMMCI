<?php
require './roots.php';
require_once $root_path.'include/care_api_classes/dashboard/Dashlet.php';
require_once $root_path.'gui/smarty_template/smarty_care.class.php';

/**
* The Plain HTML Dashlet allows simple adding of HTML
*/
class PlainHtmlDashlet extends Dashlet {

	protected static $name = 'Gallery View';

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
				'content' 			=> 'Edit this dashlet to change the contents of these dashlet...'

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
	* Render function
	*
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
			return $this->preferences->get('content');
		}
		elseif ($mode->is(DashletMode::EDIT_MODE))
		{
			$smarty = new smarty_care('common');

			$dashletSmarty = array(
				'id' => $this->getId()
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
