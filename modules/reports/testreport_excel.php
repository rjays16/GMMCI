<?php
    require_once('./roots.php');

    $report = "OPD_Demographic";
    #$params = array('from_date' => '2012-02-01',
    #                'to_date' => '2012-02-07');
    $from_date = '2012-02-01';
    $to_date = '2012-02-07';
    $params[] = array("from_date", $from_date, 'java.lang.String');
    $params[] = array("to_date", $to_date, 'java.lang.String');

    #include($root_path.'modules/reports/render.php');
    include($root_path.'modules/reports/render_excel.php');
?>
