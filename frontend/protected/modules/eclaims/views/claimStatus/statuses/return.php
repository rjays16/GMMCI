<h5>Details</h5>

<?php

// Yii::app()->user->setFlash('warning', '<strong>Return Claim Status: NOT IMPLEMENTED</strong>');

$trail = CJSON::decode($claim->status->return->deficiencies_json);
if (empty($trail)) {
    $trail = array();
}
$dp = new CArrayDataProvider($trail);
$columns = array(
    array('name'=>'deficiency', 'header'=>'Deficiency', 'htmlOptions'=>array('style'=>'width:50%')),
    array('name'=>'requirements', 'header'=>'Requirements', 'value' => function($data, $row) {
        echo "<pre>".implode("\n",$data['requirements'])."</pre>"; 
    }, 'type' => 'text'),
);
$this->widget(
    'bootstrap.widgets.TbGridView',
    array(
        'dataProvider' => $dp,
        'template' => "{items}",
        'type' => 'striped bordered condensed',
        'columns' => $columns,
    )
);