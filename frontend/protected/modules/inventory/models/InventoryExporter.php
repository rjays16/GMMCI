<?php

/**
 * InventoryExporter.php
 *
 * @author Alvin Quinones <ajmquinones@gmail.com>
 * @copyright (c) 2016, Segworks Technologies Corporation
 */

namespace SegHis\modules\inventory\models;

use Adjustment;
use PHPExcel;
use PHPExcel_IOFactory;
use PHPExcel_Style_Alignment;
use PHPExcel_Style_Border;
use PHPExcel_Style_Fill;
use PHPExcel_Style_Protection;

/**
 * Description of InventoryExporter
 *
 *
 */
class InventoryExporter extends \CFormModel
{

    /**
     * @var string $area
     */
    public $area;
    /**
     * @var string $item_type
     */
    public $item_type;
      /**
     * @var string $item_type
     */
    public $date_item;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array(
            array('area', 'required'),
            array('area, date_item','safe')

        );
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return array(
            'area' => 'Inventory area',
            'date_item' => 'Date'
        );
    }

    /**
     * @todo Decouple data extraction logic. The method is only supposed to export raw data to excel format.
     *
     * @return PHPExcel
     */
    public function exportToXls()
    {   ini_set('max_execution_time', 0);
        $tr_date = date('Y-m-d', strtotime($this->date_item));
        
        $provider = Adjustment::getSkuItemProvider($this->area, '',false,$tr_date);
        
        $items = $provider->getData();
    
        $excel = PHPExcel_IOFactory::load(dirname(__DIR__).'/resources/template.xls');
        $activeSheet = $excel->getActiveSheet();
        $activeSheet->getProtection()
            ->setSheet(true)
            ->setInsertRows(false)
            ->setInsertColumns(false);

        $count = $provider->getTotalItemCount();
        $oddRows = array(
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'startcolor' => array('argb' => 'FFD2DEED'),
            )
        );

        $rowStart = 6;
        $rowEnd = $rowStart + $count -1;

        $units = \CHtml::listData(Unit::model()->findAllByAttributes(array(
            'is_deleted' => 0
        )), 'unit_id', 'unit_name');
