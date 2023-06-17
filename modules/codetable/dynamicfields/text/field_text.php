<?php

require './roots.php';
require_once $root_path.'modules/codetable/dynamicfields/base/field_base.php';

class FieldText extends FieldBase{
	var $length;

	function FieldText( $value=null, $options=null ) {
		$this->defaultOptions['meta'] = array(
			'length' => 25,
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
		$this->fieldType = 'text';
	}

	function getListView($options=null) {
		return htmlentities($this->value);
	}
	
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