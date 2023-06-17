<?php
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
require($root_path.'include/inc_environment_global.php');
/**
 * Segworks Technologies Corporation (c)2007
 * Hospital Information System
 * MLHE
 */
define('LANG_FILE','icd10icpm.php');
$local_user='aufnahme_user';

$phic = $_GET['phic'];
if ($phic)
 	$phic_caption = " For PHIC";
	
$thisfile='icpm_new.php';

require_once($root_path.'include/inc_front_chain_lang.php');

# Load the address object
require_once($root_path.'include/care_api_classes/class_icpm.php');

$icpm_obj= new Icpm;

//$db->debug=1;
switch($retpath)
{
	case 'list': $breakfile='icpm_list.php'.URL_APPEND.'&phic='.$phic; break; 
	case 'search': $breakfile='icpm_search.php'.URL_APPEND.'&phic='.$phic; break;
	default: $breakfile='icpm_manage.php'.URL_APPEND.'&phic='.$phic;
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
			# Note: change procedure_code -> code by: Mark on Mar 17, 2007	
			$icpmCode1=$_POST['icpmcode1'];
			$rvu = $_POST['rvu'];
			$multiplier = $_POST['multiplier'];
			//echo "rvu=".$rvu;
			
			//$icpmCode2=$_POST['icpmcode2'];
			
			//$_POST['code']=trim($icpmCode1)."-".trim($icpmCode2);
			$_POST['code'] = trim($icpmCode1);
			$desc = $_POST['description'];
			$ref_code=$_POST['code'];			
			//$_POST['description']=strtoupper($desc);
			//$_POST['rvu'] = $rvu;
			#echo "active = ".$_POST['is_active'];
			#echo "phic = ".$_POST['phic'];
			if ($_POST['phic']){
				if (empty($_POST['is_active']))
					$_POST['is_active'] = '0';
			}
			
			if(!empty($_POST['code'])){
				#
				# Check if icd code exists
				#
				#if($icpm_obj->icpmCodeExists($_POST['code'])){
				if($icpm_obj->icpmCodeExists($_POST['code'], $_POST['phic'])){
					#
					# Do notification
					#
					$mode='icpm_exists';
				}else{    
					
					if($icpm_obj->saveIcpmInfoFromArray($_POST, $_POST['phic'])){
						#
						# Get the last insert ID
						#
						$insid=$db->Insert_ID();
						#
						# Resolve the ID to the primary key
						#
						$icpm_obj->LastInsertPK('code',$insid);
						
    					header("location:icpm_info.php?sid=$sid&lang=$lang&ref_code=$ref_code&mode=show&save_ok=1&retpath=$retpath&phic=".$_POST['phic']);
						exit;
					}else{echo "$sql<br>$LDDbNoSave";}
				}
			}else{
					$mode='bad_data';
			}
			break;
		}//case
	} // end of switch($mode)
}//else


# Start Smarty templating here
 /**
 * LOAD Smarty
 */
 # Note: it is advisable to load this after the inc_front_chain_lang.php so
 # that the smarty script can use the user configured template theme

 require_once($root_path.'gui/smarty_template/smarty_care.class.php');
 $smarty = new smarty_care('system_admin');
 
 # Title in toolbar
 $smarty->assign('sToolbarTitle',"$segICPM :: $segNewICPMCode $phic_caption");

 # href for help button
 $smarty->assign('pbHelp',"javascript:gethelp('icpm_new.php')");

 # href for close button
 $smarty->assign('breakfile',$breakfile);

 # Window bar title
 $smarty->assign('sWindowTitle',"$segICPM :: $segNewICPMCode $phic_caption");

# Coller Javascript code

 
ob_start();
?>

<script type="text/javascript">
<!--
 // insert javascript here.
function chkfld(){
	var c = document.getElementById("icpmcode1");
	var desp = document.getElementById("description");
	var code;
	var phic = document.getElementById("phic");
	//var code = /^[0-9]\-[0-9][0-9][0-9]$/;
	//var code = /^[0-9][0-9][0-9][0-9][0-9]$/;
	
	if (phic.value==1)
		code = /^[0-9][0-9][0-9][0-9][0-9]$/;
	else
		code = /^[0-9]\-[0-9][0-9][0-9]$/;	
	
	//alert(phic+" - "+code);
	
	if(c.value == ""){
		alert("Please enter the ICPM code.");
		c.select();
		return false;
	}
	if(!c.value.match(code)){
		alert("Please enter the ICPM code in correct format.");
		c.select();
		return false;
	}
	if(desp.value == "" ){
		alert("Please fill up the description field.");
		desp.select();
		return false;
	}
	if(document.getElementById("multiplier").value == ""){
		alert("Please fill the multiplier.");
		document.getElementById("multiplier").focus();
		document.getElementById("multiplier").select();
		return false;
	}
	return true;
}

