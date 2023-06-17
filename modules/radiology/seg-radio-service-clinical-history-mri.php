<?php

#Created by Francis L.G. 04-17-2013
#MRI Clinical History

include("roots.php");
include_once($root_path."/classes/fpdf/fpdf.php");
require_once($root_path.'include/inc_environment_global.php');
include_once($root_path.'include/inc_date_format_functions.php');

#added by VAN 02-18-08
define('NO_2LEVEL_CHK',1);
require($root_path.'include/inc_front_chain_lang.php');

require_once($root_path.'/include/care_api_classes/class_drg.php');
$objDRG= new DRG;

include_once($root_path.'include/care_api_classes/class_encounter.php');
$enc_obj=new Encounter;

//added by Francis 05-27-13
include_once($root_path.'include/care_api_classes/class_person.php');
$per_obj=new Person;

#Added by Francis L.G 01-25-13
require($root_path.'modules/radiology/ajax/radio-service-tray.common.php');

#Added by Cherry 08-05-10
require_once($root_path.'include/care_api_classes/class_dateGenerator.php');
$dategen = new DateGenerator;

require_once($root_path.'include/care_api_classes/class_personell.php');
require_once($root_path.'include/care_api_classes/class_department.php');

#Added by Cherry 08-05-10
require_once($root_path.'include/care_api_classes/class_radiology.php');
$radio_obj = new SegRadio;

$dept_obj=new Department;
$pers_obj=new Personell;

if (isset($_GET['encounter_nr']) && $_GET['encounter_nr']){
		$encounter_nr = $_GET['encounter_nr'];
}
if (isset($_POST['encounter_nr']) && $_POST['encounter_nr']){
		$encounter_nr = $_POST['encounter_nr'];
}
if(!$encounter_nr){
    $encounter_nr = 'walk in';
}
# echo "encounter_nr= ".$encounter_nr."<br>";
#Added by Cherry 08-09-10
if (isset($_GET['refno']) && $_GET['refno']){
	$refno = $_GET['refno'];
}
if (isset($_POST['refno']) && $_POST['refno']){
	$refno = $_POST['refno'];
}

//added by Francis 06-03-13
if($_GET['radServ']){
    $radServTemp = $_GET['radServ'];
    $radSrv = explode(",", $radServTemp);
    $radGrpMRI = array();
    for($i=0;$i<count($radSrv);$i++){
        $tmp = $radSrv[$i];
        $radSrvGrpInfo = $radio_obj->getRadioServiceGroupInfo($tmp);
        if(!in_array($radSrvGrpInfo['name'], $radGrpMRI)){
            if($radSrvGrpInfo['department_nr']==208)
                $radGrpMRI[] = $radSrvGrpInfo['name'];
        }
    }
}
else{
    $radGrpMRI[] = "";    
}

$grp = 0;
$allowDataEdit=0;

if($_POST['grp']){
    $grp = $_POST['grp'];
}else if(count($radGrpMRI)==1){
    $grp = $radGrpMRI[0];
}else if($_POST['srvGrp']){
    $grp = $_POST['srvGrp'];
}
else{
    $grp = "";
}

//added by Francis 05-27-13
global $allowedarea;
$allowedarea = array('_a_1_radiomanualpay');
$manpay = 0;
if (validarea($_SESSION['sess_permission'],1)) {
    $manpay = 1;
}
else{
    $manpay = 0;
}

if(!$refno){
    $refno = 0;
}

//added by Francis 05-27-2013
if($_GET['pid']) $pid = $_GET['pid'];
if($_POST['pid']) $pid = $_POST['pid'];

if (isset($_GET['pid']) && $_GET['pid']){
        $pid = $_GET['pid'];
}
if (isset($_POST['pid']) && $_POST['pid']){
        $pid = $_POST['pid'];
}

if($_SESSION['sess_temp_userid']){
    $encoder = $_SESSION['sess_temp_userid'];
}else{
    $encoder = "";
}

# echo "refno= ".$refno."<br>";
#echo "had_surgery= ".$_POST['had_surgery']."<br>";
include_once($root_path.'include/care_api_classes/class_cert_med.php');
$obj_medCert = new MedCertificate($encounter_nr);


$errorMsg='';

#----------------------------------------------
//$HTTP_POST_VARS['dr_nr'] = $_POST['doctors'];

