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
# Load the insurance object
require_once($root_path.'include/care_api_classes/class_address.php');

$address_prov=new Address('province');
#$address_prov->_useProvinces();
$prov_list = $address_prov->getAllAddress();

$address_municity=new Address('municity');
#$address_municity->_useMuniCity();

switch($retpath)
{
	case 'list': $breakfile='municity_list.php'.URL_APPEND; break;
	case 'search': $breakfile='municity_search.php'.URL_APPEND; break;
	default: $breakfile='municity_manage.php'.URL_APPEND; 
}

if(isset($mun_nr)&&$mun_nr){
	if(isset($mode)&&$mode=='update'){
			#
			# Check if address exists
			#
        //added by jasper 02/13/13
        $pr_code_temp = $address_municity->getCodebyNr($HTTP_POST_VARS['prov_nr']);
        $pr_code = $pr_code_temp->FetchRow();
        $municty_code = substr($pr_code['code'],0,4) . $HTTP_POST_VARS['code'];
        $HTTP_POST_VARS['code'] = $municty_code;
        //added by jasper 02/13/13
		if($address_municity->addressExists($mun_nr,$HTTP_POST_VARS['mun_name'],TRUE,$HTTP_POST_VARS['prov_nr'])){
				#
				# Do notification
				#
			$mode='municity_exists';
			/*
		} elseif ($address_municity->zipcodeExists($mun_nr,$HTTP_POST_VARS['zipcode'])){
				#
				# Do notification
				#
			$mode='zipcode_exists'; */
        }elseif ($address_municity->CodeExists($HTTP_POST_VARS['code'],TRUE, $mun_nr)) {
                    $mode='code_exists';
		} else {
			if($address_municity->updateAddressInfoFromArray($mun_nr,$HTTP_POST_VARS)){
				header("location:municity_info.php?sid=$sid&lang=$lang&mun_nr=$mun_nr&mode=show&save_ok=1&retpath=$retpath");
				exit;
			}else{
				echo $address_municity->getLastQuery();
				$mode='bad_data';
			}
		}
	}elseif($row=$address_municity->getAddressInfo($mun_nr)){
		if(is_object($row)){
			$address=$row->FetchRow();
			# Globalize the array values
			extract($address);
		}
	}
}else{
	// Redirect to search function
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
 $smarty->assign('sToolbarTitle',"$segMuniCity :: $LDUpdateData");

 # href for help button
 $smarty->assign('pbHelp',"javascript:gethelp('address_update.php')");

 # href for close button
 $smarty->assign('breakfile',$breakfile);

 # Window bar title
 $smarty->assign('sWindowTitle',"$segMuniCity :: $LDUpdateData");

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
			break;
		}
        case 'code_exists';
        {
            echo "Municipality/City's code already exist." . "<br>$LDDataNoSave";
            break;
        }
		/*
		case 'zipcode_exists':
		{
			echo "$segZipCodeExists<br>$LDDataNoSave";
			break;
		}
		*/
	}
?>
	</b></font><p>
</td>
  </tr>
</table>
<?php 
} 
?>
<script language="javascript">
<!--
	function check(d){
		var pattern=/^\d+$/;
		var str_zip=d.zipcode.value;

        var municity_code = d.codetmp.value;
        if ((municity_code =="")){
            alert("<?php echo "Municipality/City's code is missing \\n $LDPlsEnterInfo"; ?>");
            d.codetmp.focus();
            return false;
        }else if (municity_code=="00") {
            alert("<?php echo "Municipality/City's code should not be equal to zero"; ?>");
            d.codetmp.focus();
            return false;
        }else if (municity_code.length!==2) {
            alert("<?php echo "Municipality/City's code should be 2 characters"; ?>");
            d.codetmp.focus();
            return false;
        }
        //edited by jasper 01/30/13

		if((d.mun_name.value=="")){
			alert("<?php echo "$segAlertNoMuniCityName \\n $LDPlsEnterInfo"; ?>");
			d.mun_name.focus();
			return false;
		}
		if((d.zipcode.value=="") || !(str_zip.match(pattern))){
			alert("<?php echo "$segWrongZipCode"; ?>");
			d.zipcode.focus();
			return false;
		}
		if(str_zip.length < 4){
			alert("<?php echo "$segWrongZipCodeLength"; ?>");
			d.zipcode.focus();
			return false;
		}
		if(d.prov_nr.value=="0"){
			alert("<?php echo "$segAlertNoProvinceName \\n $LDPlsEnterInfo"; ?>");
			d.prov_nr.focus();
			return false;
		}
        d.code.value = municity_code + "000";
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

<form action="<?php echo $thisfile; ?>" method="post" name="municity"  onSubmit="return check(this)">
<table border=0>
	<tr>
        <td align=right class="adm_item"><font color=#ff0000><b>*</b></font> Municipality/City Code: </td>
        <td class="adm_input">
             <input type="text" name="codetmp" size=50 maxlength=50 onBlur="trimString(this)" value="<?php echo substr($code,-5,2); ?>"><br>
        </td>
    </tr>
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
						$selected='';
						if ($addr['prov_nr']==$prov_nr)
							$selected='selected';
				?>
				<option value="<?= $addr['prov_nr']?>" <?= $selected ?>><?= $addr['prov_name']?></option>				
				<?php 
					} # end of while loop
				?>
			</select>
		</td>
	</tr> 
	<tr>
		<td><input type="image" <?php echo createLDImgSrc($root_path,'savedisc.gif','0'); ?>></td>
		<td  align=right><a href="<?php echo $breakfile;?>"><img <?php echo createLDImgSrc($root_path,'cancel.gif','0') ?>></a></td>
	</tr>
</table>
<input type="hidden" name="sid" value="<?php echo $sid ?>">
<input type="hidden" name="mode" value="update">
<input type="hidden" name="lang" value="<?php echo $lang ?>">
<input type="hidden" name="mun_nr_old" value="<?php echo $mun_nr ?>">
<!--
<input type="hidden" name="mun_name" value="<?php echo $mun_name ?>">
-->
<input type="hidden" name="retpath" value="<?php echo $retpath ?>">
<!-- added by jasper 02/13/13 -->
<input type="hidden" name="code" id="code" value="<?php echo $code ?>">
</form>
<p>

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
