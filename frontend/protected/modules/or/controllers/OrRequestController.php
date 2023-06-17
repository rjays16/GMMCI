<?php

Yii::import('or.models.*');
// Yii::import('or.models.OrDeposit');
// Yii::import('or.models.OrChecklist');
// Yii::import('or.models.OrTechnique');
// Yii::import('or.models.OrAnesthesia');
// Yii::import('or.models.OrAnesthesiaUse');
// Yii::import('or.models.OrPackageUse');
// Yii::import('or.models.OrPackagesItems');
// Yii::import('or.models.OrPreOpDetails');
// Yii::import('or.models.OrPostOpDetails');
// Yii::import('or.models.OrSurgicalTeam');
// Yii::import('or.models.OrChecklistPreopData');
// Yii::import('or.models.Packages');
// Yii::import('or.models.PackageDetails');
// Yii::import('or.models.CaseRatePackages');
// Yii::import('or.models.PharmaProductsMain');
// Yii::import('or.models.PharmaOrders');
// Yii::import('or.models.PharmaOrderItems');
// Yii::import('or.models.LabServ');
// Yii::import('or.models.LabServdetails');
// Yii::import('or.models.RadioServ');
// Yii::import('or.models.CareTestRequestRadio');
// Yii::import('or.models.MiscOps');
// Yii::import('or.models.MiscOpsDetails');
// Yii::import('or.models.OpsRvs');

