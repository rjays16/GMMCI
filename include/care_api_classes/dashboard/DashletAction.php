<?php
/**
* Generic class that defines the basic logic structue for a Dashlet Action.
*
*/


class DashletAction
{

	/**
	* @var string $name Name for the action
	*/
	protected $name = null;
	protected $parameters = null;

	/**
	* Default constructor
	*
	* @param mixed $name
	*/
	public function __construct($name = null, $parameters=null)
	{
		$this->name = strtolower($name);

		if (is_array($parameters))
		{
			$this->parameters = $parameters;
		}
		elseif ($parameters)
		{
			$this->parameters = (array) $parameters;
		}
		else
		{
			$this->parameters = Array();
		}
	}


	/**
	* Returns the name of the Action
	*
	*/
	public function getName()
	{
		return $this->name;
	}


	/**
	* Sets the value of an Action paramter
	*
	* @param mixed $parameterName
	* @param mixed $parameterValue
	*/
	public function setParameter($parameterName, $parameterValue=null)
	{
		$this->parameters[$parameterName] = $parameterValue;
	}


	/**
	* Returns the value of a parameter for the Action
	*
	* @param mixed $parameterName
	*/
	public function getParameter($parameterName)
	{
		return $this->parameters[$parameterName];
	}


	/**
	* Returns a list of defined parameters for the Action
	*
	* @param mixed $parameterName
	*/
	public function getParameters()
	{
		return $this->parameters;
	}



	/**
	* Returns true if the Action has a name equal to $name
	*
	* @return bool
	*/
	public function is($name)
	{
		return ($this->name == strtolower($name));
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



}