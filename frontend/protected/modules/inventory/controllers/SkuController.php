<?php

/**
 * SkuController.php
 *
 * @author Alvin Quinones <ajmquinones@gmail.com>
 * @copyright &copy; 2015, Segworks Technologies Corporation
 */
use SegHis\modules\inventory\models\StockKeepingUnit;

/**
 * Description of SkuController.php
 *
 */

class SkuController extends Controller
{

    /**
     * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
     * using two-column layout. See 'protected/views/layouts/column2.php'.
     */
    //public $layout='//layouts/column2';

    /**
     * @return array action filters
     */
    public function filters()
    {
        return array(
            'accessControl', // perform access control for CRUD operations
        );
    }

    /**
     * Specifies the access control rules.
     * This method is used by the 'accessControl' filter.
     * @return array access control rules
     */
    public function accessRules()
    {
        return array();
    }

    /**
     * [actionIndex description]
     */
    public function actionSearch()
    {
        $this->layout = false;
        $request = Yii::app()->getRequest();
        $searchKey = trim($request->getQuery('name', ''));
        $area = $request->getQuery('area', 'IP');
        $start = $request->getQuery('start', 0);
        $len = $request->getQuery('length', 10);

        Yii::import('inventory.models.Adjustment');

        $data = array();
        $total = 0;
        $filtered = 0;
        try {
            $dp = Adjustment::getSkuItemProvider($area, $searchKey, array(
                'offset' => $start,
                'limit' => $len
            ));
            if ($dp) {
                $dpData = $dp->getData();
                $total = Adjustment::getTotalSkuItemsCount();
                $filtered = $dp->getTotalItemCount();
                foreach ($dpData as $row) {
                    /** @var StockKeepingUnit $sku */
                    $sku = StockKeepingUnit::model()->findByPk($row['sku_id']);

                    $expiry = strtotime($row['expiry_date']);
                    if ($expiry === false) {
                        $expiry = '';
                    } else {
                        $expiry = date('m/d/Y', $expiry);
                    }

                    $data[] = array(
                        'id' => $row['bestellnum'],
                        'name' => $row['artikelname'],
                        'generic' => $row['generic'],
                        'sku' => $row['sku_id'],
                        'batch' => $row['order_no'],
                        'lot_no' => $row['lot_no'],
                        'serial_no' => $row['serial_no'],
                        'expiry' => $expiry,
                        'small_unit' => $row['pc_unit_id'],
                        'big_unit' => $row['pack_unit_id'],
                        'packing' => $row['qty_per_pack'],
                        'unit' => $row['unit_id'],
                        'unit_cost' => $row['unit_cost'],
                        'quantity' => $sku ? $sku->getCurrentQuantity() : 0,
                    );
                }
            }
        } catch (Exception $e) {
            die($e);
        }


        echo CJSON::encode(array(
            'data' => $data,
            'recordsTotal' => $total,
            'recordsFiltered' => $filtered
        ));
    }

} // End of class block
