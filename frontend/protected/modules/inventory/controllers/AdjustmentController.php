<?php
use SegHis\modules\inventory\models\SearchStockKeepingUnit;
use \SegHis\modules\inventory\models\Unit;
use SegHis\modules\inventory\models\StockKeepingUnit;

Yii::import('inventory.models.AdjustmentReason');

class AdjustmentController extends Controller
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
            'postOnly + delete', // we only allow deletion via POST request
            array('bootstrap.filters.BootstrapFilter')
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
     * Displays a particular model.
     * @param integer $id the ID of the model to be displayed
     */
    public function actionView($id)
    {

        $items = new AdjustmentDetails('search');
        $items->unsetAttributes();

        if (isset($_GET['AdjustmentDetails']))
            $items->setAttributes($_GET['AdjustmentDetails']);

        $items->setAttribute('refno', $id);

        $this->render('view', array(
            'model' => $this->loadModel($id),
            'items' => $items
        ));
    }

    private static function saveItems(Adjustment &$adjustment)
    {
        if ($_POST['adjustment_items']) {
            foreach ($_POST['adjustment_items'] as $key => $item) {
                /* @var $model AdjustmentDetails */
                $model = AdjustmentDetails::model()->findByPk($key);
                $model->sku_id = $item['item_sku_id'];

                $model->expiry_date = date('Y-m-d', strtotime($item['item_expiry_date']));
                if ($model->expiry_date === '1970-01-01' || $model->expiry_date === '0000-00-00') {
                    $model->expiry_date = null;
                }

                $model->unit_id = $item['item_unit'] == '' ? 0 : $item['item_unit'];
                $model->serial_no = $item['item_serial_no'] == '' ? 'none' : $item['item_serial_no'];
                $model->orig_qty = $item['item_remaining'] == '' ? 1 : $item['item_remaining'];
                $model->adj_qty = $item['item_adj'] == '' ? 1 : $item['item_adj'];
                $model->unit_cost = $item['item_unit_cost'] == '' ? 1 : $item['item_unit_cost'];
                $model->reason = $item['item_reason'] == '' ? 'none' : $item['item_reason'];
                $model->lot_no = $item['item_lot_no'] == '' ? 'none' : $item['item_lot_no'];

                if (!$model->save()) {
                    return $model->getErrors();
                }
            }
        }
        if (!empty($_POST['item_id'])) {
            $itemIds = $_POST['item_id'];
            for ($i = 0; $i < count($itemIds); $i++) {
                $model = new AdjustmentDetails();
                $model->refno = $adjustment->refno;
                $model->sku_id = $_POST['item_sku_id'][$i];
                $model->item_code = $itemIds[$i];
                $model->unit_id = $_POST['item_unit'][$i];
                $model->is_unitperpc = 1;

                $model->expiry_date = date('Y-m-d', strtotime($_POST['item_expiry_date'][$i]));
                if ($model->expiry_date === '1970-01-01' || $model->expiry_date === '0000-00-00') {
                    $model->expiry_date = null;
                }

                $model->serial_no = $_POST['item_serial_no'][$i];
                $model->orig_qty = $_POST['item_remaining'][$i];
                $model->adj_qty = $_POST['item_adj'][$i];
                $model->unit_cost = $_POST['item_unit_cost'][$i];
                $model->reason = $_POST['item_reason'][$i];
                $model->lot_no = $_POST['item_lot_no'][$i];
                if (!$model->save()) {
                    return $model->getErrors();
                }
            }
        }
        return true;
    }

    /**
     * @param Adjustment $adjustment
     */
    public function saveAdjustment(Adjustment $adjustment)
    {
        $adjustment->attributes = $_POST['Adjustment'];
        $adjustment->is_deleted = 0;

//        $postToInventory = $_POST['post-to-inventory'] == true;
//        if ($postToInventory) {
//            /* Inventory is Updated via beforeSave() method of Adjustment and AdjustmentDetails */
//            $adjustment->is_posted = $postToInventory ? 1 : 0;
//        }

        /* @var $transaction CDbTransaction */
        $transaction = Yii::app()->db->beginTransaction();

        try {

            if (!$adjustment->save()) {
                throw new CException('Unable to save adjustment information');
            }

            $saveItems = $this->saveItems($adjustment);
            if ($saveItems !== true) {
                $adjustment->addErrors($saveItems);
                throw new CException('Unable to save adjustment items');
            }

            if ($_POST['post-to-inventory'] == true) {
                $adjustment->updateInventory();
                $transaction->commit();

                Yii::app()->user->setFlash('success', '<i class="fa fa-check-circle fa-lg"></i> Adjustment was successfully posted to inventory.');
                $this->redirect(array('adjustment/admin'));

            } else {
                $transaction->commit();
                if ($adjustment->getIsNewRecord()) {
                    Yii::app()->user->setFlash('success', '<i class="fa fa-check-circle fa-lg"></i> Adjustment entries successfully saved.');
                } else {
                    Yii::app()->user->setFlash('success', '<i class="fa fa-check-circle fa-lg"></i> Adjustment entries successfully updated.');
                }
                $this->redirect(array('adjustment/update/id/' . $adjustment->refno));
            }

        } catch (Exception $e) {
            $transaction->rollback();
            Yii::app()->user->setFlash('error', $e->getMessage());
        }
    }

    /**
     * Creates a new model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     */
    public function actionCreate()
    {
        $model = new Adjustment;

        // Uncomment the following line if AJAX validation is needed
        $this->performAjaxValidation($model);

        if (isset($_POST['Adjustment'])) {
            $model->refno = $model->getNewRefno();
            $this->saveAdjustment($model);
        }

        $this->render('create', array(
            'model' => $model,
            'areas' => CHtml::listData(self::getAreas(), 'area_code', 'area_name'),
            'reasons' => CHtml::listData(AdjustmentReason::model()->findAll(), 'adj_reason_id', 'adj_reason_name'),
            'units' => self::getUnits()
        ));
    }

    /**
     *
     */
    public function actionQuickCreate()
    {
        $model = new Adjustment;
        $this->performAjaxValidation($model);
        if (isset($_POST['Adjustment'])) {
            $model->refno = $model->getNewRefno();
            $this->saveAdjustment($model);
        }
        $this->render('quickCreate', array(
            'model' => $model,
            'areas' => CHtml::listData(self::getAreas(), 'area_code', 'area_name'),
            'reasons' => CHtml::listData(AdjustmentReason::model()->findAll(), 'adj_reason_id', 'adj_reason_name'),
            'units' => self::getUnits()
        ));
    }

    /**
     * Updates a particular model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id the ID of the model to be updated
     */
    public function actionUpdate($id)
    {
        $model = $this->loadModel($id);

        // Uncomment the following line if AJAX validation is needed
        $this->performAjaxValidation($model);

        if (isset($_POST['Adjustment'])) {
            $this->saveAdjustment($model);
        }

        $this->render('update', array(
            'model' => $model,
            'areas' => CHtml::listData(self::getAreas(), 'area_code', 'area_name'),
            'units' => self::getUnits()
        ));
    }

    /**
     * Deletes a particular model.
     * If deletion is successful, the browser will be redirected to the 'admin' page.
     * @param integer $id the ID of the model to be deleted
     */
    public function actionDeleteItem($id)
    {
        $item = AdjustmentDetails::model()->findByPk($id);
        $item->is_deleted = 1;
        if ($item->save()) {
            echo CJSON::encode(array('result' => true));
        } else {
            echo CJSON::encode(array('result' => false));
        }
    }

    /**
     * Lists all models.
     */
    public function actionIndex()
    {
        $dataProvider = new CActiveDataProvider('Adjustment');
        $this->render('index', array(
            'dataProvider' => $dataProvider,
        ));
    }

    /**
     * Manages all models.
     */
    public function actionAdmin()
    {
        $model = new Adjustment('search');
        $model->unsetAttributes();  // clear any default values
        $model->isPostedToInventory = '';
        if (isset($_GET['Adjustment']))
            $model->attributes = $_GET['Adjustment'];

        $this->render('admin', array(
            'model' => $model,
        ));
    }

    /**
     * Returns the data model based on the primary key given in the GET variable.
     * If the data model is not found, an HTTP exception will be raised.
     * @param integer $id the ID of the model to be loaded
     * @return Adjustment the loaded model
     * @throws CHttpException
     */
    public function loadModel($id)
    {
        $model = Adjustment::model()->findByPk($id);
        if ($model === null)
            throw new CHttpException(404, 'The requested page does not exist.');
        return $model;
    }

    /**
     * Performs the AJAX validation.
     * @param Adjustment $model the model to be validated
     */
    protected function performAjaxValidation($model)
    {
        if (isset($_POST['ajax']) && $_POST['ajax'] === 'adjustment-form') {
            echo CActiveForm::validate($model);
            Yii::app()->end();
        }
    }

    public function getReasons()
    {
        return CHtml::listData(AdjustmentReason::model()->findAll(), 'adj_reason_id', 'adj_reason_name');
    }

    public static function getAreas()
    {
        return Area::model()->findAll();
    }

    public static function getUnits(){
        return Unit::model()->findAll();
    }

    /**
     * @param $q
     * @param $area
     * @param $dateTo
     */
    public static function actionSearch($q, $area, $dateTo)
    {
        $products = StockKeepingUnit::findStockKeepingUnits($q, $area, date('m/d/Y', strtotime($dateTo)));
        header('Content-type: application/json');
        echo CJSON::encode(self::productsToJson($dateTo, $products));

//        $quantity = $product->getStockQuantityByDate($dateTo);
//        $expiryTimeStamp = strtotime($product->expiry_date);
//        $result[] = array(
//            'skuId' => $product->sku_id,
//            'code' => $product->item->bestellnum,
//            'name' => $product->item->artikelname,
//            'quantity' => (int)$quantity,
//            'unit' => $product->unit->unit_desc,
//            'unitCode' => $product->unit->unit_id,
//            'serial' => $product->serial_no,
//            'lot' => $product->lot_no,
//            'expiryDate' => ($expiryTimeStamp) ? date('m/d/Y', $expiryTimeStamp) : '',
//            'cost' => $product->unit_cost
//        );
    }

    /**
     *
     */
    public function actionSearchGrid($q = '', $area = null)
    {
        $dp = Adjustment::getSkuItemProvider($area, $q, null);
        $this->renderPartial('createUpdate/_search', array(
            'dataProvider' => $dp
        ));
    }

    public function actionStats($q, $area, $dateTo)
    {
        header('Content-type: application/json');
        echo CJSON::encode(SearchStockKeepingUnit::filter($q, $area, date('m/d/Y', strtotime($dateTo)))->stats());
    }

    public function actionPage($q, $area, $dateTo, $page)
    {
        $search = SearchStockKeepingUnit::filter($q, $area, date('m/d/Y', strtotime($dateTo)));
        header('Content-type: application/json');
        echo CJSON::encode(array(
            'products' => self::productsToJson($dateTo, $search->getPageData($page)),
            'status' => $search->stats(),
        ));
    }

    /**
     * @param $dateTo
     * @param $products StockKeepingUnit[]
     * @return array
     */
    public static function productsToJson($dateTo, $products)
    {
        $result = array();
        if (!empty($products)) {

            foreach ($products as $product) {

                $quantity = $product->getStockQuantityByDate($dateTo);
                $expiryTimeStamp = strtotime($product->expiry_date);
                $result[] = array(
                    'skuId' => $product->sku_id,
                    'code' => $product->item->bestellnum,
                    'name' => $product->item->artikelname,
                    'quantity' => (int)$quantity,
                    'unit' => $product->unit->unit_desc,
                    'unitCode' => $product->unit->unit_id,
                    'serial' => $product->serial_no,
                    'lot' => $product->lot_no,
                    'expiryDate' => ($expiryTimeStamp) ? date('m/d/Y', $expiryTimeStamp) : '',
                    'cost' => $product->unit_cost
                );

            }
        }

        return $result;
    }

}