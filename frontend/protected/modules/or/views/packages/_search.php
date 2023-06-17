<?php
/* @var $this PackagesController */
/* @var $model Packages */
/* @var $form CActiveForm */
?>

<div class="wide form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'action'=>Yii::app()->createUrl($this->route),
	'method'=>'get',
)); ?>

	<div class="row">
		<?php echo $form->label($model,'package_id'); ?>
		<?php echo $form->textField($model,'package_id'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'package_name'); ?>
		<?php echo $form->textField($model,'package_name',array('size'=>60,'maxlength'=>100)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'package_price'); ?>
		<?php echo $form->textField($model,'package_price'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'is_surgical'); ?>
		<?php echo $form->textField($model,'is_surgical'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'pkg_phiccode'); ?>
		<?php echo $form->textField($model,'pkg_phiccode',array('size'=>10,'maxlength'=>10)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'is_zpackage'); ?>
		<?php echo $form->textField($model,'is_zpackage'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'create_id'); ?>
		<?php echo $form->textField($model,'create_id',array('size'=>60,'maxlength'=>60)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'modify_id'); ?>
		<?php echo $form->textField($model,'modify_id',array('size'=>60,'maxlength'=>60)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'create_time'); ?>
		<?php echo $form->textField($model,'create_time'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'modify_time'); ?>
		<?php echo $form->textField($model,'modify_time'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'history'); ?>
		<?php echo $form->textArea($model,'history',array('rows'=>6, 'cols'=>50)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'clinic_id'); ?>
		<?php echo $form->textField($model,'clinic_id'); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton('Search'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- search-form -->