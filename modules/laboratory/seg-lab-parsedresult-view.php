<?php
    #for cron schedule
    #per minute
	# created by VAN 01-12-2012
    error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
    require('./roots.php');
    
    define('NO_2LEVEL_CHK',1);

    $local_user='ck_lab_user';
    require($root_path.'include/inc_environment_global.php');
    require_once($root_path.'include/inc_front_chain_lang.php');

    require_once($root_path.'include/care_api_classes/class_hospital_admin.php');
    $objInfo = new Hospital_Admin();
    
    require_once($root_path.'include/care_api_classes/class_labservices_transaction.php');
    $srvObj=new SegLab();

    require_once($root_path.'include/care_api_classes/seg_hl7/seg_class_parse_hl7_message.php');
    $parseObj = new seg_parse_msg_HL7();

    require_once($root_path.'include/care_api_classes/seg_hl7/seg_class_hl7.php');
    $hl7fxnObj = new seg_HL7();

    global $db;

    require_once($root_path.'gui/smarty_template/smarty_care.class.php');
    $smarty = new smarty_care('common');

    $title = "Results";
    $breakfile = '';

    # Title in toolbar
    $smarty->assign('sToolbarTitle',"Laboratory::$title");

    # hide return  button
    $smarty->assign('pbBack',FALSE);

    # href for close button
    $smarty->assign('breakfile',$breakfile);

    # Window bar title
    $smarty->assign('sWindowTitle',"Laboratory::$title");

    # Assign Body Onload javascript code
    $onLoadJS='onLoad="preset();"';
    $smarty->assign('sOnLoadJs',$onLoadJS);

    $smarty->assign('bHideTitleBar',TRUE);
    $smarty->assign('bHideCopyright',TRUE);
     ob_start();

?>
    <!-- OLiframeContent(src, width, height) script:
     (include WIDTH with its parameter equal to width, and TEXTPADDING,0, in the overlib call)
    -->
    <script type="text/javascript" src="<?=$root_path?>js/overlibmws/iframecontentmws.js"></script>

    <!-- Core module and plugins:
    -->
    <script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws.js"></script>
    <script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_draggable.js"></script>
    <script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_filter.js"></script>
    <script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_overtwo.js"></script>
    <script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_scroll.js"></script>
    <script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_shadow.js"></script>
    <script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_modal.js"></script>

    <script type="text/javascript" src="<?=$root_path?>js/jsprototype/prototype.js"></script>

    <link rel="stylesheet" href="<?= $root_path ?>js/jquery/themes/seg-ui/jquery.ui.all.css" type="text/css" />
    <script type="text/javascript" src="<?=$root_path?>js/jquery/jquery.js"></script>
    <script type="text/javascript" src="<?=$root_path?>js/jquery/ui/jquery-ui.js"></script>
    <script type="text/javascript">
        var $J = jQuery.noConflict();
    </script>

    <script type="text/javascript" src="js/lab-parsedresult-view.js?t=<?=time()?>"></script>

