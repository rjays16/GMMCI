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
$address_obj=new Address('region');
#$address_obj->_useRegions();
switch($retpath)
{
	case 'list': $breakfile='region_list.php'.URL_APPEND; break;
	case 'search': $breakfile='region_search.php'.URL_APPEND; break;
	default: $breakfile='region_manage.php'.URL_APPEND;
}

if(isset($region_nr)&&$region_nr){
	if(isset($mode)&&$mode=='update'){
			#
			# Check if address exists
			#
        if($address_obj->addressExists($region_nr,$HTTP_POST_VARS['region_name'],TRUE)){
				#
				# Do notification
				#
			$mode='region_exists';
        //}elseif (!($HTTP_POST_VARS['code']==$code)) {
        }elseif ($address_obj->CodeExists($HTTP_POST_VARS['code'], true, $region_nr)){
            //echo ($address_obj->sql);
            $mode='code_exists';
		}else{
			if($address_obj->updateAddressInfoFromArray($region_nr,$HTTP_POST_VARS)){
				header("location:region_info.php?sid=$sid&lang=$lang&region_nr=$region_nr&mode=show&save_ok=1&retpath=$retpath");
				exit;
			}else{
				echo $address_obj->getLastQuery();
				$mode='bad_data';
			}
		}
	}elseif($row=$address_obj->getAddressInfo($region_nr)){
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
 $smarty->assign('sToolbarTitle',"$segRegion :: $LDUpdateData");

 # href for help button
 $smarty->assign('pbHelp',"javascript:gethelp('address_update.php')");

 # href for close button
 $smarty->assign('breakfile',$breakfile);

 # Window bar title
 $smarty->assign('sWindowTitle',"$segRegion :: $LDUpdateData");

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
			echo $segAlertNoRegionName;
			break;
		}
		case 'region_exists':
		{
			echo "$segRegionShortNameExists<br>$LDDataNoSave";
		}
        case 'code_exists';
        {
            echo "Region code already exist." . "<br>$LDDataNoSave";
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
<script language="javascript">
<!--
	function check(d){
        //edited by jasper 01/31/13
        //alert(d.code.value + " " + d.codetmp.value + " " + d.region_nr.value + " " + d.mode.value);
		var region_code = d.codetmp.value;
        if ((region_code=="")){
            alert("<?php echo "The region's code is missing \\n $LDPlsEnterInfo"; ?>");
            d.codetmp.focus();
            return false;
        }else if (region_code.length!==2) {
            alert("<?php echo "Region code should be 2 characters"; ?>");
            d.codetmp.focus();
            return false;
        }
        //edited by jasper 01/31/13
        if((d.region_name.value=="")){
            alert("<?php echo "$segAlertNoRegionShortName \\n $LDPlsEnterInfo"; ?>");
            d.region_name.focus();
            return false;
        }
		if((d.region_desc.value=="")){
			alert("<?php echo "$segAlertNoRegionName \\n $LDPlsEnterInfo"; ?>");
			d.region_desc.focus();
			return false;
		}
        d.code.value = d.codetmp.value + "0000000";
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

<form action="<?php echo $thisfile; ?>" method="post" name="region"  onSubmit="return check(this)">
<table border=0>
    <tr>
        <td align=right class="adm_item"><font color=#ff0000><b>*</b></font> Region Code: </td>
        <td class="adm_input">
             <input type="text" name="codetmp" size=50 maxlength=50 onBlur="trimString(this)" value="<?php echo substr($code,0,2) ?>"><br>
        </td>
    </tr>
	<tr>
		<td align=right class="adm_item"><font color=#ff0000><b>*</b></font> <?php echo $segRegionShortName ?>: </td>
		<td class="adm_input">
	 		<input type="text" name="region_name" size=50 maxlength=60 onBlur="trimString(this)" value="<?php echo $region_name ?>"><br>
		</td>
	</tr>
	<tr>
		<td align=right class="adm_item"><font color=#ff0000><b>*</b></font> <?php echo $segRegionName ?>: </td>
		<td class="adm_input">
	 		<input type="text" name="region_desc" size=50 maxlength=60 onBlur="trimString(this)" value="<?php echo $region_desc ?>"><br>
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
<input type="hidden" name="region_nr" value="<?php echo $region_nr ?>">
<!--
<input type="hidden" name="region_name" value="<?php echo $region_name ?>">
-->
<input type="hidden" name="retpath" value="<?php echo $retpath ?>">
<!-- added by jasper 10/31/13 -->
<input type="hidden" name="code" value="<?php echo $code ?>">
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
