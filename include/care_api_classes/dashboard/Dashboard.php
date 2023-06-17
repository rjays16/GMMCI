<?php

require './roots.php';
require_once $root_path.'include/care_api_classes/class_core.php';
require_once 'DashletManager.php';

/**
* The Dashboard class provides the base class for the Dashboard object.
*
*/

class Dashboard
{

	const MINIMUM_WIDTH = 20;
	const DASHBOARD_TABLE = 'seg_dashboards';

	private $id;

	private $title = '';

	private $icon = '';

	private $owner = '';

	private $position = 0;

	private $columns = 1;

	private $columnWidths;

	private $manager;

	private $dashlets;

	/**
	* Creates a new Dashboard instance.
	*
	*/
	public function __construct($id=null)
	{
		if ($id)
		{
			$this->id = $id;
		}
		else
		{
			$this->id = create_guid();
		}
		$this->dashlets = Array();
	}


	/**
	* Returns the Dashboard id
	*
	*/
	public function getId()
	{
		return $this->id;
	}


	/**
	* Returns the assigned title for the Dashboard
	*
	*/
	public function getTitle()
	{
		return $this->title;
	}


	/**
	* Assigns the title to the Dashboard
	*
	* @param mixed $title
	*/
	public function setTitle($title)
	{
		$this->title = $title;
	}


	/**
	* Returns the login name of the Dashboard's owner
	*
	*/
	public function getOwner()
	{
		return $this->owner;
	}


	/**
	* Assigns the Dashboard to a user specified by $owner
	*
	* @param mixed $owner The login id of the user assigned as the Dashboard's owner
	*/
	public function setOwner($owner)
	{
		$this->owner = $owner;
	}


	/**
	* Returns the image file name of the Dashboard's icon
	*
	*/
	public function getIcon()
	{
		return $this->icon;
	}


	/**
	* Sets the icon of the Dashboard to a specified image file
	*
	* @param mixed $icon
	*/
	public function setIcon($icon)
	{
		$this->icon = $icon;
	}


	/**
	* put your comment there...
	*
	*/
	public function getPosition()
	{
		return $this->position;
	}


	/**
	* Returns the number of columns configured for the Dashboard
	*
	*/
	public function getColumnCount()
	{
		return $this->columns;
	}


	/**
	* Reset the column count
	*
	* @param mixed $column
	*/
	public function setColumnCount($columns)
	{
		$columns = (int) $columns;

		if ($this->columns === $columns)
			return;

		if ( 1 > $columns || 3 < $column )
			return;

		// reset and evenly distribute column widths
		$this->columnWidths = Array();
		for ($i=0; $i<$columns; $i++)
		{
			$this->columnWidths[$i] = floor(100.0 / $columns);
		}

		$this->columns = $columns;
	}



	public function getColumnWidths()
	{
		return $this->columnWidths;
	}

	public function getDashlets()
	{
		return $this->dashlets;
	}


	public function setAllColumnWidths($widths)
	{
		$widths = (array) $widths;
		for ($i=0; $i<$this->columns; $i++)
		{
			$w = (float) $widths[$i];
			$this->columnWidths[$i] = $w;
		}
	}


	public function setColumnWidth($columnIndex, $width)
	{
		$columnIndex = (int) $columnIndex;
		$width = (float) $width;

		if ($columnIndex < 0 || $columnIndex > $this->columns-1)
			return;

		if ($width < self::MINIMUM_WIDTH)
			$width = self::MINIMUM_WIDTH;

		if ($width > 100-self::MINIMUM_WIDTH*2)
			$width = 100-self::MINIMUM_WIDTH*2;

		$this->columnWidths[$columnIndex] = $width;
		$this->_adjustColumnWidths();
	}


	private function _adjustColumnWidths()
	{
		$totalWidth = array_sum($this->columnWidths);
		for ($i=0; $i<$this->columns; $i++)
		{
			$this->columnWidths[$i] = ($this->columnWidths[$i]/$totalWidth) * 100.0;
		}
	}


