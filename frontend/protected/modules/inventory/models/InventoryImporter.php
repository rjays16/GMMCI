<?php

/**
 * InventoryImporter.php
 *
 * @author Alvin Quinones <ajmquinones@gmail.com>
 * @copyright (c) 2016,
 */

namespace SegHis\modules\inventory\models;
use CJSON;
use PHPExcel_IOFactory;
use PHPExcel_Shared_Date;
use SegHis\modules\inventory\models\InventoryExporter;

/**
 * Description of InventoryImporter
 */

class InventoryImporter extends \CFormModel
{

    /**
     * @var string $area
     */
    public $area;
    /**
     * @var string $reason
     */
    public $reason;
    /**
     * @var string $remarks
     */
    public $remarks;
    /**
     * @var string $file
     */
    public $import_file;

    /**
     * @var string $import_data
     */
    public $import_data;

    /**
     * @inheritdoc
     */
    public $import_date;
    public $import_time;

    public function rules()
    {
        return array(
            array('area', 'required'),
            array('import_file', 'file', 'types'=>'xls,xlsx', 'allowEmpty' => false),
            array('area, reason, remarks, import_data, import_date, import_time', 'safe')
        );
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return array(
            'area' => 'Inventory area',
            'reason' => 'Reason',
            'import_file' => 'File to import',
            'import_date' => 'Adjustment Date',
            'import_time' => 'Adjustment Time'
        );
    }



    //check if date is valid not in unix format added by julz
    public function validateDate($date)
    {   
        if(is_numeric($date)){
            return true;
        }else{
            return false;
        }
    }

    /**
     * @param string $fileName
     * @param array
     */
    public function importFromXlsFile($fileName)
    {
        /** @var \PHPExcel_Reader_Excel2007 $reader */
        $reader = PHPExcel_IOFactory::createReader('Excel5');
        $reader->setReadDataOnly(true);
        $excel = $reader->load($fileName);
        $sheet = $excel->getActiveSheet();

        $validColumns = array(
            'item_code',
            'item_name',
            'unit',
            'order_no',
            'expiry',
            'unit_cost',
            'quantity',
            'adj_expiry',
            'adj_cost',
            'adj_quantity',
        );

        /**
         * For now read according to the recommended column order
         *
         * @todo Read cell data according to the order of the column headers
         */
        $columnDefs = array_combine(
        // A, B, C, ...
            range('A', chr(ord('A') + sizeof($validColumns) - 1)),
            $validColumns
        );

        $data = array();
        foreach ($sheet->getRowIterator() as $row) {

            if ($row->getRowIndex() < 6) {
                // Ignore headers, for now...
                continue;
            }

            $iterator = $row->getCellIterator();
            // $iterator->setIterateOnlyExistingCells(false);

            $rowData = array();
            $errors = array();

            /**
             * @var \PHPExcel_Cell $cell
             */
            foreach ($iterator as $cell) {

                // Get the equivalent data label for the column
                $columnDef = $columnDefs[$cell->getColumn()];

                // If it is a valid column, add to data array
                if ($columnDef) {

                    $rowData[$columnDef] = (string) $cell->getValue();
                    if($columnDef === 'adj_expiry' && $rowData[$columnDef]) {

                        //added by julz
                        $validDate = $this->validateDate($rowData[$columnDef]);
                        if($validDate){
                           $rowData[$columnDef] = date("Y-m-d", PHPExcel_Shared_Date::ExcelToPHP($rowData[$columnDef]));   
                        }
                        //end
                       
                    }
                }
            }

            if (!strlen(trim($rowData['adj_expiry'])) && !strlen(trim($rowData['adj_cost'])) && !strlen(trim($rowData['adj_quantity']))) {
                continue;
            }

            $rowData['item'] = \SegHis\modules\inventory\models\PharmacyProduct::model()->findByPk($rowData['item_code']);
            if (!$rowData['item']) {
                $errors[] = 'This item is <b>NOT</b> in the databank';
            } elseif (levenshtein($data['item_name'], $data['item']->artikelname) > 5) {
                $errors[] = 'The item name does not completely match the name in the databank';
            }

            if (floatval($rowData['unit_cost']) == 0 && floatval($rowData['adj_cost'] == 0)) {
                $errors[] = 'The <b>unit cost</b> for this item is not set';
            }

            $rowData['errors'] = $errors;

            $data[] = $rowData;

        }

        return $data;
    }

