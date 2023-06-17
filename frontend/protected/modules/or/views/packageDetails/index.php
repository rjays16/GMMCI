<?php
$this->breadcrumbs=array(
	'Package Details',
);

$this->menu=array(
array('label'=>'Create PackageDetails','url'=>array('create')),
array('label'=>'Manage PackageDetails','url'=>array('admin')),
);
?>

<h1>Package Details</h1>

<?php $this->widget('bootstrap.widgets.TbListView',array(
'dataProvider'=>$dataProvider,
'itemView'=>'_view',
)); ?>
