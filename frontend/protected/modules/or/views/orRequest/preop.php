<?php
/* @var $this OrRequestController */
/* @var $model OrRequest */

$this->breadcrumbs=array(
    'Or Requests'=>array('index'),
    $orRequest->or_refno,
);

$this->menu=array(
    array('label'=>'View All Requests', 'url'=>array('index')),
);
?>

<h1>Request Pre Operative</h1>

<?php $form=$this->beginWidget('bootstrap.widgets.TbActiveForm',array(
    'id'=>'or-request-form',
    'enableAjaxValidation'=>false,
    'htmlOptions'=>array('class'=>'well'),
    'type'=>'horizontal',
)); ?>
<?php echo $form->errorSummary($model); ?>
<?= $form->hiddenField($model, 'or_refno', array('value'=>$orRequest->or_refno)); ?>

<?php echo $this->renderPartial('_detail', array('model'=>$orRequest)); ?>

<?php $box = $this->beginWidget(
    'bootstrap.widgets.TbBox',
    array(
        'title' => 'Vital Signs',
    )
);?>
<?= $form->textFieldRow($model, 'est_length_op', array('class' => 'span5')); ?>
<?php
    // echo $form->textAreaRow($model, 'case_classification', array('class' => 'span5')); 
?>
<?= $form->textAreaRow($model, 'pre_op_diagnosis', array('class' => 'span5')); ?>
<?= $form->textFieldRow($model, 'blood_pressure', array('class' => 'span5')); ?>
<?= $form->textFieldRow($model, 'temperature', array('class' => 'span5')); ?>
<?= $form->textFieldRow($model, 'pulse', array('class' => 'span5')); ?>
<?= $form->textFieldRow($model, 'respiration', array('class' => 'span5')); ?>
<?php $this->endWidget(); ?>

<?php $box = $this->beginWidget(
    'bootstrap.widgets.TbBox',
    array(
        'title' => 'Requirements',
    )
);?>
<?= $form->checkBoxListRow(
    $model,
    'orChecklists',
    $checkListArray
) ?>
<?php $this->endWidget(); ?>

<?php
$this->widget(
    'bootstrap.widgets.TbButton',
    array(
        'label' => 'Approve',
        'type' => 'success',
        'buttonType'=>'submit',
    )
);
?>
&nbsp;
<?php
$this->widget(
    'bootstrap.widgets.TbButton',
    array(
        'label' => 'Cancel',
        'type' => 'warning',
        'url'=>$this->createUrl('orRequest/index',array('flag'=>$orRequest->request_flag))
    )
);
?>
<?php $this->endWidget(); ?>