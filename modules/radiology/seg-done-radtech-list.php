<?php
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
require($root_path.'include/inc_environment_global.php');
require($root_path.'modules/radiology/ajax/radio-schedule-common.php');

define('LANG_FILE','lab.php');
define('NO_2LEVEL_CHK',1);
$local_user='ck_lab_user';
require_once($root_path.'include/inc_front_chain_lang.php');

require_once($root_path.'include/care_api_classes/class_personell.php');
$pers_obj=new Personell;

ob_start();
$xajax->printJavascript($root_path.'classes/xajax');

$phpfd='MM/dd/yyyy';
$phpfd=str_replace("dd", "%d", strtolower($phpfd));
$phpfd=str_replace("mm", "%m", strtolower($phpfd));
$phpfd=str_replace("yyyy","%Y", strtolower($phpfd));
    
#equire_once($root_path.'include/inc_checkdate_lang.php');
?>

 <script type="text/javascript" language="javascript">
<?php
    require_once($root_path.'include/inc_checkdate_lang.php');
?>
</script>

<script type="text/javascript" src="<?=$root_path?>js/jsprototype/prototype.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/fat/fat.js"></script>
<link rel="stylesheet" type="text/css" media="all" href="<?= $root_path ?>js/jscalendar/calendar-win2k-cold-1.css">
<script language="javascript" src="<?=$root_path?>js/setdatetime.js"></script>
<script language="javascript" src="<?=$root_path?>js/checkdate.js"></script>
<script language="javascript" src="<?=$root_path?>js/dtpick_care2x.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jscalendar/calendar.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jscalendar/lang/calendar-en.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jscalendar/calendar-setup_3.js"></script>

<link rel="stylesheet" href="<?= $root_path ?>js/jquery/themes/seg-ui/jquery.ui.all.css" type="text/css" />
<script type="text/javascript" src="<?=$root_path?>js/jquery/jquery.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jquery/ui/jquery-ui.js"></script>

<script type="text/javascript" src="<?=$root_path?>js/jquery/jquery.maskedinput.js"></script>

<script type="text/javascript">

    var $J = jQuery.noConflict();

    jQuery(function($){
        $J("#served_date").mask("99/99/9999");
    });
    
    jQuery(function($){
        $J("#served_time").mask("99:99");
    });
	
    function validateform(){
        var ok=1;
        
        if (($('rad_tech').value=='0')&&($('served_date').value=='')){
            alert('Please select a rad tech and enter the served date.');
            $('rad_tech').focus();
            ok = 0;
        }else if ($('rad_tech').value=='0'){
            alert('Please select a rad tech.');
            $('rad_tech').focus();
            ok = 0;
        }else if ($('served_date').value==''){
            alert('Please enter the served date.');
            $('served_date').focus();
            ok = 0;
        }
        
        if (ok==1)
            submitform();
    }   
    
    function closeWindow(){
        window.parent.ReloadWindow();
        window.parent.cClick();
    } 
    
	function submitform(){
	    //inputform.submit();
        var batch_nr = '<?=$batch_nr?>';
        var refno = '<?=$refno?>';
        var service_code = '<?=$code?>';
        var is_served = 1;
        
        var rad_tech = $('rad_tech').value;
        var served_date = $('served_date').value;
        var served_time = $('served_time').value+' '+$('selAMPM').value;
        
        var answer;
    
        if (is_served==1)
            answer = confirm("Are you sure that this request is already DONE? \n Click OK if YES, otherwise CANCEL.");
        else
            answer = confirm("Are you sure to UNDO this done request? \n Click OK if YES, otherwise CANCEL."); 
            
        if (answer)
            //alert('batch nr, refno, code, served = '+batch_nr+', '+refno+', '+service_code+', '+is_served);
            //refno=batch nr, batch nr = refno
            xajax_savedServedPatient(refno, batch_nr, service_code, is_served, rad_tech, served_date, served_time);
        }

	var js_time = "";
    function js_setTime(jstime){
        js_time = jstime;
    }

    function js_getTime(){
        return js_time;
    }

    function validateTime(S) {
        return /^([01]?[0-9])(:[0-5][0-9])?$/.test(S);
    }

    var seg_validDate=true;
    //var seg_validTime=false;

    function seg_setValidDate(bol){
        seg_validDate=bol;
    //    alert("seg_setValidDate : seg_validDate ='"+seg_validDate+"'");
    }
    
    function trimString(objct){
        objct.value = objct.value.replace(/^\s+|\s+$/g,"");
        objct.value = objct.value.replace(/\s+/g," ");
    }/* end of function trimString */
    
    var seg_validTime=false;
    function setFormatTime(thisTime,AMPM){
        var stime = thisTime.value;
        var hour, minute;
        var ftime ="";
        var f1 = /^[01]?[0-9]\:[0-5][0-9]$/;
        var f2 = /^[0-9]\:[0-5][0-9]$/;
        var jtime = "";

        trimString(thisTime);

        if (thisTime.value==''){
            seg_validTime=false;
            return;
        }

        stime = stime.replace(':', '');

        if (stime.length == 3){
            hour = stime.substring(0,1);
            minute = stime.substring(1,3);
        } else if (stime.length == 4){
            hour = stime.substring(0,2);
            minute = stime.substring(2,4);
        }else{
            alert("Invalid time format.");
            thisTime.value = "";
            seg_validTime=false;
            thisTime.focus();
            return;
        }

        jtime = hour + ":" + minute;
        js_setTime(jtime);

        if (hour==0){
             hour = 12;
             document.getElementById(AMPM).value = "AM";
        }else    if((hour > 12)&&(hour < 24)){
             hour -= 12;
             document.getElementById(AMPM).value = "PM"; 
        }
        
        ftime =  hour + ":" + minute;

        if (ftime.length==4)
            ftime = '0'+ftime;
                
        if(!ftime.match(f1) && !ftime.match(f2)){
            thisTime.value = "";
            alert("Invalid time format.");
            seg_validTime=false;
            thisTime.focus();
        }else{
            thisTime.value = ftime;
            seg_validTime=true;
        }
    }// end of function setFormatTime

