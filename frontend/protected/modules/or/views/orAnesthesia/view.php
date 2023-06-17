<?php
$this->breadcrumbs=array(
	'Anesthesias'=>array('index'),
	$model->anesth_id,
);

$this->menu=array(
array('label'=>'List Anesthesia','url'=>array('index')),
array('label'=>'Create Anesthesia','url'=>array('create')),
array('label'=>'Update Anesthesia','url'=>array('update','id'=>$model->anesth_id)),
array('label'=>'Delete Anesthesia','url'=>'#','linkOptions'=>array('submit'=>array('delete','id'=>$model->anesth_id),'confirm'=>'Are you sure you want to delete this item?')),
array('label'=>'Manage Anesthesia','url'=>array('admin')),
);
?>

<h1>View Anesthesia #<?php echo $model->anesth_id; ?></h1>

<?php $this->widget('bootstrap.widgets.TbDetailView',array(
'data'=>$model,
'attributes'=>array(
		'anesth_id',
		'anest_name',
		'anest_category',
		'description',
),
));
 ?>


