<?php

define('ROW_MAX',15); # define here the maximum number of rows for displaying the parameters

error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
require($root_path."modules/laboratory/ajax/lab-admin.common.php");
require($root_path.'include/inc_environment_global.php');
/**
* CARE2X Integrated Hospital Information System Deployment 2.1 - 2004-10-02
* GNU General Public License
* Copyright 2002,2003,2004,2005 Elpidio Latorilla
* elpidio@care2x.org, 
*
* See the file "copy_notice.txt" for the licence notice
*/
$lang_tables=array('chemlab_groups.php','chemlab_params.php');
define('LANG_FILE','lab.php');
$local_user='ck_lab_user';
require_once($root_path.'include/inc_front_chain_lang.php');

$thisfile=basename(__FILE__);

//$db->debug=true;
# Create lab object
require_once($root_path.'include/care_api_classes/class_labservices_transaction.php');
$srvObj=new SegLab();
#require_once($root_path.'include/care_api_classes/class_lab.php');
#$srvObj=new SegLab;

#---added by VAS
require_once($root_path.'include/care_api_classes/class_department.php');
$dept_obj=new Department;

include_once('seg-lab-services-admin.action.php');
require($root_path.'include/inc_labor_param_group.php');

# Load the date formatter */
include_once($root_path.'include/inc_date_format_functions.php');
    
$breakfile="labor.php".URL_APPEND;

 # Note: it is advisable to load this after the inc_front_chain_lang.php so
 # that the smarty script can use the user configured template theme

 require_once($root_path.'gui/smarty_template/smarty_care.class.php');
 $smarty = new smarty_care('common');

# Title in toolbar
 #$smarty->assign('sToolbarTitle',"Laboratory::Services admin::".$dept_name);
 $smarty->assign('sToolbarTitle',"Laboratory::Services admin");

 # href for help button
 $smarty->assign('pbHelp',"javascript:gethelp('lab_param_config.php')");

 # hide return  button
 $smarty->assign('pbBack',FALSE);

 # href for close button
 $smarty->assign('breakfile',$breakfile);

 # Window bar title
 #$smarty->assign('sWindowTitle',"Laboratory::Services admin::".$dept_name);
 $smarty->assign('sWindowTitle',"Laboratory::Services admin");

 # collect extra javascript code
 ob_start();
echo '<link rel="stylesheet" type="text/css" media="all" href="' .$root_path.'js/jscalendar/calendar-win2k-cold-1.css">'."\r\n";
echo '<script type="text/javascript" src="'.$root_path.'js/jsprototype/prototype.js"></script>'."\r\n";
echo '<script type="text/javascript" src="'.$root_path.'js/fat/fat.js"></script>'."\r\n";
echo '<script type="text/javascript" src="'.$root_path.'js/jscalendar/calendar.js"></script>'."\r\n";
echo '<script type="text/javascript" src="'.$root_path.'js/jscalendar/lang/calendar-en.js"></script>'."\r\n";
echo '<script type="text/javascript" src="'.$root_path.'js/jscalendar/calendar-setup_3.js"></script>'."\r\n";
echo '<script type="text/javascript" src="'.$root_path.'modules/laboratory/js/lab-request-gui.js"></script>'."\r\n";

if ($xajax) {
	$xajax->printJavascript($root_path.'classes/xajax');
}


?>

