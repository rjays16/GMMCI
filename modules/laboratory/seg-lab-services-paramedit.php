<?php

define('ROW_MAX',15); # define here the maximum number of rows for displaying the parameters

error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
require($root_path.'include/inc_environment_global.php');

$lang_tables=array('chemlab_groups.php','chemlab_params.php');
define('LANG_FILE','lab.php');
$local_user='ck_lab_user';
require_once($root_path.'include/inc_front_chain_lang.php');

$thisfile=basename(__FILE__);

//$db->debug=true;

# Create lab object
require_once($root_path.'include/care_api_classes/class_labservices_transaction.php');
$srv=new SegLab();

# require($root_path.'include/inc_labor_param_group.php');

# Load the date formatter */
include_once($root_path.'include/inc_date_format_functions.php');

$excode=$_GET['nr'];
if(isset($_POST['excode'])) $excode=$_POST['excode'];

if($mode=='save'){
	# Save the nr
	
/*	if(!$HTTP_POST_VARS['msr_unit']) $HTTP_POST_VARS['msr_unit']='NULL';
	if(!$HTTP_POST_VARS['median']) $HTTP_POST_VARS['median']='NULL';
	if(!$HTTP_POST_VARS['lo_bound']) $HTTP_POST_VARS['lo_bound']='NULL';
	if(!$HTTP_POST_VARS['hi_bound']) $HTTP_POST_VARS['hi_bound']='NULL';
	if(!$HTTP_POST_VARS['lo_critical']) $HTTP_POST_VARS['lo_critical']='NULL';
	if(!$HTTP_POST_VARS['hi_critical']) $HTTP_POST_VARS['hi_critical']='NULL';
	if(!$HTTP_POST_VARS['lo_toxic']) $HTTP_POST_VARS['lo_toxic']='NULL';
	if(!$HTTP_POST_VARS['hi_toxic']) $HTTP_POST_VARS['hi_toxic']='NULL';
*/
	$x = array();
	$xrow=$_POST['row'];
	$xcode=$_POST['service_code'];
	$xname=$_POST['name'];
	$xunit=$_POST['msr_unit'];
	$xmedian=$_POST['median'];
	$xlbound=$_POST['lo_bound'];
	$xubound=$_POST['hi_bound'];
	$xlcrit=$_POST['lo_critical'];
	$xucrit=$_POST['hi_critical'];
	$xltoxic=$_POST['lo_toxic'];
	$xutoxic=$_POST['hi_toxic'];
	$xstatus=$_POST['status'];
	$xsvcode=$_POST['service_code'];
	$xid=$_POST['param_id'];

	# $HTTP_POST_VARS['modify_id']=$HTTP_SESSION_VARS['sess_user_name'];
	# $HTTP_POST_VARS['history']=$srv->ConcatHistory("Update ".date('Y-m-d H:i:s')." ".$HTTP_SESSION_VARS['sess_user_name']."\n");
	# Set to use the test params
	#$lab_obj->useTestParams();
	# Point to the data array
	#$lab_obj->setDataArray($HTTP_POST_VARS);
	
	#if($srv->updateDataFromInternalArray($HTTP_POST_VARS['nr'])){
	#if ($srv->updateLabServiceInfoFromArray($_POST['excode'],$x)){
	if ($srv->updateLabParam($xid,$xsvcode, $xname, $xunit, $xmedian, $xlbound, $xubound, $xlcrit, $xucrit, $xltoxic, $xutoxic, $xstatus)) {
		# function xrow(rowno, id, name, msrunit, median, lbound, ubound, lcrit, ucrit, ltoxic, utoxic) {
		$xrowArg = $_POST['row'].", '$xid', '$xname', '$xunit', '$xmedian', '$xlbound', '$xubound', '$xlcrit', '$xucrit', '$xltoxic', '$xutoxic'";
?>

<script language="JavaScript">
<!-- Script Begin
 window.opener.xrow(<?= $xrowArg ?>);
 window.close();
//  Script End -->
</script>

<?php
		exit;
	}
	else {
		echo $srv->sql;
	}
# end of if(mode==save)
} 	

