<?php
/**
*
*
*/

require './roots.php';
require_once $root_path.'include/care_api_classes/class_core.php';
require_once 'DashletAction.php';
require_once 'DashletException.php';
require_once 'DashletMode.php';
require_once 'DashletState.php';
require_once 'DashletPreferences.php';
require_once 'DashletResponse.php';
//require_once 'DashletSession.php';


/**
*
*
* @package Dashboard
*/


class Dashlet
{

	const DASHLET_TABLE = 'seg_dashlets';

	/**
	* @var String Each instance of a Dashlet class must be assigned a unique id
	*/
	protected $id=null;

	/**
	* @var String Fully qualified class name for the dashlet. Defaults to 'Dashlet'
	* @deprecated Use __CLASS__ instead.
	*/
	//	protected $fqcn = 'Dashlet';

	/**
	* @var DashletSession Handles persistent data for the Dashlet
	*/
	//protected $session=null;

	/**
	* @var DashletPreferences Handles internal configuration for the dashlet
	*/
	protected $preferences=null;

	/**
	* @var DashletMode The current mode for the Dashlet
	*/
	protected $mode=null;

	/**
	* @var DashletMode The current window state for the Dashlet
	*/
	protected $state=null;

	/**
	* @var String Title for the Dashlet
	*/
	protected $title;

	/**
	* @staticvar String The proper name for this Dashlet
	*/
	protected static $name = 'Generic Dashlet';

	/**
	* @staticvar String The image file name to be used as the icon for this Dashlet
	*/
	protected static $icon = 'picture.png';

	/**
	* @staticvar String Dashlet classes can be assigned to groups. This allows the Dashboard application to process action to multiple Dashlets simultaneously.
	*/
	protected static $group = '';









	/**
	* Default constructor
	*
	* @param mixed $id
	* @return Dashlet
	*/
	public function __construct( $id=null )
	{
		// generate id for this Dashlet
		if (!$id)
		{
			$this->id = create_guid();
		}
		else {
			$this->id = $id;
		}

		// if a metadata file is found, load all definitions into the class
		$filename = basename(__FILE__, '.php').'.xml';
		if (file_exists($filename))
		{
			//$xml =
		}

	}


	/**
	* Initializes and loads the default configuration for the Dashlet. Generally called by the
	* Dashboard application after instantiation of a new Dashlet.
	*
	* @param mixed $preferences Set these preferences as the preferences object for the Dashlet
	*/
	public function init($preferences=null)
	{

		$this->title = $this->getName();

		// Setup Preferences object
		$this->preferences = new DashletPreferences();
		$this->preferences->load( $preferences );

		// Set Dashlet mode
		$this->mode = DashletMode::getViewMode();

		// Set Dashlet state
		$this->state = DashletState::getNormalState();
	}


	/**
	* Attempts to load the Dashlet's configuration from the database
	*
	* @throws DashletException
	*/
	public function load()
	{
		$this->init();

		$core = new Core;
		$core->setTable(self::DASHLET_TABLE, $fetchMetadata=true);
		$core->setupLogger();
		$row = $core->fetch( Array('id'=>$this->id) );


		if ($row !== false)	{
			$this->setTitle( $row['title'] );
			$this->getPreferences()->load( $row['preferences'] );
			$this->setMode( $row['mode'] );
			$this->setState( $row['state'] );
		}
		else
		{
			// No persistent data found for this Dashlet
		}
		return $this;
	}

	/**
	* put your comment there...
	*
	*/
	public function destroy()
	{
		//
	}


	/**
	* Returns the id of the dashlet
	*
	*/
	public function getId()
	{
		return $this->id;
	}


	/**
	* Returns the Class Name of this object
	*
	*/
	public function getClassName()
	{
		return get_class($this);
	}


