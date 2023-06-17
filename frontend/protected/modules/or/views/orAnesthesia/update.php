<?php
$this->breadcrumbs=array(
	'Anesthesias'=>array('index'),
	$model->anesth_id=>array('view','id'=>$model->anesth_id),
	'Update',
);

	$this->menu=array(
	array('label'=>'List Anesthesia','url'=>array('index')),
	array('label'=>'Create Anesthesia','url'=>array('create')),
	array('label'=>'View Anesthesia','url'=>array('view','id'=>$model->anesth_id)),
	array('label'=>'Manage Anesthesia','url'=>array('admin')),
	);
	?>

	<h1>Update Anesthesia <?php echo $model->anesth_id; ?></h1>

<?php echo $this->renderPartial('_form_update',array('model'=>$model)); ?>