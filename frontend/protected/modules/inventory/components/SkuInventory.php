<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * SKUInventory is one of the core classes in implementing the inventory module.
 *
 * @api
 *
 * @author Bong
 */
class SkuInventory {
    private $encounter_no = '';
    private $refno = '';
    private $trcode = '';
    private $error_msg = '';

    /***
     *
     */
    public function __construct() {

    }

    /***
     *
     */
    public function setEncounterNo($enc_no)
    {
        $this->encounter_no = $enc_no;
    }

    /***
     *
     */
    public function setRefNo($refno)
    {
        $this->refno = $refno;
    }

    /***
     *
     */
    public function setTransactionCode($trcode)
    {
        $this->trcode = $trcode;
    }

    /***
     *
     */
    public function getErrorMsg() {
        return $this->error_msg;
    }

    /***
     * Public function which returns the current inventory count of the item identified by 'skuid'
     *
     * @params string $skuid
     * @params date   $refdate
     *
     * @return double inventory count
     */
    public function getSkuQty($skuid, $refdate = '')
    {
        $refdate = (empty($refdate)) ? $refdate = date("Y-m-d") : $refdate;
        $criteria = new CDbCriteria();
        $criteria->with = array('inventoryLedgers', 'skuQty');
        $criteria->condition = 't.sku_id = :param1 AND tr_date <= DATE(:param2)';
        $criteria->params = array(
            ':param1' => $skuid, ':param2' => $refdate
        );
        $skurow = SkuCatalog::model()->find($criteria);
        return $skurow->skuQty;
    }

    /***
     * Public function which returns the average cost of the item identified by 'skuid'
     *
     * @params string $skuid
     * @params date   $refdate
     *
     * @return double Average Cost
     */
    public function getSkuAvgCost($skuid, $refdate = '')
    {
        $refdate = (empty($refdate)) ? $refdate = date("Y-m-d") : $refdate;
        $ledger = SkuCatalog::model()->with('inventoryLedgers')->find(array(
            'condition' => 't.sku_id=:id AND tr_date <= DATE(:refdate)',
            'params' => array(':id' => $skuid,
                ':refdate' => $refdate),
            'order' => 'tr_date DESC, post_date DESC, post_uid DESC',
        ));
        if($ledger){
            $row = $ledger->inventoryLedgers[0];

            if(( $row->movement + $row->prev_qty ) === 0){
                $avgcost = 0;
            } else {
                $avgcost = ( ($row->movement * $row->unit_cost) + ($row->prev_qty * $row->prev_cost) ) /
                    ( $row->movement + $row->prev_qty );
            }

            return round($avgcost, 2);
        }else
            return 0;

    }

    /***
     * Public function which returns the total cost added or deducted from the inventory given the following:
     *
     * @param  string  $skuids     - string of sku ids.
     * @param  date    $frmdate    - start date of period.
     * @param  date    $todate     - end date of period.
     * @param  boolean $b_outgoing - incoming by default, else outgoing.
     *
     * @return double  total cost.
     */
    public function getCostInPeriod($skuids, $frmdate, $todate, $b_outgoing = false)
    {
        $data = InventoryLedger::model()->totalcost()->inperiod($skuids, $frmdate, $todate, $b_outgoing)->findAll();
        $tcost = doubleval($data['0']['tcost']);
        return round($tcost, 2);
    }

    /***
     *
     */
    public function getUuid() {
        $command = Yii::app()->db->createCommand('Select UUID()');
        return $command->queryScalar();
    }

