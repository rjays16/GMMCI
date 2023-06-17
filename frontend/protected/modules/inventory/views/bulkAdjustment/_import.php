<?php

/**
 * @var CController $this
 * @var \SegHis\modules\inventory\models\InventoryExporter $exporter
 * @var \SegHis\modules\inventory\models\InventoryImporter $importer
 */

?>

<?php
/**
 * @var TbActiveForm $form
 */
$form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
    'id' => 'import-form',
    'action' => array('/inventory/bulkAdjustment/reviewImport'),
    'enableAjaxValidation' => false,
    'enableClientValidation' => false,
    'type' => 'horizontal',
    'htmlOptions' => array(
        'enctype' => 'multipart/form-data'
    )
));
?>

<div class="alert alert-warning">
    <i class="fa fa-warning fa-lg"></i> The changes that will be made through bulk adjustment cannot be undone due to
    the amount of data that will be written to the database. After clicking the
    <b>Import</b> button, you will be able to review the changes one last time
    before the data is sent to the server.
</div>

<?php

$areas = \Area::model()->findAllByAttributes(array(
    'lockflag' => 0
));

#added by julz change format due to browser update
   echo $form->dateFieldRow($importer, 'import_date', array(
        'placeholder' => 'mm/dd/yyyy',
    ));

    echo $form->timePickerRow($importer, 'import_time', array( 
        'htmlOptions' => array(
            'style' => 'width:100px')  
    ));
#end
   



echo $form->select2Row($importer, 'area', array(
    'data' => CHtml::listData($areas, 'area_code', 'area_name')
), array(
    'hint' => 'All imported data  will be posted under the selected area'
));

?>

<?php
$reasons = \AdjustmentReason::model()->findAllByAttributes(array());
echo $form->select2Row($importer, 'reason', array(
    'data' => \CHtml::listData($reasons, 'adj_reason_id', 'adj_reason_name'),
    'htmlOptions' => array()
));

?>

<?php

echo $form->textAreaRow($importer, 'remarks', array(
    'placeholder' => 'Enter notes for the adjustment here ...',
    'rows' => 3,
    'class' => 'input-block-level'
));

?>

<?php

echo $form->fileFieldRow($importer, 'import_file', array(
    'class' => 'input-block-level'
));

?>


<?php $this->endWidget(); ?>


<?php

$exporterAreaId = \CHtml::activeId($exporter, 'area');
$importerAreaId = \CHtml::activeId($importer, 'area');

\Yii::app()->getClientScript()->registerScript('bulkAdjustment.index.import#ready', <<<js
$('#{$importerAreaId}').off('change').on('change', function(e) {
  $('#{$exporterAreaId}').select2('val', $(this).select2('val'));
});

$('#btn-import').off('click').on('click', function(e) {
  $('#import-form').submit();
});
js
    , \CClientScript::POS_READY);

?>