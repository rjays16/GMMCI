<?php

/**
 * This is the model class for table "seg_delivery_details".
 *
 * The followings are the available columns in table 'seg_delivery_details':
 * @property string $refno
 * @property string $po_no
 * @property string $item_code
 * @property double $unit_price
 * @property double $item_qty
 * @property string $unit_id
 * @property integer $is_unitperpc
 * @property string $expiry_date
 * @property string $serial_no
 * @property double $prev_qty
 * @property double $prev_avg_cost
 * @property string $id
 *
 * The followings are the available model relations:
 * @property SegDelivery $refno0
 * @property SegPoH $poNo
 * @property CarePharmaProductsMain $itemCode
 * @property SegUnit $unit
 */
class DeliveryDetails extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'seg_delivery_details';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('refno, item_code, unit_price, item_qty, unit_id, is_unitperpc, serial_no, prev_qty, prev_avg_cost, id', 'required'),
			array('is_unitperpc', 'numerical', 'integerOnly'=>true),
			array('unit_price, item_qty, prev_qty, prev_avg_cost', 'numerical'),
			array('refno, po_no', 'length', 'max'=>12),
			array('item_code, serial_no', 'length', 'max'=>25),
			array('unit_id', 'length', 'max'=>10),
			array('id', 'length', 'max'=>32),
			array('expiry_date', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('refno, po_no, item_code, unit_price, item_qty, unit_id, is_unitperpc, expiry_date, serial_no, prev_qty, prev_avg_cost, id', 'safe', 'on'=>'search'),
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
			'refno0' => array(self::BELONGS_TO, 'SegDelivery', 'refno'),
			'poNo' => array(self::BELONGS_TO, 'SegPoH', 'po_no'),
			'itemCode' => array(self::BELONGS_TO, 'CarePharmaProductsMain', 'item_code'),
			'unit' => array(self::BELONGS_TO, 'SegUnit', 'unit_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'refno' => 'Refno',
			'po_no' => 'Po No',
			'item_code' => 'Item Code',
			'unit_price' => 'Unit Price',
			'item_qty' => 'Item Qty',
			'unit_id' => 'Unit',
			'is_unitperpc' => 'Is Unitperpc',
			'expiry_date' => 'Expiry Date',
			'serial_no' => 'Serial No',
			'prev_qty' => 'Prev Qty',
			'prev_avg_cost' => 'Prev Avg Cost',
			'id' => 'ID',
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
		$criteria->compare('po_no',$this->po_no,true);
		$criteria->compare('item_code',$this->item_code,true);
		$criteria->compare('unit_price',$this->unit_price);
		$criteria->compare('item_qty',$this->item_qty);
		$criteria->compare('unit_id',$this->unit_id,true);
		$criteria->compare('is_unitperpc',$this->is_unitperpc);
		$criteria->compare('expiry_date',$this->expiry_date,true);
		$criteria->compare('serial_no',$this->serial_no,true);
		$criteria->compare('prev_qty',$this->prev_qty);
		$criteria->compare('prev_avg_cost',$this->prev_avg_cost);
		$criteria->compare('id',$this->id,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return DeliveryDetails the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
