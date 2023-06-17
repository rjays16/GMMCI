<div class="well">
	<?php $form=$this->beginWidget('bootstrap.widgets.TbActiveForm',array(
		'id'=>'seg-or-technique-form',
		'enableAjaxValidation'=>false,
		)); ?>

		<p class="help-block">Fields with <span class="required">*</span> are required.</p>

		<?php echo $form->errorSummary($model); ?>

		<?php echo $form->textFieldRow($model,'technique_name',array('class'=>'span5','maxlength'=>30)); ?>
		<br/>
		<br/>
	<?php //echo $form->textAreaRow($model,'technique_desc',array('rows'=>6, 'cols'=>50, 'class'=>'span8')); 

	echo "Technique Description";
	echo "<br/><br/>";

	$this->widget(
		'bootstrap.widgets.TbCKEditor',
		array(
			'model' => $model,
			'attribute' => 'technique_desc',
			'editorOptions' => array(
				'plugins' => 'basicstyles,toolbar,enterkey,entities,floatingspace,wysiwygarea,indentlist,link,list,dialog,dialogui,button,indent,fakeobjects'
				)
			)
		);

		?>

		<br/>
		<div class="form-actions">
			<?php $this->widget('bootstrap.widgets.TbButton', array(
				'buttonType'=>'submit',
				'type'=>'primary',
				'label'=>$model->isNewRecord ? 'Create' : 'Save',
				)); ?>
		</div>

			<?php $this->endWidget(); ?>

</div>
