<?php

/**
 * reviewImport.php
 *
 * @author Alvin Quinones <ajmquinones@gmail.com>
 * @copyright (c) 2016, Segworks Technologies Corporation
 *
 */

use SegHis\modules\inventory\models\InventoryImporter;

/**
 *
 * Description of reviewImport
 *
 * @var BulkAdjustmentController $this
 * @var InventoryImporter $importer
 * @var CClientScript $cs
 */

$cs = \Yii::app()->getComponent('clientScript');
$cs->registerCss('inventory.bulkAdjustment.reviewImport#css', <<<css

.error-list {
  margin-left: 0;
  list-style: none;
}

.error-list .fa {
  font-size: 12px;
}

.error-list li {
  font-size: 13px;
  color: #666;
}

css
);


$this->setPageTitle('Review Imported Bulk Adjustment');
$baseUrl = Yii::app()->request->baseUrl;
$this->breadcrumbs=array(
    'Inventory' => $baseUrl.'/modules/supply_office/seg-supply-functions.php',
    'Adjustments' => array('/inventory/adjustment/admin'),
    'Bulk adjustment' => array('index'),
    'Import',
);

/**
 * @var array $data
 */
$dataProvider = new \CArrayDataProvider($data, array(
    'pagination' => false
));
$stats = array_reduce($data, function($prev, $row) {
    $prev['count']++;
    if ($row['errors']) {
        $prev['errors']++;
    }
    return $prev;
}, array(
    'count' => 0,
    'errors' => 0
));

