<?php
/**
 * Metadata definition for Product Category codetable
 * @author Alvin Quinones <meinbetween@gmail.com>
 */
global $Dictionary;

$Dictionary['productcategory'] = Array(

	'coreTable' => 'seg_type_product_category',
	'auditTable' => 'seg_type_product_category_audit',
	'deleteFlag' => 'is_deleted',
	'primaryKeys' => Array('id'),
	'fields' => Array(
		'id' => Array(
			'name' => 'id',
			'type'=>'sequence',
			'metaOptions'=> array(
				'sourceTable' => 'seg_type_product_category',
				'sourceField' => 'id'
			),
			'required'=>true
		),
		'description' => Array(
			'name' => 'description',
			'type'=>'text',
			'metaOptions'=>array('length'=>40),
			'required'=>true
		),
		'is_deleted' => Array(
			'name' => 'is_deleted',
			'type'=>'flag',
			'metaOptions'=>array('default'=>0),
			'required'=>false
		)
	)
);