<?php
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
require($root_path.'include/inc_environment_global.php');
require($root_path.'modules/nursing/ajax/nursing-station-radio-common.php');
/**
* SEGHIS Integrated Hospital Information System version deployment 1.1 (mysql) 2007 
* GNU General Public License
*/
$lang_tables[] = 'departments.php';
define('LANG_FILE','konsil.php');

/* We need to differentiate from where the user is coming:
*  $user_origin != lab ;  from patient charts folder
*  $user_origin == lab ;  from the laboratory
*  and set the user cookie name and break or return filename
*/



require_once($root_path.'include/care_api_classes/class_radiology.php');
$objService=new SegRadio();

require_once($root_path.'include/care_api_classes/class_department.php');
$dept_obj = new Department();

if($user_origin=='lab')
{
  $local_user='ck_lab_user';
  if($target=="radio") $breakfile=$root_path.'modules/radiology/radiolog.php'.URL_APPEND;
   else $breakfile=$root_path.'modules/laboratory/labor.php'.URL_APPEND;  
}
else
{
  $local_user='ck_pflege_user';
  $breakfile="nursing-station-patientdaten.php".URL_APPEND."&edit=$edit&station=$station&pn=$pn";
}

require_once($root_path.'include/inc_front_chain_lang.php');
require_once($root_path.'global_conf/inc_global_address.php');

//$db->debug=1;

$thisfile=basename(__FILE__);

$bgc1='#ffffff';  // entry form's background color

$abtname=get_meta_tags($root_path."global_conf/$lang/konsil_tag_dept.pid");

$formtitle=$LDRadiology;
						
$db_request_table=$target;
#define('_BATCH_NR_INIT_',60000000);   # burn commented : July 24, 2007
$temp_batch_nr_init = date("Y")."000000";
#echo "nursing-station-patientdaten-doconsil-radio.php : temp_batch_nr_init = '".$temp_batch_nr_init."' <br> \n";
define('_BATCH_NR_INIT_',$temp_batch_nr_init); 
/*
*  The following are  batch nr inits for each type of test request
*   chemlabor = 10000000; patho = 20000000; baclabor = 30000000; blood = 40000000; generic = 50000000; radio = 60000000
*/
						
/* Here begins the real work */
require_once($root_path.'include/inc_date_format_functions.php');
   
