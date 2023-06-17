<?php

require './roots.php';
require_once $root_path.'modules/codetable/dynamicfields/base/field_base.php';

class FieldNumeric extends FieldBase{
	var $min;
	var $max;
	var $decimal;

	function FieldNumeric( $value=null, $metaOptions=null ) {
		$this->defaultOptions['meta'] = array(
			'min' => ~PHP_INT_MAX,
			'max' => PHP_INT_MAX,
			'decimal' => 0
		);
		
		$this->defaultOptions['list'] = array(
			'point' => '.',
			'separator' => ','
		);

		parent::FieldBase($value, $metaOptions);
		$this->fieldType = 'numeric';
		$metaOptions = $this->_extend( $this->defaultOptions['meta'], $metaOptions);
		$this->min = $metaOptions['min'];
		$this->max = $metaOptions['max'];
		$this->decimal = $metaOptions['decimal'];
	}

	function getListView($listOptions=null) {
		$listOptions = $this->_extend( $this->defaultOptions['list'], $listOptions);
		$listOptions['decimal'] = $this->decimal;
		return '<div align="right">'.number_format($this->value, $this->decimal, $listOptions['point'], $listOptions['separator']).'</div>';
	}
	
	function getFilters($filterOptions=null) {
		if (isset($filterOptions['field']) && $filterOptions['value']!=='') {
			return "IFNULL(`{$filterOptions['field']}`,0)=".$this->db->qstr($filterOptions['value']);
		}
		else
			return null;
	}
 
}