$columns = array(
    array(
        'header' => '#',
        'value' => function($data, $row) {
            return $row + 1;
        },
        'htmlOptions' => array(
            'width' => '3%'
        ),
    ),
    array(
        'header' => 'Item Code',
        'value' => function($data) {
            return $data['item_code'];
        },
        'htmlOptions' => array(
            'width' => '8%'
        ),
    ),
    array(
        'header' => 'Item Name',
        'type' => 'raw',
        'value' => function($data) {
            $product = \SegHis\modules\inventory\models\PharmacyProduct::model()->findByPk($data['item_code']);
            return $data['item_name'] .
                '<br/><small class="muted">'.$product->artikelname.'</small>';
        },
        'htmlOptions' => array(
            'width' => '16%'
        ),
    ),
    array(
        'header' => 'Unit',
        'type' => 'raw',
        'value' => function($data) {
            /** @var \SegHis\models\inventory\ItemExtended $item */
            $item = \SegHis\models\inventory\ItemExtended::model()->findByPk($data['item_code']);
            $subtitle = '';
            if ($item) {
                $subtitle = '<br/><small class="muted">' . $item->pieceUnit->unit_name . ' / ' . $item->packUnit->unit_name . '</small>';
            }
            return ($data['unit'] ?: $item->pieceUnit->unit_name) . $subtitle;
        },
        'htmlOptions' => array(
            'width' => '10%'
        ),
    ),
    array(
        'header' => 'Current Expiry',
        'type' => 'raw',
        'value' => function($data) {
            return $data['expiry'] == '' ? '-' : $data['expiry'];
        },
        'htmlOptions' => array(
            'width' => '8%'
        ),
    ),
    array(
        'header' => 'Current Unit Cost',
        'type' => 'raw',
        'value' => function($data) {
            return number_format((double) $data['unit_cost'], 4);
        },
        'htmlOptions' => array(
            'style' => 'text-align: right',
            'width' => '8%',
        ),
    ),

    array(
        'header' => 'Current Qty',
        'type' => 'raw',
        'value' => function($data) {
            return $data['quantity'] == '' ? '-' : number_format((int) $data['quantity'], 0);
        },
        'htmlOptions' => array(
            'style' => 'text-align: right',
            'width' => '8%',
        ),
    ),

    array(
        'header' => 'Adjusted Expiry',
        'type' => 'raw',
        'value' => function($data) {
            return $data['adj_expiry'] == '' ? '<em>No change</em>' : '<b>'.$data['adj_expiry'].'</b>';
        },
        'htmlOptions' => array(
            'width' => '8%'
        ),
    ),
    array(
        'header' => 'Adjusted Cost',
        'type' => 'raw',
        'value' => function($data) {
            return $data['adj_cost'] == '' ? '<em>No change</em>' : '<b>'.number_format((double) $data['adj_cost'], 4).'</b>';
        },
        'htmlOptions' => array(
            'style' => 'text-align: right',
            'width' => '8%',
        ),
    ),
    array(
        'header' => 'Adjusted Qty',
        'type' => 'raw',
        'value' => function($data) {
            return $data['adj_quantity'] == '' ? '<em>No change</em>' : '<b>'.number_format((int) $data['adj_quantity'], 0).'</b>';
        },
        'htmlOptions' => array(
            'style' => 'text-align: right',
            'width' => '8%',
        ),
    ),


    array(
        'header' => 'Errors/Warnings',
        'type' => 'raw',
        'value' => function($data) {
            if ($data['errors']) {
                $errorList = array_map(function($error) {
                    return '<li><i class="fa fa-warning"></i> '. $error .'</li>';
                }, $data['errors']);

                return '<ul class="error-list">'."\n".
                    implode("\n", $errorList) . "\n" .
                    '</ul>' . "\n";
            } else {
                return '<i class="fa fa-check" style="color:#060"></i>';
            }

        },
        'htmlOptions' => array(
        ),
    )
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

<?php

$this->beginWidget('application.widgets.SegBox', array(
    'title' => 'Import inventory data',
    'headerIcon' => 'fa fa-file'
));

?>

    <?php if ($stats['errors']): ?>

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

        <div class="alert alert-error">
            <i class="fa fa-warning fa-lg"></i>

            The are errors found on the uploaded file. Please locate the indicated
            errors and try to correct them before trying to import the file.
        </div>

        <div class="row-fluid">

            <div class="span6">

                <?php

                $areas = \Area::model()->findAllByAttributes(array(
                    'lockflag' => 0
                ));
                echo $form->select2Row($importer, 'area', array(
                    'disabled' => true,
                    'data' => CHtml::listData($areas, 'area_code', 'area_name')
                ), array(
                    'hint' => 'All imported data  will be posted under the selected area'
                ));

                ?>

                <?php echo CHtml::activeHiddenField($importer, 'area'); ?>

                <?php

                echo $form->fileFieldRow($importer, 'import_file', array(
                    'class' => 'input-block-level'
                ), array(
                    'label' => 'Re-upload XLS file to import'
                ));

                ?>

            </div>

            <div class="span6">

                <?php

                $reasons = \AdjustmentReason::model()->findAllByAttributes(array());
                echo $form->select2Row($importer, 'reason', array(
                    'disabled' => true,
                    'data' => \CHtml::listData($reasons, 'adj_reason_id', 'adj_reason_name'),
                    'htmlOptions' => array()
                ));

                ?>

                <?php echo CHtml::activeHiddenField($importer, 'reason'); ?>

                <?php

                echo $form->textAreaRow($importer, 'remarks', array(
                    'placeholder' => 'Enter notes for the adjustment here ...',
                    'rows' => 3,
                    'class' => 'input-block-level',
                    'readonly' => true
                ));

                ?>

            </div>

        </div>

        <div class="form-actions">

            <?php

            echo $this->widget('bootstrap.widgets.TbButton', array(
                'id' => 'btn-import',
                'buttonType' => 'submit',
                'type' => 'primary',
                'icon' => 'fa fa-upload',
                'label' => 'Import again',
                'htmlOptions' => array(
                    'style' => 'margin-left: 0.5em; font-weight: 600',
                )
            ), true);

            ?>

        </div>

        <?php $this->endWidget(); ?>

    <?php else: ?>

        <?php

        /**
         * @var TbActiveForm $form
         */
        $date = date('Y-m-d',strtotime($importer['import_date']));
        $time = date('H:i:s',strtotime($importer['import_time']));

        $form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
            'id' => 'import-form',
            'action' => array('/inventory/bulkAdjustment/import/date/'.$date.'/time/'.$time),
            'enableAjaxValidation' => false,
            'enableClientValidation' => false,
            'type' => 'horizontal',
        ));

        ?>

        <div class="alert alert-success clearfix">
            <i class="fa fa-check-circle fa-lg"></i>
            <b>Congratulations!</b> The imported file contains no errors. Continue?

            <div class="pull-right">

                <?php

                echo $this->widget('bootstrap.widgets.TbButton', array(
                    'id' => 'btn-import',
                    'buttonType' => 'submit',
                    'type' => 'success',
                    'icon' => 'fa fa-check',
                    'label' => 'Proceed',
                ), true);

                ?>

                <?php

                echo $form->hiddenField($importer, 'import_data', array(
                    'value' => CJSON::encode(array_map(function($row) {
                        unset($row['item']);
                        unset($row['errors']);
                        if(!$row['adj_cost']){
                            $row['adj_cost'] = $row['unit_cost'];
                        }
                        return $row;
                    }, $data))
                ));

                ?>

                <?php echo CHtml::activeHiddenField($importer, 'area'); ?>

                <?php echo CHtml::activeHiddenField($importer, 'reason'); ?>

                <?php echo CHtml::activeHiddenField($importer, 'remarks'); ?>

                <?php

                echo $this->widget('bootstrap.widgets.TbButton', array(
                    'type' => 'danger',
                    'url' => array('index'),
                    'icon' => 'fa fa-times',
                    'label' => 'Cancel',
                ), true);

                ?>

            </div>
        </div>

        <?php $this->endWidget(); ?>

    <?php endif; ?>


    <?php

    $template = "{items}
    <div class='pull-right'>
        {summary}
        <div class='span12'>{pager}</div>
    </div>
    ";

    $this->widget(
        'bootstrap.widgets.TbExtendedGridView',
        array(
            'id' => 'import-grid',
            'fixedHeader' => true,
            'type' => 'striped bordered hover',
//            'headerOffset' => 40,
            'dataProvider' => $dataProvider,
            'template' => $template,
            'columns' => $columns,
            'rowCssClassExpression' => function($row, $data) {
                $class = $data['errors'] ? 'error' : '';
                return $class;
            }
        )
    );

    ?>

<?php $this->endWidget(); ?>
