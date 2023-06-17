<?php
/* @var $this PackagesController */
/* @var $model Packages */

$this->breadcrumbs=array(
	'Packages'=>array('index'),
	'Manage',
);

$this->menu=array(
	array('label'=>'List Packages', 'url'=>array('index')),
	array('label'=>'Create Packages', 'url'=>array('create')),
);

Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
	$('.search-form').toggle();
	return false;
});
$('.search-form form').submit(function(){
	$('#packages-grid').yiiGridView('update', {
		data: $(this).serialize()
	});
	return false;
});
");
?>

<h1>Manage Packages</h1>

<p>
You may optionally enter a comparison operator (<b>&lt;</b>, <b>&lt;=</b>, <b>&gt;</b>, <b>&gt;=</b>, <b>&lt;&gt;</b>
or <b>=</b>) at the beginning of each of your search values to specify how the comparison should be done.
</p>

<?php echo CHtml::link('Advanced Search','#',array('class'=>'search-button')); ?>
<div class="search-form" style="display:none">
<?php $this->renderPartial('_search',array(
	'model'=>$model,
)); ?>
</div><!-- search-form -->

<?php $this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'packages-grid',
	'dataProvider'=>$model->search(),
	'filter'=>$model,
	'columns'=>array(
		'package_id',
		'package_name',
		'package_price',
		'is_surgical',
		'pkg_phiccode',
		'is_zpackage',
		/*
		'create_id',
		'modify_id',
		'create_time',
		'modify_time',
		'history',
		'clinic_id',
		*/
		array(
			'class'=>'CButtonColumn',
		),
	),
)); ?>
