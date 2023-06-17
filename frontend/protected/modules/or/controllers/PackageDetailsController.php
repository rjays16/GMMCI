<?php

Yii::import('or.models.Packages');
Yii::import('or.models.PackageDetails');

Yii::import('or.models.PharmaProductsMain');
Yii::import('or.models.LabServices');
Yii::import('or.models.RadioServices');
Yii::import('or.models.OtherServices');

class PackageDetailsController extends Controller
{
    /**
     * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
     * using two-column layout. See 'protected/views/layouts/column2.php'.
     */
    public $layout = '//layouts/or';

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
        return array(
            array('allow', // allow all users to perform 'index' and 'view' actions
                'actions' => array('index', 'view'),
                'users' => array('*'),
            ),
            array('allow', // allow authenticated user to perform 'create' and 'update' actions
                'actions' => array('create', 'update', 'jsonItemSearch'),
                'users' => array('@'),
            ),
            array('allow', // allow admin user to perform 'admin' and 'delete' actions
                'actions' => array('admin', 'delete'),
                'users' => array('@'),
            ),
            array('deny', // deny all users
                'users' => array('*'),
            ),
        );
    }

    /**
     * Displays a particular model.
     * @param integer $id the ID of the model to be displayed
     */
    public function actionView($id)
    {
        $this->render('view', array(
            'model' => $this->loadModel($id),
        ));
    }

    /**
     * Creates a new model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     */
    public function actionCreate()
    {
        $model = new PackageDetails;

        // Uncomment the following line if AJAX validation is needed
        // $this->performAjaxValidation($model);

        if (isset($_POST['PackageDetails'])) {
            $model->attributes = $_POST['PackageDetails'];
            if ($model->save())
                $this->redirect(array('view', 'id' => $model->item_id));
        }

        $this->render('create', array(
            'model' => $model,
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
        // $this->performAjaxValidation($model);

        if (isset($_POST['PackageDetails'])) {
            $model->attributes = $_POST['PackageDetails'];
            if ($model->save())
                $this->redirect(array('view', 'id' => $model->item_id));
        }

        $this->render('update', array(
            'model' => $model,
        ));
    }

    /**
     * Deletes a particular model.
     * If deletion is successful, the browser will be redirected to the 'admin' page.
     * @param integer $id the ID of the model to be deleted
     */
    public function actionDelete($id)
    {
        if (Yii::app()->request->isPostRequest) {
            // we only allow deletion via POST request
            $this->loadModel($id)->delete();

            // if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
            if (!isset($_GET['ajax']))
                $this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('admin'));
        } else
            throw new CHttpException(400, 'Invalid request. Please do not repeat this request again.');
    }

    /**
     * Lists all models.
     */
    public function actionIndex()
    {
        $dataProvider = new CActiveDataProvider('PackageDetails');
        $this->render('index', array(
            'dataProvider' => $dataProvider,
        ));
    }

    /**
     * Manages all models.
     */
    public function actionAdmin()
    {
        $model = new PackageDetails('search');
        $model->unsetAttributes(); // clear any default values
        if (isset($_GET['PackageDetails']))
            $model->attributes = $_GET['PackageDetails'];

        $this->render('admin', array(
            'model' => $model,
        ));
    }

    /**
     * Returns the data model based on the primary key given in the GET variable.
     * If the data model is not found, an HTTP exception will be raised.
     * @param integer the ID of the model to be loaded
     */
    public function loadModel($id)
    {
        $model = PackageDetails::model()->findByPk($id);
        if ($model === null)
            throw new CHttpException(404, 'The requested page does not exist.');
        return $model;
    }

    /**
     * Performs the AJAX validation.
     * @param CModel the model to be validated
     */
    protected function performAjaxValidation($model)
    {
        if (isset($_POST['ajax']) && $_POST['ajax'] === 'package-details-form') {
            echo CActiveForm::validate($model);
            Yii::app()->end();
        }
    }

    public function actionJsonItemSearch()
    {
        $results = array();

        if(isset($_GET['term']) && !empty($_GET['term'])){
            $term = '%'.$_GET['term'].'%';
            $purpose = $_GET['purpose'];

            $packageDetail = new PackageDetails();
            $packageDetail->item_purpose = $purpose;
            $itemPurposeText = $packageDetail->itemPurposeText;

            switch($purpose){
                case $packageDetail::ITEM_PURPOSE_PHARMACY:
                    $results = $this->_retrieveItems(new PharmaProductsMain(),$term,'bestellnum','artikelname','price_cash',$itemPurposeText,$purpose);
                    break;
                case $packageDetail::ITEM_PURPOSE_LABORATORY:
                    $results = $this->_retrieveItems(new LabServices(),$term,'service_code','name','price_cash',$itemPurposeText,$purpose);
                    break;
                case $packageDetail::ITEM_PURPOSE_RADIOLOGY:
                    $results = $this->_retrieveItems(new RadioServices(),$term,'service_code','name','price_cash',$itemPurposeText,$purpose);
                    break;
                case $packageDetail::ITEM_PURPOSE_MISCELLANEOUS:
                    $results = $this->_retrieveItems(new OtherServices(),$term,'service_code','name','price',$itemPurposeText,$purpose);
                    break;
                default:
                    //no default action
            }
        }

        echo CJSON::encode($results);
    }

    private function _retrieveItems($model,$term,$idAttr,$nameAttr,$priceAttr='price_cash',$purposeText,$purpose){
        $criteria = new CDbCriteria();
        $criteria->select = $idAttr.','.$nameAttr.','.$priceAttr;
        $criteria->condition = $nameAttr.' LIKE :'.$nameAttr;
        $criteria->params = array(':'.$nameAttr=> $term);
        $criteria->order = $nameAttr.' ASC';
        $items = $model->findAll($criteria);
        $results = $this->_arrangeItems($items,$idAttr,$nameAttr,$priceAttr,$purposeText,$purpose);
        return $results;
    }

    private function _arrangeItems($items,$idAttr,$nameAttr,$priceAttr,$purposeText,$purpose){
        $results = array();
        foreach($items as $item){
            $results[] = array(
                'id'=>$item->{$idAttr},
                'name'=>$item->{$nameAttr},
                'price'=>number_format($item->{$priceAttr}, 2),
                'purpose'=>$purposeText,
                'purposeValue'=>$purpose
            );
        }
        return $results;
    }
}
