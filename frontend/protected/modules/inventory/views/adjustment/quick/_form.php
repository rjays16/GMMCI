<?php
/* @var $this AdjustmentController */

/* @var $model Adjustment */
/* @var $form InventoryActiveForm */
/* @var $areas Array */
/* @var $cs CClientScript */
/* @var $units \SegHis\models\inventory\Unit[] */

$cs = Yii::app()->clientScript;
$baseUrl = Yii::app()->request->baseUrl;

$unitsData = array();
foreach ($units as $unit) {
    $unitsData[$unit->unit_id] = $unit->unit_name;
}

$readyJs = <<<'JS'
$('#adjustment-form').data('unit_lookup', {unitJSON});
$('#Adjustment_area_code').on('change', function() {
  var api = $('#adjustment-items').dataTable().api();
  api.ajax.reload(function() {});
});

JS;
$readyJs = strtr($readyJs, array(
    '{unitJSON}' => CJSON::encode($unitsData)
));
$cs->registerScript('adjustment.quickCreate.form#ready', $readyJs, CClientScript::POS_READY);

?>
<div class="form">
    <?php
        $form = $this->beginWidget('inventory.widgets.InventoryActiveForm', array(
            'id' => 'adjustment-form',
            'enableAjaxValidation' => true,
            'enableClientValidation' => true,
            'clientOptions' => array(
                'validateOnSubmit' => true
            ),
            'type' => 'horizontal'
        ));
    ?>
    <div class="alert alert-info">
        Fields with <span class="required">*</span> are required.
    </div>

    <?php
        $this->beginWidget('application.widgets.SegBox', array(
            'title' => 'Adjustment information',
            'headerIcon' => 'fa fa-wrench',
            'headerButtons' => array(
            ),
        ));
        $isNewRecord = $model->getIsNewRecord();

        echo CHtml::hiddenField('post-to-inventory', 0);

        echo $form->errorSummary($model);

        echo CHtml::tag('div', array('class' => 'clearfix'));
        echo CHtml::tag('div', array('class' => 'span5'));

        //echo $form->dropDownListRow($model, 'area_code', $areas, array('placeholder' => false));
        echo $form->select2Row($model, 'area_code', array(
            'data' => $areas,
        ));

        echo $form->dateTimePickerSlider($model, 'adjust_date', array(
            'htmlOptions' => array(
                'value' => $isNewRecord ? date('m/d/Y h:i A') : date('m/d/Y h:i A', strtotime($model->adjust_date))
            ),
            'options' => array(
                'dateFormat' => 'mm/dd/yy',
                'timeFormat' => 'hh:mm TT',
                'changeMonth' => true,
                'changeYear' => true,
            )
        ));

        echo CHtml::closeTag('div');/*.span6*/

        echo CHtml::tag('div', array('class' => 'span5'));

        echo $form->select2Row($model, 'reason', array(
            'data' => $reasons
        ));
        echo $form->textAreaRow($model, 'remarks');
        echo $form->displayTextFieldRow(
            $model,
            $isNewRecord ? $_SESSION['sess_login_username'] : $model->personnel->person->getFullName(),
            $_SESSION['sess_user_personell_nr'],
            'adjusting_id',
            array('readonly' => 'readonly')
        );
        echo CHtml::closeTag('div');/*.span6*/
        echo CHtml::closeTag('div');/*.clearfix*/

        $this->endWidget();
    ?>

    <?php
         $this->beginWidget('application.widgets.SegBox', array(
            'title' => 'Items to Adjust',
            'headerIcon' => 'fa fa-medkit',
            'headerButtons' => array(),
            'htmlOptions' => array(
                'class' => '',
            ),
            'footer' => CHtml::tag('div', array(),
                $this->widget('bootstrap.widgets.TbButton', array(
                    'id' => 'btn-submit',
                    'buttonType' => 'button',
                    'type' => 'success',
                    'icon' => 'fa fa-save',
                    'label' => 'Create Adjustment',
                    'htmlOptions' => array(
                        'class' => 'pull-right'
                    )
                ), true)
            )
        ));
    ?>

        <?php echo $this->renderPartial('quick/_dataTable'); ?>

    <?php $this->endWidget(); /* SegBox */ ?>

    <?php $this->endWidget(); ?>
</div><!-- form -->
