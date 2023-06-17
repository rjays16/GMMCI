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
define('LANG_FILE','specials.php');
define('NO_2LEVEL_CHK',1);
require_once($root_path.'include/inc_front_chain_lang.php');

// reset all 2nd level lock cookies
require($root_path.'include/inc_2level_reset.php');

require_once($root_path.'include/inc_config_color.php');

if($retpath=='home') $breakfile=$root_path.'main/startframe.php'.URL_APPEND;
    else $breakfile=$root_path.'main/spediens.php'.URL_APPEND;
//ajax
require($root_path."modules/price_adjustments/ajax/price_adjustments.common.php");
$xajax->printJavascript($root_path.'classes/xajax_0.5');
  
# Create global config object
require_once($root_path.'include/care_api_classes/class_globalconfig.php');
require_once($root_path.'include/inc_date_format_functions.php');   
    
$glob_obj=new GlobalConfig($GLOBAL_CONFIG);
$thisfile='seg_view_price_history.php';
     
$datum=strftime("%d.%m.%Y");
$zeit=strftime("%H.%M");
$toggler=0;

//echo "before: pmonth=".$pmonth." pyear=".$pyear; 
if($pmonth=='') $pmonth=date('n');
if($pyear=='') $pyear=date('Y');
/*if($_GET['pmonth']=='') $pmonth=date('n');
else $pmonth=$_GET['pmonth'];
if($_GET['pyear']=='') $pyear=date('Y');
else $pyear=$_GET['pyear'];

echo "after: pmonth=".$pmonth." pyear=".$pyear; */
#echo "GET: pmonth=".$_GET['pmonth']." pyear=".$_GET['pyear']; 

function getmaxdays($mon,$yr)
{
    if ($mon==2){ if (checkdate($mon,29,$yr)) return 29; else return 28;}
    else
    {
        if(checkdate($mon,31,$yr)) return 31; else return 30;
    }
}

$maxdays=getmaxdays($pmonth,$pyear);
 #echo "@59: pmonth=".$pmonth." pyear=".$pyear;
function wkdaynum($daynum,$mon,$yr){
        $jd=gregoriantojd($mon,$daynum,$yr);
        switch(JDDayOfWeek($jd,0))
            {
                case 0: return 6;
                case 1: return 0;
                case 2: return 1;
                case 3: return 2;
                case 4: return 3;
                case 5: return 4;
                case 6: return 5;
            }
    }

$daynumber=array();

for ($n=0;$n<wkdaynum(1,$pmonth,$pyear);$n++){
    $daynumber[$n]="";
}
 echo "@79: pmonth=".$pmonth." pyear=".$pyear; 
for($i=1;$i<=$maxdays;$i++)
{
    $daynumber[$n]=$i;$n++;
}
while ($n<35) 
{
    $daynumber[$n]="";
    $n++;
}

# Start Smarty templating here
 /**
 * LOAD Smarty
 */
 # Note: it is advisable to load this after the inc_front_chain_lang.php so
 # that the smarty script can use the user configured template theme

 require_once($root_path.'gui/smarty_template/smarty_care.class.php');
 $smarty = new smarty_care('system_admin');

# Title in toolbar
 $smarty->assign('sToolbarTitle','Service Price:: History');

 # href for help button
 $smarty->assign('pbHelp',"javascript:gethelp('seg_view_price_history.php')");

 # href for close button
 $smarty->assign('breakfile',$breakfile);

 # Window bar title
 $smarty->assign('sWindowTitle','Service Price:: History');

# Buffer page output


ob_start();
?>
<script type="text/javascript" src="<?=$root_path?>js/jsprototype/prototype.js"></script>
<script type="text/javascript" src="js/seg-effect-price.js?t=<?=time()?>"></script>  
<script language="javascript" >
<!-- 
var urlholder;

function update()
{

    var mbuf=document.direct.month.selectedIndex+1;
    var jbuf=document.direct.jahr.value;
    document.write("mbuf="+mbuf);
    document.write("jbuf="+jbuf);
    if(!isNaN(jbuf))
    {
    jbuf=parseInt(jbuf);
    //alert("pmonth="+mbuf+" pyear="+jbuf);
    var urltarget="seg_view_price_history.php<?php echo URL_APPEND; ?>&pmonth="+mbuf+"&pyear="+jbuf;
    //var urltarget="seg_view_price_history.php<?php echo URL_APPEND; ?>&pmonth="+mbuf+"&pyear="+jbuf+"&day="+d+"&month="+m+"&year="+y;
    window.location.replace(urltarget);
    }
    else document.direct.jahr.select();
    return false;
}