# Create a core object
require_once($root_path.'include/inc_front_chain_lang.php');
$core = & new Core;

     /* Check for the patient number = $pn. If available get the patients data, otherwise set edit to 0 */
     if(isset($pn)&&$pn)
	 {		
		include_once($root_path.'include/care_api_classes/class_encounter.php');
		$enc_obj=new Encounter;
	    if( $enc_obj->loadEncounterData($pn)) {
/*		
			include_once($root_path.'include/care_api_classes/class_globalconfig.php');
			$GLOBAL_CONFIG=array();
			$glob_obj=new GlobalConfig($GLOBAL_CONFIG);
			$glob_obj->getConfig('patient_%');	
			switch ($enc_obj->EncounterClass())
			{
		    	case '1': $full_en = ($pn + $GLOBAL_CONFIG['patient_inpatient_nr_adder']);
		                   break;
				case '2': $full_en = ($pn + $GLOBAL_CONFIG['patient_outpatient_nr_adder']);
							break;
				default: $full_en = ($pn + $GLOBAL_CONFIG['patient_inpatient_nr_adder']);
			}						
*/			$full_en=$pn;
			$result=&$enc_obj->encounter;
		}
	   else 
	   {
	      $edit=0;
		  $mode="";
		  $pn="";
	   }		
     }
	 

	   
	 if(!isset($mode))   $mode="";
		
		  switch($mode)
		  {
				     case 'save':
						echo "Save->";	
				     	print_r($_POST);
				     	echo "\n <br>";
				     	echo "serviceArray->".$_POST['serviceArray'];
				     	
				     	/*
                                 $sql="INSERT INTO care_test_request_".$db_request_table." 
                                          (batch_nr, encounter_nr, dept_nr, 
										  xray, ct, sono, mammograph, mrt, nuclear, 
										  if_patmobile, if_allergy, if_hyperten, if_pregnant, 
										  clinical_info, test_request, send_date, 
										  send_doctor, status, 
										  history,
										  create_id, 
										  create_time)
										  VALUES 
										  (
										   '".$batch_nr."','".$pn."','".$dept_nr."',
										   '".$xray."','".$ct."','".$sono."','".$mammograph."','".$mrt."','".$nuclear."',
										   '".$if_patmobile."','".$if_allergy."','".$if_hyperten."','".$if_pregnant."',
										   '".htmlspecialchars($clinical_info)."','".htmlspecialchars($test_request)."','".formatDate2Std($send_date,$date_format)."',
										   '".htmlspecialchars($send_doctor)."', 'pending', 
										   'Create: ".date('Y-m-d H:i:s')." = ".$HTTP_SESSION_VARS['sess_user_name']."\n',
										   '".$HTTP_SESSION_VARS['sess_user_name']."',
										   '".date('YmdHis')."'
										   )";

							      if($ergebnis=$core->Transact($sql))
       							  {
									//echo $sql;
								  	// Load the visual signalling functions
									include_once($root_path.'include/inc_visual_signalling_fx.php');
									// Set the visual signal 
									setEventSignalColor($pn,SIGNAL_COLOR_DIAGNOSTICS_REQUEST);									
									
									 header("location:".$root_path."modules/laboratory/labor_test_request_aftersave.php?sid=$sid&lang=$lang&edit=$edit&saved=insert&pn=$pn&station=$station&user_origin=$user_origin&status=$status&target=$target&noresize=$noresize&batch_nr=$batch_nr");
									 exit;
								  }
								  else 
								  {
								     echo "<p>$sql<p>$LDDbNoSave"; 
									 $mode="";
								  }
								
								break; // end of case 'save'
							*/
								
		     case 'update':
			          /*
							      $sql="UPDATE care_test_request_".$db_request_table." SET 
								          dept_nr = '".$dept_nr."', 
										  xray='".$xray."', ct='".$ct."', sono='".$sono."', 
										  mammograph='".$mammograph."', mrt='".$mrt."', nuclear='".$nuclear."', 
										  if_patmobile='".$if_patmobile."', if_allergy='".$if_allergy."', 
										  if_hyperten='".$if_hyperten."', if_pregnant='".$if_pregnant."', 
										  clinical_info='".htmlspecialchars($clinical_info)."', test_request='".htmlspecialchars($test_request)."', 
										  send_date='".formatDate2Std($send_date,$date_format)."', 
										  send_doctor='".htmlspecialchars($send_doctor)."', status='".$status."', 
										  history=".$core->ConcatHistory("Update: ".date('Y-m-d H:i:s')." = ".$HTTP_SESSION_VARS['sess_user_name']."\n").",
										  modify_id='".$HTTP_SESSION_VARS['sess_user_name']."',
										  modify_time='".date('YmdHis')."'
										   WHERE batch_nr = '".$batch_nr."'";
										  							
							      if($ergebnis=$core->Transact($sql))
       							  {
									//echo $sql;
								  	// Load the visual signalling functions
									include_once($root_path.'include/inc_visual_signalling_fx.php');
									// Set the visual signal 
									setEventSignalColor($pn,SIGNAL_COLOR_DIAGNOSTICS_REQUEST);									
									
									 header("location:".$root_path."modules/laboratory/labor_test_request_aftersave.php?sid=$sid&lang=$lang&edit=$edit&saved=update&pn=$pn&station=$station&user_origin=$user_origin&status=$status&target=$target&batch_nr=$batch_nr&noresize=$noresize");
									 exit;
								  }
								  else
								   {
								      echo "<p>$sql<p>$LDDbNoSave"; 
								      $mode='';
								   }
								
								break; // end of case 'save'
							*/						
								
	        /* If mode is edit, get the stored test request when its status is either "pending" or "draft"
			*  otherwise it is not editable anymore which happens when the lab has already processed the request,
			*  or when it is discarded, hidden, locked, or otherwise. 
			*/
			case 'edit':
			  /*
		                $sql="SELECT * FROM care_test_request_".$db_request_table." WHERE batch_nr='".$batch_nr."' AND (status='pending' OR status='draft')";
		                if($ergebnis=$db->Execute($sql))
       		            {
				            if($editable_rows=$ergebnis->RecordCount())
					        {
     					       $stored_request=$ergebnis->FetchRow();
							   $edit_form=1;
					         }
			             }
				*/		 
						 break; ///* End of case 'edit': */
			
			 default: $mode="";
						   
		  }// end of switch($mode)
  
          if(!$mode) /* Get a new batch number */
		  {
		                $sql="SELECT batch_nr FROM care_test_request_".$db_request_table." ORDER BY batch_nr DESC";
		                if($ergebnis=$db->SelectLimit($sql,1))
       		            {
				            if($batchrows=$ergebnis->RecordCount())
					        {
						       $bnr=$ergebnis->FetchRow();
							   $batch_nr=$bnr['batch_nr'];
							   if(!$batch_nr) $batch_nr=_BATCH_NR_INIT_; else $batch_nr++;
					         }
					         else
					         {
					            $batch_nr=_BATCH_NR_INIT_;
					          }
			             }
			               else 
						   {
						     echo "<p>$sql<p>$LDDbNoRead";
						   }
						 $mode="save";   
		   }
		   
