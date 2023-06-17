<?php
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
    
    $lang_tables[] = 'departments.php';
    define('LANG_FILE','konsil.php');
    define('NO_2LEVEL_CHK',1);
#    $local_user='ck_lab_user';
    $local_user='ck_radio_user';   # burn added : September 24, 2007
    require($root_path.'include/inc_environment_global.php');
    require_once($root_path.'include/inc_front_chain_lang.php');
    require($root_path.'modules/supply_office/ajax/seg-supply-office-request-tray.common.php');

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

$title=$LDRadiology;

$breakfile=$root_path.'modules/radiology/radiolog.php'.URL_APPEND;   # bun added: September 8, 2007
$thisfile=basename(__FILE__);

    # Create radiology object
    require_once($root_path.'include/care_api_classes/class_radiology.php');
    $radio_obj = new SegRadio;
    
    #added by VAN 06-17-08
    require_once($root_path.'include/care_api_classes/class_ward.php');
    $ward_obj = new Ward;
    
    require_once($root_path.'include/care_api_classes/class_department.php');
    $dept_obj=new Department;
    #-------------------
    
    #added by VAN 06-25-08
    require_once($root_path.'include/care_api_classes/class_social_service.php');
    $objSS = new SocialService;
        
    #added by VAN 07-08-08
    require_once($root_path.'include/care_api_classes/class_person.php');
    $person_obj = new Person;
    
    require_once($root_path.'include/care_api_classes/class_encounter.php');
    $enc_obj=new Encounter;
    #-------------------    
    
    require_once($root_path.'gui/smarty_template/smarty_care.class.php');
    $smarty = new smarty_care('common');


    if (!isset($popUp) || !$popUp){
        if (isset($_GET['popUp']) && $_GET['popUp']){
            $popUp = $_GET['popUp'];
        }
        if (isset($_POST['popUp']) && $_POST['popUp']){
            $popUp = $_POST['popUp'];
        }
    }
    
    # added by VAN 01-11-08
    
    if ($_GET['repeat'])
        $repeat = $_GET['repeat'];
    else
        $repeat = $_POST['repeat'];    
    
    $is_dr = $_GET['is_dr'];      
    #echo "<br>get repeat = ".$repeat."<br>";    
        
    if ($_GET['prevbatchnr'])
        $prevbatchnr = $_GET['prevbatchnr'];
        
    if ($_GET['prevrefno'])    
        $prevrefno = $_GET['prevrefno'];
    
       
    #added by VAN 03-19-08
    $repeaterror = $_GET['repeaterror'];
    
    #added by VAN 06-25-08
    $discountid_get = $_GET['discountid'];
    
    #added by VAN 07-08-08
    if ($_GET['encounter_nr'])
        $encounter_nr = $_GET['encounter_nr'];
    
    if ($_GET['area'])
        $area = $_GET['area'];    
    
    if ($_GET['pid'])
        $pid = $_GET['pid'];
    #---------------------
    
    if ($encounter_nr){
        $patient = $enc_obj->getEncounterInfo($encounter_nr);
    }
    
    if ($repeaterror){
        #$smarty->assign('sWarning',"<strong>Error:</strong> Sorry but you are not allowed to do a repeat request!");
        $smarty->assign('sWarning','<strong>Error:</strong> Sorry but you are not allowed to do a repeat request!');
    }
    #-----------------------------
    
    #added by VAN 01-29-08
    $_POST['request_time'] = date('H:i:s');
    
    #added by VAN 06-16-08
    if (empty($_POST['is_tpl'])){
        $_POST['is_tpl'] = '0';
    }

    switch($mode){
        case 'save':

                if(trim($_POST['request_date'])!=""){
                    $_POST['request_date'] = formatDate2STD($_POST['request_date'], $date_format);
                }
                $_POST['clinical_info'] = $_POST['clinicInfo'];    
                #$_POST['clinical_info'] = stripslashes($_POST['clinicInfo']);    
                $_POST['request_doctor'] = $_POST['requestDoc'];    
                $_POST['is_in_house'] = $_POST['isInHouse'];
                $_POST['service_code'] = $_POST['items'];
                $_POST['is_cash'] = $_POST['iscash'];
                $_POST['is_urgent'] = $_POST['priority'];
#                $_POST['hasPaid'] = 0;   # not yet paid since this is just a request
                $_POST['encoder'] = $HTTP_SESSION_VARS['sess_user_name'];
                $_POST['history'] = "Create ".date('Y-m-d H:i:s')." ".$HTTP_SESSION_VARS['sess_user_name']." \n";

                if ($repeat01){
                    #-------added by VAN 01-11-08-------------
                    $_POST['parent_batch_nr'] = $_POST['parent_batch_nr'];
                    $_POST['parent_refno'] = $_POST['parent_refno'];
                    $_POST['approved_by_head'] = $_POST['approved_by_head'];
                    $_POST['remarks'] = $_POST['remarks'];
                    
                    #added by VAN 03-19-08
                    $_POST['headID'] = $_POST['headID'];
                    $_POST['headpasswd'] = $_POST['headpasswd'];
                
                    #-----------------------------------------
                
                    $radio_obj->getStaffInfo($_POST['headID'],$_POST['headpasswd']);
                    #echo "<br>sql = ".$radio_obj->sql;
                    $isCorrectInfo = $radio_obj->count;
                    #echo "<br>count = ".$isCorrectInfo;
                    if ($isCorrectInfo){
                        #echo "<br>sulod save radio ";
                        if($refno = $radio_obj->saveRadioRefNoInfoFromArray($_POST)){
                            $rid = $radio_obj->createNewRID($_POST['pid']); 
                            $smarty->assign('sysInfoMessage',"Radiological Request Service successfully created.");
                        }else{
                            $smarty->assign('sysErrorMessage','<strong>Error:</strong> '.$radio_obj->getErrorMsg());
                    }
                    }else{
                        header("Location: ".$root_path."modules/laboratory/labor_test_request_pass.php".URL_APPEND."&target=radio_test&user_origin=lab&popUp=1&repeat=1&prevbatchnr=".$_POST['parent_batch_nr']."&prevrefno=".$_POST['parent_refno']."&repeaterror=1");
                        exit;
                    }
                }else{                        

                    if($refno = $radio_obj->saveRadioRefNoInfoFromArray($_POST)){
                        $rid = $radio_obj->createNewRID($_POST['pid']); 
#                        $errorMsg='<font style="color:#FF0000">Successfully saved!</font>';
                        $smarty->assign('sysInfoMessage',"Radiological Request Service successfully created.");
                    }else{
                        # $errorMsg = $db->ErrorMsg();
    #                    $errorMsg='<font style="color:#FF0000">'.$radio_obj->getErrorMsg().'</font>';
                        $smarty->assign('sysErrorMessage','<strong>Error:</strong> '.$radio_obj->getErrorMsg());
#                        $smarty->assign('sWarning','<strong>Error:</strong> '.$radio_obj->getErrorMsg());
                    }
                }    
                
                break;
        case 'update':
    #echo "nursing-station-radio-request-new.php : update mode = '".$mode."' <br> \n";            
    #echo "nursing-station-radio-request-new.php : _POST : "; print_r($_POST); echo " <br><br> \n";

    #            if($radio_obj->saveAFinding($batch_nr,$finding_nr,$findings,$radio_impression,$findings_date,$doctor_id,'Update')){
                if(trim($_POST['request_date'])!=""){
                    $_POST['request_date'] = formatDate2STD($_POST['request_date'], $date_format);
                }
                
                $_POST['clinical_info'] = $_POST['clinicInfo'];    
                #$_POST['clinical_info'] = stripslashes($_POST['clinicInfo']);
                $_POST['request_doctor'] = $_POST['requestDoc'];    
                $_POST['is_in_house'] = $_POST['isInHouse'];
                $_POST['service_code'] = $_POST['items'];
                $_POST['is_cash'] = $_POST['iscash'];
                $_POST['is_urgent'] = $_POST['priority'];
#                $_POST['hasPaid'] = 0;   # not yet paid since this is just a request
                $_POST['encoder'] = $HTTP_SESSION_VARS['sess_user_name'];
               $_POST['history'] = $radio_obj->ConcatHistory("Update: ".date('Y-m-d H:i:s')." = ".$HTTP_SESSION_VARS['sess_user_name']."\n");        
                
                if ($repeat01){
                    #-------added by VAN 01-11-08-------------
                    #echo "batch, head, remarks = ".$_POST['parent_batch_nr']." - ".$_POST['approved_by_head']." - ".$_POST['remarks'];
                    $_POST['parent_batch_nr'] = $_POST['parent_batch_nr'];
                    $_POST['parent_refno'] = $_POST['parent_refno'];
                    $_POST['approved_by_head'] = $_POST['approved_by_head'];
                    $_POST['remarks'] = $_POST['remarks'];
                
                    #added by VAN 03-19-08
                    $_POST['headID'] = $_POST['headID'];
                    $_POST['headpasswd'] = $_POST['headpasswd'];
                
                    $radio_obj->getStaffInfo($_POST['headID'],$_POST['headpasswd']);
                    #echo "<br>sql = ".$radio_obj->sql;
                    $isCorrectInfo = $radio_obj->count;
                    #echo "<br>count = ".$isCorrectInfo;
                    if ($isCorrectInfo){
                        if($radio_obj->updateRadioRefNoInfoFromArray($_POST)){
                            $rid = $radio_obj->createNewRID($_POST['pid']); 
                            $reloadParentWindow='<script language="javascript">'.
                                '    window.parent.jsOnClick(); '.
                                '</script>';
                            $smarty->assign('sysInfoMessage',"Radiological Request Service successfully updated.");                    
                        }else{
                            $errorMsg='<font style="color:#FF0000">'.$radio_obj->getErrorMsg().'</font>';
                        }
                    }else{
                        header("Location: ".$root_path."modules/laboratory/labor_test_request_pass.php".URL_APPEND."&target=radio_test&user_origin=lab&popUp=1&repeat=1&prevbatchnr=".$_POST['parent_batch_nr']."&prevrefno=".$_POST['parent_refno']."&repeaterror=1");
                        exit;
                    }    
                }else{
                
                    if($radio_obj->updateRadioRefNoInfoFromArray($_POST)){
                        $rid = $radio_obj->createNewRID($_POST['pid']); 
#                        $errorMsg='<font style="color:#FF0000">Successfully updated!</font>';
                        $reloadParentWindow='<script language="javascript">'.
                                '    window.parent.jsOnClick(); '.
#                                '    javascript:self.parent.location.href=self.parent.location.href;'.
                                '</script>';
                        $smarty->assign('sysInfoMessage',"Radiological Request Service successfully updated.");                    
                    }else{
                        $errorMsg='<font style="color:#FF0000">'.$radio_obj->getErrorMsg().'</font>';
                    }
                }    
                break;
        case 'cancel':
                if($radio_obj->deleteRefNo($_POST['refno'])){

                    header('Location: '.$breakfile);
                    exit;
                }else{
                    $errorMsg='<font style="color:#FF0000">'.$radio_obj->getErrorMsg().'</font>';
                }
                break;
    }
    if (!isset($refno) || !$refno){
        if (isset($_GET['refno']) && $_GET['refno']){
            $refno = $_GET['refno'];
        }
        if (isset($_POST['refno']) && $_POST['refno']){
            $refno = $_POST['refno'];
        }
        
        if (empty($refno)){
            $refno = $_GET['prevrefno'];
            $prevrefno = $refno;
        }    
    }

    $refInfo = $radio_obj->getRequestInfoByPrevRef($prevrefno,$prevbatchnr);
    
    if ($refInfo['parent_refno'])
        //$refno = $refInfo['parent_refno'];
        $refno = $refInfo['refno'];
    
    $mode='save';   # default mode
    if ($refNoBasicInfo = $radio_obj->getBasicRadioServiceInfo($refno)){
        #echo "van:seg-radio-request-new = ".$radio_obj->sql;
        $mode='update';
        extract($refNoBasicInfo);
        if (empty($refNoBasicInfo['pid']) || !$refNoBasicInfo['pid']){
            $person_name = trim($refNoBasicInfo['ordername']);
        }else{
                # in case there is an updated profile of the person
            $person_name = trim($refNoBasicInfo['name_first']).' '.trim($refNoBasicInfo['name_last']);
        }
#echo "nursing-station-radio-request-new.php : before : request_date='".$request_date."' <br> \n";            
        $request_date = formatDate2Local($request_date,$date_format); 
    }#end of if-stmt
    #added by VAN 07-08-08
    #elseif (($pid)&&($area=="ER")){
   
   
   #--------------------     

 $smarty->assign('sToolbarTitle',"Supply Office :: New Supply Request");

 # href for the help button
 $smarty->assign('pbHelp',"javascript:gethelp('submenu1.php','$LDLab')");