    /**
     * Generate
     */
     #modified function parameter : added import date , monmon
    public function import($import_date)
    {
        $importData = CJSON::decode($this->import_data);
        // var_dump($importData);die;
        if (!$importData) {
            throw new \CException('Imported data is invalid');
        }

        /** @var \User $user */
        $user = \User::model()->findByPk(\Yii::app()->user->id);

        $adjustment = new \Adjustment();
        $adjustment->refno = \Adjustment::getNewRefNo();

        
        // $adjustment->adjust_date = date('Y-m-d H:i:s');
        $adjustment->adjust_date = date('Y-m-d H:i:s',$import_date);
      

        $adjustment->adjusting_id = $user->personell_nr;
        $adjustment->area_code = $this->area;
        $adjustment->remarks = $this->remarks;
        $adjustment->history = "Create: ".date('Y-m-d H:i:s')." [".$_SESSION['sess_temp_userid']."]\n";
        // $adjustment->is_posted = 0;
        $adjustment->is_deleted = 0;
        $adjustment->create_id = $_SESSION['sess_temp_userid'];
        $adjustment->create_dt = date('Y-m-d');

        if (!$adjustment->save()) {
            throw new \CDbException('Adjustment not saved');
        }

        $post_date = date('Y-m-d H:i:s');
            // echo "<pre>";
            // var_dump($importData);die;
        foreach ($importData as $row) {
            #added by monmon : workaround for saving item into ledger
            $adj_qty = $row['adj_quantity'];
            
            $prev_qty = $row['quantity'];
            $movemntqty = ($adj_qty) ? ($adj_qty - $prev_qty) : 0;

            $inventoryLedger = new \InventoryLedger();
            $inventoryLedger->post_uid = \Yii::app()->getDb()->createCommand("SELECT UUID()")->queryScalar();
            $inventoryLedger->post_date = $post_date;
            $inventoryLedger->tref_no = $adjustment->refno; 
            $inventoryLedger->tr_date = date('Y-m-d',$import_date);
            $inventoryLedger->tr_code = 'ADJ';
            $inventoryLedger->mvmnt_qty = $movemntqty;
            $inventoryLedger->prev_qty = '0.000';
            $inventoryLedger->prev_cost = '0.00';


            $current_unit_cost = \Yii::app()->getDb()->createCommand("SELECT fn_getItemAvgCost(:item_code, now())")->queryScalar(array('item_code'=>$row['item_code']));

            if($movemntqty < 0)
                $inventoryLedger->unit_cost = $current_unit_cost;
            else
                $inventoryLedger->unit_cost = ($row['adj_cost']) ? $row['adj_cost'] : '0.00';

            /** @var PharmacyProduct $item */
            $item = PharmacyProduct::model()->findByPk($row['item_code']);
            if (!$item) {
                throw new \CDbException('The record for the item `'. \CHtml::encode($row['item_name']) . '` could not be found in the database');
            }

            $expiry = '0000-00-00';
            if ($row['expiry']) {
                $ts = strtotime($row['expiry']);
                if ($ts) {
                    $expiry = date('Y-m-d', $ts);
                }
            }

            $adjExpiry = $expiry;
            if ($row['adj_expiry']) {
                $ts = strtotime($row['adj_expiry']);
                if ($ts) {
                    $adjExpiry = date('Y-m-d', $ts);
                }
            }

           
            $sku = null;
            $sku = StockKeepingUnit::model()->findByAttributes(array(
                'item_code' => $row['item_code'],
                // 'order_no'=> $row['order_no'],
                'unit_id' => $item->ext->pc_unit_id,
                'expiry_date' => $expiry,
                'area_code' => $this->area,
                // 'serial_no' => '', 
                'unit_cost' => $inventoryLedger->unit_cost,
                // 'lot_no' => '',
            ));

            if (!$sku) {
                $sku = new StockKeepingUnit();
                $sku->sku_id  = \Yii::app()->getDb()->createCommand("SELECT UUID()")->queryScalar();
                $sku->item_code = $row['item_code'];
                $sku->area_code = $this->area;
                $sku->expiry_date = $adjExpiry;
                $sku->unit_id = $item->ext->pc_unit_id;
                $sku->unit_cost = (string) $row['adj_cost'] ?: (string) $row['unit_cost'];

                if (!$sku->save()) {
                    throw new \CDbException('The SKU record for the item `'. \CHtml::encode($row['item_name']) . '` could not be created');
                }
            } else{
                #modified by monmon : update sku catalog unit cost
                $adjCost = (string) $row['adj_cost'];
                $origCost = (string) $row['unit_cost'];

                $cost = ($adjCost) ? $adjCost : $origCost;
                $sku->saveAttributes(array(
                    'expiry_date' => $adjExpiry,
                    'unit_cost' => $cost
                ));
            }


            $inventoryLedger->sku_id= $sku->sku_id;
            // $inventoryLedger->packqty  = \Yii::app()->getDb()->createCommand("SELECT qty_per_pack FROM seg_item_extended WHERE item_code = :item_code ")->queryScalar(array('item_code'=>$row['item_code']));
            $inventoryLedger->packqty  = 1;

            if (!$inventoryLedger->save()) {
                throw new \CDbException('The SKU- record for the item `'. \CHtml::encode($row['item_name']) . '` could not be created');
            }

            $detail = new \AdjustmentDetails();
            $detail->refno = $adjustment->refno;
            // $detail->sku_id = $sku->sku_id;
            $detail->item_code = $row['item_code'];
            $detail->unit_id = $sku->unit_id;
            $detail->is_unitperpc = $sku->unit->is_unit_per_pc;
            $detail->expiry_date = $adjExpiry;
           
            $detail->orig_qty = ($row['quantity'] < 0) ? 0 : $row['quantity'];


            if ((string) $row['adj_quantity'] === '') {
                $detail->adj_qty = $detail->orig_qty;
            } else {
                $detail->adj_qty = $row['adj_quantity'];
            }

            // if ((string) $row['adj_cost'] === '') {
            //     $detail->unit_cost = $row['unit_cost'];
            // } else {
            //     $detail->unit_cost = $row['adj_cost'];
            // }
            $detail->reason = $this->reason;
            // $detail->is_deleted = 0;
                  
            if (!$detail->save()) {
                throw new \CDbException('The adjustment record for item `'. \CHtml::encode($row['item_name']) . '` could not be saved');
            }
           
             

        }

    }
}
