<?php

/**
 * View definition of Product Category codetable for the List action
 */
global $Views;

$Views['Edit']['productcategory'] = Array(
	'title' => 'Edit Product Category details',
	'widths' => Array( 'label'=>15, 'field'=>40, 'description'=>45 ),
	'panels' => Array(
		'PRODUCT_CATEGORY_DETAILS' => Array(
					'label' => 'Product Category details',
					'items' => array(
						'PRODUCT_CATEGORY_VIEW' => array(
																						'field' => 'description',
																						'label' => 'Description',
																						'default' => '',
																						'description' => 'Product Category',
																						'required' => true,
																						'options' => array('rows' => 1)
								),
				'DELETED_EDIT_VIEW' => array(
					'field' => 'is_deleted',
					'label' => 'Deleted?',
					'default' => '0',
					'description' => 'Mark entry as deleted',
					'required' => false,
					// field `is_deleted` is of type flag
					'editOptions' => array()
				)
					)
				)
	)
);