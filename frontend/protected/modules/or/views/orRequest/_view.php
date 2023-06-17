<?php
/* @var $this OrRequestController */
/* @var $data OrRequest */
?>

<div class="view">

	<b><?php echo CHtml::encode($data->getAttributeLabel('or_refno')); ?>:</b>
	<?php echo CHtml::link(CHtml::encode($data->or_refno), array('view', 'id'=>$data->or_refno)); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('encounter_nr')); ?>:</b>
	<?php echo CHtml::encode($data->encounter_nr); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('trans_type')); ?>:</b>
	<?php echo CHtml::encode($data->trans_type); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('is_urgent')); ?>:</b>
	<?php echo CHtml::encode($data->is_urgent); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('dept_nr')); ?>:</b>
	<?php echo CHtml::encode($data->dept_nr); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('dr_nr')); ?>:</b>
	<?php echo CHtml::encode($data->dr_nr); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('or_type')); ?>:</b>
	<?php echo CHtml::encode($data->or_type); ?>
	<br />

	<?php /*
	<b><?php echo CHtml::encode($data->getAttributeLabel('or_case')); ?>:</b>
	<?php echo CHtml::encode($data->or_case); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('package_id')); ?>:</b>
	<?php echo CHtml::encode($data->package_id); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('package_amount')); ?>:</b>
	<?php echo CHtml::encode($data->package_amount); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('request_flag')); ?>:</b>
	<?php echo CHtml::encode($data->request_flag); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('date_requested')); ?>:</b>
	<?php echo CHtml::encode($data->date_requested); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('requirements')); ?>:</b>
	<?php echo CHtml::encode($data->requirements); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('create_id')); ?>:</b>
	<?php echo CHtml::encode($data->create_id); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('create_date')); ?>:</b>
	<?php echo CHtml::encode($data->create_date); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('modify_date')); ?>:</b>
	<?php echo CHtml::encode($data->modify_date); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('modify_id')); ?>:</b>
	<?php echo CHtml::encode($data->modify_id); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('history')); ?>:</b>
	<?php echo CHtml::encode($data->history); ?>
	<br />

	*/ ?>

</div>