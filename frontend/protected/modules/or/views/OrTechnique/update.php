<?php
$this->breadcrumbs=array(
	'OR Techniques'=>array('index'),
	$model->technique_id=>array('view','id'=>$model->technique_id),
	'Update',
);

	$this->menu=array(
	//array('label'=>'List OR Technique','url'=>array('index')),
	array('label'=>'Create OR Technique','url'=>array('create')),
	array('label'=>'View OR Technique','url'=>array('view','id'=>$model->technique_id)),
	array('label'=>'Manage OR Technique','url'=>array('admin')),
	);
	?>

	<h1>Update OR Techniques <?php echo $model->technique_id; ?></h1>

<?php echo $this->renderPartial('_form',array('model'=>$model)); ?>