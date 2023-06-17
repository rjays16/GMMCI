<?php
/**
 *
 * @author  Ma. Dulce O. Polinar  <dulcepolinar1010@gmail.com>
 * @copyright (c) 2014, Segworks Technologies Corporation (http://www.segworks.com)
 *
 */

Yii::import('eclaims.models.Claim');
Yii::import('eclaims.models.ClaimVoucher');
Yii::import('eclaims.models.VoucherDetailsForm');

class ClaimVoucherController extends Controller
{

    public function filters()
    {
        return array('accessControl',
            array('bootstrap.filters.BootstrapFilter')
        );
	}

    /**
     *
     * @return type
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
     * Renders the Get Voucher's main page
     *
     */
	public function actionIndex()
	{
		$model = new VoucherDetailsForm;
        if (isset($_POST['VoucherDetailsForm'])) {
            $model->attributes = $_POST['VoucherDetailsForm'];
            if(!empty($model->voucherNo) ){
	            $voucher = ClaimVoucher::model()->findByAttributes(array('voucher_no'=>$model->voucherNo));
	            if (empty($voucher)) {
			        Yii::import('eclaims.services.ServiceExecutor');
			        $service = new ServiceExecutor(
			           array(
			               'endpoint' => 'hie/voucher/details',
			               'params' => array(
                               'pVoucherNo' => $model->voucherNo
                            )
			           )
			        );
			        try {
			            $result = $service->execute();
			            if ($result['success']) {
			                if ($voucher->extractResult($result['data'])) {
                                // Success
                            } else {
                                // Error
                            }
			            }
			        } catch (ServiceCallException $e) {
			            Yii::app()->user->setFlash('error', '<strong>Web service error:</strong> ' . $e->getMessage());
			        }
	            } else {
                    $status=true;
                }
        	}
        }

		$this->render('index', array(
            'model'=>$model,
            'voucher' => $voucher
        ));
	}

    /**
     *
     */
    public function actionDetails(){
        $this->layout = false;
        $voucherNo = $_GET['voucher'];
        $claimId = $_GET['claim'];
        if (isset($_GET['is_summary'])) {
            $this->renderPartial('../claimstatus/voucherSummary', array('voucherNo'=>$voucherNo), false, false);
        }
        else{
            $this->renderPartial('../claimstatus/voucherDetails', array('voucherNo'=>$voucherNo, 'claimId'=>$claimId), false, false);
        }
    }

}