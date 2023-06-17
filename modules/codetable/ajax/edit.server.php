<?php
require('./roots.php');
require_once($root_path.'include/inc_environment_global.php');
require_once($root_path.'modules/codetable/ajax/edit.common.php');

function delete($objectName, $pK, $comments='') {
	global $root_path;
	$objResponse = new xajaxResponse();

	// get bean
	require_once "{$root_path}modules/codetable/beans/bean_{$objectName}.php";
	$beanClass = "{$objectName}bean";
	$bean = new $beanClass();

	if ($pK) {
		$bean->setKeyValues($pK);
	}

	if ($bean->delete($comments)) {
		$objResponse->call('CodeTable.info','Info:','Item successfully deleted...');
		$objResponse->call('CodeTable.doClose');
	}
	else {
		$objResponse->call('CodeTable.alert','Error:','Error occurred while trying to delete item...');
	}
	return $objResponse;
}

function save($objectName, $pK, $data) {
	global $root_path;
	$objResponse = new xajaxResponse();

	require_once "{$root_path}modules/codetable/metadata/{$objectName}/{$objectName}_editview.php";
	$editView =& $Views['Edit'][$objectName];

	require_once "{$root_path}modules/codetable/dynamicfields/class_dynamicfield.php";
	$dynamicFields = new DynamicField();

	// retrieve mapping from panel Item id to field
	$map = array();
	foreach ($editView['panels'] as $i=>$panel) {
		foreach ($panel['items'] as $j=>$item) {
			$map[$j] = $item['field'];
		}
	}

	// create bean data array from passed Edit View data
	$bean_data = array();
	foreach ($data as $id=>$value) {
		$bean_data[$map[$id]] = $value;
	}

	// get bean
	require_once "{$root_path}modules/codetable/beans/bean_{$objectName}.php";
	$beanClass = "{$objectName}bean";
	$bean = new $beanClass();

	if ($pK) {
		$bean->setKeyValues($pK);

//		$new = false;
	}
	else {
		$pK = array();
//		$new = true;
	}

	// auto-populate required fields with auto-generated value if possible
	foreach ($bean->fields as $field=>$value) {
		// if the field does not have a value...
		if (!isset($bean_data[$field]) || $bean_data[$field]===$bean->emptyValue) {
			$fieldDef =& $bean->dictionary['fields'][$field];

			// if field is part key values, retrieve its value
			if (isset($bean->keyValues[$field]) && $bean->keyValues[$field]!==$bean->emptyValue) {
				$bean_data[$field] = $bean->keyValues[$field];
			}
			else {
				// ...otherwise, if the field is a required field
				if ($fieldDef['required']) {
					// ...get corresponding dynamicfield object...
					$fieldObj = $dynamicFields->getField($fieldDef['type'], $bean->emptyValue, $fieldDef['metaOptions']);

					// ...and attempt to generate a value
					$generated=$fieldObj->generate();
					if ($generated=$fieldObj->generate()) {
						$bean_data[$field] = $generated;
					}
					else {
						// database will hopefully supply the value
					}
				}
				else {
					// the field is not required so ignore it...
				}
			}
		}
	}

	$bean->load($bean_data);

	if ($bean->save()) {
		$objResponse->call('CodeTable.setKeys', $bean->keyValues);
		$objResponse->call('CodeTable.info','Info:','Item details successfully saved...');
	}
	else {
		//$objResponse->alert($bean->sql);
		$objResponse->call('CodeTable.alert','Error:','Error occurred while trying to save item...'.$bean->sql);
	}
	return $objResponse;
}

$xajax->processRequest();