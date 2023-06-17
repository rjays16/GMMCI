<?php
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
require($root_path.'include/inc_environment_global.php');

define('LANG_FILE','edp.php');
$local_user='ck_edv_user';
require_once($root_path.'include/inc_front_chain_lang.php');

require_once($root_path.'include/care_api_classes/class_pharma_product.php');
$itmobj=& new SegPharmaProduct;

$breakfile='seg_itemtypes_list.php'.URL_APPEND;

if(!isset($mode)) $mode='';

$type_nr = $_GET['nr'];
if(!empty($mode)){

    $is_img=false;
    switch($mode)
    {    
        case 'create': 
        {    
            $data = array(
                'name'=>$_POST["name"],
                'description'=>$_POST["description"],
                'prod_class'=>$_POST['prod_class'],
                'is_inactive'=>$_POST['is_inactive'],
                'modify_id'=>$_SESSION['sess_user_name'],
                'create_id'=>$_SESSION['sess_user_name']);            
                     
            $itmobj->setDataArray($data);
            if($itmobj->saveItemType($data)){ 
                header("location:seg_itemtypes_list.php".URL_REDIRECT_APPEND."&edit=1&mode=update");
                exit;
            }else{
                echo "<br>$LDDbNoSave";
            }    
            
            break;
        }    
        case 'update':
        { 
            $data = array(
                'name'=>$_POST["name"],
                'description'=>$_POST["description"],
                'prod_class'=>$_POST['prod_class'],
                'is_inactive'=>$_POST['is_inactive'],
                'modify_id'=>$_SESSION['sess_user_name']);    
                            
            $itmobj->useItemType();
            $itmobj->setDataArray($data);
            if($itmobj->updateDataFromInternalArray($_POST['nr'])){
                header("location:seg_itemtypes_list.php".URL_REDIRECT_APPEND."&edit=1&mode=update");
                exit;
            }else{
                 echo "<br>$LDDbNoSave";
            }
            
            break;
        }
        
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

if ($type_nr)
    $LDCreate = "Update";

# Title in toolbar
 $smarty->assign('sToolbarTitle','Item Types :: '.$LDCreate.'');

 # href for help button
 #$smarty->assign('pbHelp',"javascript:gethelp('dept_create.php')");

 # href for close button
 $smarty->assign('breakfile',$breakfile);

 # Window bar title
 $smarty->assign('sWindowTitle','Item Types :: '.$LDCreate.'');

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
    if (d.name.value == "") {
        alert("Pls. enter a name for the item type!");
        d.name.focus();
        return false;
    } else if (d.description.value == "") {
        alert("Pls. enter a description for the item type!");
        d.description.focus();
        return false;
    } else if (d.prod_class.value == "") {
        alert("Pls. select the product classification for the item type!");
        d.class_option.focus();
        return false;
    } else
        return true;    
}

function jsOptionChange(value) {
    document.getElementById('prod_class').value = value;
}

function setIsInactiveFlag() {
    document.getElementById('is_inactive').value = document.getElementById('chk_inactive').checked ? "1" : "0";
    
}

//---------------------------------
// -->
</script>

<?php

$sTemp = ob_get_contents();
ob_end_clean();

$smarty->append('JavaScript',$sTemp);

$typeinfo = $itmobj->getItemType($type_nr);

#die(print_r($typeinfo,true));

# Buffer page output

ob_start();

?>

<ul>
<body onLoad="">
<font face="Verdana, Arial" size=-1><?php echo $LDEnterAllFields ?>
<form action="seg_itemtype_new.php" method="post" name="area" ENCTYPE="multipart/form-data" onSubmit="return chkForm(this)">
<table border=0>

  <tr>
    <td width="22%" class=pblock align=right bgColor="#eeeeee"><font color=#ff0000><b>*</b>
            Name</font>: 
     </td>
    <td class=pblock>        
          <input name="name" id="name" type="text" size=20 maxlength=15 <?=($typeinfo['name'])?'readonly="readonly"':''?> value="<?php echo trim($typeinfo['name']); ?>">
    </td>
  </tr>
  <tr>
    <td class=pblock align=right bgColor="#eeeeee"><font color=#ff0000><b>*</b>
            Description</font>: 
    </td>
    <td class=pblock>        
          <input name="description" id="description" type="text" size=60 value="<?php echo trim($typeinfo['description']); ?>">
    </td>
  </tr>
  <tr>
    <td class=pblock align=right bgColor="#eeeeee"><font color=#ff0000><b>*</b>
            Classification</font>: 
    </td>
    <td class=pblock>
          <select id="class_option" name="class_option" onchange="jsOptionChange(this.options[this.selectedIndex].value);">              
            <?php
                echo $itmobj->getProdClassOption($typeinfo['prod_class']);                
            ?>
          </select>
    </td>
  </tr>
  <tr>
    <td class=pblock align=right bgColor="#eeeeee"><font color=#ff0000><b>*</b>
            InActive?</font>: 
     </td>
    <td class=pblock>
        <input name="chk_inactive" id="chk_inactive" type="checkbox" <?php echo ($typeinfo['is_inactive'] != 0 ? "checked" : ""); ?> onclick="setIsInactiveFlag();" >
    </td>
  </tr>  
 </table>

<input type="hidden" name="sid" value="<?php echo $sid ?>">
<input type="hidden" name="edit" value="<?php echo $edit ?>">
<input type="hidden" name="lang" value="<?php echo $lang ?>">

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
if ($type_nr){
?>
            <input type="hidden" name="mode" id="mode" value="update">
<?php }else{ ?>    
            <input type="hidden" name="mode" id="mode" value="create">
<?php } ?>            

<input type="hidden" name="nr" id="nr" value="<?php echo $type_nr; ?>">
<input type="hidden" name="prod_class" id="prod_class" value="">
<input type="hidden" name="is_inactive" id="is_inactive" value="<?php echo ($typeinfo['is_inactive'] != 0 ? "1" : "0"); ?>">
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
