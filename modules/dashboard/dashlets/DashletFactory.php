<?php


/**
*
*
*/
require './roots.php';
require $root_path.'include/care_api_classes/dashboard/Dashlet.php';

class DashletFactory {

	private $classList;

	/**
	* Default constructor. Set to private to prevent instantiation.
	*
	*/
	private function __construct() {
		$this->classList = array(
			'Miscellaneous' => array('PlainHtmlDashlet')
		);
	}




	/**
	*
	*/
	public static function getInstance() {

	}



	/**
	* Loads a Dashlet from the database
	*/
	public function loadDashlet( $id ) {



	}
}