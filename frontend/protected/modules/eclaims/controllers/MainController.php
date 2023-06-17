<?php

# Added by JEFF
// require_once($root_path . 'include/care_api_classes/class_acl.php');

/**
 *
 * MainController.php
 *
 * @author Alvin Quinones <ajmquinones@gmail.com>
 * @copyright (c) 2005-2014, Segworks Technologies Corporation
 */

/**
 * Description of MainController
 *
 * @package
 */

Yii::import('eclaims.models.*');

class MainController extends Controller {

    /**
    * Added by JEFF 01-12-1
    * For access permission of modules for users passed to view (view/main/index.php) Quick Navigation.
    */ 
    public $moduleAll;
    public $moduleAllData;
    public $module1Data;
    public $module2Data;
    public $module3Data;

    public $APpv;
    public $APce;
    public $APda;

    public $APpt;
    public $APte;

    public $APcs;
    public $APgv;


    public function init(){

    require_once($root_path . 'include/care_api_classes/class_acl.php');
    $objAcl = new Acl($_SESSION['sess_temp_userid']);

    $canManageAll = $objAcl->checkPermissionRaw('_a_0_all');
    $canManagemoduleAll = $objAcl->checkPermissionRaw('_a_1_eclaims_sudomanage');

    $canManagemodule1 = $objAcl->checkPermissionRaw('_a_2_eclaims_module1_sudomanage');
        $canPinVerify = $objAcl->checkPermissionRaw('_a_3_eclaims_module1_member_sudomanage');
        $canEligibility = $objAcl->checkPermissionRaw('_a_3_eclaims_module1_eligibility_sudomanage');
        $canDoctorAccreditation = $objAcl->checkPermissionRaw('_a_3_eclaims_module1_doctorAccreditation_sudomanage');

    $canManagemodule2 = $objAcl->checkPermissionRaw('_a_2_eclaims_module2_sudomanage');
        $canTransmittal = $objAcl->checkPermissionRaw('_a_3_eclaims_module2_transmittal_sudomanage');

    $canManagemodule3 = $objAcl->checkPermissionRaw('_a_2_eclaims_module3_sudomanage');
        $canCheckStatus = $objAcl->checkPermissionRaw('_a_3_eclaims_module3_claimStatus_sudomanage');
        $canGetVoucher = $objAcl->checkPermissionRaw('_a_3_eclaims_module3_voucherDetails_sudomanage');

    # Additional access permission for billing.
    $canBilling = $objAcl->checkPermissionRaw('_a_1_billmanage');
        $canTransmittalBilling = $objAcl->checkPermissionRaw('_a_1_billtransmittal');

        # -- Parent and Child Access Permission Algorithm -- #

        # Module 1 - check
        $m1typex = !$canPinVerify && !$canEligibility && !$canDoctorAccreditation && $canManagemodule1; // xxx
        $m1typey = $canPinVerify && $canEligibility && $canDoctorAccreditation && $canManagemodule1;    // 111

        $m1type1 = $canPinVerify && !$canEligibility && !$canDoctorAccreditation && $canManagemodule1;  // 1xx
        $m1type2 = !$canPinVerify && $canEligibility && !$canDoctorAccreditation && $canManagemodule1;  // x1x
        $m1type3 = !$canPinVerify && !$canEligibility && $canDoctorAccreditation && $canManagemodule1;  // xx1

        $m1type4 = $canPinVerify && $canEligibility && !$canDoctorAccreditation && $canManagemodule1;   // 11x
        $m1type5 = !$canPinVerify && $canEligibility && $canDoctorAccreditation && $canManagemodule1;   // x11
        $m1type6 = $canPinVerify && !$canEligibility && $canDoctorAccreditation && $canManagemodule1;   // 1x1

        $stype1 = $m1typey || $m1type1 || $m1type4 || $m1type6 && $canManagemodule1; // all c1
        $stype2 = $m1typey || $m1type2 || $m1type4 || $m1type5 && $canManagemodule1; // all c2
        $stype3 = $m1typey || $m1type3 || $m1type5 || $m1type6 && $canManagemodule1; // all c3

        # Module 1 - uncheck
        $Xm1typex = !$canPinVerify && !$canEligibility && !$canDoctorAccreditation && !$canManagemodule1; // xxx
        $Xm1typey = $canPinVerify && $canEligibility && $canDoctorAccreditation && !$canManagemodule1;    // 111

        $Xm1type1 = $canPinVerify && !$canEligibility && !$canDoctorAccreditation && !$canManagemodule1;  // 1xx
        $Xm1type2 = !$canPinVerify && $canEligibility && !$canDoctorAccreditation && !$canManagemodule1;  // x1x
        $Xm1type3 = !$canPinVerify && !$canEligibility && $canDoctorAccreditation && !$canManagemodule1;  // xx1

        $Xm1type4 = $canPinVerify && $canEligibility && !$canDoctorAccreditation && !$canManagemodule1;   // 11x
        $Xm1type5 = !$canPinVerify && $canEligibility && $canDoctorAccreditation && !$canManagemodule1;   // x11
        $Xm1type6 = $canPinVerify && !$canEligibility && $canDoctorAccreditation && !$canManagemodule1;   // 1x1

        $Xstype1 = $m1typey || $m1type1 || $m1type4 || $m1type6 && !$canManagemodule1; // all c1
        $Xstype2 = $m1typey || $m1type2 || $m1type4 || $m1type5 && !$canManagemodule1; // all c2
        $Xstype3 = $m1typey || $m1type3 || $m1type5 || $m1type6 && !$canManagemodule1; // all c3

        # Merge all qualified access permission for pin verification
        $APpinVerification  = $m1typex || $m1typey || $stype1 || $Xm1typey || $Xstype1 || $Xm1type1;
        $this->APpv = $APpinVerification;

        # Merge all qualified access permission for check eligibility
        $APcheckEligibility  = $m1typex || $m1typey || $stype2 || $Xm1typey || $Xstype2 || $Xm1type2;
        $this->APce = $APcheckEligibility;

        # Merge all qualified access permission for doctor accreditation
        $APdoctorAccreditation  = $m1typex || $m1typey || $stype3 || $Xm1typey || $Xstype3 || $Xm1type3;
        $this->APda = $APdoctorAccreditation;
        

        # Module 2 - check
        $m2typex = !$canTransmittal && !$canTransmitEclaims && $canManagemodule1;  // xx
        $m2typey = $canTransmittal && $canTransmitEclaims && $canManagemodule1;    // 11

        $m2type1 = $canTransmittal && !$canTransmitEclaims && $canManagemodule1;  // 1x
        $m2type2 = !$canTransmittal && $canTransmitEclaims && $canManagemodule1;  // x1

        $stype1M2 = $m2typey || $m2type1 && $canManagemodule1; // all c1
        $stype2M2 = $m2typey || $m2type2 && $canManagemodule1; // all c2

        # Module 2 - uncheck
        $Xm2typex = !$canTransmittal && !$canTransmitEclaims && !$canManagemodule1;  // xx
        $Xm2typey = $canTransmittal && $canTransmitEclaims && !$canManagemodule1;    // 11

        $Xm2type1 = $canTransmittal && !$canTransmitEclaims && !$canManagemodule1;  // 1x
        $Xm2type2 = !$canTransmittal && $canTransmitEclaims && !$canManagemodule1;  // x1

        $Xstype1M2 = $m2typey || $m2type1 && !$canManagemodule1; // all c1
        $Xstype2M2 = $m2typey || $m2type2 && !$canManagemodule1; // all c2

        # Merge all qualified access permission for process transmittal
        $APprocessTransaction = $m2typex || $m2typey || $stype1M2 || $Xm2typey || $Xstype1M2 || $Xm2type1 || $canBilling || $canTransmittalBilling;
        $this->APpt = $APprocessTransaction;

        # Merge all qualified access permission for transmit eclaims
        $APtransmitEclaims = $m2typex || $m2typey || $stype2M2 || $Xm2typey || $Xstype2M2 || $Xm2type2;
        $this->APte = $APtransmitEclaims;

        # Module 3 - check
        $m3typex = !$canCheckStatus && !$canGetVoucher && $canManagemodule1;  // xx
        $m3typey = $canCheckStatus && $canGetVoucher && $canManagemodule1;    // 11

        $m3type1 = $canCheckStatus && !$canGetVoucher && $canManagemodule1;  // 1x
        $m3type2 = !$canCheckStatus && $canGetVoucher && $canManagemodule1;  // x1

        $stype1M3 = $m3typey || $m3type1 && $canManagemodule1; // all c1
        $stype2M3 = $m3typey || $m3type2 && $canManagemodule1; // all c2

        # Module 3 - uncheck
        $Xm3typex = !$canCheckStatus && !$canGetVoucher && !$canManagemodule1;  // xx
        $Xm3typey = $canCheckStatus && $canGetVoucher && !$canManagemodule1;    // 11

        $Xm3type1 = $canCheckStatus && !$canGetVoucher && !$canManagemodule1;  // 1x
        $Xm3type2 = !$canCheckStatus && $canGetVoucher && !$canManagemodule1;  // x1

        $Xstype1M3 = $m3typey || $m3type1 && !$canManagemodule1; // all c1
        $Xstype2M3 = $m3typey || $m3type2 && !$canManagemodule1; // all c2

        # Merge all qualified access permission for check status
        $APcheckStatus = $m3typex || $m3typey || $stype1M3 || $Xm3typey || $Xstype1M3 || $Xm3type1;
        $this->APcs = $APcheckStatus;

        # Merge all qualified access permission for get voucher
        $APgetVoucher = $m3typex || $m3typey || $stype2M3 || $Xm3typey || $Xstype2M3 || $Xm3type2;
        $this->APgv = $APgetVoucher;

        # -- End of P.C.A.P. Algorithm -- #

        # Pass values to views from controller.
        $this->moduleAll = $canManageAll;
        $this->moduleAllData = $canManagemoduleAll;

        $this->module1Data = $canManagemodule1;
        $this->module2Data = $canManagemodule2;
        $this->module3Data = $canManagemodule3;

    }

    /**
     *
     * @return type
     */
    public function filters() {
        return array(
            'accessControl',
            array('bootstrap.filters.BootstrapFilter'),
        );
    }

    /**
     *
     */
    public function accessRules() {
        return array(
            array(
                'deny',
                'actions' => array('index'),
                'users' => array('?')
            ),
            array(
                'deny',
                'actions' => array('index'),
                'expression' => '!Yii::app()->user->checkPermission("eclaims")',
            ),
            array(
                'allow',
                'actions' => array('index'),
                'users' => array('@')
            ),
        );
    }

    /**
     *
     * @param type $action
     */
    public function beforeAction($action) {
        $this->breadcrumbs[] = 'Home';
        return parent::beforeAction($action);
    }

    /**
     *
     */
    public function actionIndex() {
        Yii::import('eclaims.models.EclaimsTransmittal');
        Yii::import('eclaims.models.Claim');
        $status = \EclaimsTransmittal::countTransmittalsByStatus();
        $this->render('index', array(
            'transmittalStatuses' => $status));
    }

}