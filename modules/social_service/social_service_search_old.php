<?php
//error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');

require($root_path.'include/inc_environment_global.php');
require($root_path.'modules/social_service/ajax/social_common_ajx.php');

//define('LANG_FILE','aufnahme.php');
define('LANG_FILE','social_service.php');
# Resolve the local user based on the origin of the script
//require_once('include/inc_local_user.php');

# Set break file
$thisfile=basename(__FILE__);
//$breakfile='medocs_pass.php';
$breakfile='social_service_pass.php';
$local_user='medocs_user';
//$local_user='social_service_user';

if(!stristr($breakfile,'lang=')) $breakfile.=URL_APPEND;

require($root_path.'include/inc_front_chain_lang.php');

require_once($root_path.'include/inc_date_format_functions.php');
//$breakfile='social_service_pass.php';

//$thisfile=basename(__FILE__);
//if($origin=='patreg_reg') $returnfile='patient_register_show.php'.URL_APPEND.'&pid='.$pid;


//require('include/inc_breakfile.php');

if(!session_is_registered('sess_pid')) session_register('sess_pid');
if(!session_is_registered('sess_full_pid')) session_register('sess_full_pid');
if(!session_is_registered('sess_en')) session_register('sess_en');
if(!session_is_registered('sess_full_en')) session_register('sess_full_en');

//$headframe_title=$LDMedocs;
$headframe_title=$swSocialService;

# Start Smarty templating here
 /**
 * LOAD Smarty
 */
 # Note: it is advisable to load this after the inc_front_chain_lang.php so
 # that the smarty script can use the user configured template theme

 require_once($root_path.'gui/smarty_template/smarty_care.class.php');
 $smarty = new smarty_care('common');

# Title in the toolbar
 $smarty->assign('sToolbarTitle',$headframe_title);

 # href for help button
 $smarty->assign('pbHelp',"javascript:gethelp('medocs_start.php')");

 $smarty->assign('breakfile',$breakfile);

 # Window bar title
 $smarty->assign('title',$headframe_title);

 if(!$encounter_nr && !$pid){
	//$onLoadJs='onLoad="if(document.searchform.searchkey.focus) document.searchform.searchkey.focus();"';
}

 if(defined('MASCOT_SHOW') && MASCOT_SHOW==1){
	$onLoadJs='onLoad="if (window.focus) window.focus();"';
	
}

 # Onload Javascript code
 $smarty->assign('sOnLoadJs',$onLoadJs);

 # href for help button
 $smarty->assign('pbHelp',"javascript:gethelp('medocs_entry.php')");

  # hide return button
 $smarty->assign('pbBack',FALSE);

# Load tabs

$target='search';
/*
// TABS
if(!isset($notabs)||!$notabs){
	
	if($target=="entry")  $img='document-blue.gif'; //echo '<img '.createLDImgSrc($root_path,'admit-blue.gif','0').' alt="'.$LDAdmit.'">';
		else{ $img='document-gray.gif';}
	
	$smarty->assign('pbNew','<a href="medocs_start.php'.URL_APPEND.'&target=entry"><img '.createLDImgSrc($root_path,$img,'0').' title="'.$LDAdmit.'" style=filter:alpha(opacity=70) onMouseover=hilite(this,1) onMouseOut=hilite(this,0)></a>');
	
	if($target=="search") $img='such-b.gif';
		else{ $img='such-gray.gif'; }

	$smarty->assign('pbSearch','<a href="medocs_data_search.php'.URL_APPEND.'&target=search"><img '.createLDImgSrc($root_path,$img,'0').' title="'.$LDSearch.'"  style=filter:alpha(opacity=70) onMouseover=hilite(this,1) onMouseOut=hilite(this,0)></a>');

}

if(!empty($subtitle)) $smarty->assign('subtitle','<font color="#fefefe" SIZE=3  FACE="verdana,Arial"><b>:: '.$subtitle);

*/
# Buffer page output

ob_start();

?>
<link type="text/css" rel="stylesheet" href="<?=$root_path?>js/yui/assets/dpSyntaxHighlighter.css">
<link type="text/css" rel="stylesheet" href="<?=$root_path?>js/yui/fonts/fonts.css">
<link type="text/css" rel="stylesheet" href="<?=$root_path?>js/yui/datatable/assets/datatable.css">

