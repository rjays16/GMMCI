<?php
/* @var $this OrRequestController */
/* @var $model OrRequest */

$this->breadcrumbs=array(
    'Or Requests'=>array('index'),
    $model->or_refno,
);

$this->menu=array(
    array('label'=>'View All Requests', 'url'=>array('index')),
);
?>

<h1>Request Approval</h1>

<?php $form=$this->beginWidget('bootstrap.widgets.TbActiveForm',array(
    'id'=>'or-request-form',
    'enableAjaxValidation'=>false,
    'htmlOptions'=>array('class'=>'well')
)); ?>
<?php echo $form->errorSummary($model); ?>
<?= $form->hiddenField($model, 'or_refno'); ?>
<?= $form->hiddenField($model, 'request_flag', array('value'=>'approved')); ?>

<?php echo $this->renderPartial('_detail', array('model'=>$model)); ?>

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
        'url'=>$this->createUrl('orRequest/index',array('flag'=>$model->request_flag))
    )
);
?>
<?php $this->endWidget(); ?>