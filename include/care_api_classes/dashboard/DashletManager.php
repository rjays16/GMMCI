<?php

require './roots.php';
require_once $root_path.'include/care_api_classes/dashboard/Dashboard.php';
require_once $root_path.'include/care_api_classes/dashboard/Dashlet.php';
require_once $root_path.'include/care_api_classes/dashboard/DashletException.php';
require_once $root_path.'include/care_api_classes/class_core.php';

/**
*
*
*/

class DashletManager
{

	/**
	* @var DashletManager Singleton instance for this class
	*/
	private static $instance;

	/**
	* @var Array The list of usable Dashlet prototypes
	*/
	private $prototypes;


	/**
	* @var mixed List of instantiated Dashlets
	*/
	private $dashlets;


	/**
	* Default constructor. Set to private to prevent external instantiation.
	*
	*/
	private function __construct()
	{
		global $db;
		$this->dashlets = Array();

		$core = new Core;
		$core->setTable('seg_dashlet_classes', $fetchMetadata=true);

		$prototypes = $core->fetchAll('NOT inactive', 'category, name');

		$this->prototypes = Array();

		if (is_array($prototypes))
		{
			foreach ($prototypes as $p)
			{
				$this->prototypes[$p['id']] = Array(
					'id' => $p['id'],
					'name' => $p['name'],
					'icon' => $p['icon'],
					'category' => $p['category'],
					'classPath' => $p['class_path'],
					'classFile' => $p['class_file']
				);
			}
		}
	}



	/**
	* Returns the Singleton instance of the DashletManager.
	*
	* Creates the instance if it has not been created yet or simply return
	* the instance reference if it has already been created.
	*
	*/
	public static function getInstance()
	{

		if (!isset(self::$instance))
		{
			self::$instance = new DashletManager;
		}
		return self::$instance;
	}



	/**
	* Returns the full list of Dashlets handled by the Manager
	*
	*/
	public function getDashlets()
	{
		return $this->prototypes;
	}



	/**
	* Creates a new instance of a Dashlet
	*
	* The argument $name specifies the class name of the Dashlet to be instantiated. The method
	* loads the Dashlet based on the list of Classes handled by the Dashlet Manager.
	*
	* @throws DashletException
	*/
	public function createDashlet( $name )
	{
		global $root_path;

		$prototype = $this->prototypes[$name];
		include_once $root_path.$prototype['classPath'].$prototype['classFile'];
		if (class_exists($name))
		{
			$dashlet = new $name;
			$dashlet->init();
			$this->dashlets[ $dashlet->getId() ] = $dashlet;
			return $dashlet;
		}
		else
		{
			throw new DashletException(DashletException::EXCEPTION_INVALID_CLASS);
			return false;
		}

	}



	/**
	* Saves the Dashlet data into the database.
	*
	* Aside from the Dashlet object, the method requires the Dashboard object  and two other values which specify the
	* location of the Dashlet within the Dashboard.
	*
	* @param Dashlet $dashlet The Dashlet object
	* @param Dashboard $dashboard The Dashboard object
	* @param mixed $column The column number where the Dashlet is located
	* @param mixed $rank The position of the Dashlet with respect to the other Dashlets in the same column
	*/
	public function saveDashlet( Dashlet $dashlet, Dashboard $dashboard, $column=0, $rank=0 )
	{
		global $db;

		$core =  new Core;
		$core->setTable('seg_dashlets', $fetchMetadata=true);

		$data = array(
			'id' 					=> $dashlet->getId(),
			'class_name' 	=> $dashlet->getClassName(),
			'title'				=> $dashlet->getTitle(),
			'preferences' => $dashlet->getPreferences()->pack(),
			'mode'				=> $dashlet->getMode()->getName(),
			'state'				=> $dashlet->getState()->getName(),
			'dashboard'		=> $dashboard->getId(),
			'column_no'		=> $column,
			'rank'				=> $rank,
			'is_deleted'	=> 0
		);

		$saveOk = $core->save($data);
		return $saveOk;
	}


	/**
	* Loads a Dashlet from the database
	*
	* @param String $id The id of the Dashlet to be loaded
	* @return Dashlet A new instantation of the saved Dashlet based on the Dashlet's saved information
	* @throws DashletException
	*/
	public function loadDashlet( $id )
	{
		global $db, $root_path;

		// return the Dashlet if it is already instantiated
		if ($this->dashlets[$id])
		{
			return $this->dashlets[$id];
		}

		$core = new Core;
		$core->setTable('seg_dashlets', $fetchMetadata=true);
		$row = $core->fetch(Array('id'=>$id));

		if ($row !== false)
		{
			$prototype = $this->prototypes[ $row['class_name'] ];
			include_once $root_path.$prototype['classPath'].$prototype['classFile'];
			if (class_exists($row['class_name']))
			{
				$dashlet = new $row['class_name']($id);
				$dashlet->load();

				return $dashlet;
			}
			else
			{
				throw new DashletException(DashletException::EXCEPTION_INVALID_CLASS);
				return false;
			}

		}
		else
		{
			// No persistent data found for the specified Dashlet id
			throw new DashletException(DashletException::EXCEPTION_DB_ERROR);
			return false;
		}
	}



}