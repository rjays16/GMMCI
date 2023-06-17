<?php
$this->breadcrumbs=array(
	'OR Techniques'=>array('index'),
	$model->technique_id,
);

$this->menu=array(
//array('label'=>'List OR Technique','url'=>array('index')),
array('label'=>'Create OR Technique','url'=>array('create')),
array('label'=>'Update OR Technique','url'=>array('update','id'=>$model->technique_id)),
array('label'=>'Delete OR Technique','url'=>'#','linkOptions'=>array('submit'=>array('delete','id'=>$model->technique_id),'confirm'=>'Are you sure you want to delete this item?')),
array('label'=>'Manage OR Technique','url'=>array('admin')),
);
?>

<h1>View OR Technique ID #<?php echo $model->technique_id; ?></h1>

	<br/>

	<div class="well">
		<?php $this->widget('bootstrap.widgets.TbDetailView',array(
			'data'=>$model,
			'attributes'=>array(
				'technique_name',
				array(            
					'name'=>'technique_desc',
					'type'=>'raw',
					),
				),
				)); ?>
	</div>

