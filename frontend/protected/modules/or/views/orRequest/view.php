<?php
/* @var $this OrRequestController */
/* @var $model OrRequest */

$this->breadcrumbs=array(
	'Or Requests'=>array('index'),
	$model->or_refno,
);

$this->menu=array(
	array('label'=>'List OrRequest', 'url'=>array('index')),
	array('label'=>'Create OrRequest', 'url'=>array('create')),
	array('label'=>'Update OrRequest', 'url'=>array('update', 'id'=>$model->or_refno)),
	array('label'=>'Delete OrRequest', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->or_refno),'confirm'=>'Are you sure you want to delete this item?')),
	array('label'=>'Manage OrRequest', 'url'=>array('admin')),
);
?>

<h1>View OrRequest #<?php echo $model->or_refno; ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
    'data'=>$model,
	'attributes'=>array(
		'or_refno',
		'encounter_nr',
		'trans_type',
		'is_urgent',
		'dept_nr',
		'dr_nr',
		'or_type',
		'or_case',
		'package_id',
		'package_amount',
		'request_flag',
		'date_requested',
		'requirements',
		'create_id',
		'create_date',
		'modify_date',
		'modify_id',
		'history',
	),
)); ?>
