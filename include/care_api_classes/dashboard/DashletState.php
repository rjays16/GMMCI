<?php
/**
*
*/
class DashletState
{

	const NORMAL_STATE = 'normal';
	const MINIMIZED_STATE = 'minimized';

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
		elseif ($o instanceof DashletState)
		{
			$this->name = $o->getName();
		}
		elseif (is_string($o))
		{
			$this->name = strtolower($o);
		}
	}


	/**
	* Returns the name of the DashletState
	*
	*/
	public function getName()
	{
		return $this->name;
	}



	/**
	* Returns true if the DashletState has a name equal to $name
	*
	* @return bool
	*/
	public function is($name)
	{
		return ($this->name == $name);
	}


	/**
	* Used for comparing between two DashletState instances. Two
	* DashletActions are equal if they share the same name.
	*
	* @param mixed $o
	*/
	public static function equals($o)
	{
		if($action instanceof DashletState)
		{
			return $action->name == $this->name;
		}
		return false;
	}



	public static function getNormalState()
	{
		return new DashletState(DashletState::NORMAL_STATE);
	}

	public static function getMinimizedState()
	{
		return new DashletState(DashletState::MINIMIZED_STATE);
	}


}