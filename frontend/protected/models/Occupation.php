<?php

/**
 * This is the model class for table "seg_occupation".
 *
 * The followings are the available columns in table 'seg_occupation':
 * @property integer $occupation_nr
 * @property string $occupation_name
 * @property string $modify_id
 * @property string $modify_date
 * @property string $create_id
 * @property string $create_date
 */
class Occupation extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'seg_occupation';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('occupation_name', 'required'),
			array('occupation_name, modify_id, create_id', 'length', 'max'=>50),
			array('modify_date, create_date', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('occupation_nr, occupation_name, modify_id, modify_date, create_id, create_date', 'safe', 'on'=>'search'),
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
			'occupation_nr' => 'Occupation Nr',
			'occupation_name' => 'Occupation Name',
			'modify_id' => 'Modify',
			'modify_date' => 'Modify Date',
			'create_id' => 'Create',
			'create_date' => 'Create Date',
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

		$criteria->compare('occupation_nr',$this->occupation_nr);
		$criteria->compare('occupation_name',$this->occupation_name,true);
		$criteria->compare('modify_id',$this->modify_id,true);
		$criteria->compare('modify_date',$this->modify_date,true);
		$criteria->compare('create_id',$this->create_id,true);
		$criteria->compare('create_date',$this->create_date,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Occupation the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
