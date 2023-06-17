<?php
$this->breadcrumbs=array(
	'Package Details'=>array('index'),
	'Create',
);

$this->menu=array(
array('label'=>'List PackageDetails','url'=>array('index')),
array('label'=>'Manage PackageDetails','url'=>array('admin')),
);
?>

<h1>Create PackageDetails</h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>