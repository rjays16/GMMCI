<?php
	# Start Smarty templating here
 /**
 * LOAD Smarty
 */

 # Note: it is advisable to load this after the inc_front_chain_lang.php so
 # that the smarty script can use the user configured template theme
	error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
	require('./roots.php');

/**
* CARE2X Integrated Hospital Information System Deployment 2.1 - 2004-10-02
* GNU General Public License
* Copyright 2002,2003,2004,2005 Elpidio Latorilla
* elpidio@care2x.org,
*
* See the file "copy_notice.txt" for the licence notice
*/

	$lang_tables[] = 'departments.php';
	define('LANG_FILE','lab.php');
	define('LANG_FILE','konsil.php');
	define('NO_2LEVEL_CHK',1);

	$local_user='ck_lab_user';
	require($root_path.'include/inc_environment_global.php');
	require_once($root_path.'include/inc_front_chain_lang.php');
	require($root_path.'modules/bloodBank/ajax/blood-received-sample.common.php');

	$dbtable='care_config_global'; // Taboile name for global configurations
	$GLOBAL_CONFIG=array();
	$new_date_ok=0;

	# Create global config object
	require_once($root_path.'include/care_api_classes/class_globalconfig.php');
	require_once($root_path.'include/inc_date_format_functions.php');

	$glob_obj=new GlobalConfig($GLOBAL_CONFIG);
	$glob_obj->getConfig('refno_%');
	if($glob_obj->getConfig('date_format')) $date_format=$GLOBAL_CONFIG['date_format'];
	$date_format=$GLOBAL_CONFIG['date_format'];

	$phpfd=$date_format;
	$phpfd=str_replace("dd", "%d", strtolower($phpfd));
	$phpfd=str_replace("mm", "%m", strtolower($phpfd));
	$phpfd=str_replace("yyyy","%Y", strtolower($phpfd));
    
    $breakfile=$root_path.'modules/laboratory/labor.php'.URL_APPEND;
    $thisfile=basename(__FILE__);

	# Create laboratory object
	require_once($root_path.'include/care_api_classes/class_labservices_transaction.php');
	$srvObj=new SegLab();

	global $db, $allow_updateBBDates;

	require_once($root_path.'gui/smarty_template/smarty_care.class.php');
	$smarty = new smarty_care('common');

	if ($_GET['refno'])
		$refno=$_GET['refno'];
    elseif ($_POST['refno'])
        $refno=$_POST['refno'];    

	if ($_GET['service_code'])
		$service_code = $_GET['service_code'];
    elseif ($_POST['service_code'])
        $service_code = $_POST['service_code'];    

    $row_px = $srvObj->getPatientInfoRefno($refno, $service_code);
    $quantity = $row_px['quantity'];    
	 # Assign Body Onload javascript code
	 #$onLoadJS='';
	 #$smarty->assign('sOnLoadJs',$onLoadJS);
     $smarty->assign('sOnLoadJs','onLoad="preset();"');
     
     if ($_POST["submitted"]){
        $arraySampleItems_h = array();
        $arraySampleItems_sh = array();
        $arraySampleItems_d = array();
        $hasrec = 0;
        $islack = 0;
        $with_sample_rec = 0;
        $received_qty = 0;
        
        for($i=1;$i<=$quantity;$i++){
            
          $id = $service_code.$i;
          
          if ($_POST['is_received'.$id]){
            $status_rec_d = "received";
          }else
            $status_rec_d = "not yet";  
          
          
          
          $ordering = $_POST['index'.$id];
          $serial = $_POST['serial'.$id];
          $component = $_POST['component'.$id];
          $result = $_POST['result'.$id];
          
          if (!$result)
            $result = 'noresult';
          
          #date received
          $date_received = $_POST['date_received'.$id];
          $time_received = $_POST['time_received'.$id];
          $meridian = $_POST['meridian'.$id];
          
          $datetime =  $date_received." ".$time_received." ".$meridian;
          $date = trim($date_received." ".$time_received);

          if (empty($date))
            $received_date = "0000-00-00 00:00:00";
          else  {
            $received_date = date("Y-m-d H:i:s", strtotime($datetime));
         
        }
          
          
          $arraySampleItems_d[] = array($ordering, $received_date, $component, $serial, $status_rec_d, $result);
        }
        
        #$status_sh = 'active';
        $received_qty = $_POST['received_qty'];
        $_POST['ordered_qty'] = $quantity;
        
        if ($received_qty == 0){
            $hasrec = 0;
            $islack =+ 1;
        }elseif ($received_qty < $quantity){
            $hasrec =+ 1;
            $islack =+ 1;
            $with_sample_rec =+ 1;
        }elseif ($received_qty == $quantity){
            $hasrec =+ 1;
            $iscomplete =+ 1;
            $with_sample_rec =+ 1;
        } 
        
        
        if ($hasrec == 0)
            $status_rec = 'none';
         elseif ($islack)
            $status_rec = 'lack';
         elseif ($iscomplete)
            $status_rec = 'complete';
        
        $status_sh = $status_rec;    
        $_POST['status_sh'] = $status_sh;
        $_POST['status_rec'] = $status_rec;   
        
        $_POST['arraySampleItems_d'] = $arraySampleItems_d;        
        
        $ok = $srvObj->updatebloodReceivedSample($refno, $service_code, $_POST);
        
        if ($ok)
            $smarty->assign('sysInfoMessage',"Blood Request Service successfully created.");
        else
            $smarty->assign('sysErrorMessage','<strong>Error:</strong> '.$srvObj->sql);    
     }
     
	 if ($popUp){
		 $smarty->assign('bHideTitleBar',TRUE);
		 $smarty->assign('bHideCopyright',TRUE);
	 }
     
     # Collect javascript code
     ob_start();
	 # Load the javascript code
	 $xajax->printJavascript($root_path.'classes/xajax_0.5');
     
    $sql_mc = "SELECT return_reason
                           FROM seg_blood_received_status 
                           WHERE refno=".$db->qstr($refno);                             
    $show = $db->GetOne($sql_mc);

     
