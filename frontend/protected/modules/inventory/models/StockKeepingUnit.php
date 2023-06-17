<?php
namespace SegHis\modules\inventory\models;

/**
 * This is the model class for table "seg_sku_catalog".
 *
 * The followings are the available columns in table 'seg_sku_catalog':
 * @property string $sku_id
 * @property string $item_code
 * @property string $area_code
 * @property string $expiry_date
 * @property string $serial_no
 * @property string $unit_id
 * @property string $lot_no
 * @property string $batch_no
 * @property string $unit_cost
 * @property integer $order_no
 * @property integer $has_qty
 * @property InventoryLedger[] $ledgerEntries
 * @property Unit $unit
 * @property PharmacyProduct $item
 */
class StockKeepingUnit extends \CareActiveRecord
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
            array('sku_id, item_code, area_code', 'required'),
            // array('has_qty', 'numerical', 'integerOnly' => true),
            array('sku_id', 'length', 'max' => 36),
            array('item_code, serial_no', 'length', 'max' => 25),
            array('area_code, unit_id', 'length', 'max' => 10),
            array('lot_no, batch_no', 'length', 'max' => 80),
            array('unit_cost', 'length', 'max' => 12),
            array('expiry_date', 'safe'),
            // The following rule is used by search().
            array('sku_id, item_code, area_code, expiry_date, serial_no, unit_id, lot_no, batch_no, unit_cost', 'safe', 'on' => 'search'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        return array(
            'ledgerEntries' => array(self::HAS_MANY, 'SegHis\modules\inventory\models\InventoryLedger', 'sku_id', 'order' => 'post_date DESC'),
            'unit' => array(self::HAS_ONE, 'SegHis\modules\inventory\models\Unit', array('unit_id' => 'unit_id')),
            'item' => array(self::HAS_ONE, 'SegHis\modules\inventory\models\PharmacyProduct', array('bestellnum' => 'item_code')),
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
            'area_code' => 'Area Code',
            'expiry_date' => 'Expiry Date',
            'serial_no' => 'Serial No',
            'unit_id' => 'Unit',
            'lot_no' => 'Lot No',
            'batch_no' => 'Batch No',
            'unit_cost' => 'Unit Cost',
            // 'order_no' => 'Order No',
            // 'has_qty' => 'Has Qty',
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
     * @return \CActiveDataProvider the data provider that can return the models
     * based on the search/filter conditions.
     */
    public function search()
    {
        $criteria = new \CDbCriteria;
        $criteria->compare('sku_id', $this->sku_id, true);
        $criteria->compare('item_code', $this->item_code, true);
        $criteria->compare('area_code', $this->area_code, true);
        $criteria->compare('expiry_date', $this->expiry_date, true);
        $criteria->compare('serial_no', $this->serial_no, true);
        $criteria->compare('unit_id', $this->unit_id, true);
        $criteria->compare('lot_no', $this->lot_no, true);
        $criteria->compare('batch_no', $this->batch_no, true);
        $criteria->compare('unit_cost', $this->unit_cost, true);
        // $criteria->compare('order_no', $this->order_no);
        // $criteria->compare('has_qty', $this->has_qty);

        return new \CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return StockKeepingUnit the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    /**
     * @return string
     */
    public static function generateUuid()
    {
        $command = self::model()->getDbConnection()->createCommand("SELECT UUID()");
        return $command->queryScalar();

    }

    /**
     * @param $q
     * @param $areaCode
     * @param $dateOffset
     * @return StockKeepingUnit[]
     */
    public static function findStockKeepingUnits($q, $areaCode, $dateOffset)
    {
        $criteria = new \CDbCriteria();
        $criteria->with = array('ledgerEntries', 'unit', 'item');

        if (is_numeric($q)) {
            $criteria->addColumnCondition(array('item_code' => $q));
        } else {
            $criteria->compare('item.artikelname', $q, true);
        }

        $criteria->addColumnCondition(array('area_code' => $areaCode));

        if (strtotime($dateOffset) != 0 && strtotime($dateOffset) != false) {
            $criteria->addCondition('ledgerEntries.tr_date <= :dateOffset');
            $criteria->params = \CMap::mergeArray($criteria->params, array(
                ':dateOffset' => date('Y-m-d', strtotime($dateOffset))
            ));
        }
        $criteria->order = 'artikelname';
        return self::model()->findAll($criteria);
    }

    /**
     * @return null|InventoryLedger
     */
    public function getLatestLedger()
    {
        $criteria = new \CDbCriteria();
        $criteria->addColumnCondition(array(
            'sku_id' => $this->sku_id
        ));
        $criteria->order = 'post_date DESC';
        return InventoryLedger::model()->find($criteria);
    }

    /**
     * @param $dateOffset
     * @return null|InventoryLedger
     */
    public function getLatestLedgerByDate($dateOffset)
    {
        $criteria = new \CDbCriteria();
        $criteria->addColumnCondition(array(
            'sku_id' => $this->sku_id
        ));
        $criteria->addCondition('post_date <= :dateOffset');
        $criteria->params = \CMap::mergeArray($criteria->params, array(
            ':dateOffset' => date('Y-m-d', strtotime($dateOffset))
        ));
        $criteria->order = 'post_date DESC';
        return InventoryLedger::model()->find($criteria);
    }

    /**
     * @param $dateOffset
     * @return null|InventoryLedger[]
     */
    public function getLedgerEntriesByDate($dateOffset)
    {
        $criteria = new \CDbCriteria();
        $criteria->addColumnCondition(array(
            'sku_id' => $this->sku_id
        ));
        $criteria->addCondition('post_date <= :dateOffset');
        $criteria->params = \CMap::mergeArray($criteria->params, array(
            ':dateOffset' => date('Y-m-d', strtotime($dateOffset))
        ));
        $criteria->order = 'post_date DESC';
        return InventoryLedger::model()->findAll($criteria);
    }

    /**
     * The current quantity
     * @return float|int
     */
    public function getCurrentQuantitydd()
    {
        $latestLedgerEntry = $this->getLatestLedger();
        if (!$latestLedgerEntry)
            return 0;
        return $latestLedgerEntry->mvmnt_qty + $latestLedgerEntry->prev_qty;
    }
    #modified by monmon:
    public function getCurrentQuantity(){
        global $db;
        $sql = "SELECT SUM(mvmnt_qty) FROM seg_inventory_ledger WHERE sku_id ='".$this->sku_id."'";
        $qty = $db->GetOne($sql);
        return $qty;
    }
    #end
    /**
     * The current quantity
     * @return float|int
     */
    public function getCurrentCost()
    {
        $latestLedgerEntry = $this->getLatestLedger();
        if (!$latestLedgerEntry)
            return null;
        return $latestLedgerEntry->unit_cost;
    }

    /**
     * @param $dateOffset
     * @return float|int
     */
    public function getCurrentQuantityByDate($dateOffset)
    {
        $latestLedgerEntry = $this->getLatestLedgerByDate($dateOffset);
        if (!$latestLedgerEntry)
            return 0;
        return $latestLedgerEntry->mvmnt_qty + $latestLedgerEntry->prev_qty;
    }

    public function getStockQuantityByDate($dateOffset)
    {
        $criteria = new \CDbCriteria();
        $criteria->addColumnCondition(array(
            'sku_id' => $this->sku_id
        ));
        $criteria->addCondition('STR_TO_DATE(post_date,"%Y-%m-%d") <= STR_TO_DATE(:dateOffset,"%Y-%m-%d")');
        $criteria->params = \CMap::mergeArray($criteria->params, array(
            ':dateOffset' => date('Y-m-d', strtotime($dateOffset))
        ));
        #$criteria->order = 'post_date DESC';
        $criteria->select = 'SUM(mvmnt_qty) AS mvmnt_qty';

        /* @var $model InventoryLedger */
        $model = InventoryLedger::model()->find($criteria);
        return $model->mvmnt_qty;
    }

}
