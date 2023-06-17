<?php
/* @var $this AdjustmentController */
/* @var $model Adjustment */
/* @var $cs CClientScript */
/* @var $areas Area[] */
/* @var $units \SegHis\models\inventory\Unit[] */

$baseUrl = Yii::app()->request->baseUrl;

$this->breadcrumbs=array(
	'Inventory' => $baseUrl.'/modules/supply_office/seg-supply-functions.php',
    'Adjustments' => array('inventory/adjustment/admin'),
	'Quick Adjustment',
);

$cs = Yii::app()->clientScript;
$this->setPageTitle('<b>Quick</b> Adjustment');
$this->showFooter = false;

$this->renderPartial('quick/_form', array(
    'model' => $model,
    'areas' => $areas,
    'reasons' => $reasons,
    'units' => $units
));