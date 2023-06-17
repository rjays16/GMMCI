<?php
#created by CHA 07-30-2009
#Manage blood donors
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
	
require($root_path.'include/inc_environment_global.php');
require_once($root_path.'include/care_api_classes/class_core.php');  
require($root_path."modules/bloodBank/ajax/blood-donor-register.common.php");
$xajax->printJavascript($root_path.'classes/xajax_0.5');
#$xajax->printJavascript($root_path.'classes/xajax');
#$xajax->printJavascript($root_path.'classes/xajax-0.2.5');

define('LANG_FILE','lab.php');
define('NO_2LEVEL_CHK',1);

#$breakfile = "labor.php";
$breakfile=$root_path.'modules/laboratory/labor.php'.URL_APPEND;
$local_user='ck_prod_db_user';
require_once($root_path.'include/inc_front_chain_lang.php');

$thisfile=basename(__FILE__);

require_once($root_path.'gui/smarty_template/smarty_care.class.php');
$smarty = new smarty_care('common');

$LDBloodBank = "Blood Bank";  
$smarty->assign('sToolbarTitle',"$LDBloodBank :: Blood Donor Registration");

 # href for the back button
// $smarty->assign('pbBack',$returnfile);

 # href for the help button
 $smarty->assign('pbHelp',"javascript:gethelp('submenu1.php','$LDLab')");

 # href for the close button
 $smarty->assign('breakfile',$breakfile);

 # Window bar title
 $smarty->assign('sWindowTitle',"$LDBloodBank :: Blood Donor Registration");
 
 $smarty->assign('bHideTitleBar',true); 
 # Collect javascript code
 ob_start()

?>
<style>

#municipality_autocomplete, #barangay_autocomplete {
	padding-bottom:1.75em;
	width: 185px;
 
}
</style>
<script type="text/javascript" src="<?=$root_path?>js/jsprototype/prototype.js"></script>
<script language="javascript" >
<!--

/*function preSet(){
	document.getElementById('search').focus();
} */

function BackMainMenu(){
		urlholder="labor.php<?=URL_APPEND?>";
		window.location.href=urlholder;
	}

function ReloadWindow(){
	window.location.href=window.location.href;
}
//------------------------------------------
// -->
</script> 

<!-- OLiframeContent(src, width, height) script:
 (include WIDTH with its parameter equal to width, and TEXTPADDING,0, in the overlib call)
-->
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/iframecontentmws.js"></script>

<!-- Core module and plugins:
-->
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_draggable.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_filter.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_overtwo.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_scroll.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_shadow.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_modal.js"></script>
<link rel="stylesheet" type="text/css" media="all" href="<?=$root_path?>js/jscalendar/calendar-win2k-cold-1.css">
<script type="text/javascript" src="<?=$root_path?>js/fat/fat.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jscalendar/calendar.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jscalendar/lang/calendar-en.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jscalendar/calendar-setup_3.js"></script>


<script type="text/javascript" src="<?=$root_path?>js/yui-2.7/yahoo/yahoo.js"></script>
<link rel="stylesheet" type="text/css" href="<?=$root_path?>js/yui-2.7/fonts/fonts-min.css"/>
<link rel="stylesheet" type="text/css" href="<?=$root_path?>js/yui-2.7/autocomplete/assets/skins/sam/autocomplete.css"/>
<script type="text/javascript" src="<?=$root_path?>js/yui-2.7/yahoo-dom-event/yahoo-dom-event.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/yui-2.7/connection/connection-min.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/yui-2.7/animation/animation-min.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/yui-2.7/datasource/datasource-min.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/yui-2.7/autocomplete/autocomplete-min.js"></script>
<script type="text/javascript" src="js/blood-register-donor.js?t=<?=time()?>"></script>
										
<?php

$sTemp = ob_get_contents();
ob_end_clean();
$smarty->append('JavaScript',$sTemp);
$smarty->assign('sFormStart','<form ENCTYPE="multipart/form-data" action="'.$thisfile.'" method="POST" name="suchform">');
$smarty->assign('sFormEnd','</form>');
$smarty->assign('sDonorBirthDate','<input type="text" name="donor_bdate" id="donor_bdate" size="15" onblur="computeAge()"/>');
$smarty->assign('sDonorBirthDateIcon','<img ' . createComIcon($root_path,'show-calendar.gif','0') . 'id="bdate_trigger" align="absmiddle" style="cursor:pointer">[YYYY-mm-dd]');
$jsCalScript = "<script type=\"text/javascript\">
Calendar.setup (
{
		inputField : \"donor_bdate\", 
		ifFormat : \"%Y-%m-%d\", 
		showsTime : false, 
		button : \"bdate_trigger\", 
		singleClick : true, 
		step : 1
}
);
</script>
"; 
$smarty->assign('jsCalendarSetup', $jsCalScript); 
ob_start();

# Workaround to force display of results  form
$bShowThisForm = TRUE;

# If smarty object is not available create one
if(!isset($smarty)){
	/**
 * LOAD Smarty
 * param 2 = FALSE = dont initialize
 * param 3 = FALSE = show no copyright
 * param 4 = FALSE = load no javascript code
 */
	include_once($root_path.'gui/smarty_template/smarty_care.class.php');
	$smarty = new smarty_care('common',FALSE,FALSE,FALSE);
	
	# Set a flag to display this page as standalone
	$bShowThisForm=TRUE;
}

