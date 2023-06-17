<?php
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

include_once($root_path.'include/care_api_classes/class_personell.php');
$pers_obj=new Personell;


if (isset($_GET['encounter_nr']) && $_GET['encounter_nr']){
	$encounter_nr = $_GET['encounter_nr'];
}
if (isset($_GET['cert_nr']) && $_GET['cert_nr']){
    $cert_nr = $_GET['cert_nr'];
}
else{
    $cert_nr = $_POST['cert_nr'];
}
if (isset($_POST['encounter_nr']) && $_POST['encounter_nr']){
	$encounter_nr = $_POST['encounter_nr'];
}
if (isset($_GET['referral_nr']) && $_GET['referral_nr']){
    $referral_nr = $_GET['referral_nr'];
    $HTTP_POST_VARS['referral_nr'] = $referral_nr;
    $referral = true;
}
else
{
    $HTTP_POST_VARS['referral_nr'] = '';
    $referral = false;
}
//$referral_nr = $_GET["referral_nr"];

include_once($root_path.'include/care_api_classes/class_cert_med.php');
$obj_medCert = new MedCertificate($encounter_nr);

#if($_GET['encounter_nr']){
/*
if($encounter_nr){
#	if(!($encInfo = $enc_obj->getEncounterInfo($_GET['encounter_nr']))){
	if(!($encInfo=$enc_obj->getEncounterInfo($encounter_nr))){
		echo '<em class="warn"> Sorry but the page cannot be displayed!</em>';
		exit();
	}
	#extract($encInfo);
}else{
	echo '<em class="warn">Sorry but the page cannot be displayed! <br> Invalid Case Number!</em>';
	exit();
}
*/
#echo "sql = ".$enc_obj->sql;
$errorMsg='';
/*
echo "HTTP_POST_VARS : <br>"; print_r($HTTP_POST_VARS); echo "<br> \n";
echo "medCertInfo : <br>"; print_r($medCertInfo); echo "<br> \n";
echo "encounter_nr = '".$encounter_nr."' <br> \n";
echo "obj_medCert->sql = '".$obj_medCert->sql."' <br> \n";
#exit();
*/
#echo "type = ".$_POST['cert_type'];

#added by VAN 03-27-08
$HTTP_POST_VARS['is_medico_legal']=$_POST['cert_type'];

#added by VAN 06-13-08

#added by VAN 04-28
$HTTP_POST_VARS['is_doc_sig']=$_POST['signatory'];
if ($HTTP_POST_VARS['is_doc_sig']){
	if ($_POST['doctors']!=0)
		$HTTP_POST_VARS['dr_nr'] = $_POST['doctors'];
	else
		$HTTP_POST_VARS['dr_nr'] = $_POST['doctors2'];	
}else
	$HTTP_POST_VARS['dr_nr'] = '';	

#echo "dr = ".$HTTP_POST_VARS['dr_nr'];	

#added by VAN 06-12-08
$HTTP_POST_VARS['DOI'] = date("y-m-d",strtotime($HTTP_POST_VARS['DOI']));

if (trim($HTTP_POST_VARS['TOI'])){
$time = $HTTP_POST_VARS['TOI'].":00 ".$HTTP_POST_VARS['selAMPM'];
$prev_hr = $HTTP_POST_VARS['TOI'];
$prev_mer = $HTTP_POST_VARS['selAMPM'];
#echo "time = ".$time;
$HTTP_POST_VARS['TOI'] = date("H:i:s",strtotime($time));

if ( (strstr($prev_hr,'12'))&&($prev_mer=='AM')){
	$HTTP_POST_VARS['TOI'] = '24:'.date("i",strtotime($HTTP_POST_VARS['TOI'])).":00";
}
}
/*
if (!$HTTP_POST_VARS['cert_type']){
	#$HTTP_POST_VARS['is_medico'] = '0';
	$HTTP_POST_VARS['POI'] = "";
	$HTTP_POST_VARS['TOI'] = "";
	$HTTP_POST_VARS['DOI'] = "";
}	
*/

$HTTP_POST_VARS['consultation_date'] = date("Y-m-d",strtotime($HTTP_POST_VARS['consultation_date']));	

$HTTP_POST_VARS['scheduled_date'] = date("Y-m-d",strtotime($HTTP_POST_VARS['scheduled_date']));

#echo "sh = ".$HTTP_POST_VARS['description'];						
#echo "con = ".$consultation_date;
#echo "<br>c = ".$HTTP_POST_VARS['consultation_date'];                                    
$medico_cases = $enc_obj->getMedicoCases();
$medico_count = $enc_obj->count;
#echo "count = ".$medico_count;

if ($HTTP_POST_VARS['procedure_verbatim']=="")
  $HTTP_POST_VARS['procedure_verbatim'] = " ";
