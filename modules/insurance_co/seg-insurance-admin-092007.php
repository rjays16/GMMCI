<?php
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
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
/*
if(!isset($mode)){
	$mode='';
	$edit=true;		
}else{
	switch($mode)
	{
	
		case 'save':
		{
			# Validate important data 
			$HTTP_POST_VARS['firm_id']=trim($HTTP_POST_VARS['firm_id']);
			$HTTP_POST_VARS['name']=trim($HTTP_POST_VARS['name']);
			if(!(empty($HTTP_POST_VARS['firm_id'])||empty($HTTP_POST_VARS['name']))){
				
				# Check if insurance ID exists
				if($ins_obj->FirmIDExists($HTTP_POST_VARS['firm_id'])){

				# Notify
					$mode='firm_exists';
				
				}else{
					if($ins_obj->saveFirmInfoFromArray($HTTP_POST_VARS)){
    					header("location:insurance_co_info.php?sid=$sid&lang=$lang&firm_id=$firm_id&mode=show&save_ok=1&retpath=$retpath");
						exit;
					}else{echo "$sql<br>$LDDbNoSave";}
				}
			}else{
					$mode='bad_data';
			}
			break;
		}
		
	} # end of switch($mode)
	
}
*/

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
 $smarty->assign('sToolbarTitle',"$LDInsuranceCo :: $LDNewData");

 # href for help button
 $smarty->assign('pbHelp',"javascript:gethelp('insurance_new.php','new')");

 # href for close button
 $smarty->assign('breakfile',$breakfile);

 # Window bar title
 $smarty->assign('sWindowTitle',"$LDInsuranceCo :: $LDNewData");

# Colllect javascript code

ob_start();

?>

