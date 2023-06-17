<?php

/**
 * This is the model class for table "seg_opaccommodation_details".
 *
 * The followings are the available columns in table 'seg_opaccommodation_details':
 * @property string $refno
 * @property integer $entry_no
 * @property integer $room_nr
 * @property integer $group_nr
 * @property string $charge
 * @property string $modify_id
 * @property string $modify_dt
 * @property string $create_id
 * @property string $create_dt
 *
 * The followings are the available model relations:
 * @property Opaccommodation $refno0
 */
class OpaccommodationDetails extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'seg_opaccommodation_details';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('refno, entry_no, room_nr, group_nr', 'required'),
			array('entry_no, room_nr, group_nr', 'numerical', 'integerOnly'=>true),
			array('refno', 'length', 'max'=>12),
			array('charge', 'length', 'max'=>10),
			array('modify_id, create_id', 'length', 'max'=>35),
			array('modify_dt, create_dt', 'safe'),
			array('modify_dt, create_dt','default',
				'value'=>new CDbExpression('NOW()'),
				'setOnEmpty'=>false,'on'=>'insert'),
			array('modify_id, create_id','default',
				'value'=> $_SESSION['sess_temp_userid'],
				'setOnEmpty'=>false,'on'=>'insert'),
			array('modify_dt','default',
				'value'=>new CDbExpression('NOW()'),
				'setOnEmpty'=>false,'on'=>'update'),
			array('modify_id','default',
				'value'=> $_SESSION['sess_temp_userid'],
				'setOnEmpty'=>false,'on'=>'update'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('refno, entry_no, room_nr, group_nr, charge, modify_id, modify_dt, create_id, create_dt', 'safe', 'on'=>'search'),
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
			'refno0' => array(self::BELONGS_TO, 'Opaccommodation', 'refno'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'refno' => 'Refno',
			'entry_no' => 'Entry No',
			'room_nr' => 'Room Nr',
			'group_nr' => 'Group Nr',
			'charge' => 'Room Charge',
			'modify_id' => 'Modify',
			'modify_dt' => 'Modify Dt',
			'create_id' => 'Create',
			'create_dt' => 'Create Dt',
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

		$criteria->compare('refno',$this->refno,true);
		$criteria->compare('entry_no',$this->entry_no);
		$criteria->compare('room_nr',$this->room_nr);
		$criteria->compare('group_nr',$this->group_nr);
		$criteria->compare('charge',$this->charge,true);
		$criteria->compare('modify_id',$this->modify_id,true);
		$criteria->compare('modify_dt',$this->modify_dt,true);
		$criteria->compare('create_id',$this->create_id,true);
		$criteria->compare('create_dt',$this->create_dt,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return OpaccommodationDetails the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
