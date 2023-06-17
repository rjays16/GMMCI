<?php

/* @var $this Controller */

$this->beginWidget(
    'bootstrap.widgets.TbBox',
    array(
        'title' => 'Claim statistics',
        'headerIcon' => 'fa fa-bar-chart-o',
    )
);

?>

<?php
$this->widget('application.extensions.morris.MorrisChartWidget', array(
    'id'      => 'area-graph',
    'options' => array(
        'chartType' => 'Area',
        'data'      => array(
            array('period' => '2012 Q1', 'paid' => 2666, 'return'=> null, 'deny' => 2647),
            array('period' => '2012 Q2', 'paid' => 2778, 'return'=> 3597, 'deny' => 2294),
            array('period' => '2012 Q3', 'paid' => 4912, 'return'=> 1969, 'deny' => 2501),
            array('period' => '2012 Q4', 'paid' => 3767, 'return'=> 2647, 'deny' => 5689),
            array('period' => '2013 Q1', 'paid' => 5670, 'return'=> 4293, 'deny' => 1881),
            array('period' => '2013 Q2', 'paid' => 4820, 'return'=> 3795, 'deny' => 1588),
            array('period' => '2013 Q3', 'paid' => 16073, 'return'=> 5967, 'deny' => 5175),
            array('period' => '2013 Q4', 'paid' => 10687, 'return'=> 4460, 'deny' => 2028),
            array('period' => '2014 Q1', 'paid' => 8432, 'return'=> 5713, 'deny' => 1791),
            array('period' => '2014 Q2', 'paid' => 10311, 'return'=> 7195, 'deny' => 200),
        ),
        'xkey' => 'period',
        'ykeys' => array('paid', 'return', 'deny'),
        'labels' => array('Paid', 'Returns', 'Denies'),
        'lineColors' => array(
            '#61AE24',
            '#00A1CB',
            '#E54028',
        )
    ),
    'htmlOptions' => array(
        'style' => 'height:250px'
    )
));

?>

<?php $this->endWidget() ?>