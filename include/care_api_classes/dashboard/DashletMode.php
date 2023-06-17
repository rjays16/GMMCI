<?php
/**
*
*/
class DashletMode
{

	const EDIT_MODE 			= 'edit';
	const VIEW_MODE 			= 'view';
	const FULL_MODE 			= 'full';

	protected $name = null;

	/**
	*
	*/
	public function __construct($o)
	{
		if (empty($o))
		{
			$this->name = '';
		}
		elseif ($o instanceof DashletMode)
		{
			$this->name = $o->getName();
		}
		elseif (is_string($o))
		{
			$this->name = strtolower($o);
		}
	}


	/**
	* Returns the name of the DashletMode
	*
	*/
	public function getName()
	{
		return $this->name;
	}



	/**
	* Returns true if the Mode has a name equal to $name
	*
	* @return bool
	*/
	public function is($name)
	{
		return ($this->name == $name);
	}


	/**
	* Used for comparing between two DashletAction instances. Two
	* DashletActions are equal if they share the same name.
	*
	* @param mixed $o
	*/
	public static function equals($o)
	{
		if($action instanceof DashletAction)
		{
			return $action->name == $this->name;
		}
		return false;
	}



	public static function getViewMode()
	{
		return new DashletMode(DashletMode::VIEW_MODE);
	}

	public static function getEditMode()
	{
		return new DashletMode(DashletMode::EDIT_MODE);
	}

	public static function getFullMode()
	{
		return new DashletMode(DashletMode::FULL_MODE);
	}

}