<?php
$this->breadcrumbs=array(
	'OR Techniques'=>array('index'),
	'Manage',
);

$this->menu=array(
//array('label'=>'List OR Techniques','url'=>array('admin')),
array('label'=>'Create OR Technique','url'=>array('create')),
);

Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
$('.search-form').toggle();
return false;
});
$('.search-form form').submit(function(){
$.fn.yiiGridView.update('seg-or-technique-grid', {
data: $(this).serialize()
});
return false;
});
");
?>

<h1>Manage OR Techniques</h1>

<p>
	You may optionally enter a comparison operator (<b>&lt;</b>, <b>&lt;=</b>, <b>&gt;</b>, <b>&gt;=</b>, <b>
		&lt;&gt;</b>
	or <b>=</b>) at the beginning of each of your search values to specify how the comparison should be done.
</p>


<div class="well">
	
	<?php $this->widget('bootstrap.widgets.TbGridView',array(
		'id'=>'seg-or-technique-grid',
		'dataProvider'=>$model->search(),
		'filter'=>$model,
		'columns'=>array(
			'technique_name',
			array(            
				'name'=>'technique_desc',
				'type'=>'raw',
				),
			array(
				'class'=>'bootstrap.widgets.TbButtonColumn',
				),
			),
		)); 

	?>

</div>