#----------------------------------------------
#edited by raissa 01/24/09 to cater referrals
if (isset($_POST['mode'])){
    
    switch($_POST['mode']) {
        case 'save':
            $HTTP_POST_VARS['history'] = "Create ".date('Y-m-d H:i:s')." ".$HTTP_SESSION_VARS['sess_user_name']." \n";
            $HTTP_POST_VARS['create_id']=$HTTP_SESSION_VARS['sess_user_name'];
            $HTTP_POST_VARS['create_dt']=date('Y-m-d H:i:s');
            
            if ($obj_medCert->saveMedCertificateInfoFromArray($HTTP_POST_VARS)){
                #added by VAN 06-13-08
                $enc_obj->setMedico($HTTP_POST_VARS['cert_type'],$encounter_nr,$referral_nr);
                #$HTTP_POST_VARS['description']                    
                $cases = array();
                if(is_object($medico_cases)){
                    while($result=$medico_cases->FetchRow()) {
                        if ($HTTP_POST_VARS['medico'.$result['code']]){
                            #$cases[] = array($HTTP_POST_VARS['medico'.$result['code']]);
                             if ($HTTP_POST_VARS['medico'.$result['code']]=='OT')
                                $desc =  $HTTP_POST_VARS['description'];
                            else
                                $desc = "none";  
                                         
                            $cases[] = array($HTTP_POST_VARS['medico'.$result['code']],$desc);
                        }
                    }
                }        
                /*                            
                if (!$HTTP_POST_VARS['cert_type']){
                    $enc_obj->deleteMedicoCasesEncounter($encounter_nr,$pid);
                }else{
                */
                    $enc_obj->deleteMedicoCasesEncounter($encounter_nr,$pid,$referral_nr);
                    $enc_obj->addMedicoCasesEncounter($encounter_nr,$pid,$cases,$referral_nr);
                #}
                $enc_obj->updateIncident($encounter_nr,$HTTP_POST_VARS['POI'],$HTTP_POST_VARS['TOI'],$HTTP_POST_VARS['DOI'],"Update from Medical Certificate Form");
                #----------------------
                //$tmp = $obj_medCert->getLatestCertNr($encounter_nr, $referral_nr);
                //$cert_nr = $this->LastInsertPK('cert_nr');
                $cert_nr = $db->Insert_ID();
                //echo "last returned cert_nr = '$cert_nr'";
                //$cert_nr = $tmp["cert_nr"];
                $errorMsg='<font style="color:#FF0000;font-style:italic">'."Saved sucessfully!".'</font>';
            }else{
                $errorMsg='<font style="color:#FF0000">'.$obj_medCert->getErrorMsg().'</font>';            
            }
        break;
        case 'update':
            $HTTP_POST_VARS['history'] = "Update ".date('Y-m-d H:i:s')." ".$HTTP_SESSION_VARS['sess_user_name']." \n";
            $HTTP_POST_VARS['modify_id']=$HTTP_SESSION_VARS['sess_user_name'];
            $HTTP_POST_VARS['modify_dt']=date('Y-m-d H:i:s');
            # echo "hello ";
            if ($obj_medCert->updateMedCertificateInfoFromArray($HTTP_POST_VARS)){
            #echo "upd = ".$obj_medCert->sql;
                #added by VAN 06-13-08
                $enc_obj->setMedico($HTTP_POST_VARS['cert_type'],$encounter_nr,$referral_nr);
                                    
                $cases = array();
                if(is_object($medico_cases)){
                    while($result=$medico_cases->FetchRow()) {
                        if ($HTTP_POST_VARS['medico'.$result['code']]){
                            if ($HTTP_POST_VARS['medico'.$result['code']]=='OT')
                                $desc =  $HTTP_POST_VARS['description'];
                            else
                                $desc = "none";  
                                         
                            $cases[] = array($HTTP_POST_VARS['medico'.$result['code']],$desc);
                        }
                    }
                }        
                /*                            
                if (!$HTTP_POST_VARS['cert_type']){
                    $enc_obj->deleteMedicoCasesEncounter($encounter_nr,$pid);
                }else{
                */
                    $enc_obj->deleteMedicoCasesEncounter($encounter_nr,$pid,$referral_nr);
                    $enc_obj->addMedicoCasesEncounter($encounter_nr,$pid,$cases,$referral_nr);
                #}
                $enc_obj->updateIncident($encounter_nr,$HTTP_POST_VARS['POI'],$HTTP_POST_VARS['TOI'],$HTTP_POST_VARS['DOI'],"Update from Medical Certificate Form");
                #echo $enc_obj->sql;
                #----------------------
                
                $errorMsg='<font style="color:#FF0000;font-style:italic">'."Updated sucessfully!".'</font>';
            }else{
                $errorMsg='<font style="color:#FF0000">'.$obj_medCert->getErrorMsg().'</font>';            
            }
        break;
    }# end of switch statement
}
#echo "sql = ".$obj_medCert->sql;
#echo "errorMsg = '".$errorMsg."' <br> \n";

