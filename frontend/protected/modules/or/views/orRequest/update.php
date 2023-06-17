<?php
/* @var $this OrRequestController */
/* @var $model OrRequest */

$this->breadcrumbs=array(
	'Or Requests'=>array('index'),
	$model->or_refno=>array('view','id'=>$model->or_refno),
	'Update',
);

$this->menu=array(
	array('label'=>'List OrRequest', 'url'=>array('index')),
	array('label'=>'Create OrRequest', 'url'=>array('create')),
	array('label'=>'View OrRequest', 'url'=>array('view', 'id'=>$model->or_refno)),
	array('label'=>'Manage OrRequest', 'url'=>array('admin')),
);
?>

<h1>Update OrRequest <?php echo $model->or_refno; ?></h1>

<?php $this->renderPartial('_form', array('model'=>$model,'departmentsArray'=>$departmentsArray,
        'orTypesArray'=>$orTypesArray,
        'checkListArray'=>$checkListArray,)); ?>