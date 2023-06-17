<?php
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
require($root_path.'modules/insurance_co/ajax/hcplan-admin.common.php');
	$xajax->printJavascript($root_path.'classes/xajax');

require($root_path.'include/inc_environment_global.php');
/**
* CARE2X Integrated Hospital Information System Deployment 2.1 - 2004-10-02
* GNU General Public License
* Copyright 2002,2003,2004,2005 Elpidio Latorilla
* elpidio@care2x.org, 
*
* See the file "copy_notice.txt" for the licence notice
*/
define('LANG_FILE','finance.php');
$local_user='aufnahme_user';
require_once($root_path.'include/inc_front_chain_lang.php');
/* Load the insurance object */
require_once($root_path.'include/care_api_classes/class_insurance.php');
$ins_obj=new Insurance;

$dbtable='care_config_global'; // Table name for global configurations
$GLOBAL_CONFIG=array();
$new_date_ok=0;
# Create global config object
require_once($root_path.'include/care_api_classes/class_globalconfig.php');
require_once($root_path.'include/inc_date_format_functions.php');

$glob_obj=new GlobalConfig($GLOBAL_CONFIG);
if($glob_obj->getConfig('date_format')) $date_format=$GLOBAL_CONFIG['date_format'];
$date_format=$GLOBAL_CONFIG['date_format'];
$phpfd=$date_format;
$phpfd=str_replace("dd", "%d", strtolower($phpfd));
$phpfd=str_replace("mm", "%m", strtolower($phpfd));
$phpfd=str_replace("yyyy","%Y", strtolower($phpfd));
$phpfd=str_replace("yy","%y", strtolower($phpfd));

$breakfile='insurance_co_list.php'.URL_APPEND; 

#------------added by VAN---
global $db;
$hcare_id = $_GET['id'];
$sql = "SELECT * FROM care_insurance_firm WHERE hcare_id='".$hcare_id."'";
$result=$db->Execute($sql);
$ts=$result->FetchRow();

#$sql2 = "SELECT * FROM seg_hospital_info";
#$result2=$db->Execute($sql2);
#$rs_hospital = $result2->FetchRow();

