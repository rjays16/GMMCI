<?php
$this->breadcrumbs=array(
	'Package Details'=>array('index'),
	$model->item_id=>array('view','id'=>$model->item_id),
	'Update',
);

	$this->menu=array(
	array('label'=>'List PackageDetails','url'=>array('index')),
	array('label'=>'Create PackageDetails','url'=>array('create')),
	array('label'=>'View PackageDetails','url'=>array('view','id'=>$model->item_id)),
	array('label'=>'Manage PackageDetails','url'=>array('admin')),
	);
	?>

	<h1>Update PackageDetails <?php echo $model->item_id; ?></h1>

<?php echo $this->renderPartial('_form',array('model'=>$model)); ?>