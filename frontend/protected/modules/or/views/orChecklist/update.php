<?php
$this->breadcrumbs=array(
	'OR Checklists'=>array('index'),
	$model->checklist_id=>array('view','id'=>$model->checklist_id),
	'Update',
);

	$this->menu=array(
	//array('label'=>'List OR Checklist','url'=>array('index')),
	array('label'=>'Create OR Checklist','url'=>array('create')),
	array('label'=>'View OR Checklist','url'=>array('view','id'=>$model->checklist_id)),
	array('label'=>'Manage OR Checklist','url'=>array('admin')),
	);
	?>

	<h1>Update OR Checklist <?php echo $model->checklist_id; ?></h1>

<?php echo $this->renderPartial('_form',array('model'=>$model)); ?>