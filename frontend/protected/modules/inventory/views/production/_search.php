<?php
/* @var $this ProductionController */
/* @var $model Production */
/* @var $form CActiveForm */
?>

<div class="wide form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'action'=>Yii::app()->createUrl($this->route),
	'method'=>'get',
)); ?>

	<div class="row">
		<?php echo $form->label($model,'id'); ?>
		<?php echo $form->textField($model,'id',array('size'=>11,'maxlength'=>11)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'package_id'); ?>
		<?php echo $form->textField($model,'package_id',array('size'=>25,'maxlength'=>25)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'production_date'); ?>
		<?php echo $form->textField($model,'production_date'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'expiry_date'); ?>
		<?php echo $form->textField($model,'expiry_date'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'area'); ?>
		<?php echo $form->textField($model,'area',array('size'=>11,'maxlength'=>11)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'serial_no'); ?>
		<?php echo $form->textField($model,'serial_no',array('size'=>11,'maxlength'=>11)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'lot_no'); ?>
		<?php echo $form->textField($model,'lot_no',array('size'=>11,'maxlength'=>11)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'quantity'); ?>
		<?php echo $form->textField($model,'quantity'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'unit_price'); ?>
		<?php echo $form->textField($model,'unit_price'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'create_id'); ?>
		<?php echo $form->textField($model,'create_id',array('size'=>11,'maxlength'=>11)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'create_time'); ?>
		<?php echo $form->textField($model,'create_time'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'modify_id'); ?>
		<?php echo $form->textField($model,'modify_id',array('size'=>11,'maxlength'=>11)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'modify_time'); ?>
		<?php echo $form->textField($model,'modify_time'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'history'); ?>
		<?php echo $form->textArea($model,'history',array('rows'=>6, 'cols'=>50)); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton('Search'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- search-form -->