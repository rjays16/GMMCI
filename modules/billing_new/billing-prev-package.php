<?php
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
require($root_path.'include/inc_environment_global.php');
/**
* CARE2X Integrated Hospital Information System Deployment 2.1 - 2004-10-02
* GNU General Public License
* Copyright 2002,2003,2004,2005 Elpidio Latorilla
* elpidio@care2x.org
*
* See the file "copy_notice.txt" for the licence notice
*/
define('LANG_FILE','order.php');
define('NO_2LEVEL_CHK',1);
$local_user='ck_prod_db_user';
require_once($root_path.'include/inc_front_chain_lang.php');
# Create products object
$dbtable='care_config_global'; // Table name for global configurations
$GLOBAL_CONFIG=array();
$new_date_ok=0;
# Create global config object
require_once($root_path.'include/care_api_classes/class_globalconfig.php');
require_once($root_path.'include/inc_date_format_functions.php');

$glob_obj=new GlobalConfig($GLOBAL_CONFIG);
if($glob_obj->getConfig('date_format')) $date_format=$GLOBAL_CONFIG['date_format'];
$date_format=$GLOBAL_CONFIG['date_format'];
$phpfd=$date_format;
$phpfd=str_replace("dd", "%d", strtolower($phpfd));
$phpfd=str_replace("mm", "%m", strtolower($phpfd));
$phpfd=str_replace("yyyy","%Y", strtolower($phpfd));
$phpfd=str_replace("yy","%y", strtolower($phpfd));

$title=$LDPharmacy;
$breakfile=$root_path."modules/cashier/seg-cashier-functions.php".URL_APPEND;
$imgpath=$root_path."pharma/img/";

/**
* CARE2X Integrated Hospital Information System Deployment 2.1 - 2004-10-02
* GNU General Public License
* Copyright 2002,2003,2004,2005 Elpidio Latorilla
* elpidio@care2x.org,
*
* See the file "copy_notice.txt" for the licence notice
*/

$thisfile=basename(__FILE__);

# Start Smarty templating here
 /**
 * LOAD Smarty
 */

 # Note: it is advisable to load this after the inc_front_chain_lang.php so
 # that the smarty script can use the user configured template theme

 require_once($root_path.'gui/smarty_template/smarty_care.class.php');
 $smarty = new smarty_care('common');

 # Title in the title bar
 $smarty->assign('sToolbarTitle',"Billing::Process billing");

 $smarty->assign('bHideTitleBar',TRUE);
 $smarty->assign('bHideCopyright',TRUE);

 # href for the help button
 $smarty->assign('pbHelp',"javascript:gethelp('products_db.php','search','$from','$cat')");

 # href for the close button
 $smarty->assign('breakfile',$breakfile);

 # Window bar title
 $smarty->assign('sWindowTitle',"Billing::Process billing");

 # Assign Body Onload javascript code
 $smarty->assign('sOnLoadJs','onLoad=""');

 # Collect javascript code
 ob_start()

?>
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
<style type="text/css">
<!--
.tabFrame {
    margin:5px;
}
-->
</style>

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
# Buffer page output
global $db;
include($root_path.'include/care_api_classes/billing/class_billing_new.php');
$encnr = $_GET['enc_nr'];
$pid = $_GET['pid'];
$objBilling = new Billing;

# Resolve current page (pagination)
    $result = $objBilling->GetPreviousPackage($encnr,$pid);
    
    if ($result) {
        $rows_found = $result->RecordCount();
        
        while ($row=$result->FetchRow()) {
            
     

            $rows .= "<tr class=\"$class\">
                <td style=\"color:#000080\" class=\"centerAlign\">".$row["encounter_nr"]."</td>
                <td style=\"color:#000080\" class=\"centerAlign\">".$row["code"]."</td>
                <td style=\"color:#000080\" class=\"centerAlign\">".$row["description"]."</td>
                <td style=\"color:#000080\" class=\"centerAlign\">".$row["package"]."</td>
                <td style=\"color:#000080\" class=\"centerAlign\">".$row["DateAdmitted"]."</td>
                <td style=\"color:#000080\" class=\"centerAlign\">".$row["DateDischarged"]."</td>
                <td style=\"color:#000080\" class=\"centerAlign\">".$row["daydifferent"]."</td>
            				</tr>\n";
            
        }
        if (!$rows)
            $rows = '        <tr><td colspan="10">No unserved requests found for this patient...</td></tr>';
    }
    else
        $rows = '        <tr><td colspan="10">No Previous Package</td></tr>';
//}

if (!$rows)
    $rows = '        <tr><td colspan="10">Select patient record and click Search to begin query...</td></tr>';


ob_start();
?>
<br>
<form action="<?= $thisfile.URL_APPEND."&clear_ck_sid=".$clear_ck_sid ?>" method="post" name="suchform" onSubmit="return validate()">
<div style="width:100%">
    <div class="">
        <table class="segList" width="100%" border="0" cellpadding="0" cellspacing="0">
            <thead>
                <tr>
                    <th width="15%" nowrap="nowrap">Case No.</th>
                    <th width="10%" nowrap="nowrap">Package Code</th>
                    <th width="15%" nowrap="nowrap">Package Name</th>
                    <th width="15%" nowrap="nowrap">Package Limit</th>
                    <th width="15%" nowrap="nowrap">Date Admitted</th>
                    <th width="15%" nowrap="nowrap">Date Discharged</th>
                    <th width="15%" nowrap="nowrap">Days</th>
                    <!--<th width="1%">&nbsp;</th>-->
                </tr>
            </thead>
            <tbody>
<?= $rows ?>
            </tbody>
        </table>
    </div>
</div>
<br />
</form>

<?php

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

$sTemp = ob_get_contents();
ob_end_clean();

# Assign the form template to mainframe

 $smarty->assign('sMainFrameBlockData',$sTemp);

 /**
 * show Template
 */
 $smarty->display('common/mainframe.tpl');
?>