try {
        foreach ($items as $i => $item) {

            $Refno = $this->getmaxrefno($this->area,$item['bestellnum']);
            $activeSheet->setCellValue('A'.($i+$rowStart), $item['bestellnum']);
            $activeSheet->setCellValue('B'.($i+$rowStart), $item['artikelname']);
            $activeSheet->setCellValue('C'.($i+$rowStart), $units[$item['unit_id']]);
            $activeSheet->setCellValue('D'.($i+$rowStart),$item['order_no']);

            #modified by julz
            $expiry_date = Adjustment::getMedExpry($Refno,$item['bestellnum']);
           
            $expiry_date = $item['expiry_date'] == '0000-00-00' || $item['expiry_date'] == '01/01/1970' || 
            empty($item['expiry_date']) || $item['expiry_date'] == '01/23/1914' ? '' : date('m/d/Y', strtotime($item['expiry_date']));
            
            $activeSheet->setCellValue('E'.($i+$rowStart),$expiry_date);
            #end
            // $unit_cost = $this->fetchUnitCostFromSkuCatalog($item['bestellnum'],$this->area,$tr_date);
            //$unit_cost = $item['unit_cost'];
            $unit_cost = \Yii::app()->getDb()->createCommand("SELECT fn_getItemAvgCost(:item_code, now())")->queryScalar(array('item_code'=>$item['bestellnum']));
            $unit_cost = $unit_cost == null || !$unit_cost || $unit_cost == '' ? 0 : $unit_cost;
            $activeSheet->setCellValue('F'.($i+$rowStart), $unit_cost);

        $qty = $this->getHospitalItemQty($item['bestellnum'],$this->area,$item['expiry_date'],$tr_date);

        if(empty($qty)){
        $qty = 0;
       }


            $activeSheet->setCellValue('G'.($i+$rowStart), $qty);

            $activeSheet->getRowDimension($i+$rowStart)->setRowHeight(16);

            if ($i%2 == 0) {
                $activeSheet->getStyle('A'.($i+$rowStart).':'.'J'.($i+$rowStart))->applyFromArray($oddRows);
            }
        }
} catch (Exception $e) {
    echo 'Caught exception: ',  $e->getMessage(), "\n";die;
}
        // $activeSheet->getStyle('A'.$rowStart.':'.'J'.$rowEnd)->applyFromArray(array(
        //     'borders' => array(
        //         'allborders' => array(
        //             'style' => PHPExcel_Style_Border::BORDER_THIN,
        //             'color' => array('argb' => 'FFBDC2C7'),
        //         ),
        //     ),
        //     'font' => array(
        //         'size' => 10
        //     ),
        //     'alignment' => array(
        //         'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
        //         'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
        //     ),
        // ));

        // // Lock the cells
        // $activeSheet->getStyle('A'.$rowStart.':'.'G'.$rowEnd)->getProtection()->setLocked(PHPExcel_Style_Protection::PROTECTION_PROTECTED);
        // $activeSheet->getStyle('F'.$rowStart.':'.'G'.$rowEnd)->applyFromArray(array(
        //     'alignment' => array(
        //         'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,
        //     )
        // ));

        // Add style to editable cells
        $enabledCells= $activeSheet->getStyle('H'.$rowStart.':'.'J'.$rowEnd);
        $enabledCells->getProtection()->setLocked(PHPExcel_Style_Protection::PROTECTION_UNPROTECTED);
        $enabledCells->applyFromArray(array(
            'borders' => array(
                'outline' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THICK,
                    'color' => array('argb' => 'ff3a3838')
                )
            )
        ));

        return $excel;
    }
    #added by monmon : workaround
    function getLatestItemInfoFromSku($bestellnum){
            $data = \Yii::app()->db->createCommand("Select 
                    unit_cost,
                    unit_id,
                    expiry_date,
                    order_no
                from seg_sku_catalog
                WHERE item_code = '$bestellnum' ORDER BY order_no DESC LIMIT 1")->queryAll();
            return $data ;
    }
     #added by monmon : workaround
    public function getHospitalItemQty($itemcode, $areacode = '',$expiry_date = '',$date='') {
        global $db;

        $qty = doubleval(0);
        

        $arrayFilter = array();
        $sfilter = " item_code = ? ";
        $arrayFilter[] = $itemcode;

        if (!empty($areacode)) {
            $sfilter .= " AND ";
            $sfilter .= "b.area_code = ?";
            $arrayFilter[] = $areacode;
        }

        if(empty($date)){
             $newdate = date('Y-m-d');
        }else{
             $newdate = date('Y-m-d',strtotime($date));
        }
            $sfilter .= " AND ";
            $sfilter .= "a.tr_date <= ?";
            $arrayFilter[] = $newdate;


        $sql = $db->Prepare("SELECT
                                         SUM(a.`mvmnt_qty`)
                                        FROM
                                          `seg_inventory_ledger` a
                                          INNER JOIN `seg_sku_catalog` b
                                            ON a.`sku_id` = b.`sku_id`
                                        WHERE {$sfilter}");

        if($result = $db->GetOne($sql,$arrayFilter)){
            $qty = is_null($result) ? doubleval(0) : doubleval($result);
        }

        return $qty;
    }
    #end monmon

    #added by julz
    public function getmaxrefno($areacode,$itemcode){
          global $db;
          $sql = "SELECT 
                  MAX(sil.tref_no)
                FROM
                  seg_sku_catalog AS sku 
                  LEFT JOIN seg_inventory_ledger AS sil 
                    ON sil.`sku_id` = sku.`sku_id` 
                WHERE sku.`item_code` = '$itemcode' 
                  AND sku.`area_code` = '$areacode' 
                  AND sil.`tr_code` = 'ADJ' 
                  AND sil.tref_no != 'BEGINNING'";
        $Maxrefno = $db->GetOne($sql);
        return $Maxrefno;
    }

    Public function fetchUnitCostFromSkuCatalog($itemcode,$area,$date){
    global $db;
    
        $sql = "SELECT 
                      sil.unit_cost 
                    FROM
                      seg_inventory_ledger AS sil 
                      LEFT JOIN seg_sku_catalog AS sku 
                        ON sku.sku_id = sil.sku_id 
                    WHERE sku.item_code = '$itemcode' 
                      AND sku.area_code = '$area' 
                      AND sil.tr_code IN ('ADJ','RCV')
                      AND sil.tr_date <= '$date' 
                      AND sil.unit_cost > 0 
                    ORDER BY sil.tr_date DESC";
    
    $unitCost = $db->GetOne($sql);
    return $unitCost;
}
    #end
}
