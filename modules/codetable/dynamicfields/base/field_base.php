<?php
require './roots.php';
/**
* LOAD Smarty Library
*/
require_once($root_path.'gui/smarty_template/smarty_care.class.php');

class FieldBase {
	var $smarty;
	var $fieldType;
	var $value;
	var $customTemplate;
	var $defaultOptions;
	var $imagesPath;

	function FieldBase( $value=null, $metaOptions=null ) {
		global $db;
		if (!$this->imagesPath) {
			$this->imagesPath = '../../gui/img/common/default/';
		}
		$this->fieldType = 'base';
		$this->value = $value;
		$this->smarty = new smarty_care('common', FALSE, FALSE, FALSE);
		$this->smarty->debugging = false;
		$this->smarty->left_delimiter = '{';
		$this->smarty->right_delimiter = '}';
		$this->smarty->assign ('imagesPath', $this->imagesPath);
		$this->db = $db;
	}

	/**
	 * handy extend function used for specifying default values
	 * of options passed to constructors of DynamicField extensions
	 *
	 * @param Array array to extend (usually the default values)
	 * @param Array array extender (usually the passed values)
	 * @return Array the extended array; elements in the second array will be merged with the first array,
	 *   if no matches are found on the first array, the unmatched elements are ignored
	 */
	function _extend($a, $b) {
		if (!is_array($a)) {
			$a=array();
		}
		if (!is_array($b)) {
			$b=array();
		}
		if (!$a) return $b;
		if (!$b) return $a;
		foreach($b as $k=>$v) {
			if( is_array($v) ) {
				if( !isset($a[$k]) ) {
					$a[$k] = $v;
				} else {
					$a[$k] = array_extend($a[$k], $v);
				}
			} else {
				$a[$k] = $v;
			}
		}
		return $a;
	}

	/**
	* Return the output from the Smarty template file specified by $path
	*
	* @param string $path
	* @return string
	*/
	function fetch($path) {
		return $this->smarty->fetch($path);
	}

	/**
	* Assign display options from the $displayParams array for the Smarty template, should be called
	* before each fetch call
	*
	* @param mixed $displayParams
	*/
	function setup( $displayParams=null ) {
		$this->smarty->assign('value', $this->value);
		if ($displayParams) {
			$this->smarty->assign('options',$displayParams);
		}
	}

	/**
	* put your comment there...
	*
	* @param mixed $options
	* @return mixed
	*/
	function getListView( $viewOptions=null ) {
		global $root_path;
		if (isset($this->defaultOptions['list'])) {
			$viewOptions = $this->_extend($this->defaultOptions['list'], $viewOptions);
		}
		$templateName = "{$root_path}modules/codetable/dynamicfields/{$this->fieldType}/field_{$this->fieldType}_listview.tpl";
		if ( file_exists($templateName) ) {
			$this->setup($viewOptions);
			return $this->fetch($templateName);
		}
		return $this->value;
	}

	/**
	* put your comment there...
	*
	* @param mixed $options
	* @return mixed
	*/
	function getEditView( $viewOptions=null ) {
		global $root_path;
		if (isset($this->defaultOptions['edit'])) {
			$viewOptions = $this->_extend($this->defaultOptions['edit'], $viewOptions);
		}
		$templateName = "{$root_path}modules/codetable/dynamicfields/{$this->fieldType}/field_{$this->fieldType}_editview.tpl";
		if ( file_exists($templateName) ) {
			$this->setup($viewOptions);
			return $this->fetch($templateName);
		}
		return $this->value;
	}

	/**
	* put your comment there...
	*
	* @param mixed $options
	* @return mixed
	*/
	function getDetailView( $detailOptions=null ) {
		global $root_path;
		if (isset($this->defaultOptions['detail'])) {
			$detailOptions = $this->_extend($this->defaultOptions['detail'], $detailOptions);
		}
		$templateName = "{$root_path}modules/codetable/dynamicfields/{$this->fieldType}/field_{$this->fieldType}_detailview.tpl";
		if ( file_exists($templateName) ) {
			$this->setup($detailOptions);
			return $this->fetch($templateName);
		}
		return $this->value;
	}

	/**
	* put your comment there...
	*
	* @param mixed $options
	* @return mixed
	*/
	function getSearchView( $searchOptions=null ) {
		global $root_path;
		if (isset($this->defaultOptions['search'])) {
			$searchOptions = $this->_extend($this->defaultOptions['search'], $searchOptions);
		}
		$templateName = "{$root_path}modules/codetable/dynamicfields/{$this->fieldType}/field_{$this->fieldType}_searchview.tpl";
		if ( file_exists($templateName) ) {
			$this->setup($searchOptions);
			return $this->fetch($templateName);
		}
		return $this->value;
	}

	/**
	* put your comment there...
	*
	* @param mixed $filterOptions ideally, this should contain field and value items, specifying
	* 	the fields and value of the filter
	* @return mixed returning null will unset any existing value on the filters array; if the
	* 	returned value is not FALSE, it will be added to the filters array
	*
	*/
	function getFilters($filterOptions=null) {
		if (isset($filterOptions['field']) && $filterOptions['value']!=='') {
			return "`{$filterOptions['field']}`=".$this->db->qstr($filterOptions['value']);
		}
		else
			return null;
	}

	/**
	* default value generator for the field, returns false indicating that the
	* generator failed to generate a value; extending classes that need a
	* custom generate function should override this function
	*
	* @return bool
	*/
	function generate() {
		return false;
	}
}