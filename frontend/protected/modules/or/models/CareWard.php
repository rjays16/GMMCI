<?php

/**
 * This is the model class for table "care_ward".
 *
 * The followings are the available columns in table 'care_ward':
 * @property integer $nr
 * @property integer $accomodation_type
 * @property string $ward_id
 * @property string $name
 * @property integer $is_temp_closed
 * @property integer $is_orpacu
 * @property string $date_create
 * @property string $date_close
 * @property string $description
 * @property string $info
 * @property integer $dept_nr
 * @property integer $room_nr_start
 * @property integer $room_nr_end
 * @property string $roomprefix
 * @property double $mandatory_excess
 * @property string $status
 * @property string $prototype
 * @property string $history
 * @property string $modify_id
 * @property string $modify_time
 * @property string $create_id
 * @property string $create_time
 */
class CareWard extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'care_ward';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('accomodation_type, ward_id, name, status, history', 'required'),
			array('accomodation_type, is_temp_closed, is_orpacu, dept_nr, room_nr_start, room_nr_end', 'numerical', 'integerOnly'=>true),
			array('mandatory_excess', 'numerical'),
			array('ward_id, name', 'length', 'max'=>35),
			array('roomprefix', 'length', 'max'=>4),
			array('status, modify_id, create_id', 'length', 'max'=>25),
			array('prototype', 'length', 'max'=>7),
			array('date_create, date_close, description, info, modify_time, create_time', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('nr, accomodation_type, ward_id, name, is_temp_closed, is_orpacu, date_create, date_close, description, info, dept_nr, room_nr_start, room_nr_end, roomprefix, mandatory_excess, status, prototype, history, modify_id, modify_time, create_id, create_time', 'safe', 'on'=>'search'),
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
			'room' => array(self::HAS_ONE, 'CareRoom', 'ward_nr'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'nr' => 'Nr',
			'accomodation_type' => 'Accomodation Type',
			'ward_id' => 'Ward',
			'name' => 'Name',
			'is_temp_closed' => 'Is Temp Closed',
			'is_orpacu' => 'Is Orpacu',
			'date_create' => 'Date Create',
			'date_close' => 'Date Close',
			'description' => 'Description',
			'info' => 'Info',
			'dept_nr' => 'Dept Nr',
			'room_nr_start' => 'Room Nr Start',
			'room_nr_end' => 'Room Nr End',
			'roomprefix' => 'Roomprefix',
			'mandatory_excess' => 'Mandatory Excess',
			'status' => 'Status',
			'prototype' => 'Prototype',
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
	public function search()
	{
		// @todo Please modify the following code to remove attributes that should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('nr',$this->nr);
		$criteria->compare('accomodation_type',$this->accomodation_type);
		$criteria->compare('ward_id',$this->ward_id,true);
		$criteria->compare('name',$this->name,true);
		$criteria->compare('is_temp_closed',$this->is_temp_closed);
		$criteria->compare('is_orpacu',$this->is_orpacu);
		$criteria->compare('date_create',$this->date_create,true);
		$criteria->compare('date_close',$this->date_close,true);
		$criteria->compare('description',$this->description,true);
		$criteria->compare('info',$this->info,true);
		$criteria->compare('dept_nr',$this->dept_nr);
		$criteria->compare('room_nr_start',$this->room_nr_start);
		$criteria->compare('room_nr_end',$this->room_nr_end);
		$criteria->compare('roomprefix',$this->roomprefix,true);
		$criteria->compare('mandatory_excess',$this->mandatory_excess);
		$criteria->compare('status',$this->status,true);
		$criteria->compare('prototype',$this->prototype,true);
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
	 * @return CareWard the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	public function getWardOr(){
		$criteria = new CDbCriteria();

		$criteria->with = array(
			'room' => array('joinType' => 'LEFT JOIN'),
		);
		$criteria->condition = 't.ward_id = "OR"';

		return $this->find($criteria);
	}
}
