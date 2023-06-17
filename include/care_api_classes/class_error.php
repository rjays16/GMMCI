<?php
/**
* Care2x API package
* @package care_api
*/

/* Database connection errors */
define('SEG_FATAL_ERROR_CONNECTION', 0);

/* Permission errors */
define('SEG_FATAL_ERROR_PERMISSION', 20);

/* Session errors */
define('SEG_FATAL_ERROR_SESSION_EXPIRED', 50);

/* Faulty query errors */
define('SEG_FATAL_ERROR_SQL', 100);

/* File access errors */
define('SEG_FATAL_ERROR_FILE_CANNOT_ACCESS', 200);
define('SEG_FATAL_ERROR_FILE_CANNOT_READ', 201);
define('SEG_FATAL_ERROR_FILE_CANNOT_WRITE', 201);

/* Query exceptions */
define('SEG_ERROR_SQL_INVALID', 1000);
define('SEG_ERROR_SQL_CANNOT_EXECUTE', 1001);
define('SEG_ERROR_SQL_CANNOT_CREATE', 1002);
define('SEG_ERROR_SQL_CANNOT_EDIT', 1003);
define('SEG_ERROR_SQL_CANNOT_DELETE', 1004);
define('SEG_ERROR_SQL_NO_RECORD', 1010);
define('SEG_ERROR_DATA_INVALID', 2000);

/* Data inconsistency notices  */
define('SEG_NOTICE_DATA_INVALID', 10000);
define('SEG_NOTICE_DATA_EMPTY', 10001);
define('SEG_NOTICE_SQL_NO_RECORD', 10100);



global $dictionary;
$dictionary['Errors'] = array(
/**  -- Fatal errors here --
*   Erros 0-999 are fatal errors, errors which would cause immediate
*  script termination
*
*/
	SEG_FATAL_ERROR_CONNECTION => "Could not connect to database server",
	SEG_FATAL_ERROR_PERMISSION => "Special permission required",
	SEG_FATAL_ERROR_SESSION_EXPIRED => "Session expired",
	SEG_FATAL_ERROR_SQL => "Error in SQL Query",
	SEG_FATAL_ERROR_FILE_NOACCESS => "Could not access file",
	SEG_FATAL_ERROR_FILE_NOREAD => "Could not read file",
	SEG_FATAL_ERROR_FILE_NOWRITE => "Could not write to file",

/**  -- Generic errors/warnings here --
*   Erros 1000-9999 are general non-fatal errors. Usually these
*  errors do not stop the script from executing but instead are
*  shown to the user in some form of alert
*/
	SEG_ERROR_SQL_INVALID => "Invalid query encountered",
	SEG_ERROR_SQL_CANNOT_EXECUTE => "Could not execute command",
	SEG_ERROR_SQL_CANNOT_CREATE => "Could not create record/s",
	SEG_ERROR_SQL_CANNOT_EDIT => "Could not update record/s",
	SEG_ERROR_SQL_CANNOT_DELETE => "Could not delete record/s",
	SEG_ERROR_SQL_NO_RECORD => "No record found",
	SEG_ERROR_DATA_INVALID => "Invalid data found",


/**  -- Notices here --
*   Errors 10000+ are notices
*  Notices are minor inconsistencies in the system that the casual
*  user need not be aware of, but might be of concern to the
*  the module's developers
*/
	SEG_NOTICE_DATA_INVALID => 'Some variable/data has not been set or passed properly',
	SEG_NOTICE_DATA_EMPTY => 'Required data is empty',
	SEG_NOTICE_SQL_NO_RECORD => 'It is kinda odd that no records were found'

);


class ErrorReporter {
	var $_errors;

	function ErrorReporter() {
		$this->flushErrors();
	}

	function flushErrors() {
		$this->_errors = array();
	}

	function raiseErrorByReference(&$e) {
		$e->raise();
		$this->_errors[] = $e;
	}

