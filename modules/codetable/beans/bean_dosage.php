<?php

// load relevant Bean
require_once $root_path.'/modules/codetable/beans/class_bean.php';

class DosageBean extends Bean {

	/**
	* Constructor
	*
	*/
	public function __construct() {
		$this->objectName = 'dosage';
		parent::Bean();
	}

	// other related logic go here...
}