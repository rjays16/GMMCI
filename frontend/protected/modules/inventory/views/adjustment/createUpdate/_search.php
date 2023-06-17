<?php

/**
 * @var CDataProvider $dataProvider
 */

use SegHis\modules\inventory\models\StockKeepingUnit;
use SegHis\modules\inventory\models\Unit;
use SegHis\modules\inventory\models\InventoryExporter;

$columns = array(
    array(
        'header' => 'Item #',
        'type' => 'raw',
        'value' => function($data) {
            $value = $data['bestellnum'];
            $hiddenField = CHtml::hiddenField('result-data', $value, array(
                'data-id' => 'code'
            ));
            return $value . $hiddenField;
        },
        'htmlOptions' => array( 'style' => 'width: 60px' ),
    ),
    array(
        'header' => 'Name',
        'type' => 'raw',
        'value' => function($data) {
            $name = CHtml::encode($data['artikelname']);
            if ($data['generic']) {
                $name .= '<br/>' . CHtml::tag('small', array('class' => 'light-blue-800'), $data['generic']);
            }
            $hiddenFields =
                CHtml::hiddenField('result-data', $data['artikelname'], array(
                    'data-id' => 'name'
                )).
                CHtml::hiddenField('result-data', $data['generic'], array(
                    'data-id' => 'generic'
                ));
            return $name . $hiddenFields;
        }
    ),
    array(
        'header' => 'Unit',
        'type' => 'raw',
        'value' => function($data) {
            /** @var Unit $unit */
            $unit = Unit::model()->findByPk($data['unit_id'] ? $data['unit_id'] : $data['pc_unit_id']);
            $unitName = ($unit ? $unit->unit_name : '-');
            $hiddenFields =
                CHtml::hiddenField('result-data', $unit->unit_id, array(
                    'data-id' => 'unitCode'
                )).
                CHtml::hiddenField('result-data', $unitName, array(
                    'data-id' => 'unit'
                ));
            return  $unitName . $hiddenFields;
        },
        'htmlOptions' => array( 'style' => 'width:10%' ),
    ),
    array(
        'header' => 'Batch #',
        'type' => 'raw',
        'value' => function($data) {
            $orderNo = '<em>-No stock-<em/>';
            if ($data['order_no']) {
                $orderNo = CHtml::tag('span', array(), '#'.$data['order_no']);
            }

            $hiddenFields =
                CHtml::hiddenField('result-data', $data['sku_id'], array(
                    'data-id' => 'skuId'
                )).
                CHtml::hiddenField('result-data', $data['serial_no'], array(
                    'data-id' => 'serial'
                )).
                CHtml::hiddenField('result-data', $data['lot_no'], array(
                    'data-id' => 'lot'
                ));
            return $orderNo . $hiddenFields;
        },
        'htmlOptions' => array( 'style' => 'width:10%' ),
    ),
    array(
        'header' => 'Expiry',
        'type' => 'raw',
        'value' => function($data) {

            #added by julz
            $area = $_GET['area'];
            $Refno = InventoryExporter::getmaxrefno($area,$data['bestellnum']);
            $expiry_date = Adjustment::getMedExpry($Refno,$data['bestellnum']);
             if($expiry_date == '0000-00-00' || $expiry_date == '01/01/1970'){
                $expiry_date = $data['expiry_date'] == '0000-00-00' || $data['expiry_date'] == '1970-01-01' || $data['expiry_date'] ? '' : date('m/d/Y', strtotime($data['expiry_date']));
            }
            #end

            $hiddenField = CHtml::hiddenField('result-data', $expiry_date, array(
                'data-id' => 'expiryDate'
            ));
            return $expiry_date . $hiddenField;

        },
        'htmlOptions' => array( 'style' => 'width:12.5%' ),
    ),
    array(
        'header' => 'Unit Cost',
        'type' => 'raw',
        'value' => function($data) {
             $area = $_GET['area'];

            $cost =  InventoryExporter::fetchUnitCostFromSkuCatalog($data['bestellnum'],$area);
            $hiddenField = CHtml::hiddenField('result-data', number_format($data['unit_cost'], 2, '.', ''), array(
                'data-id' => 'cost'
            ));

            return $cost . $hiddenField;
        },
        'htmlOptions' => array( 'style' => 'width:10%; text-align:right' ),
    ),
    array(
        'header' => 'Stock',
        'type' => 'raw',
        'value' => function($data) {
            
            //$qty = '0';
            // if ($data['sku_id']) {
                /** @var StockKeepingUnit $sku */
                #modified by monmon
                $area = $_GET['area'];
             //  $sku = StockKeepingUnit::model()->findByPk($data['sku_id']);
                // $qty = $sku ? $sku->getCurrentQuantity() : 0;
                // if($qty < 0){
             $qty = InventoryExporter::getHospitalItemQty($data['bestellnum'],$area,$data['expiry_date']);
                //}
           // }


            $hiddenField = CHtml::hiddenField('result-data', $qty, array(
                'data-id' => 'quantity'
            ));

            return $qty . $hiddenField;
        },
        'htmlOptions' => array( 'style' => 'width:10%; text-align:right' ),
    ),
    array(
        'header' => '',
        'class' => 'application.widgets.ButtonColumn',
        'buttons' => array(
            'add' => array(
                'icon' => 'fa fa-plus',
                'options' => array(
                    'class' => 'btn-success add-to-tray',
                    'title' => 'Add this item to the list',
                    'data' => function() {
                        return array(
                            'toggle' => 'tooltip',
                            'animation' => false,
                            'container' => 'body',
                        );
                    },
                ),
            ),
        ),
        'template' => '{add}',
    )
);


$template = "{items}
<div class='pull-right'>
    {summary}
    <div class='span12'>{pager}</div>
</div>
";

?>

<div id="search-results-container" class="row-fluid">
    <div class="span12">
        <?php
            $this->widget(
                'bootstrap.widgets.TbExtendedGridView',
                array(
                    'id' => 'item-search-grid',
                    'fixedHeader' => false,
                    'type' => 'striped bordered hover',
            //        'headerOffset' => 40,
                    // 40px is the height of the main navigation at bootstrap
                    'ajaxUrl' => array('searchGrid'),
                    'dataProvider' => $dataProvider,
                    'template' => $template,
                    'columns' => $columns,
                    'rowCssClassExpression' => functioN($index, $data) {
                        return ($data['order_no']) ? '' : 'warning';
                    },
                )
            );
        ?>
    </div>
</div>