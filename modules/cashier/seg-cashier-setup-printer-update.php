<?php
    error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
    require('./roots.php');
    
    require($root_path.'include/inc_environment_global.php');
    
    #require($root_path."modules/laboratory/ajax/lab-new.common.php");
    #$xajax->printJavascript($root_path.'classes/xajax');


define('LANG_FILE','order.php');
define('NO_2LEVEL_CHK',1);
$local_user='ck_prod_db_user';
require_once($root_path.'include/inc_front_chain_lang.php');
$breakfile=$root_path."modules/cashier/seg-cashier-functions.php".URL_APPEND;

$GLOBAL_CONFIG=array();
# Create global config object
require_once($root_path.'include/care_api_classes/class_globalconfig.php');
require_once($root_path.'include/inc_date_format_functions.php');

$glob_obj=new GlobalConfig($GLOBAL_CONFIG);

$thisfile=basename(__FILE__);

# Start Smarty templating here
 /**
 * LOAD Smarty
 */

 # Note: it is advisable to load this after the inc_front_chain_lang.php so
 # that the smarty script can use the user configured template theme

 require_once($root_path.'gui/smarty_template/smarty_care.class.php');
 $smarty = new smarty_care('common');

 $smarty->assign('bHideTitleBar',TRUE);
 $smarty->assign('bHideCopyright',TRUE);

 # Title in the title bar
 $smarty->assign('sToolbarTitle',"$title");

 # href for the back button
// $smarty->assign('pbBack',$returnfile);

 # href for the help button
 $smarty->assign('pbHelp',"javascript:gethelp('products_db.php','search','$from','$cat')");

 # href for the close button
 $smarty->assign('breakfile',$breakfile);

 # Window bar title
 $smarty->assign('sWindowTitle',"$title");

 
global $allow_ipdcancel, $allow_opdcancel, $allow_ercancel;

$status = $_GET['status'];
$submit = $_POST['submit'];

if($_POST['ipadd']){
    $ipAddress = $_POST['ipadd'];
}else{
    $ipAddress = $_GET['ip'];
}
if($_POST['printer']){
    $sharedName = $_POST['printer'];
}else{
    $sharedName = $_GET['sName'];    
}


$errorMsg = "";
    
if($submit=="Update"){
   
    $ipAdd = $_POST['ipadd'];
    $printer = $_POST['printer'];
    $ipAddressOrg = $_POST['ipAddressOrg'];
    $sharedname = '\\\\'.$ipAdd.'\\'.$printer;
    
    $sql = "UPDATE seg_print_default SET ip_address='$ipAdd',printer_port=".$db->qstr($sharedname)." WHERE ip_address='$ipAddressOrg'";
    
    if($ipAdd && $printer){
        if(filter_var($ipAdd, FILTER_VALIDATE_IP) && strlen($str)<=10) {
        $result = $db->Execute($sql);
        }
    }
    

    if($result){
        $errorMsg = "Printer setup successfully saved.";
    }else if(!filter_var($ipAdd, FILTER_VALIDATE_IP)){
        $errorMsg = "Invalid IP Address.";
    }else if(strlen($str)>10){
        $errorMsg = "Printer name too long.";
    }else{
        $errorMsg = "Failed to save printer setup.";
    }

    if(!$ipAdd){
        $errorMsg = "Please fill in the IP address.";
    }
    if(!$printer){
        $errorMsg = "Please fill in the Shared printer name.";
    }

    // $errorMsg = $printer;
    
}

# Collect javascript code
 ob_start()

?>

<!--added by VAN 02-06-08-->
<!--for shortcut keys -->
<script type="text/javascript" src="<?=$root_path?>js/shortcut.js"></script>

<script type="text/javascript" src="<?=$root_path?>js/jsprototype/prototype.js"></script>
<script language="javascript" >
<!--

// function preSet(){
//     document.getElementById('search').focus();
// }
function closeWindow(){
    window.parent.pSearchClose();
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
<script type="text/javascript" src="<?=$root_path?>js/jsprototype/prototype.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/fat/fat.js"></script>
<link rel="stylesheet" type="text/css" media="all" href="<?= $root_path ?>js/jscalendar/calendar-win2k-cold-1.css" />
<script type="text/javascript" src="<?=$root_path?>js/jscalendar/calendar.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jscalendar/lang/calendar-en.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jscalendar/calendar-setup_3.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/NumberFormat154.js"></script>



<?php

$sTemp = ob_get_contents();
ob_end_clean();
$smarty->append('JavaScript',$sTemp);

ob_start();
?>

<a name="pagetop"></a>

<div style="padding-left:10px">
<form action="" method="post" name="suchform" onSubmit="">
    <div id="tabFpanel">
        <table border="0" cellspacing="2" cellpadding="2" width="99%" align="center">
            <tr><td style="color:red" align="center"><?php echo $errorMsg;?></td></tr>
        </table>
        <table border="0" cellspacing="2" cellpadding="2" width="99%" align="center">
            
            <tr>
                            <td width="15%" nowrap="nowrap" align="left">IP address :</td>
                                <td>
                                    <input class="jedInput" name="ipadd" id="ipadd" type="text" size="40" value="<?=$ipAddress?>"/>
                                    <input type="hidden" name="ipAddressOrg" id="ipAddressOrg" value="<?=$ipAddress?>">
                            </td>
            </tr>
            <tr>
                            <td width="15%" nowrap="nowrap" align="left">Shared printer name :</td>
                                <td>
                                    <input class="jedInput" name="printer" id="printer" type="text" size="40" value="<?=$sharedName?>"/>
                                   
                            </td>
            </tr>
            <tr>
                            <td></td>
                            <td>
                                    max character : 10
                            </td>
            </tr>
            <!-- <tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr> -->
            <tr>
                            <td>&nbsp;</td>
                            <td colspan="2">
                                <input type="submit" name="submit" style="cursor:pointer" value="Update"  class="jedButton"/>
                               <!--  <input type="button" name="Cancel" value="Cancel"  class="jedButton" onclick="closeWindow();"/> -->
                            </td>
            </tr> 
        </table>

    </div>
</div>

</form>

<?php

$sTemp = ob_get_contents();
ob_end_clean();

# Assign the form template to mainframe

 $smarty->assign('sMainFrameBlockData',$sTemp);

 /**
 * show Template
 */
 $smarty->display('common/mainframe.tpl');
?>