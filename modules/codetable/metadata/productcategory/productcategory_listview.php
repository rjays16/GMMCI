<?php

/*
 * View definition of dosage codetable for list action
 */
global $Views;

$Views['List']['productcategory'] = Array(

	/**
	*  title for the Codetable list view page
	*/
	'title' => 'List of Product Category',
	'search' => Array(
	'PRODUCT_CATEGORY_SEARCH_BASIC'=>Array(
				'label' => 'Search Category',
				'columns' => 4,
				'widths' => array('label'=>5, 'field'=>50, 'filler'=>15),
				'filters'=>Array(
							'PRODUCT_CATEGORY_SEARCH_BASIC_ID'=>array(
								'label'=>'ID NO.',
								'field'=>'id',
								'searchOptions' => array()
							),
							'PRODUCT_CATEGORY_SEARCH_BASIC_DESCRIPTION'=>array(
								'label'=>'Description',
								'field'=>'description',
								'searchOptions' => array()
							)
					)
				)
	),
'columns' => Array(
		'LIST_VIEW_ID' => array(
			'field' => 'id',
			'width' => 150,
			'label' => 'ID',
			'sortable' => true,
			'sorting' => 'asc'
		),
		'LIST_VIEW_DESCRIPTION' => array(
			'field' => 'description',
			'width' => 400,
			'label' => 'Description',
			'sortable' => true,
			'sorting' => 'asc'
		),
		'LIST_VIEW_DELETED' => array(
			'field' => 'is_deleted',
			'width' => 70,
			'label' => 'Deleted',
			'sortable' => true,
		)
	)
);