	function raiseError($errorId, $debugInfo=null, $tips=null) {
		$e = new Error($errorId);
		$e->debug($debugInfo);
		$e->tip($tips);
		$this->raiseErrorByReference($e);
	}

	function report() {
		$report = "";
		foreach ($this->_errors as $error) {
			$report.=$error->report();
		}
		return $report;
	}

	function hasErrors() {
		global $config;
		$Count = 0;
		foreach ($this->_errors as $error) {
			if ($error->id() >= 10000) {
				if ($config['debug'])
					$Count++;
			}
			else $Count++;
		}
		return $Count;
	}
}


/**
* Error object.
* @author Alvin Quinones
* @version beta 0.1.0
* @copyright 2010 Alvin Quinones
* @package care_api_classes
*/
class Error {
	var $_id;
	var $_description;
	var $_tip;
	var $_debug;
	var $_timeStamp;
	var $_fileName;
	var $_userAgent;
	var $_remoteAddr;


	/**
	*
	*/
	function Error($error_id) {
		global $dictionary;

		$this->_id = $error_id;
		$this->_description = $dictionary['Errors'][$error_id];
		$this->_tips = array();
		$this->_debug = array();
	}

	/**
	*
	*/
	function getErrorType() {
		if ($this->_id>=0 && $this->_id<=999)
			return 'Fatal Error';
		elseif ($this->_id>=1000 && $this->_id<=9999)
			return 'Error';
		elseif ($this->_id>=10000 && $this->_id<=19999)
			return 'Warning';
		elseif ($this->_id>=20000)
			return 'Notice';
		else
			return 'Unknown Error';
	}

	/**
	*
	*/
	function id($id=NULL) {
		if (!is_null($id))
			$this->_id=$id;
		return $this->_id;
	}

	/**
	*
	*/
	function description($desc=NULL) {
		if (!is_null($desc))
			$this->_description=$desc;
		return $this->_description;
	}

	/**
	*
	*/
	function debug($debug) {
		if ($debug) {
			if (!is_array($debug))
				$debug=array($debug);
			$this->_debug=array_merge($this->_debug, $debug);
		}
		#die(var_export($debug,TRUE));
		return $this->_debug;
	}

	/**
	*
	*/
	function tips() { return $this->_tips; }

	/**
	*
	*/
	function tip($tip) {
		if ($tip) {
			if (!is_array($tip))
				$tip=array($tip);
			$this->_tips=array_merge($this->_tips, $tip);
		}
		return $this->_tips;
	}

	/**
	*
	*/
	function report() {
		global $config;

		$html = "<div class=\"error_item\">".
			"<div class=\"error_description\"><h1 >".$this->getErrorType().": ".htmlentities($this->_description)."</h1></div>".
			"<div class=\"error_content\">";
		if (!$config['debug']) {
			if ($this->_tips && is_array($this->_tips)) {
				 $html.="<ul class=\"error_tips\">";
				foreach ($this->_tips as $tip) {
					$html .= "<li>".htmlentities($tip)."</li>";
				}
				$html.="</ul>";
			}
		}
		else {
			$html.="<ul class=\"error_tips\">";

			if ($this->_debug && is_array($this->_debug)) {
				foreach ($this->_debug as $i=>$debug) {
					$html .= "<li><a href=\"javascript:alert('".addcslashes(htmlentities($debug),"\n\r'\"\0")."')\" style=\"\">".htmlentities($i)."</a></li>";
				}
			}
			$html.="</ul>";
		}
		$html.="</div></div>";

		return $html;
	}

	/**
	*
	*/
	function raise($doSessionDump=false) {
		$this->_fileName=$_SERVER['PHP_SELF'];
		$this->_userAgent=$_SERVER['PHP_USER_AGENT'];
		$this->_remoteAddr=$_SERVER['PHP_REMOTE_ADDR'];

		if ($doSessionDump) {
			$this->_sessionDump = print_r($_SESSION,true);
		}
		$this->_timeStamp = date('YmdHis');
		return 1;
	}
}

