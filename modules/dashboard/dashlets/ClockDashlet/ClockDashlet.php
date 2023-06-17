<?php
require './roots.php';
require_once $root_path.'include/care_api_classes/dashboard/Dashlet.php';
require_once $root_path.'gui/smarty_template/smarty_care.class.php';

/**
* The Plain HTML Dashlet allows simple adding of HTML
*/
class ClockDashlet extends Dashlet {

	protected static $name= 'Clock';
	protected static $icon = 'clock.png';

	/**
	* Constructor
	*
	* @param mixed $id The id that will be assigned to this dashlet
	* @param DeashletPreferences $preferences The Preferences to be loaded into the Dashlet
	* @return PlainHtmlDashlet
	*/
	public function __construct( $id=null ) {
		parent::__construct( $id );
	}



	public function init()
	{
		parent::init( Array(

			// The height of the content area of the dashlet, could have any values that are valid values
			// for the CSS `height` attribute
			'contentHeight' => 'auto',

			// Default settings specifcally used for this Dashlet
			'clockSkin' 				=> 'swissRail',
			'clockRadius' 			=> 80,
			'showDigital' 			=> 1,


		));
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
				if ($item['name'] == 'skin')
					$skin = $item['value'];
				if ($item['name'] == 'radius')
					$radius = $item['value'];
				if ($item['name'] == 'digital')
					$digital = $item['value'];
			}

			// Update dashlet configuration
			$this->preferences->set('clockSkin', $skin);
			$this->preferences->set('clockRadius', $radius);
			$this->preferences->set('showDigital', ($digital=='1') ? 1 : 0);

			$this->setMode(DashletMode::getViewMode());
			$ok = $this->update();

			if (false !== $ok)
				$response->call("Dashboard.dashlets.refresh", $this->getId());
			else
				$response->alert('Error saving: '.$query);
		}
		else
		{
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

		$mode = $this->getMode();
		if ($mode->is(DashletMode::VIEW_MODE))
		{
			$smarty = new smarty_care('common');

			$dashletSmarty = array(
				'id' => $this->getId()
			);


			$preferencesSmarty = Array(
				'clockSkin' => $this->getPreferences()->get('clockSkin'),
				'clockRadius' => $this->getPreferences()->get('clockRadius'),
				'showDigital' => $this->getPreferences()->get('showDigital'),
			);

			$smarty->assign('dashlet', $dashletSmarty);
			$smarty->assign('settings', $preferencesSmarty);
			return $smarty->fetch($root_path.'modules/dashboard/dashlets/ClockDashlet/templates/View.tpl');
		}
		elseif ($mode->is(DashletMode::EDIT_MODE))
		{
			$smarty = new smarty_care('common');

			$dashletSmarty = array(
				'id' => $this->getId()
			);

			$preferencesSmarty = Array(
				'clockSkin' => $this->getPreferences()->get('clockSkin'),
				'clockRadius' => $this->getPreferences()->get('clockRadius'),
				'showDigital' => $this->getPreferences()->get('showDigital'),
			);
			$smarty->assign('skins', Array(
				'swissRail',
				'chunkySwiss',
				'chunkySwissOnBlack',
				'fancy',
				'machine',
				'simonbaird_com',
				'classic',
				'modern',
				'simple',
				'securephp',
				'Tes2',
				'Lev',
				'Sand',
				'Sun',
				'Tor',
				'Cold',
				'Babosa',
				'Tumb',
				'Stone',
				'Disc',
				'watermelon'
			));

			$smarty->assign('dashlet', $dashletSmarty);
			$smarty->assign('settings', $preferencesSmarty);

			//return var_export($preferencesSmarty, true);
			return $smarty->fetch($root_path.'modules/dashboard/dashlets/ClockDashlet/templates/Config.tpl');
		}
		else
		{
			return parent::render($renderParams);
		}
	}

}
