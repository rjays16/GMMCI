<?php

/* @var $this Controller */

$this->beginWidget(
    'bootstrap.widgets.TbBox',
    array(
        //'title' => 'Daily transmittal uploads',
        'title' => 'Transmittal Upload Status',
        'headerIcon' => 'fa fa-bar-chart-o',
    )
);

?>

<?php
$dataTransmittal =array();
$totalTransmittal = 0;
foreach($transmittalStatuses as $transmittalStatus => $count) {
    $totalTransmittal += $count;
}

foreach($transmittalStatuses as $transmittalStatus => $count) {
    $transmittalStatus = ($transmittalStatus == 'notUploaded') ? "Not Uploaded" : $transmittalStatus;
    $dataTransmittal[] = array(
        'label' => $transmittalStatus, 
        'value' => $count,
        'total' => $totalTransmittal);

}
if( $totalTransmittal==0 ){
    $format = "js:function(y, data) { var pct = 0 * 100; return data.value + '/' + data.total + ' (' + pct.toFixed(0) + '%)'; }";
}
else{
    $format = "js:function(y, data) { var pct = (data.value/data.total) * 100; return data.value + '/' + data.total + ' (' + pct.toFixed(0) + '%)'; }";
}
$this->widget('application.extensions.morris.MorrisChartWidget', array(
    'id'      => 'chartUploadedTransmittals',
    'options' => array(
        'chartType' => 'Donut',
        'data'      => array(
            $dataTransmittal[0],
            $dataTransmittal[1],
            $dataTransmittal[2]
        ),
        'colors' => array(
            '#eee',
            '#0088cc',
            '#cc0',
            
        ),
        'formatter' => $format,
     ),
    'htmlOptions' => array(
        'style' => 'height:220px'
    )
));

?>

<?php $this->endWidget() ?>
