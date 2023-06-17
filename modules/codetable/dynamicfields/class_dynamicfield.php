<?php

/**
* List of DynamicFields to implement in the future:
* 
* numeric
* datetime
* enum
* area
* guid
* patientid
* encounter
* file
* image
* address
*/

require './roots.php';

class DynamicField {
	var $scripts;
	var $dynamicFields;

	function DynamicField() {
		$this->dynamicFields = array();
		$this->scripts = array();
	}

	function getField( $fieldType='base', $value=null, $options=null ) {
		global $root_path;
		if ( !isset($this->dynamicFields[$fieldType]) ) {

			if (file_exists( $root_path.'modules/codetable/dynamicfields/'.$fieldType.'/field_'.$fieldType.'.php' )) {
				require_once $root_path.'modules/codetable/dynamicfields/'.$fieldType.'/field_'.$fieldType.'.php';
				$fieldClassName = 'Field'.ucfirst($fieldType);
			}
			else {
				require_once $root_path.'modules/codetable/dynamicfields/base/field_base.php';
				$fieldClassName = 'FieldBase';
			}

			// if related Javascript file file exists, load into script queue
			if (file_exists( $root_path.'modules/codetable/dynamicfields/'.$fieldType.'/field_'.$fieldType.'.js' )) {
				$this->scripts[] = '../../modules/codetable/dynamicfields/'.$fieldType.'/field_'.$fieldType.'.js">';
			}

			$this->dynamicFields[$fieldType] = new $fieldClassName( $value, $options );
		}
		return $this->dynamicFields[$fieldType];
	}

	function appendScripts( &$smartyCareObject ) {
		if ($smartyCareObject) {
			foreach ($this->scripts as $script) {
				$smartyCareObject->append('JavaScript', "<script type=\"text/javascript\" src=\"{$script}\"></script>\n");
			}
		}
	}
}