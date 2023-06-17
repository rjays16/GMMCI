<?php
use SegHis\modules\inventory\models\IInventoryTransaction;
use SegHis\modules\inventory\models\InventoryLedger;
use SegHis\modules\inventory\models\StockKeepingUnit;

/**
 * This is the model class for table "seg_inventory_adjustment_details".
 *
 * The followings are the available columns in table 'seg_inventory_adjustment_details':
 * @property int $id
 * @property string $refno
 * @property string $sku_id
 * @property string $item_code
 * @property string $unit_id
 * @property integer $is_unitperpc
 * @property string $expiry_date
 * @property string $serial_no
 * @property double $orig_qty
 * @property double $adj_qty
 * @property string $reason
 * @property string $lot_no
 * @property double $unit_cost
 *
 * @property string $itemName
 *
 * The followings are the available model relations:
 * @property Adjustment $adjustment
 * @property ItemCatalog $product
 * @property \SegHis\models\inventory\Unit $unit
 * @property StockKeepingUnit $stockKeepingUnit
 * @property \SegHis\models\inventory\ItemExtended $itemMoreInfo
 */
class AdjustmentDetails extends CareActiveRecord implements IInventoryTransaction
{

    const TRANSACTION_CODE_ADJUSTMENT = 'ADJ';

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'seg_inventory_adjustment_details';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('refno, item_code, unit_id, is_unitperpc, orig_qty, adj_qty, reason', 'required'),
            array('is_unitperpc', 'numerical', 'integerOnly' => true),
            array('orig_qty, adj_qty', 'numerical'),
            array('refno', 'length', 'max' => 12),
            array('item_code, serial_no', 'length', 'max' => 25),
            array('unit_id, reason', 'length', 'max' => 10),
            array('lot_no', 'length', 'max' => 80),
            // The following rule is used by search().
            array('itemName, refno, item_code, unit_id, is_unitperpc, expiry_date, serial_no, orig_qty, adj_qty, reason, lot_no', 'safe', 'on' => 'search'),
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
            'adjustment' => array(self::BELONGS_TO, 'Adjustment', 'refno'),
            'product' => array(self::BELONGS_TO, 'ItemCatalog', 'item_code'),
            'unit' => array(self::BELONGS_TO, '\SegHis\models\inventory\Unit', 'unit_id'),
            'reasonInfo' => array(self::HAS_ONE, 'AdjustmentReason', 'reason'),
            'stockKeepingUnit' => array(self::HAS_ONE, 'SegHis\modules\inventory\models\StockKeepingUnit', array('sku_id' => 'sku_id')),
            'itemMoreInfo' => array(self::HAS_ONE, 'SegHis\models\inventory\ItemExtended', array('item_code' => 'item_code'))
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            // 'id' => 'ID',
            'refno' => 'Reference #',
            'item_code' => 'Item Code',
            'sku_id' => 'Stock ID',
            'unit_id' => 'Unit',
            'is_unitperpc' => 'Is Unit Per Piece',
            'expiry_date' => 'Expiry Date',
            'serial_no' => 'Serial No',
            'orig_qty' => 'Original Qty',
            'adj_qty' => 'Adjusted Qty',
            'reason' => 'Reason',
            'lot_no' => 'Lot No',
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
        $criteria = new CDbCriteria;

        // $criteria->compare('id', $this->id, true);
        $criteria->compare('refno', $this->refno, true);
        $criteria->compare('item_code', $this->item_code, true);
        $criteria->compare('unit_id', $this->unit_id, true);
        $criteria->compare('is_unitperpc', $this->is_unitperpc);
        $criteria->compare('expiry_date', $this->expiry_date, true);
        $criteria->compare('serial_no', $this->serial_no, true);
        $criteria->compare('orig_qty', $this->orig_qty);
        $criteria->compare('adj_qty', $this->adj_qty);
        $criteria->compare('reason', $this->reason, true);
        $criteria->compare('lot_no', $this->lot_no, true);
        $criteria->compare('product.artikelname', $this->itemName, true);