	/**
	* Returns the proper name of the Dashlet.
	*
	* @todo Because of the lack of support for Static Late Binding in < PHP 5.3 versions, this method cannot
	* 	be implemented as static since the static form of this method and other similar methods will always
	* 	return the variable values defined in this base class and ignores the values overridden by extending classes.
	* @return String
	*
	*/
	public function getName()
	{
		// fix for lack of static Late binding in versions < PHP 5.3
		// if Static Late Binding is properly implemented, this can be coded simply as: return static::$name
		$vars = get_class_vars( get_class($this) );
		return $vars['name'];
	}


	/**
	* Returns the Class grouping associated for the Dashlet in array format
	*
	* @return String
	*/
	public function getGroupList()
	{
		// fix for lack of static Late binding in versions < PHP 5.3
		$vars = get_class_vars( get_class($this) );

		$groups = explode(',', $vars['group']);
		$return = array();
		foreach ($groups as $i=>$group)
		{
			$group = trim($group);
			if ($group) $return[] = $group;
		}
		return $return;
	}


	/**
	* Returns true if the Dashlet class is a member of a Class group
	*/
	public function memberOf($groupName)
	{

		// fix for lack of static Late binding in versions < PHP 5.3
		$vars = get_class_vars( get_class($this) );
		$groups = explode(',', $vars['group']);
		foreach ($groups as $i=>$group)
			$groups[$i] = trim($group);
		return in_array($groupName, $groups);
	}


	/**
	* Returns the file name of the image to be used as the class' icon
	*
	*/
	public function getIcon()
	{

		// fix for lack of static Late binding in versions < PHP 5.3
		$vars = get_class_vars( get_class($this) );
		return $vars['icon'];
	}



	/**
	* Returns the title for the Dashlet.
	*
	*/
	public function getTitle()
	{
		return $this->title;
	}


	/**
	* Sets the title for the Dashlet.
	*
	*/
	public function setTitle($title)
	{
		$this->title = $title;
	}



	/**
	* Saves the current Dashlet configuration into the database, updating its persistent information.
	*
	* Used for Dashlet updates only. Newly created dashlets need to be saved first using the DashletManager::saveDashlet.
	* This function checks if the Dashlet has a database entry and only proceeds with the update when it finds a matching
	* row.
	*
	*/
	public function update()
	{
		$core = new Core();
		$core->setTable(self::DASHLET_TABLE, $fetchMetadata=true);
		$row = $core->fetch(array('id' => $this->getId()));
		if ($row['id'])
		{
			$data = Array(
				'id' 					=> $this->getId(),
				'class_name' 	=> $this->getClassName(),
				'title'				=> $this->getTitle(),
				'preferences' => $this->getPreferences()->pack(),
				'mode'				=> $this->getMode()->getName(),
				'state'				=> $this->getState()->getName()
			);
			$saveOk = $core->save($data);
			return $saveOk;
		}
		return true;
	}


	/**
	* Default logic for processing actions
	*
	* By default, the Generic Dashlet processes a few pre-defined actions such as <code>setTitle</code>. Extending
	* classes might want to delegate the processing of these Actions types to the Generic Dashlet logic by calling
	* parent::processAction and using <code>DashletResponse::extend</code> to append the generated response to their
	* own DashletResponse object.
	*
	* Example:
	* <pre>
	* $response = new DashletResponse;
	* if ($action->is('save'))
	* {
	* 	// Do save logic
	* }
	* else {
	*   $response->extend( parent::processAction( $action ) );
	* }
	*	return $response;
	* </pre>
	*
	* @param DashletAction $action
	* @returns DashletResponse Response object
	*/
	public function processAction( DashletAction $action )
	{
		global $db;
		$response = new DashletResponse();


		if ($action->is('setTitle'))
		{
			$newTitle = $action->getParameter('title');
			$oldTitle = $this->getTitle();
			$this->setTitle($newTitle);

			$ok = $this->update();

			/**
			* @todo Relegate response as a proper DashletResponse instead letting the Dashlet class interface directly
			* 	with Dashboard.js. Suggestion: $response->refresh($this->getId())
			*/
			if ($ok)
			{
				$response->call('Dashboard.dashlets.refresh', $this->getId());
			}
			else
			{
				$response->alert("Unable to process Action::setTitle...".$db->ErrorMsg());
			}
		}
		elseif ($action->is('setState'))
		{
			$stateName = $action->getParameter('state');

			if (!$this->getState()->is($stateName))
			{
				$this->setState( $stateName );
				$this->update();
			}

			/**
			* @todo Relegate response as a proper DashletResponse instead letting the Dashlet class interface directly
			* 	with Dashboard.js. Suggestion: $response->refresh($this->getId())
			*/
			$response->call('Dashboard.dashlets.refresh', $this->getId());
		}
		elseif ($action->is('setMode'))
		{
			$modeName = $action->getParameter('mode');
			if (!$this->getMode()->is($modeName))
			{
				$this->setMode( $modeName );
				$this->update();
			}

			/**
			* @todo Relegate response as a proper DashletResponse instead letting the Dashlet class interface directly
			* 	with Dashboard.js. Suggestion: $response->refresh($this->getId())
			*/
			$response->call('Dashboard.dashlets.refresh', $this->getId());
		}
		else
		{
			$response->alert("Action ".$action->getName()." is not supported by this Dashlet...");
		}

		return $response;
	}



