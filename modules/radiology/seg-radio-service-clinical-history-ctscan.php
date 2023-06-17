<?php

#created by Cherry 09-14-09
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

include_once($root_path.'include/care_api_classes/class_person.php');
$per_obj=new Person;

#Added by Francis L.G 01-25-13
require($root_path.'modules/radiology/ajax/radio-service-tray.common.php');

#Added by Cherry 08-05-10
require_once($root_path.'include/care_api_classes/class_dateGenerator.php');
$dategen = new DateGenerator;

require_once($root_path.'include/care_api_classes/class_personell.php');
require_once($root_path.'include/care_api_classes/class_department.php');

# Create global config object
require_once($root_path.'include/care_api_classes/class_globalconfig.php');
require_once($root_path.'include/inc_date_format_functions.php');

$glob_obj=new GlobalConfig($GLOBAL_CONFIG);
$glob_obj->getConfig('refno_%');
if($glob_obj->getConfig('date_format')) $date_format=$GLOBAL_CONFIG['date_format'];
$date_format=$GLOBAL_CONFIG['date_format'];

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
    $radGrpCT = array();
    for($i=0;$i<count($radSrv);$i++){
        $tmp = $radSrv[$i];
        $radSrvGrpInfo = $radio_obj->getRadioServiceGroupInfo($tmp);
        if(!in_array($radSrvGrpInfo['name'], $radGrpCT)){
            if($radSrvGrpInfo['department_nr']==167)
                $radGrpCT[] = $radSrvGrpInfo['name'];
        }
    }
}
else{
    $radGrpCT[] = "";    
}

$grp = 0;
$allowDataEdit=0;

if($_POST['grp']){
    $grp = $_POST['grp'];
}else if(count($radGrpCT)==1){
    $grp = $radGrpCT[0];
}else if($_POST['srvGrp']){
    $grp = $_POST['srvGrp'];
}
else{
    $grp = "";
}

// echo "raw = ".$radSrv." -";
 // print_r($radGrpCT);
 // print_r(" this ");
 // print_r($grp);
// print_r(" num=");
// print_r($radSrvNr);

//added by Francis 05-25-13
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

//added by Francis 05-17-2013
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
//echo $encoder;

# echo "refno= ".$refno."<br>";
#echo "had_surgery= ".$_POST['had_surgery']."<br>";
include_once($root_path.'include/care_api_classes/class_cert_med.php');
//$obj_medCert = new MedCertificate($encounter_nr);

$errorMsg='';

//echo "post=".print_r($_POST);


#----------------------------------------------
//$HTTP_POST_VARS['dr_nr'] = $_POST['doctors'];

//echo "pid=".$pid." refno=".$refno." grp=".$grp;