?>

<script language="javascript"> 
<?php
	require_once($root_path.'include/inc_checkdate_lang.php');
?>
</script>

<script type="text/javascript" src="<?=$root_path?>js/jsprototype/prototype.js"></script>

<link rel="stylesheet" href="<?= $root_path ?>js/jquery/themes/seg-ui/jquery.ui.all.css" type="text/css" />
<script type="text/javascript" src="<?=$root_path?>js/jquery/jquery.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jquery/ui/jquery-ui.js"></script>
<script type="text/javascript">
var $J = jQuery.noConflict();
</script>
<script type="text/javascript" src="<?=$root_path?>js/jquery/jquery.maskedinput.js"></script>

<script type="text/javascript" src="<?=$root_path?>js/checkdate.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/setdatetime.js"></script>

<link rel="stylesheet" type="text/css" href="<?=$root_path?>js/jscal2/css/jscal2.css" />
<link rel="stylesheet" type="text/css" href="<?=$root_path?>js/jscal2/css/border-radius.css" />
<link rel="stylesheet" type="text/css" href="<?=$root_path?>js/jscal2/css/steel/steel.css" />
<script type="text/javascript" src="<?=$root_path?>js/jscal2/js/jscal2.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jscal2/js/lang/en.js"></script>

<script type="text/javascript" src="<?= $root_path ?>modules/bloodBank/js/blood-received-sample.js" ></script>
<script type="text/javascript" src="<?= $root_path ?>js/dateformat.js" ></script>
<script type="text/javascript" src="<?= $root_path ?>js/datefuncs.js" ></script> 

