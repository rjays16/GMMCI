<?php
$cs = Yii::app()->getClientScript();

    /* @var $this Controller */
    $this->setPageTitle('eClaims Dashboard');
    $this->setPageSubTitle('Welcome to the SegHIS eClaims Portal!');
    $this->setPageIcon('fa fa-dashboard');

?>
    <div class="row-fluid">
        <div class="span10">
            <div class="row-fluid">
                <div class="span12">

<?php
Yii::import('bootstrap.widgets.TbButton');
$this->beginWidget(
    'bootstrap.widgets.TbBox',
    array(
        'title' => 'News',
        'headerIcon' => 'icon-cog',
        'htmlOptions' => array('class' => ''),
        'headerButtons' => array(
            /*array(
                'class' => 'bootstrap.widgets.TbButtonGroup',
                'type' => 'primary',
                'size' => TbButton::SIZE_SMALL,
                // '', 'primary', 'info', 'success', 'warning', 'danger' or 'inverse'
                'buttons' => array(
                    array('label' => 'Action', 'url' => '#'),
                    // this makes it split :)
                    array(
                        'items' => array(
                            array('label' => 'Action', 'url' => '#'),
                            array('label' => 'Another action', 'url' => '#'),
                            array('label' => 'Something else', 'url' => '#'),
                            '---',
                            array('label' => 'Separate link', 'url' => '#'),
                        )
                    ),
                )
            ),
            array(
                'class' => 'booster.widgets.TbButtonGroup',
                'size' => TbButton::SIZE_SMALL,
                'buttons' => array(
                    array('label' => 'Left', 'url' => '#'),
                    array('label' => 'Middle', 'url' => '#'),
                    array('label' => 'Right', 'url' => '#')
                ),
        ), */
        ),
    )
);
?>

<p>
Philippine Health Insurance Corporation (PhilHealth) is committed to ensuring ease of availment at the point of care for all its beneficiaries.

<p>
As such, in 2011, the Corporation launched the eClaims Project through PhilHealth Circular No.14 and Office Order No. 69 which broadly aims to streamline key processes such as eligibility check, claims submission, verification and payment in order to serve both members and partner providers better. The eClaims Project had three phases, namely

<ul>
    <li>Phase I or Claims Eligibility Web Service (CEWS)</li>
    <li>Phase II or Electronic Claims Submission (ECS)</li>
    <li>Phase III or Claims Status Verification/Payment (CSV)</li>
</ul>

<?php $this->endWidget() ?>
                </div>
            </div>
            <div class="row-fluid">
                <div class="span4">
                    <?php $this->renderPartial('dashlets/transmittalUploads',array("transmittalStatuses" => $transmittalStatuses)) ?>
                </div>
                <div class="span4">
                    <?php $this->renderPartial('dashlets/transmittalStatus') ?>
                </div>
                <div class="span4">
                    <?php $this->renderPartial('dashlets/stackedBar') ?>
                </div>
            </div>
            <div class="row-fluid">
                <div class="span12">
                    <?php $this->renderPartial('dashlets/areaGraph') ?>
                </div>
            </div>
        </div>

        <!-- Main menu -->
        <div class="span2">
<?php

/**
* Added access permission from controller (MainController.php) to Quick Navigation| jeff 01-12-18
*/

// << - for testing purposes - >>
// echo $this->moduleAll;
// echo $this->moduleAllData;
// echo $this->module1Data;
// echo $this->module2Data;
// echo $this->module3Data;

$this->beginWidget(
    'bootstrap.widgets.TbBox',
    array(
        'title' => 'Quick Navigation',
        'headerIcon' => 'icon-cog',
        'htmlOptions' => array('class' => 'bootstrap-widget-table'),
    )
);

$this->widget(
    'bootstrap.widgets.TbMenu',
    array(
        'type' => 'list',
        'items' => array(
            array(
                'label' => 'CEWS',
                'itemOptions' => array('class' => 'nav-header eclaimsHeader')
            ),

            ($this->moduleAll || $this->moduleAllData || $this->APce ?
            array(
                'label' => 'Claims Eligibility',
                'url' => Yii::app()->createUrl('eclaims/eligibility/index'),

                'icon' => 'icon-star',
                'linkOptions' => array(
                    'class' => 'eclaimsLabel'
                ),
            ) : array(
                'label' => 'Claims Eligibility',
                'url' => '',
                'disabled' => true,
                'icon' => 'icon-star',
                'linkOptions' => array(
                    'class' => 'eclaimsLabel'
                ),
            )
            ),

            ($this->moduleAll || $this->moduleAllData || $this->APpv ?
            array('label' => 'Get Member PIN',
                'icon' => 'icon-star',
                'url' => Yii::app()->createUrl('eclaims/member/getPin'),
                'linkOptions' => array(
                    'class' => 'eclaimsLabel'
                )) : array('label' => 'Get Member PIN',
                'icon' => 'icon-star',
                'url' => '', 
                'disabled' => true,
                'linkOptions' => array(
                    'class' => 'eclaimsLabel'
                ))
            ),

            ($this->moduleAll || $this->moduleAllData || $this->APda ?
            array('label' => 'Doctor Accreditation', 'url' => Yii::app()->createUrl('eclaims/doctorAccreditation/index'),
                'icon' => 'icon-star',
                'linkOptions' => array(
                    'class' => 'eclaimsLabel'
                )) : array('label' => 'Doctor Accreditation', 'url' => '', 
                'disabled' => true,
                'icon' => 'icon-star',
                'linkOptions' => array(
                    'class' => 'eclaimsLabel'
                ))
            ),

            array(
                'label' => 'ECS',
                'itemOptions' => array('class' => 'nav-header eclaimsHeader')
            ),

            ($this->moduleAll || $this->moduleAllData || $this->APpt ?
            array('label' => 'Transmittal', 'url' => Yii::app()->createUrl('eclaims/transmittal/index'),
                'icon' => 'icon-star',
                'linkOptions' => array(
                    'class' => 'eclaimsLabel'
                )) : array('label' => 'Transmittal', 'url' => '', 
                'disabled' => true,
                'icon' => 'icon-star',
                'linkOptions' => array(
                    'class' => 'eclaimsLabel'
                ))
            ),
            array(
                'label' => 'CVS',
                'itemOptions' => array('class' => 'nav-header eclaimsHeader')
            ),

            ($this->moduleAll || $this->moduleAllData || $this->APcs ?
            array('label' => 'Check Claim Status', 'url' => Yii::app()->createUrl('eclaims/claimstatus/index'),
                'icon' => 'icon-star',
                'linkOptions' => array(
                    'class' => 'eclaimsLabel'
                )) : array('label' => 'Check Claim Status', 'url' => '',
                'disabled' => true,
                'icon' => 'icon-star',
                'linkOptions' => array(
                    'class' => 'eclaimsLabel'
                ))
            ),

            ($this->moduleAll || $this->moduleAllData || $this->APgv ?
            array('label' => 'Get Voucher', 'url' => Yii::app()->createUrl('eclaims/claimvoucher/index'),
                'icon' => 'icon-star',
                'linkOptions' => array(
                    'class' => 'eclaimsLabel'
                )) : array('label' => 'Get Voucher', 'url' => '',
                'disabled' => true,
                'icon' => 'icon-star',
                'linkOptions' => array(
                    'class' => 'eclaimsLabel'
                ))
            ),
        )
    )
);

$this->endWidget();
?>
        </div>
    </div>
