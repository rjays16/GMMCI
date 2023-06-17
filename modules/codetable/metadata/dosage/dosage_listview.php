<?php

/*
 * View definition of dosage codetable for list action
 */
global $Views;

$Views['List']['dosage'] = Array(

	/**
	*  title for the Codetable list view page
	*/
	'title' => 'List of dosage specifications',

	/**
	 * This area specifies Search options for the List View. Defined below are the list
	 * of panels that the user can use for searching
	*/
	'search' => Array(
		// we need to specify a unique id for each panel
		// this is the BASIC SEARCH panel
		'DOSAGE_SEARCH_BASIC' => Array(

			// the label for the panel
			'label' => 'Basic search',

			// number of search filters to display per row
			'columns' => 3,

			// standard widths (in %) for each filter
			// the options are for the filter's label, the filter field and the filler space
			'widths' => array('label'=>15, 'field'=>70, 'filler'=>15),

			// the search filters
			'filters' => Array(

				// need a unique ID for the search filter
				'DOSAGE_SEARCH_BASIC_ID' => array(
					// the label for the search filter
					'label'=>'Id no.',
					// the corresponding field in the metadata definition for this search filter
					'field'=>'id',
					// search view options, as defined in the DynamicField handler
					'searchOptions' => array()
				),
				'DOSAGE_SEARCH_BASIC_DOSAGE' => array(
					'label'=>'Dosage',
					'field'=>'dosage',
					'searchOptions' => array(
						'modes' => 'startswith,endswith,contains,doesnotcontain,exactly'
					)
				),
				'DOSAGE_SEARCH_BASIC_DELETED' => array(
					'label'=>'Deleted items',
					'field'=>'is_deleted',
					'searchOptions' => array(
						'defaultText' => '--Select one--',
						'onText' => 'Deleted items only',
						'offText' => 'Active items only',
						'allText' => 'Show all',
					)
				),
				'DOSAGE_SEARCH_BASIC_TIMES' => array(
					'label'=>'Qty/day',
					'field'=>'times_a_day',
					'options' => array()
				)
			)
		),

		// New panel, blank for now
		'DOSAGE_SEARCH_ADV' => Array(
			'label' => 'Advanced search',
			'widths' => array('label'=>20, 'field'=>70, 'filler'=>10),
			'filters' => Array(
			)
		)
	),



	/**
	* This area defines the columns for the
	*/
	'columns' => Array(
		'LIST_VIEW_ID' => array(
			'field' => 'id',
			'width' => 150,
			'label' => 'ID',
			'sortable' => true
		),
		'LIST_VIEW_DOSAGE' => array(
			'field' => 'dosage',
			'width' => 150,
			'label' => 'Dosage',
			'sortable' => true,
			'sorting' => 'asc',
		),
		'LIST_VIEW_TIMES' => array(
			'field' => 'times_a_day',
			'width' => 70,
			'label' => 'Quantity',
			'sortable' => true,
		),
		'LIST_VIEW_DELETED' => array(
			'field' => 'is_deleted',
			'width' => 70,
			'label' => 'Deleted',
			'sortable' => true,
		)
	)
);

