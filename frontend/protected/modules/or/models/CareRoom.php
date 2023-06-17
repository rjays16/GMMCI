<?php

/**
 * This is the model class for table "care_room".
 *
 * The followings are the available columns in table 'care_room':
 * @property string $nr
 * @property integer $type_nr
 * @property string $date_create
 * @property string $date_close
 * @property integer $is_temp_closed
 * @property integer $room_nr
 * @property integer $ward_nr
 * @property integer $dept_nr
 * @property integer $nr_of_beds
 * @property string $closed_beds
 * @property string $info
 * @property string $status
 * @property string $history
 * @property string $modify_id
 * @property string $modify_time
 * @property string $create_id
 * @property string $create_time
 */
class CareRoom extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'care_room';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('history', 'required'),
			array('type_nr, is_temp_closed, room_nr, ward_nr, dept_nr, nr_of_beds', 'numerical', 'integerOnly'=>true),
			array('closed_beds', 'length', 'max'=>255),
			array('info', 'length', 'max'=>60),
			array('status', 'length', 'max'=>25),
			array('modify_id, create_id', 'length', 'max'=>35),
			array('date_create, date_close, modify_time, create_time', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('nr, type_nr, date_create, date_close, is_temp_closed, room_nr, ward_nr, dept_nr, nr_of_beds, closed_beds, info, status, history, modify_id, modify_time, create_id, create_time', 'safe', 'on'=>'search'),
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
			'nr' => 'Nr',
			'type_nr' => 'Type Nr',
			'date_create' => 'Date Create',
			'date_close' => 'Date Close',
			'is_temp_closed' => 'Is Temp Closed',
			'room_nr' => 'Room Nr',
			'ward_nr' => 'Ward Nr',
			'dept_nr' => 'Dept Nr',
			'nr_of_beds' => 'Nr Of Beds',
			'closed_beds' => 'Closed Beds',
			'info' => 'Info',
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
	public function search()
	{
		// @todo Please modify the following code to remove attributes that should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('nr',$this->nr,true);
		$criteria->compare('type_nr',$this->type_nr);
		$criteria->compare('date_create',$this->date_create,true);
		$criteria->compare('date_close',$this->date_close,true);
		$criteria->compare('is_temp_closed',$this->is_temp_closed);
		$criteria->compare('room_nr',$this->room_nr);
		$criteria->compare('ward_nr',$this->ward_nr);
		$criteria->compare('dept_nr',$this->dept_nr);
		$criteria->compare('nr_of_beds',$this->nr_of_beds);
		$criteria->compare('closed_beds',$this->closed_beds,true);
		$criteria->compare('info',$this->info,true);
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
	 * @return CareRoom the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
