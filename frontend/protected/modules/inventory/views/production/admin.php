<?php
/* @var $this ProductionController */
/* @var $model Production */
/* @var $cs CClientScript */

$baseUrl = Yii::app()->request->baseUrl;

$this->breadcrumbs = array(
	'Inventory'=>$baseUrl.'/modules/supply_office/seg-supply-functions.php',
	'Production',
);

$cs = Yii::app()->clientScript;
$cs->registerCss('style', 'body{padding:0;}');
$this->setPageTitle('Production');
$this->showFooter = false;

$this->widget('bootstrap.widgets.TbGridView', array(
	'dataProvider'=>$model->search(),
	'filter'=>$model,
	'afterAjaxUpdate'=>"function(){jQuery('.date-picker').datepicker({dateFormat:'dd/mm/yy',autoclose:true})}",
	'columns'=>array(
		array(
			'name'=>'packageName',
			'type'=>'raw',
			'htmlOptions' => array(
				'width' => '30%'
			)
		),
		array(
			'name'=>'production_date',
			'value' => 'date("F d, Y h:i A",strtotime($data->production_date))',
			'type'=>'raw',
			'filter' => $this->widget('bootstrap.widgets.TbDatePicker',array(
				'model' => $model,
				'attribute' => 'production_date',
				'htmlOptions' => array('class' => 'date-picker'),
				'options' => array('autoclose' => true)
			),true),
			'htmlOptions' => array(
				'width' => '15%'
			)
		),
		array(
			'name'=>'expiry_date',
			'value' => 'date("F d, Y h:i A",strtotime($data->expiry_date))',
			'type'=>'raw',
			'filter' => $this->widget('bootstrap.widgets.TbDatePicker',array(
				'model' => $model,
				'attribute' => 'expiry_date',
				'htmlOptions' => array('class' => 'date-picker'),
				'options' => array('autoclose' => true)
			),true),
			'htmlOptions' => array(
				'width' => '15%'
			)
		),
		array(
			'name'=>'areaName',
			'type'=>'raw',
			'filter' => CHtml::listData(ProductionController::getAreas(),'area_name','area_name')
		),
		array(
			'name'=>'serial_no',
			'type'=>'raw',
			'htmlOptions' => array(
				'width' => '8%'
			)
		),
		array(
			'name'=>'isPostedToInventory',
			'type'=>'raw',
			'filter' => array('yes' => 'Yes', 'no' => 'No'),
			'htmlOptions' => array(
				'width' => '15%'
			)
		),
		array(
			'class'=>'bootstrap.widgets.TbButtonColumn',
			'template' => '{view}&nbsp;&nbsp;&nbsp;{update}',
			'buttons' => array(
				'view' => array('icon' => 'fa fa-eye'),
				'update' => array(
					'icon' => 'fa fa-pencil',
					'visible' => '$data->is_posted?false:true'
				),
			),
			'htmlOptions' => array(
				'width' => '6%'
			)
		)
	),
));