if (isset($_POST['mode'])){

        
        if(!$_POST['history'])
        $lack = '1';
        if(!$_POST['chief_comp'])
        $lack = '1';
        if(!$_POST['phy_exam'])
        $lack = '1';
        if(!$_POST['impression'])
        $lack = '1';
        if(!$_POST['past_med_his'])
        $lack = '1';
        if(!$_POST['mri_purpose'])
        $lack = '1';
        if(!$_POST['info_gain'])
        $lack = '1';
        if(!$_POST['med_prob'])
        $lack = '1';
        if(!$_POST['refer_date'])
        $lack = '1';
        if(!$_POST['dr_specialty'])
        $lack = '1';
        if(!$_POST['dr_address'])
        $lack = '1';
        if(!$_POST['dr_contact_nr'])
        $lack = '1';
        if(!$_POST['dr_phone'])
        $lack = '1';
        
        $rDate = date('Y-m-d', strtotime($_POST['refer_date']));
        
        if($_POST['uuid']){
        $uuid = $_POST['uuid'];
        }
        else{
            $uuid = '';
        }
        
        if($_POST['dr_other']){
        $_POST['dr_nr'] = "";
        $_POST['dr_name'] = $_POST['dr_other'];     
        }
        else if(($_POST['dr_ref'])&&($_POST['dr_ref']!="other")){
            list($_POST['dr_nr'],$_POST['dr_name']) = explode("~",$_POST['dr_ref']);
        }
        else{    
            $lack = '1';
        }
        
        if($_POST['mri_res']){
        $_POST['mri_dr_nr'] = "";
        $_POST['mri_dr_name'] = $_POST['mri_res'];     
        }
        else if(($_POST['dr_mri'])&&($_POST['dr_mri']!="other")){
            list($_POST['mri_dr_nr'],$_POST['mri_dr_name']) = explode("~",$_POST['dr_mri']);
        }
        else{    
            $lack = '1';
        }
        
        //Manual Payment
        if($_POST['service_request_arr']){
            $sReqTemp = $_POST['service_request_arr'];
            $serviceRequests = implode(",",$sReqTemp);
        }else{
            $serviceRequests = "";
        }
        //echo $serviceRequests;

        if($_POST['payment_type_arr']){
            $pTypeTemp = $_POST['payment_type_arr'];
            $paymentType = implode(",",$pTypeTemp);
        }else{
            $paymentType = "";
        }
        //echo $paymentType;

        if($_POST['mpType']){
            if($paymentType!=$_POST['mpType']){
                $mpEncoder = $_SESSION['sess_temp_userid'];
            }else{
                $mpEncoder = $_POST['mpEncoder'];
            }
        }else{
            if($paymentType){
                $mpEncoder = $_SESSION['sess_temp_userid'];
            }else{
                $mpEncoder = "";
            }
        }
        //echo "ENCODER=".$mpEncoder;

        if($_POST['amount_arr']){
            $amountTemp = $_POST['amount_arr'];
            $amount = implode("-",$amountTemp);
        }else{
            $amount = "";
        }
        //echo $amount;
        if($_POST['man_pay_total']){
            $totalMP = $_POST['man_pay_total'];
        }else{
            $totalMP = "";
        }
          

	 $data = array('pid'=>$pid,
                       'encounterNr'=>$encounter_nr,
                       'history'=>$_POST['history'],
                       'chiefComp'=>$_POST['chief_comp'],
                       'phyExam'=>$_POST['phy_exam'],
                                                    'impression'=>$_POST['impression'],
                       'pastMedHis'=>$_POST['past_med_his'],
                                                    'creatinine'=>$_POST['creatinine'],
                                                    'bun'=>$_POST['bun'],
                       'drNr'=>$_POST['dr_nr'],
                       'drName'=>$_POST['dr_name'],
                       'mriDrNr'=>$_POST['mri_dr_nr'],
                       'mriDrName'=>$_POST['mri_dr_name'],
                       'drSpecialty'=>$_POST['dr_specialty'],
                       'drAddress'=>$_POST['dr_address'],
                       'drContactNr'=>$_POST['dr_contact_nr'],
                       'drPhone'=>$_POST['dr_phone'],
                       'referDate'=>$rDate,
                       'medProb'=>$_POST['med_prob'],
                       'infoGain'=>$_POST['info_gain'],
                       'mriPurpose'=>$_POST['mri_purpose'],
                       'createId'=>$_SESSION['sess_temp_userid'],
                       'createTm'=>date("Y-m-d H:i:s"),
                       'modifyId'=>$_SESSION['sess_temp_userid'],
                       'modifyTm'=>date("Y-m-d H:i:s"),
                       'mode'=>$_POST['mode'],
                       'grpCode'=>$grp,
                       'serviceRequests'=>$serviceRequests,
                       'paymentType'=>$paymentType,
                       'amount'=>$amount,
                       'totalMP'=>$totalMP,
                       'encoder'=>$mpEncoder
				   );

	switch($_POST['mode']) {
				case 'save':
                        if($lack!='1'){
                        $save = $radio_obj->saveMRIClinicalHistory($data);
						if($save){
								$errorMsg='<font style="color:#FF0000;font-style:italic">'."Saved sucessfully!".'</font>';
						}else{
								$errorMsg='<font style="color:#FF0000">'."Failed to save data".'</font>';
						}
                        }
                        else {
                            $errorMsg='<font style="color:#FF0000">'."**PLEASE FILL IN ALL THE NECCESSARY INFORMATION**".'</font>';
                        }
				break;
				case 'update':
                        if($lack!='1'){
                        // $refno="";
                        $update = $radio_obj->updateMRIClinicalHistory($pid,$uuid,$data);
						if ($update){
								$errorMsg='<font style="color:#FF0000;font-style:italic">'."Updated sucessfully!".'</font>';
						}else{
								$errorMsg='<font style="color:#FF0000">'.$obj_medCert->getErrorMsg().'</font>';
						}
                        }
                        else {
                            $errorMsg='<font style="color:#FF0000">'."**PLEASE FILL IN ALL THE NECCESSARY INFORMATION**".'</font>';
                        }
				break;
                case 'delete':
                        $delete = $radio_obj->deleteMRIClinicalHistory($uuid);

                        if($delete){
                            //chDeleted();
                            $errorMsg='<font style="color:#FF0000">'."Clinical history succesfully deleted !".'</font>';
                            $_POST = array();
                        }else{
                            $errorMsg='<font style="color:#FF0000">'."Deletion Failed !".'</font>';
                        }
				break;
		}# end of switch statement
}


//modified by Francis 05-17-2013
if($pid){
#    if(!($pidInfo = $per_obj->getEncounterInfo($_GET['encounter_nr']))){
        if(!($pidInfo=$per_obj->getPidInfo($pid))){
				echo '<em class="warn"> Sorry but the page cannot be displayed!</em>';
				exit();
		}
        #extract($pidInfo);
}else{
        echo '<em class="warn">Sorry but the page cannot be displayed! <br> The patient is not registered yet!</em>';
		exit();
}

//$mriHistoryInfo = $radio_obj->getMriHistoryInfo($pid,$refno);   //Added by Cherry 08-05-10
//$medico_cases = $enc_obj->getMedicoCases();
//echo "pid=".$pid." > refno=".$refno." > grp=".$grp;
$mriHistoryInfo = $radio_obj->getMriHistoryInfo($pid,$refno,$grp);

if($mriHistoryInfo){
    $_POST['uuid'] = $mriHistoryInfo['uuid'];
    if($mriHistoryInfo['purpose']=='1'){
        $f1="checked";
        $f2="";
        $f3="";    
    }
    if($mriHistoryInfo['purpose']=='2'){
        $f1="";
        $f2="checked";
        $f3="";    
    }
    if($mriHistoryInfo['purpose']=='3'){
        $f1="";
        $f2="";
        $f3="checked";    
    }
}

if($_POST['mri_purpose']=='1'){
        $f1="checked";
        $f2="";
        $f3="";    
}

if($_POST['mri_purpose']=='2'){
        $f1="";
        $f2="checked";
        $f3="";    
}

if($_POST['mri_purpose']=='3'){
        $f1="";
        $f2="";
        $f3="checked";    
}

$allowDataEdit = 0;
if($grp){
    if($_POST['dataEdit']){
        $allowDataEdit = $_POST['dataEdit'];
    }else if((!$mriHistoryInfo) || ($mriHistoryInfo['create_id']==$encoder)){
        $allowDataEdit = 1;
    }else{
        $allowDataEdit = 0;
    }
}

#echo "encInfo['encounter_type'] = '".$pidInfo['encounter_type']."' <br> \n";
$listDoctors=array();

