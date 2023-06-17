<?php
/* @var $this OrRequestController */
/* @var $model OrRequest */

$this->breadcrumbs=array(
	'Or Requests'=>array('index'),
	'Create',
);

$this->menu=array(
	array('label'=>'List OrRequest', 'url'=>array('index')),
	array('label'=>'Manage OrRequest', 'url'=>array('admin')),
);
?>

<h1>Create OrRequest</h1>

<?php $this->renderPartial('_form',
    array(
        'model'=>$model,
        'departmentsArray'=>$departmentsArray,
        'orTypesArray'=>$orTypesArray,
        'checkListArray'=>$checkListArray,
    )
);
?>