<?php
/**
 * EncounterDisposition.php
 *
 */


/**
 * This is the model class for table "seg_encounter_disposition".
 *
 * The followings are the available columns in table 'seg_encounter_disposition':
 * @property string $encounter_nr
 * @property integer $disp_code
 * @property string $modify_id
 * @property string $modify_time
 * @property string $create_id
 * @property string $create_time
 */
class EncounterDisposition extends CareActiveRecord
{
	/**
     *
	 * @return string the associated database table name
	 */
	public function tableName() {
		return 'seg_encounter_disposition';
	}

	/**
     *
	 * @return array validation rules for model attributes.
	 */
	public function rules() {
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('encounter_nr, disp_code, modify_id, create_id', 'required'),
			array('disp_code', 'numerical', 'integerOnly'=>true),
			array('encounter_nr', 'length', 'max'=>12),
			array('modify_id, create_id', 'length', 'max'=>35),
			array('modify_time, create_time', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('encounter_nr, disp_code, modify_id, modify_time, create_id, create_time', 'safe', 'on'=>'search'),
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
			'encounter' => array(self::BELONGS_TO, 'Encounter', 'encounter_nr'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels() {
		return array(
			'encounter_nr' => 'Encounter Nr',
			'disp_code' => 'Disposition Code',
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
	 * @return EncounterDisposition the static model class
	 */
	public static function model($className=__CLASS__) {
		return parent::model($className);
	}
}
