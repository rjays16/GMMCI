<?php

/**
 * This is the model class for table "seg_pmrf_dependent".
 *
 * The followings are the available columns in table 'seg_pmrf_dependent':
 * @property string $id
 * @property string $pmrf_id
 * @property string $relation
 * @property string $pin
 * @property string $last_name
 * @property string $first_name
 * @property string $name_extension
 * @property string $middle_name
 * @property string $birth_date
 * @property string $sex
 * @property integer $is_disabled
 * @property integer $is_deleted
 * @property string $create_id
 * @property string $create_time
 * @property string $modify_id
 * @property string $modify_time
 *
 * The followings are the available model relations:
 * @property Pmrf $id0
 */
class PmrfDependent extends CareActiveRecord
{

	const RELATION_CHILD = 'c';
	const RELATION_SPOUSE = 's';
	const RELATION_FATHER = 'f';
	const RELATION_MOTHER = 'm';

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'seg_pmrf_dependent';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('is_disabled, is_deleted', 'numerical', 'integerOnly'=>true),
			array('pmrf_id', 'length', 'max'=>10),
			array('relation, sex', 'length', 'max'=>1),
			array('pin', 'length', 'max'=>24),
			array('last_name, first_name, name_extension, middle_name, create_id, modify_id', 'length', 'max'=>100),
			array('birth_date, create_time, modify_time', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, pmrf_id, relation, pin, last_name, first_name, name_extension, middle_name, birth_date, sex, is_disabled, is_deleted, create_id, create_time, modify_id, modify_time', 'safe', 'on'=>'search'),
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
			'id0' => array(self::BELONGS_TO, 'Pmrf', 'id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'pmrf_id' => 'Pmrf',
			'relation' => 'Relation',
			'pin' => 'Pin',
			'last_name' => 'Last Name',
			'first_name' => 'First Name',
			'name_extension' => 'Name Extension',
			'middle_name' => 'Middle Name',
			'birth_date' => 'Birth Date',
			'sex' => 'Sex',
			'is_disabled' => 'Is Disabled',
			'is_deleted' => 'Is Deleted',
			'create_id' => 'Create',
			'create_time' => 'Create Time',
			'modify_id' => 'Modify',
			'modify_time' => 'Modify Time',
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

		$criteria->compare('id',$this->id,true);
		$criteria->compare('pmrf_id',$this->pmrf_id,true);
		$criteria->compare('relation',$this->relation,true);
		$criteria->compare('pin',$this->pin,true);
		$criteria->compare('last_name',$this->last_name,true);
		$criteria->compare('first_name',$this->first_name,true);
		$criteria->compare('name_extension',$this->name_extension,true);
		$criteria->compare('middle_name',$this->middle_name,true);
		$criteria->compare('birth_date',$this->birth_date,true);
		$criteria->compare('sex',$this->sex,true);
		$criteria->compare('is_disabled',$this->is_disabled);
		$criteria->compare('is_deleted',$this->is_deleted);
		$criteria->compare('create_id',$this->create_id,true);
		$criteria->compare('create_time',$this->create_time,true);
		$criteria->compare('modify_id',$this->modify_id,true);
		$criteria->compare('modify_time',$this->modify_time,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return PmrfDependent the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
