<?php $form=$this->beginWidget('bootstrap.widgets.TbActiveForm',array(
	'action'=>Yii::app()->createUrl($this->route),
	'method'=>'get',
)); ?>

		<?php echo $form->textFieldRow($model,'item_id',array('class'=>'span5')); ?>

		<?php echo $form->textFieldRow($model,'package_id',array('class'=>'span5')); ?>

		<?php echo $form->dropDownListRow($model,'item_purpose',array("PH"=>"PH","LB"=>"LB","RD"=>"RD","MISC"=>"MISC",),array('class'=>'input-large')); ?>

		<?php echo $form->textFieldRow($model,'quantity',array('class'=>'span5')); ?>

		<?php echo $form->textFieldRow($model,'price',array('class'=>'span5')); ?>

		<?php echo $form->textFieldRow($model,'remarks',array('class'=>'span5','maxlength'=>100)); ?>

		<?php echo $form->textFieldRow($model,'area',array('class'=>'span5','maxlength'=>10)); ?>

		<?php echo $form->textFieldRow($model,'item_type',array('class'=>'span5','maxlength'=>10)); ?>

		<?php echo $form->textFieldRow($model,'unit',array('class'=>'span5','maxlength'=>30)); ?>

	<div class="form-actions">
		<?php $this->widget('bootstrap.widgets.TbButton', array(
			'buttonType' => 'submit',
			'type'=>'primary',
			'label'=>'Search',
		)); ?>
	</div>

<?php $this->endWidget(); ?>
