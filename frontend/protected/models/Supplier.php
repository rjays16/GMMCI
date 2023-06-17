<?php

/**
 * This is the model class for table "seg_supplier".
 *
 * The followings are the available columns in table 'seg_supplier':
 * @property string $supplier_id
 * @property string $name
 * @property string $addr
 * @property string $phone
 * @property string $email
 * @property string $history
 * @property string $modify_id
 * @property string $modify_time
 * @property string $create_id
 * @property string $create_time
 *
 * The followings are the available model relations:
 * @property SegDelivery[] $segDeliveries
 */
class Supplier extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'seg_supplier';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('name, history, modify_id, modify_time, create_id', 'required'),
			array('name, email', 'length', 'max'=>60),
			array('addr', 'length', 'max'=>255),
			array('phone, modify_id, create_id', 'length', 'max'=>35),
			array('create_time', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('supplier_id, name, addr, phone, email, history, modify_id, modify_time, create_id, create_time', 'safe', 'on'=>'search'),
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
			'segDeliveries' => array(self::HAS_MANY, 'SegDelivery', 'supplier_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'supplier_id' => 'Supplier',
			'name' => 'Name',
			'addr' => 'Addr',
			'phone' => 'Phone',
			'email' => 'Email',
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

		$criteria->compare('supplier_id',$this->supplier_id,true);
		$criteria->compare('name',$this->name,true);
		$criteria->compare('addr',$this->addr,true);
		$criteria->compare('phone',$this->phone,true);
		$criteria->compare('email',$this->email,true);
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
	 * @return Supplier the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
