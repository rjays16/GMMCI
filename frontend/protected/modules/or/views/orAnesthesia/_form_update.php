<?php $form=$this->beginWidget('bootstrap.widgets.TbActiveForm',array(
	'id'=>'anesthesia-form',
	'enableAjaxValidation'=>false,
)); ?>

<p class="help-block">Fields with <span class="required">*</span> are required.</p>

<?php echo $form->errorSummary($model); ?>

	<?php echo $form->textFieldRow($model,'anesth_id',array('class'=>'span5','maxlength'=>12, 'readonly' => true)); ?>

	<?php echo $form->textFieldRow($model,'anest_name',array('class'=>'span5','maxlength'=>50)); ?>

	<?php echo $form->dropDownListRow($model,'anest_category',array("General"=>"General","Regional"=>"Regional","Local"=>"Local","Conscious"=>"Conscious",),array('class'=>'input-large')); ?>

	<?php echo $form->textAreaRow($model,'description',array('rows'=>6, 'cols'=>50, 'class'=>'span8')); ?>

<div class="form-actions">
	<?php $this->widget('bootstrap.widgets.TbButton', array(
			'buttonType'=>'submit',
			'type'=>'primary',
			'label'=>$model->isNewRecord ? 'Create' : 'Save',
		)); ?>
</div>

<?php $this->endWidget(); ?>