#-----------------
#echo "<br>rvu = ".$isrvu;
if(!isset($mode)){
	$mode='';
	$edit=true;		
}else{
#echo "mode = ".$mode;
	switch($mode)
	{
	
		case 'save':
		{
			$basis=0;
			
			if ($_POST['isconf']==1){
				$basis = $basis+1;
			}
			if ($_POST['isroomtyp']==1){
				$basis = $basis+2;
			}		
			if ($_POST['isrvu']==1){
				$basis = $basis+4;
			}	
			if ($_POST['isperitem']==1){
				$basis = $basis+8;	
			}	
			
			#check if benefit schedule is already exists
			#$ins_obj->getBenefitSked($HTTP_POST_VARS['hcare_id'], $HTTP_POST_VARS['benefit_id'], $basis);
			#$ins_obj->getBenefitSked($HTTP_POST_VARS['hcare_id'], $HTTP_POST_VARS['benefit_id']);
			#$row = $ins_obj->count;
			
			#if ($row==0){
			$ins_obj->deleteBenefitSked($HTTP_POST_VARS['hcare_id'], $HTTP_POST_VARS['benefit_id']);	
			#echo "sql = ".$ins_obj->sql;
			# Validate important data for seg_hcare_bsked table	
			$bsked['hcare_id'] = $HTTP_POST_VARS['hcare_id'];
			$bsked['benefit_id'] = $HTTP_POST_VARS['benefit_id'];
			$bsked['basis'] = $basis;
			
			if ($HTTP_POST_VARS['effectvty_dte']) {
				$bsked['effectvty_dte'] = date("Ymd",strtotime($HTTP_POST_VARS['effectvty_dte']));
			}
			
			
			#$is_exists = $ins_obj->InsuranceBenefitsExists();
			
			$ins_obj->setDataArray($bsked);
			if(!@$ins_obj->saveBenefitSked($bsked)) echo "<br>$LDDbNoSave";
			#}	
			#echo "sql = ".$ins_obj->sql;
			#if (($tab_selected0!=NULL)&&($HTTP_POST_VARS['range_start']!=NULL)){
			if ($_POST['isrvu']!=0){
				# RVU
				$ins_obj->deleteRVUBenefit($HTTP_POST_VARS['hcare_id'], $HTTP_POST_VARS['benefit_id'], $HTTP_POST_VARS['range_start']);
				#echo "<br>delete sql = ".$ins_obj->sql;
				# Validate important data for seg_hcare_RVUrange table
				#$HTTP_POST_VARS['firm_id']=trim($HTTP_POST_VARS['firm_id']);
				#$HTTP_POST_VARS['name']=trim($HTTP_POST_VARS['name']);
				$rvu['hcare_id'] = $HTTP_POST_VARS['hcare_id'];
				$rvu['benefit_id'] = $HTTP_POST_VARS['benefit_id'];
				$rvu['range_start'] = trim($HTTP_POST_VARS['range_start']);
				$rvu['range_end'] = trim($HTTP_POST_VARS['range_end']);
				$rvu['amountlimit'] = trim($HTTP_POST_VARS['rvu_amtlimit']);
				$rvu['rateperRVU'] = trim($HTTP_POST_VARS['rvu_rate']);
				$ins_obj->setDataArray($rvu);
				if(!@$ins_obj->saveRVURange($rvu)) echo "<br>$LDDbNoSave";
			}
			
			#if (($tab_selected1!=NULL)&&($HTTP_POST_VARS['room_type']!=0)){
			if ($_POST['isroomtyp']!=0){
				# Room Type
				$ins_obj->deleteRoomTypeBenefit($HTTP_POST_VARS['hcare_id'], $HTTP_POST_VARS['benefit_id'],$HTTP_POST_VARS['room_type']);
				#echo "<br>delete sql = ".$ins_obj->sql;
				# Validate important data for seg_hcare_roomtype table
				$roomtyp['hcare_id'] = $HTTP_POST_VARS['hcare_id'];
				$roomtyp['benefit_id'] = $HTTP_POST_VARS['benefit_id'];
				$roomtyp['roomtype_nr'] = $HTTP_POST_VARS['room_type'];
				$roomtyp['rateperday'] = trim($HTTP_POST_VARS['rt_rate']);
				$roomtyp['amountlimit'] = trim($HTTP_POST_VARS['rt_amtlimit']);
				$roomtyp['dayslimit'] = trim($HTTP_POST_VARS['rt_dayslimit']);
				$roomtyp['rateperRVU'] = trim($HTTP_POST_VARS['rt_rateperRVU']);
				$roomtyp['year_dayslimit'] = trim($HTTP_POST_VARS['rt_yrslimit_prin']);
				$roomtyp['year_dayslimit_alldeps'] = trim($HTTP_POST_VARS['rt_yrslimit_ben']);
				$ins_obj->setDataArray($roomtyp);
				if(!@$ins_obj->saveRoomType($roomtyp)) echo "<br>$LDDbNoSave";
		   }
		  
		  #if (($tab_selected2!=NULL)&&($HTTP_POST_VARS['conf_type']!=0)){
		  if ($_POST['isconf']!=0){
		  		# Confinement Type
				$ins_obj->deleteConfinementBenefit($HTTP_POST_VARS['hcare_id'], $HTTP_POST_VARS['benefit_id'],$HTTP_POST_VARS['conf_type']);
				#echo "<br>delete sql = ".$ins_obj->sql;
				# Validate important data for seg_hcare_confinetype table
				$conf['hcare_id'] = $HTTP_POST_VARS['hcare_id'];
				$conf['benefit_id'] = $HTTP_POST_VARS['benefit_id'];
				$conf['confinetype_id'] = $HTTP_POST_VARS['conf_type'];
				$conf['rateperday'] = trim($HTTP_POST_VARS['ct_rate']);
				$conf['amountlimit'] = trim($HTTP_POST_VARS['ct_amtlimit']);
				$conf['dayslimit'] = trim($HTTP_POST_VARS['ct_dayslimit']);
				$conf['rateperRVU'] = trim($HTTP_POST_VARS['ct_rateperRVU']);
				$conf['year_dayslimit'] = trim($HTTP_POST_VARS['ct_yrslimit_prin']);
				$conf['year_dayslimit_alldeps'] = trim($HTTP_POST_VARS['ct_yrslimit_ben']);
				$ins_obj->setDataArray($conf);
				if(!@$ins_obj->saveConfinementType($conf)) echo "<br>$LDDbNoSave";
			}
			
		  #if ($tab_selected3!=NULL){
		  if ($_POST['isperitem']!=0){
		  		
		  		if ($_POST["items"]!=NULL){
				
					$bulk_md = array();
					$bulk_hs = array();
					$bulk_OR = array();
					foreach ($_POST["items"] as $i=>$v) {
						#echo "<br>areas = ".$_POST['areas'][$i];
						if ($_POST['areas'][$i]=="DM"){
							$bulk_md[] = array($_POST["items"][$i],$_POST["amtlimit"][$i]);
						}else{
							if ($_POST['areas'][$i]=="OR"){
								$bulk_hs_OR[] = array($_POST["items"][$i],$_POST['areas'][$i],0,$_POST["amtlimit"][$i]);
							}else{
								$bulk_hs[] = array($_POST["items"][$i],$_POST['areas'][$i],$_POST["amtlimit"][$i],0);
							}	
						}	
					}
					/*
					echo "<br>med = ";
					print_r($bulk_md);
					echo "<br>or = ";
					print_r($bulk_hs_OR);
					echo "<br>hs = ";
					print_r($bulk_hs);
					*/
					if (count($bulk_md)!=0){
						#echo "<br>sulod med";
						#print_r($bulk_md);
						# save in the table for drugs and medicine only
						$ins_obj->clearProducts($HTTP_POST_VARS['hcare_id'], $HTTP_POST_VARS['benefit_id']);
						$ins_obj->addProducts($HTTP_POST_VARS['hcare_id'], $HTTP_POST_VARS['benefit_id'], $bulk_md);
					}
					
					if ((count($bulk_hs_OR)!=0)||(count($bulk_hs)!=0)){
						# save in the table for hospital services
						#echo "<br>sulod OR and hs";
						$ins_obj->clearServices($HTTP_POST_VARS['hcare_id'], $HTTP_POST_VARS['benefit_id']);
						#echo "<br>hs sql = ".$ins_obj->sql;
						# hospital service: OR
						if (count($bulk_hs_OR)!=0){
							#echo "<br>sulod OR";
							#print_r($bulk_hs_OR);
							$ins_obj->addServices($HTTP_POST_VARS['hcare_id'], $HTTP_POST_VARS['benefit_id'], $bulk_hs_OR);
						}
					
						if (count($bulk_hs)!=0){
							#echo "<br>sulod hs";
							#print_r($bulk_hs);
							# hospital service: Laboratory, Radiology, Other services
							$ins_obj->addServices($HTTP_POST_VARS['hcare_id'], $HTTP_POST_VARS['benefit_id'], $bulk_hs);
						}
					}	
					
					global $db;
					print_r($db->ErrorMsg());
					
		  		}
		   }				
		  
			break;
		}
		
	} # end of switch($mode)
	
}


