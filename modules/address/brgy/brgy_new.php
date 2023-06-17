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

$address_municity=new Address('municity');
#$address_municity->_useMuniCity();
$mucity_list = $address_municity->getAllAddress('AS t1, seg_provinces AS t2 WHERE t1.prov_nr=t2.prov_nr');

$address_brgy=new Address('barangay');
#$address_brgy->_useBarangays();

//$db->debug=1;
switch($retpath)
{
	case 'list': $breakfile='brgy_list.php'.URL_APPEND; break;
	case 'search': $breakfile='brgy_search.php'.URL_APPEND; break;
	default: $breakfile='brgy_manage.php'.URL_APPEND; 
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
			$HTTP_POST_VARS['brgy_name']=trim($HTTP_POST_VARS['brgy_name']);
			$HTTP_POST_VARS['brgy_name'] = $address_brgy->stringTrim($HTTP_POST_VARS['brgy_name']);   # burn added: August 25, 2006
			$HTTP_POST_VARS['brgy_nr'] = $address_brgy->getLatestPk(); //added by maimai 12-17-2014

			if(!empty($HTTP_POST_VARS['brgy_name'])){
				#
				# Check if address exists
				#
                //added by jasper 02/13/13
                $bgy_code_temp = $address_brgy->getCodebyNr($HTTP_POST_VARS['mun_nr']);
                $bgy_code = $bgy_code_temp->FetchRow();
                $brgy_code = substr($bgy_code['code'],0,6) . $HTTP_POST_VARS['code'];
                $HTTP_POST_VARS['code'] = $brgy_code;
                //added by jasper 02/13/13
				if($address_brgy->addressExists(0,$HTTP_POST_VARS['brgy_name'],FALSE,$HTTP_POST_VARS['mun_nr'])){
					#
					# Do notification
					#
					$mode='brgy_exists';
                }elseif ($address_brgy->CodeExists($HTTP_POST_VARS['code'])) {
                    $mode='code_exists';
				}else{
				    
					if($address_brgy->saveAddressInfoFromArray($HTTP_POST_VARS)){
						#
						# Get the last insert ID
						#
						$insid=$db->Insert_ID();
						#
						# Resolve the ID to the primary key
						#
						$brgy_nr=$address_brgy->LastInsertPK('brgy_nr',$insid);

						# Get the last insert 'brgy_nr'
						# added burn: February 23, 2007
						$brgy_nr=$address_brgy->LastInsertPKAddress(); 
    					header("location:brgy_info.php?sid=$sid&lang=$lang&brgy_nr=$brgy_nr&mode=show&save_ok=1&retpath=$retpath");
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
 $smarty->assign('sToolbarTitle',"$segBrgy :: $segNewBrgy");

 # href for help button
 $smarty->assign('pbHelp',"javascript:gethelp('address_new.php')");

 # href for close button
 $smarty->assign('breakfile',$breakfile);

 # Window bar title
 $smarty->assign('sWindowTitle',"$segBrgy :: $segNewBrgy");

# Coller Javascript code

ob_start();
?>

<script language="javascript">
<!-- 
	function check(d){
        var brgy_code = d.codetmp.value;
        if ((brgy_code =="")){
            alert("<?php echo "Barangay's code is missing \\n $LDPlsEnterInfo"; ?>");
            d.codetmp.focus();
            return false;
        }else if (brgy_code=="000") {
            alert("<?php echo "Barangay's code should not be equal to zero"; ?>");
            d.codetmp.focus();
            return false;
        }else if (brgy_code.length!==3) {
            alert("<?php echo "Barangay's code should be 3 characters"; ?>");
            d.codetmp.focus();
            return false;
        }
        //edited by jasper 01/30/13

		if((d.brgy_name.value=="")){
			alert("<?php echo "$segAlertNoBrgyName \\n $LDPlsEnterInfo"; ?>");
			d.brgy_name.focus();
			return false;
		}
		if(d.mun_nr.value=="0"){
			alert("<?php echo "$segAlertNoMuniCityName \\n $LDPlsEnterInfo"; ?>");
			d.mun_nr.focus();
			return false;
		}
        d.code.value = brgy_code;
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
			echo $segAlertNoBrgyName;
			break;
		}
		case 'brgy_exists':
		{
			echo "$segBrgyExists<br>$LDDataNoSave";
            break;
		}
        case 'code_exists';
        {
            echo "Barangay's code already exist." . "<br>$LDDataNoSave";
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

<form action="<?php echo $thisfile; ?>" method="post" name="brgy" onSubmit="return check(this)">
<font face="Verdana, Arial" size=-1><?php echo $LDEnterAllFields ?>
<table border=0>
	<tr>
        <td align=right class="adm_item"><font color=#ff0000><b>*</b></font> Barangay Code: </td>
        <td class="adm_input">
             <input type="text" name="codetmp" size=50 maxlength=50 onBlur="trimString(this)" value="<?php echo substr($code,0,3); ?>"><br>
        </td>
    </tr>
	<tr>
		<td align=right class="adm_item"><font color=#ff0000><b>*</b></font> <?php echo $segBrgyName ?>: </td>
		<td class="adm_input">
	 		<input type="text" name="brgy_name" size=50 maxlength=60 onBlur="trimString(this)" value="<?php echo $brgy_name ?>"><br>
		</td>
	</tr> 
	<tr>
		<td align=right class="adm_item"><font color=#ff0000><b>*</b></font> <?php echo $segMuniCityName ?>: </td>
		<td class="adm_input">
			<select name="mun_nr" id="mun_nr">
				<option value="0">-Select Municipality/City-</option>
				<?php 
					while($addr=$mucity_list->FetchRow()){					
						$selected="";
						if ($mun_nr==$addr['mun_nr'])
							$selected="selected";
				?>
				<option value="<?= $addr['mun_nr']?>" <?= $selected ?> ><?= $addr['mun_name'].' ('.$addr['prov_name'].')' ?></option>
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
<!-- added by jasper 02/13/13 -->
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
