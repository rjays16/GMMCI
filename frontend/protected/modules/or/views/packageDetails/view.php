<?php
$this->breadcrumbs=array(
	'Package Details'=>array('index'),
	$model->item_id,
);

$this->menu=array(
array('label'=>'List PackageDetails','url'=>array('index')),
array('label'=>'Create PackageDetails','url'=>array('create')),
array('label'=>'Update PackageDetails','url'=>array('update','id'=>$model->item_id)),
array('label'=>'Delete PackageDetails','url'=>'#','linkOptions'=>array('submit'=>array('delete','id'=>$model->item_id),'confirm'=>'Are you sure you want to delete this item?')),
array('label'=>'Manage PackageDetails','url'=>array('admin')),
);
?>

<h1>View PackageDetails #<?php echo $model->item_id; ?></h1>

<?php $this->widget('bootstrap.widgets.TbDetailView',array(
'data'=>$model,
'attributes'=>array(
		'item_id',
		'package_id',
		'item_purpose',
		'quantity',
		'price',
		'remarks',
		'area',
		'item_type',
		'unit',
),
)); ?>
