<?php

/**
 * View definition of dosage codetable for the List action
 */
global $Views;

$Views['Edit']['dosage'] = Array(
	// the title of the interface
	'title' => 'Edit dosage details',

	// column widths (in percentage) of each item
	'widths' => Array( 'label'=>15, 'field'=>40, 'description'=>45 ),

	// the panels definition defines list of panels visible in the Editor interface, each panel
	// represents a logical grouping of related interface elements, Edit view definition should
	// have at least one panel
	'panels' => Array(
		// each panel is referenced by an id
		'DOSAGE_DETAILS' => Array(
			// this is the label of the panel
			'label' => 'Dosage details',

			// each panel has a collection of panel Items which correspond to a field defined in the metadata definition
			'items' => array(

				// a unique id to reference the panel Item, the ideal format would be {FIELDNAME}_{NAME OF VIEW}
				'DOSAGE_EDIT_VIEW' => array(
					// the corresponding metadata field definiton for this panel Item
					'field' => 'dosage',
					// the label for this panel Item
					'label' => 'Dosage',
					// default Value for this item, this is also the default value displayed when we are
					//   creating a new item
					'default' => '',
					// verbose description of the panel Item
					'description' => 'Dosage specification of medicine',
					// is this item required to have a value?
					'required' => true,
					// display parameters related to the field; format is different depending on the data type
					//   of the field as defined in the metadata defintion
					// in this instance, the `dosage` field is of type `text`, so the number of text rows for the
					//   textarea element can be specified
					// the possible options for each field type are defined in the dynamicfields definition file
					'editOptions' => array(
						'rows' => 1
					)
				),
				'TIMES_EDIT_VIEW' => array(
					'field' => 'times_a_day',
					'label' => 'Dosage quantity',
					'default' => '0',
					'description' => 'Quantity of medicine prescribed daily',
					'required' => true,
					'editOptions' => array(
						'decimal'=>0
					)
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