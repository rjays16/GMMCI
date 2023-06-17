<?php

// load relevant Bean
require_once $root_path.'/modules/codetable/beans/class_bean.php';

class ProductCategoryBean extends Bean {

	/**
	* Constructor
	*
	*/
	public function __construct() {
		$this->objectName = 'productcategory';
		parent::Bean();
	}

	// other related logic go here...
}