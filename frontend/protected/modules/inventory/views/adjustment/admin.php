<?php
/* @var $this AdjustmentController */
/* @var $model Adjustment */
/* @var $cs CClientScript */
$baseUrl = Yii::app()->request->baseUrl;

$this->breadcrumbs=array(
    'Inventory' => $baseUrl.'/modules/supply_office/seg-supply-functions.php',
    'Manage adjustments',
);

$cs = Yii::app()->clientScript;
$cs->registerCss('style', 'body{padding:0;}');
$cs->registerScript('headJs',<<<JS
jQuery('#Adjustment_personnelName').tooltip();
JS
,CClientScript::POS_READY);

$this->setPageTitle('Adjustments');
$this->showFooter = false;

$this->beginWidget('application.widgets.SegBox', array(
    'title' => false
));

?>

<div class="row-fluid">
    <div class="span12">

<?php

$template = "{items}
<div class='pull-right'>
    {summary}
    <div class='span12'>{pager}</div>
</div>";

$this->widget('bootstrap.widgets.TbGridView', array(
    'id'=>'adjustment-grid',
    'dataProvider'=> $model->search(),
    'type' => array('hover', 'bordered', 'striped'),
    'filter'=>$model,
    'template' => $template,
    'afterAjaxUpdate'=>"function(){
        jQuery('.date-picker').datepicker({dateFormat:'dd/mm/yy',autoclose:true});
        jQuery('#Adjustment_personnelName').tooltip();
    }",
    'columns'=>array(
        array(
            'name' => 'refno',
            'htmlOptions' => array(
                'style' => 'width: 10%'
            )
        ),
        array(
            'name' => 'adjust_date',
            'header' => 'Date/Time',
            'value' => function($data) {
                return date("m/d/Y h:i A",strtotime($data->adjust_date));
            },
            'type' => 'raw',
            'filter' => $this->widget('bootstrap.widgets.TbDatePicker',array(
                'model' => $model,
                'attribute' => 'adjust_date',
                'htmlOptions' => array('class' => 'date-picker'),
                'options' => array('autoclose' => true)
            ),true),
            'htmlOptions' => array(
                'style' => 'width: 15%'
            )
        ),
        array(
            'name' => 'personnelName',
            'type' => 'raw',
            'filter' => CHtml::textField('Adjustment[personnelName]',$model->getAttribute('personnelName'),array(
                'title' => 'HRN, Personnel No, Personnel Name(last name, first name)'
            ))
        ),
        array(
            'name'=>'areaName',
            'type'=>'raw',
            'filter' => CHtml::listData(AdjustmentController::getAreas(),'area_code','area_name')
        ),

        array(
            'name'=>'remarks',
            'filter' => false,
            //modified by julz
            'htmlOptions' => array(
                'style' => 'max-width:400px;font-size: 0.9em; color: #999;overflow auto;word-wrap:break-word;'
            )
        ),
        array(
            'name' => 'is_posted',
            'header' => 'Posted?',
            'type'=>'raw',
            'filter' => array(1 => 'Yes', 0 => 'No'),
            'value' => function($data) {
                /** @var Adjustment $data */
                if ($data->is_posted) {
                    return '<span class="label label-success">POSTED</span>';
                }
            },
            'htmlOptions' => array(
                'style' => 'width:1; text-align:center;'
            )
        ),
        array(
            'class'=>'application.widgets.ButtonColumn',
            'template' => '{view}{update}{post}',
            'buttons' => array(
                'view' => array('icon' => 'fa fa-eye'),
                'update' => array(
                    'icon' => 'fa fa-pencil',
                    'visible' => function($row, $data) {
                        return !$data->is_posted;
                    }
                ),
//                'post' => array(
//                    'url' => function($data) {
//                        return array('post', 'id' => $data->refno);
//                    },
//                    'icon' => 'fa fa-thumb-tack',
//                    'visible' => function($row, $data) {
//                        return !$data->is_posted;
//                    }
//                ),
            ),
            'htmlOptions' => array(
                'width' => '6%'
            )
        ),
    ),
));

$this->endWidget();
?>

    </div>
</div>