<script language="javascript">
<!-- 
function check(d)
{
/*
	if((d.firm_id.value=="")){
		alert("<?php echo "$LDAlertFirmID \\n $LDPlsEnterInfo"; ?>");
		d.firm_id.select();
		return false;
	}else if((d.name.value=="")){
		alert("<?php echo "$LDAlertFirmName \\n $LDPlsEnterInfo"; ?>");
		d.name.select();
		return false;
	}else if((d.addr_mail.value=="")){
		alert("<?php echo "$LDAlertMailingAddress \\n $LDPlsEnterInfo"; ?>");
		d.addr_mail.select();
		return false;
	}else if((d.addr_billing.value=="")){
		alert("<?php echo "$LDAlertBillingAddress \\n $LDPlsEnterInfo"; ?>");
		d.addr_billing.select();
		return false;
	}else{
		return true;
	}
*/	
}
// -->
</script>

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
<form action="<?php echo $thisfile; ?>" method="post" name="insurance_co" onSubmit="return check(this)">
<font face="Verdana, Arial" size=-1><?php echo $LDEnterAllFields ?>
<table border=0>
  <tr>
    <td align=right class="reg_item"><font color=#ff0000><b>*</b></font><?php echo $LDInsuranceCoID ?>: </td>
    <td bgcolor="#ffffee" class="vi_data"><input type="text" name="firm_id" id="firm_id" size=50 maxlength=60 value="<?= $ts['firm_id'] ?>" readonly="1"><input type="hidden" name="hcareid" id="hcareid" size=2 maxlength=5 value="<?= $hcare_id ?>"><br></td>
  </tr> 
  <tr>
    <td align=right class="reg_item"><font color=#ff0000><b>*</b></font><?php echo $LDInsuranceCoName ?>: </td>
    <td bgcolor="#ffffee" class="vi_data"><input type="text" name="name" id="name" size=50 maxlength=60 value="<?= $ts['name'] ?>" readonly="1"><br></td>
  </tr>
  <tr>
  		<tr>
  			<td>&nbsp;</td>
  		</tr>
		<!--
  		<tr>
  			<td class="submenu_title">
				<select name="hosp_type" id="hosp_type">
					<?php
               	$all_hosp_type = &$ins_obj->getAllHosp_Type();	
							if(is_object($all_hosp_type)){
								while($result=$all_hosp_type->FetchRow()){
									if ($result['hosp_type']==$rs_hospital['hosp_type']){
										echo "<option value=\"".$result['hosp_type']."\" selected>".$result['hosp_desc']." \n";
                            }else{
                              echo "<option value=\"".$result['hosp_type']."\">".$result['hosp_desc']." \n";
                            }
							    }
						    }
					?>
			   </select> 
			</td>
  		</tr>
		-->
  		<tr>
     	<table border="0" cellpadding="0" cellspacing="0">
	  		<tr>
				<td valign="top" colspan="4" class="adm_item"><strong>HOSPITAL INSURANCE BENEFITS</strong></td>
			</tr>
			<tr>
  				<td class="reg_item" valign="top">&nbsp;</td>
    			<td class="reg_item" valign="top" align="center">Ordinary Case</td>
				<td class="reg_item" valign="top" align="center">Intensive Case</td>
				<td class="reg_item" valign="top" align="center">Catastrophic Case</td>
			</tr>	
			<tr>
		   	<td class="reg_item" valign="top" colspan="4">Room and Board</td> 
			</tr>
			<tr>
				<td bgcolor="#ffffee" class="vi_data" valign="top">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Room Type</td> 
				<td bgcolor="#ffffee" class="vi_data" valign="top"><input type="text" name="rb_ord_roomtype" id="rb_ord_roomtype" size="10" maxlength="10" value=""></td> 
				<td bgcolor="#ffffee" class="vi_data" valign="top"><input type="text" name="rb_int_roomtype" id="rb_int_roomtype" size="10" maxlength="10" value=""></td> 
				<td bgcolor="#ffffee" class="vi_data" valign="top"><input type="text" name="rb_roomtype" id="rb_cat_roomtype" size="10" maxlength="10" value=""></td> 
			</tr>
			<tr>
				<td bgcolor="#ffffee" class="vi_data" valign="top">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Rate per Day</td> 
				<td bgcolor="#ffffee" class="vi_data" valign="top"><input type="text" name="rb_ord_rate" id="rb_ord_rate" size="10" maxlength="10" value=""></td> 
				<td bgcolor="#ffffee" class="vi_data" valign="top"><input type="text" name="rb_int_rate" id="rb_int_rate" size="10" maxlength="10" value=""></td> 
				<td bgcolor="#ffffee" class="vi_data" valign="top"><input type="text" name="rb_cat_rate" id="rb_cat_rate" size="10" maxlength="10" value=""></td> 
			</tr>
			<tr>
				<td bgcolor="#ffffee" class="vi_data" valign="top">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Amount Limit</td> 
				<td bgcolor="#ffffee" class="vi_data" valign="top"><input type="text" name="rb_ord_amtlimit" id="rb_ord_amtlimit" size="10" maxlength="10" value=""></td> 
				<td bgcolor="#ffffee" class="vi_data" valign="top"><input type="text" name="rb_int_amtlimit" id="rb_int_amtlimit" size="10" maxlength="10" value=""></td> 
				<td bgcolor="#ffffee" class="vi_data" valign="top"><input type="text" name="rb_cat_amtlimit" id="rb_cat_amtlimit" size="10" maxlength="10" value=""></td> 
			</tr>
			<tr>
				<td bgcolor="#ffffee" class="vi_data" valign="top">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Days Limit</td> 
				<td bgcolor="#ffffee" class="vi_data" valign="top"><input type="text" name="rb_ord_daylimit" id="rb_ord_daylimit" size="10" maxlength="10" value=""></td> 
				<td bgcolor="#ffffee" class="vi_data" valign="top"><input type="text" name="rb_int_daylimit" id="rb_int_daylimit" size="10" maxlength="10" value=""></td> 
				<td bgcolor="#ffffee" class="vi_data" valign="top"><input type="text" name="rb_cat_daylimit" id="rb_cat_daylimit" size="10" maxlength="10" value=""></td> 
			</tr>
			<tr>
				<td bgcolor="#ffffee" class="vi_data" valign="top">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Yeardays Limit (Principal)</td> 
				<td bgcolor="#ffffee" class="vi_data" valign="top"><input type="text" name="rb_ord_yrlimit" id="rb_ord_yrlimit" size="10" maxlength="10" value=""></td> 
				<td bgcolor="#ffffee" class="vi_data" valign="top"><input type="text" name="rb_int_yrlimit" id="rb_int_yrlimit" size="10" maxlength="10" value=""></td> 
				<td bgcolor="#ffffee" class="vi_data" valign="top"><input type="text" name="rb_cat_yrlimit" id="rb_cat_yrlimit" size="10" maxlength="10" value=""></td> 
			</tr>
			<tr>
				<td bgcolor="#ffffee" class="vi_data" valign="top">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Yeardays Limit (Beneficiary)</td> 
				<td bgcolor="#ffffee" class="vi_data" valign="top"><input type="text" name="rb_ord_yrlimit2" id="rb_ord_yrlimit2" size="10" maxlength="10" value=""></td> 
				<td bgcolor="#ffffee" class="vi_data" valign="top"><input type="text" name="rb_int_yrlimit2" id="rb_int_yrlimit2" size="10" maxlength="10" value=""></td> 
				<td bgcolor="#ffffee" class="vi_data" valign="top"><input type="text" name="rb_cat_yrlimit2" id="rb_cat_yrlimit2" size="10" maxlength="10" value=""></td> 
			</tr>
			<tr>
		   	<td class="reg_item" valign="top" colspan="4">Drugs and Medicines</td> 
			</tr>
			<tr>
				<td bgcolor="#ffffee" class="vi_data" valign="top">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Products</td> 
				<td bgcolor="#ffffee" class="vi_data" valign="top"><input type="text" name="dm_ord_prod" id="dm_ord_prod" size="10" maxlength="10" value=""></td> 
				<td bgcolor="#ffffee" class="vi_data" valign="top"><input type="text" name="dm_int_prod" id="dm_int_prod" size="10" maxlength="10" value=""></td> 
				<td bgcolor="#ffffee" class="vi_data" valign="top"><input type="text" name="dm_cat_prod" id="dm_cat_prod" size="10" maxlength="10" value=""></td> 
			</tr>
			<tr>
				<td bgcolor="#ffffee" class="vi_data" valign="top">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Amount Limit</td> 
				<td bgcolor="#ffffee" class="vi_data" valign="top"><input type="text" name="dm_ord_amtlimit" id="dm_ord_amtlimit" size="10" maxlength="10" value=""></td> 
				<td bgcolor="#ffffee" class="vi_data" valign="top"><input type="text" name="dm_int_amtlimit" id="dm_int_amtlimit" size="10" maxlength="10" value=""></td> 
				<td bgcolor="#ffffee" class="vi_data" valign="top"><input type="text" name="dm_cat_amtlimit" id="dm_cat_amtlimit" size="10" maxlength="10" value=""></td> 
			</tr>
			<tr>
			   <td class="reg_item" valign="top" colspan="4">Xray, Lab and Others</td> 
			</tr>
			<tr>
				<td bgcolor="#ffffee" class="vi_data" valign="top">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Services</td> 
				<td bgcolor="#ffffee" class="vi_data" valign="top"><input type="text" name="sv_ord_serv" id="sv_ord_serv" size="10" maxlength="10" value=""></td> 
				<td bgcolor="#ffffee" class="vi_data" valign="top"><input type="text" name="sv_int_serv" id="sv_int_serv" size="10" maxlength="10" value=""></td> 
				<td bgcolor="#ffffee" class="vi_data" valign="top"><input type="text" name="sv_cat_serv" id="sv_cat_serv" size="10" maxlength="10" value=""></td> 
			</tr>
			<tr>
				<td bgcolor="#ffffee" class="vi_data" valign="top">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Amount Limit</td> 
				<td bgcolor="#ffffee" class="vi_data" valign="top"><input type="text" name="sv_ord_amtlimit" id="sv_ord_amtlimit" size="10" maxlength="10" value=""></td> 
				<td bgcolor="#ffffee" class="vi_data" valign="top"><input type="text" name="sv_int_amtlimit" id="sv_int_amtlimit" size="10" maxlength="10" value=""></td> 
				<td bgcolor="#ffffee" class="vi_data" valign="top"><input type="text" name="sv_cat_amtlimit" id="sv_cat_amtlimit" size="10" maxlength="10" value=""></td> 
			</tr>
			<tr>
		   	<td class="reg_item" valign="top" colspan="4">Professional Fees</td> 
			</tr>
			<tr>
		   	<td class="reg_item" valign="top" colspan="4">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;General Practitioner</td> 
			</tr>
			<tr>
				<td bgcolor="#ffffee" class="vi_data" valign="top">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Professional Fee (per day)</td> 
				<td bgcolor="#ffffee" class="vi_data" valign="top"><input type="text" name="df_ord_gp_rate" id="df_ord_gp_rate" size="10" maxlength="10" value=""></td> 
				<td bgcolor="#ffffee" class="vi_data" valign="top"><input type="text" name="df_int_gp_rate" id="df_int_gp_rate" size="10" maxlength="10" value=""></td> 
				<td bgcolor="#ffffee" class="vi_data" valign="top"><input type="text" name="df_cat_gp_rate" id="df_cat_gp_rate" size="10" maxlength="10" value=""></td> 
			</tr>
			<tr>
				<td bgcolor="#ffffee" class="vi_data" valign="top">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Amount Limit</td> 
				<td bgcolor="#ffffee" class="vi_data" valign="top"><input type="text" name="df_ord_gp_amtlimit" id="df_ord_gp_amtlimit" size="10" maxlength="10" value=""></td> 
				<td bgcolor="#ffffee" class="vi_data" valign="top"><input type="text" name="df_int_gp_amtlimit" id="df_int_gp_amtlimit" size="10" maxlength="10" value=""></td> 
				<td bgcolor="#ffffee" class="vi_data" valign="top"><input type="text" name="df_cat_gp_amtlimit" id="df_cat_gp_amtlimit" size="10" maxlength="10" value=""></td> 
			</tr>
			<tr>
		   	<td class="reg_item" valign="top" colspan="4">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Specialist</td> 
			</tr>
			<tr>
				<td bgcolor="#ffffee" class="vi_data" valign="top">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Professional Fee (per day)</td> 
				<td bgcolor="#ffffee" class="vi_data" valign="top"><input type="text" name="df_ord_sp_rate" id="df_ord_sp_rate" size="10" maxlength="10" value=""></td> 
				<td bgcolor="#ffffee" class="vi_data" valign="top"><input type="text" name="df_int_sp_rate" id="df_int_sp_rate" size="10" maxlength="10" value=""></td> 
				<td bgcolor="#ffffee" class="vi_data" valign="top"><input type="text" name="df_cat_sp_rate" id="df_cat_sp_rate" size="10" maxlength="10" value=""></td> 
			</tr>
			<tr>
				<td bgcolor="#ffffee" class="vi_data" valign="top">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Amount Limit</td> 
				<td bgcolor="#ffffee" class="vi_data" valign="top"><input type="text" name="df_ord_sp_amtlimit" id="df_ord_sp_amtlimit" size="10" maxlength="10" value=""></td> 
				<td bgcolor="#ffffee" class="vi_data" valign="top"><input type="text" name="df_int_sp_amtlimit" id="df_int_sp_amtlimit" size="10" maxlength="10" value=""></td> 
				<td bgcolor="#ffffee" class="vi_data" valign="top"><input type="text" name="df_cat_sp_amtlimit" id="df_cat_sp_amtlimit" size="10" maxlength="10" value=""></td> 
			</tr>
			<tr>
		   	<td class="reg_item" valign="top" colspan="4">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Surgeon</td> 
			</tr>
			<tr>
				<td bgcolor="#ffffee" class="vi_data" valign="top">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Professional Fee (per day)</td> 
				<td bgcolor="#ffffee" class="vi_data" valign="top"><input type="text" name="df_ord_sr_rate" id="df_ord_sr_rate" size="10" maxlength="10" value=""></td> 
				<td bgcolor="#ffffee" class="vi_data" valign="top"><input type="text" name="df_int_sr_rate" id="df_int_sr_rate" size="10" maxlength="10" value=""></td> 
				<td bgcolor="#ffffee" class="vi_data" valign="top"><input type="text" name="df_cat_sr_rate" id="df_cat_sr_rate" size="10" maxlength="10" value=""></td> 
			</tr>
			<tr>
				<td bgcolor="#ffffee" class="vi_data" valign="top">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Amount Limit</td> 
				<td bgcolor="#ffffee" class="vi_data" valign="top"><input type="text" name="df_ord_sr_amtlimit" id="df_ord_sr_amtlimit" size="10" maxlength="10" value=""></td> 
				<td bgcolor="#ffffee" class="vi_data" valign="top"><input type="text" name="df_int_sr_amtlimit" id="df_int_sr_amtlimit" size="10" maxlength="10" value=""></td> 
				<td bgcolor="#ffffee" class="vi_data" valign="top"><input type="text" name="df_cat_sr_amtlimit" id="df_cat_sr_amtlimit" size="10" maxlength="10" value=""></td> 
			</tr>
			<tr>
		   	<td class="reg_item" valign="top" colspan="4">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Anesthesiologist</td> 
			</tr>
			<tr>
				<td bgcolor="#ffffee" class="vi_data" valign="top">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Professional Fee (per day)</td> 
				<td bgcolor="#ffffee" class="vi_data" valign="top"><input type="text" name="df_ord_an_rate" id="df_ord_an_rate" size="10" maxlength="10" value=""></td> 
				<td bgcolor="#ffffee" class="vi_data" valign="top"><input type="text" name="df_int_an_rate" id="df_int_an_rate" size="10" maxlength="10" value=""></td> 
				<td bgcolor="#ffffee" class="vi_data" valign="top"><input type="text" name="df_cat_an_rate" id="df_cat_an_rate" size="10" maxlength="10" value=""></td> 
			</tr>
			<tr>
				<td bgcolor="#ffffee" class="vi_data" valign="top">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Amount Limit</td> 
				<td bgcolor="#ffffee" class="vi_data" valign="top"><input type="text" name="df_ord_an_amtlimit" id="df_ord_an_amtlimit" size="10" maxlength="10" value=""></td> 
				<td bgcolor="#ffffee" class="vi_data" valign="top"><input type="text" name="df_int_an_amtlimit" id="df_int_an_amtlimit" size="10" maxlength="10" value=""></td> 
				<td bgcolor="#ffffee" class="vi_data" valign="top"><input type="text" name="df_cat_an_amtlimit" id="df_cat_an_amtlimit" size="10" maxlength="10" value=""></td> 
			</tr>
			<tr>
				<td class="reg_item" valign="top" colspan="4">Operating Room</td> 
			 </tr>
			<tr>
				<td bgcolor="#ffffee" class="vi_data" valign="top">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Rate Per RVU</td> 
				<td bgcolor="#ffffee" class="vi_data" valign="top"><input type="text" name="or_ord_rate" id="or_ord_rate" size="10" maxlength="10" value=""></td> 
				<td bgcolor="#ffffee" class="vi_data" valign="top"><input type="text" name="or_int_rate" id="or_int_rate" size="10" maxlength="10" value=""></td> 
				<td bgcolor="#ffffee" class="vi_data" valign="top"><input type="text" name="or_cat_rate" id="or_cat_rate" size="10" maxlength="10" value=""></td> 
		   </tr>
			<tr>
				<td bgcolor="#ffffee" class="vi_data" valign="top">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;RVU Range Start</td> 
				<td bgcolor="#ffffee" class="vi_data" valign="top"><input type="text" name="or_ord_start" id="or_ord_start" size="10" maxlength="10" value=""></td> 
				<td bgcolor="#ffffee" class="vi_data" valign="top"><input type="text" name="or_int_start" id="or_int_start" size="10" maxlength="10" value=""></td> 
				<td bgcolor="#ffffee" class="vi_data" valign="top"><input type="text" name="or_cat_start" id="or_cat_start" size="10" maxlength="10" value=""></td> 
		   </tr>
			<tr>
				<td bgcolor="#ffffee" class="vi_data" valign="top">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;RVU Range End</td> 
				<td bgcolor="#ffffee" class="vi_data" valign="top"><input type="text" name="or_ord_end" id="or_ord_end" size="10" maxlength="10" value=""></td> 
				<td bgcolor="#ffffee" class="vi_data" valign="top"><input type="text" name="or_int_end" id="or_int_end" size="10" maxlength="10" value=""></td> 
				<td bgcolor="#ffffee" class="vi_data" valign="top"><input type="text" name="or_cat_end" id="or_cat_end" size="10" maxlength="10" value=""></td> 
		   </tr>
			<tr>
				<td bgcolor="#ffffee" class="vi_data" valign="top">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Amount Limit</td> 
				<td bgcolor="#ffffee" class="vi_data" valign="top"><input type="text" name="or_ord_amtlimit" id="or_ord_amtlimit" size="10" maxlength="10" value=""></td> 
				<td bgcolor="#ffffee" class="vi_data" valign="top"><input type="text" name="or_int_amtlimit" id="or_int_amtlimit" size="10" maxlength="10" value=""></td> 
				<td bgcolor="#ffffee" class="vi_data" valign="top"><input type="text" name="or_cat_amtlimit" id="or_cat_amtlimit" size="10" maxlength="10" value=""></td> 
		   </tr>
			
	  </table>
  </tr>
  <tr>
  		<td>&nbsp;</td>
  </tr>
  
  <tr>
    <td><input type="image" <?php echo createLDImgSrc($root_path,'savedisc.gif','0'); ?>></td>
    <td  align=right><a href="<?php echo $breakfile ?>"><img <?php echo createLDImgSrc($root_path,'cancel.gif','0') ?> border="0"></a></td>
  </tr>
</table>
<input type="hidden" name="sid" value="<?php echo $sid ?>">
<input type="hidden" name="mode" value="save">
<input type="hidden" name="lang" value="<?php echo $lang ?>">
<input type="hidden" name="retpath" value="<?php echo $retpath ?>">
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
