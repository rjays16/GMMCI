<?php


?>


<div class="form">

<?php
    $form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
        'id' => 'walkin-form',
        'type' => 'horizontal',
        'enableAjaxValidation' => false,
        'htmlOptions' => array(
            'class' => 'service-form'
        )
    ));

    /* @var $activeForm TbActiveForm */
?>

    <input type="hidden" name="tab" value="walkin" />

    <?php echo $form->errorSummary($model) ?>

    <?php echo $form->textFieldRow($model, 'pMemberLastName', array(
        'class' => 'input-xlarge',
        'placeholder' => ''
    )); ?>

    <?php echo $form->textFieldRow($model, 'pMemberFirstName', array(
        'class' => 'input-xlarge',
        'placeholder' => ''
    )); ?>

    <?php echo $form->textFieldRow($model, 'pMemberMiddleName', array(
        'class' => 'input-xlarge',
        'placeholder' => ''
    )); ?>

    <?php echo $form->textFieldRow($model, 'pMemberSuffix', array(
        'class' => 'input-small',
        'placeholder' => ''
    )); ?>

    <?php echo $form->datepickerRow($model,'pMemberBirthDate',
        array(
            'options' => array(
                'language' => 'en',
                'format' => 'mm-dd-yyyy'
            ),
            'htmlOptions' => array(
                'placeholder' => 'MM-DD-YYYY',
            )
        ),
        array(
            'prepend' => '<i class="icon-calendar"></i>',
        ));
    ?>


<?php $this->endWidget(); ?>

</div>