<?php
require './roots.php';
require_once $root_path.'modules/codetable/dynamicfields/base/field_base.php';

/**
* class FieldFlag
*
* An extension of the class FieldBase, FieldFlag provides support for flag, toggle, or boolean types of data
* for use in the CodeTable module.
*
* @author Alvin Quinones <meinbetween@gmail.com> May 2010
* @package codetable/dynamicfields
* @version 1.0
*/
class FieldFlag extends FieldBase{

	public function __construct( $value=null, $options=null ) {
		$this->defaultOptions['list'] = array(
			'offText' => 'No',
			'onText' => 'Yes'
		);
		$this->defaultOptions['edit'] = array();
		$this->defaultOptions['search'] = array(
			'defaultText' => '',
			'offText' => 'No',
			'onText' => 'Yes',
			'allText' => 'Show all'
		);

		parent::FieldBase($value, $options);
		$this->fieldType = 'flag';
	}


	/**
	* put your comment there...
	*
	* @param mixed $filterOptions
	* @return mixed
	*/
	function getFilters($filterOptions=null) {
		if ($filterOptions['value'] == '*') {
			return null;
		}
		elseif ($filterOptions['value'] !== '') {
			return "`{$filterOptions['field']}`=".$this->db->qstr($filterOptions['value']);
		}
		else
			return false;
	}
}