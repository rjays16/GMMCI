<?php

/**
 * This is the model class for table "seg_sku_catalog".
 *
 * The followings are the available columns in table 'seg_sku_catalog':
 * @property string $sku_id
 * @property string $item_code
 * @property string $barcode
 * @property string $unit_id
 * @property string $area_id
 * @property string $modify_dt
 * @property string $modify_id
 * @property string $create_dt
 * @property string $create_id
 * @property string $expiry_date
 * @property string $lot_no
 * @property string $batch_no
 * @property string $unit_cost
 *
 * The followings are the available model relations:
 * @property SegInventoryLedger[] $segInventoryLedgers
 * @property CarePharmaProductsMain $itemCode
 * @property SegAreas $area
 * @property SegUnit $unit
 */
class SkuCatalog extends SegActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'seg_sku_catalog';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('sku_id', 'required'),
			array('sku_id', 'length', 'max'=>36),
			array('item_code, unit_cost', 'length', 'max'=>12),
			array('barcode', 'length', 'max'=>30),
			array('unit_id, area_id', 'length', 'max'=>10),
			array('modify_id, create_id', 'length', 'max'=>35),
			array('lot_no, batch_no', 'length', 'max'=>80),
			array('modify_dt, create_dt, expiry_date', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('sku_id, item_code, barcode, unit_id, area_id, modify_dt, modify_id, create_dt, create_id, expiry_date, lot_no, batch_no, unit_cost', 'safe', 'on'=>'search'),
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
			'inventoryLedgers' => array(self::HAS_MANY, 'InventoryLedger', 'sku_id'),
			'itemCode' => array(self::BELONGS_TO, 'ItemCatalog', 'item_code'),
			'area' => array(self::BELONGS_TO, 'Area', 'area_id'),
			'unit' => array(self::BELONGS_TO, 'Unit', 'unit_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'sku_id' => 'Sku',
			'item_code' => 'Item Code',
			'barcode' => 'Barcode',
			'unit_id' => 'Unit',
			'area_id' => 'Area',
			'modify_dt' => 'Modify Dt',
			'modify_id' => 'Modify',
			'create_dt' => 'Create Dt',
			'create_id' => 'Create',
			'expiry_date' => 'Expiry Date',
			'lot_no' => 'Lot No',
			'batch_no' => 'Batch No',
			'unit_cost' => 'Unit Cost',
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

		$criteria->compare('sku_id',$this->sku_id,true);
		$criteria->compare('item_code',$this->item_code,true);
		$criteria->compare('barcode',$this->barcode,true);
		$criteria->compare('unit_id',$this->unit_id,true);
		$criteria->compare('area_id',$this->area_id,true);
		$criteria->compare('modify_dt',$this->modify_dt,true);
		$criteria->compare('modify_id',$this->modify_id,true);
		$criteria->compare('create_dt',$this->create_dt,true);
		$criteria->compare('create_id',$this->create_id,true);
		$criteria->compare('expiry_date',$this->expiry_date,true);
		$criteria->compare('lot_no',$this->lot_no,true);
		$criteria->compare('batch_no',$this->batch_no,true);
		$criteria->compare('unit_cost',$this->unit_cost,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return SkuCatalog the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

    /***
     * Public function which returns the associated information given the sku id.
     *
     * @params string   $skuid
     *
     * @return row of resultset, FALSE otherwise.
     */
    public function getSkuInfo($skuid) {
        return $this->find(array(
            'condition' => 'sku_id=:id',
            'params' => array(':id'=>$this->getDbConnection()->quoteValue($skuid))
        ));
    }

    /**
     * Retrieves associated SKU id of item given other parameters. (Will be updated later to include checking against inventory.)
     *
     * @param string $item_code - the code of item in item_catalog table.
     * @param int    $unit_id   - the packing unit id.
     * @param int    $area_id   - code of location (area) of item
     * @param date   $expiry_dt - expiration date
     * @param string $lot_no    - lot no. of item, if there is any
     * @param string $batch_no  - batch no. of item, if there is any
     * @param string $barcode   - barcode of item, if there is any
     * @return string - associated sku id.
     */
    public function getSkuId($item_code, $unit_id = 0, $area_id = 0, $expiry_dt = '0000-00-00', $lot_no = '', $batch_no = '', $barcode = '', $unit_cost = 0)
    {
        $strSQL = "SELECT
                            sku.`sku_id`
                        FROM
                            seg_sku_catalog sku
                        WHERE sku.`item_code` = '$item_code'
                            AND (
                                CASE
                                    WHEN '$batch_no' != ''
                                    THEN sku.`batch_no` = '$batch_no'
                                    ELSE 1
                                END
                            )
                            AND (
                                CASE
                                    WHEN '$expiry_dt' != '0000-00-00'
                                    THEN sku.`expiry_date` = DATE('$expiry_dt')
                                    ELSE 1
                                END
                            )
                            AND (
                                CASE
                                    WHEN '$lot_no' != ''
                                    THEN sku.`lot_no` = '$lot_no'
                                    ELSE 1
                                END
                            )
                            AND (
                                CASE
                                    WHEN ".(int)$area_id." != 0
                                    THEN sku.`area_id` = ".(int)$area_id."
                                    ELSE 1
                                END
                            )
                            AND (
                                CASE
                                    WHEN ".(int)$unit_id." != 0
                                    THEN sku.`unit_id` = ".(int)$unit_id."
                                    ELSE 1
                                END
                            )
                        ORDER BY (
                                CASE
                                    WHEN sku.`expiry_date` IS NOT NULL
                                    THEN sku.`expiry_date`
                                    ELSE sku.`item_code`
                                END
                            )
                        LIMIT 1";
        $command = Yii::app()->db->createCommand($strSQL);
        $skuid = $command->queryScalar();
        $bsuccess = true;
        if (empty($skuid)) {
            // Save the new sku id in catalog ...
            $sku = new SkuInventory();
            $skuid = $sku->getUuid();
            $bsuccess = $sku->saveNewSKUId($skuid, $item_code, $unit_id, $area_id, $expiry_dt, $lot_no, $batch_no, $barcode, $unit_cost);
        }
        return $bsuccess ? $skuid : false;
    }
}
