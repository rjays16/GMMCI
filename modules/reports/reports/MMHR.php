<?php
#created by daryl
#reports for MANDATORY MONTHLY HOSPITAL REPORT
    require_once('./roots.php');
    require_once($root_path.'include/inc_environment_global.php');

    include('parameters.php');
    #hcare_id for  PHIC
    define("hcare_id", "18");
     
   
if($mmhr_page == 'page1')
    include('MMHR_page1.php');
elseif($mmhr_page == 'page2')
    include('MMHR_page2.php');
    