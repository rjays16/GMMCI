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

$address_region=new Address('region');
#$address_region->_useRegions();
$region_list = $address_region->getAllAddress();

$address_prov=new Address('province');
#$address_prov->_useProvinces();

//$db->debug=1;
switch($retpath)
{
	case 'list': $breakfile='province_list.php'.URL_APPEND; break;
	case 'search': $breakfile='province_search.php'.URL_APPEND; break;
	default: $breakfile='province_manage.php'.URL_APPEND; 
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
			$HTTP_POST_VARS['prov_name']=trim($HTTP_POST_VARS['prov_name']);
			$HTTP_POST_VARS['prov_name'] = $address_prov->stringTrim($HTTP_POST_VARS['prov_name']);   # burn added: August 25, 2006
			$HTTP_POST_VARS['code'] = $HTTP_POST_VARS['region_nr'].$HTTP_POST_VARS['codetmp']."00000";
			$HTTP_POST_VARS['prov_nr'] = $address_prov->getLatestPk(); //added by maimai 12-17-2014

			if(!empty($HTTP_POST_VARS['prov_name'])){
				#
				# Check if address exists
				#
				if($address_prov->addressExists(0,$HTTP_POST_VARS['prov_name'],FALSE,$HTTP_POST_VARS['region_nr'])){
					#
					# Do notification
					#
					$mode='province_exists';
                }elseif ($address_prov->CodeExists($HTTP_POST_VARS['code'])) {
                    $mode='code_exists';
				}else{
					if($address_prov->saveAddressInfoFromArray($HTTP_POST_VARS)){
						#
						# Get the last insert ID
						#
						$insid=$db->Insert_ID();
						#
						# Resolve the ID to the primary key
						#
						$prov_nr=$address_prov->LastInsertPK('prov_nr',$insid);

						# Get the last insert 'prov_nr'
						# added burn: February 232, 2007
						$prov_nr=$address_prov->LastInsertPKAddress(); 
    					header("location:province_info.php?sid=$sid&lang=$lang&prov_nr=$prov_nr&mode=show&save_ok=1&retpath=$retpath");
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
 $smarty->assign('sToolbarTitle',"$segProvince :: $segNewProvince");

 # href for help button
 $smarty->assign('pbHelp',"javascript:gethelp('address_new.php')");

 # href for close button
 $smarty->assign('breakfile',$breakfile);

 # Window bar title
 $smarty->assign('sWindowTitle',"$segProvince :: $segNewProvince");

# Coller Javascript code

ob_start();
?>

<script language="javascript">
<!-- 
	function check(d){
        var regcode_x = document.getElementById("region_nr").selectedIndex;
        var regcode_y = document.getElementById("region_nr").options;
        var regcode = regcode_y[regcode_x].text;
        document.getElementById("reg_code").value = regcode.substr(0,2);
        //alert(regcode.substr(0,2));
        //edited by jasper 01/30/13
        //alert (d.codetmp.value);
        var prov_code = d.codetmp.value;
        //alert(prov_code.substr(0,2) + "/" + regcode.substr(0,2));
        if ((prov_code =="")){
            alert('<?php echo "The province\'s code is missing \n $LDPlsEnterInfo"; ?>');
            d.codetmp.focus();
            return false;
        }else if (prov_code=="00") {
            alert("<?php echo 'Province code should not be equal to zero'; ?>");
            d.codetmp.focus();
            return false;
        }else if (prov_code.length!==2) {
            alert("<?php echo 'Province code should be 2 characters'; ?>");
            d.codetmp.focus();
            return false;
        }
        //edited by jasper 01/30/13

		if((d.prov_name.value=="")){
			alert("<?php echo "$segAlertNoProvinceName \\n $LDPlsEnterInfo"; ?>");
			d.prov_name.focus();
			return false;
		}
		if(d.region_nr.value=="0"){
			alert("<?php echo "$segAlertNoRegionName \\n $LDPlsEnterInfo"; ?>");
			d.region_nr.focus();
			return false;
		}
        d.code.value = document.getElementById("reg_code").value + prov_code + "00000";
        //alert (d.code.value);
		return true;
	}/* end of function check */

    function SetRegnr(obj) {
       var regcode_x = document.getElementById("region_nr").selectedIndex;
       var regcode_y = document.getElementById("region_nr").options;
       var regcode = regcode_y[regcode_x].text
       //alert(regcode.substr(0,2));
       document.getElementById("reg_code").value = regcode.substr(0,2);
       //alert(document.getElementById("reg_code").value);
    }

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
			echo $segAlertNoProvinceName;
			break;
		}
		case 'province_exists':
		{
			echo "$segProvinceExists<br>$LDDataNoSave";
            break;
		}
        case 'code_exists';
        {
            echo "Province code already exist." . "<br>$LDDataNoSave";
            break;
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

<form action="<?php echo $thisfile; ?>" method="post" name="province" onSubmit="return check(this)">
<font face="Verdana, Arial" size=-1><?php echo $LDEnterAllFields ?>
<table border=0>
	<tr>
        <td align=right class="adm_item"><font color=#ff0000><b>*</b></font> Province Code: </td>
        <td class="adm_input">
             <input type="text" name="codetmp" id="codetmp" size=50 maxlength=50 onBlur="trimString(this)" value="<?php echo substr($code,2,2) ?>"><br>
        </td>
    </tr>
	<tr>
		<td align=right class="adm_item"><font color=#ff0000><b>*</b></font> <?php echo $segProvinceName ?>: </td>
		<td class="adm_input">
	 		<input type="text" name="prov_name" size=50 maxlength=60 onBlur="trimString(this)" value="<?php echo $prov_name ?>"><br>
		</td>
	</tr> 
	<tr>
		<td align=right class="adm_item"><font color=#ff0000><b>*</b></font> <?php echo $segRegionName ?>: </td>
		<td class="adm_input">
			<select name="region_nr" id="region_nr" onchange="SetRegnr()">
				<option value="0">-Select Region-</option>
				<?php 
					while($addr=$region_list->FetchRow()){					
						$selected="";
                        $reg_code = substr($addr['code'],0,2);
                        //$reg_code = $addr['code'];
						if ($region_nr==$addr['region_nr'])
							$selected="selected";
				?>
				<option value="<?= $addr['region_nr']?>" <?= $selected ?> ><?= $reg_code . "-" . $addr['region_name']?></option>
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
<input type="hidden" id="reg_code" name="reg_code" value="">
<!-- added by jasper 02/12/13 -->
<input type="hidden" name="code" id="code" value="<?php echo $code ?>">
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
