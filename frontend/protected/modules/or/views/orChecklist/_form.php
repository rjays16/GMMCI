<div class="well"> 

	<?php $form=$this->beginWidget('bootstrap.widgets.TbActiveForm',array(
		'id'=>'seg-or-checklist-form',
		'enableAjaxValidation'=>true,
		)); ?>

	<?php echo $form->errorSummary($model); ?>
	
	<p class="help-block">Fields with <span class="required">*</span> are required.</p>

		<?php //echo $form->textAreaRow($model,'checklist_question',array('rows'=>6, 'cols'=>50, 'class'=>'span8')); ?>

		<?php 
		echo "Checklist Question";
		echo "<br/><br/>";
		$this->widget(
			'bootstrap.widgets.TbCKEditor',
			array(
				'model' => $model,
				'attribute' => 'checklist_question',
				'editorOptions' => array(
					'plugins' => 'basicstyles,toolbar,enterkey,entities,floatingspace,wysiwygarea,indentlist,link,list,dialog,dialogui,button,indent,fakeobjects'
					)
				)
			);

		 ?>
		 <br/>

		<?php echo $form->textFieldRow($model,'label_data', array('class'=>'span5','maxlength'=>50)); ?>

		<?php 

			if ($model->isNewRecord) 
			{
				$uname = $_SESSION['sess_login_username'];
				$history = array(array($uname, date("Y-m-d H:i:s")));

				echo $form->hiddenField($model,'history', array('value'=>json_encode($history)));
			}
			else
			{	
				
				$history = json_decode($model->history);

				$uname = $_SESSION['sess_login_username'];
				$arr_json = array($uname, date("Y-m-d H:i:s"));
				
				array_push($history, $arr_json);
				echo $form->hiddenField($model,'history', array('value'=>json_encode($history)));

			}


		 ?>
		<?php 

		if ($model->isNewRecord)
		{
			
			echo $form->hiddenField($model,'date_created', array('value'=>date("Y-m-d H:i:s"))); 

		}
		else
		{

			echo $form->hiddenField($model,'date_created', array('value'=>$model->date_created)); 

		}


		?>
		<!-- Date modified must be today -->

		<?php echo $form->hiddenField($model,'date_modified',array('value'=>date("Y-m-d H:i:s"))); ?>

		<?php echo $form->dropDownListRow($model,'type',array("request"=>"request","preop"=>"preop",),array('class'=>'input-large')); ?>

	<div class="form-actions">
		<?php $this->widget('bootstrap.widgets.TbButton', array(
				'buttonType'=>'submit',
				'type'=>'primary',
				'label'=>$model->isNewRecord ? 'Create' : 'Save',
			)); ?>
	</div>

<?php $this->endWidget(); ?>


</div>

