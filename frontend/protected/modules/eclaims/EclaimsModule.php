<?php
/**
 * EclaimsModule
 *
 * @author Alvin Quinones <ajmquinones@gmail,com>
 * @copyright Copyright &copy; 2014. Segworks Technologies Corporation
 */

/**
 * @property EntityModel[] $entityModels
 */
class EclaimsModule extends WebModule
{
    /**
     *
     * @var type
     */
    public $layout = 'eclaims.views.layouts.ec-main';
    /**
     * @var string $title
     */
    public $title = 'Eclaims module';

    public $defaultController = 'main';

    public function init()
    {
        $import = array(
            'eclaims.services.*',
        );
        $this->setImport($import);
    }

    /**
     *
     * @param Controller $controller
     * @param CAction $action
     */
    public function beforeControllerAction($controller, $action)
    {

        $controller->breadcrumbs['eClaims'] = array('main/index');
        Yii::app()->getClientScript()->registerScript('eclaims', <<<SCRIPT
SCRIPT
            , CClientScript::POS_END);

        $controller->menu['eclaims-config'] = array(
            'class' => 'bootstrap.widgets.TbMenu',
            'items' => array(
                array(
                    'label' => 'Eligibility',
                    'icon' => 'fa fa-check-circle-o',
                    'items' => array(

                        (
                            array('label' => 'PIN verification', 'icon' => 'fa fa-list-ol', 'url' => Yii::app()->createUrl('eclaims/member/getPin')) 
                        ),

                        (
                            array('label' => 'Check eligibility', 'icon' => 'fa fa-check-square-o', 'url' => Yii::app()->createUrl('eclaims/eligibility/index')) 
                        ),

                        (                            array('label' => 'Doctor accreditation', 'icon' => 'fa fa-user-md', 'url' => Yii::app()->createUrl('eclaims/doctorAccreditation/index')) 
                        ),
                    )
                ),
                array(
                    'label' => 'Submit Claims',
                    'icon' => 'fa fa-envelope',
                    'items' => array(

                        (
                            /*/modules/billing/bill-pass.php?ntid=false&lang=en&userck=&target=seg_billing_transmittal_eclaims*/
                            array('label' => 'Process Transmittal', 'icon' => 'fa fa-inbox',
                                'url' => Yii::app()->request->baseUrl . "/modules/billing/bill-pass.php?sid=$sid&target=seg_billing_transmittal",
                                'linkOptions' => array(
                                    'id' => 'process_t'
                                )) 
                        ),

                        (
                            array('label' => 'Transmit e-Claim', 'icon' => 'fa fa-send-o', 'url' => Yii::app()->createUrl('eclaims/transmittal/index')) 
                        ),
                    ),
                ),
                array(
                    'label' => 'Claims Status',
                    'icon' => 'fa fa-check-square',
                    'items' => array(

                        (
                            array('label' => 'Check status', 'icon' => 'fa fa-flag', 'url' => Yii::app()->createUrl('eclaims/claimStatus/index')) 
                        ),

                        (
                            array('label' => 'Get voucher', 'icon' => 'fa fa-envelope-o', 'url' => Yii::app()->createUrl('eclaims/claimVoucher/index')) 
                        ),
                    ),
                ),
                '---',
                array(
                    'label' => false,
                    'url' => array('config/update'),
                    'icon' => 'fa fa-cog',
                    'linkOptions' => array(
                        'id' => 'eclaims-config-button',
                        'data-toggle' => 'modal',
                        'data-target' => '#eclaims-config-modal'
                    )
                ),
                '---',
            ),
            'htmlOptions' => array(
                'id' => 'eclaims-config',
                'class' => 'pull-right',
            ),
        );

        return parent::beforeControllerAction($controller, $action);
    }

}

