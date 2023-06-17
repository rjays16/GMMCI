<?php $form=$this->beginWidget('bootstrap.widgets.TbActiveForm',array(
	'action'=>Yii::app()->createUrl($this->route),
	'method'=>'get',
)); ?>

		<?php echo $form->textAreaRow($model,'checklist_question',array('rows'=>6, 'cols'=>50, 'class'=>'span8')); ?>

		<?php echo $form->textFieldRow($model,'label_data',array('class'=>'span5','maxlength'=>50)); ?>

		<?php echo $form->textAreaRow($model,'history',array('rows'=>6, 'cols'=>50, 'class'=>'span8')); ?>

		<?php echo $form->textFieldRow($model,'date_created',array('class'=>'span5')); ?>

		<?php echo $form->textFieldRow($model,'date_modified',array('class'=>'span5')); ?>

		<?php echo $form->dropDownListRow($model,'type',array("request"=>"request","preop"=>"preop",),array('class'=>'input-large')); ?>

	<div class="form-actions">
		<?php $this->widget('bootstrap.widgets.TbButton', array(
			'buttonType' => 'submit',
			'type'=>'primary',
			'label'=>'Search',
		)); ?>
	</div>

<?php $this->endWidget(); ?>
