<div class="view">

		<b><?php echo CHtml::encode($data->getAttributeLabel('anesth_id')); ?>:</b>
	<?php echo CHtml::link(CHtml::encode($data->anesth_id),array('view','id'=>$data->anesth_id)); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('anest_name')); ?>:</b>
	<?php echo CHtml::encode($data->anest_name); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('anest_category')); ?>:</b>
	<?php echo CHtml::encode($data->anest_category); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('description')); ?>:</b>
	<?php echo CHtml::encode($data->description); ?>
	<br />


</div>