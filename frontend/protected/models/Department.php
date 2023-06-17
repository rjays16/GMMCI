<?php

/**
 * This is the model class for table "care_department".
 *
 * The followings are the available columns in table 'care_department':
 * @property integer $nr
 * @property string $id
 * @property string $type
 * @property string $name_formal
 * @property string $name_short
 * @property string $name_alternate
 * @property string $LD_var
 * @property string $description
 * @property integer $admit_inpatient
 * @property integer $admit_outpatient
 * @property integer $has_oncall_doc
 * @property integer $has_oncall_nurse
 * @property integer $does_surgery
 * @property integer $this_institution
 * @property integer $is_sub_dept
 * @property integer $parent_dept_nr
 * @property string $work_hours
 * @property string $consult_hours
 * @property integer $is_inactive
 * @property integer $sort_order
 * @property string $address
 * @property string $sig_line
 * @property string $sig_stamp
 * @property string $logo_mime_type
 * @property string $status
 * @property string $history
 * @property string $modify_id
 * @property string $modify_time
 * @property string $create_id
 * @property string $create_time
 * @property integer $for_male_only
 * @property integer $for_female_only
 * @property integer $for_child_only
 * @property integer $child_age_limit
 *
 * The followings are the available model relations:
 * @property SegAreas[] $segAreases
 * @property SegHospEod[] $segHospEods
 * @property SegHospInventory[] $segHospInventories
 * @property SegOrSchedulerDeckingLimit $segOrSchedulerDeckingLimit
 * @property SegReptbl[] $segReptbls
 */
class Department extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName(){

		return 'care_department';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules(){
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('type, description, modify_time', 'required'),
			array('admit_inpatient, admit_outpatient, has_oncall_doc, has_oncall_nurse, does_surgery, this_institution, is_sub_dept, parent_dept_nr, is_inactive, sort_order, for_male_only, for_female_only, for_child_only, child_age_limit', 'numerical', 'integerOnly'=>true),
			array('id, name_formal, sig_line', 'length', 'max'=>60),
			array('type, status', 'length', 'max'=>25),
			array('name_short, LD_var, modify_id, create_id', 'length', 'max'=>35),
			array('name_alternate', 'length', 'max'=>225),
			array('work_hours, consult_hours', 'length', 'max'=>100),
			array('logo_mime_type', 'length', 'max'=>5),
			array('address, sig_stamp, history, create_time', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('nr, id, type, name_formal, name_short, name_alternate, LD_var, description, admit_inpatient, admit_outpatient, has_oncall_doc, has_oncall_nurse, does_surgery, this_institution, is_sub_dept, parent_dept_nr, work_hours, consult_hours, is_inactive, sort_order, address, sig_line, sig_stamp, logo_mime_type, status, history, modify_id, modify_time, create_id, create_time, for_male_only, for_female_only, for_child_only, child_age_limit', 'safe', 'on'=>'search'),
		);
	}


	/**
	 * @return array relational rules.
	 */
	public function relations(){
		return array(
            'orRequests' => array(self::HAS_MANY, 'OrRequest', 'dept_nr'),
        );
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels(){

		return array(
			'nr' => 'Nr',
			'id' => 'ID',
			'type' => 'Type',
			'name_formal' => 'Name Formal',
			'name_short' => 'Name Short',
			'name_alternate' => 'Name Alternate',
			'LD_var' => 'Ld Var',
			'description' => 'Description',
			'admit_inpatient' => 'Admit Inpatient',
			'admit_outpatient' => 'Admit Outpatient',
			'has_oncall_doc' => 'Has Oncall Doc',
			'has_oncall_nurse' => 'Has Oncall Nurse',
			'does_surgery' => 'Does Surgery',
			'this_institution' => 'This Institution',
			'is_sub_dept' => 'Is Sub Dept',
			'parent_dept_nr' => 'Parent Dept Nr',
			'work_hours' => 'Work Hours',
			'consult_hours' => 'Consult Hours',
			'is_inactive' => 'Is Inactive',
			'sort_order' => 'Sort Order',
			'address' => 'Address',
			'sig_line' => 'Sig Line',
			'sig_stamp' => 'Sig Stamp',
			'logo_mime_type' => 'Logo Mime Type',
			'status' => 'Status',
			'history' => 'History',
			'modify_id' => 'Modify',
			'modify_time' => 'Modify Time',
			'create_id' => 'Create',
			'create_time' => 'Create Time',
			'for_male_only' => 'For Male Only',
			'for_female_only' => 'For Female Only',
			'for_child_only' => 'For Child Only',
			'child_age_limit' => 'Child Age Limit',
		);
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return CareDepartment the static model class
	 */
	public static function model($className=__CLASS__){
		return parent::model($className);
	}

	public function scopes(){
		return array(
			'allOPDMedical' => array(
				'condition' => "type = 1 AND is_inactive = '0'",
			)
		);
	}
}
