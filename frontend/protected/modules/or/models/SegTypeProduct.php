<?php

/**
 * This is the model class for table "seg_type_product".
 *
 * The followings are the available columns in table 'seg_type_product':
 * @property string $nr
 * @property string $type_code
 * @property string $name
 * @property string $description
 * @property string $prod_class
 * @property integer $is_inactive
 * @property integer $is_withexpiry
 * @property integer $is_withserial
 * @property string $modify_id
 * @property string $modify_dt
 * @property string $create_id
 * @property string $create_dt
 */
class SegTypeProduct extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'seg_type_product';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('type_code, is_inactive, is_withexpiry, is_withserial, modify_id, modify_dt, create_id', 'required'),
			array('is_inactive, is_withexpiry, is_withserial', 'numerical', 'integerOnly'=>true),
			array('type_code', 'length', 'max'=>4),
			array('name', 'length', 'max'=>80),
			array('description', 'length', 'max'=>150),
			array('prod_class', 'length', 'max'=>2),
			array('modify_id, create_id', 'length', 'max'=>35),
			array('create_dt', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('nr, type_code, name, description, prod_class, is_inactive, is_withexpiry, is_withserial, modify_id, modify_dt, create_id, create_dt', 'safe', 'on'=>'search'),
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
			'type_code' => 'Type Code',
			'name' => 'Name',
			'description' => 'Description',
			'prod_class' => 'Prod Class',
			'is_inactive' => 'Is Inactive',
			'is_withexpiry' => 'Is Withexpiry',
			'is_withserial' => 'Is Withserial',
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

		$criteria->compare('nr',$this->nr,true);
		$criteria->compare('type_code',$this->type_code,true);
		$criteria->compare('name',$this->name,true);
		$criteria->compare('description',$this->description,true);
		$criteria->compare('prod_class',$this->prod_class,true);
		$criteria->compare('is_inactive',$this->is_inactive);
		$criteria->compare('is_withexpiry',$this->is_withexpiry);
		$criteria->compare('is_withserial',$this->is_withserial);
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
	 * @return SegTypeProduct the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
