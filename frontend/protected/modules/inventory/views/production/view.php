<?php
/* @var $this ProductionController */
/* @var $model Production */
/* @var $items ProductionItem */
/* @var $cs CClientScript */

$baseUrl = Yii::app()->request->baseUrl;

$this->breadcrumbs = array(
	'Inventory'=>$baseUrl.'/modules/supply_office/seg-supply-functions.php',
	'List'=>array('production/admin'),
	$model->id,
);

$cs = Yii::app()->clientScript;
$cs->registerCss('style', 'body{padding:0;}');
$this->setPageTitle('Production');
$this->showFooter = false;

$this->widget('bootstrap.widgets.TbDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'id',
		array(
			'label' => 'Product',
			'value' => $model->packageName
		),
		array(
			'label' => 'Production Date',
			'value' => date('F d, Y h:i A',strtotime($model->production_date))
		),
		array(
			'label' => 'Expiry Date',
			'value' => date('F d, Y h:i A',strtotime($model->expiry_date))
		),
		array(
			'label' => 'Area',
			'value' => $model->areaInfo->area_name
		),
		'serial_no',
		'lot_no',
		'quantity',
		array(
			'label' => 'Unit Price',
			'value' => number_format($model->unit_price,2)
		),
	),
));

$this->widget('bootstrap.widgets.TbGridView',array(
	'dataProvider'=>$items->search(),
	'filter'=>$items,
	'columns'=>array(
		array(
			'name'=>'itemName',
			'type'=>'raw',
			'htmlOptions' => array(
				'width' => '30%'
			)
		),
		array(
			'name'=>'quantity',
			'type'=>'raw',
			'filter'=>false,
			'htmlOptions' => array(
				'width' => '30%'
			)
		),
		array(
			'name'=>'itemPrice',
			'value'=>'number_format($data->itemPrice,2)',
			'type'=>'raw',
			'filter'=>false,
			'htmlOptions' => array(
				'width' => '30%'
			)
		),
	)
));