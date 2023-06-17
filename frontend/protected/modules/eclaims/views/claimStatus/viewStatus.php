<?php

/**
 *
 * @author  Ma. Dulce O. Polinar  <dulcepolinar1010@gmail.com>
 * @copyright (c) 2014, Segworks Technologies Corporation
 *
 */
$cs = Yii::app()->getClientScript();
$cs->registerScript('_viewstatus-js', <<<JAVASCRIPT
    (function($) {
        $(document).ready(function() {
            $('#update-claim-status').on('click', function() {
                Alerts.loading({ content: 'Please wait. We are currently getting the updates of the claim through the PHIC web service!' });
            });
        });
    }(jQuery));
JAVASCRIPT
);

$this->setPageTitle('View Claim Status <i>' . $claim->person->getFullName() . '</i>');
$this->breadcrumbs[] = '' . $claim->person->getFullName();
if($_GET['current_page']){
$this->breadcrumbs["Claim Status"][0] = "claimStatus/index&user_page=".$_GET['current_page'];    
}


$claimId = $_GET["claim_id"];
$encounter_nr = $_GET["enc_nr"];

?>

<div class="row-fluid">
    <div class="span12">
        <?php
            $this->beginWidget(
                'bootstrap.widgets.TbBox',
                array(
                    'title' => 'Claim Status',
                    'headerIcon' => 'fa fa-check-square-o',
                    'htmlOptions' => array(
                    ),
                    'headerButtons' => array(
                        array(
                            'class' => 'bootstrap.widgets.TbButtonGroup',
                            'buttons' => array(
                                array(
                                    'label' => 'Update Claim Status',
                                    // 'buttonType' => 'button',
                                    'type' => 'primary',
                                    'icon' => 'fa fa-refresh',
                                    'url' => Yii::app()->createUrl("eclaims/claimStatus/viewstatus", array("claim_id" => $_GET["claim_id"], "enc_nr"=> $_GET["enc_nr"], "update_status"=>1)),
                                    'htmlOptions' => array(
                                        'id' => 'update-claim-status',
                                        // 'data-url' => Yii::app()->createUrl("eclaims/claimstatus/viewstatus", array("claim_id" => $_GET["claim_id"], "enc_nr"=> $_GET["enc_nr"], "update_status"=>1)),
                                        // 'onclick' => 'window.location.href = $(this).data(\'url\'); return false;'
                                    )
                                ),
                            ),
                        ),
                    )
                )
            );
        ?>

            <div class="row-fluid">
                <div class="span6">
                    <h5>Admission Details</h5>
                    <?php
                        $this->widget(
                            'bootstrap.widgets.TbDetailView',
                            array(
                                'data' => $claim->encounter,
                                'attributes' => array(
                                    array('name' => 'person.phicMember.insurance_nr', 'label' => 'Member PIN'),
                                    array('name' => 'person.name_last'),
                                    array('name' => 'person.name_first'),
                                    array('name' => 'person.name_middle'),
                                    array('name' => 'person.FullAddress'),
                                    array('name' => 'admissionDt', 'label' => 'Admission Date', 'type' => 'date'),
                                    array('name' => 'discharge_date', 'label' => 'Discharge Date', 'type' => 'date'),
                                ),
                                'type' => 'striped condensed bordered',
                            )
                        );
                    ?>
                </div>
                <div class="span6">
                <h5>Claim Details</h5>
                    <?php
                        $this->widget(
                            'bootstrap.widgets.TbDetailView',
                            array(
                                'data' => $claim,
                                'attributes' => array(
                                    array('name' => 'status.status'),
                                    array('name' => 'status.as_of', 'label' => 'Date of Inquiry', 'type' => 'datetime'),
                                    array('name' => 'claim_series_lhio', 'label' => 'Claim Series Number'),
                                    array('name' => 'status.claim_date_received', 'label' => 'Date Received'),
                                    array('name' => 'status.claim_date_refile', 'label' => 'Date Refile'),
                                ),
                                'type' => 'striped condensed bordered',
                            )
                        );
                    ?>
                </div>
            </div>

            <div class="row-fluid">
                <div class="span12">
                    <?php
                        if ($claim->status->status == ClaimStatus::STATUS_IN_PROCESS) {
                            $this->renderPartial('statuses/inProcess', array(
                                'claim' => $claim
                            ));
                        } elseif ($claim->status->status == ClaimStatus::STATUS_RETURN) {
                            $this->renderPartial('statuses/return', array(
                                'claim' => $claim
                            ));
                        } elseif ($claim->status->status == ClaimStatus::STATUS_DENIED) {
                            $this->renderPartial('statuses/denied', array(
                                'claim' => $claim
                            ));
                        } elseif ($claim->status->status == ClaimStatus::STATUS_WITH_VOUCHER ||
                                $claim->status->status == ClaimStatus::STATUS_VOUCHERING ||
                                $claim->status->status == ClaimStatus::STATUS_WITH_CHEQUE) {
                            $this->renderPartial('statuses/withPayment', array(
                                'claim' => $claim
                            ));
                        }
                    ?>
                </div>
            </div>
        <?php $this->endWidget(); ?>
    </div>
</div>