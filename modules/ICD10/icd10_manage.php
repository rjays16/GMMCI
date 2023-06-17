<?php
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
require($root_path.'include/inc_environment_global.php');
/**
* SegHIS Integrated Hospital Information System Deployment 
* Copyright(c) 2007 Segworks Technologies Corporation
*/
define('LANG_FILE','icd10icpm.php');
$local_user='aufnahme_user';
require_once($root_path.'include/inc_front_chain_lang.php');

$breakfile=$root_path."main/spediens.php".URL_APPEND;

# Start Smarty templating here
 /**
 * LOAD Smarty
 */
 # Note: it is advisable to load this after the inc_front_chain_lang.php so
 # that the smarty script can use the user configured template theme

 require_once($root_path.'gui/smarty_template/smarty_care.class.php');
 $smarty = new smarty_care('system_admin');

# Title in toolbar
 $smarty->assign('sToolbarTitle',"$LDicd10 :: $LDManager");

 # href for help button
 $smarty->assign('pbHelp',"javascript:gethelp('icd10_manage.php')");

 # href for close button
 $smarty->assign('breakfile',$breakfile);

 # Window bar title
 $smarty->assign('sWindowTitle',"$LDicd10 :: $LDManager");

# Buffer page output

//added by jasper 01/25/13

$smarty2 = new smarty_care('common', FALSE);

$arrSubMenuIcon=array(createComIcon($root_path,'new_1.gif','0'),
                      createComIcon($root_path,'list.gif','0'),
                      createComIcon($root_path,'search_plus.gif','0')
                     );

$arrSubMenuItem=array('sNew'=>'<a href="'.'icd10_new.php'.URL_APPEND.'">'.$LDNewData.'</a>',
                      'sList'=>'<a href="'.'icd10_list.php'.URL_APPEND.'">'.$LDListAll.'</a>',
                      'sSearch'=>'<a href="'.'icd10_search.php'.URL_APPEND.'">'.$LDSearch.'</a>'
                     );

$arrSubMenuText=array($LDNewDataTxt,
                      $LDListAllTxt,
                      $LDSearchTxt
                     );

// Create the submenu rows
$iRunner = 0;

while(list($x,$v)=each($arrSubMenuItem)){
    $sTemp='';
    ob_start();
    //if($cfg['icons'] != 'no_icon') $smarty2->assign('sIconImg','<img '.$arrSubMenuIcon[$iRunner].'>');
    $smarty2->assign('sIconImg','<img '.$arrSubMenuIcon[$iRunner].'>');
    $smarty2->assign('sSubMenuItem',$v);
    $smarty2->assign('sSubMenuText',$arrSubMenuText[$iRunner]);
    $smarty2->display('common/submenu_row.tpl');
    $sTemp = ob_get_contents();
    ob_end_clean();
    $iRunner++;
    $smarty->assign($x,$sTemp);
}

//added by jasper 01/25/13

//removed by jasper 01/25/13
/*
ob_start();
?>

  <p><br>
<!--------removed left alignment to fill the void, 10-26-2007, fdp------  
  <table border=0 align="left" cellpadding=5 bgcolor="#F2FBFF">
------------------------------------------------------------------------>
  <table border=0 cellpadding=5 bgcolor="#F2FBFF">
    <tr>
      <td><!-- <a href="citytown_new.php<?php echo URL_APPEND; ?>"><img <?php  echo createComIcon($root_path,'form_pen.gif','0'); ?>></a> --></td>
      <td>
	  		<a href="icd10_new.php<?php echo URL_APPEND; ?>"><b><font color="#990000"><?php echo $LDNewData; ?></font></b></a><br>
	  		<?php echo $LDNewDataTxt ?></td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td>
	  		<a href="icd10_list.php<?php echo URL_APPEND; ?>"><b><font color="#990000"><?php echo $LDListAll ?></font></b></a><br>
			<?php echo $LDListAllTxt ?></td>
    </tr>
    <tr>
      <td><!-- <a href="icd10_search.php<?php echo URL_APPEND; ?>"><img <?php  echo createComIcon($root_path,'search_glass.gif','0'); ?>></a> --></td>
      <td>
	  	<a href="icd10_search.php<?php echo URL_APPEND; ?>"><b><font color="#990000"><?php echo $LDSearch ?></font></b></a><br>
			<?php echo $LDSearchTxt ?></td>
    </tr>
</table>
  
<p>
<!-----commented out, redundant, 10-26-2007, fdp--------
<ul>
<a href="<?php echo $breakfile ?>"><img <?php echo createLDImgSrc($root_path,'close2.gif','0') ?> border="0"></a>
</ul>
---------------until here only---------fdp------------->

<?php

$sTemp = ob_get_contents();
ob_end_clean();

# Assign page output to the mainframe template

$smarty->assign('sMainFrameBlockData',$sTemp);
*/
//removed by jasper 01/25/13
 /**
 * show Template
 */

 $smarty->assign('sMainBlockIncludeFile','common/submenu_data.tpl');
 $smarty->display('common/mainframe.tpl');

?>