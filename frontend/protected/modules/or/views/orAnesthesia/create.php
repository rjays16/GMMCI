<?php
$this->breadcrumbs=array(
	'Anesthesias'=>array('index'),
	'Create',
);

$this->menu=array(
array('label'=>'List Anesthesia','url'=>array('index')),
array('label'=>'Manage Anesthesia','url'=>array('admin')),
);
?>

<h1>Create Anesthesia</h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>