//$_GET['encounter_nr'] = 2007500006;
#$encounter_nr = $_GET['encounter_nr'];
//$encounter_nr = '2007500006';
#transferred by VAN 06-13-08

    if($encounter_nr){
    #    if(!($encInfo = $enc_obj->getEncounterInfo($_GET['encounter_nr']))){
        if(!($encInfo=$enc_obj->getEncounterInfo($encounter_nr))){
            echo '<em class="warn"> Sorry but the page cannot be displayed!</em>';
            exit();
        }
        else
        {
            if($referral)
            {
                if(!($refInfo=$enc_obj->getReferralInfo($referral_nr))){
                    echo '<em class="warn"> Sorry but the page cannot be displayed!</em>';
                    exit();
                }
            }
        }
    }else{
        echo '<em class="warn">Sorry but the page cannot be displayed! <br> Invalid Case Number!</em>';
        exit();
    }
    $medCertInfo = $obj_medCert->getMedCertRecord($encounter_nr,$referral_nr,$cert_nr);
    $medico_cases = $enc_obj->getMedicoCases();
    #$info = $enc_obj->getEncounterInfo($encounter_nr);
    #echo "sql = ".$obj_medCert->sql;
    #echo "cert = ".$medCertInfo;
    //print_r($encInfo);
      
    #echo "encInfo['encounter_type'] = '".$encInfo['encounter_type']."' <br> \n";
    $listDoctors=array();

    #added by VAN 06-28-08
      if ($encInfo['current_dept_nr'])    
        $dept_nr = $encInfo['current_dept_nr'];
      else    
        $dept_nr = $encInfo['consulting_dept_nr'];
            
      #$doctors = $pers_obj->getDoctorsOfDept($dept_nr);
      #commented by VAN 07-31-08
      #$doctors = $pers_obj->getDoctorsOfDept($dept_nr);
      #echo "sql = ".$pers_obj->sql;
      #if ($dept_nr)        
     #     $doctors = $pers_obj->getDoctorsOfDept($dept_nr);
      #else
          $doctors = $pers_obj->getDoctors(1);    
         #echo "sql = ".$pers_obj->sql;
      $listDoctors[0]="-Select a Doctor-";     
      if (is_object($doctors)){    
        while($drInfo=$doctors->FetchRow()){
        #print_r($drInfo);
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
                
            #$name_doctor = trim($drInfo["name_first"])." ".trim($drInfo["name_2"])." ".$middleInitial.trim($drInfo["name_last"]);
            #$name_doctor = "Dr. ".$name_doctor;
            #if (trim($drInfo["name_middle"]))
            #    $dot  = ".";
                        
            $name_doctor = trim($drInfo["name_last"]).", ".trim($drInfo["name_first"])." ".$middleInitial; #substr(trim($drInfo["name_middle"]),0,1).$dot;
            $name_doctor = ucwords(strtolower($name_doctor)).", MD";
            
            #echo "<br> dr = ".$name_doctor;
            #$listDoctors['doctor_name']=$name_doctor;
            
            $listDoctors[$drInfo["personell_nr"]]=$name_doctor;
            #$listDoctors['doctor_nr']=$drInfo["personell_nr"];
                
            #print_r($listDoctors);
        }    
     }    
        
    #----------------------
    
    if ($result_diagnosis = $objDRG->getDiagnosisCodes($encounter_nr,$encInfo['encounter_type'])){
        #echo "<br>sql 1 = ".$objDRG->sql;
        $result['diagnosis_principal']='';
        $result['diagnosis_others']='';
        $rowsDiagnosis = $result_diagnosis->RecordCount();
        #echo "   code  :   diagnosis <br> \n";
        while($temp=$result_diagnosis->FetchRow()){
            #echo $temp['code']." : ".$temp['diagnosis']." <br> \n";
            #commented by VAN 06-28-08
            #$listDoctors[$temp['diagnosing_clinician']]=$temp['diagnosing_clinician_name'];
            if ($temp['type'])
                $result['diagnosis_principal'].= "&nbsp;&nbsp;&nbsp;&nbsp;".$temp['code']." : ".$temp['diagnosis']." <br> \n";
            else
                $result['diagnosis_others'].= "&nbsp;&nbsp;&nbsp;&nbsp;".$temp['code']." : ".$temp['diagnosis']." <br> \n";
        }
    }
    
    if ($result_therapy = $objDRG->getProcedureCodes($encounter_nr,$encInfo['encounter_type'])){
            #echo "<br>sql 2 = ".$objDRG->sql;
            $result['therapy_principal']='';
            $result['therapy_others']='';
            $rowsTherapy = $result_therapy->RecordCount();
            #echo "   code  :   therapy <br> \n";
            while($temp=$result_therapy->FetchRow()){
                #echo $temp['code']." : ".$temp['therapy']." <br> \n";
                #commented by VAN 06-28-08
                #$listDoctors[$temp['responsible_clinician']]=$temp['responsible_clinician_name'];
                if ($temp['type']){
                    $result['therapy_principal'].= $temp['code']." : ".$temp['therapy']." <br> \n";
                    $result['therapy_principal'].= "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; ".$temp['responsible_dept_name']." - ".$temp['responsible_clinician_name']." (".@formatDate2Local($temp['date'],$date_format,TRUE).") <br> \n";
                }else{
                    $result['therapy_others'].= $temp['code']." : ".$temp['therapy']." <br> \n";
                    $result['therapy_others'].= "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; ".$temp['responsible_dept_name']." - ".$temp['responsible_clinician_name']." (".@formatDate2Local($temp['date'],$date_format,TRUE).") <br> \n";
                }
            }
        }
    
#edited by raissa 01/24/09 to cater referrals
if(!$referral)
{
 
	#added by VAN 04-28-08
#echo "objDRG->sql = '".$objDRG->sql."' <br> \n";
	$patientEncInfo = $enc_obj->getEncounterInfo($encounter_nr);
	#echo "<br>".$enc_obj->sql;
	#echo "size = ".sizeof($listDoctors);
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
		#echo "nr = ".$attending_dr_middleInitial;	
	}
	
	$consulting_dr_name = "Dr. ".$consulting_dr['name_first']." ".$consulting_dr['name_2']." ".$consulting_dr_middleInitial." ".$consulting_dr['name_last'];
	$attending_dr_name = "Dr. ".$attending_dr['name_first']." ".$attending_dr['name_2']." ".$attending_dr_middleInitial." ".$attending_dr['name_last'];
	
	#commented by VAN 06-28-08
	/*
	if (sizeof($listDoctors)==0){
		$listDoctors[$patientEncInfo['consulting_dr_nr']] = 	$consulting_dr_name;
		$listDoctors[$patientEncInfo['current_att_dr_nr']] = 	$attending_dr_name;
	}
	*/		
	#$listDoctors[$temp['responsible_clinician']]=$temp['responsible_clinician_name'];
}
else
{
      
        #added by VAN 04-28-08
    #echo "objDRG->sql = '".$objDRG->sql."' <br> \n";
        $patientEncInfo = $enc_obj->getEncounterInfo($encounter_nr);
        $patientRefInfo = $enc_obj->getReferralInfo($referral_nr);
        //echo "<br>".$enc_obj->sql;
        //echo $patientRefInfo[""];
        #echo "size = ".sizeof($listDoctors);
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
        
        $attending_dr = $pers_obj->getPersonellInfo($patientRefInfo['referrer_dr']);
        
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
            #echo "nr = ".$attending_dr_middleInitial;    
        }
        
        $consulting_dr_name = "Dr. ".$consulting_dr['name_first']." ".$consulting_dr['name_2']." ".$consulting_dr_middleInitial." ".$consulting_dr['name_last'];
        $attending_dr_name = "Dr. ".$attending_dr['name_first']." ".$attending_dr['name_2']." ".$attending_dr_middleInitial." ".$attending_dr['name_last'];
}	
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
#commented by VAN 04-28-08
/*
if ($rowsDiagnosis==0){
?>
<script language="javascript">
			var msg = 	"No ICD10 codes found for this encounter! \n"+
							"Please insert FIRST an ICD10 code for this encounter. \n"+
							"Encoding for ICD10 can be done in the Medocs menu.";
			alert(msg);	
			window.close();
</script>
<?php
}
*/
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
echo '<script type="text/javascript" src="'.$root_path.'js/shortcuts.js"></script>';
?>
<script language="javascript">
<?php
	require_once($root_path.'include/inc_checkdate_lang.php'); 
