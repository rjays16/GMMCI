<?php

/* @var $this MembershipController */
/* @var $model MembershipForm */
/* @var $cs CClientScript */
/* @var $form TbActiveForm */

/* @var $relationOptions MemberRelation[] */
/* @var $countryOptions AddressCountry[] */
/* @var $pmrfMemberCategoryOptions PmrfMemberCategory[] */
/* @var $personInfo [] */

$cs = Yii::app()->clientScript;

$this->setPageTitle('Insurance Membership');
$this->showFooter = false;
$cs->registerCss('headCss', 'body{padding:0;}');
$baseUrl = Yii::app()->baseUrl;
$cs->registerScriptFile($baseUrl . '/js/jquery/jquery.numberformatter.js');
$cs->registerScriptFile($baseUrl . '/modules/billing_new/js/membership.js');
$cs->registerCssFile($baseUrl . '/modules/billing_new/css/membership.css');
$personInfoJson = CJSON::encode($personInfo);
$headJs = <<<JS

var personInfo = {$personInfoJson};
var baseUrl = '{$baseUrl}';
JS;

$cs->registerScript('headJs', $headJs, CClientScript::POS_HEAD);

$form = $this->beginWidget('phic.widgets.PhicActiveForm', array(
    'id' => 'membership-form',
    'type' => 'vertical',
//    'enableAjaxValidation' => true
));

?>

<div class="alert alert-block alert-error" id="membership-form_es_" style="display: none;">
    <p>Please fix the following input errors:</p>
    <ul></ul>
</div>

<?php
//echo $form->errorSummary($model);

$this->renderPartial('_basic', array(
    'model' => $model,
    'form' => $form,
    'relationOptions' => $relationOptions,
    'countryOptions' => $countryOptions,
));

$this->renderPartial('_cf1', array(
    'model' => $model,
    'form' => $form,
    'relationOptions' => $relationOptions
));

$this->renderPartial('_pmrf', array(
    'model' => $model,
    'form' => $form,
    'pmrfMemberCategoryOptions' => $pmrfMemberCategoryOptions
));

$this->widget('bootstrap.widgets.TbButton', array(
    'label' => 'Save',
    'type' => 'success',
    'buttonType' => 'button',
    'size' => 'large',
    'id' => 'btn-submit'
));

$this->endWidget();//TbActiveForm

$this->renderPartial('_pmrfDependentMustache',array(
    'cs' => $cs
));