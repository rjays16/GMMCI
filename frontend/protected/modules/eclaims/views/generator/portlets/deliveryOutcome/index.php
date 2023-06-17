<?php
use SegHEIRS\components\web\ClientScript;
use SegHEIRS\components\web\Controller;



\Yii::import('bootstrap.widgets.TbActiveForm');
$baseUrl = Yii::app()->baseUrl;
$cs = Yii::app()->getClientScript();
$cs->registerScriptFile($baseUrl . '/js/jquery/ui/jquery-ui.js');

Yii::app()->clientScript->registerScriptFile('js/jquery/ui/jquery.livequery.min.js');

$form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
    'id' => 'dt-delivery-outcome',
    'type' => \TbActiveForm::TYPE_VERTICAL,
    'htmlOptions' => array(
        'data-url-save-dt-delivery-outcome' =>  $this->createUrl('DTDeliveryOutcome/saveDTDeliveryOutcome'),
        'data-url-save-dt-discharge-outcome' =>  $this->createUrl('DTDischargeOutcome/saveDTDischargeOutcome'),
        'data-url-save-pregnancy-uterine' =>  $this->createUrl('pregnancyUterine/savePregnancyUterine'),
        'data-url-save-birth-outcome' =>  $this->createUrl('birthOutcome/saveBirthOutcome'),
        'data-url-delete-birth-outcome' =>  $this->createUrl('birthOutcome/deleteBirthOutcome'),
        'data-url-save-spf-delivery-outcome' =>  $this->createUrl('SPFDeliveryOutcome/saveSPFDeliveryOutcome'),
        'data-encounter_nr' =>  $encounter->encounter_nr,
        'data-pid' =>  $model['person']->pid,

    ),
    // 'action' => $this->createUrl('medicalHistory.newSaveMedHistory', array(
    //     // 'encounter_no' => $model_latestEncounter['encounter_no'],
    //     // 'spin' => $model_latestEncounter['spin'],
    // ))
));

?>

<div class="row-fluid">
    <div class="span12">

        <?php
        $tabs = array(
            array(
                'label' => 'Date and Time of Delivery',
                'content'     => $this->renderPartial(
                    'portlets/deliveryOutcome/view/dt_of_delivery',
                    array(
                        'encounter' => $encounter,
                        'form' => $form,
                        'dtDeliveryOutcome' => $dtDeliveryOutcome,
                    ),
                    true
                ),
                'active' => true,
            ),
            array(
                'label' => 'Pregnancy Uterine',
                'content'     => $this->renderPartial(
                    'portlets/deliveryOutcome/view/pregnancy_uterine',
                    array(
                        'encounter' => $encounter,
                        'form' => $form,
                        'maternalOutcome' => $maternalOutcome,
                        'pregnancyUterine' => $pregnancyUterine,
                    ),
                    true
                ),
            ),
            array(
                'label' => 'Birth Outcome',
                'content'     => $this->renderPartial(
                    'portlets/deliveryOutcome/view/birth_outcome',
                    array(
                        'encounter' => $encounter,
                        'form' => $form,
                        'birthoutcome' => $birthoutcome,
                        'birthoutcomes' => $birthoutcomes,
                        'genderList' => $genderList,
                    ),
                    true
                ),
            ),

            array(
                'label' => 'Scheduled Postpartum follow-up',
                'content'     => $this->renderPartial(
                    'portlets/deliveryOutcome/view/followup_scheduled',
                    array(
                        'encounter' => $encounter,
                        'form' => $form,
                        'spfdeliveryoutcome' => $spfdeliveryoutcome
                    ),
                    true
                ),
            ),
            array(
                'label' => 'Date and Time of Discharge',
                'content'     => $this->renderPartial(
                    'portlets/deliveryOutcome/view/dt_of_discharge',
                    array(
                        'encounter' => $encounter,
                        'form' => $form,
                        'dtDischargeOutcome' => $dtDischargeOutcome,
                    ),
                    true
                ),
            ),




        );

        $this->widget('bootstrap.widgets.TbTabs', array(
            'encodeLabel' => false,
            'tabs' => $tabs
        ));

        ?>

    </div>
</div>

<?php
$this->endWidget();

?>

<script>

</script>