if (isset($_POST['mode'])){
    //echo $_POST['mode'];

    if(!$_POST['had_surgery'])
        $_POST['had_surgery']='0';
    if(!$_POST['laboratory'])
        $_POST['laboratory']='0';
    if(!$_POST['has_blood_chem'])
        $_POST['has_blood_chem']='0';
    if(!$_POST['has_xray'])
        $_POST['has_xray']='0';
    if(!$_POST['has_ultrasound'])
        $_POST['has_ultrasound']='0';
    if(!$_POST['has_ct_mri'])
        $_POST['has_ct_mri']='0';
    if(!$_POST['has_biopsy'])
        $_POST['has_biopsy']='0';

    //added by Francis L.G 02-07-2013    
    if($_POST['uuid']){
        $uuid = $_POST['uuid'];
    }
    else{
        $uuid = '';
    }

    //echo $uuid;
    
    if($_POST['dr_other']){
        $_POST['dr_in'] = "";
        $_POST['dr_name'] = $_POST['dr_other'];     
    }
    else if(($_POST['doctor_in'])&&($_POST['doctor_in']!="other")){
        list($_POST['dr_in'],$_POST['dr_name']) = explode("~",$_POST['doctor_in']);
    }
    else{    
        $lack = '1';
    }
    
    if(!$_POST['cln_imp'])
        $lack = '1';
    if(!$_POST['chf_cmp'])
        $lack = '1';
    if(!$_POST['subj_comp'])
        $lack = '1';
    if(!$_POST['obj_comp'])
        $lack = '1';
    if(!$_POST['assessment'])
        $lack = '1';
     
    
    if($_POST['had_surgery']=='0'){
        $surg_date = null;
        $surg_proc = null;
    }
    else if(($_POST['had_surgery']=='1')&&($_POST['surgery_date']=="")||$_POST['surgery_proc']==""){
        $lack = '1';    
    }
    else{
        $surg_date = date('Y-m-d', strtotime($_POST['surgery_date']));
        $surg_proc = $_POST['surgery_proc'];    
    }
    
    if($_POST['has_blood_chem']=='0'){
        $dateBloodChem = "";
        $_POST['bld_chm_res']="";
        $_POST['bld_chm_rem']="";
    }
    else if(($_POST['has_blood_chem']=='1')&&($_POST['date_blood_chem'] == ""||$_POST['bld_chm_res'] == ""||$_POST['bld_chm_rem'] == "")){
        $lack = '1';    
    }
    else{
        $dateBloodChem = date('Y-m-d', strtotime($_POST['date_blood_chem']));    
    }
    
    
    if($_POST['has_xray']=='0'){
        $dateXray = "";
        $_POST['xray_res']="";
        $_POST['xray_rem']="";
    }
    else if(($_POST['has_xray']=='1')&&($_POST['date_xray'] == ""||$_POST['xray_res']==""||$_POST['xray_rem']=="")){
        $lack = '1';    
    }
    else{
        $dateXray = date('Y-m-d', strtotime($_POST['date_xray']));    
    }
    
    
    
    if($_POST['has_ultrasound']=='0'){
        $dateUltrasound = "";
        $_POST['ultrasound_res']="";
        $_POST['ultrasound_rem']="";
    }
    else if(($_POST['has_ultrasound']=='1')&&($_POST['date_ultrasound'] ==""||$_POST['ultrasound_res']==""||$_POST['ultrasound_rem']=="")){
        $lack = '1';
    }
    else{
        $dateUltrasound = date('Y-m-d', strtotime($_POST['date_ultrasound']));    
    }
    
    
            
    if($_POST['has_ct_mri']=='0'){
        $dateCtMri = "";
        $_POST['ct_mri_res']="";
        $_POST['ct_mri_rem']="";
    }
    else if(($_POST['has_ct_mri']=='1')&&($_POST['date_ct_mri'] == ""||$_POST['ct_mri_res']==""||$_POST['ct_mri_rem']=="")){
        $lack = '1';    
    }
    else{
        $dateCtMri = date('Y-m-d', strtotime($_POST['date_ct_mri']));    
    }
    
    
    if($_POST['has_biopsy']=='0'){
        $dateBiopsy = "";
        $_POST['biopsy_res']="";
        $_POST['biopsy_rem']="";
    }
    else if(($_POST['has_biopsy']=='1')&&($_POST['date_biopsy']==""||$_POST['biopsy_res']==""||$_POST['biopsy_rem']=="")){
        $lack = '1';    
    }
    else{
        $dateBiopsy = date('Y-m-d', strtotime($_POST['date_biopsy']));    
    }
    
    if($_POST['medico_legal']=='0'){
        $_POST['noi'] ="";
        $dateOfInjury ="";
        $_POST['poi'] ="";
        $_POST['toi'] ="";
        $medicoLegal ='0';
    }
    else if(($_POST['medico_legal']=='1')&&($_POST['noi']==""||$_POST['doi']==""||$_POST['poi']==""||$_POST['toi']=="")){
        $lack = '1';    
    }
    else{
        $medicoLegal = '1';
        $dateOfInjury = date('Y-m-d', strtotime($_POST['doi']));    
    }
    

    if (trim($_POST['toi'])){
        $time = $_POST['toi'].":00 ".$_POST['selAMPM'];
        $prev_hr = $_POST['toi'];
        $prev_mer = $_POST['selAMPM'];
        $_POST['toi'] = date("H:i:s",strtotime($time));

        if ( (strstr($prev_hr,'12'))&&($prev_mer=='AM')){
            $_POST['toi'] = '24:'.date("i",strtotime($_POST['toi'])).":00";
        }
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
            // echo "foo + ";
        }else{
            $mpEncoder = $_POST['mpEncoder'];
            // echo "bar + ";
        }       
    }else{
            if($paymentType){
            $mpEncoder = $_SESSION['sess_temp_userid'];
            // echo "foobar + ";
            }
    }
            
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
                    'encounter_nr'=>$encounter_nr,
                    'cln_imp'=>$_POST['cln_imp'],
                    'chf_cmp'=>$_POST['chf_cmp'],
                                                    'subj_comp'=>$_POST['subj_comp'],
                                                    'obj_comp'=>$_POST['obj_comp'],
                                                    'assessment'=>$_POST['assessment'],
                                                    'has_conscious'=>$_POST['has_conscious'],
                                                    'did_vomit'=>$_POST['did_vomit'],
                                                    'gcs'=>$_POST['gcs'],
                                                    'rls'=>$_POST['rls'],
                                                    'had_surgery'=>$_POST['had_surgery'],
                                                    'surgery_date'=>$surg_date,
                                                    'surgery_proc'=>$surg_proc,
                                                    'date_blood_chem'=>$dateBloodChem,
                                                    'date_xray'=>$dateXray,
                                                    'date_ultrasound'=>$dateUltrasound,
                                                    'date_ct_mri'=>$dateCtMri,
                                                    'date_biopsy'=>$dateBiopsy,
                                                    'has_blood_chem'=>$_POST['has_blood_chem'],
                                                    'has_xray'=>$_POST['has_xray'],
                                                    'has_ultrasound'=>$_POST['has_ultrasound'],
                                                    'has_ct_mri'=>$_POST['has_ct_mri'],
                                                    'has_biopsy'=>$_POST['has_biopsy'],
                    'doctor_in'=>$_POST['dr_in'],
                    'dr_name'=>$_POST['dr_name'],
                    'create_id'=>$_SESSION['sess_temp_userid'],
                                                    'create_tm'=>date("Y-m-d H:i:s"),
                    'modify_id'=>$_SESSION['sess_temp_userid'],
                                                    'modify_tm'=>date("Y-m-d H:i:s"),
                    'bld_chm_res'=>$_POST['bld_chm_res'],
                    'bld_chm_rem'=>$_POST['bld_chm_rem'],
                    'xray_res'=>$_POST['xray_res'],
                    'xray_rem'=>$_POST['xray_rem'],
                    'ultrasound_res'=>$_POST['ultrasound_res'],
                    'ultrasound_rem'=>$_POST['ultrasound_rem'],
                    'ct_mri_res'=>$_POST['ct_mri_res'],
                    'ct_mri_rem'=>$_POST['ct_mri_rem'],
                    'biopsy_res'=>$_POST['biopsy_res'],
                    'biopsy_rem'=>$_POST['biopsy_rem'],
                    'medico_legal'=>$medicoLegal,
                    'noi'=>$_POST['noi'],
                    'doi'=>$dateOfInjury,
                    'poi'=>$_POST['poi'],
                    'toi'=>$_POST['toi'], 
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
                        $save = $radio_obj->saveCTscanClinicalHistory($data);
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
                        $update = $radio_obj->updateCTClinicalHistory($pid,$uuid,$data);

                        if ($update){
                                $errorMsg='<font style="color:#FF0000;font-style:italic">'."Updated sucessfully!".'</font>';
                        }else{
                                $errorMsg='<font style="color:#FF0000">'."Update Failed!".'</font>';
                        }
                        }
                        else {
                            $errorMsg='<font style="color:#FF0000">'."**PLEASE FILL IN ALL THE NECCESSARY INFORMATION**".'</font>';
                        }
                break;
                case 'delete':
                        $delete = $radio_obj->deleteCTClinicalHistory($uuid);

                        if($delete){
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

$cthistoryInfo = $radio_obj->getCTHistoryInfo($pid,$refno,$grp);

if($cthistoryInfo){
    $_POST['uuid'] = $cthistoryInfo['uuid'];
    if($cthistoryInfo['medico_legal']=='1'){
        $medLegalNo="";
        $medLegalYes="checked";    
    }
    else{
        $medLegalNo="checked";
        $medLegalYes="";
    }
}
else{
    $medLegalNo="checked";
    $medLegalYes="";
}

if($_POST['medico_legal']=='1'){
    $medLegalNo="";
    $medLegalYes="checked";    
}

if($grp){
    if($_POST['dataEdit']){
        $allowDataEdit = $_POST['dataEdit'];
        //echo 'a';
    }else if((!$cthistoryInfo) || ($cthistoryInfo['create_id']==$encoder)){
        $allowDataEdit = 1;
        //echo 'b';
    }else{
        $allowDataEdit = 0;
        //echo 'c';
        //echo $cthistoryInfo['create_id'];
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
        
        $sTemp = '<option value="0">--Select a Doctor--</option>';
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
                if($cthistoryInfo['doctor_nr']==$drInfo["personell_nr"]||$_POST['dr_in']==$drInfo["personell_nr"]){                    
                    $sTemp = $sTemp.'<option selected="selected" value="'.$drInfo["personell_nr"].'~'.$name_doctor.'">'.$name_doctor.'</option>';
                                     
                }
                else{
                    $sTemp = $sTemp.'<option value="'.$drInfo["personell_nr"].'~'.$name_doctor.'">'.$name_doctor.'</option>';
                }
                
        }
        if(((!$POST['dr_in'])&&($_POST['dr_other']))||(($cthistoryInfo['dr_name'])&&(!$cthistoryInfo['doctor_nr']))){
            $sTemp = $sTemp.'<option selected="selected" id="other_doctor" name="other_doctor" value="other" > Other . . . </option>';    
        }
        else{
            $sTemp = $sTemp.'<option id="other_doctor" name="other_doctor" value="other" > Other . . . </option>';    
 }


 } 
 
 if($cthistoryInfo['medico_legal']=='1'){
    $noiVal = $cthistoryInfo['noi'];
    $toiVal = $cthistoryInfo['toi'];
    $doiVal = $cthistoryInfo['doi'];
    $poiVal = $cthistoryInfo['poi'];
    
 }
 else{
    $noiVal = "";
    $toiVal = "";
    $doiVal = "";
    $poiVal = "";
 }
 
 if($_POST['medico_legal']=='1'){
     if($_POST['noi'])$noiVal = $_POST['noi'];
     if($_POST['toi'])$toiVal = $_POST['toi'];
     if($_POST['poi'])$poiVal = $_POST['poi'];     
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

        function viewMedicoLegal()
        {
            if(document.clinical_history.medico_legal_yes.checked==true){
                document.getElementById('medico_legal1').style.display='';
                document.getElementById('medico_legal2').style.display='';
                document.getElementById('medico_legal3').style.display='';
                document.getElementById('medico_legal4').style.display='';
                document.getElementById('space1').style.display='';
                document.getElementById('space2').style.display='';
                document.getElementById('space3').style.display='';
                document.getElementById('space4').style.display='';
            }else{
                document.getElementById('medico_legal1').style.display='none';
                document.getElementById('medico_legal2').style.display='none';
                document.getElementById('medico_legal3').style.display='none';
                document.getElementById('medico_legal4').style.display='none';
                document.getElementById('space1').style.display='none';
                document.getElementById('space2').style.display='none';
                document.getElementById('space3').style.display='none';
                document.getElementById('space4').style.display='none';
            }
        }
        
        function selectDoctor()
        {
            if(document.getElementById('doctor_in').value=="other"){
                document.getElementById('dr_other1').style.display='';
                document.getElementById('dr_other2').style.display='';    
            }else{
                document.getElementById('dr_other1').style.display='none';
                document.getElementById('dr_other2').style.display='none';
                document.getElementById('dr_other').value="";
            }   
        }


        function showSurgicalDetails()
        {
            if(document.clinical_history.had_surgery.checked==true){
                document.getElementById('proc_date').style.display = '';
                document.getElementById('proc').style.display='';
            }else{
                document.getElementById('proc_date').style.display = 'none';
                document.getElementById('proc').style.display = 'none';
            }
        }
        
        function viewBloodChemDetails()
        {
            if(document.clinical_history.has_blood_chem.checked==true){
                document.getElementById('blood_chem1').style.display='';
                document.getElementById('blood_chem2_a').style.display='';
                document.getElementById('blood_chem2_b').style.display='';
                document.getElementById('blood_chem3').style.display='';
                document.getElementById('blood_chem4').style.display='';
            }else{
                document.getElementById('blood_chem1').style.display = 'none';
                document.getElementById('blood_chem2_a').style.display='none';
                document.getElementById('blood_chem2_b').style.display='none';
                document.getElementById('blood_chem3').style.display = 'none';
                document.getElementById('blood_chem4').style.display='none';
            }
        }

        function viewBiopsyDetails(){
            if(document.clinical_history.has_biopsy.checked==true){
                document.getElementById('biopsy1').style.display='';
                document.getElementById('biopsy2_a').style.display='';
                document.getElementById('biopsy2_b').style.display='';
                document.getElementById('biopsy3').style.display='';
                document.getElementById('biopsy4').style.display='';
            }else{
                document.getElementById('biopsy1').style.display='none';
                document.getElementById('biopsy2_a').style.display='none';
                document.getElementById('biopsy2_b').style.display='none';
                document.getElementById('biopsy3').style.display='none';
                document.getElementById('biopsy4').style.display='none';
            }
        }

        function viewCTDetails(){
            if(document.clinical_history.has_ct_mri.checked==true){
                document.getElementById('ct_mri1').style.display='';
                document.getElementById('ct_mri2_a').style.display='';
                document.getElementById('ct_mri2_b').style.display='';
                document.getElementById('ct_mri3').style.display='';
                document.getElementById('ct_mri4').style.display='';
            }else{
                document.getElementById('ct_mri1').style.display='none';
                document.getElementById('ct_mri2_a').style.display='none';
                document.getElementById('ct_mri2_b').style.display='none';
                document.getElementById('ct_mri3').style.display='none';
                document.getElementById('ct_mri4').style.display='none';
            }
        }

        function viewUltrasoundDetails(){
            if(document.clinical_history.has_ultrasound.checked==true){
                document.getElementById('ultrasound1').style.display='';
                document.getElementById('ultrasound2_a').style.display='';
                document.getElementById('ultrasound2_b').style.display='';
                document.getElementById('ultrasound3').style.display='';
                document.getElementById('ultrasound4').style.display='';
            }else{
                document.getElementById('ultrasound1').style.display='none';
                document.getElementById('ultrasound2_a').style.display='none';
                document.getElementById('ultrasound2_b').style.display='none';
                document.getElementById('ultrasound3').style.display='none';
                document.getElementById('ultrasound4').style.display='none';
            }
        }

        function viewXrayDetails(){
            if(document.clinical_history.has_xray.checked==true){
                document.getElementById('xray1').style.display='';
                document.getElementById('xray2_a').style.display='';
                document.getElementById('xray2_b').style.display='';
                document.getElementById('xray3').style.display='';
                document.getElementById('xray4').style.display='';
            }else{
                document.getElementById('xray1').style.display='none';
                document.getElementById('xray2_a').style.display='none';
                document.getElementById('xray2_b').style.display='none';
                document.getElementById('xray3').style.display='none';
                document.getElementById('xray4').style.display='none';
            }
        }


        //End Cherry

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
        function printCTHistory(pid,refno,grp){
            //alert('try lang');

                window.open("seg-radio-ct-history-pdf.php?pid="+pid+"&refno="+refno+"&grp="+grp,"medicalCertificate","modal, width=600,height=500,menubar=no,resizable=yes,scrollbars=no");
               //window.open("seg-radio-ct-history-pdf.php?pid="+pid+"&refno="+refno+"&grp="+grp,"medicalCertificate","modal, width=600,height=500,menubar=no,resizable=yes,scrollbars=no");
        }

        function chkMultigroup(){
            var n = document.getElementById('grp').value;
            //alert(n);
            
            if(n){
                        document.getElementById('ct_cli_his_1').style.display='';
                        document.getElementById('service_group_1').style.display='none';

                        if($('dataEdit').value=='1'){
                            document.getElementById('ct_cli_his_0').style.display='';
                        }else{
                            document.getElementById('ct_cli_his_0').style.display='none';
                        }

                    }else{
                        document.getElementById('service_group_1').style.display='';
                        document.getElementById('ct_cli_his_0').style.display='none';
                        document.getElementById('ct_cli_his_1').style.display='none'; 
            }

        }


        function preset(){
            //alert('HOY!');
                var d = document.clinical_history;
                var mp = <?php echo $manpay; ?>;

                //alert(encounter_nr);


                if(d.had_surgery.checked==true){
                     document.getElementById('proc_date').style.display = '';
                     document.getElementById('proc').style.display = '';
                }else{
                    document.getElementById('proc_date').style.display = 'none';
                    document.getElementById('proc').style.display = 'none';
                }
                chkMultigroup();
                viewMedicoLegal();
                viewUltrasoundDetails();
                viewXrayDetails();
                viewBloodChemDetails();
                viewBiopsyDetails();
                viewCTDetails();
                selectDoctor();
                allowManualPayment(mp); 

              //added by Francis L.G 02-07-2013  
              window.parent.ctmributtons();

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
                 document.getElementById('selAMPM').value = "A.M.";
        }else    if((hour > 12)&&(hour < 24)){
                 hour -= 12;
                 document.getElementById('selAMPM').value = "P.M.";
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

function isNumber(amt) {
    var num = amt.value; 
      if(isNaN(num)||num<0){
        alert("Invalid amount.");
        amt.value = "";
        amt.focus();
        return;
      }
}

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

    //alert(rowNum);
    //var amt = $('amount').value;
    // if (/^[A-Z0-9A-Z0-9]+$/.test(amt)) {
    // // Validation failed
    // alert(amt);
    // }

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
    //window.parent.ctmributtons();
    //closeWindow();
    //alert("awwwww");
}
        //-------------------
                                //<body onLoad="preset();"
</script>
</head>
<body onLoad="preset();">
<!-- <table id="service_group_0" width="300" height="50" border="0" align="center" cellpadding="0" cellspacing="0" bordercolor="#BAD0FC" class="style2">
    <tr style="font-size:15" align="center" ><td>&nbsp;</td><td>CLINICAL HISTORY :</td></tr>
</table> -->
<form id="clinical_history" name="clinical_history" method="post" action="" onSubmit="return chkForm()">
<table id="service_group_1" width="300" height="50" border="0" align="center" cellpadding="0" cellspacing="0" bordercolor="#BAD0FC" class="style2">
    <tr><td>&nbsp;</td></tr>
    <tr style="font-size:15"><td>&nbsp;</td><td>CLINICAL HISTORY :</td></tr>
    <tr><td>&nbsp;</td></tr>
        <?php
            for($i=0;$i<count($radGrpCT);$i++){
                echo "<tr>";
                echo "<td valign='top' align='right'>";
                echo "<input type='radio' name='srvGrp' id='srvGrp' value='".$radGrpCT[$i]."'>";
                echo "</td>";
                echo "<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".$radGrpCT[$i];
                echo "<ul>";
                for($j=0;$j<count($radSrv);$j++){
                    $tmp = $radSrv[$j];
                    $radSrvGrpInfo = $radio_obj->getRadioServiceGroupInfo($tmp);
                    if($radSrvGrpInfo['name']==$radGrpCT[$i]){
                        echo "<li>".$tmp."</li>";
                    }
                }
                echo "</ul>";
                echo "</td>";
                echo "</tr>";
            }
            echo '<tr><td>&nbsp;</td><td>&nbsp;&nbsp;&nbsp;';
            echo '<input type="submit" name="Submit" value="submit">';
            echo '<input type="button" name="Cancel" value="cancel"  onclick="closeWindow();">';
            echo '</td></tr>';
        ?>
    <!-- </form> -->
</table>
<table id="ct_cli_his_0" width="520" height="80" border="0" align="center" cellpadding="0" cellspacing="0" bordercolor="#BAD0FC" class="style2" <?php if($allowDataEdit=='0')echo 'style="display:none"';?>>
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
                                                        echo $pidInfo['age'].' years old';

                                                ?>  <? #echo stripslashes(strtoupper($pidInfo['name_last']));?>
                                                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
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
        <?php


                        $patientEncInfo = $per_obj->getPersonInfo($pid);
                        $consulting_dr = $pers_obj->getPersonellInfo($patientEncInfo['consulting_dr_nr']);

                        $consulting_dr_middleInitial = "";
                        if (trim($consulting_dr['name_middle'])!=""){
                                $thisMI=split(" ",$consulting_dr['name_middle']);
                                foreach($thisMI as $value){
                                        if (!trim($value)=="")
                                                $consulting_dr_middleInitial .= $value[0];
                                }
                                if (trim($consulting_dr_middleInitial)!="")
                                        $consulting_dr_middleInitial = " ".$consulting_dr_middleInitial.".";
                        }

                        $attending_dr = $pers_obj->getPersonellInfo($patientEncInfo['current_att_dr_nr']);

                        $attending_dr_middleInitial = "";
                        if (trim($attending_dr['name_middle'])!=""){
                                $thisMI=split(" ",$attending_dr['name_middle']);

                                foreach($thisMI as $value){
                                        if (!trim($value)=="")
                                                $attending_dr_middleInitial .= $value[0];
                                }
                                        if (trim($attending_dr_middleInitial)!=""){
                                                $attending_dr_middleInitial = " ".$attending_dr_middleInitial.".";
                                        }
                        }

                        $consulting_dr_name = "Dr. ".$consulting_dr['name_first']." ".$consulting_dr['name_2']." ".$consulting_dr_middleInitial." ".$consulting_dr['name_last'];
                        $attending_dr_name = "Dr. ".$attending_dr['name_first']." ".$attending_dr['name_2']." ".$attending_dr_middleInitial." ".$attending_dr['name_last'];



        ?>
        <tr id="space5">
                <td>&nbsp;</td>
        </tr>                   
        <tr>
            <td valign="top">
                <font style="color:#FF0000">*</font> CONSULTING DOCTOR
            </td>                      
        </tr>
        <tr>
            <td>&nbsp;</td>
        </tr>
        <tr>
            <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Select a Doctor : </td>
        </tr>
        <tr>
            <td valign="top">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <select name="doctor_in" id="doctor_in" onchange="selectDoctor()">
                    <?php echo $sTemp; ?>
                </select>
            </td>

        </tr>
        <tr id="dr_other1">
            <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Doctor's Name and Licence Number: </td>
        </tr>
        <tr id="dr_other2">
            <td align="left">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <input type="text" style="width: 450px;" name="dr_other" id="dr_other" value="<?php if($_POST['dr_other']&&!$_POST['dr_in']) echo $_POST['dr_other']; else if(($cthistoryInfo['dr_name'])&&(!$cthistoryInfo['doctor_nr'])){echo $cthistoryInfo['dr_name'];} else echo ""; ?>">
            </td>
        </tr>
        <tr>
            <td>&nbsp;</td>
        </tr>

        <tr>
            <td>
                Medico-Legal :&nbsp;&nbsp;
                <input type="radio" name="medico_legal" id="medico_legal_yes" value="1" onclick="viewMedicoLegal();" <?php echo $medLegalYes; ?>>&nbsp;&nbsp;Yes&nbsp;&nbsp;     
                <input type="radio" name="medico_legal" id="medico_legal_no" value="0" onclick="viewMedicoLegal();" <?php echo $medLegalNo; ?>>&nbsp;&nbsp;No&nbsp;&nbsp;
            </td>
        </tr>

        <tr>
            <td>&nbsp;</td>                                                                  
        </tr>
        
        <tr id="medico_legal1">
            <td>
                &nbsp;&nbsp;<font style="color:#FF0000">*</font>Nature of Injury (NOI)&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; : &nbsp;&nbsp;&nbsp;
                <input type="text" name="noi" id="noi" size="50" value="<?php echo $noiVal; ?>">
            </td>
        </tr>

        <tr id="space1">
            <td>&nbsp;</td>
        </tr>
        
        <tr id="medico_legal2">
            <td>
                &nbsp;&nbsp;<font style="color:#FF0000">*</font>Place of Incident (POI) &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; : &nbsp;&nbsp;&nbsp;
                <input name="poi" id="poi" type="text" size="50" value="<?=ucwords(strtolower(trim($poiVal)))?>">
            </td>                                                       
        </tr>
        <tr id="space2">
                <td>&nbsp;</td>
        </tr>

        <tr id="medico_legal3">
            <?php
            #echo "wait lng po = ".$pidInfo['TOI'];
                    $meridian = date("A",strtotime($toiVal));
                    #echo "meridian = ".$meridian;
                    if ($meridian=='PM'){
                        $selected1 = "";
                        $selected2 = "selected";
                    }else{
                        $selected1 = "selected";
                        $selected2 = "";
                    }

                    #if ($pidInfo['TOI']=='00:00:00'){
                    if (($toiVal=='00:00:00') || (empty($toiVal))){
                        $TOI_val = "";
                    }else{
                        if (strstr($toiVal,'24')){
                            $TOI_val = "12:".substr($toiVal,3,2);
                            $selected1 = "selected";
                            $selected2 = "";
                        }else
                            $TOI_val = date("h:i",strtotime($toiVal));
                    }
            ?>
            <td class="">
                &nbsp;&nbsp;<font style="color:#FF0000">*</font>Time of Incident (TOI) &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; : &nbsp;&nbsp;&nbsp;
                <input type="text" id="toi" name="toi" size="4" maxlength="5" value="<?php echo $TOI_val; ?>" onChange="setFormatTime(this,'selAMPM')" />
                <select id="selAMPM" name="selAMPM">                             
                    <option value="AM" <?php echo $selected1; ?>>A.M.</option>
                    <option value="PM" <?php echo $selected2; ?>>P.M.</option>
                </select>&nbsp;<font size=1>[hh:mm]</font>
                </td>
                
        </tr>
        <tr id="space3">
            <td>&nbsp;</td>
        </tr>
        <tr id="medico_legal4">
                 <?php
                    $phpfd=$date_format;
                    $phpfd=str_replace("dd", "%d", strtolower($phpfd));
                    $phpfd=str_replace("mm", "%m", strtolower($phpfd));
                    $phpfd=str_replace("yyyy","%Y", strtolower($phpfd));

                    if (($doiVal!='0000-00-00 00:00:00') && ($doiVal!=""))
                        $DOI_val = @formatDate2Local($doiVal,$date_format);
                    else
                        $DOI_val='';
                   
                    if(($_POST['medico_legal']=='1')&&($_POST['doi'])) $DOI_val = $_POST['doi'];
                                                                                      
                    $sDateJS= '<input name="doi" type="text" size="15" maxlength=10 value="'.$DOI_val.'"'.
                                'onFocus="this.select();"
                                id = "doi"
                                onBlur="IsValidDate(this,\''.$date_format.'\'); "
                                onKeyUp="setDate(this,\''.$date_format.'\',\''.$lang.'\')">
                                <img '.createComIcon($root_path,'show-calendar.gif','0','absmiddle').' id="doi_trigger" style="cursor:pointer" >
                                <font size=1>[';
                                ob_start();
                ?>
                <script type="text/javascript">
                    Calendar.setup ({
                            inputField : "doi", ifFormat : "<?php echo $phpfd?>", showsTime : false, button : "doi_trigger", singleClick : true, step : 1
                    });
                </script>
                <?php
                        $calendarSetup = ob_get_contents();
                        ob_end_clean();

                    $sDateJS .= $calendarSetup;

                    $dfbuffer="LD_".strtr($date_format,".-/","phs");
                    $sDateJS = $sDateJS.$$dfbuffer.']';               
                ?>
            <td>
                &nbsp;&nbsp;<font style="color:#FF0000">*</font>Date of Incident (DOI)&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; : &nbsp;&nbsp;&nbsp;
                <?php echo$sDateJS; ?>                                   
            </td>
        </tr>
        <tr id="space4">
                <td>&nbsp;</td>
        </tr>
        <tr>
                <td valign="top" bgcolor="#F8F9FA">
                        <font style="color:#FF0000">*</font> Clinical Impression :
                </td>
        </tr>
        <tr>
                        <td align="right">
                                <textarea cols="43" rows="5" name="cln_imp" id="cln_imp"><?php if($_POST['cln_imp'])echo $_POST['cln_imp']; else echo $cthistoryInfo['cln_imp']; ?></textarea>
                        </td>
        </tr>
        <tr>
                <td>&nbsp;</td>
        </tr>
        <tr>
                <td valign="top" bgcolor="#F8F9FA">
                        <font style="color:#FF0000">*</font> Chief Complaint :
                </td>
        </tr>
        <tr>
                        <td align="right">
                                <textarea cols="43" rows="5" name="chf_cmp" id="chf_cmp" wrap="physical"><?php if($_POST['chf_cmp'])echo $_POST['chf_cmp']; else echo $cthistoryInfo['chf_cmp']; ?></textarea>
                           </td>
        </tr>
        <tr>
                <td>&nbsp;</td>
        </tr>
        <tr>
                <td valign="top" bgcolor="#F8F9FA">
                        <font style="color:#FF0000">*</font> S: (Subjective complaints)
                </td>
        </tr>
        <tr>
                        <td align="right">
                                <textarea cols="43" rows="5" name="subj_comp" id="subj_comp" wrap="physical"><?php if($_POST['subj_comp'])echo $_POST['subj_comp']; else echo $cthistoryInfo['subj_comp']; ?></textarea>
                        </td>
        </tr>
        <tr>
                <td>&nbsp;</td>
        </tr>

        <tr>
                <td valign="top" bgcolor="#F8F9FA">
                        <font style="color:#FF0000">*</font> O: (pertinent PE findings)
                </td>
        </tr>
        <tr>
                        <td align="right">
                                <textarea cols="43" rows="5" name="obj_comp" id="obj_comp" wrap="physical"><?php if($_POST['obj_comp'])echo $_POST['obj_comp']; else echo $cthistoryInfo['obj_comp']; ?></textarea>
                        </td>
        </tr>
        <tr>
                <td>&nbsp;</td>
        </tr>

        <tr>
            <td valign="top" bgcolor="#F8F9FA">
                <font style="color:#FF0000">*</font> A: Assessment
            </td>
        </tr>
        <tr>
            <td align="right">
                <textarea cols="43" rows="5" name="assessment" id="assessment" wrap="physical"><?php if($_POST['assessment'])echo $_POST['assessment']; else echo $cthistoryInfo['assessment']; ?></textarea>
            </td>
        </tr>

        <tr>
            <td>
                <?php
                    if($cthistoryInfo){
                        if($cthistoryInfo['had_surgery']!="0")
                            $checked = "checked";
                    }else{
                        $checked = "";
                    }
                ?>
                <!--<input type="checkbox" name="had_surgery" id="had_surgery" value="1" onclick="showSurgicalDetails()"> -->
                <input type="checkbox" name="had_surgery" id="had_surgery" value="1" onclick="showSurgicalDetails();" <?php if($_POST['had_surgery'])echo "checked"; else echo $checked; //echo "<script language='javascript'>document.getElementById('proc').style.show='';<script>"?>>
                &nbsp;&nbsp;Surgical Procedure Done
            </td>
        </tr>
        <tr id="proc_date">
            <td>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <!--<input type="text" name="from_date" id="from_date" value="">
            <img src="images/show-calendar.gif" id="to_date_trigger" align="absmiddle" style="cursor:pointer">
            <!--<input type="button" src="images/show-cale">-->

        <?php
                            $phpfd=$date_format;
                            $phpfd=str_replace("dd", "%d", strtolower($phpfd));
                            $phpfd=str_replace("mm", "%m", strtolower($phpfd));
                            $phpfd=str_replace("yyyy","%Y", strtolower($phpfd));
                            /*
                            if (($medCertInfo['consultation_date']!='0000-00-00') && ($medCertInfo['consultation_date']!=""))
                                $consultation_date= @formatDate2Local($medCertInfo['consultation_date'],$date_format);
                            else
                                $consultation_date = @formatDate2Local($pidInfo['encounter_date'],$date_format);  */

                            if (($cthistoryInfo['surgery_date']!='0000-00-00') && ($cthistoryInfo['surgery_date']!="") && ($checked == "checked"))
                                $surgery_date= @formatDate2Local($cthistoryInfo['surgery_date'],$date_format);
                            else
                                $surgery_date = null;
                                
                                if(($_POST['had_surgery'])&&($_POST['surgery_date'])) $surgery_date = $_POST['surgery_date'];
                                 
                                $sDateJS= '<input name="surgery_date" type="text" size="15" maxlength=10 value="'.$surgery_date.'"'.
                                'onFocus="this.select();"
                                id = "surgery_date"
                                onBlur="IsValidDate(this,\''.$date_format.'\'); "
                                onKeyUp="setDate(this,\''.$date_format.'\',\''.$lang.'\')">
                                <img '.createComIcon($root_path,'show-calendar.gif','0','absmiddle').' id="surgery_date_trigger" style="cursor:pointer" >
                                <font size=1>[';
                                                      
                                ob_start();
        ?>
                       <script type="text/javascript">
                                    Calendar.setup ({
                                        inputField : "surgery_date", ifFormat : "<?php echo $phpfd?>", showsTime : false, button : "surgery_date_trigger", singleClick : true, step : 1
                                    });
                       </script>
                        <?php
                            $calendarSetup = ob_get_contents();
                            ob_end_clean();

                            $sDateJS .= $calendarSetup;
                            /**/
                            $dfbuffer="LD_".strtr($date_format,".-/","phs");
                            $sDateJS = $sDateJS.$$dfbuffer.']';
                            //ob_start();
                        ?>
                        <font style="color:#FF0000">*</font> Date &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: &nbsp;
                                                <?= $sDateJS ?>
                                            </span>

            </td>
        </tr>
        <tr id="proc">
            <td>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<font style="color:#FF0000">*</font> Procedure&nbsp;&nbsp;: &nbsp;
                <textarea id="surgery_proc" name="surgery_proc"><?php if(($_POST['had_surgery'])&&($_POST['surgery_proc'])) echo $_POST['surgery_proc'];else echo $cthistoryInfo['surgery_proc'] ?></textarea>
            </td>
        </tr>

        <tr>
                <td>&nbsp;</td>
        </tr>
    <!--
        <tr>
            <td colspan="1">
                <input type="checkbox" name="laboratory" value="1" onclick="showLabDetails()">
                &nbsp;&nbsp;Laboratory Work-Up Done
            </td>
            <td>Date
            </td>
        </tr>   -->

        <tr>
                <td colspan="*" height="23" bgcolor="#FFFFFF" >
                    
                        <?php
                            
                            $cnt = 0;
                            
                            if($cthistoryInfo){
                                if($cthistoryInfo['has_blood_chem']!="0"){
                                    $checked_has_blood_chem = "checked";
                                    $bldChmRes = $cthistoryInfo['bld_chm_res'];
                                    $bldChmRem = $cthistoryInfo['bld_chm_rem'];
                                    $cnt++;
                                }
                                if($cthistoryInfo['has_xray']!="0"){
                                    $checked_has_xray = "checked";
                                    $xrayRes = $cthistoryInfo['xray_res'];
                                    $xrayRem = $cthistoryInfo['xray_rem'];
                                    $cnt++;
                                }
                                if($cthistoryInfo['has_ultrasound']!="0"){
                                    $checked_has_ultrasound = "checked";
                                    $ultrasoundRes = $cthistoryInfo['ultrasound_res'];
                                    $ultrasoundRem = $cthistoryInfo['ultrasound_rem'];
                                    $cnt++;
                                }
                                if($cthistoryInfo['has_ct_mri']!="0"){
                                    $checked_has_ct_mri = "checked";
                                    $ctMriRes = $cthistoryInfo['ct_mri_res'];
                                    $ctMriRem = $cthistoryInfo['ct_mri_rem'];
                                    $cnt++;
                                }
                                if($cthistoryInfo['has_biopsy']!="0"){
                                    $checked_has_biopsy = "checked";
                                    $biopsyRes = $cthistoryInfo['biopsy_res'];
                                    $biopsyRem = $cthistoryInfo['biopsy_rem'];
                                    $cnt++;
                                }
                                
                            }
                            else{
                                $checked_has_lab = "";
                                $checked_has_blood_chem = "";
                                $checked_has_xray = "";
                                $checked_has_ultrasound = "";
                                $checked_has_ct_mri = "";
                                $checked_has_biopsy = "";
                            }
                            
                            
                            if($cnt>0){
                                    $checked_has_lab = "checked";
                            }
                            else{
                                    $checked_has_lab = "";
                                    $checked_has_blood_chem = "";
                                    $checked_has_xray = "";
                                    $checked_has_ultrasound = "";
                                    $checked_has_ct_mri = "";
                                    $checked_has_biopsy = "";
                                }
                            
                            if($_POST['has_blood_chem']){
                                $checked_has_blood_chem = "checked";
                                $checked_has_lab = "checked";
                                if($_POST['bld_chm_res']) $bldChmRes = $_POST['bld_chm_res'];
                                if($_POST['bld_chm_rem']) $bldChmRem = $_POST['bld_chm_rem'];    
                            }
                                
                            if($_POST['has_xray']){
                                $checked_has_xray = "checked";
                                $checked_has_lab = "checked";
                                if($_POST['xray_res']) $xrayRes = $_POST['xray_res'];
                                if($_POST['xray_rem']) $xrayRem = $_POST['xray_rem'];    
                            }
                                
                            if($_POST['has_ultrasound']){
                                $checked_has_ultrasound = "checked";
                                $checked_has_lab = "checked";
                                if($_POST['ultrasound_res']) $ultrasoundRes = $_POST['ultrasound_res'];
                                if($_POST['ultrasound_rem']) $ultrasoundRem = $_POST['ultrasound_rem'];    
                            }
                                
                            if($_POST['has_ct_mri']){
                                $checked_has_ct_mri = "checked";
                                $checked_has_lab = "checked";
                                if($_POST['ct_mri_res']) $ctMriRes = $_POST['ct_mri_res'];
                                if($_POST['ct_mri_rem']) $ctMriRem = $_POST['ct_mri_rem'];    
                            }
                              
                            if($_POST['has_biopsy']){
                                $checked_has_biopsy = "checked";
                                $checked_has_lab = "checked";
                                if($_POST['biopsy_res']) $biopsyRes = $_POST['biopsy_res'];
                                if($_POST['biopsy_rem']) $biopsyRem = $_POST['biopsy_rem'];    
                            }
                               
                            
                        ?>
                        <table width="100%" border="0" bgcolor="#F8F9FA"class="style2">
                                <tr>
                                        <td>
                                        <input type="checkbox" name="laboratory" id="laboratory" value="1" onclick="" <?php echo $checked_has_lab; ?>>
                                            Laboratory Work-Up Done 
                                        </td>
                                </tr>
                                <tr>
                                        <td>&nbsp;</td>
                                </tr>
                                <tr>
                                    <td id="blood_chem">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                        <input type="checkbox" name="has_blood_chem" id="has_blood_chem" value="1" onclick="viewBloodChemDetails();" <?php echo $checked_has_blood_chem; ?>>Blood Chemistry
                                    </td>
                                    
                                        <?php
                                            $phpfd=$date_format;
                                            $phpfd=str_replace("dd", "%d", strtolower($phpfd));
                                            $phpfd=str_replace("mm", "%m", strtolower($phpfd));
                                            $phpfd=str_replace("yyyy","%Y", strtolower($phpfd));
                                            
                                            if (($cthistoryInfo['date_blood_chem']!='0000-00-00')&&($cthistoryInfo['date_blood_chem']!="")&&($checked_has_blood_chem!=""))
                                                $date_blood_chem= @formatDate2Local($cthistoryInfo['date_blood_chem'],$date_format);
                                            else
                                                $date_blood_chem = '';
                                                
                                            if($_POST['has_blood_chem']&&$_POST['date_blood_chem']) $date_blood_chem = $_POST['date_blood_chem'];
                                                
                                            $dateBloodChemJS= '<input name="date_blood_chem" type="text" size="15" maxlength=10 value="'.$date_blood_chem.'"'.
                                                                      'onFocus="this.select();"id = "date_blood_chem"
                                                                      onBlur="IsValidDate(this,\''.$date_format.'\'); "
                                                                      onKeyUp="setDate(this,\''.$date_format.'\',\''.$lang.'\')">
                                                                      <img '.createComIcon($root_path,'show-calendar.gif','0','absmiddle').' id="date_blood_chem_trigger" 
                                                                      style="cursor:pointer" >
                                                                      <font size=1>[';
                                            ob_start();                                                           
                                        ?>
                                        
                                        <script type="text/javascript">
                                            Calendar.setup ({
                                                inputField : "date_blood_chem", ifFormat : "<?php echo $phpfd?>", showsTime : false, button : "date_blood_chem_trigger", singleClick : true, step : 1
                                            });
                                       </script>
                                        <?php
                                            $calendarSetup = ob_get_contents();
                                            ob_end_clean();

                                            $dateBloodChemJS .= $calendarSetup;
                                         
                                            $dfbuffer="LD_".strtr($date_format,".-/","phs");
                                            $dateBloodChemJS = $dateBloodChemJS.$$dfbuffer.']';
                                        ?>                                    
                                    
                                </tr>
                                <tr id="blood_chem1">
                                    <td align="left">
                                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                        
                                        <font style="color:#FF0000">*</font>Date&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: &nbsp;                                       
                                        <?php echo $dateBloodChemJS; ?>
                                    </td>    
                                </tr>
                                <tr id="blood_chem2_a">
                                    <td>
                                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                        
                                        <font style="color:#FF0000">*</font>Result&nbsp;&nbsp;: &nbsp;
                                        
                                    </td>
                                </tr>
                                <tr id="blood_chem2_b">
                                    <td>
                                        
                                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                        <textarea id="bld_chm_res" name="bld_chm_res" cols="45"><?php echo $bldChmRes; ?></textarea>
                                    </td>
                                </tr>
                                <tr id="blood_chem3">
                                    <td>
                                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                        
                                        <font style="color:#FF0000">*</font>Comments/Remarks&nbsp;&nbsp;: &nbsp;
                                    </td>
                                </tr>
                                <tr id="blood_chem4">
                                    <td>
                                        
                                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                        <textarea id="bld_chm_rem" name="bld_chm_rem" cols="45"><?php echo $bldChmRem; ?></textarea>
                                    </td>
                                </tr>
                                <tr>
                                    <td id="xray">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                            <input type="checkbox" name="has_xray" id="has_xray" value="1" onclick="viewXrayDetails();" <?php echo $checked_has_xray; ?>>X-ray <br>
                                    </td>
                                    
                                        <?php
                                            $phpfd=$date_format;
                                            $phpfd=str_replace("dd", "%d", strtolower($phpfd));
                                            $phpfd=str_replace("mm", "%m", strtolower($phpfd));
                                            $phpfd=str_replace("yyyy","%Y", strtolower($phpfd));
                                        
                                            if (($cthistoryInfo['date_xray']!='0000-00-00')&&($cthistoryInfo['date_xray']!="")&&($checked_has_xray!=""))
                                                $date_xray= @formatDate2Local($cthistoryInfo['date_xray'],$date_format);
                                            else
                                                $date_xray = '';
                                                
                                            if($_POST['has_xray']&&$_POST['date_xray']) $date_xray = $_POST['date_xray'];
                                                
                                            $dateXrayJS= '<input name="date_xray" type="text" size="15" maxlength=10 value="'.$date_xray.'"'.
                                                                      'onFocus="this.select();"id = "date_xray"
                                                                      onBlur="IsValidDate(this,\''.$date_format.'\'); "
                                                                      onKeyUp="setDate(this,\''.$date_format.'\',\''.$lang.'\')">
                                                                      <img '.createComIcon($root_path,'show-calendar.gif','0','absmiddle').' id="date_xray_trigger" 
                                                                      style="cursor:pointer" >
                                                                      <font size=1>[';
                                            ob_start();                                                           
                                        ?>
                                        
                                        <script type="text/javascript">
                                            Calendar.setup ({
                                                inputField : "date_xray", ifFormat : "<?php echo $phpfd?>", showsTime : false, button : "date_xray_trigger", singleClick : true, step : 1
                                            });
                                       </script>
                                        <?php
                                            $calendarSetup = ob_get_contents();
                                            ob_end_clean();

                                            $dateXrayJS .= $calendarSetup;
                                            /**/
                                            $dfbuffer="LD_".strtr($date_format,".-/","phs");
                                            $dateXrayJS = $dateXrayJS.$$dfbuffer.']';
                                        ?>
                                </tr>
                                <tr  id="xray1">
                                    <td align="left">
                                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                        
                                        <font style="color:#FF0000">*</font>Date&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: &nbsp;                                       
                                        <?php echo $dateXrayJS; ?>
                                    </td>    
                                </tr>
                                <tr id="xray2_a">
                                    <td>
                                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                        
                                        <font style="color:#FF0000">*</font>Result&nbsp;&nbsp;: &nbsp;
                                    </td>
                                </tr>
                                <tr id="xray2_b">
                                    <td>
                                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                        <textarea id="xray_res" name="xray_res" cols="45"><?php echo $xrayRes; ?></textarea>
                                    </td>
                                </tr>
                                <tr id="xray3">
                                    <td>
                                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                        
                                        <font style="color:#FF0000">*</font>Comments/Remarks&nbsp;&nbsp;: &nbsp;
                                    </td>
                                </tr>
                                <tr id="xray4">
                                    <td>
                                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                        <textarea id="xray_rem" name="xray_rem" cols="45"><?php echo $xrayRem; ?></textarea>
                                    </td>
                                </tr>
                                <tr>
                                    <td id="ultrasound">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                        <input type="checkbox" name="has_ultrasound" id="has_ultrasound" value="1" onclick="viewUltrasoundDetails();" <?php echo $checked_has_ultrasound; ?>>Ultrasound
                                    </td>
                                    
                                        <?php
                                            $phpfd=$date_format;
                                            $phpfd=str_replace("dd", "%d", strtolower($phpfd));
                                            $phpfd=str_replace("mm", "%m", strtolower($phpfd));
                                            $phpfd=str_replace("yyyy","%Y", strtolower($phpfd));
                                        
                                            if (($cthistoryInfo['date_ultrasound']!='0000-00-00')&&($cthistoryInfo['date_ultrasound']!="")&&($checked_has_ultrasound!=""))
                                                $date_ultrasound= @formatDate2Local($cthistoryInfo['date_ultrasound'],$date_format);
                                            else
                                                $date_ultrasound = '';
                                                
                                            if($_POST['has_ultrasound']&&$_POST['date_ultrasound']) $date_ultrasound = $_POST['date_ultrasound'];
                                                
                                            $dateUltrasoundJS= '<input name="date_ultrasound" type="text" size="15" maxlength=10 value="'.$date_ultrasound.'"'.
                                                                      'onFocus="this.select();"id = "date_ultrasound"
                                                                      onBlur="IsValidDate(this,\''.$date_format.'\'); "
                                                                      onKeyUp="setDate(this,\''.$date_format.'\',\''.$lang.'\')">
                                                                      <img '.createComIcon($root_path,'show-calendar.gif','0','absmiddle').' id="date_ultrasound_trigger" 
                                                                      style="cursor:pointer" >
                                                                      <font size=1>[';
                                            ob_start();                                                           
                                        ?>
                                        
                                        <script type="text/javascript">
                                            Calendar.setup ({
                                                inputField : "date_ultrasound", ifFormat : "<?php echo $phpfd; ?>", showsTime : false, button : "date_ultrasound_trigger", singleClick : true, step : 1
                                            });
                                       </script>
                                        <?php
                                            $calendarSetup = ob_get_contents();
                                            ob_end_clean();

                                            $dateUltrasoundJS .= $calendarSetup;
                                            /**/
                                            $dfbuffer="LD_".strtr($date_format,".-/","phs");
                                            $dateUltrasoundJS = $dateUltrasoundJS.$$dfbuffer.']';
                                        ?>
                                </tr>
                                <tr  id="ultrasound1">
                                    <td align="left">
                                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                        
                                        <font style="color:#FF0000">*</font>Date&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: &nbsp;                                       
                                        <?php echo $dateUltrasoundJS; ?>
                                    </td>    
                                </tr>
                                <tr id="ultrasound2_a">
                                    <td>
                                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                        
                                        <font style="color:#FF0000">*</font>Result&nbsp;&nbsp;: &nbsp;
                                    </td>
                                </tr>
                                <tr id="ultrasound2_b">
                                    <td>
                                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                        <textarea id="ultrasound_res" name="ultrasound_res" cols="45"><?php echo $ultrasoundRes; ?></textarea>
                                    </td>
                                </tr>
                                <tr id="ultrasound3">
                                    <td>
                                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                        
                                        <font style="color:#FF0000">*</font>Comments/Remarks&nbsp;&nbsp;: &nbsp;
                                    </td>
                                </tr>
                                <tr id="ultrasound4">
                                    <td>
                                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                        <textarea id="ultrasound_rem" name="ultrasound_rem" cols="45"><?php echo $ultrasoundRem; ?></textarea>
                                    </td>
                                </tr>
                                <tr>
                                    <td id="ct_mri">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                        <input type="checkbox" name="has_ct_mri" id="has_ct_mri" value="1" onclick="viewCTDetails();" <?php echo $checked_has_ct_mri; ?>>CT/MRI
                                    </td>
                                    
                                        <?php
                                            $phpfd=$date_format;
                                            $phpfd=str_replace("dd", "%d", strtolower($phpfd));
                                            $phpfd=str_replace("mm", "%m", strtolower($phpfd));
                                            $phpfd=str_replace("yyyy","%Y", strtolower($phpfd));
                                            
                                            if (($cthistoryInfo['date_ct_mri']!='0000-00-00')&&($cthistoryInfo['date_ct_mri']!="")&&($checked_has_ct_mri!=""))
                                                $date_ct_mri= @formatDate2Local($cthistoryInfo['date_ct_mri'],$date_format);
                                            else
                                                $date_ct_mri = '';
                                                
                                            if($_POST['has_ct_mri']&&$_POST['date_ct_mri']) $date_ct_mri = $_POST['date_ct_mri'];
                                                
                                            $dateCtMriJS= '<input name="date_ct_mri" type="text" size="15" maxlength=10 value="'.$date_ct_mri.'"'.
                                                                      'onFocus="this.select();"id = "date_ct_mri"
                                                                      onBlur="IsValidDate(this,\''.$date_format.'\'); "
                                                                      onKeyUp="setDate(this,\''.$date_format.'\',\''.$lang.'\')">
                                                                      <img '.createComIcon($root_path,'show-calendar.gif','0','absmiddle').' id="date_ct_mri_trigger" 
                                                                      style="cursor:pointer" >
                                                                      <font size=1>[';
                                            ob_start();                                                           
                                        ?>
                                        
                                        <script type="text/javascript">
                                            Calendar.setup ({
                                                inputField : "date_ct_mri", ifFormat : "<?php echo $phpfd?>", showsTime : false, button : "date_ct_mri_trigger", singleClick : true, step : 1
                                            });
                                       </script>
                                        <?php
                                            $calendarSetup = ob_get_contents();
                                            ob_end_clean();

                                            $dateCtMriJS .= $calendarSetup;
                                            /**/
                                            $dfbuffer="LD_".strtr($date_format,".-/","phs");
                                            $dateCtMriJS = $dateCtMriJS.$$dfbuffer.']';
                                        ?>
                                </tr>
                                <tr id="ct_mri1">
                                    <td align="left" id="">
                                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                        
                                        <font style="color:#FF0000">*</font>Date&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: &nbsp;                                       
                                        <?php echo $dateCtMriJS; ?>
                                    </td>    
                                </tr>
                                <tr id="ct_mri2_a">
                                    <td>
                                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                        
                                        <font style="color:#FF0000">*</font>Result&nbsp;&nbsp;: &nbsp;
                                    </td>
                                </tr>
                                <tr id="ct_mri2_b">
                                    <td>
                                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                        <textarea id="ct_mri_res" name="ct_mri_res" cols="45"><?php echo $ctMriRes; ?></textarea>
                                    </td>
                                </tr>
                                <tr id="ct_mri3">
                                    <td>
                                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                        
                                        <font style="color:#FF0000">*</font>Comments/Remarks&nbsp;&nbsp;: &nbsp;
                                    </td>
                                </tr>
                                <tr id="ct_mri4">
                                    <td>
                                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                        <textarea id="ct_mri_rem" name="ct_mri_rem" cols="45"><?php echo $ctMriRem; ?></textarea>
                                    </td>
                                </tr>
                                <tr>
                                    <td id="biopsy">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                        <input type="checkbox" name="has_biopsy" id="has_biopsy" value="1" onclick="viewBiopsyDetails();" <?php echo $checked_has_biopsy; ?>>Biopsy
                                    </td>
                                    
                                        <?php
                                            $phpfd=$date_format;
                                            $phpfd=str_replace("dd", "%d", strtolower($phpfd));
                                            $phpfd=str_replace("mm", "%m", strtolower($phpfd));
                                            $phpfd=str_replace("yyyy","%Y", strtolower($phpfd));
                                        
                                            if (($cthistoryInfo['date_biopsy']!='0000-00-00')&&($cthistoryInfo['date_biopsy']!="")&&($checked_has_biopsy!=""))
                                                $date_biopsy= @formatDate2Local($cthistoryInfo['date_biopsy'],$date_format);
                                            else
                                                $date_biopsy = '';
                                                
                                            if($_POST['has_biopsy']&&$_POST['date_biopsy']) $date_biopsy = $_POST['date_biopsy']; 
                                                
                                            $dateBiopsyJS= '<input name="date_biopsy" type="text" size="15" maxlength=10 value="'.$date_biopsy.'"'.
                                                                      'onFocus="this.select();"id = "date_biopsy"
                                                                      onBlur="IsValidDate(this,\''.$date_format.'\'); "
                                                                      onKeyUp="setDate(this,\''.$date_format.'\',\''.$lang.'\')">
                                                                      <img '.createComIcon($root_path,'show-calendar.gif','0','absmiddle').' id="date_biopsy_trigger" 
                                                                      style="cursor:pointer" >
                                                                      <font size=1>[';
                                            ob_start();                                                           
                                        ?>
                                        
                                        <script type="text/javascript">
                                            Calendar.setup ({
                                                inputField : "date_biopsy", ifFormat : "<?php echo $phpfd?>", showsTime : false, button : "date_biopsy_trigger", singleClick : true, step : 1
                                            });
                                       </script>
                                        <?php
                                            $calendarSetup = ob_get_contents();
                                            ob_end_clean();

                                            $dateBiopsyJS .= $calendarSetup;
                                            /**/
                                            $dfbuffer="LD_".strtr($date_format,".-/","phs");
                                            $dateBiopsyJS = $dateBiopsyJS.$$dfbuffer.']';
                                        ?>
                                </tr>
                                <tr id="biopsy1">
                                    <td align="left" id="">
                                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                        
                                        <font style="color:#FF0000">*</font>Date&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: &nbsp;                                       
                                        <?php echo $dateBiopsyJS; ?>
                                    </td>    
                                </tr>
                                <tr id="biopsy2_a">
                                    <td>
                                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                        
                                        <font style="color:#FF0000">*</font>Result&nbsp;&nbsp;: &nbsp;
                                    </td>
                                </tr>
                                <tr id="biopsy2_b">
                                    <td>
                                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                        <textarea id="biopsy_res" name="biopsy_res" cols="45"><?php echo $biopsyRes; ?></textarea>
                                    </td>
                                </tr>
                                <tr id="biopsy3">
                                    <td>
                                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                        
                                        <font style="color:#FF0000">*</font>Comments/Remarks&nbsp;&nbsp;: &nbsp;
                                    </td>
                                </tr>
                                <tr id="biopsy4">
                                    <td>
                                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                        <textarea id="biopsy_rem" name="biopsy_rem" cols="45"><?php echo $biopsyRem; ?></textarea>
                                    </td>
                                </tr>
                                <!--<tr>
                                    <td>
                                            <input type="checkbox" name="has_blood_chem" id="has_blood_chem" value="1">Blood Chemistry <br>
                                            <input type="checkbox" name="has_xray" id="has_xray" value="1">X-ray <br>
                                            <input type="checkbox" name="has_ultrasound" id="has_ultrasound" value="1">Ultrasound <br>
                                            <input type="checkbox" name="has_ct_mri" id="has_ct_mri" value="1">CT/MRI<br>
                                            <input type="checkbox" name="has_biopsy" id="has_biopsy" value="1">Biopsy<br>

                                    </td>
                            </tr>-->

                        </table>
                </td>
        </tr>

        <tr>
            <td>&nbsp;</td>
        </tr>

        <tr>
            <td>
                <?php
                    if($cthistoryInfo){
                        if($cthistoryInfo['has_conscious']=='1')
                            $checked_has_conscious = "checked";
                        if($cthistoryInfo['did_vomit']=='1')
                            $checked_did_vomit = "checked";
                    }

                ?>

                <input type="checkbox" name="has_conscious" id="has_conscious" value="1" <?php if($_POST['has_conscious'])echo "checked"; else echo $checked_has_conscious; ?>>
                Loss of consciousness<br>
                <input type="checkbox" name="did_vomit" id="did_vomit" value="1" <?php if($_POST['did_vomit'])echo "checked"; else echo $checked_did_vomit; ?>>
                Vomiting<br>
            </td>
        </tr>

        <tr>
            <td>&nbsp;</td>
        </tr>

        <tr>
            <td>GCS &nbsp;&nbsp;
                <input type="text" style="text-align:right" name="gcs" id="gcs" value="<?php if($_POST['gcs']) echo $_POST['gcs']; else echo $cthistoryInfo['gcs'];?>" >
            </td>
        </tr>

        <tr>
            <td>&nbsp;</td>
        </tr>

        <tr>
            <td>RLS &nbsp;&nbsp;
                <input type="text" style="text-align:right" name="rls" id="rls" value="<?php if($_POST['rls']) echo $_POST['rls']; else echo $cthistoryInfo['rls'];?>">
            </td>
        </tr>

        <tr>
            <td>&nbsp;</td>
        </tr>
</table>
<table id="ct_cli_his_1" width="520" height="50" border="0" align="center" cellpadding="0" cellspacing="0" bordercolor="#BAD0FC" class="style2">

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
                            }else if($cthistoryInfo['mp_request']){
                                $srTmp = $cthistoryInfo['mp_request'];
                                $sr = explode(",", $srTmp);
                                $ptTmp = $cthistoryInfo['mp_trans_type'];
                                $pt = explode(",", $ptTmp);
                                $aTmp = $cthistoryInfo['mp_amount'];
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
                        }else if($cthistoryInfo['mp_total']){
                            $totalMP = $cthistoryInfo['mp_total'];    
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

                        if (!$cthistoryInfo || empty($cthistoryInfo)){
                            
                                echo '            <input type="hidden" name="mode" id="mode" value="save">'."\n";
                                echo '            <input type="submit" name="Submit" value="Save">'."\n";

                        }else if(!$allowDataEdit && !$manpay){

                                echo '<input type="hidden" name="mode" id="mode" value="delete">'."\n";
                                echo '<input type="button" name="Print" value="Print" onClick="printCTHistory(\''.$pid.'\',\''.$refno.'\',\''.$grp.'\')">'."\n";
                                echo '<input type="submit" name="Submit" value="Delete" onClick="if(confirm(\'Are you sure you want to delete this Clinical History ?\')){submitMode(\'delete\');}else{submitMode(\'\');}">'."\n";

                        }else{
                                echo '            <input type="hidden" name="mode" id="mode" value="update">'."\n";
                                echo '<input type="button" name="Print" value="Print" onClick="printCTHistory(\''.$pid.'\',\''.$refno.'\',\''.$grp.'\')">'."\n";
                                echo '            <input type="submit" name="Submit" value="Update">'."\n";
                                echo '<input type="submit" name="Submit" value="Delete" onClick="if(confirm(\'Are you sure you want to delete this Clinical History ?\')){submitMode(\'delete\');}else{submitMode(\'\');}">'."\n";
                                
                        }
                        
                        $uuid = $cthistoryInfo['uuid'];
                        
                       // echo '<input type="hidden" name="encounter_nr" id="encounter_nr" value="'.$encounter_nr.'">'."\n";
                        echo '<input type="hidden" name="uuid" id="uuid" value="'.$uuid.'">'."\n";
                        
?>
                        
                        <input type="button" name="Cancel" value="Cancel"  onclick="closeWindow();">
                        <input type="hidden" name="pid" id="pid" value="<?=$pid?>">
                        <input type="hidden" name="refno" id="refno" value="<?php echo $refno;?>">
                        <input type="hidden" name="encounter_nr" id="encounter_nr" value="<?php echo $encounter_nr;?>">
                        <input type="hidden" name="grp" id="grp" value="<?=$grp?>">
                        <input type="hidden" name="dataEdit" id="dataEdit" value="<?=$allowDataEdit?>">
                        <input type="hidden" name="mpType" id="mpType" value="<?=$cthistoryInfo['mp_trans_type']?>">
                        <input type="hidden" name="mpEncoder" id="mpEncoder" value="<?=$cthistoryInfo['encoder']?>">

                        <!--<input type="text" name="refno" id="refno" value="<?=$refno?>"> -->
                </td>
        </tr>
        <tr>
                <td colspan="*" align="center"><?= $errorMsg ?></td>
        </tr>
</table>
        </form>
</body>
</html>