</script>
<body bgcolor="#FFFFFF">
<form ENCTYPE="multipart/form-data" action="" method="POST" name="inputgroupform" id="inputgroupform">
	<div style="background-color:#e5e5e5; color: #2d2d2d; overflow-y:hidden;">
	<table border="0" width="98%" cellspacing="2" cellpadding="2" style="margin:0.7%; font:bold 12px Arial; background-color:#e5e5e5; color: #2d2d2d; overflow-y:hidden">
		<tbody>
			<tr>
				<td width="30%">Radiologic Technologist: </td>
				<td width="*">
					<select class="segInput" id="rad_tech" name="rad_tech">
                        <option value="0">-Select a Rad Tech-</option>
                        <?
                            $result = $pers_obj->getRadTech();
                            while($row=$result->FetchRow()){
                                $name = $row['name_last'].", ".$row['name_first'];
                                echo '<option value="'.$row['nr'].'">'.mb_strtoupper( $name).'</option>';
                            }
                        ?>
                    </select>
				</td>
			</tr>
			<tr>
				<td>Date Served: </td>
				<td>
					<input class="segInput"  type="type" name="served_date" id="served_date" size="8" maxlength=10 value="<?= date('m/d/Y');?>" onchange="IsValidDate(this,'MM/dd/yyyy'); ">
				    <img id="served_date_trigger" border="0" align="absmiddle" width="26" height="22" style="cursor:pointer" src="../../gui/img/common/default/show-calendar.gif">
                    <font size=2>[mm/dd/yyyy]</font>
                    <script type="text/javascript">
                        Calendar.setup ({
                                inputField : "served_date", ifFormat : "<?php echo $phpfd?>", showsTime : false, button : "served_date_trigger", singleClick : true, step : 1
                        });
                    </script>
                </td>
			</tr>
            <tr>
                <td>Time Served: </td>
                <td>
                    <?php
                        $meridian = date("A");
                
                        if ($meridian=='AM'){
                            $selected1 = 'selected';
                            $selected2 = '';
                        }else{
                            $selected1 = '';
                            $selected2 = 'selected';
                        }
                    ?>
                    <input class="segInput"  type="type" name="served_time" id="served_time" size="4" maxlength="5" value="<?= date('h:i');?>" onChange="setFormatTime(this,'selAMPM')">
                    <select id="selAMPM" name="selAMPM">
                            <option value="AM" <?=$selected1?> >A.M.</option>
                            <option value="PM" <?=$selected2?>>P.M.</option>
                    </select>&nbsp;<font size=1>[hh:mm]</font>
                </td>
            </tr>
			<tr>
				<td colspan="2">
					<img id="save" name="save" src="../../gui/img/control/default/en/en_done2.gif" border=0 alt="Ok" title="Ok" style="cursor:pointer" onClick="validateform();">
					<img id="cancel" name="cancel" src="../../gui/img/control/default/en/en_cancel.gif" border=0 alt="Cancel" onClick="javascript:window.parent.cClick();" title="Cancel" style="cursor:pointer">
				</td>
			</tr>
		</tbody>
	</table>
	</div>
</form>
</body>