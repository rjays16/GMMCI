<?php
/* @var $this MembershipController */
/* @var $model MembershipForm */
/* @var $form TbActiveForm */

/* @var $relationOptions MemberRelation[] */
/* @var $countryOptions AddressCountry[] */

ob_start();
echo $form->hiddenField($model, 'pid');
echo $form->hiddenField($model, 'encounterNr');
echo $form->hiddenField($model, 'hcareId');
echo $form->checkBoxRow($model, 'isMember');
echo $form->maskedTextFieldRow($model, 'pin', array(
    'mask' => '99-999999999-9'
));
echo $form->dropDownListRow($model, 'relation', array_merge(array('' => '- Select Relation -'),array(
    'B' => 'Sibling',
    'C' => 'Child',
    'P' => 'Parent',
    'S' => 'Spouse',
)), array(
    'placeholder' => false
));
echo $form->textFieldRow($model, 'nameLast');
echo $form->textFieldRow($model, 'nameFirst');
echo $form->textFieldRow($model, 'nameMiddle');
echo $form->textFieldRow($model, 'nameExtension');
$col1 = ob_get_clean();


ob_start();
echo $form->dropDownListRow($model, 'sex', array(
    'm' => 'Male',
    'f' => 'Female'
), array('placeholder' => false));
echo $form->dropDownListRow($model, 'civilStatus',array(
    'single' => 'Single',
    'married' => 'Married',
    'separated' => 'Separated',
    'widowed' => 'Widowed',
),array('placeholder' => false));
echo CHtml::tag('div', array(
    'id' => 'married-female-box',
));
echo $form->textFieldRow($model, 'maidenNameLast');
echo $form->textFieldRow($model, 'maidenNameFirst');
echo $form->textFieldRow($model, 'maidenNameMiddle');
echo $form->textFieldRow($model, 'maidenNameExtension');
echo CHtml::closeTag('div');
echo $form->dropDownListRow($model, 'nationality',CHtml::listData($countryOptions,'citizenship','citizenship'),array(
    'placeholder' => false,
));
echo $form->datePickerRow($model, 'birthDate', array('options' => array('autoclose' => true)));
echo $form->textFieldRow($model, 'birthPlace');
$col2 = ob_get_clean();


ob_start();
echo $form->textFieldRow($model, 'floor');
echo $form->textFieldRow($model, 'buildingName');
echo $form->textFieldRow($model, 'lotNo');
echo $form->textFieldRow($model, 'street');
echo $form->textFieldRow($model, 'subdivision');
echo $form->textFieldRow($model, 'barangay');
$col3 = ob_get_clean();


ob_start();
echo $form->textFieldRow($model, 'municipality');
echo $form->textFieldRow($model, 'province');
echo $form->dropDownListRow($model, 'country',CHtml::listData($countryOptions,'country_name','country_name'),array(
    'placeholder' => false
));
echo $form->textFieldRow($model, 'zipCode');
echo $form->textFieldRow($model, 'telNo');
echo $form->textFieldRow($model, 'mobileNo');
echo $form->emailFieldRow($model, 'email');
$col4 = ob_get_clean();





$headerButtons = array();

if (!$model->pmrf) {
    $headerButtons[] = array(
        'class' => 'bootstrap.widgets.TbButton',
        'label' => 'Create PMRF', 'type' => 'primary',
        'icon' => 'fa fa-pencil',
        'toggle' => true,
        'id' => 'btn-toggle-pmrf',
        'htmlOptions' => array(
            'data-content' => 'pmrf-box',
            'data-flag' => 'MembershipForm_pmrfForm',
            'class' => $model->pmrfForm ? 'active' : ''
        )
    );
}

if (!$model->cf1) {
    $headerButtons[] = array(
        'class' => 'bootstrap.widgets.TbButton',
        'label' => 'Create CF1', 'type' => 'primary',
        'icon' => 'fa fa-pencil',
        'toggle' => true,
        'id' => 'btn-toggle-cf1',
        'htmlOptions' => array(
            'data-content' => 'cf1-box',
            'data-flag' => 'MembershipForm_cf1Form',
            'class' => $model->cf1Form ? 'active' : ''
        )
    );
}

$this->beginWidget('bootstrap.widgets.TbBox', array(
    'title' => 'Member Information',
    'headerButtons' => $headerButtons
));

echo CHtml::tag('div', array('class' => 'span3'), $col1, true);
echo CHtml::tag('div', array('class' => 'span3'), $col2, true);
echo CHtml::tag('div', array('class' => 'span3'), $col3, true);
echo CHtml::tag('div', array('class' => 'span3'), $col4, true);

$this->endWidget();//TbBox