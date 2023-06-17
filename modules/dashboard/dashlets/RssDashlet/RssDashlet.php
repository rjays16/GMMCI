<?php
require './roots.php';
require_once $root_path.'include/care_api_classes/dashboard/Dashlet.php';
require_once $root_path.'gui/smarty_template/smarty_care.class.php';

/**
* RSS Dashlet for
*/
class RssDashlet extends Dashlet {
	protected static $name = 'RSS Reader';
	protected static $icon = 'rss.png';

	/**
	* Constructor
	*/
	public function __construct( $id=null ) {

		parent::__construct( $id );
	}


	/**
	* put your comment there...
	*
	*/
	public function init()
	{
		parent::init( Array(
			'contentHeight' => 'auto',
			'url' 					=> '',
			'maxItems'			=> 5
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

			$data = (array) $action->getParameter('data');
			foreach ($data as $i=>$item)
			{
				if ($item['name'] == 'url')
				{
					$url = $item['value'];
				}
				elseif ($item['name'] == 'maxItems')
				{
					$maxItems = $item['value'];
				}
			}

			// Update dashlet configuration
			$this->preferences->set('url', $url);
			$this->preferences->set('maxItems', $maxItems);
			$this->setMode(DashletMode::getViewMode());
			$ok = $this->update();

			if (false !== $ok)
			{
				$response->call("Dashboard.dashlets.refresh", $this->getId());
			}
			else
			{
				$response->alert('Error saving: '.$query);
			}
		}
		else
		{
			$response->extend( parent::processAction($action) );
		}
		return $response;
	}



	/**
	* put your comment there...
	*
	*/
	public function render($renderParams=null) {
		global $root_path;

		$mode = $this->getMode();

		if ($mode->is(DashletMode::VIEW_MODE))
		{
			require_once 'RssLib.php';

			$smarty = new smarty_care('common');
			$rss = @RSS_Display( $this->preferences->get('url'), $this->preferences->get('maxItems'));

			$smarty->assign('rssFeed', $rss);
			return $smarty->fetch($root_path.'modules/dashboard/dashlets/RssDashlet/templates/rss.tpl');
		}
		elseif ($mode->is(DashletMode::EDIT_MODE))
		{
			$smarty = new smarty_care('common');

			$dashletSmarty = array(
				'id' => $this->id
			);

			$preferencesSmarty = Array(
				'url' => $this->preferences->get('url'),
				'maxItems' => $this->preferences->get('maxItems'),
			);

			$smarty->assign('dashlet', $dashletSmarty);
			$smarty->assign('preferences', $preferencesSmarty);
			return $smarty->fetch($root_path.'modules/dashboard/dashlets/RssDashlet/templates/config.tpl');
		}
		else
		{
			return parent::render($renderParams);
		}
	}

}