<style type="text/css">
	#ysearchinput {width:20em; height:1.4em;}
	#ysearchcontainer {position:absolute;z-index:9050;}
    #ysearchcontainer .yui-ac-content {position:absolute;left:0;top:0;width:20em;border:1px solid #404040;background:#fff;overflow:hidden;text-align:left;z-index:9050;}
    #ysearchcontainer .yui-ac-shadow {position:absolute;left:0;top:0;margin:.3em;background:#a0a0a0;z-index:9049;}
    #ysearchcontainer ul {padding:5px 0;width:100%;}
    #ysearchcontainer li {padding:0 5px;cursor:default;white-space:nowrap;}
    #ysearchcontainer li.yui-ac-highlight {background:#ff0;}

	/* custom css*/ 
	#paginated {margin:1em;} 
	#paginated table {border-collapse:collapse;} 
	#paginated th, #paginated td {border:1px solid #000;} 
	#paginated th {background-color:#696969;color:#fff;}/*dark gray*/ 
	#paginated .yui-dt-odd {background-color:#eee;} /*light gray*/ 
	
		/* custom css*/ 
	#text {margin:1em;} 
	#text table {border-collapse:collapse;} 
	#text th, #text td {padding:.5em;border:1px solid #000;} 
	#text th {background-color:#696969;color:#fff;}/*dark gray*/ 
	#text th a {color:white;} 
	#text th a:hover {color:blue;} 
	#text .yui-dt-odd {background-color:#eee;} /*light gray*/ 


</style>





<script  language="javascript">
<!-- 
<?php require($root_path.'include/inc_checkdate_lang.php'); ?>
 -->
</script>

<script language="javascript" src="<?php echo $root_path; ?>js/setdatetime.js"></script>
<script language="javascript" src="<?php echo $root_path; ?>js/checkdate.js"></script>

<script type="text/javascript" src ="<?=$root_path?>modules/social_service/js/social_service.js"></script>
<?=$xajax->printJavascript($root_path.'classes/xajax')?>

<?php 

$sTemp = ob_get_contents();
ob_end_clean();
$smarty->append('JavaScript',$sTemp);

#Load tabs

$target='entry';
//include('./gui_bridge/default/gui_tabs_medocs.php');

# Buffer the page output

ob_start();

?>

<ul>
<!--  Library begins -->
<script type="text/javascript" src="<?=$root_path?>js/yui/yahoo/yahoo.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/yui/dom/dom.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/yui/event/event-debug.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/yui/connection/connection.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/yui/animation/animation.js"></script>
<script type="text/javascript" src="./js/json.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/yui/autocomplete/autocomplete-debug.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/yui/logger/logger.js"></script>

<script type="text/javascript" src="<?=$root_path?>js/yui/event/event.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/yui/datasource/datasource-beta-debug.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/yui/datatable/datatable-beta-debug.js"></script>

<!--  Library ends -->