# Start Smarty templating here
 /**
 * LOAD Smarty
 */

 # Note: it is advisable to load this after the inc_front_chain_lang.php so
 # that the smarty script can use the user configured template theme

 require_once($root_path.'gui/smarty_template/smarty_care.class.php');
 $smarty = new smarty_care('nursing');

# Title in toolbar
 $smarty->assign('sToolbarTitle', "$LDDiagnosticTest :: $formtitle");

  # hide back button
 $smarty->assign('pbBack',FALSE);

 # href for help button
 $smarty->assign('pbHelp',"javascript:gethelp('request_radio.php','$pn')");

 # href for close button
 $smarty->assign('breakfile',$breakfile);

 # Window bar title
 $smarty->assign('sWindowTitle',"$LDDiagnosticTest :: $formtitle");

 # Create start new button if user comes from lab
  if($user_origin=='lab'){
	$smarty->assign('pbAux1',$thisfile.URL_APPEND."&station=$station&user_origin=$user_origin&status=$status&target=$target&noresize=$noresize");
	$smarty->assign('gifAux1',createLDImgSrc($root_path,'newpat2.gif','0'));
}

 if(!$noresize){
	$sOnLoadJs= 'if (window.focus) window.focus();window.moveTo(0,0); window.resizeTo(1000,740)';
}else{
 	$sOnLoadJs='if (window.focus) window.focus();';
}
if($pn=="") $sOnLoadJs = $sOnLoadJs.'document.searchform.searchkey.focus()';

$sOnLoadJs.=';jsGetServiceGroup();'; //xajax_srvGui()
$smarty->assign('sOnLoadJs','onLoad="'.$sOnLoadJs.'"');

 # Collect extra javascript code

 ob_start();
?>

