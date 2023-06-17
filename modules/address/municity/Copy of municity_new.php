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
define('LANG_FILE','place.php');
$local_user='aufnahme_user';
require_once($root_path.'include/inc_front_chain_lang.php');
# Load the address object
require_once($root_path.'include/care_api_classes/class_address.php');

$address_prov=new Address('province');
#$address_prov->_useProvinces();
$prov_list = $address_prov->getAllAddress();

$address_municity=new Address('municity');
#$address_municity->_useMuniCity();

//$db->debug=1;
switch($retpath)
{
	case 'list': $breakfile='municity_list.php'.URL_APPEND; break;
	case 'search': $breakfile='municity_search.php'.URL_APPEND; break;
	default: $breakfile='municity_manage.php'.URL_APPEND; 
}

if(!isset($mode)){
	$mode='';
	$edit=true;		
}else{
	switch($mode)
	{
		case 'save':
		{
			#
			# Validate important data
			#
			$HTTP_POST_VARS['mun_name']=trim($HTTP_POST_VARS['mun_name']);
			$HTTP_POST_VARS['mun_name'] = $address_municity->stringTrim($HTTP_POST_VARS['mun_name']);   # burn added: August 25, 2006

			if(!empty($HTTP_POST_VARS['mun_name'])){
				#
				# Check if address exists
				#
				if($address_municity->addressExists(0,$HTTP_POST_VARS['mun_name'],FALSE,$HTTP_POST_VARS['prov_nr'])){
					#
					# Do notification
					#
					$mode='municity_exists';
				}else{
				    
					if($address_municity->saveAddressInfoFromArray($HTTP_POST_VARS)){
						#
						# Get the last insert ID
						#
						$insid=$db->Insert_ID();
						#
						# Resolve the ID to the primary key
						#
						$mun_nr=$address_municity->LastInsertPK('mun_nr',$insid);

						# Get the last insert 'mun_nr'
						# added burn: February 232, 2007
						$mun_nr=$address_municity->LastInsertPKAddress(); 
    					header("location:municity_info.php?sid=$sid&lang=$lang&mun_nr=$mun_nr&mode=show&save_ok=1&retpath=$retpath");
						exit;
					}else{echo "$sql<br>$LDDbNoSave";}
				}
			}else{
					$mode='bad_data';
			}
			break;
		}
	} // end of switch($mode)
}

# Start Smarty templating here
 /**
 * LOAD Smarty
 */
 # Note: it is advisable to load this after the inc_front_chain_lang.php so
 # that the smarty script can use the user configured template theme

 require_once($root_path.'gui/smarty_template/smarty_care.class.php');
 $smarty = new smarty_care('system_admin');

# Title in toolbar
 $smarty->assign('sToolbarTitle',"$segMuniCity :: $segNewMuniCity");

 # href for help button
 $smarty->assign('pbHelp',"javascript:gethelp('address_new.php')");

 # href for close button
 $smarty->assign('breakfile',$breakfile);

 # Window bar title
 $smarty->assign('sWindowTitle',"$segMuniCity :: $segNewMuniCity");

# Coller Javascript code

ob_start();
?>

<script language="javascript">
<!-- 
	function check(d){
		if((d.mun_name.value=="")){
			alert("<?php echo "$segAlertNoMuniCityName \\n $LDPlsEnterInfo"; ?>");
			d.mun_name.focus();
			return false;
		}
		if((d.zipcode.value=="")){
			alert("<?php echo "$segWrongZipCode \\n $LDEnterZero"; ?>");
			d.zipcode.focus();
			return false;
		}
		if(d.prov_nr.value=="0"){
			alert("<?php echo "$segAlertNoProvinceName \\n $LDPlsEnterInfo"; ?>");
			d.prov_nr.focus();
			return false;
		}
		return true;
	}/* end of function check */
		/*	
				This will trim the string i.e. no whitespaces in the
				beginning and end of a string AND only a single
				whitespace appears in between tokens/words 
				input: object
				output: object (string) value is trimmed
		*/
	function trimString(objct){
		objct.value = objct.value.replace(/^\s+|\s+$/g,"");
		objct.value = objct.value.replace(/\s+/g," "); 
	}/* end of function trimString */

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
    <td valign="bottom"><br><font class="warnprompt"><b>
<?php 
	switch($mode)
	{
		case 'bad_data':
		{
			echo $segAlertNoMuniCityName;
			break;
		}
		case 'municity_exists':
		{
			echo "$segMuniCityExists<br>$LDDataNoSave";
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
&nbsp;<br>

<form action="<?php echo $thisfile; ?>" method="post" name="municity" onSubmit="return check(this)">
<font face="Verdana, Arial" size=-1><?php echo $LDEnterAllFields ?>
<table border=0>
	<tr>
		<td align=right class="adm_item"><font color=#ff0000><b>*</b></font> <?php echo $segMuniCityName ?>: </td>
		<td class="adm_input">
	 		<input type="text" name="mun_name" size=50 maxlength=60 onBlur="trimString(this)" value="<?php echo $mun_name ?>"><br>
		</td>
	</tr> 
	<tr>
		<td align=right class="adm_item"><font color=#ff0000><b>*</b></font> <?php echo $LDZipCode ?>: </td>
		<td class="adm_input">
	 		<input type="text" name="zipcode" size=10 maxlength=6 onBlur="trimString(this)" value="<?php echo $zipcode ?>"><br>
		</td>
	</tr> 
	<tr>
		<td align=right class="adm_item"><font color=#ff0000><b>*</b></font> <?php echo $segProvinceName ?>: </td>
		<td class="adm_input">
			<select name="prov_nr" id="prov_nr">
				<option value="0">-Select Province-</option>
				<?php 
					while($addr=$prov_list->FetchRow()){
						$selected="";
						if ($prov_nr==$addr['prov_nr'])
							$selected="selected";
				?>
				<option value="<?= $addr['prov_nr']?>" <?= $selected ?> ><?= $addr['prov_name']?></option>				
				<?php 
					} # end of while loop
				?>
			</select>
		</td>
	</tr> 
	<tr>
		<td class=pblock>
			<input type="image" <?php echo createLDImgSrc($root_path,'savedisc.gif','0'); ?>>
		</td>
		<td align=right>
			<a href="<?php echo $breakfile ?>"><img <?php echo createLDImgSrc($root_path,'cancel.gif','0') ?> border="0"></a>
		</td>
	</tr>
</table>
<input type="hidden" name="sid" value="<?php echo $sid ?>">
<input type="hidden" name="mode" value="save">
<input type="hidden" name="lang" value="<?php echo $lang ?>">
<input type="hidden" name="retpath" value="<?php echo $retpath ?>">
</form>

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
