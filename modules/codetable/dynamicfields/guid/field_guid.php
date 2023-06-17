<?php

require './roots.php';
require_once $root_path.'modules/codetable/dynamicfields/base/field_base.php';

class FieldGuid extends FieldBase{

	/**
	* put your comment there...
	*
	* @param mixed $value
	* @param Array $metaOptions
	* @return FieldGuid
	*/
	public function __construct( $value=null, $metaOptions=null ) {
		$this->defaultOptions['meta'] = array();
		$this->defaultOptions['edit'] = array();
		$this->defaultOptions['filter'] = array();

		parent::FieldBase($value, $metaOptions);
		$this->fieldType = 'sequence';
		$metaOptions = $this->_extend( $this->defaultOptions['meta'], $metaOptions);
	}

	/**
	* put your comment there...
	*
	* @param mixed $viewOptions
	* @return string
	*/
	function getListView($viewOptions=null) {
		return $this->value;
	}

	/**
	* put your comment there...
	*
	*/
	function generate( ) {
		return create_guid();
	}

}