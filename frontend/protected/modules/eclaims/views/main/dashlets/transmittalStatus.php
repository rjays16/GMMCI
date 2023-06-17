<?php

/* @var $this Controller */

$this->beginWidget(
    'bootstrap.widgets.TbBox',
    array(
        'title' => 'e-Claims Status',
        'headerIcon' => 'fa fa-bar-chart-o',
    )
);

?>

<?php
Yii::import('eclaims.models.Claim');
Yii::import('eclaims.models.ClaimStatus');

$data = array(
    array('label' => 'In process', 'value' => ClaimStatus::model()->count('status=:status', array(':status'=>'in process'))),
    array('label' => 'Return', 'value' => ClaimStatus::model()->count('status=:status', array(':status'=>'return'))),
    array('label' => 'Denied', 'value' => ClaimStatus::model()->count('status=:status', array(':status'=>'denied'))),
    array('label' => 'With Cheque', 'value' => ClaimStatus::model()->count('status=:status', array(':status'=>'with cheque'))),
    array('label' => 'With Voucher', 'value' => ClaimStatus::model()->count('status=:status', array(':status'=>'with voucher'))),
    array('label' => 'With Payment', 'value' => ClaimStatus::model()->count('status=:status', array(':status'=>'vouchering'))),
);

$total=0;

foreach ($data as $i=>$datum) {
    $total += $datum['value'];
}

foreach ($data as $i=>$datum) {
    $data[$i]['total'] = $total;
}

if(ClaimStatus::model()->count() != 0){
    $format = "js:function(y, data) { var pct = (data.value/data.total) * 100; return data.value + '/' + data.total + ' (' + pct.toFixed(0) + '%)'; }";
}
else{
    $format = "js:function(y, data) { var pct = 0 * 100; return data.value + '/' + data.total + ' (' + pct.toFixed(0) + '%)'; }";
}
$this->widget('application.extensions.morris.MorrisChartWidget', array(
    'id'      => 'chartTransmittalStatus',
    'options' => array(
        'chartType' => 'Donut',
        'data'      => $data,
        'colors' => array(
            '#C6DCD7',
            '#B5DC10',
            '#E42200',
            '#05349E',
            '#917EC8',
            '#7FC31C',
        ),
            'formatter' => $format,
    ),
    'htmlOptions' => array(
        'style' => 'height:220px'
    )
));

?>

<?php $this->endWidget() ?>
