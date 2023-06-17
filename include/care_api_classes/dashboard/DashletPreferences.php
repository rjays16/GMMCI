<?php

/**
* Provides API layer for handling user preferences for each Dashlet.
*
* DashletPreferences differ from DashletSession in that preferences for a dashlet are
* usually set during the EDIT mode of a Dashlet. Sessions are generally accessed and
* modified during processing of Dashlet actions and render requests. Moreover, Sessions
* do not persist when the user changes location, uses another workstation, or clears
* browser session/cookies.
*
*/

class DashletPreferences
{

	/**
	* @var
	*/
	private $items;


	/**
	* Default constructor
	*
	* @param Dashlet $id
	* @return DashletPreferences
	*/
	public function __construct()
	{
		$this->items = array();
	}


	/**
	* put your comment there...
	*
	* @param mixed $itemName
	*/
	public function get( $itemName )
	{
		return $this->items[$itemName];
	}


	/**
	* put your comment there...
	*
	* @param mixed $itemName
	* @param mixed $value
	*/
	public function set( $itemName, $value=null )
	{
		if (!is_scalar($value))
		{
			return false;
		}

		$this->items[ $itemName ] = $value;
	}


	/**
	* Returns the mapping of the dashlet preferences in key-value array format
	*
	* @return Array
	*/
	public function getMap()
	{
		return $this->items;
	}


	/**
	* Attempts to load the preferences items from either a DashletPreferences object, a key-value mapping array object, or a string in packed data format
	*
	* @param mixed $packed
	*/
	public function load( $obj=null ) {
		$this->items = null;
		if ( $obj instanceof DashletPreferences )
		{
			$this->items = $obj->getMap();
		}
		elseif (is_array($obj))
		{
			$this->items =$obj;
		}
		elseif (is_string($obj))
		{
			$unpacked = $this->unpack($obj);
			if ($unpacked)
			{
				$this->items = $unpacked;
			}
		}

	}


	/**
	* Method for converting preferences data into packed format which can be
	* easily stored as plain text or in a database field
	*
	* @return mixed
	*/
	public function pack()
	{
		return base64_encode( serialize($this->items) );
	}


	/**
	* Method for decoding the packed data format retrieved from the <code>pack</code> method
	*
	* @param mixed $packed
	* @return mixed
	*/
	public function unpack($packed)
	{
		return unserialize(base64_decode($packed, $strict=true));
	}


}