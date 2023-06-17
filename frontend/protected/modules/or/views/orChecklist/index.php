<?php
$this->breadcrumbs=array(
	'OR Checklists',
	);

	
	$this->menu=array(
		array('label'=>'Create OR Checklist','url'=>array('create')),
		array('label'=>'Manage OR Checklist','url'=>array('admin')),
		);

?>

	<h1>OR Checklists</h1>

	<div class="well">

		<?php 

		$this->widget('bootstrap.widgets.TbListView',array(
			'dataProvider'=>$dataProvider,
			'itemView'=>'_view',
			)); 

			?> 

	</div>

