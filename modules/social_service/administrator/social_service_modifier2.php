<?php
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
require($root_path.'include/inc_environment_global.php');

require_once($root_path.'modules/social_service/ajax/social_common_ajx.php');


define('LANG_FILE','social_service.php');
$local_user='aufnahme_user';
$thisfile=basename(__FILE__);

#$breakfile =$root_path.'main/spediens.php'.URL_APPEND;
$breakfile=$root_path.'modules/social_service/social_service_main.php'.URL_APPEND;

require($root_path.'include/inc_front_chain_lang.php');

# Start Smarty templating here
 require_once($root_path.'gui/smarty_template/smarty_care.class.php');
 $smarty = new smarty_care('common');

# Title in the toolbar
 $smarty->assign('sToolbarTitle','Social Service :: Classifications Modifiers');

 # href for help button
 $smarty->assign('pbHelp',"javascript:gethelp('medocs_start.php')");

 $smarty->assign('breakfile',$breakfile);

 # Window bar title
 #$smarty->assign('title',$swSocialService);
 $smarty->assign('sWindowTitle',"Social Service :: Classifications Modifiers");

 # Onload Javascript code
 $smarty->assign('sOnLoadJs',$onLoadJs);

 # href for help button
 $smarty->assign('pbHelp',"javascript:gethelp('medocs_entry.php')");

  # hide return button
// $smarty->assign('pbBack',FALSE);

//if(!empty($subtitle)) $smarty->assign('subtitle','<font color="#fefefe" SIZE=3  FACE="verdana,Arial"><b>:: '.$subtitle);

# Buffer page output
ob_start();

?>

<script type="text/javascript" src="<?=$root_path?>js/jsprototype/prototype.js"></script>
<script language="javascript" src="<?php echo $root_path; ?>js/setdatetime.js"></script>
<script language="javascript" src="<?php echo $root_path; ?>js/checkdate.js"></script>

<!-- YUI-2.2 Library -->
<script type="text/javascript" src="<?=$root_path?>js/yui/yahoo/yahoo.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/yui/event/event.js" ></script>
<script type="text/javascript" src="<?=$root_path?>js/yui/dom/dom.js" ></script>

<script type="text/javascript" src="<?=$root_path?>js/yui/dragdrop/dragdrop.js" ></script>
<script type="text/javascript" src="<?=$root_path?>js/yui/connection/connection.js" ></script>

<script type="text/javascript" src="<?=$root_path?>js/yui/container/container.js"></script>
<link type="text/css" rel="stylesheet" href="<?=$root_path?>js/yui/container/assets/container.css">
<link type="text/css" rel="stylesheet" href="<?=$root_path?>js/yui/fonts/fonts.css">

<!-- Core module and plugins:
-->
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/iframecontentmws.js"></script>

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

<script type="text/javascript" src="../js/social_service.js"></script>

<?php 
$xajax->printJavascript($root_path.'classes/xajax-0.2.5');

?>

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

<script>
	xajax_listModifierRow(1);
	xajax_listModifierRow(2);
	xajax_listModifierRow(3);
	YAHOO.util.Event.on("btnGrp", "click",btnClickHandler);
</script>
<script type="text/javascript">
<!--
	function popWinClose(){
		cClick();
	}
-->
</script>

<?php
$sTemp = ob_get_contents();
ob_end_clean();
$smarty->append('JavaScript',$sTemp);
require_once($root_path.'include/care_api_classes/class_social_service.php');
//Instantiate social service class
$objSS = new SocialService;
//$row = $ss->getSSInfo();

ob_start();

?>

<!--<ul>-->
<center>
<table width="60" height="20">
	<tr id="btnGrp">	 
		<!--<td>
			<a href="<?=$root_path?>modules/social_service/administrator/social_service_admin_update.php<?=URL_APPEND.'&mode=update'?>"><img <?= createLDImgSrc($root_path,'update.gif','0') ?>></a>		
		</td>-->
		<td>
			<button id="btnAddMod">Add</button>
		</td>
		<!--<td align="right"><a href="<?php echo $breakfile;?>"><img <?php echo createLDImgSrc($root_path,'close2.gif','0') ?> alt="<?php echo $LDCancelClose ?>" /></a>
		</td>id="sslistTable"-->
	</tr>
</table>

<div id="container" style="width:850px">
	<table id="sslistTable" border="1" width="100%" cellpadding="0" cellspacing="0" class="segList">
		<thead>
			<tr>
				<th colspan="5" align="center">Social Service Classification's Modifiers</th>
			</tr>
		</thead>
		<thead>
			<?php
				$modifier_list = $objSS->getAllModifiers();	
			?>
			<tr>
				<!--
				<th width="33%">Code</th>
				<th width="34%">Description</th>
				<th width="33%">Discount %</th>
				-->
				<?php
					while($row = $modifier_list->FetchRow()){
						echo '<th width="33.33%">'.$row['mod_desc'].'</th>';
					}
				?>
			</tr>
		</thead>
		<tbody id="sslistTbody">
			<!-- List of social service classification -->
			<tr>
				<td>
					<table height="100%" width="100%" border="1"> 
						<tbody id="sslistTbody1">
						<tr>
							<td width="15%" >Code</td>
							<td width="*">Description</td>
							<td width="5%">Edit</td>
							<td width="5%">Delete</td>
						</tr>
						</tbody>
					</table>
				</td>
				<td>
					<table height="100%" width="100%" border="1">
						<tbody id="sslistTbody2">
						<tr>
							<td width="15%">Code</td>
							<td width="*">Description</td>
							<td width="5%">Edit</td>
							<td width="5%">Delete</td>
						</tr>
						</tbody>
					</table>
				</td>
				<td>
					<table height="100%" width="100%" border="1">
						<tbody id="sslistTbody3">
						<tr>
							<td width="15%">Code</td>
							<td width="*">Description</td>
							<td width="5%">Edit</td>
							<td width="5%">Delete</td>
						</tr>
						</tbody>
					</table>
				</td>
			</tr>
		</tbody>
	</table>
</div>
</center>
<input type="hidden" id="sid" name="sid" value="<?=$sid ?>" >
<input type="hidden" id="lang" name="lang" value="<?=$lang ?>" >
<input type="hidden" id="create_id" name="create_id" value="<?=$HTTP_SESSION_VARS['sess_user_name']?>" >

</ul>
<?php

$sTemp = ob_get_contents();

$smarty->assign('sMainDataBlock',$sTemp);

ob_end_clean();


$smarty->assign('sMainBlockIncludeFile','medocs/main_plain.tpl');

$smarty->display('common/mainframe.tpl');


?>