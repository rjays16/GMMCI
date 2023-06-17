<?php
use SegHis\models\inventory\Unit;
use SegHis\models\inventory\ItemExtended;

class ProductionController extends Controller
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
			array('bootstrap.filters.BootstrapFilter'),
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
		$items=new ProductionItem('search');
		$items->unsetAttributes();

		if(isset($_GET['ProductionItem']))
			$items->setAttributes($_GET['ProductionItem']);

		$items->setAttribute('production_id',$id);

		$this->render('view',array(
			'model'=>$this->loadModel($id),
			'items'=>$items
		));
	}

	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionCreate()
	{
		$model=new Production;

		// Uncomment the following line if AJAX validation is needed
		$this->performAjaxValidation($model);

		if(isset($_POST['Production'])) {
			$model->id = Production::getNewRefNo();
			$this->save($model);
		}

		$this->render('form',array(
			'model'=>$model,
			'area'=>CHtml::listData(self::getAreas(),'area_code','area_name'),
			'unit'=>CHtml::listData(self::getUnits(),'unit_id','unit_name'),
		));
	}

	private function save(Production &$model){
		/* @var $transaction CDbTransaction */
		$postToInventory = $_POST['post-to-inventory'] == true;
		$transaction = Yii::app()->db->beginTransaction();

		if(!$model->getIsNewRecord()){
			$_POST['Production']['to_smaller'] = $model->to_smaller;
		}

		$model->setAttributes($_POST['Production']);

		global $db;
		if($postToInventory) {
			$model->is_posted = $postToInventory ? 1 : 0;
			$db->StartTrans();
		}

		if($model->save()){

			$updateInventory = true;
			if($postToInventory)
				$updateInventory = $model->updateInventory();

			$saveItems = self::saveProductionItem($model,$postToInventory);

			if($saveItems===true && $updateInventory === true){
				$transaction->commit();
				$db->CompleteTrans();
				if($postToInventory){
					Yii::app()->user->setFlash('success',"<strong>Production was successfully saved and posted to Inventory!</strong>");
					$this->redirect(array('production/view/id/'.$model->id));
				}else{
					Yii::app()->user->setFlash('success',"<strong>Production was successfully saved!</strong>");
					$this->redirect(array('production/update/id/'.$model->id));
				}
			}else{
				if(is_array($saveItems))
					$model->addErrors($saveItems);
				$transaction->rollback();
				$db->FailTrans();
				Yii::app()->user->setFlash('error',"<strong>An error occurred!</strong>");
			}
		}

	}

	private static function saveProductionItem(Production $production, $postToInventory = false){
		if(empty($_POST['item_id']) && empty($_POST['product_items']))
			return array('Production items can\'t be empty.');

		if(!empty($_POST['product_items'])){
			foreach ($_POST['product_items'] as $item) {
				/* @var ProductionItem $model */
				$model = ProductionItem::model()->findByAttributes(array('id' => $item['id']));
				$model->quantity = $item['item_quantity'];
				$model->history .= 'Updated by ' . Yii::app()->user->getId() . ' at ' . date('Y-m-d h:i:s a') . "\n";

				if($postToInventory)
					$model->updateInventory();

				if(!$model->save())
					return $model->getErrors();
			}
		}
		if(!empty($_POST['item_id'])){
			foreach ($_POST['item_id'] as $index => $item_id) {
				$model  = new ProductionItem();
				$model->item_id = $item_id;
				$model->production_id = $production->id;
				$model->quantity = $_POST['item_quantity'][$index];
				$model->history = 'Created by ' . Yii::app()->user->getId() . ' at ' . date('Y-m-d h:i:s a') . "\n";

				if($postToInventory)
					$model->updateInventory();

				if(!$model->save())
					return $model->getErrors();
			}
		}
		return true;
	}

	public function actionUpdate($id)
	{
		$model=$this->loadModel($id);
		if($model->is_posted)
			throw new CException('This product is already posted in inventory and therefore can\'t be updated!');

		if(isset($_POST['Production'])) {
			$this->save($model);
		}

		$this->render('form',array(
			'model'=>$model,
			'area'=>CHtml::listData(self::getAreas(),'area_code','area_name'),
			'unit'=>CHtml::listData(self::getUnits(),'unit_id','unit_name'),
		));
	}

	/**
	 * Deletes a particular model.
	 * If deletion is successful, the browser will be redirected to the 'admin' page.
	 * @param integer $id the ID of the model to be deleted
	 */
	public function actionDelete($id)
	{
		$this->loadModel($id)->delete();

		// if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
		if(!isset($_GET['ajax']))
			$this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('admin'));
	}

	public function actionDeleteProductionItem($id){
		/* @var ProductionItem $model */
		$model = ProductionItem::model()->findByAttributes(array('id'=>$id));
		$model->is_deleted = 1;
		$model->history .= 'Deleted by ' . Yii::app()->user->getId() . ' at ' . date('Y-m-d h:i:s a') . "\n";
		if($model->save())
			echo CJSON::encode(array('result' => true));
		else
			echo CJSON::encode(array('result' => false));
	}

	/**
	 * Lists all models.
	 */
	public function actionIndex()
	{
		$dataProvider=new CActiveDataProvider('Production');
		$this->render('index',array(
			'dataProvider'=>$dataProvider,
		));
	}

	/**
	 * Manages all models.
	 */
	public function actionAdmin()
	{
		$model=new Production('search');
		$model->unsetAttributes();  // clear any default values
		$model->isPostedToInventory = null;

		if(isset($_GET['Production']))
			$model->setAttributes($_GET['Production']);

		$this->render('admin',array(
			'model'=>$model,
		));
	}

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer $id the ID of the model to be loaded
	 * @return Production the loaded model
	 * @throws CHttpException
	 */
	public function loadModel($id)
	{
		$model=Production::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}

	/**
	 * Performs the AJAX validation.
	 * @param Production $model the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='production-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}

	public function actionPackages(){

		require_once dirname(dirname(dirname(dirname(dirname(dirname(__FILE__)))))) . '/include/care_api_classes/inventory/class_sku_inventory.php';
		$inventory = new SKUInventory();

		$q = $_GET['q'];
		$area = $_GET['area'];
		$criteria = new CDbCriteria();
		$criteria->compare('artikelname',$q,true);

		$pharmacyItems = ItemCatalog::model()->findAll($criteria);

		$data = array();

		/* @var $pharmacyItem ItemCatalog */
		foreach($pharmacyItems as $pharmacyItem){

			$remaining = $inventory->getItemQty($pharmacyItem->bestellnum,'',$area);
			$unit =  ItemExtended::model()->findByPk($pharmacyItem->bestellnum);

			$data[] = array(
				'item_id' => $pharmacyItem->bestellnum,
				'name' => $pharmacyItem->artikelname,
				'price' => number_format($pharmacyItem->price_cash,2,'.',''),
				'remaining' => $remaining,
				'unitId' => $unit->pc_unit_id,
				'unitDesc' => ucwords($unit->unit->unit_desc)
			);

		}

		echo CJSON::encode($data);
	}

	public static function getAreas(){
		return Area::model()->findAll();
	}

	public static function getUnits(){
		return Unit::model()->findAll();
	}

}