<script type="text/javascript">
/*
YAHOO.mark.ACJson = function(){
	var oACDs;
	var oAutoComp;
	
	return{
		init: function() {
			//Instantiate an XHR DataSource and define schema as an array:
			//	["Multi-depth.object.notation.to.find.a.single.result.item",
			//	"Query Key",
			// "Additional Param Name 1",
			//	....
			// "Additionla Param name n."]
			//oACDs = new YAHOO.widget.DS_XHR("social_service.php",["ResultSet.Result", "Title"]);
			oACDs = new YAHOO.widget.DS_XHR("social_service_flat.php",["\n", "\t"]);
			oACDs.responseType = YAHOO.widget.DS_XHR.TYPE_FLAT;
			//oACDs.queryMacthContains = true;
			//oACDs.scriptQueryAppend ="output=json&results = 100";  	
			oACDs.maxCacheEntries = 60;
			oACDs.queryMatchSubset = true;
			
			
			//Instantiate AutoComplete
		//	var myInput = document.getElementById('ysearchinput');
		//	var myContainer = document.getElementById('ysearchcontainer');
			
			oAutoComp = new YAHOO.widget.AutoComplate("ysearchinput", "ysearchcontainer", oACDs);
			oAutoComp.delimChar = ";";
			oAutoComp.queryDelay = 1;
			oAutoComp.useShadow = true;
			//overide format output
			//oAutoComp.formatResult = function(oResultItem, sQuery) {
			//	return oResultItem[1].Title + "(" + oResultItem[1].Url + ")";
			//};
			
			oAutoComp.formatResult = function(oResultItem, sQuery){
				var sKey = oResultItem[0];
				var nQuantity = oResultItem[1];
				var sKeyQuery = sKey,substr(0, sQuery.length);
				var sKeyRemainder = sKey.substr(sQuery.length);
                var aMarkup = ["<div id='ysearchresult'><div class='ysearchquery'>",
                    nQuantity,
                    "</div><span style='font-weight:bold'>",
                    sKeyQuery,
                    "</span>",
                    sKeyRemainder,
                    "</div>"];
                return (aMarkup.join(""));
            }; */
			/*
			oAutoComp.doBeforeExpandContainer = function(oTextbox, oContainer, sQuery, aResults) {
				var pos = YAHOO.util.DOM.getXY(oTextbox);
				pos[1] += YAHOO.util.DOM.get(oTextbox).offsetHeight;
				YAHOO.util.DOM.setXY(oContainer, pos);
				return true;
			};
			*/
/*		},
		
		validateForm: function(){
			//validate form inputs here
			return false;
		}
	};
	
	
}();
YAHOO.util.Event.on(this,"load", YAHOO.mark.ACJson.init);
*/


// YAHOO datasource 

</script>

<?php 
/* If the origin is admission link, show the search prompt */
if(!isset($pid) || !$pid)
{
/* Set color values for the search mask */

$searchmask_bgcolor="#f3f3f3";
$searchprompt=$LDEntryPrompt;
$entry_block_bgcolor='#fff3f3';
$entry_border_bgcolor='#6666ee';
$entry_body_bgcolor='#ffffff';

//$LDPlsSelectPatientFirst
?>
<table border=0>
  <tr>
    <td valign="bottom"><img <?php echo createComIcon($root_path,'angle_down_l.gif','0') ?>></td>
    <td><font color="#000099" SIZE=3  FACE="verdana,Arial"> <b><?php echo $swSelectPatientFrist ?></b></font></td>
    <td><img <?php echo createMascot($root_path,'mascot1_l.gif','0','absmiddle') ?>></td>
  </tr>
</table>
<table border=0 cellpadding=10 bgcolor="<?php echo $entry_border_bgcolor ?>">
	<tr>
    	<td>
	   		<table border=0 cellspacing=5 cellpadding=5>
	   			<tr bgcolor="<?php if ($searchmask_bgcolor) echo $searchmask_bgcolor; else echo "#ffffff"; ?>">
	   				<td>	
	   					<form method="post" name="ysearchForm<?php if($searchform_count) echo "_".$searchform_count; ?>" onSubmit=""
	   				  	<?php if(isset($search_script) && $search_script!='') echo 'action="'.$search_script.'"'; ?> >
	   				  	&nbsp;
		   				  	<label><?=$swSelectKeyWord?></label>
		   				  	<br>
		   				  	<!-- AutoComplete begins  -->
		   				  	<label>Yahoo! Search</label>
				          	<input id="ysearchinput">
	            		  	<!-- <input id="ysearchsubmit" type="submit" value="Submit Query">  -->
	            		  	
	            		  	<input type="image" <?php echo createLDImgSrc($root_path,'searchlamp.gif','0','absmiddle') ?>>
	            			<div id="ysearchcontainer"></div>
	            		   	<!-- AutoComplete ends -->
	            			<?php
	            				if(defined('SHOW_FIRSTNAME_CONTROLLER')&&SHOW_FIRSTNAME_CONTROLLER){
	      					?>
	      						<input type="checkbox" name="firstname_too" <?php if(isset($firstname_too)&&$firstname_too) echo 'checked'; ?>> <?php echo $LDIncludeFirstName; ?><p>	
	      					<?php			
	            				}
	            			?>
	      	      			<br>
							<!--  <input type="image" <?php echo createLDImgSrc($root_path,'searchlamp.gif','0','absmiddle') ?>> -->
							<input type="hidden" name="sid" value="<?php echo $sid; ?>">
							<input type="hidden" name="lang" value="<?php echo $lang; ?>">
							<input type="hidden" name="noresize" value="<?php echo $noresize; ?>">
							<input type="hidden" name="target" value="<?php echo $target; ?>">
							<input type="hidden" name="user_origin" value="<?php echo $user_origin; ?>">
							<input type="hidden" name="retpath" value="<?php echo $retpath; ?>">
							<input type="hidden" name="aux1" value="<?php echo $aux1; ?>">
							<input type="hidden" name="ipath" value="<?php echo $ipath; ?>">
							<input type="hidden" name="mode" value="search">
            			</form>
					</td>
				</tr>
		 	</table>
	  	</td>
	</tr>
</table>

<?php 
}
?>