function chkvalue(){
	var v = document.getElementById("rvu");
	var u = /^[0-9]$/;
	var a,i,c,k, IsNumber = true;
	
	c = v.value.toString();
	k = c.length;
	if(v.value !=""){
		i=0; a = ""; 
		while ( i < k){
			if(!c.charAt(i).match(u)){
			 	a += c.charAt(i);
				IsNumber = false;
			}
			i++;
		}
		if (!IsNumber){
			alert( "'"+a+"' is not a number! \n Please enter a number.");
			v.focus();
			return false;	
		}	
	}
	return true;
}

//for icpm encoding
function autoComplate(){
	var i = document.getElementById("icpmcode1");
	var c;
	if( i.value != ""){
		c = i.value.toString();
		if(c.length == 1){
			i.value +="-";				
		}
	}	
}

//-->
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
			echo $LDalerticdChar1;
			break;
		}
		case 'icpm_exists':
		{
			echo $LDicdCodeExists;
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
<?php
		if ($_GET['phic'])
			$phic = $_GET['phic'];
		else	
			$phic = $_POST['phic'];
?>

<form action="<?php echo $thisfile; ?>?phic=<?=$phic?>" method="post" name="icpm" onSubmit="return chkfld();">
<font face="Verdana, Arial" size=-1><?php echo $LDEnterAllFields ?></font>
<table border=0>
  <tr>
    <td align=right class="adm_item"><font color=#ff0000><b>*</b></font> <?php echo $segICPMCode ?> : </td>
    <td class="adm_input">
	 	<?php if ($phic){ ?>
			<input type="text" id="icpmcode1" name="icpmcode1" size=5 maxlength=5 value="<?php echo $icpmCode1 ?>" />
		<?php }else{?>	
    		<input type="text" id="icpmcode1" name="icpmcode1" size=5 maxlength=5 onkeypress="autoComplate()" value="<?php echo $icpmCode1 ?>" />
		<?php } ?>
		
		<span style="font-style:normal">RVU:</span>
		<input type="text" id="rvu" name="rvu" size=4 maxlength="5" onblur="chkvalue()" value="<?php echo $rvu ?>" />
		<?php if ($phic==0){ ?>
			<span style="font:normal">Multiplier:</span>
			<input type="text" id="multiplier" name="multiplier" size=5 value="<?php echo $multiplier ?>">
		<?php } ?>
	</td>    
  </tr> 
  <tr>
    <td align=right class="adm_item"><font color=#ff0000><b>*</b></font> <?php echo $LDdescriptionA ?>: </td>
    <td class="adm_input"><!--<input type="text" id="description" name="description" size=50 maxlength=60 value="<?php echo $description ?>"><br>-->
	 			<textarea id="description" name="description" cols="35" rows="4"><?php echo $description ?></textarea>
	 </td>
  </tr> 
  <!-- added by VAN 08-27-08-->
  
  <?php
  		if ($phic){
			if (empty($is_active))
				$active=1;
  ?>
  
  <tr>
    <td align=right class="adm_item"><font color=#ff0000><b>*</b></font> Is active? : </td>
    <td class="adm_input"><input type="checkbox" id="is_activen" name="is_active" value="1" '<?= ($active)?'checked=checked':''?>'><br></td>
  </tr> 
  <?php } ?>
  <!-- -->
  <tr>
    <td class=pblock><input type="image" <?php echo createLDImgSrc($root_path,'savedisc.gif','0'); ?>></td>
    <td  align=right><a href="<?php echo $breakfile ?>"><img <?php echo createLDImgSrc($root_path,'cancel.gif','0') ?> border="0"></a></td>
  </tr>
</table>
<input type="hidden" name="sid" value="<?php echo $sid ?>">
<input type="hidden" name="mode" value="save">
<input type="hidden" name="lang" value="<?php echo $lang ?>">
<input type="hidden" name="retpath" value="<?php echo $retpath ?>">
<input type="hidden" name="phic" id="phic" value="<?=$phic?>">
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