?>

<form action="<?php echo $breakfile?>" method="post">
<input type="hidden" name="sid" value="<?php echo $sid ?>">
<input type="hidden" name="lang" value="<?php echo $lang ?>">
<input type="hidden" name="userck" value="<?php echo $userck ?>">
<input type="hidden" name="key" id="key">
<input type="hidden" name="pagekey" id="pagekey"> 
</form>



<?php

$sTemp = ob_get_contents();
ob_end_clean();

# Assign the form template to mainframe
 $smarty->assign('sMainBlockIncludeFile','blood/blood_register_donor_tray.tpl');   
 $smarty->assign('sMainFrameBlockData',$sTemp);

 /**
 * show Template
 */
 $smarty->display('common/mainframe.tpl');
?> 

<script>
function setMuniCity(mun_nr, mun_name) {
		document.getElementById('donor_mun_nr').value   = mun_nr;
		document.getElementById('donor_mun').value = mun_name;
}

function clearNr(id) {
	if (document.getElementById(id).value == '') {
		switch (id) {
			case "donor_brgy":
				document.getElementById('donor_brgy_nr').value = '';  
			break;
								
			case "donor_mun":
				document.getElementById('donor_mun_nr').value = '';  
			break;           
		}
	}
}

YAHOO.example.BasicRemote = function() {  
		// Use an XHRDataSource -- for barangay
		var brgyDS = new YAHOO.util.XHRDataSource("<?= $root_path ?>modules/system_admin/ajax/seg_brgy_query.php");
		// Set the responseType
		brgyDS.responseType = YAHOO.util.XHRDataSource.TYPE_TEXT;
		// Define the schema of the delimited results
		brgyDS.responseSchema = {
				recordDelim: "\n",
				fieldDelim: "\t"
		};
		// Enable caching
		brgyDS.maxCacheEntries = 5;        
		 
		// Instantiate the AutoComplete
		var brgyAC = new YAHOO.widget.AutoComplete("donor_brgy", "barangay_container", brgyDS); 
		brgyAC.formatResult = function(oResultData, sQuery, sResultMatch) {      
				return "<span style=\"display:none;\">"+oResultData[0]+"</span><span style=\"float:left;width:50%\">"+oResultData[1]+"</span><span>"+oResultData[2]+"</span>";
		};                
		brgyAC.generateRequest = function(sQuery) { 
				return "?query="+sQuery+"&mun_nr="+document.getElementById('donor_mun_nr').value; 
		};     
		
		var munName = YAHOO.util.Dom.get("donor_mun");
		var brgyName = YAHOO.util.Dom.get("donor_brgy");        
		
		// Define an event handler to populate a hidden form field 
		// when an item gets selected 
		var brgyNr = YAHOO.util.Dom.get("donor_brgy_nr");    
		var brgyHandler = function(sType, aArgs) { 
				var bmyAC  = aArgs[0]; // reference back to the AC instance 
				var belLI  = aArgs[1]; // reference to the selected LI element 
				var boData = aArgs[2]; // object literal of selected item's result data 

				// update text input control ...
				brgyNr.value = boData[0];
				brgyName.value = boData[1];
				xajax_getMuniCityandProv(brgyNr.value);        
		}; 
		brgyAC.itemSelectEvent.subscribe(brgyHandler);    
						
		// Use an XHRDataSource --- for municipality or city
		var munDS = new YAHOO.util.XHRDataSource("<?= $root_path ?>modules/system_admin/ajax/seg_municity_query.php");
		// Set the responseType
		munDS.responseType = YAHOO.util.XHRDataSource.TYPE_TEXT;
		// Define the schema of the delimited results
		munDS.responseSchema = {
				recordDelim: "\n",
				fieldDelim: "\t"
		};
		// Enable caching
		munDS.maxCacheEntries = 5;        

		// Instantiate the AutoComplete
		var munAC = new YAHOO.widget.AutoComplete("donor_mun", "municipality_container", munDS);
		munAC.formatResult = function(oResultData, sQuery, sResultMatch) {              
				return "<span style=\"display:none;\">"+oResultData[0]+"</span><span style\"float:left;\">"+oResultData[1]+"</span>";
		};                 
		
		// Define an event handler to populate a hidden form field 
		// when an item gets selected 
		var munNr = YAHOO.util.Dom.get("donor_mun_nr"); 
		var munHandler = function(sType, aArgs) { 
				var mmyAC  = aArgs[0]; // reference back to the AC instance 
				var melLI  = aArgs[1]; // reference to the selected LI element 
				var moData = aArgs[2]; // object literal of selected item's result data 

				// update text input control ...
				munNr.value = moData[0];
				munName.value = moData[1];
				//xajax_getProvince(munNr.value);
				brgyNr.value = '';
				brgyName.value = '';           
		}; 
		munAC.itemSelectEvent.subscribe(munHandler);        
		
								
		return {
				brgyDS: brgyDS,
				munDS: munDS,
				brgyAC: brgyAC,
				munAC: munAC,
		};
}(); 
</script> 
