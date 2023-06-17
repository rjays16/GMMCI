<?php

/**
 * This is the model class for table "seg_or_package_use".
 *
 * The followings are the available columns in table 'seg_or_package_use':
 * @property integer $id
 * @property string $or_refno
 * @property string $package_id
 * @property string $package_amount
 * @property string $rvs_code
 *
 * The followings are the available model relations:
 * @property OrRequest $orRefno
 * @property OrPackagesItems[] $orPackagesItems
 */
class OrPackageUse extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'seg_or_package_use';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('or_refno, package_id', 'required'),
			array('or_refno', 'length', 'max'=>12),
			array('package_id, package_amount, rvs_code', 'length', 'max'=>10),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, or_refno, package_id, package_amount, rvs_code', 'safe', 'on'=>'search'),
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
			'orRefno' => array(self::BELONGS_TO, 'OrRequest', 'or_refno'),
			'orPackagesItems' => array(self::HAS_MANY, 'OrPackagesItems', 'seg_or_package_use_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'or_refno' => 'Or Refno',
			'package_id' => 'Package',
			'package_amount' => 'Package Amount',
			'rvs_code' => 'Rvs Code',
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

		$criteria->compare('id',$this->id);
		$criteria->compare('or_refno',$this->or_refno,true);
		$criteria->compare('package_id',$this->package_id,true);
		$criteria->compare('package_amount',$this->package_amount,true);
		$criteria->compare('rvs_code',$this->rvs_code,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return OrPackageUse the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
