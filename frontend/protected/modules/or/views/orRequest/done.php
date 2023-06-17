<?php
/* @var $this OrRequestController */
/* @var $orRequestModel OrRequest */
/* @var $model OpaccommodationDetails */
$this->breadcrumbs=array(
    'Or Requests'=>array('index'),
    $orRequestModel->or_refno,
);

$this->menu=array(
    array('label'=>'View All Requests', 'url'=>array('index')),
);
?>

<h1>Room Charging</h1>

<?php $form=$this->beginWidget('bootstrap.widgets.TbActiveForm',array(
    'id'=>'or-request-form',
    'enableAjaxValidation'=>false,
    'htmlOptions'=>array('class'=>'well'),
    'type'=>'horizontal',
)); ?>

<?php echo $form->errorSummary($model); ?>
<?= $form->hiddenField($orRequestModel, 'or_refno'); ?>
<?= $form->hiddenField($model, 'request_flag', array('value'=>'done')); ?>

<?php echo $this->renderPartial('_detail', array('model'=>$orRequestModel)); ?>


<div class="row-fluid">
    <div class="span6">
        <?php
            echo $form->numberFieldRow($model, 'charge', array('step' => 'any'));
        ?>

        <?php
            $this->widget(
                'bootstrap.widgets.TbButton',
                array(
                    'label' => 'Save',
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
    </div>
    <div class="span6">
        <?php
            $template = "{items}<div class='row'>
                <div class='pull-right'>{pager}</div>
            </div>";

            $this->widget(
                'bootstrap.widgets.TbGridView', 
                array(
                    'id' => 'user-grid',
                    'type' => 'bordered',
                    'dataProvider' => $dataProvider,
                    'template' => $template,
                    'pagerCssClass' => 'pagination pull-right',
                    'columns' => array(
                        array(
                            'name' => 'name', 
                        ),
                        array(
                            'name' => 'price', 
                        ),
                    )
                )
            );
        ?>
    </div>
</div>
<?php $this->endWidget(); ?>


<script>
$(document).ready(function(){
    var room_charge = $('#OpaccommodationDetails_charge');

    room_charge.on('change', function(){
        var $this = $(this);

        $this.val(parseFloat($this.val()).toFixed(2));
    });
});
</script>