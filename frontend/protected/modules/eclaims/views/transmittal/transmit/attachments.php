<?php

/* @var $this Controller */
/* @var $transmittal EclaimsTransmittal */

Yii::import('bootstrap.widgets.TbButton');
$this->breadcrumbs[] = $transmittal->transmit_no;

$cs = Yii::app()->getClientScript();
$cs->registerScript('transmittal-attachments', <<<SCRIPT
//$('.view-attachments').click(function() {
//    $('#attachments-modal').modal();
//});
SCRIPT
, CClientScript::POS_READY);

?>

<?php

$this->widget('bootstrap.widgets.TbGridView', array(
    'type' => 'striped condensed bordered hover',
    'dataProvider' => $transmittal->searchTransmittalDetails(),
    'template' => "{items}\n{summary}\n{pager}",
    'columns' => array(
        array('name' => 'encounter.memberType.memcategory.memcategory_desc',
            'header' => 'Member Category',
            'type' => 'raw',
            'value' => '$data->encounter->encounterMemCategory->memcategory->memcategory_desc',
        ),
        array('name' => 'encounter.person.FullName',
            'header' => 'Patient',
            'type' => 'raw',
            'value' => 'mb_convert_case($data->encounter->person->getFullName(), MB_CASE_UPPER, "UTF-8") . "<br/><small><b>HRN:</b> ". $data->encounter->pid ."</small>"'
        ),
        array(
            'header' => 'Type',
            'type' => 'raw',
            'value' => '$data->encounter->type->name . "<br/><small><b>Case No:</b> " . $data->encounter_nr . "</small>"'
        ),
        array(
            'header' => 'Discharge date',
            'type' => 'raw',
            'value' => 'date("M d, Y h:i A", strtotime($data->encounter->discharge_date." ".$data->encounter->discharge_time))'


        ),
        array(
            'name' => 'Package',
            'type' => 'raw',
            'value' => function($data) {
                if(empty($data->billing->caseRatePackage))
                    return CHtml::tag('em', array('class' => 'muted'), 'None');
                
                $_packages = array();

                /* Check first item is a second rate,If true; swap. */
                if(!empty($data->billing->caseRate[0])) {
                    if($data->billing->caseRate[0]->rate_type == 2)
                        $data->billing->caseRate = array_reverse($data->billing->caseRate);
                }

                foreach($data->billing->caseRate as $caseRate) {
                    $_helpIcon = CHtml::tag('i', array(
                        'class' => 'fa fa-question-circle',
                        'data-title'  => $caseRate->package->description,
                        'data-toggle' => 'tooltip',
                    ), ' ');

                    $_rateType = 'First Case Rate';
                    if($caseRate->rate_type == 2)
                        $_rateType = 'Second Case Rate';

                    $_rateType = CHtml::tag('small', array(),
                        CHtml::tag('b', array('style' => 'display: block;'), $_rateType)
                    );

                    $_caseRateCode = CHtml::tag('small', array(), 
                        CHtml::tag('b', array(), 'Code: ') . $caseRate->package->code
                    );

                    $_packages[] = CHtml::tag('p', array(), 
                        $_rateType . $_caseRateCode . ' ' . $_helpIcon
                    );
                }

                return implode("\n", $_packages);
            }
        ),
        array(
            'header' => 'Eligibility',
            'type' => 'raw',
            'value' => function($data) {
                return empty($data->eligibility->eligibility) ? 'No' : $data->eligibility->eligibility;
            }
        ),
        array(
            'header' => 'Attachments',
            'type' => 'raw',
            'name' => 'AttachmentsSummary'
        ),
        array(
            /* Below are the button changes if returned or not. */
            'header'=>Yii::t('ses', 'Action'),
            'headerHtmlOptions'=> array('style' => 'text-align:center; width: 221px;'),
            'htmlOptions' =>array('style' => 'align:center; text-align: center;'),
            'value' => function($data) {

                $model = new EclaimsTransmittal();
                $getReturn = $model->getClaimStatusReturn($data->encounter_nr);

                Yii::app()->controller->widget('bootstrap.widgets.TbButtonGroup', array(
                    'size' => 'small',
                    'htmlOptions'=> array(
                        'class'=>'col-md-12',
                        'style' => 'text-align:center;'
                    ),
                    'buttons' => array(
                        'add' => array(          
                            'label' => (!$getReturn)?'Attachments...':'Returned...',
                            'linkOptions' => array('style' => 'text-align:left'),
                            'url' => Yii::app()->createUrl('eclaims/transmittal/manageAttachments', array(
                                'transmit_no' => $data->transmit_no,
                                'encounter_nr' =>$data->encounter_nr,
                                'ptname' => $data->encounter->person->getFullName(),
                                'pid' => $data->encounter->pid,
                            )),
                            'size' => TbButton::SIZE_MINI,
                            'type' => (!$getReturn) ? TbButton::TYPE_PRIMARY : TbButton::TYPE_DANGER,
                            'buttonType' => TbButton::BUTTON_LINK,
                            'htmlOptions' => array(
                            )
                        ),         
                    )
                ));

                Yii::app()->controller->widget('bootstrap.widgets.TbButtonGroup', array(
                    'size' => 'small',
                    'htmlOptions'=> array(
                        'class'=>'col-md-12',
                        'style' => 'text-align:center;'
                    ),
                    'buttons' => array(
                        'add' => array(          
                            'label' => 'CF4 Data Entry',
                            'url' => Yii::app()->createUrl('eclaims/generator/index', array(
                                'encounter_nr' => $data->encounter_nr,
                            )),
                            'size' => TbButton::SIZE_MINI,
                            'buttonType' => TbButton::BUTTON_LINK,
                            'htmlOptions' => array(
                            )
                        ),         
                    )
                ));
            },
        ),
    ),
));


?>


<?php
$this->beginWidget('bootstrap.widgets.TbModal', array(
    'id' => 'attachments-modal',
    'htmlOptions' => array(
        'style' => 'width: 1000px; margin-left:-500px;'
    ),
));
?>

<div class="modal-header">
    <a class="close" data-dismiss="modal"><i class="fa fa-times-circle"></i></a>
    <h5>Manage claim attachments</h5>
</div>

<div class="modal-body">
</div>

<div class="modal-footer">
    <?php
        $this->widget('bootstrap.widgets.TbButton', array(
            'label' => 'View transmittal',
            'type' => 'primary',
            'htmlOptions' => array('data-dismiss' => 'modal'),
        ));

        $this->widget('bootstrap.widgets.TbButton', array(
            'label' => 'Close',
            'type' => 'inverse',
            'htmlOptions' => array('data-dismiss' => 'modal'),
        ));
    ?>
</div>

<?php $this->endWidget(); ?>
