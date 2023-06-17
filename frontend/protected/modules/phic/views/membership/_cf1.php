<?php
/* @var $this MembershipController */
/* @var $model MembershipForm */
/* @var $form TbActiveForm */

/* @var $relationOptions MemberRelation[] */

ob_start();
echo $form->hiddenField($model,'cf1Form',array(
    'value' => $model->cf1Form ? 1 : 0
));
//echo $form->checkBoxRow($model,'cf1Signatory_is_representative');
echo $form->dropDownListRow($model,'cf1Signatory_is_representative',array(
    0 => 'Member',
    1 => 'Representative'
),array('placeholder' => false));
echo $form->textFieldRow($model,'cf1Signatory_name');
//if(!strtotime($model->cf1Signed_date)) {
//    $model->cf1Signed_date = date('m/d/Y');
//}

echo $form->datePickerRow($model,'cf1Signed_date',array('options' => array('autoclose' => true)));
$col1 = ob_get_clean();

ob_start();
echo $form->maskedTextFieldRow($model,'cf1Pin',array('mask' => '99-999999999-9'));
echo $form->dropDownListRow($model,'cf1Signatory_relation',CHtml::listData($relationOptions, 'relation_code', 'relation_desc'), array(
    'placeholder' => false
));
echo $form->textFieldRow($model,'cf1Other_relation');
$col2 = ob_get_clean();

ob_start();
echo $form->checkBoxRow($model,'cf1Is_incapacitated');
echo $form->textFieldRow($model,'cf1Reason');
$col3 = ob_get_clean();


ob_start();
echo Chtml::tag('div', array('class' => 'span3'));
echo $form->maskedTextFieldRow($model,'cf1EmployerPen',array('mask' => '99-999999999-9'));
echo $form->textFieldRow($model,'cf1ContactNo');
echo CHtml::closeTag('div');

echo Chtml::tag('div', array('class' => 'span3'));
echo $form->textFieldRow($model,'cf1BusinessName');
echo $form->textFieldRow($model,'cf1EmployerName');
echo CHtml::closeTag('div');

echo Chtml::tag('div', array('class' => 'span3'));
echo $form->textFieldRow($model,'cf1OfficialCapacity');
//if(!strtotime($model->cf1DateSigned)) {
//    $model->cf1DateSigned = date('m/d/Y');
//}
echo $form->datePickerRow($model,'cf1DateSigned',array('options' => array('autoclose' => true)));
echo CHtml::closeTag('div');
$part3 = ob_get_clean();



$headerButtons = array();
if ($model->cf1) {
    $headerButtons[] = array(
        'class' => 'bootstrap.widgets.TbButton',
        'label' => 'Print', 'type' => 'primary',
        'icon' => 'fa fa-print',
        'id' => 'print-cf1'
    );
}
$this->beginWidget('bootstrap.widgets.TbBox',array(
    'title' => 'CF1',
    'headerButtons' => $headerButtons,
    'htmlOptions' => array(
        'id' => 'cf1-box',
        'style' => $model->cf1Form ? 'display:block' : 'display:none'
    )
));

echo CHtml::tag('div',array('class' => 'clearfix'));
echo CHtml::tag('div',array('class' => 'span3'),$col1,true);
echo CHtml::tag('div',array('class' => 'span3'),$col2,true);
echo CHtml::tag('div',array('class' => 'span3'),$col3,true);
echo CHtml::closeTag('div');

echo '<hr/>';

echo CHtml::tag('h4',array(),'Part IV',true);
echo $part3;

$this->endWidget();//TbBox