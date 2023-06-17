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
if($glob_obj->getConfig('date_format')) $date_format=$GLOBAL_CONFIG['date_format'];
$date_format=$GLOBAL_CONFIG['date_format'];
$phpfd=$date_format;
$phpfd=str_replace("dd", "%d", strtolower($phpfd));
$phpfd=str_replace("mm", "%m", strtolower($phpfd));
$phpfd=str_replace("yyyy","%Y", strtolower($phpfd));
$phpfd=str_replace("yy","%Y", $phpfd);

$php_date_format = strtolower($date_format);
$php_date_format = str_replace("dd","d",$php_date_format);
$php_date_format = str_replace("mm","m",$php_date_format);
$php_date_format = str_replace("yyyy","Y",$php_date_format);
$php_date_format = str_replace("yy","y",$php_date_format); 

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

$login_id = $_GET['login_id'];
$from_date = $_GET['from_date'];
$to_date = $_GET['to_date'];
$from_or = $_GET['or_from'];
$to_or = $_GET['or_to'];
$status = $_GET['status'];
$submit = $_POST['submit_val'];

if($status=="update"){
    $rd = "disabled = 'disabled'";
    $mode = "update";
}
elseif($status=="add"){
    $from_date = date("m/d/Y");
    $to_date = date("m/d/Y");
}
    
if($submit=="update"){
    if($login_id == '')
        $login_id = $_POST['login_user']; 
    $old_from_date = date("Y-m-d",strtotime($_POST["old_from_date"]));
    $old_to_date = date("Y-m-d",strtotime($_POST["old_to_date"]));
    $old_from_or = $_POST["old_from_or"];
    $old_to_or = $_POST["old_to_or"];
    $from_date = date("Y-m-d",strtotime($_POST["fromdate"]));
    $to_date = date("Y-m-d",strtotime($_POST["todate"]));
    $from_or = $_POST['fromor'];
    $to_or = $_POST['toor'];
    $vat = $_POST['vat'];
    $sql = "UPDATE seg_assigned_ornos SET modify_id='".$_SESSION['sess_temp_userid']."', modify_time=NOW(),from_date='".date("Y-m-d",strtotime($_POST["fromdate"]))."',to_date='".date("Y-m-d",strtotime($_POST["todate"]))."',or_from='".$_POST['fromor']."',or_to='".$_POST['toor']."', is_vat = '$vat' WHERE login_id='$login_id' AND from_date='$old_from_date' AND to_date='$old_to_date' AND or_from='$old_from_or' AND or_to='$old_to_or' AND is_deleted=0";
    $result = $db->Execute($sql);

    $mode = 'update';
    $status = 'update';
}
elseif($submit=="add"){
    $login_id = $_POST['user'];
    $from_date = date("Y-m-d",strtotime($_POST["fromdate"]));
    $to_date = date("Y-m-d",strtotime($_POST["todate"]));
    $from_or = $_POST['fromor'];
    $to_or = $_POST['toor'];
    $vat = $_POST['vat'];
    $sql = "INSERT INTO seg_assigned_ornos(login_id, from_date, to_date, is_locked, is_deleted, or_from, or_to, create_id, create_time, is_vat) VALUES('$login_id','$from_date','$to_date',0,0,'$from_or','$to_or','".$_SESSION['sess_temp_userid']."',NOW(), '$vat')";
    $result = $db->Execute($sql);

    $mode = 'update';
    $status = 'update';
}
elseif($submit=="delete"){
    if($login_id == '')
        $login_id = $_POST['login_user']; 
    $from_date = $_POST['old_from_date'];
    $to_date = $_POST['old_to_date'];
    $from_or = $_POST["old_from_or"];
    $to_or = $_POST["old_to_or"];
    $sql = "UPDATE seg_assigned_ornos SET is_deleted=1 WHERE login_id='$login_id' AND from_date='$from_date' AND to_date='$to_date' AND or_from='$from_or' AND or_to='$to_or' AND is_deleted=0";
    $result = $db->Execute($sql);
    echo "<script language='javascript' >window.parent.location='seg-or-assign.php'</script>";

    $mode = 'add';
}

$sql = "SELECT sao.login_id, cu.name, sao.from_date,sao.to_date, sao.or_from, sao.or_to, sao.is_locked, sao.is_vat,
        (SELECT COUNT(or_no) FROM seg_pay WHERE CAST(or_no AS UNSIGNED) > CAST(sao.or_from AS UNSIGNED) AND CAST(or_no AS UNSIGNED) <= CAST(sao.or_to AS UNSIGNED)) as or_used
        FROM seg_assigned_ornos AS sao
        LEFT JOIN care_users AS cu ON sao.login_id = cu.login_id
        WHERE is_deleted=0 AND sao.login_id='$login_id' AND sao.from_date='$from_date' AND sao.to_date='$to_date' AND sao.or_from='$from_or' AND sao.or_to='$to_or'";
$rs = $db->Execute($sql);
if($rs!=NULL && $result = $rs->FetchRow())
    extract($result);

# Collect javascript code
 ob_start()

?>

<!--added by VAN 02-06-08-->
<!--for shortcut keys -->
<script type="text/javascript" src="<?=$root_path?>js/shortcut.js"></script>

<script type="text/javascript" src="<?=$root_path?>js/jsprototype/prototype.js"></script>
<script language="javascript" >
<!--

function preSet(){
    document.getElementById('search').focus();
}


//---------------adde by VAN 02-06-08

//------------------------------------------

//--------------added by VAN 09-12-07------------------
var currentPage=0, lastPage=0;
var FIRST_PAGE=1, PREV_PAGE=2, NEXT_PAGE=3, LAST_PAGE=4, SET_PAGE=0;
var AJAXTimerID=0;
var lastSearch="";
  