	/**
	* Sets the ordering of the Dashboard with respect to other dashboards for the user
	*
	* @param mixed $position The new order of the dashboard, the dashboard will be displayed in order of increasing value
	*/
	public function setPosition($position)
	{
		$this->position = (int) $position;
	}



	/**
	* Adds a Dashlet to the Dashboard
	*
	* @param Dashlet $dashlet
	* @param mixed $column
	*/
	public function addDashlet(Dashlet $dashlet)
	{
		$column=0;
		if (!is_array($this->dashlets[$column]))
		{
			$this->dashlets[$column] = Array();
		}
		$this->dashlets[$column][] = $dashlet;
	}


	/**
	* Loads the Dashlets associated with this Dashboard from the database
	*
	*/
	public function loadDashlets()
	{
		global $db;

		// clear the Dashboard's dashlets
		$this->dashlets = array();

		$query = "SELECT id,column_no,rank\n".
			"FROM seg_dashlets\n".
			"WHERE NOT is_deleted AND dashboard=".$db->qstr($this->id)."\n".
			"ORDER BY column_no ASC,rank DESC";
		$rs = $db->Execute($query);

		if (false !== $rs)
		{
			$dashlets = $rs->GetRows();
			$manager = DashletManager::getInstance();
			foreach ($dashlets as $dashlet)
			{
				$column_no = (int) $dashlet['column_no'];

				// Assign Dashlets with invalid column No to column 0
				if ($column_no < 0)
					$column_no = 0;
				if ($column_no >= $this->columns)
					$olumn_no = 0;

				if (!is_array($this->dashlets[$column_no]))
				{
					$this->dashlets[$column_no] = array();
				}

				$dashletObject = $manager->loadDashlet($dashlet['id']);
				if (false !== $dashletObject)
				{
					$this->dashlets[$column_no][] = $dashletObject;
				}
			}

			return true;
		}
		else
		{
			return false;
		}

	}



	/**
	* Saves the Dashboard and its configurations parameters to the database
	*
	*/
	public function save()
	{
		global $db;

		$core = new Core;
		$core->setTable(Dashboard::DASHBOARD_TABLE, $fetchMetadata=true);

		$data = Array(
			'id' 						=> $this->id,
			'icon' 					=> $this->icon,
			'title'					=> $this->title,
			'owner' 				=> $this->owner,
			'columns' 			=> $this->columns,
			'column_widths'	=> implode("|",$this->columnWidths),
			'position'			=> $this->position
		);

		$saveOk = $core->save($data);
		if (!$saveOk)
		{
			return false;
		}

		$i=0;

		// Clear dashlets
		$query = "UPDATE seg_dashlets SET is_deleted=1 WHERE dashboard=".$db->qstr($this->id)."";
		$saveOk = $db->Execute($query);

		if (false === $saveOk)
		{
			return false;
		}

		$manager = DashletManager::getInstance();

		foreach ($this->dashlets as $column)
		{
			$rank = count($column)*10;
			foreach ($column as $dashlet)
			{
				$saveOk = $manager->saveDashlet($dashlet, $this, $i, $rank);
				if (!$saveOk)
				{
					return false;
				}
				$rank -= 10;
			}
			$i++;
		}


		return true;
	}


	/**
	* Loads a Dashboard object from the database
	*
	* @param Mixed $id
	* @param Boolean $loadDashlets Instructs the method whether or not to load the Dashlets' information from the database
	*/
	public static function loadDashboard($id, $loadDashlets=true)
	{
		global $db;

		$core = new Core();
		$core->setTable( self::DASHBOARD_TABLE, $fetchMetadata=true);
		$row = $core->fetch( Array('id'=>$id) );

		if ($row !== false)
		{
			$dashboard = new Dashboard($id);
			$dashboard->setIcon($row['icon']);
			$dashboard->setTitle($row['title']);
			$dashboard->setOwner($row['owner']);
			$dashboard->setColumnCount($row['columns']);
			$dashboard->setPosition($row['position']);
			$dashboard->setAllColumnWidths(explode("|", $row['column_widths']));

			if ($loadDashlets)
			{
				$dashboard->loadDashlets();
			}
			return $dashboard;
		}
		else
		{
			return false;
		}
	}

}