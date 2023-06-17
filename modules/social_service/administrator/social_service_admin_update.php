<?php
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
require($root_path.'include/inc_environment_global.php');

define('LANG_FILE','social_service.php');
$local_user='aufnahme_user';
$thisfile=basename(__FILE__);
//$breakfile='social_service_pass.php';
$breakfile =$root_path.'/modules/social_service/administrator/social_service_admin'.URL_APPEND;


require($root_path.'include/inc_front_chain_lang.php');

# Start Smarty templating here
 require_once($root_path.'gui/smarty_template/smarty_care.class.php');
 $smarty = new smarty_care('common');

# Title in the toolbar
 $smarty->assign('sToolbarTitle',$swSocialService);

 # href for help button
 $smarty->assign('pbHelp',"javascript:gethelp('medocs_start.php')");

 $smarty->assign('breakfile',$breakfile);

 # Window bar title
 $smarty->assign('title',$swSocialService);

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
<!-- javascript code here -->
<?php 

$sTemp = ob_get_contents();
ob_end_clean();
$smarty->append('JavaScript',$sTemp);

require_once($root_path.'include/care_api_classes/class_social_service.php');
//Instantiate social service class
$ss = new SocialService;


if(!isset($mode)){
	$mode ='';
}elseif($HTTP_POST_VARS['mode']== 'save'){
	//save updated data
	//$HTTP_POST_VARS['service_discount'] = $HTTP_POST_VARS['service_discount0']; 
	//echo "service_discount=".$HTTP_POST_VARS['service_discount'];
	//echo "_POST['count']=".$_POST['count']."<br>";
	$redirect = FALSE;
	for($c=1 ; $c<$_POST['count']; $c++){
		$HTTP_POST_VARS['modify_id'] = $HTTP_POST_VARS['sess_en'];
		$HTTP_POST_VARS['modify_time']= date('Y-m-d H:i:s');
		$HTTP_POST_VARS['service_discount'] = $HTTP_POST_VARS['service_discount'.$c];
		//echo '<br>service_discount'.$c."=";
	//	echo $HTTP_POST_VARS['service_discount'];
		 if($ss->updateSocialService($c,$HTTP_POST_VARS)){
		// 	echo $ss->sql."\n";
			$HTTP_POST_VARS['service_discount']="";
			if($c>$POST['count']){
				$redirect = TRUE;
			}
		}
	}
	
	if($redirect){
		header("location:social_service_admin.php?sid=$sid&lang=$lang&mode=show");
		exit;						
	}
		
}else{
	//show display data
	$rows = $ss->getSSInfo();
}

//$rows = $ss->getSSInfo();
ob_start();

?>
<ul>
<script type="text/javascript">
</script>
<form action="<?= $thisfile?>"method="post" onSubmit="return true">
<table width="358" height="160" border="1" cellpadding="0" cellspacing="0" bordercolor="#003366">
  <tr>
    <td width="348" height="23"><span class="style1">Social Service Classification</span> </td>
  </tr>
  <tr>
    <td width="100%" height="111" valign="top">
      <table width="100%" border="0">
        <tr>
          <td><span class="style2">Type Discount</span> </td>
        </tr>
        <tr>
          <td height="79">
	           	<table width="250" border="0">
	             <?php $count=1; 
	             	while($code=$rows->FetchRow()){?>
		              <tr>
		                <td><label for="a"><?=$code['service_desc'] ?>  - </label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="text" id="service_discount<?=$count?>" name="service_discount<?=$count?>" size="5"  value="<?=$code['service_discount']?>" /></td>
		              </tr>
	             <?php $count++;
					}?>
	            </table>
	            <input type="hidden" name="mode" value = "save">
	            <input type="hidden" name="count" value = "<?=$count?>">
          </td>
        </tr>
      </table>
    </td>
  </tr>
</table>
<table width="60" height="20">
	<tr>
		 <td><input type="image" <?php echo createLDImgSrc($root_path,'savedisc.gif','0'); ?>></td>	 
		<td align=right><a href="<?php echo $breakfile;?>"><img <?php echo createLDImgSrc($root_path,'cancel.gif','0') ?> alt="<?php echo $LDCancelClose ?>"></a> 
		</td>
	</tr>
</table>
 </form>
<p>
</ul>

<?php

$sTemp = ob_get_contents();

$smarty->assign('sMainDataBlock',$sTemp);

ob_end_clean();


$smarty->assign('sMainBlockIncludeFile','medocs/main_plain.tpl');

$smarty->display('common/mainframe.tpl');


?>