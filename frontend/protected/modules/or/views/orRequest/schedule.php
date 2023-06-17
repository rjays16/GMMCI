<?php
/* @var $this OrRequestController */
/* @var $model OrRequest */

Yii::app()->clientScript->registerScriptFile(Yii::app()->request->baseUrl . '/frontend/scripts/orRequest/schedule.js');

$this->breadcrumbs=array(
    'Or Requests'=>array('index'),
    $orRequest->or_refno,
);

$this->menu=array(
    array('label'=>'View All Requests', 'url'=>array('index')),
);
?>

<h1>Request Scheduling</h1>

<?php $form=$this->beginWidget('bootstrap.widgets.TbActiveForm',array(
    'id'=>'or-pre-op-details-form',
    'enableAjaxValidation'=>false,
    'htmlOptions'=>array('class'=>'well'),
    'type'=>'horizontal',
)); ?>
<?php echo $form->errorSummary($model); ?>
<?= $form->hiddenField($model, 'or_refno', array('value'=>$orRequest->or_refno)); ?>

<!-- Request Details -->
<?php echo $this->renderPartial('_detail', array('model'=>$orRequest)); ?>

<!-- Pre-op Details -->
<?php $box = $this->beginWidget(
    'bootstrap.widgets.TbBox',
    array(
        'title' => 'Operation Details',
        'headerIcon' => 'icon-th-list',
    )
);?>
<?=
$form->textFieldRow($model, 'operation_date',
    array(
        'class' => 'datetime_field',
        'placeholder'=>false
    )
);
?>
<?php $this->endWidget(); ?>



    <!-- Personnel Involved -->
<?php echo $this->renderPartial('_personnelInvolved', array('model'=>$model, 'form'=>$form)); ?>

<?php
$this->widget(
    'bootstrap.widgets.TbButton',
    array(
        'label' => 'Set Schedule',
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

<!-- Personnel Modal -->
<?php echo $this->renderPartial('_personnelModal', array('person'=>$person)); ?>