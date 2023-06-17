<?php

/* @var $this Controller */

$this->beginWidget(
    'bootstrap.widgets.TbBox',
    array(
        'title' => 'Statistics',
        'headerIcon' => 'fa fa-bar-chart-o',
    )
);

?>


<?php
/* @var $this Controller */
$this->widget('application.extensions.sparklines.SparklineWidget', array(
    'data' => array(
        array(5,4),
        array(4,3),
        array(7,5),
        array(3,6),
        array(6,4),
        array(5,6),
        array(2,8),
        array(3,4),
        array(4,6),
        array(2,6),
        array(4,4),
        array(2,7),
        array(5,8),
        array(2,4),
    ),
    'options' => array(
        'type' => 'bar',
        'width' => '96%',
        'height' => '200px',
        'barWidth' => 20,
        'stackedBarColor' => array('#92A2A8', '#4493B1'),

    )
));


?>


<?php $this->endWidget() ?>