    /***
     * Private function which saves the sku id in sku catalog.
     *
     * @param string $skuid
     * @param string $item_code
     * @param int    $unit_id
     * @param int    $area_id
     * @param date   $expiry_dt
     * @param string $lot_no
     * @param string $batch_no
     * @param string $barcode
     *
     * @return boolean TRUE if successful, FALSE otherwise
     */
    public function saveNewSKUId($skuid, $item_code, $unit_id = 0, $area_id = '', $expiry_dt = '0000-00-00', $lot_no = '', $batch_no = '', $barcode = '', $unit_cost = 0)
    {
        $conn = SkuCatalog::model()->getDbConnection();
        $trans = $conn->getCurrentTransaction();
        $istrlocal = false;
        if (!$trans) {
            $trans = $conn->beginTransaction();
            $istrlocal = true;
        }
        try {
            $skuctlg = new SkuCatalog();
            $skuctlg->attributes = array(
                'sku_id' => $skuid,
                'item_code' => $item_code,
                //'barcode' => empty($barcode) ? "NULL" : $barcode,
                'unit_id' => (int)$unit_id,
                //do not parse area id
                'area_id' => $area_id,
                'lot_no' => $lot_no,
                'batch_no' => $batch_no,
                'expiry_date' => $expiry_dt,
                'unit_cost' => $unit_cost,
            );
            $bsuccess = $skuctlg->save();

            if ($istrlocal) {
                if ($bsuccess) {
                    $trans->commit();
                }
                else {
                    $trans->rollback();
                }
            }
            return $bsuccess;
        } catch (Exception $ex) {
            echo $ex->getMessage();
            if ($istrlocal) {
                $trans->rollback();
            }
            return false;
        }
    }

    /***
     * Routine which posts inventory movement in inventory ledger.
     *
     * @param  string  $itemcode
     * @param  int     $areaid
     * @param  double  $qty
     * @param  mixed   $unitid
     * @param  string  $barcode
     * @param  date    $trns_date
     * @param  double  $unit_cost
     *
     * @return boolean TRUE if posting succeeds, FALSE otherwise.
     */
    function addInventory($itemcode, $areaid, $qty, $unitid, $barcode=NULL, $trns_dte=NULL, $expiry_dt='0000-00-00', $lot_no=NULL, $batch_no=NULL, $unit_cost=0)
    {
        //is big unit is kanang mga boxes
        $isbigunit = UnitCatalog::model()->isUnitIdBigUnit($unitid);
        //item packaging use seg_item_extended
        $n = ItemPackaging::model()->findByPk($itemcode)->pack_qty;
        $packqty = (!empty($n)) ? ( ($isbigunit) ? doubleval($n) : doubleval(1) ) : doubleval(1);

        //get sku ids
        $skuid = SkuCatalog::model()->getSkuId($itemcode, $unitid, $areaid, $expiry_dt, $lot_no, $batch_no, $barcode);
        $bsuccess = false;

        if ($skuid) {
            $ledger = new InventoryLedger();
            $bsuccess = $ledger->postSkuItemMovement($this->encounter_no, $this->refno, $this->trcode, $trns_dte, $skuid, $qty, $packqty, $unit_cost);
        }
        return $bsuccess;
    }

    /***
     *
     */
    private function postBigUnitMovement($skuid, $curqty, $unit_id, $packqty, $trns_dte, &$runqty)
    {
        $inventoryLedger = new InventoryLedger();
        if (UnitCatalog::model()->isUnitIdBigUnit($unit_id)) {
            $pckqty = 1;
            $actpackqty = $packqty;
        }
        else {
            $pckqty = $packqty;
            $actpackqty = 1;
            $curqty = intval($curqty/$packqty);
        }

        if ($runqty > $curqty) {
            $runqty -= $curqty;
        }
        else {
            $curqty = $runqty;
            $runqty = 0;
        }
        return $inventoryLedger->postSkuItemMovement($this->encounter_no, $this->refno, $this->trcode, $trns_dte, $skuid, -1 * ($curqty * $pckqty), $actpackqty, 0);
    }

