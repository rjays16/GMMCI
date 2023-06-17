<?php
require './roots.php';
require_once $root_path.'modules/codetable/dynamicfields/base/field_base.php';

/**
*
*/
class FieldCollection extends FieldBase{

	/**
	* put your comment there...
	*
	* @param mixed $value
	* @param mixed $options
	* @return FieldCollection
	*/
	public function __construct( $value=null, $options=null ) {
		$this->defaultOptions['meta'] = array(
			'source' 		=> 'codetable',
			''					=> '',
		);
		$this->defaultOptions['edit'] = array(
			'width' => '98%',
			'rows' => 1,
		);
		$this->defaultOptions['filter'] = array(
			'field' => '',
			'mode' => 'startswith',
			'value' => ''
		);
		parent::FieldBase($value, $options);
		$this->fieldType = 'collection';
	}

	/**
	* put your comment there...
	*
	* @param mixed $options
	* @return string
	*/
	function getListView($options=null) {
		return htmlentities($this->value);
	}


	/**
	* put your comment there...
	*
	* @param Array $filterOptions
	* @return mixed
	*/
	function getFilters($filterOptions=null) {
		$filterOptions = $this->_extend($this->defaultOptions['filter'], $filterOptions);
		if ($filterOptions['value']) {
			switch(strtolower($filterOptions['mode'])) {
				case 'exactly':
					return "`{$filterOptions['field']}`=".$this->db->qstr($filterOptions['value']);
					break;
				case 'contains':
					return "`{$filterOptions['field']}` LIKE ".$this->db->qstr("%{$filterOptions['value']}%");
					break;
				case 'doesnotcontain':
					return "NOT `{$filterOptions['field']}` LIKE ".$this->db->qstr("%{$filterOptions['value']}%");
					break;
				case 'endswith':
					return "`{$filterOptions['field']}` LIKE ".$this->db->qstr("%{$filterOptions['value']}");
					break;
				case 'startswith': default:
					return "`{$filterOptions['field']}` LIKE ".$this->db->qstr("{$filterOptions['value']}%");
					break;
			}
		}
		else
			return false;
	}
}