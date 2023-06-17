<?php
/* @var $this AdjustmentController */
/* @var $model Adjustment */
/* @var $items AdjustmentDetails */
/* @var $cs CClientScript */
/* @var $areas Array */

$baseUrl = Yii::app()->request->baseUrl;

$this->breadcrumbs=array(
    'Inventory' => $baseUrl.'/modules/supply_office/seg-supply-functions.php',
    'Adjustments' => array('/inventory/adjustment/admin'),
    'View',
    $model->refno,
);

$cs = Yii::app()->clientScript;
$this->setPageTitle('<b>View</b> adjustment');
$this->showFooter = false;
?>

<?php
    $this->beginWidget('application.widgets.SegBox', array(
        'title' => false,
        'htmlOptions' => array(
            'class' => 'bootstrap-widget-table'
        )
    ));
?>

<?php 
    $this->widget('bootstrap.widgets.TbDetailView', array(
        'data'=>$model,
        'attributes'=>array(
            'refno',
            array(
                'label' => 'Adjustment Date',
                'value' => date('F j, Y h:i A',strtotime($model->adjust_date))
            ),
            'personnelName',
            'areaName',
            'remarks',
            array(
                'label' => 'Posted to inventory?',
                'type' => 'raw',
                'value' => function($data) {
                    return $data->is_posted ? '<span class="label label-success">POSTED</span>' : '<span class="label">NOT YET POSTED</span>';
                }
            )
        ),
    ));
?>

<?php $this->endWidget(); ?>


<?php
    $this->beginWidget('application.widgets.SegBox', array(
        'headerIcon' => 'fa fa-list',
        'title' => 'Adjustment items',
        'htmlOptions' => array(
            'class' => 'bootstrap-widget-table'
        )
    ));
?>

<?php
    $this->widget('bootstrap.widgets.TbGridView',array(
        'dataProvider' => $items->search(),
        'type' => array('bordered', 'hover', 'striped'),
        'template' => '{items}',
        'columns'=>array(
            array(
                'name' => 'item_code',
                'header' => 'Item Code',
                'sortable' => false,
            ),
            array(
                'name'=>'product.artikelname',
                'header' => 'Name',
                'type'=>'raw',
                'sortable' => false,
                'htmlOptions' => array(
                    'style' => 'width:20%'
                )
            ),
            array(
                'header' => 'Batch No.',
                'type' => 'raw',
                'filter'=>false,
                'sortable' => false,
                'value' => function($data) {
                    return $data->stockKeepingUnit ? $data->stockKeepingUnit->order_no : '<em>-No stock-</em>';
                },
            ),
            array(
                'name'=>'expiry_date',
                'type'=>'raw',
                'filter'=>false,
                'sortable' => false,
            ),
            array(
                'name'=>'serial_no',
                'type'=>'raw',
                'filter'=>false,
                'sortable' => false,
            ),
            array(
                'name'=>'lot_no',
                'type'=>'raw',
                'filter'=>false,
                'sortable' => false,
            ),
            array(
                'name'=>'unit_cost',
                'type'=>'raw',
                'filter'=>false,
                'sortable' => false,
            ),
            array(
                'name'=>'orig_qty',
                'type'=>'raw',
                'filter'=>false,
                'sortable' => false,
            ),
            array(
                'name'=>'adj_qty',
                'type'=>'raw',
                'filter'=>false,
                'sortable' => false,
            ),
        )
    ));
?>

<?php $this->endWidget(); ?>
