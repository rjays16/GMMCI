<?php
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require_once('./roots.php');
require_once($root_path.'include/inc_environment_global.php');
/**
* CARE2X Integrated Hospital Information System beta 1.0.08 - 2003-10-05
* GNU General Public License
* Copyright 2002,2003,2004,2005 Elpidio Latorilla
* elpidio@care2x.org, 
*
* See the file "copy_notice.txt" for the licence notice
*/
$lang_tables=array('departments.php');

define('LANG_FILE','abteilung.php');
define('NO_2LEVEL_CHK',1);

require_once($root_path.'include/inc_front_chain_lang.php');

if(!session_is_registered('sess_path_referer')) session_register('sess_path_referer');

$HTTP_SESSION_VARS['sess_user_origin']='dept';

$default_url_news='modules/supply_office/seg-supply-office-request.php';

$returnfile=$root_path.$HTTP_SESSION_VARS['sess_path_referer'].URL_APPEND;
$breakfile=$root_path.'main/startframe.php'.URL_APPEND;

$HTTP_SESSION_VARS['sess_path_referer']=$top_dir.basename(__FILE__);

//$db->debug=1;
/* dept type = 1 = medical */

$sql="SELECT dept.nr, dept.description, dept.name_formal, dept.LD_var AS \"LD_var\",
                        dept.work_hours, dept.consult_hours, 
                        reg.news_start_script 
                        FROM care_department as dept LEFT JOIN care_registry AS reg ON dept.id=reg.registry_id 
                        WHERE dept.type=1 AND dept.is_inactive IN ('',0) ORDER BY name_formal";

//$sql='SELECT nr, name_formal, work_hours, consult_hours, url_news FROM care_department WHERE  is_inactive=0 ORDER BY sort_order';
    
if ($result = $db->Execute($sql)) {
    $rows = $result->RecordCount();
}

# Start Smarty templating here
 /**
 * LOAD Smarty
 */

 # Note: it is advisable to load this after the inc_front_chain_lang.php so
 # that the smarty script can use the user configured template theme

 require_once($root_path.'gui/smarty_template/smarty_care.class.php');
 $smarty = new smarty_care('common');

# Toolbar title

 $smarty->assign('sToolbarTitle',$LDPageTitle);

 # href for help button
 $smarty->assign('pbHelp',"javascript:gethelp()");
 # href for close file
 $smarty->assign('breakfile',$breakfile);
 # href for return file
 //$smarty->assign('pbBack',$returnfile);

 # Window title
 $smarty->assign('title',$LDPageTitle);

 # Buffer the page output

 ob_start();
echo "name = ".$HTTP_SESSION_VARS['sess_login_username'];    
?>

<ul>
    <table border=0 cellspacing=0 cellpadding=0>
        <tr>
            <td>
                <table border=0 cellspacing=1 cellpadding=3>
                    <tr bgcolor=#ffffff background="../../gui/img/common/default/tableHeaderbg.gif">
                        <td class="segPanelHeader">Deparment</td>
                        <td class="segPanelHeader">Description</td>                        
                    </tr>
<?php

$toggle=0;

if($rows) {

    while ($dept=$result->FetchRow()) {
    
        if (empty($dept['news_start_script'])) $dept['news_start_script']=$default_url_news;
        echo '<tr bgcolor="#ffffff" ';
        
        if($toggle) {
            echo ' background="../../gui/img/common/default/tableHeaderbg3.gif"';
        }
        
        $toggle=!$toggle;
        
        echo '><td class="segPanel"><a href="'.$root_path.$dept['news_start_script'].URL_APPEND.'&dept_nr='.$dept['nr'].'"> ';
        
        $buf=$dept['LD_var'];
        if(isset($$buf)&&!empty($$buf)) echo $$buf;
            else echo $dept['name_formal'];

        echo '</a></td>
        <td class="segPanel">'.$dept['description'].'</td>
        </tr>';
        echo "\r\n";
    }
}
?>
                </table>
            </td>
        </tr>
    </table>
<p>
<a href="<?php echo $breakfile ?>"><img <?php echo createLDImgSrc($root_path,'close2.gif','0') ?>  alt="<?php echo $LDClose ?>" align="absmiddle"></a>
<p>
</ul>

<?php

    $sTemp = ob_get_contents();

ob_end_clean();

$smarty->assign('sMainFrameBlockData',$sTemp);

$smarty->display('common/mainframe.tpl');

?>

