<?php
/**
 * Event.php
 *
 * @author Alvin Quinones
 * @copyright Copyright &copy; 2012-2013 Segworks Technologies Corporation
 */

/**
 * Base class for event handling
 *
 * @version 1.0
 * @package base
 */

class Event
{
	protected $origin;
	protected $handled = false;
	protected $data = array();

	/**
	 * Description
	 * @param mixed $origin 
	 * @param mixed $data 
	 */
	public function __construct($origin, $data = array())
	{
		$this->origin = $origin;
		$this->data = $data;
	}

	/**
	 * Description
	 * @return void
	 */
	public function stopPropagation()
	{
		$this->handled = true;
	}
    
    /**
     * 
     * @param string $key
     */
    public function getData($key)
    {
        if (isset($this->data[$key])) {
            return $this->data[$key];
        } else {
            return null;
        }
    }
}