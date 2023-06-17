<?php
/* @var $this ProductionController */
/* @var $data Production */
?>

<div class="view">

	<b><?php echo CHtml::encode($data->getAttributeLabel('id')); ?>:</b>
	<?php echo CHtml::link(CHtml::encode($data->id), array('view', 'id'=>$data->id)); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('package_id')); ?>:</b>
	<?php echo CHtml::encode($data->package_id); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('production_date')); ?>:</b>
	<?php echo CHtml::encode($data->production_date); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('expiry_date')); ?>:</b>
	<?php echo CHtml::encode($data->expiry_date); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('area')); ?>:</b>
	<?php echo CHtml::encode($data->area); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('serial_no')); ?>:</b>
	<?php echo CHtml::encode($data->serial_no); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('lot_no')); ?>:</b>
	<?php echo CHtml::encode($data->lot_no); ?>
	<br />
</div>