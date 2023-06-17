<?php
$this->breadcrumbs=array(
	'Anesthesias',
);

$this->menu=array(
array('label'=>'Create Anesthesia','url'=>array('create')),
array('label'=>'Manage Anesthesia','url'=>array('admin')),
);
?>

<h1>Anesthesias</h1>

<?php $this->widget('bootstrap.widgets.TbGridView',array(
    'dataProvider' => $dataProvider,
    'type' => 'striped',
    'template' => "{items}",
    'columns' => array(
    	'anest_name', 
    	'description', 
    	'anest_category',
    	),
)); ?>


