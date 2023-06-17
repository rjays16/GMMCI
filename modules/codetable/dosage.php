<?php

// load relevant Bean
require_once $root_path.'/metadata/class_bean.php';

class Dosage extends Bean {
	var $id;
	var $dosage;
	var $is_deleted;
	
	function Dosage() {
		$this->objectName = 'dosage';
		parent::Bean();
	}
	
	// other class specific functions go here...
}

// define settings
$CodeTable = Array(
	'title' => Array(
		'edit' => 'Edit dosage details',
		'view' => 'View dosage details',
		'list' => 'List of dosage entries',
	),
);

// create wrapped Bean instance
$CodeTable['bean'] = new Dosage();