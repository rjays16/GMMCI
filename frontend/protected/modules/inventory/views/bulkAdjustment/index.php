<?php

/**
 * @var CController $this
 * @var \SegHis\modules\inventory\models\InventoryExporter $exporter
 * @var \SegHis\modules\inventory\models\InventoryImporter $importer
 */

$this->setPageTitle('Bulk Adjustments');

$baseUrl = Yii::app()->request->baseUrl;
$this->breadcrumbs=array(
    'Inventory' => $baseUrl.'/modules/supply_office/seg-supply-functions.php',
    // 'Adjustments' => array('/inventory/adjustment/admin'),
    'Bulk adjustment',
);

?>

<style type="text/css">
    #page > .navbar{
        display: none;
    }
    body{
        padding-top: 0px;
    }
</style>

<div class="row-fluid">

    <!-- EXPORT INVENTORY -->
    <div class="span6">

        <?php

        $this->beginWidget('application.widgets.SegBox', array(
            'title' => 'Export inventory data',
            'headerIcon' => 'fa fa-file',
            'footer' => CHtml::tag('div', array(),
                $this->widget('bootstrap.widgets.TbButton', array(
                    'id' => 'btn-export',
                    'buttonType' => 'button',
                    'type' => 'primary',
                    'icon' => 'fa fa-download',
                    'label' => 'Export',
                    'htmlOptions' => array(
                        'style' => 'margin-left: 0.5em; font-weight: 600',
                        'class' => 'pull-right'
                    )
                ), true)
            )
        ));

        ?>

        <?php

        $this->renderPartial('_export', array(
            'exporter' => $exporter,
            'importer' => $importer,
        ));

        ?>

        <?php $this->endWidget(); ?>

    </div>


    <!-- IMPORT INVENTORY -->
    <div class="span6">

        <?php


        $this->beginWidget('application.widgets.SegBox', array(
            'title' => 'Import inventory data',
            'headerIcon' => 'fa fa-list',
            'footer' =>
//CHtml::tag('div', array('class' => 'row-fluid'),
//'<div class="span9"><div class="alert alert-info">
//    <i class="fa fa-info-circle fa-lg"></i> The import functionality currently disabled while resetting of previous inventory
//    records is ongoing.
//</div></div>
//<div class="span3">'.
                '<div class="pull-right">'.

                $this->widget('bootstrap.widgets.TbButton', array(
                    'id' => 'btn-import',
                    'buttonType' => 'button',
                    'type' => 'primary',
                    'icon' => 'fa fa-upload',
                    'label' => 'Import',
                    'htmlOptions' => array(
                        'style' => 'margin-left: 0.5em; font-weight: 600',
                        'class' => 'pull-right',
                    )
                ), true) .

//                $this->widget('bootstrap.widgets.TbButton', array(
//                    'id' => 'btn-verify',
//                    'buttonType' => 'button',
//                    'type' => 'success',
//                    'icon' => 'fa fa-check',
//                    'label' => 'Verify import file',
//                    'htmlOptions' => array(
//                        'style' => 'margin-left: 0.5em; font-weight: 600',
//                        'class' => 'pull-right',
//                    )
//                ), true) .

                '</div>'
//. CHtml::closeTag('div'))
        ));

        ?>

        <?php

        $this->renderPartial('_import', array(
            'exporter' => $exporter,
            'importer' => $importer,
        ));

        ?>

        <?php $this->endWidget(); ?>

    </div>
</div>
