<?php



?>


<h5>Claim Process Trail</h5>

<?php


$trail = CJSON::decode($claim->status->inProcess->process_trail_json);
if (empty($trail)) {
    $trail = array();
}
$dp = new CArrayDataProvider($trail);
$columns = array(
    array('name'=>'stage', 'header'=>'Process Stage', 'htmlOptions'=>array('style'=>'width:50%')),
    array('name'=>'date', 'header'=>'Date', 'value' => function($data, $row) {
        return strtotime($data['date']);
    }, 'type' => 'date'),
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
