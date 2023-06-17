<?php
/**
 * Metadata definition for dosage codetable
 * @author Alvin Quinones <meinbetween@gmail.com>
 */
global $Dictionary;

$Dictionary['dosage'] = Array(

	// the name of the core table for this codetable object
	'coreTable' => 'seg_dosages',

	// audit table to be used (OPTIONAL)
	'auditTable' => 'seg_dosages_audit',

	// the name of the field in the core table which will be used as the flag for logical deletions
	'deleteFlag' => 'is_deleted',

	// the primary keys of the codeTable ( alternatively, we can use ADODB::MetaPrimaryKeys for this )
	'primaryKeys' => Array('id'),

	// identify the fields of the core table and classify as to what type of data they hold
	'fields' => Array(

		// first field is named 'id'
		'id' => Array(

			// redeclare the name, for backreferencing purposes
			'name' => 'id',
			// field type is sequence, this tells the parser that the dynamicField handler to be used is the class FieldSequence
			'type'=>'guid',
			// list of metadata options for the field, the available options are documented in the DynamicField class that this field uses
			'metaOptions'=> array(
//				'min'=>1,
//				'max'=>999999,
//				'increment'=>1,
//				'sourceTable'=>'seg_dosages',
//				'sourceField'=>'id'
			),
			// is this field required? The parser checks for a generate function in the DynamicField handler class. If
			// it couldn't find one, it makes sure that the user supplies data for the field
			'required'=>true
		),

		'dosage' => Array(
			'name' => 'dosage',
			'type'=>'text',
			'metadataOptions'=>array('length'=>20),
			'required'=>true
		),
		'times_a_day' => Array(
			'name' => 'times_a_day',
			'type'=>'numeric',
			'metaOptions'=>array(
				'decimal' => 0
			),
			'required'=>true
		),
		'is_deleted' => Array(
			'name' => 'is_deleted',
			'type'=>'flag',
			'metaOptions'=>array('default'=>0),
			'required'=>false
		)
//		,
//		'details' => Array(
//			'name' => 'details',
//			'type' => 'collection',
//			'metaOptions' => array()
//		)
	)
);