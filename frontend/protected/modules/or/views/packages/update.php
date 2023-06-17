<?php
/* @var $this PackagesController */
/* @var $model Packages */

$this->breadcrumbs=array(
	'Packages'=>array('index'),
	$model->package_id=>array('view','id'=>$model->package_id),
	'Update',
);

$this->menu=array(
	array('label'=>'List Packages', 'url'=>array('index')),
	array('label'=>'Create Packages', 'url'=>array('create')),
	array('label'=>'View Packages', 'url'=>array('view', 'id'=>$model->package_id)),
	array('label'=>'Manage Packages', 'url'=>array('admin')),
);
?>

<h1>Update Packages <?php echo $model->package_id; ?></h1>

<?php $this->renderPartial('_form', array('model'=>$model,'packageDetail'=>$packageDetail)); ?>