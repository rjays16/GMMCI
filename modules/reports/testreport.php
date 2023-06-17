<?php
    require_once('./roots.php');

    $report = "History_of_ Smoking";
    #$params = array('from_date' => '2012-02-01',
    #                'to_date' => '2012-02-07');
    $from_date = '2011-01-01';
    $to_date = '2012-05-31';
    $params[] = array("from_date", $from_date, 'java.lang.String');
    $params[] = array("to_date", $to_date, 'java.lang.String');
    
    include($root_path.'modules/reports/render.php');
?>
