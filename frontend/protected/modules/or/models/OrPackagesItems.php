<?php

/**
 * This is the model class for table "seg_or_packages_items".
 *
 * The followings are the available columns in table 'seg_or_packages_items':
 * @property integer $id
 * @property integer $seg_or_package_use_id
 * @property string $or_refno
 * @property integer $package_id
 * @property string $item_code
 * @property double $qty
 * @property double $price
 *
 * The followings are the available model relations:
 * @property OrPackageUse $segOrPackageUse
 */
class OrPackagesItems extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'seg_or_packages_items';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('or_refno, package_id, item_code, qty, price', 'required'),
			array('seg_or_package_use_id, package_id', 'numerical', 'integerOnly'=>true),
			array('qty, price', 'numerical'),
			array('or_refno, item_code', 'length', 'max'=>12),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, seg_or_package_use_id, or_refno, package_id, item_code, qty, price', 'safe', 'on'=>'search'),
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
			'segOrPackageUse' => array(self::BELONGS_TO, 'OrPackageUse', 'seg_or_package_use_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'seg_or_package_use_id' => 'Seg Or Package Use',
			'or_refno' => 'Or Refno',
			'package_id' => 'Package',
			'item_code' => 'Item Code',
			'qty' => 'Qty',
			'price' => 'Price',
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
		$criteria->compare('seg_or_package_use_id',$this->seg_or_package_use_id);
		$criteria->compare('or_refno',$this->or_refno,true);
		$criteria->compare('package_id',$this->package_id);
		$criteria->compare('item_code',$this->item_code,true);
		$criteria->compare('qty',$this->qty);
		$criteria->compare('price',$this->price);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return OrPackagesItems the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
