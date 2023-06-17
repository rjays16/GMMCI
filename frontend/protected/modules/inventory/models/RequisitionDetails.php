<?php

/**
 * This is the model class for table "seg_internal_request_details".
 *
 * The followings are the available columns in table 'seg_internal_request_details':
 * @property string $refno
 * @property string $item_code
 * @property double $item_qty
 * @property string $unit_id
 * @property integer $is_unitperpc
 *
 * The followings are the available model relations:
 * @property SegInternalRequest $refno0
 * @property CarePharmaProductsMain $itemCode
 * @property SegUnit $unit
 */
class RequisitionDetails extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'seg_internal_request_details';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('refno, item_code, unit_id, is_unitperpc', 'required'),
			array('is_unitperpc', 'numerical', 'integerOnly'=>true),
			array('item_qty', 'numerical'),
			array('refno', 'length', 'max'=>12),
			array('item_code', 'length', 'max'=>25),
			array('unit_id', 'length', 'max'=>10),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('refno, item_code, item_qty, unit_id, is_unitperpc', 'safe', 'on'=>'search'),
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
			'requisition' => array(self::BELONGS_TO, 'Requisition', 'refno'),
			'item' => array(self::BELONGS_TO, 'ItemCatalog', 'item_code'),
			'unit' => array(self::BELONGS_TO, 'Unit', 'unit_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
    {
		return array(
			'refno' => 'Refno',
			'item_code' => 'Item Code',
			'item_qty' => 'Item Qty',
			'unit_id' => 'Unit',
			'is_unitperpc' => 'Is Unitperpc',
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
		$criteria->compare('item_code',$this->item_code,true);
		$criteria->compare('item_qty',$this->item_qty);
		$criteria->compare('unit_id',$this->unit_id,true);
		$criteria->compare('is_unitperpc',$this->is_unitperpc);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return RequisitionDetails the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