#echo "encInfo['current_dept_nr'] = '".$pidInfo['current_dept_nr']."' <br> \n";
#added by VAN 06-28-08
	if ($pidInfo['current_dept_nr'])
		$dept_nr = $pidInfo['current_dept_nr'];
	else
		$dept_nr = $pidInfo['consulting_dept_nr'];


			 $doctors = $pers_obj->getDoctors(1);

	if (is_object($doctors)){
        
        $drRef = '<option value="0">--Select a Doctor--</option>';
        $drMri = '<option value="0">--Select a Doctor--</option>';
		while($drInfo=$doctors->FetchRow()){

				$middleInitial = "";
				if (trim($drInfo['name_middle'])!=""){
						$thisMI=split(" ",$drInfo['name_middle']);
						foreach($thisMI as $value){
								if (!trim($value)=="")
										$middleInitial .= $value[0];
						}
						if (trim($middleInitial)!="")
								$middleInitial .= ". ";
				}
                if($drInfo['license_nr'] && !$drInfo['license_nr']==""){
                    $licNr = "Lic.No. ".$drInfo['license_nr'];
                }
                else
                {
                    $licNr = "";
                }
				#$name_doctor = trim($drInfo["name_first"])." ".trim($drInfo["name_2"])." ".$middleInitial.trim($drInfo["name_last"]);
				#$name_doctor = "Dr. ".$name_doctor;
				$name_doctor = trim($drInfo["name_last"]).", ".trim($drInfo["name_first"])." ".$middleInitial; #substr(trim($drInfo["name_middle"]),0,1).$dot;
                $name_doctor = ucwords(strtolower($name_doctor)).", MD,".$licNr;

				#echo "<br> dr = ".$name_doctor;
				#$listDoctors['doctor_name']=$name_doctor;

				$listDoctors[$drInfo["personell_nr"]]=$name_doctor;
				#$listDoctors['doctor_nr']=$drInfo["personell_nr"];

				#print_r($listDoctors);
                if($mriHistoryInfo['dr_nr']==$drInfo["personell_nr"]||$_POST['dr_nr']==$drInfo["personell_nr"]){                    
                    $drRef = $drRef.'<option selected="selected" value="'.$drInfo["personell_nr"].'~'.$name_doctor.'">'.$name_doctor.'</option>';
                                     
		}
                else{
                    $drRef = $drRef.'<option value="'.$drInfo["personell_nr"].'~'.$name_doctor.'">'.$name_doctor.'</option>';
                                
        }
                
                if($mriHistoryInfo['mri_dr_nr']==$drInfo["personell_nr"]||$_POST['mri_dr_nr']==$drInfo["personell_nr"]){                    
                    $drMri = $drMri.'<option selected="selected" value="'.$drInfo["personell_nr"].'~'.$name_doctor.'">'.$name_doctor.'</option>';
                                     
                }
                else{
                    $drMri = $drMri.'<option value="'.$drInfo["personell_nr"].'~'.$name_doctor.'">'.$name_doctor.'</option>';
                                
                }
        }

        if(((!$POST['dr_nr'])&&($_POST['dr_other']))||(($mriHistoryInfo['dr_name'])&&(!$mriHistoryInfo['dr_nr']))){
            $drRef = $drRef.'<option selected="selected" id="other_doctor" name="other_doctor" value="other" > Other . . . </option>';    
        }
        else{
            $drRef = $drRef.'<option id="other_doctor" name="other_doctor" value="other" > Other . . . </option>';    
        }

        if(((!$POST['mri_dr_nr'])&&($_POST['mri_res']))||(($mriHistoryInfo['mri_dr_name'])&&(!$mriHistoryInfo['mri_dr_nr']))){
            $drMri = $drMri.'<option selected="selected" id="other_doctor" name="other_doctor" value="other" > Other . . . </option>';    
        }
        else{
            $drMri = $drMri.'<option id="other_doctor" name="other_doctor" value="other" > Other . . . </option>';    
 }

 }

#----------------------

?>
<html>
<head>
<style type="text/css">
<!--
body {
		margin-left: 0px;
		margin-top: 0px;
		margin-right: 0px;
		margin-bottom: 0px;
		background-color: #F8F9FA;
}
.style2 {
		font-family: Geneva, Arial, Helvetica, sans-serif;
		font-size: 12px;
		font-weight: bold;
}

.style3 {
		font-family: Geneva, Arial, Helvetica, sans-serif;
		font-size: 12px;
		font-weight: normal;
}

-->
</style>
<?php
echo '<script type="text/javascript" language="javascript" src="'.$root_path.'js/jsprototype/prototype.js"></script>';

#added by VAN 06-13-08
#require($root_path.'include/inc_checkdate_lang.php');
echo '<link rel="stylesheet" type="text/css" media="all" href="' .$root_path.'js/jscalendar/calendar-win2k-cold-1.css">'."\n";
echo '<script language="javascript" src="'.$root_path.'js/setdatetime.js"></script>'."\n";
echo '<script language="javascript" src="'.$root_path.'js/checkdate.js"></script>'."\n";
/*echo '<script language="javascript" src="'.$root_path.'js/dtpick_care2x.js"></script>'."\n";*/
echo '<script type="text/javascript" src="'.$root_path.'js/jscalendar/calendar.js"></script>'."\n";
echo '<script type="text/javascript" src="'.$root_path.'js/jscalendar/lang/calendar-en.js"></script>'."\n";
echo '<script type="text/javascript" src="'.$root_path.'js/jscalendar/calendar-setup_3.js"></script>'."\n";
?>

<script language="javascript">
<?php
		require_once($root_path.'include/inc_checkdate_lang.php');
?>
</script>

