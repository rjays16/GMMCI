<?php $form=$this->beginWidget('bootstrap.widgets.TbActiveForm',array(
	'id'=>'package-details-form',
	'enableAjaxValidation'=>false,
)); ?>

<p class="help-block">Fields with <span class="required">*</span> are required.</p>

<?php echo $form->errorSummary($model); ?>

	<?php // echo $form->textFieldRow($model,'item_id',array('class'=>'span5')); ?>

	<?php echo $form->dropDownListRow($model,'package_id', 
	CHtml::listData(Packages::model()->findAll(), 'package_id', 'package_name'), 
	array('class' => 'span5','maxlength'=>258)); ?>

	<?php echo $form->dropDownListRow($model,'item_purpose',array("PH"=>"PH","LB"=>"LB","RD"=>"RD","MISC"=>"MISC",),array('class'=>'input-large')); ?>

	<?php echo $form->textFieldRow($model,'quantity',array('class'=>'span5')); ?>

	<?php echo $form->textFieldRow($model,'price',array('class'=>'span5')); ?>

	<?php echo $form->textAreaRow($model,'remarks',array('class'=>'span5','maxlength'=>100)); ?>

	<?php echo $form->textFieldRow($model,'area',array('class'=>'span5','maxlength'=>10)); ?>

	<?php echo $form->textFieldRow($model,'item_type',array('class'=>'span5','maxlength'=>10)); ?>

	<?php echo $form->textFieldRow($model,'unit',array('class'=>'span5','maxlength'=>30)); ?>

<div class="form-actions">
	<?php $this->widget('bootstrap.widgets.TbButton', array(
			'buttonType'=>'submit',
			'type'=>'primary',
			'label'=>$model->isNewRecord ? 'Create' : 'Save',
		)); ?>
</div>

<?php $this->endWidget(); ?>