<script type="text/javascript" src="<?=$root_path?>js/overlibmws/iframecontentmws.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_draggable.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_filter.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_overtwo.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_scroll.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_shadow.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_modal.js"></script>
<?php

    function level_label($index){
        switch($index){
            case '1' :  $label_index = 'First'; break;
            case '2' :  $label_index = 'Second'; break;
            case '3' :  $label_index = 'Third'; break;
            case '4' :  $label_index = 'Fourth'; break;
            case '5' :  $label_index = 'Fift'; break;
            case '6' :  $label_index = 'Sixth'; break;
            case '7' :  $label_index = 'Seventh'; break;
            case '8' :  $label_index = 'Eighth'; break;
            case '9' :  $label_index = 'Ninth'; break;
            case '10' : $label_index = 'Tenth'; break;
        }

        return $label_index;
    }

	$sTemp = ob_get_contents();
	ob_end_clean();
	$smarty->append('JavaScript',$sTemp);
    
    $smarty->assign('sPatientName',mb_strtoupper($row_px['patient_name']));
    $smarty->assign('sHRN',$row_px['pid']);
    
    $smarty->assign('sAge',$row_px['age']);
    $smarty->assign('sSex',$row_px['sex']);
    $smarty->assign('sBloodType',$row_px['blood_type']);
    
	$smarty->assign('sRefno',$refno);
	$smarty->assign('sTestName',$row_px['test_name']);
	$smarty->assign('sTestCode',$service_code);
    $smarty->assign('sQuantity',$row_px['quantity']);
    
    $date_encoded =date("m/d/Y h:iA", strtotime($row_px['serv_dt']." ".$row_px['serv_tm']));
    $smarty->assign('sDateEncoded',$date_encoded);

    $row_rs = $srvObj->getTestbyRefno($refno, $service_code);
    $norows = $srvObj->FoundRows();
    $date_format2 = '%m/%d/%Y';      
    if ($norows){
        if ($row_rs['quantity']){
            $i=1;
            
            for($i=1; $i<=$row_rs['quantity']; $i++){
                
                $row_i = $srvObj->getBloodReceived($refno, $service_code, $i);
                $row_status = $srvObj->getBloodReceivedStatus($refno, $service_code, $i);
                
                $service_code = $row_rs['service_code'];             
                $no_repeat = 0;
                
                $id = $service_code.$i;
                
                $checkox = '<input '.(($row_i['status']=='received')?'checked="checked" ':'').' type="checkbox" value="1" id="is_received'.$id.'" name="is_received'.$id.'" onClick="setEnable(\''.$id.'\',\''.$no_repeat.'\')">';
                $index_label = level_label($i);
                $label =  $index_label." Test".'<input value="'.$i.'" type="hidden" id="index'.$id.'" name="index'.$id.'">';
                
                #$date_received = '<input type="text" readonly="readonly" maxlength="10" size="8" value="" class="segInput" id="date_received'.$id.'" name="date_received'.$id.'">
                #                  <button disabled class="segButton" id="date_received_trigger'.$id.'"><img height="16" width="16" border="0" src="../../gui/img/common/default/calendar.png">Set</button>';
                
                if (($row_i['received_date']=='0000-00-00 00:00:00')||($row_i['received_date']=='')){
                    $date_received = '';
                    $time_received = '';
                    $meridian = 'AM';
                }else{
                    $date_received = date("m/d/Y",strtotime($row_i['received_date']));
                    $time_received = date("h:i",strtotime($row_i['received_date']));
                    $meridian = date("A",strtotime($row_i['received_date']));
                }
                
                if (($row_status['done_date']=='0000-00-00 00:00:00')||($row_status['done_date']=='')){
                    $date_done = '';
                    $time_done = '';
                    $done_meridian = 'AM';
                }else{
                    $date_done = date("m/d/Y",strtotime($row_status['done_date']));
                    $time_done = date("h:i",strtotime($row_status['done_date']));
                    $done_meridian = date("A",strtotime($row_status['done_date']));
                }
                
                if (($row_status['issuance_date']=='0000-00-00 00:00:00')||($row_status['issuance_date']=='')){
                    $date_issuance = '';
                    $time_issuance = '';
                    $issuance_meridian = 'AM';
                }else{
                    $date_issuance = date("m/d/Y",strtotime($row_status['issuance_date']));
                    $time_issuance = date("h:i",strtotime($row_status['issuance_date']));
                    $issuance_meridian = date("A",strtotime($row_status['issuance_date']));
                }
                //added by:borj 2013/23/11
                if (($row_status['date_return']=='0000-00-00 00:00:00')||($row_status['date_return']=='')){
                    $date_returned = '';
                    $time_returned = '';
                    $returned_meridian = 'AM';
                }else{
                    $date_returned = date("m/d/Y",strtotime($row_status['date_return']));
                    $time_returned = date("h:i",strtotime($row_status['date_return']));
                    $returned_meridian = date("A",strtotime($row_status['date_return']));
                }

                 if (($row_status['date_reissue']=='0000-00-00 00:00:00')||($row_status['date_reissue']=='')){
                    $date_reissue = '';
                    $time_reissue = '';
                    $reissue_meridian = 'AM';
                }else{
                    $date_reissue = date("m/d/Y",strtotime($row_status['date_reissue']));
                    $time_reissue = date("h:i",strtotime($row_status['date_reissue']));
                    $reissue_meridian = date("A",strtotime($row_status['date_reissue']));
                }

                if (($row_status['date_consumed']=='0000-00-00 00:00:00')||($row_status['date_consumed']=='')){
                    $date_consumed = '';
                    $time_consumed = '';
                    $consumed_consumed = 'AM';
                }else{
                    $date_consumed = date("m/d/Y",strtotime($row_status['date_consumed']));
                    $time_consumed = date("h:i",strtotime($row_status['date_consumed']));
                    $consumed_meridian = date("A",strtotime($row_status['date_consumed']));
                }

                //end borj
                                    /*<select disabled class="segInput" name="meridian'.$id.'" id="meridian'.$id.'">
                                        <option '.(($meridian=='AM')?'selected="selected" ':'').' value = "AM">AM</option>
                                        <option '.(($meridian=='PM')?'selected="selected" ':'').' value = "PM">PM</option>
                                    </select>*/
               
                $date_received_row = '<div class="input text">
                                    <div style="display:inline-block">
                                        <input type="text" maxlength="10" size="7" id="date_received'.$id.'" name="date_received'.$id.'" value="'.$date_received.'" class="segInput" readonly>
                                    </div>
                                    <button disabled id="date_received_trigger'.$id.'" name="date_received_trigger'.$id.'" style="cursor: pointer;  width: 25px" onclick="return false" title="Select Received Date">
                                        <img height="16" width="16" border="0" src="../../gui/img/common/default/calendar.png">
                                    </button>
                                    <br>
                                    
                                    <input class="segInput" maxlength="5" size="1" id="time_received'.$id.'" name="time_received'.$id.'" value="'.$time_received.'" type="text" value="">
                                    <input readonly class="segInput" maxlength="5" size="1" id="meridian'.$id.'" name="meridian'.$id.'" value="'.$meridian.'" type="text">
                                  </div>';
                                  
                #date done
                $checkox_done = '<input '.((($date_done!='')&&($date_done!='00/00/0000'))?'checked="checked" ':'').' type="checkbox" value="1" id="is_done'.$id.'" name="is_done'.$id.'" onClick="getDoneCurrentDate(\''.$id.'\')">';
                $date_done_row = '<div class="input text">
                                '.$checkox_done.'
                                    <div style="display:inline-block">
                                        <input readonly="readonly" type="text" maxlength="10" size="7" id="date_done'.$id.'" name="date_done'.$id.'" value="'.$date_done.'" class="segInput">
                                    </div>
                                    <button id="date_done_trigger'.$id.'" name="date_done_trigger'.$id.'" style="cursor: pointer;  width: 25px" onclick="return false" title="Select Date Done">
                                        <img height="16" width="16" border="0" src="../../gui/img/common/default/calendar.png">
                                    </button>
                                    
                                    <img id="date_done_save'.$id.'" name="date_done_save'.$id.'" height="16" width="16" border="0" src="../../gui/img/common/default/disk.png" title="Save Date Done">
                                    <br>
                                    <input readonly="readonly" class="segInput" maxlength="5" size="1" id="time_done'.$id.'" name="time_done'.$id.'" value="'.$time_done.'" type="text" value="">
                                    <select disabled class="segInput" name="done_meridian'.$id.'" id="done_meridian'.$id.'">
                                        <option '.(($done_meridian=='AM')?'selected="selected" ':'').' value = "AM">AM</option>
                                        <option '.(($done_meridian=='PM')?'selected="selected" ':'').' value = "PM">PM</option>
                                    </select> &nbsp;&nbsp;&nbsp;
                                    <img id="date_done_cancel'.$id.'" name="date_done_cancel'.$id.'" height="16" width="16" border="0" src="../../images/close_small.gif"  title="Cancel Date Done">
                                  </div>';                  
                
                # issuance date
                $checkox_issued = '<input '.((($date_issuance!='')&&($date_issuance!='00/00/0000'))?'checked="checked" ':'').' type="checkbox" value="1" id="is_issued'.$id.'" name="is_issued'.$id.'" onClick="getIssuanCurrentDate(\''.$id.'\')">';
                $date_issuance_row = '<div class="input text">
                                    '.$checkox_issued.'
                                    <div style="display:inline-block">
                                        <input readonly="readonly" type="text" maxlength="10" size="7" disabled id="date_issuance'.$id.'" name="date_issuance'.$id.'" value="'.$date_issuance.'" class="segInput">
                                    </div>
                                    <button id="date_issuance_trigger'.$id.'" name="date_issuance_trigger'.$id.'" style="cursor: pointer; width: 25px" onclick="return false" title="Select Issuance Date">
                                        <img height="16" width="16" border="0" src="../../gui/img/common/default/calendar.png">
                                    </button>
                                    
                                    <img id="date_issuance_save'.$id.'" name="date_issuance_save'.$id.'" height="16" width="16" border="0" src="../../gui/img/common/default/disk.png" title="Save Issuance Date">
                                    <br>
                                    <input readonly="readonly" class="segInput" maxlength="5" size="1" id="time_issuance'.$id.'" name="time_issuance'.$id.'" value="'.$time_issuance.'" type="text" value="">
                                    <select disabled class="segInput" name="issuance_meridian'.$id.'" id="issuance_meridian'.$id.'">
                                        <option disabled'.(($issuance_meridian=='AM')?'selected="selected" ':'').' value = "AM">AM</option>
                                        <option disabled'.(($issuance_meridian=='PM')?'selected="selected" ':'').' value = "PM">PM</option>
                                    </select> &nbsp;&nbsp;&nbsp;
                                    <img id="date_issuance_cancel'.$id.'" name="date_issuance_cancel'.$id.'" height="16" width="16" border="0" src="../../images/close_small.gif"  title="Cancel Issuance Date">
                                  </div>';
                  

                //added by: borj 2013/21/11
                #returned date
                  if($date_returned==null){
                    $returnedDateShow = 'none';//hide
                    }
                     else{
                    $returnedDateShow = 'block';//show
                   }
                $checkox_returnedreason = '<input type="checkbox" id="is_returnedreason'.$id.'" title="'.$row_status['return_reason'].'" name="is_returnedreason'.$id.'" onClick="getReturnReason(\''.$mode.'\',\''.$dateinfo.'\',\''.$timeinfo.'\',\''.$id.'\')">';
                $date_returned_row =
                ''.$checkox_returnedreason.'
                <div class="input text" style="display:'.$returnedDateShow.'" id="date_returned_show'.$id.'" name="date_returned_show'.$id.'">
                                                                                                                                      
                                    <div style="display:inline-block">
                                        <input type="text" maxlength="10" size="7" disabled id="date_returned'.$id.'" name="date_returned'.$id.'" value="'.$date_returned.'" class="segInput">
                                    </div>

                                    <button id="date_returned_trigger'.$id.'" name="date_returned_trigger'.$id.'" style="cursor: pointer; width: 25px" onclick="return false" title="Select Returned Date">
                                        <img height="16" width="16" border="0" src="../../gui/img/common/default/calendar.png">
                                    </button>
                                    
                                    <img id="date_returned_save'.$id.'" name="date_returned_save'.$id.'" height="16" width="16" border="0" src="../../gui/img/common/default/disk.png" title="Save Returned Date">
                                    <br>
                                    <input class="segInput" maxlength="5" size="1" id="time_returned'.$id.'" name="time_returned'.$id.'" value="'.$time_returned.'" type="text" value="">
                                    <select class="segInput" name="returned_meridian'.$id.'" id="returned_meridian'.$id.'">
                                        <option disabled'.(($returned_meridian=='AM')?'selected="selected" ':'').' value = "AM">AM</option>
                                        <option disabled'.(($returned_meridian=='PM')?'selected="selected" ':'').' value = "PM">PM</option>
                                    </select> &nbsp;&nbsp;&nbsp;
                                    <img id="date_returned_cancel'.$id.'" name="date_returned_cancel'.$id.'" height="16" width="16" border="0" src="../../images/close_small.gif"  title="Cancel Returned Date">
                                  </div>';
                 #reissue date
                 if($date_returned==null){
                    $reissueDateShow = 'none';//hide
                    }
                     else{
                    $reissueDateShow = 'block';//show
                   }
                $checkox_reissue = '<input '.((($date_reissue!='')&&($date_reissue!='00/00/0000'))?'checked="checked" ':'').' type="checkbox" value="1" id="is_reissue'.$id.'" name="is_reissue'.$id.'" onClick="getReissueCurrentDate(\''.$id.'\')">';
                $date_reissue_row =
                 '<div class="input text" style="display:'.$reissueDateShow.'" id="date_reissue_show'.$id.'"  name="date_reissue_show'.$id.'">
                                    '.$checkox_reissue.'
                                    <div style="display:inline-block">
                                        <input type="text" maxlength="10" size="7" disabled id="date_reissue'.$id.'" name="date_reissue'.$id.'" value="'.$date_reissue.'" class="segInput">
                                    </div>
                                    <button id="date_reissue_trigger'.$id.'" name="date_reissue_trigger'.$id.'" style="cursor: pointer; width: 25px" onclick="return false" title="Select Reissue Date">
                                        <img height="16" width="16" border="0" src="../../gui/img/common/default/calendar.png">
                                    </button>
                                    
                                    <img id="date_reissue_save'.$id.'" name="date_reissue_save'.$id.'" height="16" width="16" border="0" src="../../gui/img/common/default/disk.png" title="Save Reissue Date">
                                    <br>
                                    <input class="segInput" maxlength="5" size="1" id="time_reissue'.$id.'" name="time_reissue'.$id.'" value="'.$time_reissue.'" type="text" value="">
                                    <select class="segInput" name="reissue_meridian'.$id.'" id="reissue_meridian'.$id.'">
                                        <option disabled'.(($reissue_meridian=='AM')?'selected="selected" ':'').' value = "AM">AM</option>
                                        <option disabled'.(($reissue_meridian=='PM')?'selected="selected" ':'').' value = "PM">PM</option>
                                    </select> &nbsp;&nbsp;&nbsp;
                                    <img id="date_reissue_cancel'.$id.'" name="date_reissue_cancel'.$id.'" height="16" width="16" border="0" src="../../images/close_small.gif"  title="Cancel Reissue Date">
                                  </div>';

                #consumed date
                if($date_issuance==null){
                    $consumedDateShow = 'none';//hide
                    }
                     else{
                    $consumedDateShow = 'block';//show
                   }                 
                $checkox_consumed = '<input '.((($date_consumed!='')&&($date_consumed!='00/00/0000'))?'checked="checked" ':'').' type="checkbox" value="1" id="is_consumed'.$id.'" name="is_consumed'.$id.'" onClick="getConsumedCurrentDate(\''.$id.'\')">';
                $date_consumed_row =
                                    '<div class="input text" style="display:'.$consumedDateShow.'"  id="date_consumed_show'.$id.'"  name="date_consumed_show'.$id.'">
                                    '.$checkox_consumed.'
                                    <div style="display:inline-block">
                                        <input type="text" maxlength="10" size="7" disabled id="date_consumed'.$id.'" name="date_consumed'.$id.'" value="'.$date_consumed.'" class="segInput">
                                    </div>
                                     <button id="date_consumed_trigger'.$id.'" name="date_consumed_trigger'.$id.'" style="cursor: pointer; width: 25px" onclick="return false" title="Select Reissue Date">
                                        <img height="16" width="16" border="0" src="../../gui/img/common/default/calendar.png">
                                    </button>
                                    
                                    <img id="date_consumed_save'.$id.'" name="date_consumed_save'.$id.'" height="16" width="16" border="0" src="../../gui/img/common/default/disk.png" title="Save Consumed Date">
                                    <br>
                                    <input class="segInput" maxlength="5" size="1" id="time_consumed'.$id.'" name="time_consumed'.$id.'" value="'.$time_consumed.'" type="text" value="">
                                    <select class="segInput" name="consumed_meridian'.$id.'" id="consumed_meridian'.$id.'">
                                        <option disabled'.(($consumed_meridian=='AM')?'selected="selected" ':'').' value = "AM">AM</option>
                                        <option disabled'.(($consumed_meridian=='PM')?'selected="selected" ':'').' value = "PM">PM</option>
                                    </select> &nbsp;&nbsp;&nbsp;
                                    <img id="date_consumed_cancel'.$id.'" name="date_consumed_cancel'.$id.'" height="16" width="16" border="0" src="../../images/close_small.gif"  title="Cancel Consumed Date">
                                  </div>';
                //end borj
        
                  
                $serial_col = '<input type="text" readonly="readonly" size="14" class="segInput" id="serial'.$id.'" name="serial'.$id.'" value="'.$row_i['serial_no'].'">';
                
                $component = $row_i['component'];
                
                $sql_components = 'SELECT * FROM seg_blood_component';
                $rs_components = $db->Execute($sql_components);
                $components_option="<option value=''>-Select a Component-</option>";
                if (is_object($rs_components)){
                    while ($row_components=$rs_components->FetchRow()) {
                        $selected='';
                        if ($component==$row_components['id'])
                            $selected='selected';
                        
                        $components_option.='<option '.$selected.' value="'.$row_components['id'].'">'.ucwords($row_components['long_name']).'</option>';
                    }
                }
                $components_col = '<select disabled id="component'.$id.'" name="component'.$id.'" class="segInput">
                                        '.$components_option.'
                                   </select>';
                                   
                
                $result = $row_i['result'];
                
                if (!$result)
                    $result = 'noresult';
                
                $sql_result = 'SELECT * FROM seg_blood_result';
                $rs_result = $db->Execute($sql_result);
                $result_option="";
                if (is_object($rs_result)){
                    while ($row_result=$rs_result->FetchRow()) {
                        $selected='';
                        
                        if ($result==$row_result['id'])
                            $selected='selected';
                        
                        $result_option.='<option '.$selected.' value="'.$row_result['id'].'">'.ucwords($row_result['name']).'</option>';
                    }
                }                   
                $result_col = '<select disabled id="result'.$id.'" name="result'.$id.'" class="segInput">
                                        '.$result_option.'
                                   </select>';                   
                                   
                $jsCalScript  = '<script type="text/javascript">
                                        now = new Date();
                                        
                                        Calendar.setup ({
                                                inputField: "date_done'.$id.'",
                                                dateFormat: "'.$date_format2.'",
                                                trigger: "date_done_trigger'.$id.'",
                                                showTime: false,
                                                fdow: 0,
                                                max : Calendar.dateToInt(now),
                                                onSelect: function() { this.hide() }
                                        });
                                        
                                    </script>
                                    ';                                     
                
                $class = (($i%2)==0)?"":"wardlistrow2";
                #edited by nick, class="tdrec", 2/5/14
                $rows .= "<tr class=\"$class\" id=\"row$i\">
                            <td class=\"tdrec\" align=\"center\">$checkox</td>
                            <td align=\"centerAlign\">$label</td>
                            <td align=\"center\">$serial_col</td>
                            <td align=\"center\">$components_col</td>
                            <td align=\"center\">$date_received_row</td>
                            <td align=\"center\">$date_done_row</td>
                            <td align=\"center\">$result_col</td>
                            <td align=\"center\">$date_issuance_row</td>
                            <td align=\"center\">$date_returned_row</td>
                            <td align=\"center\">$date_reissue_row</td>
                            <td align=\"center\">$date_consumed_row</td>
                            </tr>$jsCalScript\n";
            }
        }
    }else{
    
        $rows = "
                    <tr>
                        <td colspan=\"10\">Request list is currently empty...</td>
                    </tr>";
    }

	$smarty->assign('sOrderItems',$rows);
    
    #$submit_btn = '<button class="segButton" id="submit_btn" name="submit_btn" onclick="return submitRequest(\''.$refno.'\',\''.$service_code.'\')" style="margin-left: 4px; cursor: pointer;"><img height="16" width="16" border="0" src="../../images/button_split_small.png">Submit</button>';
    $submit_btn = '<img border="0" src="../../images/btn_submit.gif" onclick="submitRequest(\''.$refno.'\',\''.$service_code.'\');" style="margin-left: 4px; cursor: pointer;">';
    #$close_btn = '<button class="segButton" id="close_btn" name="close_btn" onclick="javascript:void(0); return reset(\''.$service_code.'\');" style="margin-left: 4px; cursor: pointer;"><img height="16" width="16" border="0" src="../../images/close_small.gif">Clear</button>';
    $close_btn = '<img border="0" src="../../images/reset.gif" onclick="reset(\''.$service_code.'\');" style="margin-left: 4px; cursor: pointer;">';
    $smarty->assign('sSubmitButton',$submit_btn);
    $smarty->assign('sCloseButton',$close_btn);

    //added by Nick, 11/23/2013 12:45 AM
    $print_btn = '<img border="0" src="../../images/btn_claim_stab.gif" onclick="printClaimStub2('.$refno.');" style="margin-left: 4px; cursor: pointer;">';
    $smarty->assign('sPrintButton',$print_btn);
    /*
    $jsPrintDialog = '<script type="text/javascript">
                        $J("#printClaimStubDialog").dialog({
                            autoOpen:false,
                            modal:true,
                            title:"Additional Info",
                            width:"auto",
                            hide:"explode",
                            show:{
                                effect:\'blind\',
                                complete:function(){
                                    $(\'patient_name\').value = "'.$row_px['patient_name'].'";
                                    $(\'case_no\').value = "'.$refno.'";
                                    
                                    $(\'crossmatching\').checked = false;
                                    $(\'coombs_test\').checked = false;
                                    $(\'bb_component\').checked = false;
                                    $(\'du_variant\').checked = false;

                                    $(\'cm_count\').value = 0;
                                    $(\'coombs_count\').value = 0;
                                    $(\'bb_component_count\').value = 0;
                                    $(\'du_variant_count\').value = 0;
                                    $(\'others\').value = \'\';
                                }
                            },                            
                            buttons:{
                                Print:function(){

                                    var refno = $(\'case_no\').value;
                                    var cmCheck = $(\'crossmatching\').checked;
                                    var coombsCheck = $(\'coombs_test\').checked;
                                    var compCheck = $(\'bb_component\').checked;
                                    var duCheck = $(\'du_variant\').checked;

                                    var cmVal = $(\'cm_count\').value;
                                    var coombsVal = $(\'coombs_count\').value;
                                    var compVal = $(\'bb_component_count\').value;
                                    var duVal = $(\'du_variant_count\').value;
                                    var others = $(\'others\').value;
                                    printClaimStub(refno,
                                                   cmCheck.toString(),
                                                   coombsCheck.toString(),
                                                   compCheck.toString(),
                                                   duCheck.toString(),
                                                   cmVal.toString(),
                                                   coombsVal.toString(),
                                                   compVal.toString(),
                                                   duVal.toString(),
                                                   others
                                                   );
                                    $J("#printClaimStubDialog").dialog("close");
                                },
                                Cancel:function(){
                                    $J("#printClaimStubDialog").dialog("close");
                                }
                            }
                        });                        
                      </script>';
    
    $print = '<div id="printClaimStubDialog">
                <table>
                <tr>
                    <td>Patient:</td>
                    <td><input id="patient_name" type="text" readonly /></td>
                    <td>Hospital Case no:</td>
                    <td><input id="case_no" type="text" readonly /></td>
                </tr>              
                </table>
                <hr>
                <table>
                <tr>
                    <td>Crossmatching:</td>
                    <td><input id="crossmatching" type="checkbox"/>x<input id="cm_count" class="int_input" value = "0" onkeydown="return key_check(event, this.value);" type="number" style="width:30px;"/></td>
                    <td>Coombs Test:</td>
                    <td><input id="coombs_test" type="checkbox"/>x<input id="coombs_count" class="int_input" value = "0" onkeydown="return key_check(event, this.value);" type="number" style="width:30px;"/></td>
                </tr>
                <tr>
                    <td>(PC,FFP,CRYO):</td>
                    <td><input id="bb_component" type="checkbox"/>x<input id="bb_component_count" class="int_input" value = "0" onkeydown="return key_check(event, this.value);" type="number" style="width:30px;"/></td>                    
                    <td>Du Variant:</td>
                    <td><input id="du_variant" type="checkbox"/>x<input id="du_variant_count" class="int_input" value = "0" onkeydown="return key_check(event, this.value);" type="number" style="width:30px;"/></td>
                </tr>
                <tr>
                    <td>
                        OTHERS:<input id="others" type="text" />
                    </td>
                </tr>
                </table>
            </div>
             ';
        */
             
    $smarty->assign('jsPrintDialog',$jsPrintDialog);
    $smarty->assign('printDialog',$print);
    //end Nick
             
    $sTemp='<input type="hidden" name="refno" id="refno" value="'.$refno.'">
            <input type="hidden" name="service_code" id="service_code" value="'.$service_code.'">
            <input type="hidden" name="quantity" id="quantity" value="'.$quantity.'">
            <input type="hidden" name="current_date" id="current_date" value="'.date("m/d/Y").'">
            <input type="hidden" name="current_time" id="current_time" value="'.date("h:i").'">
            <input type="hidden" name="current_meridian" id="current_meridian" value="'.date("A").'">
            <input type="hidden" name="submitted" id="submitted" value="0">
            <input type="hidden" name="received_qty" id="received_qty" value="0">
            <script>
            $J(function(){
                parent.getRequestInfo($(\'qty\').innerHTML,$(\'test_code\').innerHTML);
            });
            </script>
            ';
    $smarty->assign('sHiddenInputs',$sTemp);
    
    $smarty->assign('sFormStart','<form ENCTYPE="multipart/form-data" action="'.$thisfile.'?popUp='.$popUp.'" method="POST" name="inputform" id="inputform">');
    $smarty->assign('sFormEnd','</form>');
    
# Assign the form template to mainframe
$smarty->assign('sMainBlockIncludeFile','blood/blood-received-sample.tpl');
$smarty->display('common/mainframe.tpl');

?>