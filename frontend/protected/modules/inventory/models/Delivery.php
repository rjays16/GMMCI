<?php

/**
 * This is the model class for table "seg_delivery".
 *
 * The followings are the available columns in table 'seg_delivery':
 * @property string $refno
 * @property string $receipt_date
 * @property integer $receiving_id
 * @property string $area_code
 * @property string $supplier_id
 * @property string $remarks
 * @property integer $is_deleted
 * @property string $history
 * @property string $modify_id
 * @property string $modify_dt
 * @property string $create_id
 * @property string $create_dt
 *
 * The followings are the available model relations:
 * @property SegAreas $areaCode
 * @property SegSupplier $supplier
 * @property CarePersonell $receiving
 * @property SegDeliveryDetails[] $segDeliveryDetails
 */
class Delivery extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'seg_delivery';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('refno, receiving_id, area_code, supplier_id, remarks, is_deleted, history, modify_id, modify_dt', 'required'),
			array('receiving_id, is_deleted', 'numerical', 'integerOnly'=>true),
			array('refno', 'length', 'max'=>12),
			array('area_code', 'length', 'max'=>10),
			array('supplier_id', 'length', 'max'=>8),
			array('modify_id, create_id', 'length', 'max'=>32),
			array('receipt_date, create_dt', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('refno, receipt_date, receiving_id, area_code, supplier_id, remarks, is_deleted, history, modify_id, modify_dt, create_id, create_dt', 'safe', 'on'=>'search'),
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
			'areaCode' => array(self::BELONGS_TO, 'SegAreas', 'area_code'),
			'supplier' => array(self::BELONGS_TO, 'SegSupplier', 'supplier_id'),
			'receiving' => array(self::BELONGS_TO, 'CarePersonell', 'receiving_id'),
			'segDeliveryDetails' => array(self::HAS_MANY, 'SegDeliveryDetails', 'refno'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'refno' => 'Refno',
			'receipt_date' => 'Receipt Date',
			'receiving_id' => 'Receiving',
			'area_code' => 'Area Code',
			'supplier_id' => 'Supplier',
			'remarks' => 'Remarks',
			'is_deleted' => 'Is Deleted',
			'history' => 'History',
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
		$criteria->compare('receipt_date',$this->receipt_date,true);
		$criteria->compare('receiving_id',$this->receiving_id);
		$criteria->compare('area_code',$this->area_code,true);
		$criteria->compare('supplier_id',$this->supplier_id,true);
		$criteria->compare('remarks',$this->remarks,true);
		$criteria->compare('is_deleted',$this->is_deleted);
		$criteria->compare('history',$this->history,true);
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
	 * @return Delivery the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

    public static function getLastRefNo() {

    }
}