<script language="javascript">
		String.prototype.trim = function() { return this.replace(/^\s+|\s+$/, ''); };

		function chkForm(){

				if ($F('purpose')==''){
						alert(" Please enter the purpose of requesting the certificate.");
						$('purpose').focus();
						return false;
				}

				return true;
		}

		//Added by Cherry 08-05-10
		function viewResult(batch_nr,pid,dept_nr,rep){
			if(rep==1)
				window.open('<?=$root_path?>modules/laboratory/seg-lab-result-pdf.php?pid='+pid,null,'width=620,height=440,menubar=no,resizable=yes,scrollbars=yes');
			else
				window.open('<?=$root_path?>modules/radiology/certificates/seg-radio-unified-report-pdf.php?batch_nr='+batch_nr+'&pid='+pid+'&dept_nr='+dept_nr,null,'width=620,height=440,menubar=no,resizable=yes,scrollbars=yes');
			//window.open("cert_reinstatement_pdf.php?id="+id,"medicalCertificate","modal, width=600,height=500,menubar=no,resizable=yes,scrollbars=no");
			//window.open('<?=$root_path?>modules/laboratory/seg-lab-result-pdf.php?pid='+pid,null,'width=620,height=440,menubar=no,resizable=yes,scrollbars=yes');

		}

		//End Cherry

        function selectDoctor()
        {
            if(document.getElementById('dr_ref').value=="other"){
                document.getElementById('dr_other1').style.display='';
                document.getElementById('dr_other2').style.display='';    
            }else{
                document.getElementById('dr_other1').style.display='none';
                document.getElementById('dr_other2').style.display='none';
                document.getElementById('dr_other').value="";
            }   
        }
        
        function selectMriRes()
        {
            if(document.getElementById('dr_mri').value=="other"){
                document.getElementById('mri_res1').style.display='';
                document.getElementById('mri_res2').style.display='';    
            }else{
                document.getElementById('mri_res1').style.display='none';
                document.getElementById('mri_res2').style.display='none';
                document.getElementById('mri_res').value="";
            }   
        }

		function closing(){
			 alert('hello');
			//close = <td align="RIGHT"><a href="javascript:return '+fnRef+'cClick();" '+closeevent+'="return '+fnRef+'cClick();" style="color: '+o3_closecolor+'; font-family: '+o3_closefont+'; font-size: '+o3_closesize+o3_closesizeunit+'; text-decoration: '+o3_closedecoration+'; font-weight: '+o3_closeweight+'; font-style:'+o3_closestyle+';">'+close+'</a></td>';
			cClick();
		}

		function checkType(thisType){

				type = thisType;
		}

		function printMedCert(id){


						window.open("cert_reinstatement_pdf.php?id="+id,"medicalCertificate","modal, width=600,height=500,menubar=no,resizable=yes,scrollbars=no");
		}

		//Added by Cherry 08-09-10
		function printMRIHistory(pid,refno,grp){
			//alert('try lang');
				//window.open("seg-radio-mri-history-pdf.php?encounter_nr="+encounter_nr+"&pid="+pid,"medicalCertificate","modal, width=600,height=500,menubar=no,resizable=yes,scrollbars=no");
                window.open("seg-radio-mri-history-pdf.php?pid="+pid+"&refno="+refno+"&grp="+grp,"medicalCertificate","modal, width=600,height=500,menubar=no,resizable=yes,scrollbars=no");
		}


		function preset(){
			//alert('HOY!');
				var d = document.clinical_history;
                var mp = <?php echo $manpay; ?>;
				var encounter_nr = window.parent.$('encounter_nr').value;


              //added by Francis L.G 02-07-2013  
              window.parent.ctmributtons();
              selectDoctor();
              selectMriRes();
              chkMultigroup();
              allowManualPayment(mp);

		}

        function chkMultigroup(){
            var n = document.getElementById('grp').value;
            //alert(n);
            
            if(n){
                        document.getElementById('mri_cli_his_1').style.display='';
                        document.getElementById('service_group_1').style.display='none';

                        if($('dataEdit').value=='1'){
                            document.getElementById('mri_cli_his_0').style.display='';
                        }else{
                            document.getElementById('mri_cli_his_0').style.display='none';
                        }

                    }else{
                        document.getElementById('service_group_1').style.display='';
                        document.getElementById('mri_cli_his_0').style.display='none';
                        document.getElementById('mri_cli_his_1').style.display='none'; 
            }

		}

		function trimString(objct){
		objct.value = objct.value.replace(/^\s+|\s+$/g,"");
		objct.value = objct.value.replace(/\s+/g,"");
}/* end of function trimString */

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

function closeWindow(){
	window.parent.pSearchClose();
}

var seg_validTime=false;
function setFormatTime(thisTime,AMPM){
//    var time = $('time_text_d');
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
				 document.getElementById(AMPM).value = "A.M.";
		}else    if((hour > 12)&&(hour < 24)){
				 hour -= 12;
				 document.getElementById(AMPM).value = "P.M.";
		}

		ftime =  hour + ":" + minute;

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


function allowManualPayment(mp){
    if(mp==1){
        $('manual_payment_title_0').style.display='';
        $('manual_payment_title_1').style.display='';
        $('manual_payment_table').style.display='';
        $('total_manual_payment').style.display='';
    }else{
        $('manual_payment_title_0').style.display='none';
        $('manual_payment_title_1').style.display='none';
        $('manual_payment_table').style.display='none';
        $('total_manual_payment').style.display='none';
    }
    
}

function manualPayment(id){
    var tbl = document.getElementById(id);
    var lastRow = tbl.rows.length;
    var req = $('service_request').value;
    var payType = $('payment_type').value;
    //var amt = ($('amount').value).replace(/[^\d\.\-\ ]/g, '');
    var amt = ($('amount').value).replace(/\,/g,'');
    var rowNum = lastRow+1;

    if(!isNaN(parseFloat(amt)) && isFinite(amt) && (amt>0)){

        var amount = parseFloat(amt).toFixed(2);
        amount = amount.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");

        $('manual_payment_tbody').innerHTML += '<tr id="row'+rowNum+'">'+
                                                    '<td>'+req+'</td>'+
                                                    '<td>'+payType.toUpperCase()+'</td>'+
                                                    '<td>'+amount+'</td>'+
                                                    '<td>'+
                                                        '<input id="'+rowNum+'" type="button" onclick="delRow(this,'+amt+');" value="x" />'+
                                                    '</td>'+
                                                    '<input type="hidden" name="service_request_arr[]" value="'+req+'">'+
                                                    '<input type="hidden" name="payment_type_arr[]" value="'+payType.toUpperCase()+'">'+
                                                    '<input type="hidden" name="amount_arr[]" value="'+amt+'">'+
                                                '</tr>';
    manualPaymentTotal(amt,1);

    }else{
        alert("Please input valid amount!");
    }
    $('amount').value = "";
}


function delRow(obj,amt){
    var dRow = parseInt(obj.id);
    var tbl = document.getElementById('manual_payment_table');
    var rowId = 'row'+(dRow);
    //alert(dRow);
    
    tbl.deleteRow(document.getElementById(rowId).rowIndex);
    
    var lastRow = tbl.rows.length;
    //alert(lastRow);
    
    for(i=dRow;i<=lastRow;i++){
        var row = 'row'+(i+1);
        var dBtn = (i+1);
        //alert(dBtn);
        var nRowNum = i;

        document.getElementById(row).id = 'row'+nRowNum;
        document.getElementById(dBtn).id = nRowNum;
    }
    // var test = $('amount_arr').value;
     //alert(amt);
    manualPaymentTotal(amt,0);
}

