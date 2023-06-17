<?php
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
require($root_path.'include/inc_environment_global.php');

require_once($root_path.'modules/social_service/ajax/social_mod_common_ajx.php');


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
 
 require_once($root_path.'include/care_api_classes/class_social_service.php');
//Instantiate social service class
 $objSS = new SocialService;

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

<script type="text/javascript" src="../js/social_service_mod.js"></script>

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

<?php
	$modifier_list = $objSS->getAllModifiers();
	#echo $objSS->sql;	
	while($row = $modifier_list->FetchRow()){
?>
<script>
	//alert(<?=$row['mod_code']?>);
	xajax_listModifierRow(<?=$row['mod_code']?>);
</script>	
<!--<div id="container" style="width:850px">-->
<div id="container<?=$row['mod_code']?>" style="display:block; border:1px solid #8cadc0; overflow-x:hidden;overflow-y:hidden; height:200px; width:90%; background-color:#e5e5e5">
	<div style="display:block; border:1px solid #8cadc0; overflow-x:hidden;overflow-y:hidden; width:100%; background-color:#e5e5e5">
		<table border="0" width="100%" cellpadding="0" cellspacing="0" class="segList">
			<thead>
				<tr>
					<th colspan="5" align="center"><?=$row['mod_desc']?></th>
				</tr>
				<!--
				<tr class="nav">
					<th colspan="9">
						<div id="pageFirst" class="segDisabledLink" style="float:left" onclick="jumpToPage(this,FIRST_PAGE)">
							<img title="First" src="<?= $root_path ?>images/start.gif" border="0" align="absmiddle"/>
							<span title="First">First</span>
						</div>
						<div id="pagePrev" class="segDisabledLink" style="float:left" onclick="jumpToPage(this,PREV_PAGE)">
							<img title="Previous" src="<?= $root_path ?>images/previous.gif" border="0" align="absmiddle"/>
							<span title="Previous">Previous</span>
						</div>
						<div id="pageShow" style="float:left; margin-left:10px">
							<span></span>
						</div>
						<div id="pageLast" class="segDisabledLink" style="float:right" onclick="jumpToPage(this,LAST_PAGE)">
							<span title="Last">Last</span>
							<img title="Last" src="<?= $root_path ?>images/end.gif" border="0" align="absmiddle"/>
						</div>
						<div id="pageNext" class="segDisabledLink" style="float:right" onclick="jumpToPage(this,NEXT_PAGE)">
							<span title="Next">Next</span>
							<img title="Next" src="<?= $root_path ?>images/next.gif" border="0" align="absmiddle"/>
						</div>
					</th>
				</tr>
				-->
			</thead>
		</table>
	</div>
	<!--<div id="container" style="width:850px">-->
	<div style="display:block; border:1px solid #8cadc0; overflow-x:hidden;overflow-y:scroll; height:175px; width:100%; background-color:#e5e5e5">
	<table id="sslistTable" border="0" width="100%" cellpadding="0" cellspacing="0" class="segList">
		<!--
		<thead>
			<tr>
				<th colspan="5" align="center"><?=$row['mod_desc']?></th>
			</tr>
		</thead>
		-->
		<thead>
			<tr>
				<th width="5%">Code</th>
				<th width="*">Description</th>
				<th width="3%">Edit</th>
				<th width="5%">Delete</th>
			</tr>
		</thead>
		<tbody id="sslistTbody<?=$row['mod_code']?>">
			<!-- List of social service classification -->
		</tbody>
		
		<!--
		<tbody id="sslistTbody<?=$row['mod_code']?>"> -->
			<!-- List of social service classification -->
		<!--</tbody>	-->
		
	</table>
	</div>
</div>
<br />
<br />
<?php } ?>
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