<style type="text/css">
div.fva2_ml10 {font-family: verdana,arial; font-size: 12; margin-left: 10;}
div.fa2_ml10 {font-family: arial; font-size: 12; margin-left: 10;}
div.fva2_ml3 {font-family: verdana; font-size: 12; margin-left: 3; }
div.fa2_ml3 {font-family: arial; font-size: 12; margin-left: 3; }
.fva2_ml10 {font-family: verdana,arial; font-size: 12; margin-left: 10; color:#000000;}
.fva2b_ml10 {font-family: verdana,arial; font-size: 12; margin-left: 10; color:#000000;}
.fva0_ml10 {font-family: verdana,arial; font-size: 10; margin-left: 10; color:#000000;}
</style>

<script language="javascript">
<!-- 

function chkForm(d){

/*    if((d.test_request.value=='')||(d.test_request.value==' '))
	{
		alert("<?php echo $LDPlsEnterDiagnosisQuiry ?>");
		d.test_request.focus();
		return false;
	}
	else if((d.send_doctor.value=='')||(d.send_doctor.value==' '))
	{
		alert("<?php echo $LDPlsEnterDoctorName ?>");
		d.send_doctor.focus();
		return false;
	}
	else if((d.send_date.value=='')||(d.send_date.value==' '))
	{
		alert("<?php echo $LDPlsEnterDate ?>");
		d.send_date.focus();
		return false;
	}
	else return true;
	*/
	return true;
}

function sendLater()
{
   document.form_test_request.status.value="draft";
   if(chkForm(document.form_test_request)) document.form_test_request.submit(); 
}

function printOut()
{
	urlholder="<?php echo $root_path ?>modules/laboratory/labor_test_request_printpop.php?sid=<?php echo $sid ?>&lang=<?php echo $lang ?>&user_origin=<?php echo $user_origin ?>&subtarget=<?php echo $target ?>&batch_nr=<?php echo $batch_nr ?>&pn=<?php echo $pn; ?>";
	testprintout<?php echo $sid ?>=window.open(urlholder,"testprintout<?php echo $sid ?>","width=800,height=600,menubar=no,resizable=yes,scrollbars=yes");
    testprintout<?php echo $sid ?>.print();
}

<?php require($root_path.'include/inc_checkdate_lang.php'); ?>
//-->
</script>

<script language="javascript" src="<?php echo $root_path; ?>js/setdatetime.js"></script>
<script language="javascript" src="<?php echo $root_path; ?>js/checkdate.js"></script>
<script language="javascript" src="<?php echo $root_path; ?>js/dtpick_care2x.js"></script>

<?php
    echo '<script type="text/javascript" src="'.$root_path.'js/jsprototype/prototype.js"></script>'."\r\n";
	echo '<script type="text/javascript" src="'.$root_path.'js/fat/fat.js"></script>'."\r\n";
    echo '<link rel="stylesheet" type="text/css" media="all" href="' .$root_path.'js/jscalendar/calendar-win2k-cold-1.css">';
	echo '<script type="text/javascript" src="'.$root_path.'js/jscalendar/calendar.js"></script>';
	echo '<script type="text/javascript" src="'.$root_path.'js/jscalendar/lang/calendar-en.js"></script>';
	echo '<script type="text/javascript" src="'.$root_path.'js/jscalendar/calendar-setup_3.js"></script>';
    echo '<script type="text/javascript" src="'.$root_path.'modules/nursing/js/nursing-station-request.js"></script>'."\r\n";
    
    $xajax->printJavascript($root_path.'classes/xajax-0.2.5');
    
$sTemp = ob_get_contents();

ob_end_clean();

$smarty->append('JavaScript',$sTemp);

ob_start();

?>

<ul>

<?php
if($edit){

?>
<form name="form_test_request" id="form_test_request" method="post" action="<?php echo $thisfile ?>" onSubmit="return chkForm(this)">

<?php
/* If in edit mode display the control buttons */

$controls_table_width=700;

require($root_path.'include/inc_test_request_controls.php');

}
elseif(!$read_form && !$no_proc_assist)
{
?>

<table border=0>
  <tr>
    <td valign="bottom"><img <?php echo createComIcon($root_path,'angle_down_l.gif','0') ?>></td>
    <td><font color="#000099" SIZE=3  FACE="verdana,Arial"> <b><?php echo $LDPlsSelectPatientFirst ?></b></font></td>
    <td><img <?php echo createMascot($root_path,'mascot1_l.gif','0','absmiddle') ?>></td>
  </tr>
</table>
<?php
}
?>
   
   <!--  outermost table creating form border -->
<table border=0 bgcolor="#000000" cellpadding=1 cellspacing=0>
  <tr>
    <td>
	
	<table border=0 bgcolor="#ffffff" cellpadding=0 cellspacing=0>
   <tr>
     <td>
	
	   <table   cellpadding=0 cellspacing=1 border=0 width=700>
   <tr  valign="top">
   <td  bgcolor="#ffffff" rowspan=2>
 <?php
/*echo '
		<div class=fva2b_ml10><span style="background:yellow"><b>'.$result[patnum].'</b></span><br>
		<b>'.$result[name].', '.$result[vorname].'</b> <br>
		<font color=maroon>'.formatDate2Local($result[gebdatum],$date_format).'</font> <br><font size=1>
		'.nl2br($result[address]).'<p>
		'.$station.'&nbsp;'.$result[kasse].' '.$result[kassename].'</div>';
echo '
		<input type="text" name="stat_dept" value="'.strtoupper($station).'" size=25 maxlength=30>
  		</div>
		';*/
        if($edit)
        {
           #echo "sid=".$sid."\n fullname=".$full_en." \n encounter = ". $pn;
        	
		   echo '<img src="'.$root_path.'main/imgcreator/barcode_label_single_large.php?sid='.$sid.'&lang='.$lang.'&fen='.$full_en.'&en='.$pn.'" width=282 height=178>';
		}
        elseif($pn=='')
		{
		    $searchmask_bgcolor="#f3f3f3";
            include($root_path.'include/inc_test_request_searchmask.php');
        }
		?></td>
      <td bgcolor="<?php echo $bgc1 ?>"  class=fva2_ml10><div   class=fva2_ml10><font size=5 color="#0000ff"><b><?php echo $formtitle ?></b></font>
		 <br><?php echo $global_address[$target].'<br>'.$LDTel.'&nbsp;'.$global_phone[$target]; ?>
		 </td>
		 </tr>
	 <tr>
      <td bgcolor="<?php echo $bgc1 ?>" align="right" valign="bottom">	 
	  <?php
		    echo '<font size=1 color="#990000" face="verdana,arial">'.$batch_nr.'</font>&nbsp;&nbsp;<br>';
			  echo "<img src='".$root_path."classes/barcode/image.php?code=".$batch_nr."&style=68&type=I25&width=145&height=40&xres=2&font=5' border=0>";
     ?>
	     </td>
		 </tr>
		 	
		<tr bgcolor="<?php echo $bgc1 ?>">
		<td  valign="top" colspan=2 >
  </td>
</tr>
		 
	<tr bgcolor="<?php echo $bgc1 ?>">
		<td colspan=2><div class=fva2_ml10><?php echo $LDClinicalInfo ?>:<br>
		<textarea name="clinical_info" id="clinical_info" cols=80 rows=2 wrap="physical"><?php if($edit_form || $read_form) echo stripslashes($stored_request['clinical_info']) ?></textarea>
		</td>
	</tr>
	<!-- List of service requested -->
	<tr>
		<div id="srvlist">
		<!-- 
			<table id="srclistTable" style="margin-botton:5px" width="85%" border="0" cellpadding="0" cellspacing="0">
				<tbody>
				
				</tbody>
			</table>
		 -->
		</div>	
	</tr>
	<!-- Request service for radiology -->
	<tr bgcolor="<?php echo $bgc1 ?>">
		<td colspan=2><div class=fva2_ml10><?php echo $LDReqTest ?>:<br>
		   <?php
		   	$all_radGrp=&$dept_obj->getAllRadiologyDept(); 
		   if(is_object($all_radGrp)){
			$stmp = '';
			$radRow=$all_radGrp->FetchRow();
			//print_r($radRow);
		   	echo '<select name="raddept_nr" id="raddept_nr" onChange="jsGetServiceGroup()">
		   		<option value="0">Select a Department</option>';
		   			while($radRow=$all_radGrp->FetchRow()){
		   				$stmp = $stmp.'<option value="'.$radRow['nr'].'"';
		   				if(isset($raddept_nr)&&($raddept_nr ==$radRow['nr'])) $stmp = $stmp.'selected';
		   				  $stmp = $stmp.'>';
		   					if($$radRow['LD_var']!= '') $stmp = $stmp.$$radRow['LD_var'];
		   					else $stmp = $stmp.$radRow['name_formal'];
		   				$stmp = $stmp.'</option>';
		   			}
		    	$stmp = $stmp.'</select>
					<font size=1><img '.createComIcon($root_path,'redpfeil_l.gif','0','',TRUE).'>Radiology Service Department</font>';
    		    echo $stmp;  
		   }else{
				echo "Invalid object";
           }
           ?>
		        <select name="paramselect" id="paramselect" onChange="jsGetRadioService(1)"></select>
		   		<?='<font size=1><img '.createComIcon($root_path,'redpfeil_l.gif','0','',TRUE).'> Radiology Service Group</font>' ?>
			   		<div id="listcontainer">
			   			<!-- <span>
				   			<br>Filter<input type="text" id="searchservice" name="searchservice" style="width:120px" value="" onKeyUp="fetchServList(300)" onBlur="clearText();"/>&nbsp;<?='<font size=1><img '.createComIcon($root_path,'redpfeil_l.gif','0','',TRUE).'>Type here the service code to filter</font>'?>
					   		<br>Selected:<span id="selectedcount">0</span>
						</span>
						 -->
						<br>Selected:<span id="selectedcount">0</span>	
				   		<table id="srcRowsTable" style="margin-botton:5px" width="85%" border="0" cellpadding="0" cellspacing="0">
							<!-- service list -->
				   		</table>
				   	</div>
				
				
		 </td>
	</tr>	


	
	<tr bgcolor="<?php echo $bgc1 ?>">
		<td colspan=2 align="right"><div class=fva2_ml10><font color="#000099">
		 <?php echo $LDDate ?>:
		 <?
		 			$phpfd=$date_format;
			 		$phpfd=str_replace("dd", "%d", strtolower($phpfd));
			 		$phpfd=str_replace("mm", "%m", strtolower($phpfd));
	     		$phpfd=str_replace("yyyy","%Y", strtolower($phpfd));
	     		//$phpfd=str_replace("yy","%Y", strtolower($phpfd));
		 ?>
		 
		<input type="text" name="send_date" 
		value="<?php 
		
		            if($edit_form || $read_form)
					{
					  echo formatDate2Local($stored_request['send_date'],$date_format); 
					}
					else
					{
					  echo formatDate2Local(date("Y-m-d"),$date_format);
					}
					
					
				  ?>" id="date_text" size=10 maxlength=10 onBlur="IsValidDate(this,'<?php echo $date_format ?>')"  onKeyUp="setDate(this,'<?php echo $date_format ?>','<?php echo $lang ?>')">
	  	<!--<a href="javascript:show_calendar('form_test_request.send_date','<?php echo $date_format ?>')">-->
		<img <?php echo createComIcon($root_path,'show-calendar.gif','0','absmiddle'); ?> id="date_trigger" style="cursor:pointer"><font size=1 face="arial">
		  <!--EDITED: SEGWORKS -->
	<script type="text/javascript">
	Calendar.setup ({
		inputField : "date_text", ifFormat : "<?php echo $phpfd?>", showsTime : false, button : "date_trigger", singleClick : true, step : 1
	
	});
</script>		  
  <?php echo $LDRequestingDoc ?>:
		<input type="text" name="send_doctor" size=40 maxlength=40 value="<?php if($edit_form || $read_form) echo $stored_request['send_doctor'] ?>"></div><br>
		</td>
    </tr>
  <!--  
	<tr bgcolor="<?php echo $bgc1 ?>">
		<td  colspan=2 bgcolor="#cccccc"><div class=fva2_ml10><font color="#000099">
		 <?php echo $LDXrayNumber ?>
		<img src="<?php echo $root_path ?>gui/img/common/default/gray_pixel.gif" border=0 width=100 height=20 align="absmiddle" vspace=3>
  		<?php echo $LD_r_cm2 ?>
		<img src="<?php echo $root_path ?>gui/img/common/default/gray_pixel.gif" border=0 width=50 height=20 align="absmiddle" vspace=3>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		 <?php echo $LDXrayTechnician ?>&nbsp;<img src="<?php echo $root_path ?>gui/img/common/default/gray_pixel.gif" border=0 width=150 height=20 align="absmiddle" vspace=3>
		<?php echo $LDDate ?>&nbsp;<img src="<?php echo $root_path ?>gui/img/common/default/gray_pixel.gif" border=0 width=100 height=20 align="absmiddle" vspace=3>
     
	  </div>
    </tr>
   -->
    <!--  	
	<tr bgcolor="<?php echo $bgc1 ?>">
		<td colspan=2> 
		 	<div class=fva2_ml10>&nbsp;<br><font color="#969696"><?php echo $LDNotesTempReport ?></font><br>
			<img src="<?php echo $root_path ?>gui/img/common/default/gray_pixel.gif" border=0 width=675 height=120>
		</td>
	</tr>
	 -->	
	<!-- 	
	<tr bgcolor="<?php echo $bgc1 ?>">
		<td colspan=2 align="right"><div class=fva2_ml10><font color="#969696">
		 <?php echo $LDDate ?>
		<img src="<?php echo $root_path ?>gui/img/common/default/gray_pixel.gif" border=0 width=100 height=20 align="absmiddle" vspace=3>
        <?php echo $LDReportingDoc ?>
		<img src="<?php echo $root_path ?>gui/img/common/default/gray_pixel.gif" border=0 width=250 height=20 align="absmiddle" vspace=3></div>
		</td>
    </tr>
     -->
		</table> 
	 
	 </td>
   </tr>
 </table>
	
	</td>
  </tr>
</table>
<p>

<?php
$edit = 1;
if($edit)
{

/* If in edit mode display the control buttons */
require($root_path.'include/inc_test_request_controls.php');

require($root_path.'include/inc_test_request_hiddenvars.php');

?>

</form>

<?php
}
?>

</ul>

<?php

$sTemp = ob_get_contents();
ob_end_clean();

# Assign to page template object
$smarty->assign('sMainFrameBlockData',$sTemp);

 /**
 * show Template
 */
 $smarty->display('common/mainframe.tpl');

 ?>
