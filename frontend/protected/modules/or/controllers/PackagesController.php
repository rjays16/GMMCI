<?php

Yii::import('or.models.Packages');
Yii::import('or.models.PackageDetails');
Yii::import('or.models.PackagesClinics');
Yii::import('or.models.LabServices');
Yii::import('or.models.RadioServices');
Yii::import('or.models.PharmaProductsMain');
Yii::import('or.models.OtherServices');

class PackagesController extends Controller
{
	/**
	 * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
	 * using two-column layout. See 'protected/views/layouts/column2.php'.
	 */
	public $layout='//layouts/or';

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
		return array(
			array('allow',  // allow all users to perform 'index' and 'view' actions
				'actions'=>array('index','view'),
				'users'=>array('*'),
			),
			array('allow', // allow authenticated user to perform 'create' and 'update' actions
				'actions'=>array('create','update','jsonList','jsonDetail','requestPackage', 'PackageDetails', 'listItem'),
				'users'=>array('@'),
			),
			array('allow', // allow admin user to perform 'admin' and 'delete' actions
				'actions'=>array('admin','delete'),
				'users'=>array('@'),
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}

	/**
	 * Displays a particular model.
	 * @param integer $id the ID of the model to be displayed
	 */
	public function actionView($id)
	{
        if(!empty($_POST))
        {
            $packageDetail = new PackageDetails();
            $packageDetail->deleteAll(array('condition'=>'package_id=:package_id','params'=>array(':package_id'=>$id)));

            $grandTotal = 0;
            foreach($_POST['PackageDetails']['item_code'] as $i => $item_code){
                $packageDetail = new PackageDetails();
                $packageDetail->package_id = $id;
                $packageDetail->item_code = $item_code;
                $packageDetail->item_name = $_POST['PackageDetails']['item_name'][$i];
                $packageDetail->item_purpose = $_POST['PackageDetails']['item_purpose'][$i];
                $packageDetail->quantity = $_POST['PackageDetails']['quantity'][$i];
                $packageDetail->price = $_POST['PackageDetails']['price'][$i];
                $grandTotal +=  ($packageDetail->quantity *  $packageDetail->price);
                if(!$packageDetail->save(false))
                    die('Oops!');
            }
            $package = $this->loadModel($id);
            $package->package_price = $grandTotal;
            if($package->save(false))
                $this->redirect(array('view','id'=>$id));
        }

        $packageDetail=new PackageDetails();
		$this->render('view',array(
			'model'=>$this->loadModel($id),
            'packageDetail'=>$packageDetail,
		));
	}

	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionCreate()
	{
		$model=new Packages();
        $packageDetail = new PackageDetails();

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['Packages']))
		{
			$model->attributes=$_POST['Packages'];
			if($model->save()){
                if(!empty($_POST['Packages']['items'])){
                    $grandTotal = 0;
                    foreach ($_POST['Packages']['items']['item_code'] as $key => $value) {
                        $packageDetailsModel = new PackageDetails();
                        $packageDetailsModel->attributes = array(
                            'package_id' => $model->package_id,
                            'item_code' => $value,
                            'item_name' => $_POST['Packages']['items']['item_name'][$key],
                            'item_purpose' => $_POST['Packages']['items']['item_purpose'][$key],
                            'quantity' => $_POST['Packages']['items']['quantity'][$key],
                            'price' => $_POST['Packages']['items']['price'][$key],
                        );

                        $grandTotal +=  ((int) $_POST['Packages']['items']['quantity'][$key] *  (int) $_POST['Packages']['items']['price'][$key]);
                        if(!$packageDetailsModel->save()){
                            die('Oops!');
                        }
                    }

                    $model->package_price = $grandTotal;
                    if($model->save()){
                        $this->redirect(array('view','id'=>$model->package_id));
                    }
                }
            }

            $this->redirect(array('view','id'=>$model->package_id));
		}