<p>
<a href="<?php echo $breakfile;?>"><img <?php echo createLDImgSrc($root_path,'cancel.gif','0') ?> alt="<?php echo $LDCancelClose ?>"></a>
<p>

<table>
	<td><b><?php // echo $pagen->makeSortLink($LDCaseNr, 'encounter_nr', $oitem, $odir, $append); ?></b></td>
	<td><b><?php //echo $pagen->makeSortLink("Encounter Date", 'date', $oitem, $odir, $append); ?></b></td>
</table>
<table id="sTable">
	<thead></thead>
	<tbody>
		<tr><div id="text"></div>
			<!--  <tr></tr> -->	
		</tr>
	</tbody>
</table>


</ul>

<script type="text/javascript">

	var formatUrl = function(elCell, oRecord, oColumn, sData) {
    elCell.innerHTML = "<a href='" + oRecord.Url + "' target='_blank'>" + sData + "</a>";
	};
/*	
	var myColumnHeaders = [
	    {key:"Name", sortable:true, formatter:formatUrl},
	    {key:"Address"},
	    {key:"Phone"},
	    {key:"City"},
	    {key:"Rating", type:"number", sortable:true}
	
	];*/

	var myColumnHeaders = [
		{key:"pid"},
		{key:"encounter_nr"},
		{key:"name_last"},
		{key:"name_first"},
	];
	var myColumnSet = new YAHOO.widget.ColumnSet(myColumnHeaders);
	
	var myDataSource = new YAHOO.util.DataSource("./social_service.php");
	//var myDataSource = new YAHOO.util.DataSource("./text_proxy.php");
	//var myDataSource = new YAHOO.util.DataSource("./social_service_flat.php");
	//myDataSource.responseType = YAHOO.util.DataSource.TYPE_TEXT;
	myDataSource.responseType = YAHOO.util.DataSource.TYPE_JSON; 
	
	/*myDataSource.responseSchema = {
	    recordDelim: "\n",
	    fieldDelim: "|",
	    fields: ["Name","Address","City","Phone",{key:"Rating",converter:YAHOO.util.DataSource.convertNumber},"Url"]
	};*/
	/*
	myDataSource.responseSchema = {
	    resultsList: "ResultSet.Result",
	    recordDelim: "\n",
	    fieldDelim: "|",
	    fields: ["pid","encounter_nr","name_last","name_first"]
	};
	*/
	myDataSource.responseSchema = {
    resultsList: "ResultSet.Result",
    fields: ["pid","encounter_nr","name_last","name_first"]
	};
	
	var ysearch = document.getElementById("ysearchinput");
	//alert("ysearch="+ysearch.value);
	var myDataTable = new YAHOO.widget.DataTable("text", myColumnSet, myDataSource,{initialRequest:"query="+ysearch.value+"$output=json"});
	//var myDataTable = new YAHOO.widget.DataTable("json", myColumnSet, myDataSource,{initialRequest:"query=pizza&zip=94089&results=10&output=json"}); 

</script>


<?php

$sTemp = ob_get_contents();

//$smarty->assign('sMainDataBlock',$sTemp);
$smarty->assign('sMainFrameBlockData',$sTemp);

ob_end_clean();


//$smarty->assign('sMainBlockIncludeFile','medocs/main_plain.tpl');

$smarty->display('common/mainframe.tpl');


?>