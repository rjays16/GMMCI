<?php

/**
 * This is the model class for table "seg_unit".
 *
 * The followings are the available columns in table 'seg_unit':
 * @property string $unit_id
 * @property string $unit_code
 * @property string $unit_name
 * @property integer $is_unit_per_pc
 * @property string $unit_desc
 * @property integer $is_deleted
 * @property integer $is_default
 * @property string $modify_id
 * @property string $modify_time
 * @property string $create_id
 * @property string $create_time
 *
 * The followings are the available model relations:
 * @property SegDeliveryDetails[] $segDeliveryDetails
 * @property SegInternalRequestDetails[] $segInternalRequestDetails
 * @property SegInventoryAdjustmentDetails[] $segInventoryAdjustmentDetails
 * @property SegIssuanceDetails[] $segIssuanceDetails
 * @property SegItemExtended[] $segItemExtendeds
 * @property SegItemExtended[] $segItemExtendeds1
 * @property SegPoD[] $segPoDs
 * @property SegSkuCatalog[] $segSkuCatalogs
 */
class UnitCatalog extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'seg_unit';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('unit_code, unit_name, unit_desc, is_deleted', 'required'),
			array('is_unit_per_pc, is_deleted, is_default', 'numerical', 'integerOnly'=>true),
			array('unit_code', 'length', 'max'=>4),
			array('unit_name', 'length', 'max'=>25),
			array('unit_desc', 'length', 'max'=>80),
			array('modify_id, create_id', 'length', 'max'=>35),
			array('modify_time, create_time', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('unit_id, unit_code, unit_name, is_unit_per_pc, unit_desc, is_deleted, is_default, modify_id, modify_time, create_id, create_time', 'safe', 'on'=>'search'),
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
			'segDeliveryDetails' => array(self::HAS_MANY, 'SegDeliveryDetails', 'unit_id'),
			'segInternalRequestDetails' => array(self::HAS_MANY, 'SegInternalRequestDetails', 'unit_id'),
			'segInventoryAdjustmentDetails' => array(self::HAS_MANY, 'SegInventoryAdjustmentDetails', 'unit_id'),
			'segIssuanceDetails' => array(self::HAS_MANY, 'SegIssuanceDetails', 'unit_id'),
			'segItemExtendeds' => array(self::HAS_MANY, 'SegItemExtended', 'pack_unit_id'),
			'segItemExtendeds1' => array(self::HAS_MANY, 'SegItemExtended', 'pc_unit_id'),
			'segPoDs' => array(self::HAS_MANY, 'SegPoD', 'unit_id'),
			'segSkuCatalogs' => array(self::HAS_MANY, 'SegSkuCatalog', 'unit_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'unit_id' => 'Unit',
			'unit_code' => 'Unit Code',
			'unit_name' => 'Unit Name',
			'is_unit_per_pc' => 'Is Unit Per Pc',
			'unit_desc' => 'Unit Desc',
			'is_deleted' => 'Is Deleted',
			'is_default' => 'Is Default',
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

		$criteria->compare('unit_id',$this->unit_id,true);
		$criteria->compare('unit_code',$this->unit_code,true);
		$criteria->compare('unit_name',$this->unit_name,true);
		$criteria->compare('is_unit_per_pc',$this->is_unit_per_pc);
		$criteria->compare('unit_desc',$this->unit_desc,true);
		$criteria->compare('is_deleted',$this->is_deleted);
		$criteria->compare('is_default',$this->is_default);
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
	 * @return Unit the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

    /**
     * @param  integer $unitid
     *
     * @return boolean TRUE if given unit id is that of a big unit, FALSE if unit pertains to the smallest measure.
     */
    public function isUnitIdBigUnit($unitid)
    {
        $data = self::model()->findByPk($unitid);
        return $data->is_unit_per_pc == 0;
    }
}
