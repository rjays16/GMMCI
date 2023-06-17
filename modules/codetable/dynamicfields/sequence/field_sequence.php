<?php

require './roots.php';
require_once $root_path.'modules/codetable/dynamicfields/base/field_base.php';

class FieldSequence extends FieldBase{
	var $min;
	var $max;
	var $increment;

	function FieldSequence( $value=null, $metaOptions=null ) {
		$this->defaultOptions['meta'] = array(
			'min' => 0,
			'max' => 999999,
			'increment' => 1,
			'sourceTable' => 'table',
			'sourceField' => 'id'

		);

		parent::FieldBase($value, $metaOptions);
		$this->fieldType = 'sequence';
		$metaOptions = $this->_extend( $this->defaultOptions['meta'], $metaOptions);

		$this->min = $metaOptions['min'];
		$this->max = $metaOptions['max'];
		$this->increment = $metaOptions['increment'];
		$this->sourceTable = $metaOptions['sourceTable'];
		$this->sourceField = $metaOptions['sourceField'];
	}

	function getListView($viewOptions=null) {
		return str_pad($this->value, strlen($this->max), '0', STR_PAD_LEFT);
	}

	function generate( ) {
		$last_sequence = (int) $this->db->GetOne("SELECT MAX(`{$this->sourceField}`) FROM {$this->sourceTable}");
		if ($last_sequence === false) return $this->min;
		if ($last_sequence < $this->min) {
			$next_sequence = $this->min;
			return $next_sequence;
		}
		else {
			$next_sequence = floor($last_sequence/$this->increment+1)*$this->increment;
			if ($next_sequence > $this->max) {
				return false;
			}
			else {
				return $next_sequence;
			}
		}
	}
}