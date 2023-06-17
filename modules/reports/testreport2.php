<?php
    require_once('./roots.php');

    $report = "testrep";
    
    // 2 - dimensional array ...
    $params = array();   // For every row:  
                         //     0th element -- name of parameter as specified in .jrxml report.
                         //     1st element -- parameter value
                         //     2nd element -- any of the ff.: java.lang.String, java.lang.Boolean, java.lang.Integer, 
                         //                                    java.lang.Short, java.lang.Long, java.lang.Double, 
                         //                                    java.lang.BigDecimal, java.sql.Timestamp, java.sql.Time,
                         //                                    java.util.Date
    include($root_path.'modules/reports/render.php');
?>