$bgc=$root_path.'gui/img/skin/default/tableHeaderbg3.gif';
$bgc2='#eeeeee';

# Start Smarty templating here
 /**
 * LOAD Smarty
 */
 # Note: it is advisable to load this after the inc_front_chain_lang.php so
 # that the smarty script can use the user configured template theme

 require_once($root_path.'gui/smarty_template/smarty_care.class.php');
 $smarty = new smarty_care('system_admin');

# Title in toolbar
 $smarty->assign('sToolbarTitle',"$LDInsuranceCo :: $LDSchedule");

 # href for help button
 $smarty->assign('pbHelp',"javascript:gethelp('insurance_new.php','new')");

 # href for close button
 $smarty->assign('breakfile',$breakfile);

 # Window bar title
 $smarty->assign('sWindowTitle',"$LDInsuranceCo :: $LDSchedule");

 $smarty->assign('sOnLoadJs','onLoad="preSet();"');	
# Colllect javascript code

#------------row Product-list----

$row_body = '<tr>
			     <td colspan=\'7\'>Item list is currently empty...</td>
		       </tr>';

#----------------
	if ($_POST['isrvu']!=0){
		# get RVU
		$ObjRVU = $ins_obj->getRVUBenefit($HTTP_POST_VARS['hcare_id'], $HTTP_POST_VARS['benefit_id'], $HTTP_POST_VARS['range_start']);
		$rsRVU=$ObjRVU->FetchRow();
	}
	
	if ($_POST['isroomtyp']!=0){	
		# get RoomType
		$ObjRoom = $ins_obj->getRoomTypeBenefit($HTTP_POST_VARS['hcare_id'], $HTTP_POST_VARS['benefit_id'], $HTTP_POST_VARS['room_type']);
		$rsRoom=$ObjRoom->FetchRow();
	}
	
	if ($_POST['isconf']!=0){	
		# get Confinement Type
		$ObjConf = $ins_obj->getConfinementBenefit($HTTP_POST_VARS['hcare_id'], $HTTP_POST_VARS['benefit_id'], $HTTP_POST_VARS['conf_type']);
		$rsConf=$ObjConf->FetchRow();
	}	
	
#-------------------


ob_start();

echo "<!--Include dojo toolkit -->";
echo "<script type=\"text/javascript\" src=\"".$root_path."js/dojo/dojo.js\"></script>"."\n \r";
echo "<script type=\"text/javascript\" src=\"".$root_path."js/jsprototype/prototype1.5.js\"></script>"."\n \r";
echo "<script type=\"text/javascript\" src=\"js/seg-insurance-admin.js\"></script>";


?>

<script type="text/javascript" src="<?=$root_path?>js/NumberFormat154.js"></script>

