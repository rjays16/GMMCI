<?php
/* @var $this AdjustmentController */
/* @var $dataProvider CActiveDataProvider */

$this->breadcrumbs=array(
	'Adjustments',
);

$this->menu=array(
	array('label'=>'Create Adjustment', 'url'=>array('create')),
	array('label'=>'Manage Adjustment', 'url'=>array('admin')),
);
?>

<h1>Adjustments</h1>

<?php $this->widget('zii.widgets.CListView', array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_view',
)); ?>