    /***
     *
     */
    private function postSmallUnitMovement($itemcode, $skuid, $area_id, $curqty, $unit_id, $packqty, $trns_dte, $sku, &$runqty)
    {
        $ledger = new InventoryLedger();
        if (UnitCatalog::model()->isUnitIdBigUnit($unit_id)) {
            $actpackqty = $packqty;
            $invqty = $curqty;
            $curqty = $invqty * $packqty;

            if ($runqty > $curqty) {
                $runqty -= $curqty;
                $bsuccess = $ledger->postSKUItemMovement($this->encounter_no, $this->refno, $this->trcode, $trns_dte, $skuid, -1 * $invqty, $actpackqty, 0);
            }
            else {
                $mvmntqty = intval($runqty/$packqty) + ((($runqty % $packqty) != 0) ? 1 : 0);
                $avgcost = round(doubleval($this->getSkuAvgCost($skuid, $trns_dte)/$actpackqty), 2);

                $bsuccess = $ledger->postSKUItemMovement($this->encounter_no, $this->refno, Yii::app()->params['UNPACK'], $trns_dte, $skuid, -1 * $mvmntqty, $actpackqty, 0);
                if ($bsuccess) {
                    $cunit_id = ItemPackaging::model()->findByPk($itemcode)->piece_unit_id;
                    $expirydt = $sku->barcode0->expiry_date;
                    $cskuid = SkuCatalog::model()->getSkuId($itemcode, $cunit_id, $area_id, $expirydt, $sku->barcode0->lot_no, $sku->barcode0->batch_no, $sku->barcode0->barcode);
                    if ($cskuid) {
                        $bsuccess = $ledger->postSKUItemMovement($this->encounter_no, $this->refno, Yii::app()->params['UNPACK'], $trns_dte, $cskuid, $mvmntqty * $packqty, 1, $avgcost);
                        if ($bsuccess) {
                            $curqty = $runqty;
                            $runqty = 0;
                            $bsuccess = $ledger->postSKUItemMovement($this->encounter_no, $this->refno, $this->trcode, $trns_dte, $cskuid, -1 * $curqty, 1, $avgcost);
                        }
                    }
                    else {
                        $bsuccess = false;
                    }
                }
            }
        }
        else {
            if ($runqty > $curqty) {
                $runqty -= $curqty;
            }
            else {
                $curqty = $runqty;
                $runqty = 0;
            }

            $bsuccess = $ledger->postSKUItemMovement($this->encounter_no, $this->refno, $this->trcode, $trns_dte, $skuid, -1 * $curqty, 1, 0);
        }
        return $bsuccess;
    }

    /***
     * Routine which posts a negative inventory movement in inventory ledger.
     *
     * @param  string  $itemcode
     * @param  int     $areaid
     * @param  double  $qty
     * @param  mixed   $unitid
     * @param  string  $barcode
     * @param  date    $trns_date
     * @param  double  $unit_cost
     *
     * @return boolean TRUE if posting succeeds, FALSE otherwise.
     */
    public function remInventory($itemcode, $areaid, $qty, $unitid, $barcode=NULL, $trns_dte=NULL)
    {
        $isbigunit = UnitCatalog::model()->isUnitIdBigUnit($unitid);
        $itemname = ItemCatalog::model()->findByPk($itemcode)->item_name;
        $pack_qty = ItemPackaging::model()->findByPk($itemcode)->pack_qty;
        $packqty = (!empty($pack_qty)) ? ( ($isbigunit) ? doubleval($pack_qty) : doubleval(1) ) : 1;
        $trns_dte = (empty($trns_dte)) ? $trns_dte = date("Y-m-d") : $trns_dte;

        $runqty = $qty;
        $bsuccess = false;
        if (BarcodeCatalog::model()->isExists($barcode)) {
            $data = BarcodeCatalog::model()->find('barcode = :barcode', array(':barcode' => $barcode));
            $expiry_dt = $data->expiry_date;
            $lot_no = $data->lot_no;
            $batch_no = $data->batch_no;
        }
        else {
            $expiry_dt = '0000-00-00';
            $lot_no = '';
            $batch_no = '';
        }
//        $rsskuids = SkuCatalog::model()->getSkuIdswInventory($itemcode, $trns_dte, $unitid, $areaid, $expiry_dt, $lot_no, $batch_no); 
        $rsskuids = SkuCatalog::model()->getSkuIdswInventory($itemcode, $trns_dte, 0, $areaid);
        if ($rsskuids && !empty($rsskuids)) {
            foreach($rsskuids as $row) {
                $skuid = $row->sku_id;
                if ($runqty > 0) {
                    $curqty = $this->getSkuQty($skuid, $trns_dte);
                    if ($isbigunit) {
                        $bsuccess = $this->postBigUnitMovement($skuid, $curqty, $row->unit_id, $packqty, $trns_dte, $runqty);
                    }
                    else {
                        $bsuccess = $this->postSmallUnitMovement($itemcode, $skuid, $areaid, $curqty, $row->unit_id, $packqty, $trns_dte, $row, $runqty);
                    }
                }
            }

            if ($runqty > 0) {
                $curqty = $runqty;
                if ($isbigunit) {
                    $bsuccess = $this->postBigUnitMovement($skuid, $curqty, $row->unit_id, $packqty, $trns_dte, $runqty);
                }
                else {
                    $bsuccess = $this->postSmallUnitMovement($itemcode, $skuid, $areaid, $curqty, $row->unit_id, $packqty, $trns_dte, $row, $runqty);
                }
            }

            if ($bsuccess) {
                $alert_qty = $this->getItemQty($itemcode, 0, $trns_dte, $areaid);
                $min_qty = ItemPackaging::model()->findByPk($itemcode)->reorder_qty;
                if ($alert_qty <= $min_qty) {
                    $this->error_msg = "Must reorder item ".$itemname." now!";
                }
            }
        }
        else {
            $areadesc = AreaCatalog::model()->findByPk((int)$areaid)->area_desc;
            $this->error_msg = "No inventory of item ".$itemname." in ".$areadesc;
            $bsuccess = false;
        }

        return $bsuccess;
    }

