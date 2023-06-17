<?php
/* @var $this AdjustmentController */
/* @var $data Adjustment */
?>

<div class="view">

	<b><?php echo CHtml::encode($data->getAttributeLabel('refno')); ?>:</b>
	<?php echo CHtml::link(CHtml::encode($data->refno), array('view', 'id'=>$data->refno)); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('adjust_date')); ?>:</b>
	<?php echo CHtml::encode($data->adjust_date); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('adjusting_id')); ?>:</b>
	<?php echo CHtml::encode($data->adjusting_id); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('area_code')); ?>:</b>
	<?php echo CHtml::encode($data->area_code); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('remarks')); ?>:</b>
	<?php echo CHtml::encode($data->remarks); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('is_deleted')); ?>:</b>
	<?php echo CHtml::encode($data->is_deleted); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('history')); ?>:</b>
	<?php echo CHtml::encode($data->history); ?>
	<br />

	<?php /*
	<b><?php echo CHtml::encode($data->getAttributeLabel('modify_id')); ?>:</b>
	<?php echo CHtml::encode($data->modify_id); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('modify_dt')); ?>:</b>
	<?php echo CHtml::encode($data->modify_dt); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('create_id')); ?>:</b>
	<?php echo CHtml::encode($data->create_id); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('create_dt')); ?>:</b>
	<?php echo CHtml::encode($data->create_dt); ?>
	<br />

	*/ ?>

</div>