<?php
/* @var $this AdjustmentController */
/* @var $model Adjustment */
/* @var $form CActiveForm */
?>

<div class="wide form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'action'=>Yii::app()->createUrl($this->route),
	'method'=>'get',
)); ?>

	<div class="row">
		<?php echo $form->label($model,'refno'); ?>
		<?php echo $form->textField($model,'refno',array('size'=>12,'maxlength'=>12)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'adjust_date'); ?>
		<?php echo $form->textField($model,'adjust_date'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'adjusting_id'); ?>
		<?php echo $form->textField($model,'adjusting_id'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'area_code'); ?>
		<?php echo $form->textField($model,'area_code',array('size'=>10,'maxlength'=>10)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'remarks'); ?>
		<?php echo $form->textArea($model,'remarks',array('rows'=>6, 'cols'=>50)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'is_deleted'); ?>
		<?php echo $form->textField($model,'is_deleted'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'history'); ?>
		<?php echo $form->textArea($model,'history',array('rows'=>6, 'cols'=>50)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'modify_id'); ?>
		<?php echo $form->textField($model,'modify_id',array('size'=>35,'maxlength'=>35)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'modify_dt'); ?>
		<?php echo $form->textField($model,'modify_dt'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'create_id'); ?>
		<?php echo $form->textField($model,'create_id',array('size'=>35,'maxlength'=>35)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'create_dt'); ?>
		<?php echo $form->textField($model,'create_dt'); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton('Search'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- search-form -->