        $criteria->with = array(
            'product' => array(
                'select' => 'product.artikelname'
            ),
            'stockKeepingUnit' => array(
                'select' => array(
                    'sku_id',
                    // 'order_no'
                )
            ),
        );

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
            'pagination' => false
        ));
    }

    public function getItemName()
    {
        return $this->product->artikelname;
    }

    public function setItemName($value)
    {
        $this->itemName = $value;
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return AdjustmentDetails the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

//    protected function beforeSave()
//    {
//        if ($this->adjustment->is_posted) {
//            return $this->updateInventory();
//        }
//        return parent::beforeSave();
//    }

    /**
     * Called by beforeSave()
     * Updates SKU data
     * Updates inventory only if there are changes in the Stocks' Quantity
     *
     * @return bool
     */
    public function updateInventory()
    {
        if ($this->adjustment->is_posted) {
            throw new CException('Adjustment is already posted');
        }


        if (empty($this->sku_id)) {
            $stockKeepingUnit = new StockKeepingUnit();
            $stockKeepingUnit->sku_id = StockKeepingUnit::generateUuid();
            $stockKeepingUnit->item_code = $this->item_code;
            $stockKeepingUnit->area_code = $this->adjustment->area_code;
        } else {
            $stockKeepingUnit = StockKeepingUnit::model()->findByPk($this->sku_id);
        }

        if (!$stockKeepingUnit) {
//            $this->addError('', 'SKU record not found for item `' . $this->itemName . '`');
//            return false;

            throw new CException('SKU record not found for item `' . $this->itemName . '`');
        }

        //update stock keeping unit
        /* @var $stockKeepingUnit StockKeepingUnit */;

        $stockKeepingUnit->expiry_date = strtotime($this->expiry_date) ? date('Y-m-d', strtotime($this->expiry_date)) : '0000-00-00';
        $stockKeepingUnit->serial_no = $this->serial_no;
        $stockKeepingUnit->lot_no = $this->lot_no;
        // $stockKeepingUnit->unit_cost = $this->unit_cost;
        $stockKeepingUnit->unit_id = $this->unit_id;
        $ok = $stockKeepingUnit->save() && ($this->sku_id ? true : $this->saveAttributes(array('sku_id' => $stockKeepingUnit->sku_id)));
        if(!$ok) {
//            $this->addError('', 'Unable to update SKU record');
//            return false;

            throw new CException('Unable to update SKU record for item `' . $this->itemName  .'`');
        }

        // Post to inventory
        // if($this->adj_qty != $this->orig_qty) { #comment out condition by monmon
            $postOk = InventoryLedger::postToInventory($this);
            if (!$postOk) {
                throw new CException('Unable to post item ' . $this->itemName . ' to inventory.');
            }
        // }

        return true;
    }

    public function getTransactionType()
    {
        return InventoryLedger::TRANSACTION_TYPE_ADJUSTMENT;
    }

    public function getReferenceNo()
    {
        return $this->refno;
    }

    public function getTransactionDate()
    {
        return strtotime($this->adjustment->adjust_date) ? date('Y-m-d H:i:s', strtotime($this->adjustment->adjust_date)) : null;
    }

    public function getSkuId()
    {
        return $this->sku_id;
    }

    public function getUnitCost()
    {
        return 0;
    }

    public function getPreviousCost()
    {
        if ($this->stockKeepingUnit) {
            $ledgerEntries = $this->stockKeepingUnit->getLedgerEntriesByDate($this->adjustment->adjust_date);
            if($ledgerEntries) {

                $total = 0;

                /* @var $ledgerEntry InventoryLedger */
                foreach ($ledgerEntries as $ledgerEntry) {

                    if($ledgerEntry->mvmnt_qty+$ledgerEntry->prev_qty <= 0) {
                        continue;
                    }

                    $cost = (($ledgerEntry->mvmnt_qty * $ledgerEntry->packqty * ($ledgerEntry->unit_cost / $ledgerEntry->packqty)) + ($ledgerEntry->prev_qty * $ledgerEntry->prev_cost)) /
                        ($ledgerEntry->packqty * $ledgerEntry->mvmnt_qty + $ledgerEntry->prev_qty);

                    $avgCost = ($cost == 0 ? floatval($ledgerEntry->prev_cost) : round(floatval($cost), 2));
                    $total += $avgCost;

                }

                return round(floatval($total)/count($ledgerEntries),2);
            }
        }

        return 0;
    }

    public function getMovementQuantity()
    {
        return $this->adj_qty - $this->orig_qty;
    }

    public function getPreviousQuantity()
    {
        return $this->orig_qty;
    }

    public function getPackQuantity()
    {
        if($this->unit->is_unit_per_pc)
            return 1;

        return $this->itemMoreInfo->qty_per_pack;
    }

    public function getPostDate()
    {
        return $this->getTransactionDate();
    }
}