<?php
    
    $details = (object) 'details';
    
    if ($row_hosp = $objInfo->getAllHospitalInfo()) {
        $row_hosp['hosp_agency'] = strtoupper($row_hosp['hosp_agency']);
        $row_hosp['hosp_name']   = strtoupper($row_hosp['hosp_name']);
    }else {
        $row_hosp['hosp_country'] = "Republic of the Philippines";
        $row_hosp['hosp_agency']  = "DEPARTMENT OF HEALTH";
        $row_hosp['hosp_name']    = "Gonzales Maranan Medical Center Incorporated";
        $row_hosp['hosp_addr1']   = "Quezon Ave., Digos City, Davao del Sur";
    }

    #header
    $smarty->assign('shosp_country',$row_hosp['hosp_country']);
    $smarty->assign('shosp_agency',$row_hosp['hosp_agency']);
    $smarty->assign('shosp_name',$row_hosp['hosp_name']);
    $smarty->assign('shosp_addr1',$row_hosp['hosp_addr1']);

    $hrn = $_GET['pid'];
    $lis_order_no = $_GET['lis_order_no'];

    $rs = $hl7fxnObj->getAllResultByOrder($hrn, $lis_order_no);

    if (is_object($rs)){    
        
        $numrows = $hl7fxnObj->count;

        $row_pathologist = $srvObj->getPathologist();

        if ($row_pathologist['other_title'])
            $title = ", ".$row_pathologist['other_title'];
        
        $prefix_pathologist = "SGD ";               
        $pathologist = $prefix_pathologist.$row_pathologist['fullname'].$title;
        
        $i=1;
        while($row=$rs->FetchRow()){

            $message = $row['hl7_msg'];
            $filename = $row['filename'];

            #parse result starts here
            $segments = explode($parseObj->delimiter, trim($message));
            
            $counter_obx = 1;
            $counter_nte = 1;
            $cnt=1;
            $cnt2=1;

            foreach($segments as $segment) {
                $data = explode('|', trim($segment));
                
                if (in_array("MSH", $data)) {
                    $msh[$i] = $parseObj->segment_msh($data);
                }

                if (in_array("MSA", $data)) {
                    $msa[$i] = $parseObj->segment_msa($data);
                }

                if (in_array("PID", $data)) {
                    $pidsegment[$i] = $parseObj->segment_pid($data);
                }

                if (in_array("OBR", $data)) {
                    $obr[$i] = $parseObj->segment_obr($data);
                }

                if (in_array("OBX", $data)) {
                    $obx[$i][$counter_obx] = $parseObj->segment_obx($data);
                    $counter_obx++;
                }

                if (in_array("NTE", $data)) {
                    $nte[$i][$counter_nte] = $parseObj->segment_nte($data,$counter_obx);
                    $counter_nte++;
                }
            }
            #=========================

            $patient_name = $row['patient_name'];
            $date_update = $row['date_update'];
                        
            $date_received = date("m-d-Y, h:i A", strtotime($obr[$i]['date_received']));
            $date_reported = date("m-d-Y, h:i A", strtotime($msh[$i]['date_reported']));

            $arr_test = explode($parseObj->COMPONENT_SEPARATOR, trim($obr[$i]['test']));
            
            $testcode = $arr_test[0];
            $testname = $arr_test[1];
            
            $rstest = $srvObj->getLabGroup($testcode);
            $testgroup = $rstest['name'];

            $sex =  (($pidsegment[$i]['sex']=='M')?'Male':
                    (($pidsegment[$i]['sex']=='F')?'Female':'Unspecified'));

            $sql_age = "SELECT fn_get_age(DATE(".$db->qstr($date_update)."),DATE(".$db->qstr($pidsegment[$i]['bdate']).")) AS age";
            $age = $db->GetOne($sql_age);
            
            $arr_physician = explode($parseObj->COMPONENT_SEPARATOR, trim($obr[$i]['physician']));
            $physician = $arr_physician[1];
            
            $arr_loc = explode($parseObj->COMPONENT_SEPARATOR, trim($obr[$i]['location']));
            $location = $arr_loc[1];
            
            $date_released = date("m-d-Y  h:i A", strtotime($date_update));
            
            $space = '&nbsp;&nbsp;&nbsp;&nbsp;';

            $labtestdetails = '';
            
            for ($cnt2=1; $cnt2 < $counter_nte; $cnt2++){
                $comments = str_ireplace('\\.br\\', ' * ', stripslashes($nte[$i][$cnt2]['comment']));
                $comments = str_ireplace('\\', '', $comments);

                $index = $nte[$i][$cnt2]['index'];           
                $notes[$i][$index] = '<tr>
                                        <td colspan="4">'.$space.$space.$space3.$space.$space.$space3.$comments.'</td>
                                      </tr>    
                                       '; 
                $notes[$i][$index2] = $index;
            }
            
            unset($parenttest);
            for ($cnt=1; $cnt < $counter_obx; $cnt++){
                $arr_testservice = explode($parseObj->COMPONENT_SEPARATOR, trim($obx[$i][$cnt]['testservice']));
                $testservice = $arr_testservice[1];
                $testcode = $arr_testservice[0];

                $sql_test = $srvObj->getTestCode($testcode);
                $code = $sql_test['service_code'];   
                
                if (stripslashes($obx[$i][$cnt]['result'])!='\"\"'){
                    $result = stripslashes($obx[$i][$cnt]['result']);

                    if ($result=='""')
                        $result = str_ireplace('""', '', $result);
                }else{
                    $result = '';
                }

                $sql_childtest = "SELECT fn_get_labtest_child_code_all(".$db->qstr(trim($testcode)).") AS childtest";
                $childtest = $db->GetOne($sql_childtest);   
                
                $arr_testchild = explode(",",$childtest);
                
                if ($arr_testchild[0]=='')
                    unset($arr_testchild);
                
                if (in_array($code, $parenttest))
                    $space3 = $space;
                else
                    $space3 = '';
                
                
                $units = str_ireplace('\\S\\', $parseObj->COMPONENT_SEPARATOR, stripslashes($obx[$i][$cnt]['units']));
                $units = str_ireplace('\\', '', $units);

                if (substr($arr_testservice[1], 0, 1)==' ')
                    $space4 = $space.$space;
                else
                    $space4 = '';    
                
                if ($obx[$i][$cnt]['result_flag']=='N')      
                    $flag = '';
                else
                    $flag = $obx[$i][$cnt]['result_flag'];
                
                if ($obx[$i][$cnt]['result']!='!'){   

                if ($units!=''){
                    $td = '<td width="30%">'.$result.'</td>
                           <td width="50%">'.$units.'</td>';
                }else{
                    $td = '<td width="80%">'.$result.'</td>';           
                }   

                $labtestdetails .= '<table border="0" cellspacing="0" cellpadding="0" width="99%" align="center">
                                        <tr>
                                            <td width="33%">'.$space.$space.$space3.$space4.$testservice.'</td>
                                            <td width="33%">
                                                <table border="0" cellspacing="2" cellpadding="2" width="99%" align="center">
                                                    <tr>
                                                        <td width="20%">'.$flag.'</td>
                                                        '.$td.'
                                                    </tr>
                                                </table>
                                            </td>
                                            <td width="33%">'.$space.$space.$space.$space.$obx[$i][$cnt]['reference_range'].'</td>
                                        </tr>   
                                            '.$notes[$i][$cnt+1].'  
                                    </table>     
                                ';
                }      

                if (sizeof($arr_testchild)){
                    $parenttest = $arr_testchild;
                }

            } 

            $medtechobj = trim($obx[$i][$counter_obx-1]['medtech']);  
            $arr_medtech = explode($parseObj->COMPONENT_SEPARATOR, $medtechobj);
            $medtech = $arr_medtech[1]; 

            $labresults .= '<table border="0" cellspacing="2" cellpadding="2" width="99%" align="center">
                                <tbody>
                                    <tr>
                                        <td width="10%">Name</td>
                                        <td width="40%"><strong>:<span style="font-size:15px;">'.$space.$patient_name.'</span></strong></td>
                                        <td width="10%">Lab No</td>
                                        <td width="40%"><strong>:'.$space.$obr[$i]['lab_no'].'</strong></td>
                                    </tr>
                                    <tr>
                                        <td width="10%">PID</td>
                                        <td width="40%"><strong>:'.$space.$hrn.'</strong></td>
                                        <td width="10%">Location</td>
                                        <td width="40%"><strong>:'.$space.$location.'</strong></td>
                                    </tr>
                                    <tr>
                                        <td colspan="2" width="50%">
                                            <table border="0" width="100%">
                                                <tr>
                                                    <td width="20%">Age</td>
                                                    <td width="30%"><strong>:'.$space.$age.' old</strong></td>
                                                    <td width="20%">Sex</td>
                                                    <td width="30%"><strong>:'.$space.$sex.'</strong></td>
                                                </tr>   
                                            </table>    
                                        </td>
                                        <td width="10%">Physician</td>
                                        <td width="40%"><strong>:'.$space.$physician.'</strong></td>
                                    </tr>
                                </tbody>
                            </table>
                            <br>
                            <table border="0" cellspacing="2" cellpadding="2" width="98%" align="center">
                            <thead>
                                <tr>
                                    <td colspan="4" width="100%">
                                        <table border="0" width="100%">
                                            <tr>
                                                <td width="6%">Date Received</td>
                                                <td width="10%"><strong>:'.$space.$date_received.'</strong></td>
                                                <td width="6%">Date Reported</td>
                                                <td width="10%"><strong>:'.$space.$date_reported.'</strong></td>
                                                <td width="6%">Date Released</td>
                                                <td width="10%"><strong>:'.$space.$date_released.'</strong></td>
                                            </tr>   
                                        </table>    
                                    </td>
                                </tr>   
                                <tr>
                                    <td width="33%" style="padding: 5px; border-top: 2px dashed #585E66; border-bottom: 2px dashed #585E66;">'.$space.$space.$space.'<strong>TEST</strong></td>
                                    <td width="33%" style="padding: 5px; border-top: 2px dashed #585E66; border-bottom: 2px dashed #585E66;">'.$space.$space.$space.'<strong>RESULT</strong></td>
                                    <td width="33%" style="padding: 5px; border-top: 2px dashed #585E66; border-bottom: 2px dashed #585E66;">'.$space.$space.$space.'<strong>REFERENCE RANGE</strong></td>
                                </tr>
                            </thead>
                            <tbody>
                                <table border="0" cellspacing="2" cellpadding="2" width="99%" align="center">
                                                <tr>
                                                    <td colspan="3"><strong>'.$space.$testgroup.'</strong></td>
                                                </tr>
                                                <tr>
                                                    <td colspan="3">&nbsp;</td>
                                                </tr>
                                                <tr>
                                                    <td colspan="3"><strong>'.$space.$testname.'</strong></td>
                                                </tr>
                                                '.$labtestdetails.'
                                                </table>
                            </tbody>
                        </table>
                        <br><br>
                        <table border="0" cellspacing="2" cellpadding="2" width="99%" align="center">
                            <tr>
                                <td width="40%" align="center"><strong>'.mb_strtoupper($medtech).'</strong></td>
                                <td width="20%">&nbsp;</td>
                                <td width="40%" align="center"><strong>'.mb_strtoupper($pathologist).'</strong></td>
                            </tr>   
                            <tr>
                                <td width="40%" align="center">Medical Technologist</td>
                                <td width="20%">&nbsp;</td>
                                <td width="40%" align="center">Pathologist</td>
                            </tr>   
                        </table>
                        <br><hr>';

            $i++;
        }
            
        $smarty->assign('sResultDetails',$labresults);
        
        $smarty->assign('sDisclaimer','*** This is electronically generated report. No signature is required but it can\'t be printed. ***');

        $smarty->assign('space',$space);
        $smarty->assign('space2','&nbsp;&nbsp;&nbsp;');
    }else{
        $text2 = "The HL7 Laboratory result is not parsed or analyzed yet. Status is pending. Try to view later. Thank you.";
        echo "<html><head></head><body><strong><font color='RED' size='5px'>".$text2."</font></strong></body></html>";
    }

    # Assign the form template to mainframe
    $smarty->assign('sMainBlockIncludeFile','laboratory/lab-parsedresult-view.tpl');
    $smarty->display('common/mainframe.tpl');
?>