    /***
     * Public function which returns the inventory quantity filtered by the following:
     *
     * @params string $itemcode
     * @params date   $refdate
     * @params int    $area_id
     * @params date   $expirydate
     * @params string $barcode
     * @params int    $unit_id
     *
     * @return double inventory quantity.
     */
    public function getItmQty($itemcode, $refdate = '', $area_id = '', $barcode = '', $unit_id = 0, $brecent = false)
    {
        $refdate = (empty($refdate)) ? $refdate = date("Y-m-d") : $refdate;
        $bcode = BarcodeCatalog::model()->findByPk($barcode);
        if (!empty($bcode)) {
            $expiry_dt = $bcode->expiry_date;
            $lot_no = $bcode->lot_no;
            $batch_no = $bcode->batch_no;
        }
        else {
            $expiry_dt = '0000-00-00';
            $lot_no = '';
            $batch_no = '';
        }

        return $this->getItemQty($itemcode, $unit_id, $refdate, $area_id, $expiry_dt, $lot_no, $batch_no, $brecent);
    }

    /***
     * Public function which returns the quantity of the item identified by 'itmcode'
     *
     * @params string $itmcode
     * @params date   $refdate
     * @params int    $area_id
     * @params date   $expirydate
     * @params string $serialno
     * @params int    $unitid
     *
     * @return double item quantity
     */
    public function getItemQty($itmcode, $unitid = 0, $refdate = '', $area_id = 0, $expirydate = '', $lot_no = '', $batch_no = '', $brecent = false)
    {
        $totalqty = doubleval(0);
        $refdate = (empty($refdate)) ? $refdate = date("Y-m-d") : $refdate;
        $skuids = SkuCatalog::model()->getSKUIds($itmcode, $unitid, $area_id, $expirydate, $lot_no, $batch_no);
        $dtefilter = ($brecent) ? "tr_date < DATE('{$refdate}')" : "tr_date <= DATE('{$refdate}')";
        if(is_null($skuids))
            $skuids = "''";
        $strSQL = "SELECT sel.* 
                      FROM ".  InventoryLedger::model()->tableName()." sel 
                        INNER JOIN 
                          (SELECT sku_id,
                            (SELECT 
                              post_uid 
                            FROM
                              ".InventoryLedger::model()->tableName()." sel2 
                            WHERE sel2.sku_id = sel1.sku_id 
                               AND {$dtefilter}  
                            ORDER BY tr_date DESC, post_date DESC 
                            LIMIT 1) postuid 
                          FROM
                            ".InventoryLedger::model()->tableName()." sel1 
                          WHERE sku_id IN ({$skuids}) 
                          GROUP BY sku_id) t 
                          ON t.postuid = sel.`post_uid`";
        $command = Yii::app()->db->createCommand($strSQL);
        $rows = $command->queryAll();

        foreach($rows as $row) {
            $packqty = $row['pack_qty'];
            $totalqty  += (($row['movement'] * $packqty) + ($row['prev_qty'] * $packqty));
        }

        return $totalqty;
    }