<script language="javascript" name="j1">
<!--  
	var popWindowEditLabReq = "";
	
	function closechild(){
		try{
			if ((popWindowEditLabReq!=null)&&(false == popWindowEditLabReq.closed)){
				popWindowEditLabReq.close ();
			}
		}finally{
			//alert('Deleted sucessfully!');
		}
	}
	
	function editService(nr,pid,rowno){
		/*
		urlholder="<?php echo $root_path ?>modules/laboratory/seg-lab-request-edit.php?sid=<?php echo "$sid&lang=$lang" ?>&nr="+nr+"&pid="+pid+"&row="+rowno;
		editsrv_<?php echo $sid ?>=window.open(urlholder,"editsrv_<?php echo $sid ?>","width=500,height=600,menubar=no,resizable=no,scrollbars=yes");
		*/
		//alert("editService");
		var w=window.screen.width;
		var h=window.screen.height;
		var ww=500;
		var wh=500;
		
		urlholder="<?php echo $root_path ?>modules/laboratory/seg-lab-request-edit.php?sid=<?php echo "$sid&lang=$lang" ?>&nr="+nr+"&pid="+pid+"&row="+rowno;

		if (window.showModalDialog){  //for IE
			window.showModalDialog(urlholder,"width=" + ww + ",height=" + wh + ",menubar=no,resizable=yes,scrollbars=yes,dependent=yes, center=yes");
		}else{
			popWindowEditLabReq=window.open(urlholder,"editsrv_<?php echo $sid ?>","width=" + ww + ",height=" + wh + ",menubar=no,resizable=yes,scrollbars=yes, left=300, top=100");
			//window.editsrv_<?php echo $sid ?>.moveTo((w/2)+80,(h/2)-(wh/2));
		}
	}

	function jsGetRequestor(d){
		var aEncType=d.enctype;
	   var aEncType_id = aEncType.options[aEncType.selectedIndex].value;
		//alert("jsGetRequestor aEncType_id = "+aEncType_id);
		xajax_getLabListReq(aEncType_id);	
	}

// -->
</script>

<?php

$script = ob_get_contents();
ob_end_clean();

$smarty->append('JavaScript',$script);

# Assign Body Onload javascript code

#if (isset($parameterselect)) $gid=$parameterselect;
#else $gid=$groups[0]['group_id'];
	#$onLoadJS='onLoad="jsGetServiceGroup(paramselect);"';
	
$onLoadJS='onLoad="jsGetRequestor(requestselect);"';	
#$onLoadJS.='"';
$smarty->assign('sOnLoadJs',$onLoadJS);

$sTemp = '';
$sTemp = $sTemp.'<select name="enctype" id="enctype" onChange="jsGetRequestor(requestselect);">
								<option value="0">ALL</option>
								<option value="1">IN-PATIENT</option>
								<option value="2">WALKIN</option>';
					$sTemp = $sTemp.'</select>
							<font size=1><img '.createComIcon($root_path,'redpfeil_l.gif','0','',TRUE).'> Filter</font>';
$smarty->assign('sEncTypeSelect',$sTemp);

#$smarty->assign('sContinueButton','<input type="image" '.createLDImgSrc($root_path,'continue.gif','0','left').' align="absmiddle" onClick="jsSaveRequest();get_check_value();">');
#'<td align="center"><a href="" id="srvEdit{{ROWNO}}" onclick="editService(\''+code+'\',{{ROWNO}});return false;" style="text-decoration:underline">Edit</a></td>'+
#'<td align="center"><a href="" id="srvDel{{ROWNO}}" onclick="if(confirm(\'Do you wish to remove this service?\')) { xajax_dsrv({{ROWNO}},\''+code+'\'); } return false;" style="text-decoration:underline">Delete</a></td>'+
#$smarty->assign('sEditButton','<input type="button" name="editform" id="editform" value="Edit" style="cursor:pointer ">');
#$smarty->assign('sDeleteButton','<input type="button" name="deleteform" id="deleteform" value="Delete" style="cursor:pointer " onclick="if(confirm(\'Do you wish to remove this service?\')) { }">');

$fileforward="seg-lab-test-request.php".URL_REDIRECT_APPEND."&user_origin=".$user_origin;

$smarty->assign('sAddNewRequest','<a href="'.$fileforward.'"><img '.createLDImgSrc($root_path,'newrequest.gif','0','left').' border=0 alt="Enter New Lab Request"></a>');
#$smarty->assign('sAddNewRequest','<input type="image" '.createLDImgSrc($root_path,'newdata.gif','0','left').' align="absmiddle" onClick="">');

$smarty->assign('sMainBlockIncludeFile','laboratory/lab_services_request.tpl');

$smarty->display('common/mainframe.tpl');
?>
