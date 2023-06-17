<?php
$this->breadcrumbs=array(
	'OR Checklists'=>array('index'),
	'Manage',
	);

$this->menu=array(
	//array('label'=>'List OR Checklist','url'=>array('admin')),
	array('label'=>'Create OR Checklist','url'=>array('create')),
	);

Yii::app()->clientScript->registerScript('search', "
	$('.search-button').click(function(){
		$('.search-form').toggle();
		return false;
	});
$('.search-form form').submit(function(){
	$.fn.yiiGridView.update('seg-or-checklist-grid', {
		data: $(this).serialize()
	});
return false;
});
");
?>

<h1>Manage OR Checklists</h1>

<p>
	You may optionally enter a comparison operator (<b>&lt;</b>, <b>&lt;=</b>, <b>&gt;</b>, <b>&gt;=</b>, <b>
	&lt;&gt;</b>
	or <b>=</b>) at the beginning of each of your search values to specify how the comparison should be done.
</p>

	<?php 

	/*echo CHtml::link('Advanced Search','#',array('class'=>'search-button btn btn-success')); ?>
	<div class="search-form" style="display:none">
		<?php $this->renderPartial('_search',array(
			'model'=>$model,
			));*/ 

	?>

	<?php 

		//changes the date format for user viewing

		//var_dump($model->date_created);

/*		$datetime = strtotime($model->date_created);
		$model->date_created = date("F j, Y, g:i a", $datetime);

		$datetime = strtotime($model->date_modified);
		$model->date_modified = date("F j, Y, g:i a", $datetime);*/


		?>

		<br />
		<br />


		<div class="well">
			
			<?php $this->widget('bootstrap.widgets.TbGridView',array(
				'id'=>'seg-or-checklist-grid',
				'dataProvider'=>$model->search(),
				'filter'=>$model,
				'columns'=>array(
					array(            
						'name'=>'checklist_question',
						'type'=>'raw',
						),
					'label_data',
				'type',
				array(
					'class'=>'bootstrap.widgets.TbButtonColumn',
					),
				),
				)); ?>

		</div>