function manualPaymentTotal(amt,add){
    var total = ($('man_pay_total').value).replace(/\,/g,'');
    var total = parseFloat(total);
    var nTotal = "";
    
    if(add){
        nTotal = total + parseFloat(amt);
    }else{
        nTotal = total - parseFloat(amt);
    }

    nTotal = parseFloat(nTotal).toFixed(2);
    nTotal = nTotal.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");

    $('man_pay_total').value = nTotal;

}

function submitMode(mod){
    $('mode').value = mod;
    //window.parent.pSearchClose();
}

function chDeleted(){
    window.parent.ctmributtons();
    window.parent.pSearchClose();
}

		//-------------------
								//<body onLoad="preset();"
</script>
</head>
<body onLoad="preset();">
<form id="clinical_history" name="clinical_history" method="post" action="" onSubmit="return chkForm()">
<table id="service_group_1" width="300" height="50" border="0" align="center" cellpadding="0" cellspacing="0" bordercolor="#BAD0FC" class="style2">
    <tr><td>&nbsp;</td></tr>
    <tr style="font-size:15"><td>&nbsp;</td><td>CLINICAL HISTORY :</td></tr>
    <tr><td>&nbsp;</td></tr>
        <?php
            for($i=0;$i<count($radGrpMRI);$i++){
                echo "<tr style='display:none'>";
                echo "<td valign='top' align='right'>";
                echo "<input type='radio' name='srvGrp' id='srvGrp' value='".$radGrpMRI[$i]."'>";
                echo "</td>";
                echo "<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".$radGrpMRI[$i];
                echo "<ul>";
                for($j=0;$j<count($radSrv);$j++){
                    $tmp = $radSrv[$j];
                    $radSrvGrpInfo = $radio_obj->getRadioServiceGroupInfo($tmp);
                    if($radSrvGrpInfo['name']==$radGrpMRI[$i]){
                        echo "<li>".$tmp."</li>";
                    }
                }
                echo "</ul>";
                echo "</td>";
                echo "</tr>";
            }
            echo '<tr style="display:none"><td>&nbsp;</td><td>&nbsp;&nbsp;&nbsp;';
            echo '<input type="submit" name="Submit" value="submit">';
            echo '<input type="button" name="Cancel" value="cancel"  onclick="closeWindow();">';
            echo '</td></tr>';
        ?>