    /***
     * Public function which returns the average cost of the item identified by 'itmcode'
     *
     * @params string $itmcode
     * @params date   $refdate
     * @params int    $area_id
     * @params date   $expirydate
     * @params string $serialno
     * @params int    $unitid
     *
     * @return double Average Cost
     */
    public function getItemAvgCost($itmcode, $unitid = 0, $refdate = '', $area_id = 0, $barcode = '',$expirydate = '', $lot_no = '', $batch_no = '', $brecent = false)
    {
        if(!empty($barcode)){
            $data = BarcodeCatalog::model()->findByPk($barcode);
            $expirydate = $data->expiry_date;
            $lot_no = $data->lot_no;
            $batch_no = $data->batch_no;
        }

        $totalcost = doubleval(0);
        $totalqty = doubleval(0);

        $refdate = (empty($refdate)) ? $refdate = date("Y-m-d") : $refdate;
        $skuids = SkuCatalog::model()->getSKUIds($itmcode, $unitid, $area_id, $expirydate, $lot_no, $batch_no);
        $dtefilter = ($brecent) ? "tr_date < DATE('{$refdate}')" : "tr_date <= DATE('{$refdate}')";

        $strSQL = "SELECT sel.* 
                      FROM ".InventoryLedger::model()->tableName()." sel 
                        INNER JOIN 
                          (SELECT sku_id,
                            (SELECT 
                              post_uid 
                            FROM
                              ".InventoryLedger::model()->tableName()." sel2 
                            WHERE sel2.sku_id = sel1.sku_id 
                               AND {$dtefilter} 
                            ORDER BY tr_date DESC, post_date DESC 
                            LIMIT 1) postuid 
                          FROM
                            ".InventoryLedger::model()->tableName()." sel1 
                          WHERE sku_id IN ({$skuids}) 
                          GROUP BY sku_id) t 
                          ON t.postuid = sel.`post_uid`";
        $command = Yii::app()->db->createCommand($strSQL);
        $rows = $command->queryAll();

        foreach($rows as $row) {
            $packqty = $row['pack_qty'];
            $totalcost += (($row['movement'] * $row['unit_cost']) + ($row['prev_qty'] * $row['prev_cost']));
            $totalqty  += (($row['movement'] * $packqty) + ($row['prev_qty'] * $packqty));
        }
        $avgcost = ($totalqty == 0) ? doubleval($totalcost) : doubleval($totalcost / $totalqty);

        // Added to return the average cost of item based on unit id (if in pack or pc.) ...
        if (!empty($unitid)) {
            $isbigunit = UnitCatalog::model()->isUnitIdBigUnit($unitid);
            $n = ItemPackaging::model()->findByPk($itmcode)->pack_qty;
            $packqty = ($isbigunit) ? ( !empty($n) ? doubleval($n) : doubleval(1) ) : doubleval(1);
            $avgcost *= $packqty;
        }

        return round($avgcost, 2);
    }

    public function multiReduceQtyFromSkuInventory(array $removeItems, $area_id)
    {
        $this->setTransactionCode(Yii::app()->params['SALE']);
        $is_ok = true;

        foreach ($removeItems as $item) {
            $is_ok = $this->remInventory(
                $item->item_code, $area_id, $item->quantity, $item->unit_id, $item->barcode
            );

            if (!$is_ok) {
                throw new Exception('It seems the item cannot be saved in the SkuInventory :(');
            }
        }

        InventoryLedger::model()->clearTmpTable();
    }

}
