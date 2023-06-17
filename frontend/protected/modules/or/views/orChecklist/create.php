<?php
$this->breadcrumbs=array(
	'OR Checklists'=>array('index'),
	'Create',
);

$this->menu=array(
//array('label'=>'List OR Checklist','url'=>array('index')),
array('label'=>'Manage OR Checklist','url'=>array('admin')),
);
?>

<h1>Create OR Checklist</h1>


<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>