function refreshWindow(){
    //window.location.href=window.location.href;
}
                            
function ReloadWindow(){
    window.location.href=window.location.href;
}

function delete_confirm(val){
    var msg = confirm('Are you sure you want to delete OR assignment?');

    if(msg){
        document.getElementById("submit_val").value = val;
    }
}

function validate(){
    var fromdate = document.getElementById("fromdate");
    var todate = document.getElementById("todate");
    var fromor = document.getElementById("fromor");
    var toor = document.getElementById("toor");
    if(fromdate.value > todate.value){
        alert("Effectivity start date should not be after the end date.");
        return false;
    }
    if(fromor.value > toor.value){
        alert("OR starting number should be less than OR ending number.");
        return false;
    }
    return true;
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

require_once($root_path.'include/care_api_classes/class_labservices_transaction.php');
$srvObj=new SegLab();

require_once($root_path.'include/care_api_classes/class_hclab_oracle.php');
$hclabObj = new HCLAB;

ob_start();
?>

<a name="pagetop"></a>
<br>
<div style="padding-left:10px">
<form action="<?php echo $thisfile; ?>" method="post" name="suchform" onSubmit="return validate();">
    <div id="tabFpanel">
        <table border="0" cellspacing="2" cellpadding="2" width="99%" align="center">
            <tr>
                <td class="segPanelHeader" align="left" colspan="2"> OR Assignment Details </td>
            </tr>
            <tr>
                <td width="35%" class="segPanel"><strong>User</strong></td>
                <td class="segPanel"><select name="user" <?php echo $rd; ?>>
                <?php
                    $sql = "SELECT name, login_id FROM care_users AS cu
                            LEFT JOIN care_personell_assignment AS cpa ON cu.personell_nr = cpa.personell_nr
                            WHERE cpa.location_nr=170";
                    $rs = $db->Execute($sql);
                    while($rs!= NULL && $result = $rs->FetchRow()){
                        if($login_id ==$result["login_id"])
                            echo "<option value='".$result["login_id"]."' selected='selected'>".$result["name"]."</option>";
                        else
                            echo "<option value='".$result["login_id"]."' >".$result["name"]."</option>";
                    }
                ?></select></td>
            </tr>
            <tr>
                <td width="35%" class="segPanel"><strong>Date of Effectivity</strong></td>
                <td class="segPanel"><input class="jedInput" name="fromdate" id="fromdate" type="text" size="8" value="<?=strftime("%m/%d/%Y ",strtotime($from_date))?>"/>
                                    <img src="<?= $root_path ?>gui/img/common/default/show-calendar.gif" id="tg_fromdate" align="absmiddle" style="cursor:pointer"  />
                                    <script type="text/javascript">
                                        Calendar.setup ({
                                            inputField : "fromdate", ifFormat : "<?= $phpfd ?>", showsTime : false, button : "tg_fromdate", singleClick : true, step : 1
                                        });
                                    </script>to 
                                    <input class="jedInput" name="todate" id="todate" type="text" size="8" value="<?=strftime("%m/%d/%Y ",strtotime($to_date))?>"/>
                                    <img src="<?= $root_path ?>gui/img/common/default/show-calendar.gif" id="tg_todate" align="absmiddle" style="cursor:pointer"  />
                                    <script type="text/javascript">
                                        Calendar.setup ({
                                            inputField : "todate", ifFormat : "<?= $phpfd ?>", showsTime : false, button : "tg_todate", singleClick : true, step : 1
                                        });
                                    </script></td>
            </tr>
            <tr>
                <td class="segPanel"><strong>OR Series</strong></td>
                <td class="segPanel"><input class="jedInput" name="fromor" id="fromor" type="text" size="5" value="<?=$or_from?>"/>&nbsp;-&nbsp;<input class="jedInput" name="toor" id="toor" type="text" size="5" value="<?=$or_to?>"/></td>
            </tr> 
            <tr>
                <td class="segPanel"><strong>Vatable OR</strong></td>
                <td class="segPanel">&nbsp;Yes&nbsp;<input class="jedInput" name="vat" type="radio" size="5" value="1" <?php if($is_vat){?> checked <?php }?>/>&nbsp;&nbsp;No<input class="jedInput" name="vat" id="vat" type="radio" size="5" value="0" <?php if(!$is_vat){?> checked <?php }?>/></td>
            </tr>  
        </table>
    </div>
</div>
    <input type="hidden" name="old_from_date" id="old_from_date" value="<?= $from_date?>">
    <input type="hidden" name="old_to_date" id="old_to_date" value="<?= $to_date?>">
    <input type="hidden" name="old_from_or" id="old_from_date" value="<?= $or_from?>">
    <input type="hidden" name="old_to_or" id="old_to_date" value="<?= $or_to?>">
    <input type="hidden" name="submit_val" id="submit_val" value="<?= $submit_val?>">
    <?php if($login_id){
        $login_user = $login_id;
    }?>
    <input type="hidden" name="login_user" id="login_user" value="<?= $login_user?>">
<?php
    if($mode=="update"){?>
        <input type="image" name="submit" id="submit" value="update" src="../../images/btn_save.gif" onclick="$('submit_val').value = this.value;">&nbsp;&nbsp;&nbsp;<input type="image" name="submit" id="submit" value="delete" src="../../images/btn_delete.gif" onclick="return delete_confirm(this.value);">
    <?php } else{ ?>
        <input type="image" name="submit" id="submit" value="add" src="../../images/btn_save.gif" onclick="$('submit_val').value = this.value;$('login_user').value = $('user').value;">
<?php }?>
</form>
<br />
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