?>
</script>
<!-- -->
<script language="javascript">
	String.prototype.trim = function() { return this.replace(/^\s+|\s+$/, ''); };
	
	//saving and updating
	shortcut("F2",
		function(){
			document.med_certificate.onsubmit = chkForm(med_certificate);
			if (document.med_certificate.onsubmit)
				document.med_certificate.submit();
		}
	);
	
	//view pdf
	shortcut("F5",
		function(){
			var encounter_nr = '<?=$encounter_nr?>';
            var cert_nr = '<?=$cert_nr?>';
            var referral_nr = '<?=$referral_nr?>';
			printMedCert(encounter_nr, cert_nr, "'"+referral_nr+"'");
		}
	);

	function chkForm(){
		$('diagnosis_verbatim').value=$F('diagnosis_verbatim').trim();
		if ($F('diagnosis_verbatim')==''){
			alert(" Please enter the diagnosis");
			$('diagnosis_verbatim').focus();
			return false;
		}
		
		if (($F('doctors2')=='')&&($F('doctors')==0)){
			alert(" Please enter the doctor");
			$('doctors').focus();
			return false;
		}
		return true;
	}

	//edited by VAN 03-27-08
	//var type=<?php if (isset($_POST['cert_type'])) echo "'".$_POST['cert_type']."'"; else echo "'NML'"; ?>;	
	var type=<?php if (isset($_POST['cert_type'])) echo "'".$_POST['cert_type']."'"; else echo "0"; ?>;	
	function checkType(thisType){
		//alert($('cert_type').value);
		type = thisType;
	}
	
	function printMedCert(id, cert_nr, referral_nr){
		var doc = document.getElementById('doctors');
		var doc_name = doc.options[doc.selectedIndex].text;
		if (doc.selectedIndex == 0)
			doc_name='';
		var msg = "doc = '"+doc+"' \n"+
					"doc.selectedIndex = '"+doc.selectedIndex+"' \n"+
					"doc_name = '"+doc_name+"' \n";
//alert(msg);
		if (id==0) 
			id="";
		if (window.showModalDialog){  //for IE
			window.showModalDialog("cert_med_pdf.php?id="+id+"&type="+type+"&doc_name="+doc_name+"&cert_nr="+cert_nr+"&referral_nr="+referral_nr,"width=600,height=400,menubar=no,resizable=yes,scrollbars=no");
		}else{
			window.open("cert_med_pdf.php?id="+id+"&type="+type+"&doc_name="+doc_name+"&cert_nr="+cert_nr+"&referral_nr="+referral_nr,"medicalCertificate","modal, width=600,height=400,menubar=no,resizable=yes,scrollbars=no");
		}
	}
	
	//added by VAN 04-28-08
	function checkSignatory(val){
		//alert('hello ='+val);
		if (val==1){
			//show doctor as signatory
			document.getElementById('doc_sig').style.display = '';
		}else{
			//hide doctor as signatory
			document.getElementById('doc_sig').style.display = 'none';	
		}	
	}
	
	//added by VAN 06-13-08
	function checkMedico(){
		var d = document.med_certificate;
		/*
		if (d.cert_type[0].checked){
			//show medico info
			document.getElementById('ERMedico').style.display = '';
			document.getElementById('ERMedicoPOI').style.display = '';
			document.getElementById('ERMedicoTOI').style.display = '';
			document.getElementById('ERMedicoDOI').style.display = '';
			
			document.getElementById('space1').style.display = '';
			document.getElementById('space2').style.display = '';
			document.getElementById('space3').style.display = '';
			document.getElementById('space4').style.display = '';
			document.getElementById('space5').style.display = '';
			
		}else if(d.cert_type[1].checked){
			//hide medico info
			document.getElementById('ERMedico').style.display = 'none';	
			document.getElementById('ERMedicoPOI').style.display = 'none';	
			document.getElementById('ERMedicoTOI').style.display = 'none';	
			document.getElementById('ERMedicoDOI').style.display = 'none';	
			
			document.getElementById('space1').style.display = 'none';
			document.getElementById('space2').style.display = 'none';
			document.getElementById('space3').style.display = 'none';
			document.getElementById('space4').style.display = 'none';
			document.getElementById('space5').style.display = 'none';
		}
		*/
		uncheckall();
		document.getElementById('POI').value = "";
		document.getElementById('TOI').value = "";
		document.getElementById('DOI').value = "";
		document.getElementById('description').style.display="none";
	}
	
	function uncheckall () {
		cbox=document.getElementsByTagName('INPUT');
		for (i=0; i<cbox.length; i++){
			if (cbox[i].type=='checkbox'){
				cbox[i].checked = null; 
			}
		}
	}
	//-------------------
	
	function preset(){
		//alert('preset');
		var d = document.med_certificate;
		//alert(d.signatory[0].checked);
		//alert(d.signatory[1].checked);
		
		//checkMedico();
        enableTextBox('medicoOT');
		
		if (d.signatory[0].checked){
			//show doctor as signatory
			document.getElementById('doc_sig').style.display = '';
		}else if(d.signatory[1].checked){
			//hide doctor as signatory
			document.getElementById('doc_sig').style.display = 'none';	
		}
		
		//alert(document.getElementById('doctors').value);
		//document.getElementById('TOI').value = "";
		enableDoctor();
		
	}
	