function cxjahr(offs)
{
    
    var buf=document.direct.jahr.value;
    if(offs<1) buf--; else buf++;
    if(!isNaN(buf)) 
    {
    buf=parseInt(buf);
    document.direct.jahr.value=buf;
    }
    else document.direct.jahr.select();
} 
function optionwin(d,m,y)
{
    var mbuf=document.direct.month.selectedIndex+1;
    var jbuf=document.direct.jahr.value;
    if (!d) d="";
    if(!isNaN(jbuf))
    {
    jbuf=parseInt(jbuf);
    var urltarget="seg_view_price_history.php<?php echo URL_APPEND; ?>&day="+d+"&month="+m+"&year="+y+"&pmonth="+mbuf+"&pyear="+jbuf;
    //ar urltarget="seg_view_price_history.php<?php echo URL_APPEND; ?>&day="+d+"&month="+m+"&year="+y+"&pmonth="+mbuf+"&pyear="+jbuf;
    window.location.replace(urltarget);
    }
    else document.direct.jahr.select();
    return false;
}

// -->
</script>

<?php

$sTemp = ob_get_contents();
ob_end_clean();

$smarty->append('JavaScript',$sTemp);

# Buffer page output

ob_start();

?>

<ul>

<?php 
echo '<table cellspacing=0 cellpadding=0 border=0>
        <tr><td align=left>';
echo '<a href="seg_view_price_history.php'.URL_APPEND.'&pmonth=';
if($pmonth<2) echo '12&pyear='.($pyear-1).'" title="'.$LDPrevMonth.'"><FONT  SIZE=2 color=silver><b>&lt;'.$monat[12];
else echo ($pmonth-1).'&pyear='.$pyear.'" title="'.$LDPrevMonth.'"><FONT  SIZE=2 color=silver><b>&lt;'.$monat[$pmonth-1];
echo '</a></td><td  align=center>';
echo '<FONT  SIZE=4 color=navy><b>'.$monat[(int)$pmonth].' '.$pyear.'</b></font>';
echo '</td><td align=right><FONT  SIZE=2 color=silver><b>';
echo '<a href="seg_view_price_history.php'.URL_APPEND.'&pmonth=';
if($pmonth>11) echo '1&pyear='.($pyear+1).'" title="'.$LDNextMonth.'"><FONT  SIZE=2 color=silver><b>'.$monat[1];
else echo ($pmonth+1).'&pyear='.$pyear.'" title="'.$LDNextMonth.'"><FONT  SIZE=2 color=silver><b>'.$monat[$pmonth+1];
echo '&gt;</a></td></tr><tr><td bgcolor=black colspan=3>';

echo '<table border="0" cellspacing=1 cellpadding=5 width="100%">';

echo '<tr>';
for($n=0;$n<6;$n++)
    {
        echo '<td bgcolor=white><FONT  SIZE=2 ><b>'.$tagename[$n].'</b></td>';
    }
echo '<td bgcolor="#ffffcc"><FONT SIZE=2 color=red ><b>'.$tagename[6].'</b></td>';
echo '</tr>';

$j=0;
for($x=0;$x<6;$x++)
{    echo '<tr>';
    
        for($n=0;$n<6;$n++)
        {
            if($daynumber[$j].$pmonth.$pyear==date(jnY)) echo '<td bgcolor=orange>'; else echo '<td bgcolor=white>';
            echo '<FONT face="times new roman"   SIZE=4  color=navy><b>&nbsp;<a href="javascript:optionwin(\''.$daynumber[$j].'\',\''.$pmonth.'\',\''.$pyear.'\')" title="'.$LDClk4Options.'">'.$daynumber[$j].' </a></b></td>'; $j++;
        }
    if($daynumber[$j].$pmonth.$pyear==date(jnY)) echo '<td bgcolor=orange>'; else echo '<td bgcolor=white>';
    echo '<b>&nbsp;<a href="javascript:optionwin(\''.$daynumber[$j].'\',\''.$pmonth.'\',\''.$pyear.'\')" title="'.$LDClk4Options.'"><FONT  face="times new roman"   SIZE=4  color=red>'.$daynumber[$j].'</a></b></td>';     $j++;
    echo '</tr>';
    if($daynumber[$j]=="") break;

}
echo '</table>';
echo '</td></tr></table>';
?>

<br><FONT color=navy>

