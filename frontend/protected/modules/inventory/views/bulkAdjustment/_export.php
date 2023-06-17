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
    'id' => 'export-form',
    'action' => array('/inventory/bulkAdjustment/index'),
    'enableAjaxValidation' => false,
    'enableClientValidation' => false,
    'type' => 'horizontal'
));
?>

<div class="alert alert-info">
    <i class="fa fa-info-circle fa-lg"></i> Download the adjustment XLS template using the <b>Export</b> button. Only
    items that match the selected options will be included in the template.
</div>

<?php
$areas = Area::model()->findAllByAttributes(array(
    'lockflag' => 0
));
echo $form->select2Row($exporter, 'area', array(
    'data' => CHtml::listData($areas, 'area_code', 'area_name')
), array(
    'hint' => 'Only items that are <b>*available*</b> in the selected area will be included in the template'
));

#added by julz
echo $form->dateFieldRow($exporter, 'date_item', array(
        'value' => date('m/d/Y')
    ));
#end

?>



<?php $this->endWidget(); ?>

<?php

$exporterAreaId = \CHtml::activeId($exporter, 'area');
$importerAreaId = \CHtml::activeId($importer, 'area');



\Yii::app()->getClientScript()->registerScript('bulkAdjustment.index.download#ready', <<<js

$('#btn-export').off('click').on('click', function(e) {
  Alerts.confirm({
    title: 'Import inventory data',
    content: 'Do you wish to start importing the inventory data for the selected area? This might take a few minutes.',
    callback: function(result) {
      if (result) {
        $('#export-form').submit();
      }
    }
  })

});

$('#{$exporterAreaId}').off('change').on('change', function(e) {
  $('#{$importerAreaId}').select2('val', $(this).select2('val'));
});
js
    , \CClientScript::POS_READY);



 
   #end monmon

?>