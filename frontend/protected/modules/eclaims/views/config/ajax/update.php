<?php

$this->breadcrumbs[] = 'Update';
$this->setPageTitle('Update HIE Service Configuration');
$this->setPageIcon('fa fa-cog');

$activeForm = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
    'id'=>'eclaims-config-form',
    'type' => 'horizontal',
    'enableAjaxValidation' => true,
));

/* @var $activeForm TbActiveForm */

?>

<?php

$this->widget('bootstrap.widgets.TbAlert', array(
        'block' => true,
        'fade' => true,
        'closeText' => '&times;', // false equals no close link
        'events' => array(),
        'htmlOptions' => array(),
        'userComponentId' => 'user',
        'alerts' => array( // configurations per alert type
            // success, info, warning, error or danger
            'success' => array('closeText' => '&times;'),
            'info', // you don't need to specify full config
            'warning' => array('block' => false, 'closeText' => '&times;'),
            'error' => array('block' => false, 'closeText' => '&times;')
        ),
));

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

<?php $this->endWidget(); // Form ?>