<?php
/* @var $this OrRequestController */
/* @var $dataProvider CActiveDataProvider */

Yii::app()->clientScript->registerScriptFile(Yii::app()->request->baseUrl . '/frontend/scripts/orRequest/index.js');

$this->breadcrumbs=array(
	'Or Requests',
);

$this->menu=array(
	array('label'=>'Create OrRequest', 'url'=>array('create')),
	array('label'=>'Manage OrRequest', 'url'=>array('admin')),
);
?>

<input type="hidden" id="flag" value="<?= $flag; ?>" />

<?php
$this->widget(
    'bootstrap.widgets.TbGridView',
    array(
        'id'=>'request-list',
        'type' => 'striped',
        'dataProvider' => $gridDataProvider,
        'template' => "{items}{pager}",
        'columns' => $gridColumns,
        'htmlOptions'=>array(
            //'style'=>'display:none'
        )
    )
);
?>

<h1>Or Requests</h1>
<div class="well"><?php
    $this->widget(
        'bootstrap.widgets.TbTabs',
        array(
            'type' => 'tabs', // 'tabs' or 'pills'
            'tabs' => array(
                array(
                    'label' => 'Pending',
                    'id'=>'pending-tab',
                    'content' => '',
                    'active' => ($flag == 'pending')?true:false,
                    'class'=>'test'
                ),
                array(
                    'label' => 'Approved',
                    'id'=>'approved-tab',
                    'content' => '',
                    'active' => ($flag == 'approved')?true:false,
                ),
                array(
                    'label' => 'Scheduled',
                    'id'=>'scheduled-tab',
                    'content' => '',
                    'active' => ($flag == 'scheduled')?true:false,
                ),
                array(
                    'label' => 'Pre-operative',
                    'id'=>'preop-tab',
                    'content' => '',
                    'active' => ($flag == 'preop')?true:false,
                ),
                array(
                    'label' => 'Post-operative',
                    'id'=>'postop-tab',
                    'content' => '',
                    'active' => ($flag == 'postop')?true:false,
                ),
                array(
                    'label' => 'Done',
                    'id'=>'done-tab',
                    'content' => '',
                    'active' => ($flag == 'done')?true:false,
                ),
                array(
                    'label' => 'Removed',
                    'id'=>'deleted-tab',
                    'content' => '',
                    'active' => ($flag == 'deleted')?true:false,
                ),
            ),
        )
    );
    ?>
</div>