class OrRequestController extends Controller
{
    /**
     * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
     * using two-column layout. See 'protected/views/layouts/column2.php'.
     */
    public $layout = 'or.views.layouts.or-main';

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
                'actions'=>array('create','update','approve','schedule','preop',
                    'postop','listRequest', 'getExtPackage', 'listIcpm', 'listMed', 
                    'listPackageMed', 'orForm', 'removeEntry', 'done', 'listMisc', 
                    'updatePostop', 'listExtIcpm', 'listExtAnesthesia', 'listExtMisc'),
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
        $this->render('view',array(
            'model'=>$this->loadModel($id),
        ));
    }

    /**
     * Creates a new model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     */
    public function actionCreate()
    {
        $model=new OrRequest;

        // Uncomment the following line if AJAX validation is needed
        $this->performAjaxValidation($model);

        if(isset($_POST['OrRequest']))
        {
            if($this->saveOr($model))
                $this->redirect(array('index','flag'=>$model->request_flag));
        }

        if(!empty($_GET['pid'])){
            $person = Person::model()->findByPk($_GET['pid']);
            $model->patient_name = $person->fullname;
            $model->patient_gender = $person->sex;
            $model->patient_age = $person->age;
            $model->patient_address = $person->address;
            $model->encounter_nr = $_GET['enc_no'];
        }
        $model->personnel_name = $_SESSION['sess_login_username'];
        $model->trans_type = 1;

        $departmentsArray = CHtml::listData(Department::model()->findAll('is_inactive = 0'), 'nr', 'name_formal');
        $orTypesArray = CHtml::listData(OrType::model()->findAll(), 'or_type_acro', 'or_type_description');
        $checkListArray = CHtml::listData(OrChecklist::model()->findAll(array('condition'=>'type=:type','params'=>array(':type'=>'request'))), 'checklist_id', 'label_data');
        //$checkListArray = array('test1','test2','test3');
        //die(print_r($checkListArray));

        $this->render('create',array(
            'model'=>$model,
            'departmentsArray'=>$departmentsArray,
            'orTypesArray'=>$orTypesArray,
            'checkListArray'=>$checkListArray
        ));
    }

    public function actionOrForm(){
        $this->layout = 'or.views.layouts.or-form';
        $model=new OrRequest;

        $this->performAjaxValidation($model);

        if(isset($_POST['OrRequest']))
        {
            if($this->saveOr($model)){
                $model=new OrRequest;
                Yii::app()->user->setFlash('success', '<strong>Great!</strong> Your New Record has been successfully saved.');
                // $this->refresh();
            }
        }else{
            if(!empty($_GET['pid'])){
                $person = Person::model()->findByPk($_GET['pid']);
                $model->patient_name = $person->fullname;
                $model->patient_gender = $person->sex;
                $model->patient_age = $person->age;
                $model->patient_address = $person->address;
                $model->encounter_nr = $_GET['enc_no'];
            }
            $model->personnel_name = $_SESSION['sess_login_username'];
            $model->trans_type = 1;
        }

        $departmentsArray = CHtml::listData(Department::model()->findAll('is_inactive = 0'), 'nr', 'name_formal');
        $orTypesArray = CHtml::listData(OrType::model()->findAll(), 'or_type_acro', 'or_type_description');
        $checkListArray = CHtml::listData(OrChecklist::model()->findAll(array('condition'=>'type=:type','params'=>array(':type'=>'request'))), 'checklist_id', 'label_data');

        $this->render('_form',array(
            'model'=>$model,
            'departmentsArray'=>$departmentsArray,
            'orTypesArray'=>$orTypesArray,
            'checkListArray'=>$checkListArray
        ));
    }

    public function saveOr($model){
        if(isset($_POST['OrRequest']))
        {
            $transaction = Yii::app()->db->beginTransaction();

            if(isset($_POST['OrRequest']['date_requested'])){
                $_POST['OrRequest']['date_requested'] = date('Y-m-d H:i:s', strtotime($_POST['OrRequest']['date_requested']));
            }
            $model->or_refno = $this->_generateRefno();
            $model->create_date = date( 'Y-m-d H:i:s', time());
            $model->attributes=$_POST['OrRequest'];
            $model->create_id = $_SESSION['sess_temp_userid'];
            $model->modify_id = $_SESSION['sess_temp_userid'];
            $model->history = 'Create: '.date("Y-m-d H:i:s").' = '.$_SESSION['sess_temp_userid'];
            if(isset($_POST['OrRequest']['orChecklists'])){
                $model->orChecklists = $_POST['OrRequest']['orChecklists'];
            }
            if($model->save()){
                if(isset($_POST['OrRequest']['orPackageUses'])){
                    foreach($_POST['OrRequest']['orPackageUses'] as $package_id){
                        $package = Packages::model()->findByPk($package_id);
                        $orPackageUse = new OrPackageUse();
                        $orPackageUse->or_refno = $model->or_refno;
                        $orPackageUse->package_id = $package->package_id;
                        $orPackageUse->package_amount = $package->package_price;
                        if($orPackageUse->save()){
                            foreach($package->packageDetails as $packageDetail){
                                $orPackagesItem = new OrPackagesItems();
                                $orPackagesItem->seg_or_package_use_id = $orPackageUse->id;
                                $orPackagesItem->or_refno = $model->or_refno;
                                $orPackagesItem->package_id = $packageDetail->package_id;
                                $orPackagesItem->item_code = $packageDetail->item_code;
                                $orPackagesItem->qty = $packageDetail->quantity;
                                $orPackagesItem->price = $packageDetail->price;
                                if(!$orPackagesItem->save()){
                                    $transaction->rollback();
                                    die('Oops! Error in Packages');
                                }
                            }
                        }
                    }
                }

                $orDepositModel = new OrDeposit;
                $orDepositModel->attributes = array(
                    'refno' => $model->or_refno,
                    'encounter_nr' => $model->encounter_nr,
                    'pid' => $model->encounter->pid,
                    'amount' => $_POST['OrRequest']['amount'],
                    'status' => 'pending',
                    'proc_status' => $_POST['OrRequest']['surgery_type'],
                );
                if(!$orDepositModel->save())
                    die('Oops! Error in Deposit');

                $transaction->commit();
                return true;
            }
        }

        return false;
    }

    /**
     * Updates a particular model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id the ID of the model to be updated
     */
    public function actionUpdate($id)
    {
        $model=$this->loadModel($id);

        // Uncomment the following line if AJAX validation is needed
        $this->performAjaxValidation($model);

        if(isset($_POST['OrRequest']))
        {
            $transaction = Yii::app()->db->beginTransaction();

            if(isset($_POST['OrRequest']['date_requested'])){
                $_POST['OrRequest']['date_requested'] = date( 'Y-m-d H:i:s', strtotime($_POST['OrRequest']['date_requested']));
            }
            $model->modify_id = $_SESSION['sess_temp_userid'];
            $model->attributes=$_POST['OrRequest'];
            $model->history .= "\n" . 'Updated: '.date("Y-m-d H:i:s").' = '.$_SESSION['sess_temp_userid'];

            if(isset($_POST['OrRequest']['orChecklists'])){
                $model->orChecklists = $_POST['OrRequest']['orChecklists'];
            }

            if($model->save()){
                OrPackageUse::model()->deleteAllByAttributes(array('or_refno'=>$model->or_refno));
                if(isset($_POST['OrRequest']['orPackageUses'])){
                    foreach($_POST['OrRequest']['orPackageUses'] as $package_id){
                        $package = Packages::model()->findByPk($package_id);
                        $orPackageUse = new OrPackageUse();
                        $orPackageUse->or_refno = $model->or_refno;
                        $orPackageUse->package_id = $package->package_id;
                        $orPackageUse->package_amount = $package->package_price;
                        if($orPackageUse->save()){
                            foreach($package->packageDetails as $packageDetail){
                                $orPackagesItem = new OrPackagesItems();
                                $orPackagesItem->seg_or_package_use_id = $orPackageUse->id;
                                $orPackagesItem->or_refno = $model->or_refno;
                                $orPackagesItem->package_id = $packageDetail->package_id;
                                $orPackagesItem->item_code = $packageDetail->item_code;
                                $orPackagesItem->qty = $packageDetail->quantity;
                                $orPackagesItem->price = $packageDetail->price;
                                if(!$orPackagesItem->save()){
                                    $transaction->rollback();
                                    die('Oops! Error in Packages');
                                }
                            }
                        }
                    }
                }

                $orDepositModel = OrDeposit::model()->findByAttributes(array('refno' => $model->or_refno));
                $orDepositModel->attributes = array(
                    'amount' => $_POST['OrRequest']['amount'],
                    // 'remarks' => $_POST['OrRequest']['remarks'],
                    'proc_status' => $_POST['OrRequest']['surgery_type'],
                );
                if(!$orDepositModel->save())
                    die('Oops! Error in Deposit');

                $transaction->commit();
                $this->redirect(array('index','flag'=>$model->request_flag));
            }
        }

        $model->personnel_name = $_SESSION['sess_login_username'];
        $model->patient_name = $model->encounter->person->fullname;
        $model->patient_gender = $model->encounter->person->sex;
        $model->patient_age = $model->encounter->person->age;
        $model->patient_address = $model->encounter->person->address;
        $model->orChecklists = $model->getOrChecklist();

        $orDepositModel = OrDeposit::model()->findByAttributes(array('encounter_nr' => $model->encounter_nr, 'pid' => $model->encounter->pid));
        $model->surgery_type = $orDepositModel->proc_status;
        // $model->remarks = $orDepositModel->remarks;
        $model->amount = $orDepositModel->amount;

        $departmentsArray = CHtml::listData(Department::model()->findAll('is_inactive = 0'), 'nr', 'name_formal');
        $orTypesArray = CHtml::listData(OrType::model()->findAll(), 'or_type_acro', 'or_type_description');
        $checkListArray = CHtml::listData(OrChecklist::model()->findAll(array('condition'=>'type=:type','params'=>array(':type'=>'request'))), 'checklist_id', 'label_data');
        $this->render('update',array(
            'model'=>$model,
            'departmentsArray'=>$departmentsArray,
            'orTypesArray'=>$orTypesArray,
            'checkListArray'=>$checkListArray
        ));
    }

    public function savePharmaOrder($package, $model){
        $total = 0;
        $pharmaOrdersRefno = PharmaOrders::model()->latest()->find()->refno + 1;
        $pharmaOrdersModel = new PharmaOrders();
        $pharmaOrdersModel->attributes = array(
            'refno' => $pharmaOrdersRefno,
            'orderdate' =>$model->date_requested,
            'request_source' => 'OR',
            'pid' => $model->encounter->pid,
            'encounter_nr' => $model->encounter_nr,
            'related_refno' => $model->or_refno,
            'ordername' => $model->encounter->person->fullname,
            'orderaddress' => $model->encounter->person->address,
            'amount_due' => 0,
            'history' => 'Create: '.date("Y-m-d H:i:s").' = '.$_SESSION['sess_temp_userid']
        );
        if(!$pharmaOrdersModel->save())
            die('Oops! Error in Ordering Pharmacy');

        foreach($package->packageDetails as $packageDetail){
            if($packageDetail->item_purpose == $packageDetail::ITEM_PURPOSE_PHARMACY){
                $pharmaOrderItemsModel = new PharmaOrderItems();
                $pharmaOrderItemsModel->attributes = array(
                    'refno' => $pharmaOrdersModel->refno,
                    'bestellnum' => $packageDetail->item_code,
                    'quantity' => $packageDetail->quantity,
                    'pricecash' => $packageDetail->price,
                    'pricecharge' => $packageDetail->price,
                    'price_orig' => $packageDetail->price,
                    'serve_remarks' => ' ',
                    'serve_status' => 'S',
                    'serve_dt' => date('Y-m-d H:i:s'),
                );

                if(!$pharmaOrderItemsModel->save())
                    die('Oops! Error in Ordering Pharmacy');

                $total += $packageDetail->quantity *  $packageDetail->price;
            }
        }

        $pharmaOrdersModel->amount_due = $total;
        if(!$pharmaOrdersModel->save())
            die('Oops!');
    }

    public function saveLabOrder($package, $model){
        $labServModel = new LabServ();

        $labTrackerModel = LabTracker::model()->find();
        $labServRefno = $labTrackerModel->last_refno + 1;
        $labServModel->attributes = array(
            'refno' => $labServRefno,
            'serv_dt' => date('Y-m-d'),
            'serv_tm' => date('H:i:s'),
            'encounter_nr' => $model->encounter_nr,
            'pid' => $model->encounter->pid,
            'history' => 'Create: '.date("Y-m-d H:i:s").' = '.$_SESSION['sess_temp_userid'],
            'ordername' => $model->encounter->person->fullname,
            'orderaddress' => $model->encounter->person->address,
            'source_req' => 'OR',
            'is_cash' => 0,
            'ref_source' => 'LB',
            'status' => ' '
        );
        if(!$labServModel->save())
            die('Oops! Error in Ordering Laboratory');
        
        $labTrackerModel->last_refno = $labServRefno;
        if(!$labTrackerModel->save())
            die('Oops! Error in Ordering Laboratory');

        foreach($package->packageDetails as $packageDetail){
            if($packageDetail->item_purpose == $packageDetail::ITEM_PURPOSE_LABORATORY){
                $labServdetailsModel = new LabServdetails();
                $labServdetailsModel->attributes = array(
                    'refno' => $labServModel->refno,
                    'service_code' => $packageDetail->item_code,
                    'price_cash' => $packageDetail->price,
                    'price_cash_orig' => $packageDetail->price,
                    'price_charge' => $packageDetail->price,
                    'quantity' => $packageDetail->quantity,
                    'history' => 'Create: '.date("Y-m-d H:i:s").' = '.$_SESSION['sess_temp_userid'],
                    'date_request' => $model->date_requested
                );
                if(!$labServdetailsModel->save())
                        die('Oops! Error in Ordering Laboratory');
            }
        }
    }

    public function saveRadOrder($package, $model){
        $radioServModel = new RadioServ();

        $radioServRefno = RadioServ::model()->latest()->find()->refno + 1;
        $radioServModel->attributes = array(
            'refno' => $radioServRefno,
            'request_date' => date('Y-m-d', strtotime($model->date_requested)),
            'request_time' => date('H:i:s', strtotime($model->date_requested)),
            'encounter_nr' => $model->encounter_nr,
            'pid' => $model->encounter->pid,
            'ordername' => $model->encounter->person->fullname,
            'orderaddress' => $model->encounter->person->address,
            'history' => 'Create: '.date("Y-m-d H:i:s").' = '.$_SESSION['sess_temp_userid'],
            'source_req' => 'OR',
            'is_cash' => 0,
            'status' => ' ',
        );
        if(!$radioServModel->save())
            die('Oops! Error in Ordering Radiology');

        foreach($package->packageDetails as $packageDetail){
            if($packageDetail->item_purpose == $packageDetail::ITEM_PURPOSE_RADIOLOGY){
                $careTestRequestRadioModel = new CareTestRequestRadio();
                $careTestRequestRadioRefno = CareTestRequestRadio::model()->latest()->find()->refno + 1;
                $careTestRequestRadioModel->attributes = array(
                    'batch_nr' => $careTestRequestRadioRefno,
                    'refno' => $radioServModel->refno,
                    'clinical_info' => ' ',
                    'service_code' => $packageDetail->item_code,
                    'price_cash' => $packageDetail->price,
                    'price_cash_orig' => $packageDetail->price,
                    'price_charge' => $packageDetail->price,
                    'service_date' => date("Y-m-d H:i:s", '0000-00-00'),
                    'history' => 'Create: '.date("Y-m-d H:i:s").' = '.$_SESSION['sess_temp_userid'],
                    'status' => 'pending',
                    'is_in_house' => 0,
                    'request_doctor' => ' ',
                    'request_date' => date('Y-m-d'),
                    'encoder' => $_SESSION['sess_temp_userid'],
                );
                if(!$careTestRequestRadioModel->save())
                    die('Oops! Error in Ordering Radiology');
            }
        }
    }

    public function saveOthersOrder($package, $model){
        $miscServiceModel = new MiscService;
        $refno = MiscService::model()->getPk(date('Y-m-d H:i:s', strtotime($model->date_requested)));
        // CVarDumper::dump($refno, 10, true);die;
        $miscServiceModel->attributes = array(
            'refno' => $refno,
            'chrge_dte' => date('Y-m-d H:i:s', strtotime($model->date_requested)),
            'encounter_nr' => $model->encounter_nr,
            'pid' => $model->encounter->pid,
            'is_cash' => 0,
            'request_source' => 'OR',
            'history' => 'Create: '.date("Y-m-d H:i:s").' = '.$_SESSION['sess_temp_userid']
            // 'area' => 'Area',
        );
        if(!$miscServiceModel->save())
            die('Oops! Error in Ordering Miscellaneous');

        $entryNo = MiscService::model()->getEntry($model->encounter_nr);
        foreach($package->packageDetails as $packageDetail){
            if($packageDetail->item_purpose == $packageDetail::ITEM_PURPOSE_MISCELLANEOUS){
                $tempModel = OtherServices::model()->findByPk($packageDetail->item_code);
                $miscServicedetailsModels = new MiscServiceDetails;
                $miscServicedetailsModels->attributes = array(
                    'refno' => $miscServiceModel->refno,
                    'service_code' => $tempModel->alt_service_code,
                    'entry_no' => $entryNo,
                    'account_type' => 3,
                    'chrg_amnt' => $packageDetail->price,
                    'quantity' => $packageDetail->quantity
                );
                if(!$miscServicedetailsModels->save())
                    die('Oops! Error in Ordering Miscellaneous');
            }
        }

        
    }

    /*
        Get Existing Packages for Update
    */
    public function actionGetExtPackage(){
        $model=$this->loadModel($_GET['or_refno']);

        echo CJSON::encode($model->orPackageUses);
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

    public function actionRemoveEntry($id)
    {
        $this->loadModel($id)->delete();
        $this->redirect('index.php?r=or/orRequest/index');
    }

    /**
     * Lists all models.
     */
    /*public function actionIndex()
    {
        if(!isset($_GET['flag'])){
            $flag='pending';
        }
        else{
            $flag=$_GET['flag'];
        }
        $this->render(('index'),array('flag'=>$flag));
    }*/

    /**
     * Manages all models.
     */
    public function actionAdmin()
    {
        $model=new OrRequest('search');
        $model->unsetAttributes();  // clear any default values
        if(isset($_GET['OrRequest']))
            $model->attributes=$_GET['OrRequest'];

        $this->render('admin',array(
            'model'=>$model,
        ));
    }

    /**
     * Request approval
     */
    public function actionApprove($id){
        $model=$this->loadModel($id);
        if($model->request_flag !== 'pending'){
            throw new CHttpException(403,'The current status of this request is not pending.');
        }
        else{
            if(isset($_POST['OrRequest']))
            {
                $transaction = Yii::app()->db->beginTransaction();
                $isPharma = false;
                $isLab = false;
                $isRad = false;
                $isOthers = false;

                $model->attributes=$_POST['OrRequest'];
                $model->history .= "\n" . 'Approved: '.date("Y-m-d H:i:s").' = '.$_SESSION['sess_temp_userid'];
                //die (print_r($model));
                if($model->save(false))
                {
                    $packageUse = OrPackageUse::model()->findAllByAttributes(array('or_refno' => $model->or_refno));
                    if($packageUse){
                        foreach ($packageUse as $key => $value) {
                            $package = Packages::model()->findByPk($value->package_id);
                            foreach ($package->packageDetails as $packageDetail) {
                                switch ($packageDetail->item_purpose) {
                                    case $packageDetail::ITEM_PURPOSE_PHARMACY:
                                        $isPharma = true;
                                        break;
                                    case $packageDetail::ITEM_PURPOSE_LABORATORY:
                                        $isLab = true;
                                        break;
                                    case $packageDetail::ITEM_PURPOSE_RADIOLOGY:
                                        $isRad = true;
                                        break;
                                    case $packageDetail::ITEM_PURPOSE_MISCELLANEOUS:
                                        $isOthers = true;
                                        break;
                                }
                            }
                        }
                        foreach ($packageUse as $key => $value) {
                            $package = Packages::model()->findByPk($value->package_id);

                            if($isPharma)
                                $this->savePharmaOrder($package, $model);
                            if($isLab)
                                $this->saveLabOrder($package, $model);
                            if($isRad)
                                $this->saveRadOrder($package, $model);
                            if($isOthers)
                                $this->saveOthersOrder($package, $model);
                        }
                    }

                    $transaction->commit();
                    $this->redirect(array('index','flag'=>'approved'));
                }
            }

            $this->render('approve',array(
                'model'=>$model,
            ));
        }
    }

    /*
     * Request Scheduling
     */

    public function actionSchedule($id)
    {
        $orRequest=$this->loadModel($id);
        $model=new OrPreOpDetails();
        $person=new Person();
        if($orRequest->request_flag !== 'approved'){
            throw new CHttpException(403,'The current status of this request is not approved.');
        }
        else{
            if(isset($_POST['OrPreOpDetails']))
            {
                if(isset($_POST['OrPreOpDetails']['operation_date'])){
                    $_POST['OrPreOpDetails']['operation_date'] = date( 'Y-m-d H:i:s', strtotime($_POST['OrPreOpDetails']['operation_date']));
                }
                $model->attributes=$_POST['OrPreOpDetails'];
                if($model->save())
                {
                    if(isset($_POST['OrPreOpDetails']['orSurgicalTeams']['personell_nr'])){
                        foreach($_POST['OrPreOpDetails']['orSurgicalTeams']['personell_nr'] as $key => $value){
                            $orSurgicalTeam = new OrSurgicalTeam();
                            $orSurgicalTeam->or_refno = $orRequest->or_refno;
                            $orSurgicalTeam->personell_nr = $_POST['OrPreOpDetails']['orSurgicalTeams']['personell_nr'][$key];
                            $orSurgicalTeam->role_type = $_POST['OrPreOpDetails']['orSurgicalTeams']['role_type'][$key];
                            if(!$orSurgicalTeam->save()){
                                die('Oops! Error in Surgical Team');
                            }
                        }
                    }
                    $orRequest->request_flag = 'scheduled';
                    $orRequest->history .= "\n" . 'Scheduled: '.date("Y-m-d H:i:s").' = '.$_SESSION['sess_temp_userid'];
                    if($orRequest->save(false))
                    {
                        $this->redirect(array('index','flag'=>'scheduled'));
                    }
                }
            }

            $this->render('schedule',array(
                'orRequest'=>$orRequest,
                'model'=>$model,
                'person'=>$person
            ));
        }
    }

    /**
     * Request preop
     */
    public function actionPreop($id){
        $orRequest=$this->loadModel($id);
        $model=$orRequest->orPreOpDetail;
        if($orRequest->request_flag !== 'scheduled'){
            throw new CHttpException(403,'The current status of this request is not scheduled.');
        }
        else{
            if(isset($_POST['OrPreOpDetails']))
            {
                $model->attributes=$_POST['OrPreOpDetails'];
                if(isset($_POST['OrPreOpDetails']['orChecklists'])){
                    $model->orChecklists = $_POST['OrPreOpDetails']['orChecklists'];
                }
                if($model->save(false))
                {
                    $orRequest->request_flag = 'preop';
                    $orRequest->history .= "\n" . 'Preop: '.date("Y-m-d H:i:s").' = '.$_SESSION['sess_temp_userid'];
                    if($orRequest->save(false))
                    {
                        $this->redirect(array('index','flag'=>'preop'));
                    }
                }
            }


            $checkListArray = CHtml::listData(OrChecklist::model()->findAll(array('condition'=>'type=:type','params'=>array(':type'=>'preop'))), 'checklist_id', 'label_data');
            $this->render('preop',array(
                'model'=>$model,
                'orRequest'=>$orRequest,
                'checkListArray'=>$checkListArray
            ));
        }
    }

    /**
     * Request preop
     */
    public function actionPostop($id){
        $orRequest=$this->loadModel($id);
        $model=new OrPostOpDetails();
        $person = new Person();
        $anesthesia = new OrAnesthesia();
        $caseRate = new OpsRvs();
        $medsAndSupplies = new PharmaProductsMain();
        $miscellaneous = new OtherServices();
        $preOp = $orRequest->orPreOpDetail;

        if($orRequest->request_flag !== 'preop'){
            throw new CHttpException(403,'The current status of this request is not pre operative.');
        }
        else{
            if(isset($_POST['OrPostOpDetails']))
            {
                $transaction = Yii::app()->db->beginTransaction();

                if(isset($_POST['OrPostOpDetails']['operation_start'])){
                    $_POST['OrPostOpDetails']['operation_start'] = date('Y-m-d H:i:s', strtotime($_POST['OrPostOpDetails']['operation_start']));
                }
                if(isset($_POST['OrPostOpDetails']['operation_end'])){
                    $_POST['OrPostOpDetails']['operation_end'] = date('Y-m-d H:i:s', strtotime($_POST['OrPostOpDetails']['operation_end']));
                }

                $preOp->attributes=$_POST['OrPreOpDetails'];
                if(!$preOp->save(false)){
                    die('Oops! Error in Preop');
                }

                //die(print_r($_POST['OrPostOpDetails']));
                $model->attributes=$_POST['OrPostOpDetails'];
                if($model->save(false))
                {
                    $this->saveOrSurgicalTeam($orRequest->or_refno);
                    $this->saveOrAnesthesiaUse($orRequest->or_refno);
                    $this->saveMiscOps($orRequest);
                    $this->savePharmaOrderPostop($orRequest);
                    $this->saveMiscService($orRequest);
                    $this->saveOrPackageUse($orRequest);

                    $orRequest->request_flag = 'postop';
                    $orRequest->history .= "\n" . 'Postop: '.date("Y-m-d H:i:s").' = '.$_SESSION['sess_temp_userid'];
                    if($orRequest->save(false))
                    {
                        $transaction->commit();
                        $this->redirect(array('index','flag'=>'postop'));
                    }
                }
            }

            $techniquesArray = CHtml::listData(OrTechnique::model()->findAll(), 'technique_id', 'technique_name');
            $this->render('postop',array(
                'model'=>$model,
                'orRequest'=>$orRequest,
                'person'=>$person,
                'techniquesArray'=>$techniquesArray,
                'anesthesia'=>$anesthesia,
                'caseRate' => $caseRate,
                'medsAndSupplies' => $medsAndSupplies,
                'preOp' => $preOp,
                'miscellaneous' => $miscellaneous,
                'isFinalBill' => false
            ));
        }
    }

    private function saveOrSurgicalTeam($or_refno){
        if(isset($_POST['OrPreOpDetails']['orSurgicalTeams']['personell_nr'])){
            OrSurgicalTeam::model()->deleteAllByAttributes(array('or_refno' => $or_refno));
            foreach($_POST['OrPreOpDetails']['orSurgicalTeams']['personell_nr'] as $key => $value){
                $orSurgicalTeam = new OrSurgicalTeam();
                $orSurgicalTeam->or_refno = $or_refno;
                $orSurgicalTeam->personell_nr =  $value;
                $orSurgicalTeam->role_type = $_POST['OrPreOpDetails']['orSurgicalTeams']['role_type'][$key];
                if(!$orSurgicalTeam->save()){
                    die('Oops! Error in Surgical Team');
                }
            }
        }
    }

    private function saveOrAnesthesiaUse($or_refno){
        if(isset($_POST['OrPostOpDetails']['orAnesthesiaUses']['anesth_id'])){
            OrAnesthesiaUse::model()->deleteAllByAttributes(array('or_refno' => $or_refno));
            foreach($_POST['OrPostOpDetails']['orAnesthesiaUses']['anesth_id'] as $key => $value){
                $orAnesthesiaUse = new OrAnesthesiaUse();
                $orAnesthesiaUse->or_refno = $or_refno;
                $orAnesthesiaUse->anesth_id = $value;
                $orAnesthesiaUse->time_begun = date('H:i:s', strtotime($_POST['OrPostOpDetails']['orAnesthesiaUses']['time_begun'][$key]));
                $orAnesthesiaUse->time_end = date('H:i:s', strtotime($_POST['OrPostOpDetails']['orAnesthesiaUses']['time_end'][$key]));
                if(!$orAnesthesiaUse->save()){
                    die('Oops! Error in Anestesia');
                }
            }
        }
    }

    private function saveMiscOps($orRequest){
        if(isset($_POST['OrPostOpDetails']['orIcpm'])){
            $date_requested = $orRequest->date_requested;
            $or_refno = $orRequest->or_refno;
            $enc_no = $orRequest->encounter_nr;

            $miscOpsRefno = MiscOps::model()->getPk($date_requested);

            $miscOpsModel = new MiscOps();
            $miscOpsModel->attributes = array(
                'refno' => $miscOpsRefno,
                'chrge_dte' => $date_requested,
                'encounter_nr' => $enc_no,
                'create_dt' => date('Y-m-d H:i:s'),
            );
            if($miscOpsModel->save()){
                foreach($_POST['OrPostOpDetails']['orIcpm']['code'] as $key => $value){
                    $tempModel = OpsRvs::model()->findByPk($value);

                    $miscOpsDetailsModel = new MiscOpsDetails();
                    $miscOpsDetailsModel->attributes = array(
                        'refno' => $miscOpsModel->refno,
                        'ops_code' => $value,
                        'entry_no' => 1,
                        'op_date' => $date_requested,
                        'rvu' => $tempModel->rvu,
                        'multiplier' => 0,
                        'laterality' => ' ',
                        'group_code' => ' ',
                        'chrg_amnt' => 0,
                        'description' => $tempModel->description,
                    );

                    if($_POST['OrPostOpDetails']['orIcpm']['for_laterality'][$key]){
                        $miscOpsDetailsModel->laterality = $_POST['OrPostOpDetails']['orIcpm']['laterality'][$key];
                    }

                    if(!$miscOpsDetailsModel->save()){
                        die('Oops! Error in ICPM');
                    }
                }
                
            }else{
                die('Oops! Error in ICPM');
            }
        }
    }

    private function savePharmaOrderPostop($orRequest){
        if(isset($_POST['OrPostOpDetails']['ormedsup'])){
            $total = 0;
            $existH = PharmaOrders::model()->findByAttributes(
                array(
                    'request_source' => 'OR',
                    'encounter_nr' => $orRequest->encounter_nr,
                    'related_refno' => $orRequest->or_refno,
                )
            );

            if($existH)
                $pharmaOrdersModel = $existH;
            else{
                $pharmaOrdersModel = new PharmaOrders();

                $pharmaOrdersRefno = PharmaOrders::model()->latest()->find()->refno + 1;
                $pharmaOrdersModel->attributes = array(
                    'refno' => $pharmaOrdersRefno,
                    'orderdate' =>$orRequest->date_requested,
                    'request_source' => 'OR',
                    'pid' => $orRequest->encounter->pid,
                    'encounter_nr' => $orRequest->encounter_nr,
                    'related_refno' => $orRequest->or_refno,
                    'ordername' => $orRequest->encounter->person->fullname,
                    'orderaddress' => $orRequest->encounter->person->address,
                    'amount_due' => 0,
                    'history' => 'Create: '.date("Y-m-d H:i:s").' = '.$_SESSION['sess_temp_userid'],
                );

                if(!$pharmaOrdersModel->save())
                    die('Oops! Error in Medicine and Supplies');
            }

            PharmaOrderItems::model()->deleteAllByAttributes(array('refno' => $pharmaOrdersModel->refno));
            foreach($_POST['OrPostOpDetails']['ormedsup']['id'] as $key => $value){
                $pharmaOrderItemsModel = new PharmaOrderItems();
                $pharmaOrderItemsModel->attributes = array(
                    'refno' => $pharmaOrdersModel->refno,
                    'bestellnum' => $value,
                    'quantity' => $_POST['OrPostOpDetails']['ormedsup']['qty'][$key],
                    'pricecash' => $_POST['OrPostOpDetails']['ormedsup']['price'][$key],
                    'pricecharge' => $_POST['OrPostOpDetails']['ormedsup']['price'][$key],
                    'price_orig' => $_POST['OrPostOpDetails']['ormedsup']['price'][$key],
                    'serve_remarks' => ' ',
                    'serve_status' => 'S',
                    'serve_dt' => date('Y-m-d H:i:s'),
                );
                if(!$pharmaOrderItemsModel->save())
                    die('Oops! Error in Medicine and Supplies');

                $total += ($pharmaOrderItemsModel->quantity * $pharmaOrderItemsModel->pricecash);
                if(!$pharmaOrdersModel->save())
                    die('Oops! Error in Medicine and Supplies');
            }

            $pharmaOrdersModel->serve_status = 'S';
            $pharmaOrdersModel->amount_due = $total;
            if(!$pharmaOrdersModel->save())
                die('Oops! Error in Medicine and Supplies');
        }
    }

    private function saveMiscService($orRequest){
        if(isset($_POST['OrPostOpDetails']['orMisc'])){
            // $total = 0;
            $existH = MiscService::model()->findByAttributes(
                array(
                    'request_source' => 'OR',
                    'encounter_nr' => $orRequest->encounter_nr
                )
            );

            if($existH)
                $miscServiceModel = $existH;
            else{
                $miscServiceModel = new MiscService();

                $refno = MiscService::model()->getPk(date('Y-m-d H:i:s', strtotime($orRequest->date_requested)));
                $miscServiceModel->attributes = array(
                    'refno' => $refno,
                    'chrge_dte' => date('Y-m-d H:i:s', strtotime($orRequest->date_requested)),
                    'encounter_nr' => $orRequest->encounter_nr,
                    'pid' => $orRequest->encounter->pid,
                    'is_cash' => 0,
                    'request_source' => 'OR',
                    'history' => 'Create: '.date("Y-m-d H:i:s").' = '.$_SESSION['sess_temp_userid']
                );
                if(!$miscServiceModel->save())
                    die('Oops! Error in Miscellaneous');
            }

            MiscServiceDetails::model()->deleteAllByAttributes(array('refno' => $miscServiceModel->refno));
            $entryNo = MiscService::model()->getEntry($orRequest->encounter_nr);
            foreach($_POST['OrPostOpDetails']['orMisc']['id'] as $key => $value){
                $tempModel = OtherServices::model()->findByPk($value);
                $miscServicedetailsModels = new MiscServiceDetails;
                $miscServicedetailsModels->attributes = array(
                    'refno' => $miscServiceModel->refno,
                    'service_code' => $tempModel->alt_service_code,
                    'entry_no' => $entryNo,
                    'account_type' => 3,
                    'chrg_amnt' => $_POST['OrPostOpDetails']['orMisc']['price'][$key],
                    'quantity' => $_POST['OrPostOpDetails']['orMisc']['qty'][$key],
                );
                if(!$miscServicedetailsModels->save())
                    die('Oops! Error in Miscellaneous');
            }
        }
    }

    private function saveOrPackageUse($orRequest){
        if(isset($_POST['OrRequest']['orPackageUses'])){
            OrPackageUse::model()->deleteAllByAttributes(array('or_refno' => $orRequest->or_refno));
            foreach($_POST['OrRequest']['orPackageUses'] as $package_id){
                $package = Packages::model()->findByPk($package_id);
                $orPackageUse = new OrPackageUse();
                $orPackageUse->or_refno = $orRequest->or_refno;
                $orPackageUse->package_id = $package->package_id;
                $orPackageUse->package_amount = $package->package_price;
                if($orPackageUse->save()){
                    foreach($package->packageDetails as $packageDetail){
                        $orPackagesItem = new OrPackagesItems();
                        $orPackagesItem->seg_or_package_use_id = $orPackageUse->id;
                        $orPackagesItem->or_refno = $orRequest->or_refno;
                        $orPackagesItem->package_id = $packageDetail->package_id;
                        $orPackagesItem->item_code = $packageDetail->item_code;
                        $orPackagesItem->qty = $packageDetail->quantity;
                        $orPackagesItem->price = $packageDetail->price;
                        if(!$orPackagesItem->save()){
                            $transaction->rollback();
                            die('Oops! Error in Packages');
                        }
                    }
                }
            }
        }
    }

    public function actionUpdatePostop($id){
        $orRequest=$this->loadModel($id);
        $model= $orRequest->orPostOpDetail;
        $person = new Person();
        $anesthesia = new OrAnesthesia();
        $caseRate = new OpsRvs();
        $medsAndSupplies = new PharmaProductsMain();
        $miscellaneous = new OtherServices();
        $preOp = $orRequest->orPreOpDetail;
        $isFinalBill = BillingEncounter::model()->isFinalBill($orRequest->encounter_nr);

        if($isFinalBill)
            Yii::app()->user->setFlash('error', 'This patient is advised to go home.');

        if(isset($_POST['OrPostOpDetails']))
        {
            $transaction = Yii::app()->db->beginTransaction();

            if(isset($_POST['OrPostOpDetails']['operation_start'])){
                $_POST['OrPostOpDetails']['operation_start'] = date( 'Y-m-d H:i:s', strtotime($_POST['OrPostOpDetails']['operation_start']));
            }
            if(isset($_POST['OrPostOpDetails']['operation_end'])){
                $_POST['OrPostOpDetails']['operation_end'] = date( 'Y-m-d H:i:s', strtotime($_POST['OrPostOpDetails']['operation_end']));
            }

            $preOp->attributes=$_POST['OrPreOpDetails'];
            if(!$preOp->save(false)){
                die('Oops! Error in Preop');
            }

            //die(print_r($_POST['OrPostOpDetails']));
            $model->attributes=$_POST['OrPostOpDetails'];
            if($model->save(false))
            {
                $this->saveOrSurgicalTeam($orRequest->or_refno);
                $this->saveOrAnesthesiaUse($orRequest->or_refno);

                if(!$isFinalBill){
                    $this->saveMiscOps($orRequest);
                    $this->savePharmaOrderPostop($orRequest);
                    $this->saveMiscService($orRequest);
                }
                $this->saveOrPackageUse($orRequest);

                $orRequest->history .= "\n" . 'Postop Update: '.date("Y-m-d H:i:s").' = '.$_SESSION['sess_temp_userid'];
                if($orRequest->save(false))
                {
                    $transaction->commit();
                    $this->redirect(array('index','flag'=>'done'));
                }
                else
                    die('Oops! Error in Update');
            }
            else
                die('Oops! Error in Postop');
        }

        $techniquesArray = CHtml::listData(OrTechnique::model()->findAll(), 'technique_id', 'technique_name');
        $this->render('postop',array(
            'model'=>$model,
            'orRequest'=>$orRequest,
            'person'=>$person,
            'techniquesArray'=>$techniquesArray,
            'anesthesia'=>$anesthesia,
            'caseRate' => $caseRate,
            'medsAndSupplies' => $medsAndSupplies,
            'preOp' => $preOp,
            'miscellaneous' => $miscellaneous,
            'isFinalBill' => $isFinalBill
        ));
    }

    /*
        Search for Case Rate Package (ICPM)
    */
    public function actionListIcpm(){
        $final = array();
        $result = OpsRvs::model()->icpmSearch($_GET);

        foreach ($result as $key => $value) {
            array_push(
                $final,
                array(
                    'package_id' => $value->caseRatePackage->package_id,
                    'code' => $value->code,
                    'description' => $value->description,
                    'rvu' => $value->rvu,
                    'laterality' => $value->caseRatePackage->for_laterality,
                )
            );
        }

        echo CJSON::encode($final);
    }

    /*
        Search for Medicine and Supplies
    */
    public function actionListMed(){
        echo CJSON::encode(PharmaProductsMain::model()->medSearch($_GET));
    }

    /*
        Search for Miscellaneous
    */
    public function actionListMisc(){
        echo CJSON::encode(OtherServices::model()->othersSearch($_GET));
    }

    public function actionListPackageMed(){
        $model=$this->loadModel($_GET['id']);
        $final = array();

        $pharmaOrdersModel = PharmaOrders::model()->findByAttributes(
            array(
                'request_source' => 'OR',
                'encounter_nr' => $model->encounter_nr,
                'related_refno' => $model->or_refno,
            )
        );

        foreach ($pharmaOrdersModel->pharmaOrderItems as $key => $value) {
            array_push(
                $final,
                array(
                    'id' => $value->bestellnum,
                    'name' => $value->pharmaProductsMain->name,
                    'qty' => $value->quantity,
                    'price' => number_format(floatval($value->pricecash),2, '.', '')
                )
            );
        }
        
        echo CJSON::encode($final);
    }

    public function actionListExtMisc(){
        $model=$this->loadModel($_GET['id']);
        $final = array();

        $miscServiceModel = MiscService::model()->findByAttributes(
            array(
                'request_source' => 'OR',
                'encounter_nr' => $model->encounter_nr
            )
        );

        foreach ($miscServiceModel->miscServiceDetails as $key => $value) {
            array_push(
                $final,
                array(
                    'id' => $value->serviceCode->service_code,
                    'name' => $value->serviceCode->name,
                    'qty' => $value->quantity,
                    'price' => number_format(floatval($value->chrg_amnt),2, '.', '')
                )
            );
        }
        
        echo CJSON::encode($final);
    }

    public function actionListExtIcpm(){
        $model=$this->loadModel($_GET['id']);
        $final = array();

        $miscOpsModel = MiscOps::model()->findByAttributes(array('encounter_nr' => $model->encounter_nr), array('order' => 'refno DESC'));

        foreach ($miscOpsModel->miscOpsDetails as $key => $value) {
            array_push(
                $final,
                array(
                    'code' => $value->ops_code,
                    'name' => $value->opsCode->description,
                    'laterality' => $value->laterality
                )
            );
        }

        echo CJSON::encode($final);
    }

    public function actionListExtAnesthesia(){
        $model=$this->loadModel($_GET['id']);
        $final = array();

        foreach ($model->orAnesthesiaUses as $key => $value) {
            array_push(
                $final,
                array(
                    'id' => $value->anesth->anesth_id,
                    'name' => $value->anesth->anest_name,
                    'cat' => $value->anesth->anest_category,
                    'tStart' => date('h:i A', strtotime($value->time_begun)),
                    'tend' => date('h:i A', strtotime($value->time_end)),
                )
            );
        }

        echo CJSON::encode($final);
    }

    public function actionDone($id){
        $orRequestModel = $this->loadModel($id);
        $model = new OpaccommodationDetails();

        if($orRequestModel->request_flag !== 'postop'){
            throw new CHttpException(403,'The current status of this request is not post operative.');
        }
        else{
            if(isset($_POST['OpaccommodationDetails'])){
                $transaction = Yii::app()->db->beginTransaction();

                $OpaccommodationModel = new Opaccommodation;
                $OpaccommodationDetailsModel = new OpaccommodationDetails;

                $refno = $OpaccommodationModel->getPk(date('Y-m-d H:i:s', strtotime($orRequestModel->date_requested)));
                $OpaccommodationModel->attributes = array(
                    'refno' => $refno,
                    'encounter_nr' => $orRequestModel->encounter_nr,
                );
                $OpaccommodationModel->chrge_dte = date('Y-m-d H:i:s', strtotime($orRequestModel->date_requested));
                if(!$OpaccommodationModel->save())
                    die('Oops!');

                $entry = Opaccommodation::model()->getEntry($orRequestModel->encounter_nr);
                $ward = CareWard::model()->getWardOr();
                $OpaccommodationDetailsModel->attributes = array(
                    'refno' => $refno,
                    'entry_no' => $entry,
                    'room_nr' => $ward->room->nr,
                    'group_nr' => $ward->nr,
                    'charge' => empty($_POST['OpaccommodationDetails']['charge'])?0:$_POST['OpaccommodationDetails']['charge'],
                );
                if(!$OpaccommodationDetailsModel->save())
                    die('Oops!');

                $orRequestModel->request_flag = 'done';
                $orRequestModel->history .= "\n" . 'Done: '.date("Y-m-d H:i:s").' = '.$_SESSION['sess_temp_userid'];
                if($orRequestModel->save(false))
                {
                    $transaction->commit();
                    $this->redirect(array('index','flag'=>'done'));
                }
            }
        }

        $this->render(
            'done',
            array(
                'orRequestModel' => $orRequestModel,
                'model'=>$model,
                'dataProvider' => OtherServices::model()->getAllOrService()
            )
        );
    }

    /**
     * Listing request for tabs
     */
    function actionIndex(){
        if(!isset($_GET['flag'])){
            $flag='pending';
        }
        else{
            $flag=$_GET['flag'];
        }
        //$this->render(('index'),array('flag'=>$flag));

        $model = new OrRequest();
        $requestsArray = $model->findAll(array('condition'=>'request_flag=:request_flag','order'=>'create_date DESC', 'params'=>array(':request_flag'=>$flag)));
        $gridDataProvider=new CArrayDataProvider($requestsArray, array('keyField'=>'or_refno'));
        // CVarDumper::dump($requestsArray[0]->cashierLink, 10, true);die;
        if($flag == 'pending'){
            $gridColumns = array(
                array('name'=>'or_refno', 'header'=>'Reference #'),
                array('name'=>'date_requested','value'=>'$data->dateRequestedDateText','header'=>'Date Requested'),
                array('name'=>'patient_id','value'=>'$data->encounter->person->pid','header'=>'Patient ID'),
                array('name'=>'dept_nr','value'=>'$data->encounter->person->fullName','header'=>'Patient Name'),
                array('name'=>'cashier_or','value'=>'$data->cashierOr','header'=>'OR No.'),
                array('name'=>'request_flag','value'=>'$data->department->name_formal','header'=>'Department'),
                // array('value'=>'ucfirst($data->request_flag)','header'=>'Status'),
            );
        }
        else{
            $gridColumns = array(
                array('name'=>'or_refno', 'header'=>'Reference #'),
                array('name'=>'date_requested','value'=>'$data->dateRequestedDateText','header'=>'Date Requested'),
                array('name'=>'patient_id','value'=>'$data->encounter->person->pid','header'=>'Patient ID'),
                array('name'=>'dept_nr','value'=>'$data->encounter->person->fullName','header'=>'Patient Name'),
                array('name'=>'request_flag','value'=>'$data->department->name_formal','header'=>'Department'),
                array('name'=>'dept_nr','value'=>'ucfirst($data->request_flag)','header'=>'Status'),
            );
        }

        $defaultAttributes = array(
            'htmlOptions' => array('nowrap'=>'nowrap'),
            'class'=>'bootstrap.widgets.TbButtonColumn',
        );

        switch($_GET['flag']){
            case 'approved':
                $defaultAttributes['template']='{schedule}';
                $defaultAttributes['buttons']=array(
                    'schedule'=>array(
                        'options' => array('rel' => 'tooltip', 'data-toggle' => 'tooltip','title' => Yii::t('app', 'Schedule')),
                        'label'=>'<i class="icon-calendar"></i>',
                        'url'=>'Yii::app()->createUrl("or/orRequest/schedule", array("id"=>$data->or_refno))',
                    )
                );
                $gridColumns[6] = $defaultAttributes;
                break;
            case 'scheduled':
                $defaultAttributes['template']='{preop}';
                $defaultAttributes['buttons']=array(
                    'preop'=>array(
                        'options' => array('rel' => 'tooltip', 'data-toggle' => 'tooltip','title' => Yii::t('app', 'Pre Operative')),
                        'label'=>'<i class="icon-backward"></i>',
                        'url'=>'Yii::app()->createUrl("or/orRequest/preop", array("id"=>$data->or_refno))',
                    )
                );
                $gridColumns[6] = $defaultAttributes;
                break;
            case 'preop':
                $defaultAttributes['template']='{postop}';
                $defaultAttributes['buttons']=array(
                    'postop'=>array(
                        'options' => array('rel' => 'tooltip', 'data-toggle' => 'tooltip','title' => Yii::t('app', 'Post Operative')),
                        'label'=>'<i class="icon-forward"></i>',
                        'url'=>'Yii::app()->createUrl("or/orRequest/postop", array("id"=>$data->or_refno))',
                    )
                );
                $gridColumns[6] = $defaultAttributes;
                break;
            case 'done':
                $defaultAttributes['template']='{update} {done}';
                $defaultAttributes['buttons']=array(
                    'update'=>array(
                        'options' => array('rel' => 'tooltip', 'data-toggle' => 'tooltip','title' => Yii::t('app', 'Update')),
                        'label'=>'<i class="icon-edit"></i>',
                        'url'=>'Yii::app()->createUrl("or/orRequest/updatePostop", array("id" => $data->or_refno))',
                    ),
                    'done'=>array(
                        'options' => array(
                            'class' => 'surgical-memo-btn',
                            'rel' => 'tooltip', 
                            'data-toggle' => 'tooltip',
                            'title' => Yii::t('app', 'Surgical Memo')
                        ),
                        'label'=>'<i class="icon-print"></i>',
                        'url' => '$data->or_refno',
                    )
                );
                $gridColumns[6] = $defaultAttributes;
                break;
            case 'postop':
                $defaultAttributes['template']='{done}';
                $defaultAttributes['buttons']=array(
                    'done'=>array(
                        'options' => array('rel' => 'tooltip', 'data-toggle' => 'tooltip','title' => Yii::t('app', 'Done')),
                        'label'=>'<i class="icon-forward"></i>',
                        'url'=>'Yii::app()->createUrl("or/orRequest/done", array("id"=>$data->or_refno))',
                    )
                );
                $gridColumns[6] = $defaultAttributes;
                break;
            default:
                $defaultAttributes['template']="{approve} {update} {delete}";
                $defaultAttributes['buttons']=array(
                    'approve'=>array(
                        'options' => array('rel' => 'tooltip', 'data-toggle' => 'tooltip','title' => Yii::t('app', 'Approve')),
                        'label'=>'<i class="icon-ok"></i>',
                        'url'=>'Yii::app()->createUrl("or/orRequest/approve", array("id"=>$data->or_refno))',
                    ),
                    'delete'=>array(
                        'options' => array('rel' => 'tooltip', 'data-toggle' => 'tooltip','title' => Yii::t('app', 'Delete')),
                        'label'=>'<i class="icon-trash"></i>',
                        'url'=>'Yii::app()->createUrl("or/orRequest/removeEntry", array("id"=>$data->or_refno))',
                    ) ,
                );
                $gridColumns[6] = $defaultAttributes;
        }

        $this->render('index',
            array(
                'flag'=>$flag,
                'gridDataProvider'=>$gridDataProvider,
                'gridColumns'=>$gridColumns
            )
        );
    }

    /**
     * Returns the data model based on the primary key given in the GET variable.
     * If the data model is not found, an HTTP exception will be raised.
     * @param integer $id the ID of the model to be loaded
     * @return OrRequest the loaded model
     * @throws CHttpException
     */
    public function loadModel($id)
    {
        $model=OrRequest::model()->findByPk($id);
        if($model===null)
            throw new CHttpException(404,'The requested page does not exist.');
        return $model;
    }

    /**
     * Performs the AJAX validation.
     * @param OrRequest $model the model to be validated
     */
    protected function performAjaxValidation($model)
    {
        if(isset($_POST['ajax']) && $_POST['ajax']==='or-request-form')
        {
            echo CActiveForm::validate($model);
            Yii::app()->end();
        }
    }

    private function _generateRefno(){
        return time().rand(10,99);
    }
}
