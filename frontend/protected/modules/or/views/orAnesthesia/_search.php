<?php $form=$this->beginWidget('bootstrap.widgets.TbActiveForm',array(
	'action'=>Yii::app()->createUrl($this->route),
	'method'=>'get',
)); ?>

		<?php echo $form->textFieldRow($model,'anesth_id',array('class'=>'span5','maxlength'=>12)); ?>

		<?php echo $form->textFieldRow($model,'anest_name',array('class'=>'span5','maxlength'=>50)); ?>

		<?php echo $form->dropDownListRow($model,'anest_category',array("General"=>"General","Regional"=>"Regional","Local"=>"Local","Conscious"=>"Conscious",),array('class'=>'input-large')); ?>

		<?php echo $form->textAreaRow($model,'description',array('rows'=>6, 'cols'=>50, 'class'=>'span8')); ?>

	<div class="form-actions">
		<?php $this->widget('bootstrap.widgets.TbButton', array(
			'buttonType' => 'submit',
			'type'=>'primary',
			'label'=>'Search',
		)); ?>
	</div>

<?php $this->endWidget(); ?>