function enableDoctor(){
	//alert(document.getElementById('doctors').value);
	if ((document.getElementById('doctors').value!=0)){
		document.getElementById('doctors2').readOnly = true;
		document.getElementById('doctors2').value = "";
	}else{		
		document.getElementById('doctors2').readOnly = false;
		if(document.getElementById('dr').value==0)
			document.getElementById('doctors2').value = "";
	}
}	
	
//added by VAN 06-13-08
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
//	alert("seg_setValidDate : seg_validDate ='"+seg_validDate+"'");	
}

var seg_validTime=false;
function setFormatTime(thisTime,AMPM){
//	var time = $('time_text_d');
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
	}else	if((hour > 12)&&(hour < 24)){
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


function enableTextBox(objID){
    //alert('case = '+obj.id);
    if (objID=='medicoOT'){
        if (document.getElementById(objID).checked)
            document.getElementById('description').style.display="";
        else    
            document.getElementById('description').style.display="none";        
    }
}

	//--------------------	
</script>
</head>

<body onLoad="preset();">
<table width="467" height="236" border="0" align="center" cellpadding="0" cellspacing="0" bordercolor="#BAD0FC" class="style2">
	<tr>
		<td colspan="*"><?= $errorMsg ?></td>
	</tr>
	<tr>		
		<td colspan="*" height="23" bgcolor="#FFFFFF" >
			<table width="100%" border="0" bgcolor="#F8F9FA"class="style3">
				<tr>
					<td width="22%" ><? echo 'Health Record Number:'?></td>
					<td width="37%" ><? echo $encInfo['pid']; ?></td>
					<td width="28%" align="right" ><? echo 'Case No.: '?></td>
					<td width="17%" align="left"><? if($referral)echo $encounter_nr." (".$referral_nr.")"; else echo $encounter_nr; ?></td>
				</tr>
				<tr>
					<td><span class="style3"><? echo "Name :"?> </span></td>
					<td colspan="2" class="style2">
						<? 
							#edited by VAN 02-28-08 
							$name = stripslashes(strtoupper($encInfo['name_first'])).' '.stripslashes(strtoupper($encInfo['name_middle'])).' '.stripslashes(strtoupper($encInfo['name_last']));
							echo $name;
						
						?>  <? #echo stripslashes(strtoupper($encInfo['name_last']));?>
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 
						<span class="style3"><? echo "Age :  "?></span><? echo $encInfo['age'].' old';?>
					</td>
					<td>&nbsp;</td>
				</tr>
				<tr>
					<td><span class="style3"><? echo "Address :"?></span></td>
					<td colspan="2" class="style2">
						<? echo stripslashes(strtoupper($encInfo['street_name']))."&nbsp;&nbsp; ".stripslashes(strtoupper($encInfo['brgy_name']))."&nbsp;&nbsp; ".stripslashes(strtoupper($encInfo['mun_name'])). ", ".stripslashes(strtoupper($encInfo['prov_name'])). "&nbsp;&nbsp;".stripslashes(strtoupper($encInfo['zipcode']));?>
					</td>
					<td>&nbsp;</td>
				</tr>
				
<?php
if (isset($result['diagnosis_principal']) && !empty($result['diagnosis_principal'])){
?>
				<tr>
					<td colspan="4"><b> Principal Diagnosis : </b><br>
						<?php
							echo $result['diagnosis_principal'];
						?>
					</td>
				</tr>
<?php
}
?>
<?php
if (isset($result['diagnosis_others']) && !empty($result['diagnosis_others'])){
?>
				<tr>
					<td colspan="4"><b>Other Diagnosis : </b><br>
						<?php
							echo $result['diagnosis_others'];
						?>
					</td>
				</tr>
<?php
}
?>
<?php
if (isset($result['therapy_principal']) && !empty($result['diagnosis_principal'])){
?>
				<tr>
					<td colspan="4"><b> Principal Procedure : </b><br>
						<?php
							echo $result['therapy_principal'];
						?>
					</td>
				</tr>
<?php
}
?>
<?php
if (isset($result['therapy_others']) && !empty($result['therapy_others'])){
?>
				<tr>
					<td colspan="4"><b>Other Procedure : </b><br>
						<?php
							echo $result['therapy_others'];
						?>
					</td>
				</tr>
<?php
}
?>
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
	<form id="med_certificate" name="med_certificate" method="post" action="" onSubmit="return chkForm()">
	<tr>
					<!--<td><span class="style3"><? echo "Consultation Date :"?></span></td>-->
					<td colspan="4" class="style2">
						<!--<input type="text" name="consultation_date" id="consultation_date" size="10" value="<?=date("m/d/Y",strtotime($encInfo['consultation_date']))?>">-->
						<?php
							$phpfd=$date_format;
							$phpfd=str_replace("dd", "%d", strtolower($phpfd));
							$phpfd=str_replace("mm", "%m", strtolower($phpfd));
							$phpfd=str_replace("yyyy","%Y", strtolower($phpfd));
	
							if (($medCertInfo['consultation_date']!='0000-00-00') && ($medCertInfo['consultation_date']!=""))
								$consultation_date= @formatDate2Local($medCertInfo['consultation_date'],$date_format);
							else
								$consultation_date = @formatDate2Local($encInfo['encounter_date'],$date_format);

							$sDateJS= '<input name="consultation_date" type="text" size="15" maxlength=10 value="'.$consultation_date.'"'. 
								'onFocus="this.select();" 
								id = "consultation_date"
								onBlur="IsValidDate(this,\''.$date_format.'\'); "
								onKeyUp="setDate(this,\''.$date_format.'\',\''.$lang.'\')">
								<img '.createComIcon($root_path,'show-calendar.gif','0','absmiddle').' id="consultation_date_trigger" style="cursor:pointer" >
								<font size=1>['; 			
								ob_start();
						?>
          				<script type="text/javascript">
							Calendar.setup ({
								inputField : "consultation_date", ifFormat : "<?php echo $phpfd?>", showsTime : false, button : "consultation_date_trigger", singleClick : true, step : 1
							});
			      		</script>
                        <?php
							$calendarSetup = ob_get_contents();
							ob_end_clean();
			
							$sDateJS .= $calendarSetup;
							/**/
							$dfbuffer="LD_".strtr($date_format,".-/","phs");
							$sDateJS = $sDateJS.$$dfbuffer.']';
						?>
						Consultation Date &nbsp;&nbsp; : &nbsp;
                        <?= $sDateJS ?>
                      </span>
		
					</td>
					
				</tr>
	<tr>
					<!--<td><span class="style3"><? echo "Scheduled Date :"?></span></td>-->
					<td colspan="4" class="style2">
						<!--<input type="text" name="consultation_date" id="consultation_date" size="10" value="<?=date("m/d/Y",strtotime($encInfo['consultation_date']))?>">-->
						<?php
							$phpfd=$date_format;
							$phpfd=str_replace("dd", "%d", strtolower($phpfd));
							$phpfd=str_replace("mm", "%m", strtolower($phpfd));
							$phpfd=str_replace("yyyy","%Y", strtolower($phpfd));
	
							if (($medCertInfo['scheduled_date']!='0000-00-00') && ($medCertInfo['scheduled_date']!=""))
								$scheduled_date= @formatDate2Local($medCertInfo['scheduled_date'],$date_format);
							else
								$scheduled_date= date("m/d/Y");	
							
							$sDateJS= '<input name="scheduled_date" type="text" size="15" maxlength=10 value="'.$scheduled_date.'"'. 
								'onFocus="this.select();" 
								id = "scheduled_date"
								onBlur="IsValidDate(this,\''.$date_format.'\'); "
								onKeyUp="setDate(this,\''.$date_format.'\',\''.$lang.'\')">
								<img '.createComIcon($root_path,'show-calendar.gif','0','absmiddle').' id="scheduled_date_trigger" style="cursor:pointer" >
								<font size=1>['; 			
								ob_start();
						?>
          				<script type="text/javascript">
							Calendar.setup ({
								inputField : "scheduled_date", ifFormat : "<?php echo $phpfd?>", showsTime : false, button : "scheduled_date_trigger", singleClick : true, step : 1
							});
			      		</script>
                        <?php
							$calendarSetup = ob_get_contents();
							ob_end_clean();
			
							$sDateJS .= $calendarSetup;
							/**/
							$dfbuffer="LD_".strtr($date_format,".-/","phs");
							$sDateJS = $sDateJS.$$dfbuffer.']';
						?>
						Scheduled Date : &nbsp;&nbsp; : &nbsp;
                        <?= $sDateJS ?>
                      </span>
		
					</td>
					
				</tr>			
	<tr>
		<td colspan="*" align="left" valign="top" bgcolor="#F8F9FA">
			<font style="color:#FF0000">Diagnosis</font> :
		</td>
	</tr>
	<tr>
		<td align="center" valign="top" bgcolor="#F8F9FA">
			<textarea name="diagnosis_verbatim" id="diagnosis_verbatim" cols="75" rows="7" wrap="physical"><?echo $medCertInfo['diagnosis_verbatim']; ?></textarea>
		</td>
	</tr>
	<tr>
		<td colspan="*" align="left" valign="top" bgcolor="#F8F9FA">
			Procedure :
		</td>
	</tr>
	<tr>
		<td align="center" valign="top" bgcolor="#F8F9FA">
			<!--<textarea name="procedure_verbatim" id="procedure_verbatim" cols="75" rows="7" <?= $rowsTherapy? "":"readonly" ?>><?= $medCertInfo['procedure_verbatim'] ?></textarea>-->
			<textarea name="procedure_verbatim" id="procedure_verbatim" cols="75" rows="7" wrap="physical" ><?= $medCertInfo['procedure_verbatim'] ?></textarea>
		</td>
	</tr>
	<tr>
		<td>&nbsp;</td>
	</tr>
	<tr>
		<td valign="top" bgcolor="#F8F9FA">
			Certificate Type &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; : &nbsp;&nbsp;&nbsp;
			<!--
			<input name="cert_type" id="cert_type" type="radio" value="0" <?php if($_POST['cert_type']=="0") echo"checked"; ?>>Medicolegal
			&nbsp;&nbsp;
			<input name="cert_type" id="cert_type" type="radio" value="1" <?php if(!isset($_POST['cert_type'])||($_POST['cert_type']=="1")) echo"checked"; ?>>Non-Medicolegal
			&nbsp;&nbsp;
			<input name="cert_type" id="cert_type" type="radio" value="2" <?php if(!isset($_POST['cert_type'])||($_POST['cert_type']=="2")) echo"checked"; ?>>Para-Medicolegal
-->
			<!--
			<input name="cert_type" id="cert_type" type="radio" value="ML" onclick="checkType(this.value)" <?php if($_POST['cert_type']=="ML") echo"checked"; ?>>Medicolegal
			&nbsp;&nbsp;
			<input name="cert_type" id="cert_type" type="radio" value="NML" onclick="checkType(this.value)" <?php if(!isset($_POST['cert_type'])||($_POST['cert_type']=="NML")) echo"checked"; ?>>Non-Medicolegal
			-->
			<?php 
				 if ($medCertInfo){
					if ($medCertInfo['is_medico_legal']!="0"){
						$checked1 = "checked";
						$checked2 = "";
					}else{
						$checked1 = "";
						$checked2 = "checked";
					}
				}else{
					#from care_encounter
					if ($encInfo['is_medico']!="0"){
						$checked1 = "checked";
						$checked2 = "";
					}else{
						$checked1 = "";
						$checked2 = "checked";
					}
				}	
			?>
			
			<input name="cert_type" id="cert_type" type="radio" value="1" onClick="checkType(this.value);checkMedico();" <?php echo $checked1; ?>>Medicolegal
			&nbsp;&nbsp;
			<input name="cert_type" id="cert_type" type="radio" value="0" onClick="checkType(this.value);checkMedico();" <?php echo $checked2; ?>>Non-Medicolegal

		</td>
	</tr>
	<tr id="space1">
		<td>&nbsp;</td>
	</tr>
	<!-- added by VAN -->
	<tr id="ERMedico">
		<?php
				   
				if(is_object($medico_cases)){
					$sTemp = '';
					$count=0;
                    $row_size = ($medico_count/2)-1;
					#$row_size = floor($medico_count/2);
				#echo "enc = ".$row_size;
					while($result=$medico_cases->FetchRow()) {
						$sTemp = $sTemp.'<input name="medico'.$result['code'].'" id="medico'.$result['code'].'" type="checkbox" onclick="enableTextBox(this.id);" value="'.$result['code'].'" ';
						
						$medico=$enc_obj->getEncounterByMedicoCases($encounter_nr,$encInfo['pid'],$result['code']);
						#echo "<br>sql = ".$enc_obj->sql;
                        if (($medico['description'])&& ($medico['description']!='none'))
                            $description = $medico['description'];
                            
						if($medico['medico_cases']==$result['code']) $sTemp = $sTemp.'checked';
						#echo "case = ".$medico['medico_cases'];
                        #echo "<br>desc = ".$medico['description'];
						$sTemp = $sTemp.'>';
						$sTemp = $sTemp.$result['medico_cases']."<br>";
						if($count<=5){
							$rowMedicoA =$sTemp;
							if($count==5){$sTemp='';}
						}else{ $rowMedicoB =$sTemp; }
						$count++;
                        
					}
					  #echo "here = ".$description;   
				}		
		?>
		<td class="adm_item" width="30%">
			Medico Legal Cases &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; : &nbsp;&nbsp;&nbsp;
			<table width="40%" height="84" border="0" cellpadding="1" id="srcMedicoTable" style="width:100%; font-size:12px">
				<td width="36%" height="80" valign="middle" id="leftTdMedico">
					<?=$rowMedicoA?>					
				</td>
				<td width="64%" valign="middle" id="rightTdMedico">
					<?=$rowMedicoB?>
                    <textarea style="display:none" id="description" name="description" cols="42" rows="2"><?=$description?></textarea>					
				</td>
			</table>
			
		</td>
					
	</tr>
	<tr id="space2">
		<td>&nbsp;</td>
	</tr>

	<tr id="ERMedicoPOI">
		<td class="adm_item">
			Place of Incident (POI) &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; : &nbsp;&nbsp;&nbsp;
			<input name="POI" id="POI" type="text" size="65" value="<?=ucwords(strtolower(trim($encInfo['POI'])))?>">
		</td>
	</tr>
	<tr id="space3">
		<td>&nbsp;</td>
	</tr>
	
	<tr id="ERMedicoTOI">
		<?php
		#echo "wait lng po = ".$encInfo['TOI'];
				$meridian = date("A",strtotime($encInfo['TOI']));
				#echo "meridian = ".$meridian;
				if ($meridian=='PM'){
					$selected1 = "";
					$selected2 = "selected";
				}else{
					$selected1 = "selected";
					$selected2 = "";
				}
				
				#if ($encInfo['TOI']=='00:00:00'){
				if (($encInfo['TOI']=='00:00:00') || (empty($encInfo['TOI']))){
					$TOI_val = "";
				}else{
					if (strstr($encInfo['TOI'],'24')){
						$TOI_val = "12:".substr($encInfo['TOI'],3,2);
						$selected1 = "selected";
						$selected2 = "";
					}else
						$TOI_val = date("h:i",strtotime($encInfo['TOI']));
				}
		?>
		<td class="adm_item">
			Time of Incident (TOI) &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; : &nbsp;&nbsp;&nbsp;
			<input type="text" id="TOI" name="TOI" size="4" maxlength="5" value="<?=$TOI_val?>" onChange="setFormatTime(this,'selAMPM')" />
			<select id="selAMPM" name="selAMPM">
				<option value="AM" <?=$selected1?>>A.M.</option>
				<option value="PM" <?=$selected2?>>P.M.</option>
			</select>&nbsp;<font size=1>[hh:mm]</font>
		</td>
		
	</tr>
	<tr id="space4">
		<td>&nbsp;</td>
	</tr>
	<tr id="ERMedicoDOI">
		<td class="adm_item">
			 <?php
				$phpfd=$date_format;
				$phpfd=str_replace("dd", "%d", strtolower($phpfd));
				$phpfd=str_replace("mm", "%m", strtolower($phpfd));
				$phpfd=str_replace("yyyy","%Y", strtolower($phpfd));
	
				if (($encInfo['DOI']!='0000-00-00') && ($encInfo['DOI']!=""))
					$DOI_val = @formatDate2Local($encInfo['DOI'],$date_format);
				else
					$DOI_val='';

				$sDateJS= '<input name="DOI" type="text" size="15" maxlength=10 value="'.$DOI_val.'"'. 
							'onFocus="this.select();" 
							id = "DOI"
							onBlur="IsValidDate(this,\''.$date_format.'\'); "
							onKeyUp="setDate(this,\''.$date_format.'\',\''.$lang.'\')">
							<img '.createComIcon($root_path,'show-calendar.gif','0','absmiddle').' id="DOI_trigger" style="cursor:pointer" >
							<font size=1>['; 			
							ob_start();
			?>
          <script type="text/javascript">
			Calendar.setup ({
					inputField : "DOI", ifFormat : "<?php echo $phpfd?>", showsTime : false, button : "DOI_trigger", singleClick : true, step : 1
			});
			      </script>
                        <?php
			$calendarSetup = ob_get_contents();
			ob_end_clean();
			
		$sDateJS .= $calendarSetup;
		/**/
		$dfbuffer="LD_".strtr($date_format,".-/","phs");
		$sDateJS = $sDateJS.$$dfbuffer.']';
#echo "$ dfbuffer ='".$dfbuffer."' &nbsp;&nbsp;";
#echo "$ $ dfbuffer ='".$$dfbuffer."' &nbsp;&nbsp;";
?>
                        Date of Incident (DOI) &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; : &nbsp;&nbsp;&nbsp;
                        <?= $sDateJS ?>
                      </span>
		</td>
	</tr>
	<!-- -->
	<tr id="space5">
		<td>&nbsp;</td>
	</tr>
	<!--added by VAN 04-28-08 -->
	<!-- edited by VAN 06-28-08 -->
	<!-- hide -->
	<!--<tr style="display:none">-->
	<tr>
		<td valign="top" bgcolor="#F8F9FA">
			Signatory &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; : &nbsp;&nbsp;&nbsp;
			
			<?php 
				if ($medCertInfo['is_doc_sig']!="0"){
					$checked1 = "checked";
					$checked2 = "";
				}else{
					$checked1 = "";
					$checked2 = "checked";
				}
			?>
			
			<input name="signatory" id="signatory" type="radio" value="1" onClick="checkSignatory(this.value);" checked>Doctor
			<!--
			<input name="signatory" id="signatory" type="radio" value="1" onClick="checkSignatory(this.value);" <?php echo $checked1; ?>>Doctor
			
			-->&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			<input name="signatory" id="signatory" type="radio" value="0" onClick="checkSignatory(this.value);" <?php echo $checked2; ?>>Medical Record Officer
			
		</td>
	</tr>
	
	<tr>
		<td>&nbsp;</td>
	</tr>
	
	<tr id="doc_sig">
		<td valign="top" bgcolor="#F8F9FA">
			Consulting/Attending Doctor :  &nbsp;&nbsp;&nbsp;
			<select name="doctors" id="doctors" onChange="enableDoctor();">
				<!--<option value='0'>-Select a doctor-</option>-->
<?php
	/*
	function print_doctors($value, $key){
		#echo "				<option value='".$key."'>".$value."</option> \n";
		if ($medCertInfo['dr_nr']==$key){
			echo "				<option value='".$key."' selected=\"selected\">".$value."</option> \n";
		}else{
			echo "				<option value='".$key."'>".$value."</option> \n";
		}	
	}
	#print_r($listDoctors);
	$listDoctors = array_unique($listDoctors);
	#print_r($listDoctors);
	array_walk($listDoctors, 'print_doctors');
	*/
	#edited by VAN 04-28-08
	$listDoctors = array_unique($listDoctors);
	
	if (empty($medCertInfo['dr_nr']))
		$medCertInfo['dr_nr'] = 0;
	
	foreach($listDoctors as $key=>$value){
	
		#echo "key = ".$key;
		#echo "<br>val = ".$value;
		if ($medCertInfo['dr_nr']==$key){
			echo "				<option value='".$key."' selected=\"selected\">".$value."</option> \n";
		}else{
			echo "				<option value='".$key."'>".$value."</option> \n";
		}	
	}
?>
			</select>
			<br>
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			<input type="text" size="50" name="doctors2" id="doctors2" value="<?=$medCertInfo['dr_nr']?>">
			<input type="hidden" name="dr" id="dr" value="<?=$medCertInfo['dr_nr']?>">
            <input type="hidden" name="cert_nr" id="cert_nr" value="<?=$cert_nr?>">
		</td>
	</tr>
	
	<tr>
		<td>&nbsp;</td>
	</tr>
	<tr>
		<td align="center" background="images/top_05.jpg" bgcolor="#EDF2FE">
<?php
#echo "med - ".$medCertInfo["dr_nr"];
if ($medCertInfo["dr_nr"]==="0")
 $medCertInfo = NULL;
 
#print_r($medCertInfo); 
#print_r($listDoctors);
			#if (!$medCertInfo || empty($medCertInfo)){
			if (empty($medCertInfo) || $medCertInfo["dr_nr"]==="0" || $medCertInfo["dr_nr"]===0){
				
				echo '			<input type="hidden" name="mode" id="mode" value="save">'."\n";
				echo '			<input type="submit" name="Submit" value="Save">'."\n";
			}else{
			
				echo '			<input type="hidden" name="mode" id="mode" value="update">'."\n";
				echo '			<input type="button" name="Print" value="Print" onClick="printMedCert('.$encounter_nr.','.$cert_nr.',\''.$referral_nr.'\')">'."\n &nbsp; &nbsp;";
				echo '			<input type="submit" name="Submit" value="Update">'."\n";
			}
			echo '			<input type="hidden" name="encounter_nr" id="encounter_nr" value="'.$encounter_nr.'">'."\n";
?>
			&nbsp; &nbsp;
			<input type="button" name="Cancel" value="Cancel"  onclick="javascript:window.parent.cClick();">
			<input type="hidden" name="pid" id="pid" value="<?=$encInfo['pid']?>">
		</td>
	</tr>
	</form>
</table>
</body>
</html>
