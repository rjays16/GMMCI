<?php
/* @var $this MembershipController */
/* @var $model MembershipForm */
/* @var $form TbActiveForm */
/* @var $pmrfMemberCategoryOptions PmrfMemberCategory[] */

ob_start();
echo $form->hiddenField($model,'pmrfForm');
echo $form->dropDownListRow($model,'pmrfPurpose',array(
    'enrollment' => 'For Enrollment',
    'update' => 'For Update'
),array('placeholder' => false));

$categories = array('' => '- Select Category -');
$categories = CMap::mergeArray($categories,CHtml::listData($pmrfMemberCategoryOptions,'id','name'));

echo $form->dropDownListRow($model,'pmrfMembershipCategory',$categories,array(
    'placeholder' => false
));
$col1 = ob_get_clean();

ob_start();
echo $form->textFieldRow($model,'pmrfMembershipOther');
echo $form->textFieldRow($model,'pmrfMembershipIncome',array('class' => 'money'));
$col2 = ob_get_clean();

ob_start();
echo $form->datePickerRow($model,'pmrfMembershipEffectiveDate',array('options' => array('autoclose' => true)));
echo $form->textFieldRow($model,'pmrfTin');
$col3 = ob_get_clean();

$headerButtons = array();
if ($model->pmrf) {
    $headerButtons[] = array(
        'class' => 'bootstrap.widgets.TbButton',
        'label' => 'Print', 'type' => 'primary',
        'icon' => 'fa fa-print',
        'id' => 'print-pmrf'
    );
}

$this->beginWidget('bootstrap.widgets.TbBox',array(
    'title' => 'PMRF',
    'headerButtons' => $headerButtons,
    'htmlOptions' => array(
        'id' => 'pmrf-box',
        'style' => $model->pmrfForm ? 'display:block' : 'display:none'
    )
));

echo CHtml::tag('div',array('class' => 'clearfix'));
echo CHtml::tag('div',array('class' => 'span3'),$col1,true);
echo CHtml::tag('div',array('class' => 'span3'),$col2,true);
echo CHtml::tag('div',array('class' => 'span3'),$col3,true);
echo CHtml::closeTag('div');

$this->renderPartial('_pmrfDependents',array(
    'model' => $model,
    'form' => $form
));

$this->endWidget();//TbBox