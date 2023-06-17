<?php
/* @var $this OrRequestController */
/* @var $model OrRequest */
/* @var $form CActiveForm */
?>

<div class="wide form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'action'=>Yii::app()->createUrl($this->route),
	'method'=>'get',
)); ?>

	<div class="row">
		<?php echo $form->label($model,'or_refno'); ?>
		<?php echo $form->textField($model,'or_refno',array('size'=>12,'maxlength'=>12)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'encounter_nr'); ?>
		<?php echo $form->textField($model,'encounter_nr',array('size'=>12,'maxlength'=>12)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'trans_type'); ?>
		<?php echo $form->textField($model,'trans_type'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'is_urgent'); ?>
		<?php echo $form->textField($model,'is_urgent'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'dept_nr'); ?>
		<?php echo $form->textField($model,'dept_nr',array('size'=>5,'maxlength'=>5)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'dr_nr'); ?>
		<?php echo $form->textField($model,'dr_nr',array('size'=>12,'maxlength'=>12)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'or_type'); ?>
		<?php echo $form->textField($model,'or_type',array('size'=>5,'maxlength'=>5)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'or_case'); ?>
		<?php echo $form->textField($model,'or_case',array('size'=>12,'maxlength'=>12)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'request_flag'); ?>
		<?php echo $form->textField($model,'request_flag',array('size'=>8,'maxlength'=>8)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'date_requested'); ?>
		<?php echo $form->textField($model,'date_requested'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'requirements'); ?>
		<?php echo $form->textArea($model,'requirements',array('rows'=>6, 'cols'=>50)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'create_id'); ?>
		<?php echo $form->textField($model,'create_id',array('size'=>20,'maxlength'=>20)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'create_date'); ?>
		<?php echo $form->textField($model,'create_date'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'modify_date'); ?>
		<?php echo $form->textField($model,'modify_date'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'modify_id'); ?>
		<?php echo $form->textField($model,'modify_id',array('size'=>20,'maxlength'=>20)); ?>
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