		$this->render('create',array(
			'model'=>$model,
            'packageDetail' => $packageDetail
		));
	}

	/**
	 * Updates a particular model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 * @param integer $id the ID of the model to be updated
	 */
	public function actionUpdate($id)
	{
		$model=$this->loadModel($id);
        $packageDetail = new PackageDetails();

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['Packages']))
		{
			$model->attributes=$_POST['Packages'];
            $grandTotal = 0;

            if(!empty($_POST['Packages']['items'])){
                PackageDetails::model()->deleteAllByAttributes(array('package_id'=>$model->package_id));
                foreach ($_POST['Packages']['items']['item_code'] as $key => $value) {
                    $packageDetailsModel = new PackageDetails();
                    $packageDetailsModel->attributes = array(
                        'package_id' => $model->package_id,
                        'item_code' => $value,
                        'item_name' => $_POST['Packages']['items']['item_name'][$key],
                        'item_purpose' => $_POST['Packages']['items']['item_purpose'][$key],
                        'quantity' => $_POST['Packages']['items']['quantity'][$key],
                        'price' => $_POST['Packages']['items']['price'][$key],
                    );

                    $grandTotal +=  ((int) $_POST['Packages']['items']['quantity'][$key] *  (int) $_POST['Packages']['items']['price'][$key]);
                    if(!$packageDetailsModel->save()){
                        die('Oops!');
                    }
                }

                
            }

            $model->package_price = $grandTotal;
            if($model->save()){
                $this->redirect(array('view','id'=>$model->package_id));
            }
		}

		$this->render('update',array(
			'model'=>$model,
            'packageDetail' => $packageDetail
		));
	}

	/**
	 * Deletes a particular model.
	 * If deletion is successful, the browser will be redirected to the 'admin' page.
	 * @param integer $id the ID of the model to be deleted
	 */
	public function actionDelete($id)
	{
        try{
            $this->loadModel($id)->delete();

            // if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
            if(!isset($_GET['ajax']))
                $this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('admin'));
        }catch(CDbException  $e){
            if($e->errorInfo[1] == 1451) {
                header("HTTP/1.0 400 Relation Restriction");
                echo "Cannot Delete. Package already used.";
            } else {
                throw new CHttpException(400, Yii::t('err', 'bad request'));
            }
        }
	}

	/**
	 * Lists all models.
	 */
	public function actionIndex()
	{
//        $model=new Packages('search');
//        $model->unsetAttributes();
//        if(isset($_GET['Packages']))
//            $model->attributes=$_GET['Packages'];
        $package = new Packages();
        $package->unsetAttributes();
        if(isset($_GET['Packages']))
            $package->attributes=$_GET['Packages'];

        $gridColumns=array(
            array('name'=>'package_id', 'header'=>'Package #','htmlOptions'=>array('style'=>'width:100px;')),
            array('name'=>'package_name','header'=>'Name'),
            array(
                'class'=>'bootstrap.widgets.TbButtonColumn',
                'template'=>'{view} {update} {delete}'
            )
        );

		$this->render('index',array(
            'gridColumns'=>$gridColumns,
            'package'=>$package
		));
	}

	/**
	 * Manages all models.
	 */
	public function actionAdmin()
	{
		$model=new Packages('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['Packages']))
			$model->attributes=$_GET['Packages'];

		$this->render('admin',array(
			'model'=>$model,
		));
	}

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer $id the ID of the model to be loaded
	 * @return Packages the loaded model
	 * @throws CHttpException
	 */
	public function loadModel($id)
	{
		$model=Packages::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}

	/**
	 * Performs the AJAX validation.
	 * @param Packages $model the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='packages-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}

    /**
     * Json Package List for Search
     */
    public function actionJsonList(){
        $results = array();
        if(isset($_GET['name'])){
            $results = Packages::model()->findAll('package_name LIKE :name',array(':name'=>'%'.$_GET['name'].'%'));
        }
        echo CJSON::encode($results);
    }

    /**
     * Json Package detail per package id
     */
    public function actionJsonDetail()
    {
        $results = array();
        if(isset($_GET['package_id']) && !empty($_GET['package_id'])){
            $results = Packages::model()->find('package_id = :package_id', array(':package_id'=>$_GET['package_id']));
        }
        echo CJSON::encode($results);
    }

    public function actionRequestPackage(){
        $package = array();
        if(isset($_GET['package_id']) && !empty($_GET['package_id'])){
            $package = Packages::model()->find('package_id = :package_id', array(':package_id'=>$_GET['package_id']));
        }

        $this->renderPartial('_requestPackage',array('package'=>$package));
    }

    public function actionPackageDetails()
	{
    $model=new PackageDetails;

    // uncomment the following code to enable ajax-based validation
    /*
    if(isset($_POST['ajax']) && $_POST['ajax']==='package-details-_form_package_details-form')
    {
        echo CActiveForm::validate($model);
        Yii::app()->end();
    }
    */

    if(isset($_POST['PackageDetails']))
    {
        $model->attributes=$_POST['PackageDetails'];
        if($model->validate())
        {
            // form inputs are valid, do something here
            return;
        }
    }
    $this->render('_form_package_details',array('model'=>$model));
	}
}