<form name="direct" method="post" onSubmit="return update()" >
<b><?php echo $LDDirectDial ?>:</b>&nbsp;&nbsp;<?php echo $LDMonth ?> <select name="month" size="1"> 

<?php for ($n=1;$n<sizeof($monat);$n++)
{    
    echo '<option ';
    if($n==$pmonth) echo 'selected';
    echo'>'.$monat[$n].'</option>';
}
?>
</select>
<?php echo $LDYear ?> <input type="text" name="jahr" size=4 value="<?php echo $pyear; ?>" >
<?php if($cfg['dhtml'])
echo '
<a href="javascript:cxjahr(\'1\')"><img '.createComIcon($root_path,'varrow-u.gif','0').' alt="'.$LDPlus1Year.'"></a>
<a href="javascript:cxjahr(\'0\')"><img '.createComIcon($root_path,'varrow-d.gif','0').' alt="'.$LDMinus1Year.'"></a>';
else echo'<input  type="button" value="+1" onClick=cxjahr(\'1\')> <input  type="button" value="-1" onClick=cxjahr(\'0\')>';
?>
&nbsp;&nbsp;&nbsp;<input  type="submit" value="<?php echo $LDGO ?>">
<p>


<div class="segContentPane">
<table class="jedList" width="50%" border="0" cellpadding="0" cellspacing="0">
    <thead>
        <tr class="nav">
            <th colspan="10">
                <div id="pageFirst" class="segDisabledLink" style="float:left" onclick="jumpToPage(this,FIRST_PAGE), initialize(modifiedCode,modifiedCash,modifiedCharge,modLen)">
                    <img title="First" src="<?= $root_path ?>images/start.gif" border="0" align="absmiddle"/>
                    <span title="First">First</span>
                </div>
                <div id="pagePrev" class="segDisabledLink" style="float:left" onclick="jumpToPage(this,PREV_PAGE), initialize(modifiedCode,modifiedCash,modifiedCharge,modLen)">
                    <img title="Previous" src="<?= $root_path ?>images/previous.gif" border="0" align="absmiddle"/>
                    <span title="Previous">Previous</span>
                </div>
                <div id="pageShow" style="float:left; margin-left:10px">
                    <span></span>
                </div>
                <div id="pageLast" class="segDisabledLink" style="float:right" onclick="jumpToPage(this,LAST_PAGE), initialize(modifiedCode,modifiedCash,modifiedCharge,modLen)">
                    <span title="Last">Last</span>
                    <img title="Last" src="<?= $root_path ?>images/end.gif" border="0" align="absmiddle"/>
                </div>
                <div id="pageNext" class="segDisabledLink" style="float:right" onclick="jumpToPage(this,NEXT_PAGE), initialize(modifiedCode,modifiedCash,modifiedCharge,modLen)">
                    <span title="Next">Next</span>
                    <img title="Next" src="<?= $root_path ?>images/next.gif" border="0" align="absmiddle"/>
                </div>
            </th>
        </tr>
    </thead>
</table>
<table id="PriceList" class="jedList" width="50%" border="0" cellpadding="0" cellspacing="0">
    <thead>
        <tr>
            <th rowspan="3" width="1%"></th>
            <th rowspan="3" width="15%" align="left">Name</th>
            <th rowspan="3" width="15%" align="center">Service Code</th>
            <th rowspan="3" width="10%" align="center">Price in Cash</th>
            <th rowspan="3" width="10%" align="center">Price in Charge</th>
            <th rowspan="3" width="10%" align="center">Date Created</th>
        </tr>
    </thead>
    <tbody id="PriceList-body">
    <?
        if($_GET['day'] && $_GET['month'] && $_GET['year'])
        {
            ?> 
              <script language="javascript">
                callAjax();
                //printAjax();
                function callAjax()
                {
                    //alert("ajax: "+<?echo $_GET['day']?>+" "+<?echo $_GET['month']?>+" "+<?echo $_GET['year']?>);
                    xajax_populatePriceHistory(<?echo $_GET['day']?>,<?echo $_GET['month']?>,<?echo $_GET['year']?>,0);
                }
              </script>
            <?
        }
        else
        {
            ?>
               <tr><td colspan="6" style="">No date selected...</td></tr>  
            <?
        }
    ?>     
    </tbody>
</table>
<img id="ajax-loading" src="<?= $root_path ?>images/loading6.gif" align="absmiddle" border="0" style="display:none"/>
</div>  


<input type="hidden" name="sid" value="<?php echo $sid ?>">
<input type="hidden" name="lang" value="<?php echo $lang ?>">
</form>
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
