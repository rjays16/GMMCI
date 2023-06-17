<?php
$this->breadcrumbs=array(
	'Anesthesias'=>array('index'),
	'Manage',
);

$this->menu=array(
array('label'=>'List Anesthesia','url'=>array('index')),
array('label'=>'Create Anesthesia','url'=>array('create')),
);

Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
$('.search-form').toggle();
return false;
});
$('.search-form form').submit(function(){
$.fn.yiiGridView.update('anesthesia-grid', {
data: $(this).serialize()
});
return false;
});
");
?>

<h1>Manage Anesthesias</h1>

<?php $this->widget('bootstrap.widgets.TbGridView',array(
'id'=>'anesthesia-grid',
'dataProvider'=>$model->search(),
'filter'=>$model,
'columns'=>array(
		'anest_name',
		'anest_category',
		'description',
array(
'header' => 'Actions',
'class'=>'bootstrap.widgets.TbButtonColumn',
),
),
)); ?>

<?php echo CHtml::link('Advanced Search','#',array('class'=>'search-button btn')); ?>
<div class="search-form" style="display:none">
	<?php $this->renderPartial('_search',array(
	'model'=>$model,
)); ?>
</div><!-- search-form -->