if ($popUp!='1'){
         # href for the close button
     $smarty->assign('breakfile',$breakfile);
}else{
        # CLOSE button for pop-ups
     #edited by VAN 07-11-08
     #if ($area=='ER')
     if ($area)
         $smarty->assign('breakfile','javascript:window.parent.cClick();');
     else    
         $smarty->assign('breakfile','javascript:window.parent.pSearchClose();');
     
     $smarty->assign('pbBack','');
}

 # Window bar title
 #$smarty->assign('sWindowTitle',"$LDRadiology :: $LDDiagnosticTest");
 $smarty->assign('sWindowTitle',"Supply Office :: Request");

 # Assign Body Onload javascript code
 
 #$onLoadJS='onLoad="preSet();"';
 #edited by VAN 06-14-08
 $onLoadJS='onLoad="CheckRepeatInfo();checkCash();"';
 #echo "onLoadJS = ".$onLoadJS;
 $smarty->assign('sOnLoadJs',$onLoadJS);

 # Collect javascript code

ob_start();
     # Load the javascript code
    $xajax->printJavascript($root_path.'classes/xajax-0.2.5');     
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

<script type="text/javascript">
<!--
OLpageDefaults(BGCLASS,'olbg', CGCLASS,'olcg', FGCLASS,'olfg',
 CAPTIONFONTCLASS,'olcap', CLOSEFONTCLASS,'olclo', TEXTFONTCLASS,'oltxt');
