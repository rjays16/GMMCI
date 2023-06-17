<?php

$this->breadcrumbs[] = 'Update';
$this->setPageTitle('Update HIE Service Configuration');
$this->setPageIcon('fa fa-cog');

$activeForm = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
    'id'=>'eclaims-config-form',
    'type' => 'horizontal',
    'enableAjaxValidation' => true,
));

$box = $this->beginWidget(
    'application.widgets.SegBox',
    array(
        'title' => 'HIE Service',
        'headerIcon' => 'icon-cog',
        'htmlOptions' => array('class' => ''),
        'footer' => CHtml::tag('div',
            array('class' => 'form-actions'),
            $this->widget('bootstrap.widgets.TbButton',
                array(
                    'buttonType' => 'submit',
                    'type' => 'primary',
                    'icon' => 'fa fa-save',
                    'loadingText' => 'Saving ...',
                    'label' => 'Save changes',
                    'htmlOptions' => array(
                        'class' => 'stateful',
                    )
                ),
                true
            )
        ),
        'htmlFooterOptions' => array('class' => 'form-horizontal'),
    )
);


/* @var $activeForm TbActiveForm */

?>

<div class="row-fluid">
    <div class="span12">

        <?php echo $activeForm->errorSummary($model) ?>

        <?php echo $activeForm->textFieldRow($model, 'hospital_name', array(
            'class' => 'input-xlarge',
        )); ?>

        <?php echo $activeForm->textFieldRow($model, 'client_id', array(
            'class' => 'input-xlarge',
        )); ?>

        <?php echo $activeForm->textFieldRow($model, 'client_secret', array(
            'class' => 'input-xlarge',
        )); ?>

        <?php echo $activeForm->textFieldRow($model, 'base_url', array(
            'class' => 'input-xlarge',
        )); ?>

        <?php echo $activeForm->textFieldRow($model, 'files_url', array(
            'class' => 'input-xlarge',
        )); ?>

        <?php echo $activeForm->textFieldRow($model, 'hospital_code', array(
            'class' => 'input-xlarge',
        )); ?>

    </div>
</div>

<?php $this->endWidget(); // Box ?>
<?php $this->endWidget(); // Form ?>