<?php
$this->breadcrumbs=array(
	'OR Techniques',
	);

$this->menu=array(
	array('label'=>'Create OR Technique','url'=>array('create')),
	array('label'=>'Manage OR Technique','url'=>array('admin')),
	);

	?>

	<h1>OR Techniques</h1>

	<div class="well">
		<?php 

		$this->widget('bootstrap.widgets.TbListView',
			array(
				'dataProvider'=>$dataProvider,
				'itemView'=>'_view',
				)

			); 


			?>
	</div>