//-->
</script>

<style type="text/css">
<!--
.olbg {
    background-image:url("<?= $root_path ?>images/bar_05.gif");
    background-color:#0000ff;
    border:1px solid #4d4d4d;
}
.olcg {
    background-color:#aa00aa; 
    background-image:url("<?= $root_path ?>images/bar_05.gif");
    text-align:center;
}
.olcgif {background-color:#333399; text-align:center;}
.olfg {
    background-color:#ffffcc; 
    text-align:center;
}
.olfgif {background-color:#bbddff; text-align:center;}
.olcap {
    font-family:Arial; font-size:13px; 
    font-weight:bold; 
    color:#708088;
}
a.olclo {font-family:Verdana; font-size:11px; font-weight:bold; color:#ddddff;}
a.olclo:hover {color:#ffffff;}
.oltxt {font-family:Arial; font-size:12px; color:#000000;}
.olfgright {text-align: right;}
.olfgjustify {background-color:#cceecc; text-align: justify;}

a {color:#338855;font-weight:bold;}
a:hover {color:#FF00FF;}
.text12 {font-family:Verdana,Arial,sans-serif; font-size:12px;}
.text14 {font-family:Verdana,Arial,sans-serif; font-size:14px;}
.text16 {font-family:Verdana,Arial,sans-serif; font-size:16px;}
.text18 {font-family:Verdana,Arial,sans-serif; font-size:18px;}

.myHeader {font-family:Verdana,Arial,Helvetica,sans-serif;font-size:22px;}
.mySubHead {font-family:Verdana,Arial,Helvetica,sans-serif;font-size:18px;}
.mySpacer {font-family:Verdana,Arial,Helvetica,sans-serif;font-size:4px;}
.myText {font-family:Verdana,Arial,Helvetica,sans-serif;font-size:13px;color:#000000;}
.snip {font-family:Verdana,Arial,Helvetica;font-size:10px;}
.purple14 {font-family:Verdana,Arial,Helvetica,sans-serif;font-size:14px;color:purple;
 font-weight:bold;}
.purple18 {font-family:Verdana,Arial,Helvetica,sans-serif;font-size:18px;color:purple;
 font-weight:bold;font-style:italic;}
.yellow {color:#ffff00;}
.red {color:#cc0000;}
.blue {color:#0000cc;}
-->
</style> 

<script type="text/javascript" src="<?=$root_path?>js/jsprototype/prototype.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/fat/fat.js"></script>

            <!-- START for setting the DATE (NOTE: should be IN this ORDER...i think soo..) -->
<script type="text/javascript" language="javascript">
<?php
    require_once($root_path.'include/inc_checkdate_lang.php'); 
?>
</script>
<link rel="stylesheet" type="text/css" media="all" href="<?= $root_path ?>js/jscalendar/calendar-win2k-cold-1.css">
<script language="javascript" src="<?=$root_path?>js/setdatetime.js"></script>
<script language="javascript" src="<?=$root_path?>js/checkdate.js"></script>
<script language="javascript" src="<?=$root_path?>js/dtpick_care2x.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jscalendar/calendar.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jscalendar/lang/calendar-en.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jscalendar/calendar-setup_3.js"></script>
            <!-- END for setting the DATE (NOTE: should be IN this ORDER...i think soo..) -->

<script type="text/javascript" src="<?=$root_path?>js/NumberFormat154.js"></script>
<script type="text/javascript" src="js/radio-request-new.js?t=<?=time()?>"></script>

<script type="text/javascript" language="javascript">
<!--
    var trayItems = 0;

    //added by VAN 06-14-08
    function checkCash(){
        if ($("iscash1").checked){
            document.getElementById('is_cash').value = 1;
            //$('tplrow').style.display = '';
            $('type_charge').style.display = '';
            
        }else{
            document.getElementById('is_cash').value = 0;    
            //$('tplrow').style.display = 'none';
            //$('type_charge').style.display = '';
            $('type_charge').style.display = '';
            //document.getElementById('is_tpl').checked = false;
        }    
    }
    //----------------------
    
-->
</script>

<?php
#echo "nursing-station-radio-request-new.php : hasPaid='".$hasPaid."' <br> \n";            
    if ($popUp=='1'){
        echo $reloadParentWindow;
    }
    $sTemp = ob_get_contents();
    ob_end_clean();
    $smarty->append('JavaScript',$sTemp);
    
    if ($_GET['dept_nr'])
    $dept_nr=$_GET['dept_nr'];

    $res = $dept_obj->getDeptAllInfo($dept_nr);
    
//start paul here

#$smarty->assign('sDeptName','<input type="hidden" name="dept_nr" id="dept_nr" value="'.$dept_nr.'">'.$res["name_formal"]);
$smarty->assign('sDeptName','<input type="hidden" name="dept_nr" id="dept_nr" value="'.$dept_nr.'"><input class="jedInput" type="text" name="name_formal" id="name_formal" size="30" value="'.$res["name_formal"].'" style="font:bold 12px Arial;" disabled />');
                             
#Current person name----------------------------------------------------------------
$smarty->assign('sOrderEncID','<input id="pid" name="pid" type="hidden" value="'.$pid.'"/>');

#echo "name = ".$HTTP_SESSION_VARS['refno'];
#echo "name = ".$HTTP_SESSION_VARS['sess_user_name'];
$smarty->assign('sOrderName','<input class="jedInput" id="ordername" name="ordername" type="text" size="40" value="'.$HTTP_SESSION_VARS['sess_user_name'].'" style="font:bold 12px Arial;" disabled />');

#ref input & button here------------------------------------------------------------
$smarty->assign('sRefNo','<input class="segInput" name="refno" id="refno" class="jedInput" type="text" size="10"  value="'.$ferno.'" style="font:bold 12px Arial" />');
$smarty->assign('sResetRefNo','<input class="segInput" type="button" value="Reset" style="font:bold 11px Arial"/>');

#order date input field-------------------------------------------------------------


$curTme  = strftime("%Y-%m-%d %H:%M:%S");
$curDate = strftime("%b %d, %Y %I:%M%p", strtotime($curTme));

$smarty->assign('sDate', '<span id="show_transmitdate" class="jedInput" style="font-weight:bold; color:#0000c0; padding:0px 2px;width:200px; height:24px">'.$curDate.'</span><input class="jedInput" name="transmitdte" id="transmitdte" type="hidden" value="'.($submitted ? strftime("%Y-%m-%d %H:%M:%S", strtotime($_POST['transmitdte'])) : $curTme).'" style="font:bold 12px Arial">');
$smarty->assign('sCalendarIcon','<img ' . createComIcon($root_path,'show-calendar.gif','0') . ' id="transmitdte_trigger" class="segSimulatedLink" align="absmiddle" style="margin-left:2px;cursor:pointer">');
$jsCalScript = "<script type=\"text/javascript\">
    Calendar.setup ({
        displayArea : \"show_transmitdate\",
        inputField : \"transmitdte\",
        ifFormat : \"%Y-%m-%d %H:%M:%S\", 
        daFormat : \"%b %d, %Y %I:%M%p\", 
        showsTime : true, 
        button : \"transmitdte_trigger\", 
        singleClick : true,
        step : 1                                            
    });
</script>";
$smarty->assign('jsCalendarSetup', $jsCalScript);

      
    
    
//calendar here
    
    if ($area=="ER"){
        $smarty->assign('sIsCash','<input class="jedInput" type="radio" name="iscash" id="iscash1" value="1" '.(($pid)?'':'checked="checked" ').' '.(($pid)?'disabled="disabled" ':'').' onclick="if (warnClear()) { emptyTray(); changeTransactionType(); return true;} else {return false;}" /><label for="iscash1" class="jedInput">Cash</label>');
        $smarty->assign('sIsCharge','<input class="jedInput" type="radio" name="iscash" id="iscash0" value="0" '.(($pid)?'checked="checked" ':'').' '.(($pid)?'':'disabled="disabled" ').' onclick="if (warnClear()) { emptyTray(); changeTransactionType(); return true;} else return false;" style="margin-left:10px" /><label class="jedInput" for="iscash0">Charge</label>');
    }elseif ($area=="clinic"){
 
        if ($is_dr){
              # echo "type = ".$patient['encounter_type']; 
            if (($patient['encounter_type']==3)||($patient['encounter_type']==4)){
                $smarty->assign('sIsCash','<input class="jedInput" type="radio" name="iscash" id="iscash1" value="1" checked onclick="if (warnClear()) { emptyTray(); changeTransactionType(); return true;} else {return false;}" /><label for="iscash1" class="jedInput">Cash</label>');
                $smarty->assign('sIsCharge','<input class="jedInput" type="radio" name="iscash" id="iscash0" value="0" onclick="if (warnClear()) { emptyTray(); changeTransactionType(); return true;} else return false;" style="margin-left:10px" /><label class="jedInput" for="iscash0">Charge</label>');
            }elseif($patient['encounter_type']==1){

                $smarty->assign('sIsCash','<input class="jedInput" type="radio" name="iscash" id="iscash1" value="1" '.(($pid)?'':'checked="checked" ').' onclick="if (warnClear()) { emptyTray(); changeTransactionType(); return true;} else {return false;}" /><label for="iscash1" class="jedInput">Cash</label>');
                $smarty->assign('sIsCharge','<input class="jedInput" type="radio" name="iscash" id="iscash0" value="0" '.(($pid)?'checked="checked" ':'').' onclick="if (warnClear()) { emptyTray(); changeTransactionType(); return true;} else return false;" style="margin-left:10px" /><label class="jedInput" for="iscash0">Charge</label>');
            }elseif($patient['encounter_type']==2){
                $smarty->assign('sIsCash','<input class="jedInput" type="radio" name="iscash" id="iscash1" value="1" checked onclick="if (warnClear()) { emptyTray(); changeTransactionType(); return true;} else {return false;}" /><label for="iscash1" class="jedInput">Cash</label>');
                $smarty->assign('sIsCharge','<input class="jedInput" type="radio" name="iscash" id="iscash0" value="0" disabled onclick="if (warnClear()) { emptyTray(); changeTransactionType(); return true;} else return false;" style="margin-left:10px" /><label class="jedInput" for="iscash0">Charge</label>');
            }
            
        }else{
           $smarty->assign('sIsCash','<input class="jedInput" type="radio" name="iscash" id="iscash1" value="1" checked onclick="if (warnClear()) { emptyTray(); changeTransactionType(); return true;} else {return false;}" /><label for="iscash1" class="jedInput">Cash</label>');
        $smarty->assign('sIsCharge','<input class="jedInput" type="radio" name="iscash" id="iscash0" value="0" disabled onclick="if (warnClear()) { emptyTray(); changeTransactionType(); return true;} else return false;" style="margin-left:10px" /><label class="jedInput" for="iscash0">Charge</label>');
         }    
          
    }else{
        $smarty->assign('sIsCash','<input class="jedInput" type="radio" name="iscash" id="iscash1" value="1" '.(($is_cash!="0")?'checked="checked" ':'').'onclick="if (warnClear()) { emptyTray(); changeTransactionType(); return true;} else {return false;}" /><label for="iscash1" class="jedInput">Cash</label>');
        $smarty->assign('sIsCharge','<input class="jedInput" type="radio" name="iscash" id="iscash0" value="0" '.(($is_cash=="0")?'checked="checked" ':'').'onclick="if (warnClear()) { emptyTray(); changeTransactionType(); return true;} else return false;" style="margin-left:10px" /><label class="jedInput" for="iscash0">Charge</label>');
    }
   
     #-------------------------
    
    $smarty->assign('sOrderItems',"
                <tr>
                    <td colspan=\"6\">Request list is currently empty...</td>
                </tr>");

$smarty->assign('sBtnAddItem','<img type="image" name="btnAdd" id="btnAdd" src="'.$root_path.'images/btn_additems.gif" border="0" style="cursor:pointer;"
            onclick="return overlib(
                OLiframeContent(\'seg-issue-tray.php?dr_nr='.$dr_nr.'&dept_nr='.$dept_nr.'\', 600, 435, \'fOrderTray\', 1, \'auto\'),
                    WIDTH,435, TEXTPADDING,0, BORDER,0, 
                    STICKY, SCROLL, CLOSECLICK, MODAL, 
                    CLOSETEXT, \'<img src='.$root_path.'/images/close.gif border=0 >\',
                    CAPTIONPADDING,4, 
                    CAPTION,\'Add supply office item from request tray\',
                    MIDX,0, MIDY,0, 
                    STATUS,\'Add radiological service item from request tray\');"
            onmouseout="nd();">');

$smarty->assign('sBtnEmptyList','<img type="image" name="btnEmpty" id="btnEmpty" src="'.$root_path.'images/btn_emptylist.gif" border="0" style="cursor:pointer;" onclick="emptyTray();"></a>');

$smarty->assign('sFormStart','<form ENCTYPE="multipart/form-data" action="'.$thisfile.'?popUp='.$popUp.'" method="POST" name="inputform" id="inputform" onSubmit="return checkRequestForm()">');
$smarty->assign('sFormEnd','</form>');
#echo "refno & batch = ".$refno." - ".$prevbatchnr;
 
?>
<?php
ob_start();
$sTemp='';

# added by VAN 01-14-08
#echo "b4 ref, batch = ".$refno." - ".$batchnr;
if (!empty($Ref)){
    $refno = $Ref;
    #$batchnr = $Ref; 
}else{
    if ($refInfo['parent_batch_nr'])
        $batchnr = $refInfo['batch_nr'];
    else    
        $batchnr = $prevbatchnr;
}
#echo "<br>after ref, batch = ".$refno." - ".$batchnr;
?>
    <script type="text/javascript" language="javascript">
        preset(<?= ($is_cash=='0')? "0":"1"?>);
        //xajax_populateRequestListByRefNo(<?=$refno? $refno:0?>);    
        
        // edited by VAN 01-11-08
        xajax_populateRequestListByRefNo(<?=$refno? $refno:0?>,<?=$batchnr? $batchnr:0?>);    
        
        //xajax_getCharityDiscounts(<?=$refno?>);
    </script>
<?php
$sTemp = ob_get_contents();
ob_end_clean();

if ($mode=='update'){
    $smarty->assign('sIntialRequestList',$sTemp);
}

ob_start();
$sTemp='';
?>
    <input type="hidden" name="submitted" value="1">
    <input type="hidden" name="sid" value="<?php echo $sid?>">
    <input type="hidden" name="lang" value="<?php echo $lang?>">
    <input type="hidden" name="cat" value="<?php echo $cat?>">
    <input type="hidden" name="userck" value="<?php echo $userck?>">  
<!--  
    <input type="hidden" name="mode" id="modeval" value="<?php if($saveok) echo "update"; else echo "save"; ?>">
-->
    <input type="hidden" name="encoder" value="<?php echo  str_replace(" ","+",$HTTP_COOKIES_VARS[$local_user.$sid])?>">
    <input type="hidden" name="dstamp" value="<?php echo  str_replace("_",".",date(Y_m_d))?>">
    <input type="hidden" name="tstamp" value="<?php echo  str_replace("_",".",date(H_i))?>">
    <input type="hidden" name="lockflag" value="<?php echo  $lockflag?>">
    <input type="hidden" name="update" value="<?php if($saveok) echo "1"; else echo $update;?>">
    
    <?php
        $discountInfo = $objSS->getSSClassInfo($discountid);
        
        if ($discountInfo){
            $discount =     $discountInfo['discount'];
        }    
        #echo "sc = ".$_POST['issc'];
        #echo "<br>type = ".$encounter_type;
        
        if(($_POST['issc'])&&(trim($encounter_type)=="")){
            $discount = 0.20;
        }
    ?>
    
    <input type="hidden" name="is_cash" id="is_cash" value="<?=$is_cash?>" >
    <input type="hidden" id="encounter_nr" name="encounter_nr" value="<?=$encounter_nr?>">
    <input type="hidden" name="discount2" id="discount2" value="<?=$discount2?>" >
    <input type="hidden" name="discount" id="discount" value="<?=$discount?>" >
    <input type="hidden" name="latest_valid_show-discount" id="latest_valid_show-discount" value="<?=number_format($adjusted_amount, 2, '.', '')?>" >
    
    <!-- added by VAN 06-16-08 -->
    <!--<input type="hidden" id="discountid" name="discountid" value="<?php if ($info["discountid"]) echo $info["discountid"]; else $discountid;?>">-->
    <input type="hidden" id="discountid" name="discountid" value="<?=$discountid;?>">
    
    <!-- -->
    
    
    <?php 
        #----- added by VAN 01-12-08
        if ((empty($refInfo['parent_batch_nr']))&&(empty($refInfo['parent_refno']))&&(empty($Ref)))
            $mode='save';        
        else
            $mode='update';        
        #---------------------------
    ?>
    
    <input type="hidden" name="mode" id="mode" value="<?=$mode?$mode:'save'?>">
    <input type="hidden" name="popUp" id="popUp" value="<?=$popUp?$popUp:'0'?>">
    <input type="hidden" name="hasPaid" id="hasPaid" value="<?=$hasPaid?$hasPaid:'0'?>">
    <input type="hidden" name="view_from" id="view_from" value="<?=$view_from?$view_from:''?>">
    <input type="hidden" name="encoder_id" id="encoder_id" value="<?php echo $HTTP_SESSION_VARS['sess_login_personell_nr']; ?>">
    
    <input type="hidden" name="repeat01" id="repeat01" value="<?= $repeat?$repeat:'0'?>">
    
    <input type="hidden" name="area" id="area" value="<?=$area?>" />

<?php 

$sTemp = ob_get_contents();
ob_end_clean();

$smarty->assign('sHiddenInputs',$sTemp);

if (($mode=="update") && ($popUp!='1')){
    $sBreakImg ='cancel.gif';
    $smarty->assign('sBreakButton','<img type="image" '.createLDImgSrc($root_path,$sBreakImg,'0','center').' align="center" alt="'.$LDBack2Menu.'" onclick="$(\'mode\').value=\'cancel\';" style="cursor:pointer">');
}elseif ($popUp!='1'){
    $sBreakImg ='close2.gif';    
    $smarty->assign('sBreakButton','<img '.createLDImgSrc($root_path,$sBreakImg,'0','center').' alt="'.$LDBack2Menu.'" onclick="window.location=\''.$breakfile.'\'" onsubmit="return false;" style="cursor:pointer">');
}

#$smarty->assign('sContinueButton','<img type="image" name="btnSubmit" id="btnSubmit" src="'.$root_path.'images/btn_submitorder.gif" align="center" style="cursor:pointer" onClick="checkRequestForm();">');
#edited by VAN 06-27-08
$smarty->assign('sContinueButton','<img type="image" name="btnSubmit" id="btnSubmit" src="'.$root_path.'images/btn_submitorder.gif" align="center" style="cursor:pointer" onclick="if (confirm(\'Process this radiology request?\')) if (checkRequestForm()) document.inputform.submit()">');

if (($hasPaid)|| ((($encounter_type!='')||($encounter_type!=NULL)) && ($encounter_type!=2)) || ($type_charge) || $repeat)
    $smarty->assign('sClaimStub','<img name="claimstub" id="claimstub" onClick="viewClaimStub(\''.$is_cash.'\',\''.$refno.'\');" style="cursor:pointer" align="absmiddle" ' . createLDImgSrc($root_path,'claim_stub.gif','0','left') . ' border="0">');


#added by VAN 07-10-08
#echo "from = ".$popUp;
if (($view_from!='ssview') && ($popUp!=1)){ 
    
}

#document.inputform.submit()
# Assign the form template to mainframe
$smarty->assign('sMainBlockIncludeFile','supply_office/supply-office-request.tpl');
$smarty->display('common/mainframe.tpl');

?>