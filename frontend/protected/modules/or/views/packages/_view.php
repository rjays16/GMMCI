<?php
/* @var $this PackagesController */
/* @var $data Packages */
?>

<div class="view">

	<b><?php echo CHtml::encode($data->getAttributeLabel('package_id')); ?>:</b>
	<?php echo CHtml::link(CHtml::encode($data->package_id), array('view', 'id'=>$data->package_id)); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('package_name')); ?>:</b>
	<?php echo CHtml::encode($data->package_name); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('package_price')); ?>:</b>
	<?php echo CHtml::encode($data->package_price); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('is_surgical')); ?>:</b>
	<?php echo CHtml::encode($data->is_surgical); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('pkg_phiccode')); ?>:</b>
	<?php echo CHtml::encode($data->pkg_phiccode); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('is_zpackage')); ?>:</b>
	<?php echo CHtml::encode($data->is_zpackage); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('create_id')); ?>:</b>
	<?php echo CHtml::encode($data->create_id); ?>
	<br />

	<?php /*
	<b><?php echo CHtml::encode($data->getAttributeLabel('modify_id')); ?>:</b>
	<?php echo CHtml::encode($data->modify_id); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('create_time')); ?>:</b>
	<?php echo CHtml::encode($data->create_time); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('modify_time')); ?>:</b>
	<?php echo CHtml::encode($data->modify_time); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('history')); ?>:</b>
	<?php echo CHtml::encode($data->history); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('clinic_id')); ?>:</b>
	<?php echo CHtml::encode($data->clinic_id); ?>
	<br />

	*/ ?>

</div>