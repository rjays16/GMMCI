<?php
require './roots.php';
require_once $root_path.'classes/json/json.php';

class DashletResponse {

	const DEFAULT_CONTENT_TYPE 	= 'text/json';
	const DEFAULT_CHAR_ENCODING = 'iso-8859-1';

	/* Javascript related responses */
	const RESPONSE_ALERT 					= 'alert';
	const RESPONSE_CALL 					= 'call';
	const RESPONSE_INCLUDE_SCRIPT = 'inc';
	const RESPONSE_EXECUTE 				= 'exec';

	/* Dashlet Group related responses */
	const RESPONSE_GROUP_SEND 		= 'gs';
	const RESPONSE_GROUP_REFRESH 	= 'gref';

	/* Dashlet Class related responses */
	const RESPONSE_CLASS_SEND 		= 'cs';
	const RESPONSE_CLASS_REFRESH 	= 'cref';

	//const RESPONSE_DASHLET_SET_TITLE = 'dl.s_title';

	const EXTEND_APPEND 					= 'append';


	private $responses;

	/**
	* Default constructor
	*
	*/
	public function __construct() {
		$this->responses = array();
	}


	/**
	* Adds a response to the response buffer
	*
	* @param mixed $response
	*/
	public function includeResponse( $response ) {
		$this->responses[] = $response;
	}

	/**
	* Response which invokes a javascript alert displaying text specified by $message
	*
	* @param String $message
	*/
	public function alert( $message ) {
		$this->includeResponse(Array(
			'rsp'=>self::RESPONSE_ALERT,
			'data' => $message
		));
		return $this;
	}



	/**
	* Response which processes a Javascript script file and loads it to the DOM
	*
	* @param String $path
	*/
	public function loadScript( $path ) {
		$this->includeResponse(Array(
			'rsp'=>self::RESPONSE_INCLUDE_SCRIPT,
			'data' => $path
		));
		return $this;
	}


	/**
	* Response which calls a Javascript function specified and passes the specified
	* parameters to the function call
	*
	* @param String the name of the Javascript function
	* @param args Argument list of parameters passed to the function
	*/
	public function call( ) {
		$args = func_get_args();
		$functionName = array_shift($args);
		$this->includeResponse( Array(
			'rsp'=>self::RESPONSE_CALL,
			'data' => Array(
				'fn' => $functionName,
				'args' => $args
			)
		));
		return $this;
	}

	/**
	* Response which evaluates and executes a Javascript code block. The Javascript block is
	* executed in its own context, so locally declared variables will not be available after
	* the execution.
	*
	* @param String the script block to execute
	*/
	public function execute( $script ) {
		$args = func_get_args();
		$functionName = array_shift($args);
		$this->includeResponse( Array(
			'rsp'=>self::RESPONSE_EXECUTE,
			'data' => $script
		));
		return $this;
	}



	/**
	* Dashlet group related response which sends an action to all active Dashlets belonging to a specific Dashlet group
	*
	* @param String $name The name of the Dashlet group to receive the sent Action
	* @param String $action The name of the Action to send
	* @param String $params Optional. The parameters that will be attached to the Action
	*/
	public function groupSend( $name, $action, $params=null ) {
		$this->includeResponse( Array(
			'rsp'=>self::RESPONSE_GROUP_SEND,
			'data' => array(
				'n' => $name,
				'a' => $action,
				'p' => $params,
			)
		));
		return $this;
	}



	/**
	* Dashlet group related response which refreshes all active Dashlets belonging to a specific Dashlet group
	*
	* @param String $name The name of the Dashlet group receiving the refresh
	*/
	public function groupRefresh( $name ) {
		$this->includeResponse( Array(
			'rsp'=>self::RESPONSE_GROUP_REFRESH,
			'data' => array(
				'n' => $name
			)
		));
		return $this;
	}





	/**
	* Dashlet class related response which sends an action to active Dashlets with a specific Class name
	*
	* @param String $name The Class Name of the Dashlets to receive the sent Action
	* @param String $action The name of the Action to send
	* @param String $params Optional. The parameters that will be attached to the Action
	*/
	public function classSend( $name, $action, $params=null ) {
		$this->includeResponse( Array(
			'rsp'=>self::RESPONSE_CLASS_SEND,
			'data' => array(
				'n' => $name,
				'a' => $action,
				'p' => $params,
			)
		));
		return $this;
	}



	/**
	* Dashlet group related response which refreshes all active Dashlets with a specific Class name
	*
	* @param String $name The Class Name of the Dashlets receiving the refresh
	*/
	public function classRefresh( $name ) {
		$this->includeResponse( Array(
			'rsp'=>self::RESPONSE_CLASS_REFRESH,
			'data' => array(
				'n' => $name
			)
		));
		return $this;
	}







	/**
	* Special Dashlet-speciic response which renders the Dashlet title
	*
	* @param String $id The id of the dashlet
	* @param String $title The new title for the dashlet
	*/
//	public function setTitle( $id, $title ) {
//		$this->includeResponse( Array(
//			'rsp'=>self::RESPONSE_DASHLET_SET_TITLE,
//			'data' => array(
//				'id' => $id,
//				't' => $title
//			)
//		));
//		return $this;
//	}


	/**
	* Clears the response buffer
	*
	*/
	public function clear() {
		$thus->responses = array();
	}


	/**
	* Returns the response queue
	*
	*/
	public function getResponses()
	{
		return $this->responses;
	}




	/**
	* Extends the DashletResponse object to include the response commands of another DashletResponse object.
	*
	* This method simply retrieves the response commands of another DashletResponse object (the extender) and
	* loads the commandst ogether with the extending Dashlet's' own commands. The method of loading the commands
	* is determined by the $extendMethod argument which, by default, appends the extender's commands to the
	* extending DashletRepsonse' commands,
	*
	*
	* @param mixed $response
	* @param mixed $extendMethod
	*/
	public function extend(DashletResponse $response, $extendMethod=self::EXTEND_APPEND)
	{
		if ($response instanceof DashletResponse)
		{
			switch ($extendMethod)
			{
				case self::EXTEND_APPEND:
				default:
					$this->responses = array_merge($this->responses, $response->getResponses());
					break;
			}
		}
	}
}