<?php
$this->breadcrumbs=array(
	'OR Techniques'=>array('index'),
	'Create',
);

$this->menu=array(
//array('label'=>'List OR Technique','url'=>array('index')),
array('label'=>'Manage OR Technique','url'=>array('admin')),
);
?>

<h1>Create OR Techniques</h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>