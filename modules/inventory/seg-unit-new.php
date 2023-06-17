<?php
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
require($root_path.'include/inc_environment_global.php');

define('LANG_FILE','edp.php');
$local_user='ck_edv_user';
require_once($root_path.'include/inc_front_chain_lang.php');
/* Load the insurance object */

require_once($root_path.'include/care_api_classes/inventory/class_unit.php');
$unit_obj = new Unit();

$breakfile='seg-units-list.php'.URL_APPEND;

if(!isset($mode)) $mode='';

$unit_id = $_GET['unitid'];
//echo "code = ".$unit_id;
if(!empty($mode)) {    
    $unit_obj->setDataArray($_POST);
    switch($mode)
    {    
        case 'create':            
            if ($unit_obj->insertDataFromInternalArray()) {
                header("location:seg-units-list.php".URL_REDIRECT_APPEND."&edit=1&mode=update");
                exit;
            } else
                echo "<br>ERROR: ".$unit_obj->getErrorMsg();       
            break;

        case 'update':
            $unit_obj->setWhereCondition("unit_id = '".$_POST["unit_id"]."'");
            if ($unit_obj->updateDataFromInternalArray($_POST["unit_id"], FALSE)) {
                header("location:seg-units-list.php".URL_REDIRECT_APPEND."&edit=1&mode=update");
                exit;
            } else
                echo "<br>ERROR: ".$unit_obj->getErrorMsg();            
                            
            break;
        
    }// end of switch
}

# Start Smarty templating here
 /**
 * LOAD Smarty
 */
 # Note: it is advisable to load this after the inc_front_chain_lang.php so
 # that the smarty script can use the user configured template theme

 require_once($root_path.'gui/smarty_template/smarty_care.class.php');
 $smarty = new smarty_care('system_admin');

if ($unit_id)
    $LDCreate = "Update";

# Title in toolbar
 $smarty->assign('sToolbarTitle','Units :: '.$LDCreate.'');

 # href for help button
 $smarty->assign('pbHelp',"javascript:gethelp('dept_create.php')");

 # href for close button
 $smarty->assign('breakfile',$breakfile);

 # Window bar title
 $smarty->assign('sWindowTitle','Units :: '.$LDCreate.'');

# Buffer page output

ob_start();
?>

<style type="text/css" name="formstyle">

td.pblock{ font-family: verdana,arial; font-size: 12}
div.box { border: solid; border-width: thin; width: 100% }
div.pcont{ margin-left: 3; }

</style>

<script language="javascript">
<!-- 

function chkForm(d){
    if (d.unit_name == "") {
        alert("Pls. enter the unit name!");
        d.unit_name.focus();
        return false;
    } else if (d.unit_desc == "") {
        alert("Pls. enter the unit description!");
        d.unit_desc.focus();
        return false;    
    }
    
    return true;    
}

function toggleCheckBox(d) {
    d.value = (d.checked) ? "1" : "0";
}

//---------------------------------
// -->
</script>

<?php

$sTemp = ob_get_contents();
ob_end_clean();

$smarty->append('JavaScript',$sTemp);

if ($result = $unit_obj->getUnitInfo($unit_id)) {
    extract($result);
}

# Buffer page output
ob_start();

?>

 <ul>
 <body onLoad="">
<font face="Verdana, Arial" size=-1><?php echo $LDEnterAllFields ?>
<form action="seg-unit-new.php" method="post" name="unit" ENCTYPE="multipart/form-data" onSubmit="return chkForm(this)">
<table border=0>

  <tr>
    <td class=pblock align=right bgColor="#eeeeee"><font color=#ff0000><b>*</b>
            Unit Name</font>: 
     </td>
    <td class=pblock>
          <input name="unit_name" id="unit_name" type="text" size=20 maxlength=10 value="<?php echo trim($unit_name); ?>">
    </td>
  </tr>
  <tr>
    <td class=pblock align=right bgColor="#eeeeee"><font color=#ff0000><b>*</b>
            Is small unit?</font>: 
     </td>
    <td class=pblock>
        <input type="checkbox" name="is_unit_per_pc" id="is_unit_per_pc" onclick="toggleCheckBox(this);" value="<?= ($is_unit_per_pc == 0) ? "0" : "1" ?>" <?= ($is_unit_per_pc == 0) ? "" : "checked" ?>>Check if YES.<br>        
    </td>
  </tr>      
  <tr>
    <td class=pblock align=right bgColor="#eeeeee"><font color=#ff0000><b>*</b>
            Description</font>: 
     </td>
    <td class=pblock>
          <input name="unit_desc" id="unit_desc" type="text" size=60 value="<?php echo trim($unit_desc); ?>">
    </td>
  </tr>
  <tr>
    <td class=pblock align=right bgColor="#eeeeee"><font color=#ff0000><b>*</b>
            Default?</font>: 
     </td>
    <td class=pblock>
        <input type="checkbox" name="is_default" id="is_default" onclick="toggleCheckBox(this);" value="<?= ($is_default == 0) ? "0" : "1" ?>" <?= ($is_default == 0) ? "" : "checked" ?>>Check if YES.<br>        
    </td>
  </tr>  
  <tr>
    <td class=pblock align=right bgColor="#eeeeee"><font color=#ff0000><b>*</b>
            Inactive?</font>: 
     </td>
    <td class=pblock>
        <input type="checkbox" name="is_deleted" id="is_deleted" onclick="toggleCheckBox(this);" value="<?= ($is_deleted == 0) ? "0" : "1" ?>" <?= ($is_deleted == 0) ? "" : "checked" ?>>Check if DELETED.<br>        
    </td>
  </tr>      
 </table>

<input type="hidden" name="unit_id" value="<?php echo $unit_id ?>">
<input type="hidden" name="modify_id" value="<?php echo $modify_id ?>">
<!--
<?php
 if($mode=='select') {
?>
<input type="hidden" name="mode" value="update">

<input type="image" <?php echo createLDImgSrc($root_path,'savedisc.gif','0'); ?>>
<?php
}
else
{
?>
<input type="hidden" name="mode" value="create">
 
<input type="submit" value="<?php echo $LDCreate ?>">
<?php
}
?>
-->
<?php
       if ($unit_id){
?>
            <input type="hidden" name="mode" id="mode" value="update">
<?php }else{ ?>    
            <input type="hidden" name="mode" id="mode" value="create">
<?php } ?>            

<input type="submit" value="<?php echo $LDSave ?>">

</form>
<p>

<a href="javascript:history.back()"><img <?php echo createLDImgSrc($root_path,'cancel.gif','0') ?> border="0"></a>

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
</body>