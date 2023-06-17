<?php

/**
 * This is the model class for table "care_type_encounter".
 *
 * The followings are the available columns in table 'care_type_encounter':
 * @property string $type_nr
 * @property string $type
 * @property string $name
 * @property string $LD_var
 * @property string $description
 * @property integer $hide_from
 * @property string $status
 * @property string $history
 * @property string $modify_id
 * @property string $modify_time
 * @property string $create_id
 * @property string $create_time
 */
class EncounterType extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'care_type_encounter';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('history, modify_time', 'required'),
			array('hide_from', 'numerical', 'integerOnly'=>true),
			array('type, name, modify_id, create_id', 'length', 'max'=>35),
			array('LD_var, status', 'length', 'max'=>25),
			array('description', 'length', 'max'=>255),
			array('create_time', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('type_nr, type, name, LD_var, description, hide_from, status, history, modify_id, modify_time, create_id, create_time', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'type_nr' => 'Type Nr',
			'type' => 'Type',
			'name' => 'Name',
			'LD_var' => 'Ld Var',
			'description' => 'Description',
			'hide_from' => 'Hide From',
			'status' => 'Status',
			'history' => 'History',
			'modify_id' => 'Modify',
			'modify_time' => 'Modify Time',
			'create_id' => 'Create',
			'create_time' => 'Create Time',
		);
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return CareTypeEncounter the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
