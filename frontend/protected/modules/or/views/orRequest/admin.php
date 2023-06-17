<?php
/* @var $this OrRequestController */
/* @var $model OrRequest */

$this->breadcrumbs=array(
	'Or Requests'=>array('index'),
	'Manage',
);

$this->menu=array(
	array('label'=>'List OrRequest', 'url'=>array('index')),
	array('label'=>'Create OrRequest', 'url'=>array('create')),
);

Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
	$('.search-form').toggle();
	return false;
});
$('.search-form form').submit(function(){
	$('#or-request-grid').yiiGridView('update', {
		data: $(this).serialize()
	});
	return false;
});
");
?>

<h1>Manage Or Requests</h1>

<?php 
	// echo CHtml::link('Advanced Search','#',array('class'=>'search-button')); 
?>
<div class="search-form" style="display:none">
<?php $this->renderPartial('_search',array(
	'model'=>$model,
)); ?>
</div><!-- search-form -->

<?php $this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'or-request-grid',
	'dataProvider'=>$model->search(),
	'filter'=>$model,
	'columns'=>array(
		'or_refno',
		'encounter_nr',
		array(
			'header' => 'Transaction Type',
			// 'name' => 'department.name_formal',
			'value' => '$data->trans_type?"Charge":"Cash"'
		),
		array(
			'header' => 'Is Urgent',
			// 'name' => 'department.name_formal',
			'value' => '$data->is_urgent?"True":"False"'
		),
		array(
			'header' => 'Department',
			'name' => 'department.name_formal',
		),
		array(
			'class'=>'CButtonColumn',
		),
	),
)); ?>