	/**
	* Default rendering routine for the dashlet
	*/
	public function render($renderParams=null)
	{
		return 'Mode:'.$this->mode->getName().' is not supported...<a href="#" onclick="Dashboard.dashlets.sendAction(\''.$this->getId().'\', \'setMode\')">Back</a>';
	}



	/**
	* Returns the DashletPreferences object of the Dashlet
	*
	*/
	public function getPreferences()
	{
		return $this->preferences;
	}


	/**
	* Updates the Dashlet's saved preferences which is stored in packed format under the <code>preferences</code> field.
	* @return Boolean Returns the result of the operation
	*/
	public function savePreferences()
	{
		global $db;
		$query = "UPDATE ".self::DASHLET_TABLE." SET preferences=".$db->qstr($this->preferences->pack())." WHERE id=".$db->qstr($this->getId())." LIMIT 1";
		$ok = $db->Execute($query);
		if ($ok !== false)
			return true;
		else
			return false;
	}


	/**
	* Returns the current Mode for the Dashlet
	*
	*/
	public function getMode()
	{
		return $this->mode;
	}

	/**
	* Sets the Mode of the Dashlet.
	*
	* @param $mode Set the Dashlet's mode to a mode specified by $mode. Defaults to VIEW mode if null is passed.
	*/
	public function setMode( $mode=null )
	{
		if (is_null($mode))
		{
			$this->mode = DashletMode::getViewMode();
		}
		else
		{
			$this->mode = new DashletMode( $mode );
		}
	}


	/**
	* Returns the current State for the Dashlet
	*
	*/
	public function getState()
	{
		return $this->state;
	}

	/**
	* Sets the State of the Dashlet.
	*
	* @param $mode Set the Dashlet's state to  specified by $mode. Defaults to VIEW mode if null is passed.
	*/
	public function setState( $state=null )
	{
		if (is_null($state))
		{
			$this->state = DashletState::getNormalState();
		}
		else
		{
			$this->state = new DashletState( $state );
		}
	}


	/**
	* Retrieves the DashletSession object in this Dashlet's context
	*
	*/
//	public function getSession()
//	{
//		return $this->session;
//	}



	/**
	* put your comment there...
	*
	*/
	public function getDashboardContext()
	{
		$core = new Core;
		$core->setTable(self::DASHLET_TABLE, $fetchMetadata=true);
		$row = $core->fetch(Array('id'=>$this->id));

		if ($row !== false)
		{
			$dashboard = $row['dashboard'];
			$core->setTable(Dashboard::DASHBOARD_TABLE, $fetchMetadata=true);
			$row = $core->fetch(Array('id'=>$dashboard));
			return $row;
		}
		else
		{
			//throw new DashletException( DashletException::EXCEPTION_DB_ERROR );
			return false;
		}
	}



}