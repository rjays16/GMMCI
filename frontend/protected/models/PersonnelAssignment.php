<?php

/**
 * This is the model class for table "care_personell_assignment".
 *
 * The followings are the available columns in table 'care_personell_assignment':
 * @property string $nr
 * @property string $personell_nr
 * @property integer $role_nr
 * @property integer $location_type_nr
 * @property integer $location_nr
 * @property string $date_start
 * @property string $date_end
 * @property integer $is_temporary
 * @property string $list_frequency
 * @property string $status
 * @property string $history
 * @property string $modify_id
 * @property string $modify_time
 * @property string $create_id
 * @property string $create_time
 */
class PersonnelAssignment extends CareActiveRecord{


	const STATUS_DELETED = 'deleted';

	/**
	 * @return string the associated database table name
	 */
	public function tableName(){
		return 'care_personell_assignment';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules(){
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('history, modify_time', 'required'),
			array('role_nr, location_type_nr, location_nr, is_temporary', 'numerical', 'integerOnly'=>true),
			array('personell_nr, list_frequency', 'length', 'max'=>11),
			array('status', 'length', 'max'=>25),
			array('modify_id, create_id', 'length', 'max'=>35),
			array('date_start, date_end, create_time', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('nr, personell_nr, role_nr, location_type_nr, location_nr, date_start, date_end, is_temporary, list_frequency, status, history, modify_id, modify_time, create_id, create_time', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations(){
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
			'personnel'=>array(self::BELONGS_TO, 'Personnel','personell_nr'),
			'department'=>array(self::BELONGS_TO,'Department','location_nr'),
		);

	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels(){

		return array(
			'nr' => 'Nr',
			'personell_nr' => 'Personell Nr',
			'role_nr' => 'Role Nr',
			'location_type_nr' => 'Location Type Nr',
			'location_nr' => 'Location Nr',
			'date_start' => 'Date Start',
			'date_end' => 'Date End',
			'is_temporary' => 'Is Temporary',
			'list_frequency' => 'List Frequency',
			'status' => 'Status',
			'history' => 'History',
			'modify_id' => 'Modify',
			'modify_time' => 'Modify Time',
			'create_id' => 'Create',
			'create_time' => 'Create Time',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 *
	 * Typical usecase:
	 * - Initialize the model fields with values from filter form.
	 * - Execute this method to get CActiveDataProvider instance which will filter
	 * models according to data in model fields.
	 * - Pass data provider to CGridView, CListView or any similar widget.
	 *
	 * @return CActiveDataProvider the data provider that can return the models
	 * based on the search/filter conditions.
	 */
	public function search(){
		// @todo Please modify the following code to remove attributes that should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('nr',$this->nr,true);
		$criteria->compare('personell_nr',$this->personell_nr,true);
		$criteria->compare('role_nr',$this->role_nr);
		$criteria->compare('location_type_nr',$this->location_type_nr);
		$criteria->compare('location_nr',$this->location_nr);
		$criteria->compare('date_start',$this->date_start,true);
		$criteria->compare('date_end',$this->date_end,true);
		$criteria->compare('is_temporary',$this->is_temporary);
		$criteria->compare('list_frequency',$this->list_frequency,true);
		$criteria->compare('status',$this->status,true);
		$criteria->compare('history',$this->history,true);
		$criteria->compare('modify_id',$this->modify_id,true);
		$criteria->compare('modify_time',$this->modify_time,true);
		$criteria->compare('create_id',$this->create_id,true);
		$criteria->compare('create_time',$this->create_time,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return CarePersonellAssignment the static model class
	 */
	public static function model($className=__CLASS__){
		return parent::model($className);
	}
}