# Get the laboratory service values
if($tsrv=&$srv->getLabParams("param_id=".addslashes($id))){
	$ts=$tsrv->FetchRow();
}else{
	$ts=false;
}
	
?>
<!DOCTYPE HTML PUBLIC "-//IETF//DTD HTML 3.0//EN" "html.dtd">
<?php html_rtl($lang); ?>
<HEAD>
<?php echo setCharSet(); ?>
 <TITLE>Edit Parameter</TITLE>

<script language="javascript" name="j1">
<!--        
function editParam(nr)
{
	urlholder="labor_test_param_edit?sid=<?php echo "$sid&lang=$lang" ?>&nr="+encodeURIComponent(nr);
	editparam_<?php echo $sid ?>=window.open(urlholder,"editparam_<?php echo $sid ?>","width=500,height=600,menubar=no,resizable=yes,scrollbars=yes");
}
// -->
</script>

<?php 
require($root_path.'include/inc_js_gethelp.php'); 
require($root_path.'include/inc_css_a_hilitebu.php');
?>
<style type="text/css" name="1">
.va12_n{font-family:verdana,arial; font-size:12; color:#000099}
.a10_b{font-family:arial; font-size:10; color:#000000}
.a12_b{font-family:arial; font-size:12; color:#000000}
.a10_n{font-family:arial; font-size:10; color:#000099}
</style>

</HEAD>

<BODY topmargin=0 leftmargin=0 marginwidth=0 marginheight=0 
<?php

/*if($newid) echo ' onLoad="document.datain.test_date.focus();" ';*/
 if (!$cfg['dhtml']){ echo 'link='.$cfg['body_txtcolor'].' alink='.$cfg['body_alink'].' vlink='.$cfg['body_txtcolor']; } 
 ?>>

<table width=100% border=0 cellspacing=0 cellpadding=0>

<tr>
<td bgcolor="<?php echo $cfg['top_bgcolor']; ?>" >
<FONT  COLOR="<?php echo $cfg['top_txtcolor']; ?>"  SIZE=+2  FACE="Arial"><STRONG> &nbsp;
<?php 	
	echo $ts['name'];
 ?>
 </STRONG></FONT>
</td>
<td bgcolor="<?php echo $cfg['top_bgcolor']; ?>" height="10" align=right ><nobr><a href="javascript:gethelp('lab_param_edit.php')"><img <?php echo createLDImgSrc($root_path,'hilfe-r.gif','0') ?>  <?php if($cfg['dhtml'])echo'style=filter:alpha(opacity=70) onMouseover=hilite(this,1) onMouseOut=hilite(this,0)>';?></a><a href="javascript:window.close()" ><img <?php echo createLDImgSrc($root_path,'close2.gif','0') ?>  <?php if($cfg['dhtml'])echo'style=filter:alpha(opacity=70) onMouseover=hilite(this,1) onMouseOut=hilite(this,0)>';?></a></nobr></td>
</tr>
<tr align="center">
<td  bgcolor=#dde1ec colspan=2>

<FONT    SIZE=-1  FACE="Arial">


<table border=0 bgcolor=#ffdddd cellspacing=1 cellpadding=1 width="100%">
<tr>
<td  bgcolor=#ff0000 colspan=2><FONT SIZE=2  FACE="Verdana,Arial" color="#ffffff">
<b>
<?php //echo $parametergruppe[$ts['group_id']]; ?>
</b>
</td>
</tr>
<tr>
<td  colspan=2>

<form action="<?php echo $thisfile; ?>" method="post" name="paramedit">

<table border="0" cellpadding=2 cellspacing=1>
	
<?php 
	
$toggle=0;

if($ts){

?>
	<tr>
		<td class="a12_b" bgcolor="#fefefe" style="padding-left:4px">Parameter</td>
		<td bgcolor="#ffffee" class="a12_b">
			<input type="text" name="name" size=35 style="width:100%" value="<?= $ts['name'] ?>">
		</td>
	</tr>
	<tr>
		<td class="a12_b" bgcolor="#fefefe" style="padding-left:4px">Msr Unit</td>
		<td bgcolor="#efefef" class="a12_b">
			<input type="text" name="msr_unit" size=35 style="width:100%" value="<?= $ts['msr_unit'] ?>">
		</td>
	</tr>
	<tr>
		<td class="a12_b" bgcolor="#fefefe" style="padding-left:4px">Median</td>
		<td bgcolor="#ffffee" class="a12_b">
			<input type="text" name="median" size=35 style="width:100%" value="<?= $ts['median'] ?>">
		</td>
	</tr>
	<tr>
		<td class="a12_b" bgcolor="#fefefe" style="padding-left:4px">Lower bound</td>
		<td bgcolor="#efefef" class="a12_b">
			<input type="text" name="lo_bound" size=35 maxlength=30 style="width:100%" value="<?= $ts['lo_bound'] ?>">
		</td>
	</tr>
	<tr>
		<td class="a12_b" bgcolor="#fefefe" style="padding-left:4px">Upper bound</td>
		<td bgcolor="#efefef" class="a12_b">
			<input type="text" name="hi_bound" size=35 maxlength=30 style="width:100%" value="<?= $ts['hi_bound'] ?>">
		</td>
	</tr>
	<tr>
		<td class="a12_b" bgcolor="#fefefe" style="padding-left:4px">Lower critical</td>
		<td bgcolor="#efefef" class="a12_b">
			<input type="text" name="lo_critical" size=35 maxlength=30 style="width:100%" value="<?= $ts['lo_critical'] ?>">
		</td>
	</tr>
		<tr>
		<td class="a12_b" bgcolor="#fefefe" style="padding-left:4px">Upper Critical</td>
		<td bgcolor="#efefef" class="a12_b">
			<input type="text" name="hi_critical" size=35 maxlength=30 style="width:100%" value="<?= $ts['hi_critical'] ?>">
		</td>
	</tr>
	<tr>
		<td class="a12_b" bgcolor="#fefefe" style="padding-left:4px">Lower Toxic</td>
		<td bgcolor="#efefef" class="a12_b">
			<input type="text" name="lo_toxic" size=35 maxlength=30 style="width:100%" value="<?= $ts['lo_toxic'] ?>">
		</td>
	</tr>
		<tr>
		<td class="a12_b" bgcolor="#fefefe" style="padding-left:4px">Upper Toxic</td>
		<td bgcolor="#efefef" class="a12_b">
			<input type="text" name="hi_toxic" size=35 maxlength=30 style="width:100%" value="<?= $ts['hi_toxic'] ?>">
		</td>
	</tr>
	<tr>
		<td class="a12_b" bgcolor="#fefefe" style="padding-left:4px">Status</td>
		<td bgcolor="#ffffee" class="a12_b">
			<textarea name="status" cols="35" rows="8" style="width:100%"><?= $ts['status'] ?></textarea>
		</td>
	</tr>

<?php

/*
	if($toggle) $bgc='#ffffee'; else $bgc='#efefef';
	$toggle=!$toggle;
	
	for($i=0;$i<sizeof($sItems);$i++){
		echo '<tr><td class="a12_b" bgcolor="#fefefe">&nbsp;'.$pnames[$i].'</td>
			<td bgcolor="'.$bgc.'"  class="a12_b"><input type="text" name="'.$sItems[$i].'" size=30 maxlength=30 value="';
		if($i>1){
			if($ts[$pitems[$i]]>0) echo $tp[$pitems[$i]];
		}else{ 
			echo $tp[$pitems[$i]];
		}
		echo '">&nbsp;
			</td></tr>
			';
	}
	
*/
	
/*	echo '<tr><td  class="a12_b" bgcolor="#fefefe">&nbsp;'.$LDParameter.'</td>
			<td bgcolor="'.$bgc.'"  class="a12_b"><input type="text" name="name" size=15 maxlength=15 value="'.$tp['name'].'">&nbsp;
			</td></tr>
			';
	echo '<tr><td  class="a12_b" bgcolor="#fefefe">&nbsp;'.$LDMsrUnit.'</td>
			<td bgcolor="'.$bgc.'"  class="a12_b"><input type="text" name="msr_unit" size=15 maxlength=15 value="'.$tp['msr_unit'].'">&nbsp;
			</td></tr>
			';
	echo '<tr><td  class="a12_b" bgcolor="#fefefe">&nbsp;'.$LDMedian.'</td>
			<td bgcolor="'.$bgc.'"  class="a12_b"><input type="text" name="median" size=15 maxlength=15 value="'.$tp['median'].'">&nbsp;
			</td></tr>
			';
	echo '<tr><td  class="a12_b" bgcolor="#fefefe">&nbsp;'.$LDUpperBound.'</td>
			<td bgcolor="'.$bgc.'"  class="a12_b"><input type="text" name="hi_bound" size=15 maxlength=15 value="'.$tp['hi_bound'].'">&nbsp;
			</td></tr>
			';
	echo '<tr><td  class="a12_b" bgcolor="#fefefe">&nbsp;'.$LDLowerBound.'</td>
			<td bgcolor="'.$bgc.'"  class="a12_b"><input type="text" name="lo_bound" size=15 maxlength=15 value="'.$tp['lo_bound'].'">&nbsp;
			</td></tr>';
	echo '<tr><td  class="a12_b" bgcolor="#fefefe">&nbsp;'.$LDUpperCritical.'</td>
			<td bgcolor="'.$bgc.'"  class="a12_b"><input type="text" name="hi_critical" size=15 maxlength=15 value="'.$tp['hi_critical'].'">&nbsp;
			</td></tr>
			';
	echo '<tr><td  class="a12_b" bgcolor="#fefefe">&nbsp;'.$LDLowerCritical.'</td>
			<td bgcolor="'.$bgc.'"  class="a12_b"><input type="text" name="lo_critical" size=15 maxlength=15 value="'.$tp['lo_critical'].'">&nbsp;
			</td></tr>
			';
	echo '<tr><td  class="a12_b" bgcolor="#fefefe">&nbsp;'.$LDUpperToxic.'</td>
			<td bgcolor="'.$bgc.'"  class="a12_b"><input type="text" name="hi_toxic" size=15 maxlength=15 value="'.$tp['hi_toxic'].'">&nbsp;
			</td></tr>
			';
	echo '<tr><td  class="a12_b" bgcolor="#fefefe">&nbsp;'.$LDLowerToxic.'</td>
			<td bgcolor="'.$bgc.'"  class="a12_b"><input type="text" name="lo_toxic" size=15 maxlength=15 value="'.$tp['lo_toxic'].'">&nbsp;
			</td></tr>
			';
*/ }
?>
</table>

<input type=hidden name="nr" value="<?php echo $nr; ?>">
<input type=hidden name="sid" value="<?php echo $sid; ?>">
<input type=hidden name="lang" value="<?php echo $lang; ?>">
<input type=hidden name="mode" value="save">
<input type=hidden name="excode" value="<?= $excode ?>">
<input type=hidden name="row" value="<?= $row ?>">
<input type=hidden name="param_id" value="<?= $ts['param_id']  ?>">
<input type=hidden name="service_code" value="<?= $ts['service_code']  ?>">
<input  type="image" <?php echo createLDImgSrc($root_path,'savedisc.gif','0') ?>> 

</td>
</tr>

</table>

</form>

</FONT>
<p>
</td>

</tr>
</table>        

</BODY>
</HTML>