<!-- Include dojoTab Dependencies -->
<script type="text/javascript">
	dojo.require("dojo.widget.TabContainer");
	dojo.require("dojo.widget.LinkPane");
	dojo.require("dojo.widget.ContentPane");
	dojo.require("dojo.widget.LayoutContainer");
	dojo.require("dojo.event.*");
	
</script>

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
	background-color:#ffffff;
	border:1px outset #3d3d3d;
}
.olcg {
	background-color:#ffffff; 
	background-image:url("<?= $root_path ?>images/bar_05.gif");
	text-align:center;
}
.olcgif {background-color:#333399; text-align:center;}
.olfg {
	background-color:#ffffff; 
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

<link rel="stylesheet" type="text/css" media="all" href="<?= $root_path ?>js/jscalendar/calendar-win2k-cold-1.css" />

<script language="javascript" src="<?=$root_path?>js/setdatetime.js"></script>
<script language="javascript" src="<?=$root_path?>js/checkdate.js"></script>
<!--<script language="javascript" src="<?=$root_path?>js/dtpick_care2x.js"></script>-->

<script type="text/javascript" src="<?=$root_path?>js/jscalendar/calendar.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jscalendar/lang/calendar-en.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jscalendar/calendar-setup_3.js"></script>

<?php
$sTemp = ob_get_contents();
ob_end_clean();

$smarty->append('JavaScript',$sTemp);

# Buffer page output

ob_start();

?>

 <ul>
<?php
if(!empty($mode)){ 
?>
<table border=0>
  <tr>
    <td><img <?php echo createMascot($root_path,'mascot1_r.gif','0','bottom') ?>></td>
    <td valign="bottom"><br><font face="Verdana, Arial" size=3 color="#880000"><b>
<?php 
	switch($mode)
	{
		case 'bad_data':
		{
			echo $LDMissingFirmInfo;
			break;
		}
		case 'firm_exists':
		{
			echo "$LDFirmExists<br>$LDDataNoSave<br>$LDPlsChangeFirmID";
		}
	}
?>
	</b></font><p>
</td>
  </tr>
</table>
<?php 
} 

?>
<form action="<?php echo $thisfile; ?>" method="post" name="insurance_co" id="insurance_co" onSubmit="return check(this)">
<!--<font face="Verdana, Arial" size=-1><?php echo $LDEnterAllFields ?>-->
<table border=0>
  <tr>
    <td align=right class="reg_item"><?php echo $LDInsuranceCoID ?>: </td>
    <td bgcolor="#ffffee" class="vi_data"><input type="text" name="firm_id" id="firm_id" size=50 maxlength=60 value="<?= $ts['firm_id'] ?>" readonly="1"><input type="hidden" name="hcare_id" id="hcare_id" size=2 maxlength=5 value="<?= $hcare_id ?>"><br></td>
  </tr> 
  <tr>
    <td align=right class="reg_item"><?php echo $LDInsuranceCoName ?>: </td>
    <td bgcolor="#ffffee" class="vi_data"><input type="text" name="name" id="name" size=50 maxlength=60 value="<?= $ts['name'] ?>" readonly="1"><br></td>
  </tr>
  <tr>
  		<tr>
  			<td>&nbsp;</td>
  		</tr>
		
  		<tr>
		
     	<table border="0" cellpadding="0" cellspacing="0" width="80%">
	  		<tr>
				<td valign="top" colspan="4" class="adm_item" align="center"><strong>HOSPITAL INSURANCE BENEFIT SCHEDULE</strong></td>
			</tr>
			<tr>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<td valign="top" colspan="2" class="vi_data">
				<!--<td valign="top" class="vi_data">-->
					<!--<select name="benefit_id" id="benefit_id" onChange="BenefitUnload();BenefitLoad(1);">-->
					<select name="benefit_id" id="benefit_id" onChange="BenefitUnload(); BenefitDisable(); uncheckBasis(); getbenefit(this.value);">
					<option value=0>-- Select an option --</option>
					<?php
						$all_benefits = &$ins_obj->getAllBenefits();	
							if(is_object($all_benefits)){
								while($result=$all_benefits->FetchRow()){
									echo "<option value=\"".$result['benefit_id']."\">".$result['benefit_desc']." \n";
									/*
									if ($result['benefit_desc']==$benefit_id){
										echo "<option value=\"".$result['benefit_id']."\" selected>".$result['benefit_desc']." \n";
                           }else{
                              echo "<option value=\"".$result['benefit_id']."\">".$result['benefit_desc']." \n";
                           }
									*/
                        }
						    }
					?>
			   	</select> 
				</td>
				<td align="right" class="vi_data">Date of Effectivity :&nbsp;</td>
				<td>
					<input type="text" id="effectvty_dte" name="effectvty_dte" value="<?=$effectvty_dte;?>" size="10">
					<img <?= createComIcon($root_path,'show-calendar.gif','0') ?> id="effectvty_dte_trigger" align="absmiddle" style="cursor:pointer">
					&nbsp;&nbsp;mm/dd/yyyy
					
					<script type="text/javascript">
						Calendar.setup ({
								inputField : "effectvty_dte", ifFormat : "<?=$phpfd?>", showsTime : false, button : "effectvty_dte_trigger", singleClick : true, step : 1
							});
					</script>	
				</td>
			</tr>
			<tr>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<td valign="top" colspan="4" class="reg_item">BASIS:</td>
			</tr>
			<tr>
				<td valign="top" class="vi_data" width="25%">
					<input type="checkbox" name="basis_check" id="basis_check" value="rvu" onClick="get_check_value_rvu();">&nbsp;&nbsp;<strong>RVU</strong>
				</td>
				<td valign="top" class="vi_data" width="25%">
					<input type="checkbox" name="basis_check" id="basis_check" value="room" onClick="get_check_value_room();">&nbsp;&nbsp;<strong>Room Type</strong>
				</td>
				<td valign="top" class="vi_data" width="25%">
					<input type="checkbox" name="basis_check" id="basis_check" value="conf" onClick="get_check_value_conf();">&nbsp;&nbsp;<strong>Confinement</strong>
				</td>
				<td valign="top" class="vi_data" width="25%">
					<input type="checkbox" name="basis_check" id="basis_check" value="item" onClick="get_check_value_item();">&nbsp;&nbsp;<strong>Per Item</strong>
				</td>
				<!--
				<td valign="top" class="vi_data">
					<input type="checkbox" name="rvu_check" id="rvu_check" value="1" onClick="checkBasis();">&nbsp;&nbsp;<strong>RVU</strong>
				</td>
				<td valign="top" class="vi_data">
					<input type="checkbox" name="room_check" id="room_check" value="1" onClick="checkBasis();">&nbsp;&nbsp;<strong>Room Type</strong>
				</td>
				<td valign="top" class="vi_data">
					<input type="checkbox" name="conf_check" id="conf_check" value="1" onClick="checkBasis();">&nbsp;&nbsp;<strong>Confinement</strong>
				</td>
				<td valign="top" class="vi_data">
					<input type="checkbox" name="item_check" id="item_check" value="1" onClick="checkBasis();">&nbsp;&nbsp;<strong>Per Item</strong>
				</td>
				-->
			</tr>
			<tr>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<td colspan="4" class="vi_data">
				  <div id="rlistContainer" dojoType="TabContainer" style="width:100%; height:20em;" align="center">
					<div dojoType="ContentPane" widgetId="tab0" label="By RVU Range" style="display:none;overflow:auto">
					  <center>
						<table id="rvurange" bgcolor="#ffffee" class="segList" border="0" cellpadding="0" cellspacing="0" width="80%">
							<tr><td colspan="2">&nbsp;</td></tr>
							<tr>
								<td width="25%">Range Start <strong>*</strong></td>
								<td width="75%"><input type="text" name="range_start" id="range_start" value="<?=$rsRVU['range_start'];?>" style="text-align:right" onBlur="loadRVUBenefit(this.value);"></td>
							</tr>
							<tr>
								<td width="25%">Range End <strong>*</strong></td>
								<td width="75%"><input type="text" name="range_end" id="range_end" value="<?=$rsRVU['range_end'];?>" style="text-align:right"></td>
							</tr>
							<tr>
								<td width="25%">Amount Limit</td>                                                    
								<td width="75%"><input type="text" name="rvu_amtlimit" id="rvu_amtlimit" value="<?=number_format($rsRVU['amountlimit'],2,".","");?>" onBlur="formatAmount(this);" style="text-align:right"></td>
							</tr>
							<tr>
								<td width="25%">Rate Per RVU </td>
								<td width="75%"><input type="text" name="rvu_rate" id="rvu_rate" value="<?=number_format($rsRVU['rateperRVU'],2,".","");?>" onBlur="formatAmount(this);" style="text-align:right"></td>
							</tr>
						</table>
						</center>
					</div>
					<div dojoType="ContentPane" widgetId="tab1" label="By Room Type" style="display:none;overflow:auto">
						<center>
						<table id="roomtype" bgcolor="#ffffee" class="segList" border="0" cellpadding="0" cellspacing="0" width="80%">
							<tr><td colspan="2">&nbsp;</td></tr>
							<tr>
								<td width="40%">Room Type</td>
								<td width="60%">
									<select id="room_type" name="room_type" onChange="UnSetRoomType(0); loadRoomTypeBenefit(this.value);">
										<option value=0>-- Select an option --</option>
										<?php
											$all_rooms = &$ins_obj->getAllRooms();	
											if(is_object($all_rooms)){
												while($result=$all_rooms->FetchRow()){
													#echo "<option value=\"".$result['nr']."\">".$result['name']." \n";
													if ($result['nr']==$rsRoom['roomtype_nr']){
														echo "<option value=\"".$result['nr']."\" selected>".$result['name']." \n";
				                           }else{
				                              echo "<option value=\"".$result['nr']."\">".$result['name']." \n";
            					            }
            				            }
						    				}
										?>
									</select>
								</td>
							</tr>
							<tr>
								<td width="40%">Rate Per Day</td>
								<td width="60%"><input type="text" name="rt_rate" id="rt_rate" value="<?=number_format($rsRoom['rateperday'],2,".","");?>" onBlur="formatAmount(this);" style="text-align:right"></td>
							</tr>
							<tr>
								<td width="40%">Amount Limit</td>
								<td width="60%"><input type="text" name="rt_amtlimit" id="rt_amtlimit" value="<?=number_format($rsRoom['amountlimit'],2,".","");?>" onBlur="formatAmount(this);" style="text-align:right"></td>
							</tr>
							<tr>
								<td width="40%">Days Limit </td>
								<td width="60%"><input type="text" name="rt_dayslimit" id="rt_dayslimit" value="<?=$rsRoom['dayslimit'];?>" style="text-align:right"></td>
							</tr>
							<tr>
								<td width="40%">Rate per RVU</td>
								<td width="60%"><input type="text" name="rt_rateperRVU" id="rt_rateperRVU" value="<?=number_format($rsRoom['rateperRVU'],2,".","");?>" onBlur="formatAmount(this);" style="text-align:right"></td>
							</tr>
							<tr>
								<td width="40%">Year Days Limit (Principal)</td>
								<td width="60%"><input type="text" name="rt_yrslimit_prin" id="rt_yrslimit_prin" value="<?=$rsRoom['year_dayslimit'];?>" style="text-align:right"></td>
							</tr>
							<tr>
								<td width="40%">Year Days Limit (Beneficiary)</td>
								<td width="60%"><input type="text" name="rt_yrslimit_ben" id="rt_yrslimit_ben" value="<?=$rsRoom['year_dayslimit_alldeps'];?>" style="text-align:right"></td>
							</tr>
						</table>
						</center>
					</div>
					<div dojoType="ContentPane" widgetId="tab2" label="By Confinement Type" style="display:none;overflow:auto">
						<center>
						<table id="conftype" bgcolor="#ffffee" class="segList" border="0" cellpadding="0" cellspacing="0" width="80%">
							<tr><td colspan="2">&nbsp;</td></tr>
							<tr>
								<td width="40%">Confinement Type</td>
								<td width="60%">
									<select id="conf_type" name="conf_type" onChange="UnSetConfinement(0); loadConfinementBenefit(this.value);">
										<option value=0>-- Select an option --</option>
										<?php
											$all_conf = &$ins_obj->getAllConfinement();	
											if(is_object($all_conf)){
												while($result=$all_conf->FetchRow()){
													#echo "<option value=\"".$result['confinetype_id']."\">".$result['confinetypedesc']." \n";
													if ($result['confinetype_id']==$rsConf['confinetype_id']){
														echo "<option value=\"".$result['confinetype_id']."\" selected>".$result['confinetypedesc']." \n";
				                           }else{
				                              echo "<option value=\"".$result['confinetype_id']."\">".$result['confinetypedesc']." \n";
            					            }
            				            }
						    				}
										?>
									</select>
								</td>
							</tr>
							<tr>
								<td width="40%" id="ct_label">Rate Per Day</td>
								<td width="60%"><input type="text" name="ct_rate" id="ct_rate" value="<?=number_format($rsConf['rateperday'],2,".","");?>" onBlur="formatAmount(this);" style="text-align:right"> <label id="ct_label2"></label></td>
							</tr>
							<tr>
								<td width="40%">Amount Limit</td>
								<td width="60%"><input type="text" name="ct_amtlimit" id="ct_amtlimit" value="<?=number_format($rsConf['amountlimit'],2,".","");?>" onBlur="formatAmount(this);" style="text-align:right"></td>
							</tr>
							<tr>
								<td width="40%">Days Limit </td>
								<td width="60%"><input type="text" name="ct_dayslimit" id="ct_dayslimit" value="<?=$rsConf['dayslimit'];?>" style="text-align:right"></td>
							</tr>
							<tr>
								<td width="40%">Rate per RVU</td>
								<td width="60%"><input type="text" name="ct_rateperRVU" id="ct_rateperRVU" value="<?=number_format($rsConf['rateperRVU'],2,".","");?>" onBlur="formatAmount(this);" style="text-align:right"></td>
							</tr>
							<tr>
								<td width="40%">Year Days Limit (Principal)</td>
								<td width="60%"><input type="text" name="ct_yrslimit_prin" id="ct_yrslimit_prin" value="<?=$rsConf['year_dayslimit'];?>" style="text-align:right"></td>
							</tr>
							<tr>
								<td width="40%">Year Days Limit (Beneficiary)</td>
								<td width="60%"><input type="text" name="ct_yrslimit_ben" id="ct_yrslimit_ben" value="<?=$rsConf['year_dayslimit_alldeps'];?>" style="text-align:right"></td>
							</tr>
						</table>
						</center>
					</div>
					<div dojoType="ContentPane" widgetId="tab3" label="Per Item" style="display:none;overflow:auto;height:auto">
						<center>
						
						<table id="peritem" bgcolor="#ffffee" class="segList" border="0" cellpadding="0" cellspacing="0" width="95%">
							<tr>
								<td colspan="2">
											<!--DRAGGABLE, -->
											<a href="javascript:void(0);" id="urladd" name="urladd" onclick="return overlib(
										       OLiframeContent('<?=$root_path;?>modules/insurance_co/seg-product-tray.php', 600, 375, 'fOrderTray', 1, 'auto'),
										       WIDTH,600, TEXTPADDING,0, BORDER,0, 
								   			 STICKY, SCROLL, CLOSECLICK, MODAL, 
								   			 CLOSETEXT, '<img src=\'<?=$root_path?>images/close.gif\' border=0 >',
                                     CAPTIONPADDING,4, 
                           			 CAPTION,'Add item in a Product tray',
        										 MIDX,0, MIDY,0, 
										       STATUS,'Add item in a Product tray');"
									          onmouseout="nd();">
											
									 	<img name="btnAdd" id="btnAdd" src="<?=$root_path?>images/btn_additems.gif" border=0 alt="Add Item" title="Add Product Item"></a>
								</td>
							</tr>
							
							<tr>
								<td colspan="2">
									<div style="overflow:auto;height:auto">
									<table id="product-list" class="segList" bgcolor="#ffffee" border="0" cellpadding="0" cellspacing="0" width="100%">
											<thead style="height:5">
												<tr id="product-list-header">
													<th width="4%" nowrap></th>
													<th width="15%" nowrap align="left">&nbsp;&nbsp;Item No.</th>
													<th width="*" nowrap align="left">&nbsp;&nbsp;Item Description</th>
													<th width="1%" align="left">&nbsp;&nbsp;&nbsp;Service</th>
													<th width="27%" align="center">Amount Limit / Max. RVU</th>
												</tr>
											</thead>
											<tbody>
												<?= $row_body;?>
												<?php 
													if ($_POST['isperitem']!=0){
														$rsbillarea = $ins_obj->getBenefitInfo($HTTP_POST_VARS['benefit_id']);
														if ($rsbillarea['bill_area'] == "MS")
															$item_type = "MS";
														elseif (($rsbillarea['bill_area'] == "HS")||($rsbillarea['bill_area'] == "OR"))
															$item_type = "HS";
				
														$ObjItem = $ins_obj->getProductBenefit($HTTP_POST_VARS['hcare_id'], $HTTP_POST_VARS['benefit_id'], $item_type);
			
														$rows=array();
														while ($row=$ObjItem->FetchRow()) {
															$rows[] = $row;
														}
		
														$dbtable='care_pharma_products_main';
														$labtable = 'seg_lab_services';
														$radiotable = 'seg_radio_services';
														$ORtable = 'care_ops301_en';
														$othertable = 'seg_otherhosp_services';
														
														foreach ($rows as $i=>$row) {
															if ($row) {
																$count++;
																$alt = ($count%2)+1;
	                                             if ($item_type=="MS"){
																	$sql = "SELECT p.bestellnum AS code, p.artikelname AS name, p.generic, p.description 
					        												  FROM $dbtable AS p
												                       WHERE p.bestellnum='".trim($row['code'])."'";
																	$res=$db->Execute($sql);
																	$rsrow=$res->RecordCount();
																	if ($rsrow!=0){
																		$rsProduct=$res->FetchRow();
																	}
																   echo "<SCRIPT type=\"text/javascript\">";
																 	echo "ajxSetMedItem('product-list','".trim($row['code'])."','".trim($rsProduct['name'])."','".trim($row['amountlimit'])."','DM');";
																 	echo "</SCRIPT>";			
				
																}elseif ($item_type=="HS"){
																	if ($row['provider']=='LB'){
																		$sql = "SELECT * FROM $labtable
																				  WHERE service_code ='".trim($row['code'])."'";
																		$res=$db->Execute($sql);
																		$rsrow=$res->RecordCount();
																		if ($rsrow!=0){
																			$rsProduct=$res->FetchRow();
																		}
																	}elseif ($row['provider']=='RD'){
																		$sql = "SELECT * FROM $radiotable
																				  WHERE service_code ='".trim($row['code'])."'";
																		$res=$db->Execute($sql);
																		$rsrow=$res->RecordCount();
																		if ($rsrow!=0){
																			$rsProduct=$res->FetchRow();
																		}
																	}elseif ($row['provider']=='OR'){
																		$sql = "SELECT description AS name FROM $ORtable
																				  WHERE code ='".trim($row['code'])."'";
																		$res=$db->Execute($sql);
																		$rsrow=$res->RecordCount();
																		if ($rsrow!=0){
																			$rsProduct=$res->FetchRow();
																		}
																	}elseif ($row['provider']=='OA'){
																		$sql = "SELECT * FROM $othertable
																				  WHERE service_code ='".trim($row['code'])."'";
																		$res=$db->Execute($sql);
																		$rsrow=$res->RecordCount();
																		if ($rsrow!=0){
																			$rsProduct=$res->FetchRow();
																		}
																	}
																	echo "<SCRIPT type=\"text/javascript\">";
															 		echo "ajxSetServiceItem('product-list','".trim($row['code'])."','".trim($rsProduct['name'])."','".trim($row['provider'])."','".trim($row['amountlimit'])."','".trim($row['maxRVU'])."');";
															 		echo "</SCRIPT>";			
																}	# end of elseif ($item_type=="HS")
															}	# end of if ($row)
														}	# end of foreach ($rows as $i=>$row)
													}	# end of if ($tab_selected3!=NULL)

												?>
											</tbody>
										</table>
									</div>
								</td>	
								
							</tr>	
						</table>
						
						</center>
					</div>
					  <tr>
					    <td><input type="image" <?php echo createLDImgSrc($root_path,'savedisc.gif','0'); ?>>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
					    	  <a href="<?php echo $breakfile ?>"><img <?php echo createLDImgSrc($root_path,'cancel.gif','0') ?> border="0"></a>	
						 </td>	
						 <td align="right"><!--<input type="image" <?php echo createLDImgSrc($root_path,'benefitsSched.gif','0'); ?> onClick="viewSummaryBenefitSked();">-->
						 	<a href="javascript:void(0);"><img <?php echo createLDImgSrc($root_path,'benefitsSched.gif','0') ?> border="0" onClick="viewSummaryBenefitSked();"></a>
						 </td> 
					  </tr>

				</div>	
				</td>
			</tr>
	  </table>
  </tr>
  <tr>
  		<td>&nbsp;</td>
  </tr>
 <!-- 
  <tr>
    <td><input type="image" <?php echo createLDImgSrc($root_path,'savedisc.gif','0'); ?>></td>
    <td  align=right><a href="<?php echo $breakfile ?>"><img <?php echo createLDImgSrc($root_path,'cancel.gif','0') ?> border="0"></a></td>
  </tr>
 --> 
</table>
<input type="hidden" name="sid" value="<?php echo $sid ?>">
<input type="hidden" name="mode" value="save">
<input type="hidden" name="lang" value="<?php echo $lang ?>">
<input type="hidden" name="retpath" value="<?php echo $retpath ?>">
<input type="hidden" name="benefit" id="benefit" value="<?= $benefit;?>">
<input type="hidden" name="area" id="area" value="<?= $area;?>">
<input type="hidden" name="basis" id="basis" value="<?=$basis;?>">
<input type="hidden" name="isrvu" id="isrvu" value="<?=$isrvu;?>">
<input type="hidden" name="isconf" id="isconf" value="<?=$isconf;?>">
<input type="hidden" name="isroomtyp" id="isroomtyp" value="<?=$isroomtyp;?>">
<input type="hidden" name="isperitem" id="isperitem" value="<?=$isperitem;?>">
<!--
<input type="text" name="tab_selected0" id="tab_selected0" value="<?=$tab_selected0;?>">
<input type="text" name="tab_selected1" id="tab_selected1" value="<?=$tab_selected1;?>">
<input type="text" name="tab_selected2" id="tab_selected2" value="<?=$tab_selected2;?>">
<input type="text" name="tab_selected3" id="tab_selected3" value="<?=$tab_selected3;?>">
-->
<!--<input type="text" name="row_body" id="row_body" value="<?=$row_body;?>" size="100">-->

<input type="hidden" name="mode" id="mode" value="<?php if($saveok) echo "update"; else echo "save"; ?>">
<input type="hidden" name="update" id="update" value="<?php if($saveok) echo "1"; else echo $update;?>">

</form>
<p>

</FONT>
</ul>

<?php

$sTemp = ob_get_contents();
ob_end_clean();

# Assign page output to the mainframe template

$smarty->assign('sMainFrameBlockData',$sTemp);
 /**
 * show Template
 */
 $smarty->display('common/mainframe.tpl');

?>