</table>
<table id="mri_cli_his_0" width="520" height="80" border="0" align="center" cellpadding="0" cellspacing="0" bordercolor="#BAD0FC" class="style2" <?php if($allowDataEdit=='0')echo 'style="display:none"';?>>
<!-- <table id="ct_cli_his_1" width="520" height="236" border="0" align="center" cellpadding="0" cellspacing="0" bordercolor="#BAD0FC" class="style2"> -->
		<tr>
				<td colspan="*"><?= $errorMsg ?></td>
		</tr>
		<tr>
				<td colspan="*" height="23" bgcolor="#FFFFFF" >
						<table width="100%" border="0" bgcolor="#F8F9FA"class="style3">
								<tr>
										<td width="18%" ></td>
										<td width="37%" >&nbsp;</td>
										<!--<td width="28%" align="right" ><? echo 'Case No. '?></td>
										<td width="17%" align="left"><? echo $encounter_nr; ?></td>  -->
										<td width="28%" align="right"></td>
										<td width="17%" align="left"></td>
								</tr>
								<tr>
										<td><span class="style3"><? echo "Name :"?> </span></td>
										<td colspan="2" class="style2">
												<?
														#edited by VAN 02-28-08
														$name = stripslashes(strtoupper($pidInfo['name_first'])).' '.stripslashes(strtoupper($pidInfo['name_middle'])).' '.stripslashes(strtoupper($pidInfo['name_last']));
														echo $name;

												?>  <? #echo stripslashes(strtoupper($pidInfo['name_last']));?>
												&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
												<!--<span class="style3"><? echo "Age :  "?></span><? echo $pidInfo['age'].' old';?>  -->

										</td>
										<td>&nbsp;</td>
								</tr>
								<!--<tr>
										<td><span class="style3"><? echo "Age :  "?></span><? echo $pidInfo['age'].' old';?></td>
										<td><span class="style2"><? echo "Sex :  "?></span><? echo $pidInfo['sex']?></td>
										<!--<td><span class="style3"><? echo "Address :"?></span></td>
										<td colspan="2" class="style2">
												<? echo stripslashes(strtoupper($pidInfo['street_name']))."&nbsp;&nbsp; ".stripslashes(strtoupper($pidInfo['brgy_name']))."&nbsp;&nbsp; ".stripslashes(strtoupper($pidInfo['mun_name'])). ", ".stripslashes(strtoupper($pidInfo['prov_name'])). "&nbsp;&nbsp;".stripslashes(strtoupper($pidInfo['zipcode']));?>
										</td>
										<td>&nbsp;</td>
								</tr> -->
							<tr>
										<td><span class="style3"><? echo "Age :"?> </span></td>
										<td colspan="2" class="style2">
												<?
														echo $pidInfo['age'].' old';

												?>  <? #echo stripslashes(strtoupper($pidInfo['name_last']));?>
												&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
												&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                                &nbsp;&nbsp;&nbsp;&nbsp;
												<span class="style3"><? echo "Sex :  "?></span><?
													if($pidInfo['sex']=='f')
														echo "Female";
													else
														echo "Male";

													?>

										</td>
										<td>&nbsp;</td>
								</tr>

							 <tr>
										<td><span class="style3"><? echo "Hospital # :"?> </span></td>
										<td colspan="2" class="style2">
												<?
													echo $pid;
												?>  <? #echo stripslashes(strtoupper($pidInfo['name_last']));?>
												&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
												&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
												&nbsp;&nbsp;
												<span class="style3"><? echo "Case # :  "?></span>
                                                 <? echo ($encounter_nr) ? $encounter_nr : 'Walk In';?>


										</td>
										<td>&nbsp;</td>
								</tr>

						</table>
				</td>
		</tr>
		<tr>
				<td colspan="*" align="left" valign="top" bgcolor="#F8F9FA">&nbsp;</td>
		</tr>
		<tr>
				<td colspan="*" width="467" height="23" bgcolor="#FFFFFF">
						<table width="100%" border="0" cellpadding="0" cellspacing="0">
								<tr>
										<td width="15" align="left" background="images/top_05.jpg" bgcolor="#FFFFFF">&nbsp;</td>
										<td width="442" background="images/top_05.jpg">&nbsp;</td>
										<td width="10" background="images/top_05.jpg" bgcolor="#FFFFFF">&nbsp;</td>
								</tr>
						</table>
				</td>
		</tr>
		<!-- <form id="clinical_history" name="clinical_history" method="post" action="" onSubmit="return chkForm()"> -->
        <tr>
            <td>&nbsp;</td>
        </tr>
        <tr>
            <td valign="top">
                REFERRING/REQUESTING CONSULTANT/DEPARTMENT CHIEF RESIDENT
            </td>                      
        </tr>
        <tr>
            <td>&nbsp;</td>
        </tr>
        <tr>
            <td>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <font style="color:#FF0000">*</font>Select a Doctor :
            </td>
        </tr>
        <tr>
            <td valign="top">
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <select name="dr_ref" id="dr_ref" onchange="selectDoctor()">
                    <?php echo $drRef; ?>
                </select>
            </td>

        </tr>
        <tr id="dr_other1" style="display:none">
            <td>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<font style="color:#FF0000">*</font>Doctor's Name and Licence Number:
            </td>
        </tr>
        <tr id="dr_other2" style="display:none">
            <td align="left">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <input type="text" style="width: 450px;" name="dr_other" id="dr_other" value="<?php if($_POST['dr_other']&&!$_POST['dr_nr']) echo $_POST['dr_other']; else if(($mriHistoryInfo['dr_name'])&&(!$mriHistoryInfo['dr_nr'])){echo $mriHistoryInfo['dr_name'];} else echo ""; ?>">
            </td>
        </tr>
        <tr>
				<td>&nbsp;</td>
		</tr>
        <tr>
            <td>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<font style="color:#FF0000">*</font>Specialty :
            </td>
        </tr>
        <tr>
            <td align="left">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <input type="text" style="width: 450px;" name="dr_specialty" id="dr_specialty" value="<?php if($_POST['dr_specialty']) echo $_POST['dr_specialty']; else echo $mriHistoryInfo['dr_specialty']; ?>">
            </td>
        </tr>
        <tr>
                <td>&nbsp;</td>
        </tr>
        <tr>
            <td valign="top" bgcolor="#F8F9FA">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <font style="color:#FF0000">*</font> Referrer's Address or SPMC Unit or Ward ?
            </td>
        </tr>
        <tr>
            <td align="left">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <textarea cols="53" rows="1" name="dr_address" id="dr_address" wrap="physical"><?php if($_POST['dr_address']) echo $_POST['dr_address']; else echo $mriHistoryInfo['dr_address']; ?></textarea>
            </td>
        </tr>
        <tr>
                <td>&nbsp;</td>
        </tr>
        <tr>
            <td>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<font style="color:#FF0000">*</font>Contact Number:
            </td>
        </tr>
        <tr>
            <td align="left">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <input type="text" style="width: 450px;" name="dr_contact_nr" id="dr_contact_nr" value="<?php if($_POST['dr_contact_nr']) echo $_POST['dr_contact_nr']; else echo $mriHistoryInfo['dr_contact_nr']; ?>">
            </td>
        </tr>
        <tr>
                <td>&nbsp;</td>
        </tr>
        <tr>
            <td>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<font style="color:#FF0000">*</font>Phone/Fax:
            </td>
        </tr>
        <tr>
            <td align="left">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <input type="text" style="width: 450px;" name="dr_phone" id="dr_phone" value="<?php if($_POST['dr_phone']) echo $_POST['dr_phone']; else echo $mriHistoryInfo['dr_phone']; ?>">
                           </td>
        </tr>
        <tr>
                <td>&nbsp;</td>
        </tr>
        
        <?php
            $phpfd=$date_format;
            $phpfd=str_replace("dd", "%d", strtolower($phpfd));
            $phpfd=str_replace("mm", "%m", strtolower($phpfd));
            $phpfd=str_replace("yyyy","%Y", strtolower($phpfd));
                                        
            if (($mriHistoryInfo['refer_date']!='0000-00-00')&&($mriHistoryInfo['refer_date']!=""))
                $referDate= @formatDate2Local($mriHistoryInfo['refer_date'],$date_format);
            else
                $referDate = '';

            if($_POST['refer_date']) $referDate = $_POST['refer_date'];
                                                
            $referDateJS= '<input name="refer_date" type="text" size="15" maxlength=10 value="'.$referDate.'"'.
                            'onFocus="this.select();"id = "refer_date"
                            onBlur="IsValidDate(this,\''.$date_format.'\'); "
                            onKeyUp="setDate(this,\''.$date_format.'\',\''.$lang.'\')">
                            <img '.createComIcon($root_path,'show-calendar.gif','0','absmiddle').' id="refer_date_trigger" 
                            style="cursor:pointer" >
                             <font size=1>[';
            ob_start();                                                           
        ?>
                                        
            <script type="text/javascript">
                Calendar.setup ({
                inputField : "refer_date", ifFormat : "<?php echo $phpfd?>", showsTime : false, button : "refer_date_trigger", singleClick : true, step : 1
                });
            </script>
            <?php
                $calendarSetup = ob_get_contents();
                ob_end_clean();

                $referDateJS .= $calendarSetup;
                /**/
                $dfbuffer="LD_".strtr($date_format,".-/","phs");
                $referDateJS = $referDateJS.$$dfbuffer.']';
            ?>
        <tr id="biopsy1">
            <td align="left" id="">
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                                
                <font style="color:#FF0000">*</font>Date&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: &nbsp;                                       
                <?php echo $referDateJS; ?>
            </td>    
        </tr>
        <tr>
                <td>&nbsp;</td>
        </tr>       
        <tr>
            <td valign="top" bgcolor="#F8F9FA">
                <font style="color:#FF0000">*</font> INDICATION OF THE MRI EXAMINATION (MEDICAL PROBLEM) ?
            </td>
        </tr>
        <tr>
            <td align="right">
                <textarea cols="43" rows="5" name="med_prob" id="med_prob" wrap="physical"><?php if($_POST['med_prob']) echo $_POST['med_prob']; else echo $mriHistoryInfo['med_prob']; ?></textarea>
            </td>
        </tr>
        <tr>
                <td>&nbsp;</td>
        </tr>
        <tr>
            <td valign="top" bgcolor="#F8F9FA">
                <font style="color:#FF0000">*</font> Any specific information you wish to gain from the study ?
            </td>
        </tr>
        <tr>
            <td align="right">
                <textarea cols="43" rows="5" name="info_gain" id="info_gain" wrap="physical"><?php if($_POST['info_gain']) echo $_POST['info_gain']; else echo $mriHistoryInfo['info_gain']; ?></textarea>
            </td>
        </tr>
        <tr>
            <td>&nbsp;</td>
        </tr>
        <tr>
            <td><font style="color:#FF0000">*</font>PLEASE CHECK :</td>
        </tr>
        <tr>
            <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <input type="radio" name="mri_purpose" id="mri_purpose_f1" value="1" <?php echo $f1; ?>>
                F1 (for diagnosis)
                <ul style="list-style: none;">
                    <li>MRI is initial imaging modality for diagnosis</li>
                </ul> 
            </td>
        </tr>
        <tr>    
            <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <input type="radio" name="mri_purpose" id="mri_purpose_f2" value="2" <?php echo $f2; ?>>
                F2 (for further investigation)            
                <ul style="list-style: none;">
                    <li>Secondary imaging, diagnosis uncertain or to assess extent of severity condition</li>
                </ul>
            </td>
        </tr>
        <tr>
            <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <input type="radio" name="mri_purpose" id="mri_purpose_f1" value="3" <?php echo $f3; ?>>
                F3 (for monitoring)
                <ul style="list-style: none;">
                    <li>Diagnosis is confirmed, to assess progress following treatment</li>
                </ul> 
            </td>
        </tr>
        <tr>
				<td>&nbsp;</td>
		</tr>
		<tr>
				<td valign="top" bgcolor="#F8F9FA">
                        <font style="color:#FF0000">*</font> CHIEF COMPLAINT :
				</td>
		</tr>
		<tr>
						<td align="right">
                <textarea cols="43" rows="5" name="chief_comp" id="chief_comp" wrap="physical"><?php if($_POST['chief_comp'])echo $_POST['chief_comp']; else echo $mriHistoryInfo['chief_comp']; ?></textarea>
            </td>
		</tr>
		<tr>
				<td>&nbsp;</td>
		</tr>
		<tr>
				<td valign="top" bgcolor="#F8F9FA">
                        <font style="color:#FF0000">*</font> HISTORY :
				</td>
		</tr>
		<tr>
						<td align="right">
                <textarea cols="43" rows="5" name="history" id="history" wrap="physical"><?php if($_POST['history']) echo $_POST['history']; else echo $mriHistoryInfo['history']; ?></textarea>
            </td>
		</tr>
		<tr>
				<td>&nbsp;</td>
		</tr>
		<tr>
			<td valign="top" bgcolor="#F8F9FA">
                <font style="color:#FF0000">*</font> PHYSICAL EXAMINATION :
			</td>
		</tr>
		<tr>
			<td align="right">
                <textarea cols="43" rows="5" name="phy_exam" id="phy_exam" wrap="physical"><?php if($_POST['phy_exam']) echo $_POST['phy_exam']; else echo $mriHistoryInfo['phy_exam']; ?></textarea>
			</td>
		</tr>
		<tr>
				<td>&nbsp;</td>
		</tr>

		<tr>
            <td valign="top" bgcolor="#F8F9FA">
                <font style="color:#FF0000">*</font> IMPRESSION :
									</td>
								</tr>
								<tr>
            <td align="right">
                <textarea cols="43" rows="5" name="impression" id="impression" wrap="physical"><?php if($_POST['impression']) echo $_POST['impression']; else echo $mriHistoryInfo['impression']; ?></textarea>
									</td>
								</tr>
								<tr>
                <td>&nbsp;</td>
								</tr>

								<tr>
            <td valign="top" bgcolor="#F8F9FA">
                <font style="color:#FF0000">*</font> PAST MEDICAL HSTORY :
									</td>
								</tr>
								<tr>
            <td align="right">
                <textarea cols="43" rows="5" name="past_med_his" id="past_med_his" wrap="physical"><?php if($_POST['past_med_his']) echo $_POST['past_med_his']; else echo $mriHistoryInfo['past_med_his']; ?></textarea>
				</td>
		</tr>
		<tr>
			<td>&nbsp;</td>
		</tr>
		<tr>
            <td>CREATININE &nbsp;&nbsp;
                <input type="text" name="creatinine" id="creatinine" value="<?php if($_POST['creatinine'])echo $_POST['creatinine']; else echo $mriHistoryInfo['creatinine']; ?>">
                &nbsp;&nbsp;&nbsp;&nbsp;
                BUN &nbsp;&nbsp;
                <input type="text" name="bun" id="bun" value="<?php if($_POST['bun'])echo $_POST['bun']; else echo $mriHistoryInfo['bun']; ?>">
			</td>
		</tr>
        <tr>
            <td>&nbsp;</td>
        </tr>
        <tr>
            <td valign="top">
                MRF-1 Reviewed by :
            </td>                      
        </tr>
        <tr>
            <td>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <font style="color:#FF0000">*</font>MRI Resident-in-charge :
            </td>
        </tr>
        <tr>
            <td valign="top">
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;                
                <select name="dr_mri" id="dr_mri" onchange="selectMriRes()">
                    <?php echo $drMri; ?>
                </select>
            </td>
                           
        </tr>
        <tr id="mri_res1" style="display:none">
            <td>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<font style="color:#FF0000">*</font>Doctor's Name and Licence Number:
            </td>
        </tr>
        <tr id="mri_res2" style="display:none">
            <td align="left">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <input type="text" style="width: 450px;" name="mri_res" id="mri_res" value="<?php if($_POST['mri_res']&&!$_POST['mri_dr_nr']) echo $_POST['mri_res']; else if(($mriHistoryInfo['mri_dr_name'])&&(!$mriHistoryInfo['mri_dr_nr'])){echo $mriHistoryInfo['mri_dr_name'];} else echo ""; ?>">
			</td>
		</tr>

		<tr>
			<td>&nbsp;</td>
		</tr>

</table>
<table id="mri_cli_his_1" width="520" height="50" border="0" align="center" cellpadding="0" cellspacing="0" bordercolor="#BAD0FC" class="style2">

		<tr>
            <td>&nbsp;</td>
        </tr>

        <tr id="manual_payment_title_0">
            <td> Manual Payment : &nbsp;</td>
        </tr>

        <tr id="manual_payment_title_1">
            <td>&nbsp;</td>
        </tr>

        <tr>
            <td>
                <table id="manual_payment_table" border="1" cellspacing="0" width="100%" bgcolor="#F8F9FA" class="style2">
                    <tbody id="manual_payment_tbody" align="center">
                        <tr>
                            <td>REQUEST</td>
                            <td>PAYMENT TYPE</td>
                            <td>AMOUNT</td>
                            <td>OPTIONS</td>
                        </tr>
                        <?php
                            //$options1 = "";
                            for($j=0;$j<count($radSrv);$j++){
                                $tmp = $radSrv[$j];
                                $radSrvGrpInfo = $radio_obj->getRadioServiceGroupInfo($tmp);
                                if($radSrvGrpInfo['name']==$grp){
                                    $options1.='<option value="'.$tmp.'">'.$tmp.'</option>';
                                }
                            }

                            $result = $enc_obj->getChargeType("WHERE id NOT IN ('')","ordering");
                            // $options2 = "<option value='CASH'>CASH</option>";
                            while ($row=$result->FetchRow()) {
                                $options2.='<option value="'.$row['id'].'">'.$row['charge_name'].'</option>';
                            }
                        ?>
                        <tr>
                            <td><select name="service_request" id="service_request"> <?php echo $options1;?> </select></td>

                            <td><select name="payment_type" id="payment_type"> <?php echo $options2;?> </select></td>

                            <td><input type="text" style="text-align:right;width:100" name="amount" id="amount" /></td>

                            <td><input type="button" onclick="manualPayment('manual_payment_tbody');" value="Add" /></td>
                        </tr>
                        <?php
                            if($_POST['service_request_arr']){
                                $sr = $_POST['service_request_arr'];
                                $pt = $_POST['payment_type_arr'];
                                $a = $_POST['amount_arr'];
                            }else if($mriHistoryInfo['mp_request']){
                                $srTmp = $mriHistoryInfo['mp_request'];
                                $sr = explode(",", $srTmp);
                                $ptTmp = $mriHistoryInfo['mp_trans_type'];
                                $pt = explode(",", $ptTmp);
                                $aTmp = $mriHistoryInfo['mp_amount'];
                                $a = explode("-", $aTmp);
                            }else{
                                $sr = "";
                                $pt = "";
                                $a = "";
                            }

                            if($sr){
                                for($i=0;$i<count($sr);$i++){
                                    $rowNum = 3+$i;

                                    $amnt = number_format($a[$i], 2);

                                    echo '<tr id="row'.$rowNum.'">';
                                    echo '<td>'.$sr[$i].'</td>';
                                    echo '<td>'.$pt[$i].'</td>';
                                    echo '<td>'.$amnt.'</td>';
                                    echo '<td><input id="'.$rowNum.'" type="button" onclick="delRow(this,'.$a[$i].');" value="x" /></td>';
                                    echo '<input type="hidden" name="service_request_arr[]" value="'.$sr[$i].'">';
                                    echo '<input type="hidden" name="payment_type_arr[]" value="'.$pt[$i].'">';
                                    echo '<input type="hidden" name="amount_arr[]" value="'.$a[$i].'">';
                                    echo '</tr>';
                                }
                            }
                        ?>
                    </tbody>
                </table>
                <table id="total_manual_payment" width="100%" border="0" align="right" cellpadding="0" cellspacing="0" class="style2">
                    <?php
                        if($_POST['man_pay_total']){
                            $totalMP = $_POST['man_pay_total'];
                        }else if($mriHistoryInfo['mp_total']){
                            $totalMP = $mriHistoryInfo['mp_total'];    
                        }else{
                            $totalMP = 0.00;
                        }
                    ?>
                    <tr><td>&nbsp;</td><tr>
                    <tr align="right">
                        <td align="right">
                            Total &nbsp;:
                            <input type="text" name="man_pay_total" id="man_pay_total" value="<?=$totalMP?>" style="text-align:right;width:120" readonly>
                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			</td>
                        <td align="right">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
		</tr>
                </table>
            </td>
        </tr>
        <?php
            if(!$allowDataEdit && !$manpay){
                echo '<tr>';
                echo '<td>&nbsp;</td>';
                echo '</tr>';
                echo '<tr>';
                echo '<td align="center"><font style="color:#FF0000;font-style:bold">This person has saved Clinical History, click "Print" button to view.</font></td>';
                echo '</tr>';
                echo '<tr>';
                echo '<td>&nbsp;</td>';
                echo '</tr>';
            }
        ?>

		<tr>
			<td>&nbsp;</td>
		</tr>

		<tr>
				<td align="center" background="images/top_05.jpg" bgcolor="#EDF2FE">
<?php
						if (!$mriHistoryInfo || empty($mriHistoryInfo)){

								echo '            <input type="hidden" name="mode" id="mode" value="save">'."\n";
								echo '            <input type="submit" name="Submit" value="Save">'."\n";

                        }else if(!$allowDataEdit && !$manpay){

                                echo '<input type="hidden" name="mode" id="mode" value="delete">'."\n";
                                echo '<input type="button" name="Print" value="Print" onClick="printMRIHistory(\''.$pid.'\',\''.$refno.'\',\''.$grp.'\')">'."\n";
                                echo '<input type="submit" name="Submit" value="Delete" onClick="if(confirm(\'Are you sure you want to delete this Clinical History ?\')){submitMode(\'delete\');}else{submitMode(\'\');}">'."\n";
                                
						}else{

								echo '            <input type="hidden" name="mode" id="mode" value="update">'."\n";
								echo '<input type="button" name="Print" value="Print" onClick="printMRIHistory(\''.$pid.'\',\''.$refno.'\',\''.$grp.'\')">'."\n";
								echo '            <input type="submit" name="Submit" value="Update">'."\n";
                                echo '<input type="submit" name="Submit" value="Delete" onClick="if(confirm(\'Are you sure you want to delete this Clinical History ?\')){submitMode(\'delete\');}else{submitMode(\'\');}">'."\n";
						}
                        
                        $uuid = $mriHistoryInfo['uuid'];
						echo '<input type="hidden" name="uuid" id="uuid" value="'.$uuid.'">'."\n";
?>
						
						<input type="button" name="Cancel" value="Cancel"  onclick="closeWindow();"
						<input type="hidden" name="pid" id="pid" value="<?=$pid?>">
						<input type="hidden" name="refno" id="refno" value="<?php echo $refno;?>">
                        <input type="hidden" name="encounter_nr" id="encounter_nr" value="<?php echo $encounter_nr;?>">
                        <input type="hidden" name="grp" id="grp" value="<?=$grp?>">
                        <input type="hidden" name="dataEdit" id="dataEdit" value="<?=$allowDataEdit?>">
                        <input type="hidden" name="mpType" id="mpType" value="<?=$mriHistoryInfo['mp_trans_type']?>">
                        <input type="hidden" name="mpEncoder" id="mpEncoder" value="<?=$mriHistoryInfo['encoder']?>">
				</td>
		</tr>
        <tr>
                <td colspan="*" align="center"><?= $errorMsg ?></td>
        </tr>
</table>
		</form>
</body>
</html>
