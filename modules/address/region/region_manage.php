<?php
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
require($root_path.'include/inc_environment_global.php');
/**
* CARE2X Integrated Hospital Information System Deployment 2.1 - 2004-10-02
* GNU General Public License
* Copyright 2002,2003,2004,2005 Elpidio Latorilla
* elpidio@care2x.org, 
*
* See the file "copy_notice.txt" for the licence notice
*/
define('LANG_FILE','place.php');

$local_user='aufnahme_user';
require_once($root_path.'include/inc_front_chain_lang.php');

#$breakfile=$root_path."main/spediens.php".URL_APPEND; # burn commented: Feb. 20, 2006
$breakfile=$address_menu.URL_APPEND; # burn added: Feb. 20, 2006


# Start Smarty templating here
 /**
 * LOAD Smarty
 */
 # Note: it is advisable to load this after the inc_front_chain_lang.php so
 # that the smarty script can use the user configured template theme

 require_once($root_path.'gui/smarty_template/smarty_care.class.php');
 $smarty = new smarty_care('system_admin');

# Title in toolbar
 $smarty->assign('sToolbarTitle',"$segRegion :: $LDManager");

 # href for help button
 $smarty->assign('pbHelp',"javascript:gethelp('address_manage.php')");

 # href for close button
 $smarty->assign('breakfile',$breakfile);

 # Window bar title
 $smarty->assign('sWindowTitle',"$segRegion :: $LDManager");

# Buffer page output

/*  removed by jasper 12/14/12

ob_start();
?>

  <p><br>
  
  <table border=0 cellpadding=5>
    <tr>
      <td><!-- <a href="region_new.php<?php echo URL_APPEND; ?>"><img <?php  echo createComIcon($root_path,'form_pen.gif','0'); ?>></a> --></td>
      <td>
	  		<a href="region_new.php<?php echo URL_APPEND; ?>"><b><font color="#990000"><?php echo $LDNewData; ?></font></b></a><br>
	  		<?php echo $segRegionNewDataTxt ?></td>
    </tr>
    <tr>
      <td><!-- <a href="region_list.php<?php echo URL_APPEND; ?>"><img <?php  echo createComIcon($root_path,'form_pen.gif','0'); ?>></a> --></td>
      <td>
	  		<a href="region_list.php<?php echo URL_APPEND; ?>"><b><font color="#990000"><?php echo $LDListAll ?></font></b></a><br>
			<?php echo $segRegionListAllTxt ?></td>
    </tr>
    <tr>
      <td><!-- <a href="region_search.php<?php echo URL_APPEND; ?>"><img <?php  echo createComIcon($root_path,'search_glass.gif','0'); ?>></a> --></td>
      <td>
	  	<a href="region_search.php<?php echo URL_APPEND; ?>"><b><font color="#990000"><?php echo $LDSearch ?></font></b></a><br>
			<?php echo $segRegionSearchTxt ?></td>
    </tr>
  </table>
<p>
<ul>
<a href="<?php echo $breakfile ?>"><img <?php echo createLDImgSrc($root_path,'close2.gif','0') ?> border="0"></a>
</ul>

<?php

$sTemp = ob_get_contents();
ob_end_clean();

removed by jasper 12/14/12
*/


//added by jasper 12/26/12
$smarty->assign('SubMenuTitle','Region Manager');



$smarty2 = new smarty_care('common', FALSE);

$arrSubMenuIcon=array(createComIcon($root_path,'adrsbook_regn.gif','0'),
                      createComIcon($root_path,'list.gif','0'),
                      createComIcon($root_path,'search_plus.gif','0')
                     );

$arrSubMenuItem=array('AddressNew'=>'<a href="'.$root_path.'modules/address/region/region_new.php'.URL_APPEND.'">'.$LDNewData.'</a>',
                      'AddressList'=>'<a href="'.$root_path.'modules/address/region/region_list.php'.URL_APPEND.'">'.$LDListAll.'</a>',
                      'AddressSearch'=>'<a href="'.$root_path.'modules/address/region/region_search.php'.URL_APPEND.'">'.$LDSearch.'</a>'
                     );

$arrSubMenuText=array('Enter new region data',
                      'List all available region data',
                      'Search for a region data'
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

//added by jasper 12/26/12


# Assign page output to the mainframe template

//$smarty->assign('sMainFrameBlockData',$sTemp);

$smarty->assign('sMainBlockIncludeFile','common/submenu_addressdata.tpl');
 /**
 * show Template
 */
 $smarty->display('common/mainframe.tpl');

?>
