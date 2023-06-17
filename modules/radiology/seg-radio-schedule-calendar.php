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
/*
if($retpath=='home') $breakfile=$root_path.'main/startframe.php'.URL_APPEND;
	else $breakfile=$root_path.'main/spediens.php'.URL_APPEND;
*/
	# burn added: November 22, 2007
$append=URL_APPEND."&status=".$status."&target=".$target."&noresize=1&user_origin=".$user_origin."&dept_nr=".$dept_nr;   # burn added: Septmeber 19, 2007
$breakfile="radiolog.php$append";   
$breakfile=$root_path.'modules/radiology/'.$breakfile;

$thisfile=basename(__FILE__);
	 
$datum=strftime("%d.%m.%Y");
$zeit=strftime("%H.%M");
$toggler=0;

if($pmonth=='') $pmonth=date('n');
if($pyear=='') $pyear=date('Y');

function getmaxdays($mon,$yr)
{
	if ($mon==2){ if (checkdate($mon,29,$yr)) return 29; else return 28;}
	else
	{
		if(checkdate($mon,31,$yr)) return 31; else return 30;
	}
}

$maxdays=getmaxdays($pmonth,$pyear);

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
 $smarty->assign('sToolbarTitle',"Radiology :: Scheduling ".$LDCalendar);

 # href for help button
 $smarty->assign('pbHelp',"javascript:gethelp('calendar.php')");

 # href for close button
 $smarty->assign('breakfile',$breakfile);

 # Window bar title
 $smarty->assign('sWindowTitle',"Radiology :: Scheduling ".$LDCalendar);

# Buffer page output

ob_start();
?>

<script language="javascript" >
<!-- 
var urlholder;

function update()
{

	var mbuf=document.direct.month.selectedIndex+1;
	var jbuf=document.direct.jahr.value;
	if(!isNaN(jbuf)){
		jbuf=parseInt(jbuf);
	//	var urltarget="calendar.php<?php echo URL_REDIRECT_APPEND; ?>&pmonth="+mbuf+"&pyear="+jbuf;
		var urltarget="<?php echo $thisfile.$append; ?>&pmonth="+mbuf+"&pyear="+jbuf;		
		window.location.replace(urltarget);
	}else{
		document.direct.jahr.select();
	}
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
	var rpath=document.getElementById('rpath').value;
	var obj_sub_dept_nr = document.getElementById('sub_dept_nr');
	var sub_dept_nr_index =	document.getElementById('sub_dept_nr').selectedIndex;
	var sub_dept_nr_name = obj_sub_dept_nr[sub_dept_nr_index].text;
	var sub_dept_nr = obj_sub_dept_nr.value;
/*
	var msg = "obj_sub_dept_nr = '"+obj_sub_dept_nr+"' \n"+
			"sub_dept_nr_index = '"+sub_dept_nr_index+"' \n"+
			"sub_dept_nr = '"+sub_dept_nr+"' \n"+
			"sub_dept_nr_name = '"+sub_dept_nr_name+"' \n";
	alert(msg);
*/
	if (sub_dept_nr==0){
		alert("Please select a department!");
		document.getElementById('sub_dept_nr').focus();
		//return false;
	}else{
		if (!d) d="";
	//	urlholder="calendar-options.php?sid=<?php echo "$sid&lang=$lang" ?>&day="+d+"&month="+m+"&year="+y;
		urlholder="seg-radio-schedule-daily.php?sid=<?php echo "$sid&lang=$lang" ?>&day="+d+"&month="+m+"&year="+y+"&dept_nr=<?=$dept_nr?>"+"&sub_dept_nr="+sub_dept_nr+"&sub_dept_nr_name="+sub_dept_nr_name;
	//	alert("urlholder : \n'"+urlholder+"'");	
	//	optwin=window.open(urlholder,"optwin","width=500,height=400,menubar=no,resizable=yes,scrollbars=yes");
		window.location.replace(urlholder);
	}
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
<br>

<select name="sub_dept_nr" id="sub_dept_nr">
	<option value='0'>-Select department-</option>
<?php 
#Department object
include_once($root_path.'include/care_api_classes/class_department.php');
$dept_obj = new Department;

$radio_sub_dept=$dept_obj->getSubDept($dept_nr);

if($dept_obj->rec_count){
	while ($rowSubDept = $radio_sub_dept->FetchRow()){
		echo "	<option value='".$rowSubDept['nr']."'>".trim($rowSubDept['name_formal'])."</option>\n";
	}
}
?>
</select>

<ul>

<?php 
echo '
	<table cellspacing=0 cellpadding=0 border=0>
		<tr>
			<td align=left>'."\n";
#echo '<a href="calendar.php'.URL_APPEND.'&pmonth=';
echo '			<a href="'.$thisfile.$append.'&pmonth='."\n";
if($pmonth<2) 
	echo '12&pyear='.($pyear-1).'" title="'.$LDPrevMonth.'"><FONT  SIZE=4 color=silver><b>&lt;'.$monat[12]."\n";
else 
	echo ($pmonth-1).'&pyear='.$pyear.'" title="'.$LDPrevMonth.'"><FONT  SIZE=4 color=silver><b>&lt;'.$monat[$pmonth-1]."\n";
echo '			</a>
			</td>'."\n"; 
echo '			<td align=center>'."\n";
echo '				<FONT  SIZE=6 color=navy><b>'.$monat[(int)$pmonth].' '.$pyear.'</b></font>'."\n";
echo '			</td>'."\n";
echo '			<td align=right><FONT  SIZE=4 color=silver><b>'."\n";
#echo '				<a href="calendar.php'.URL_APPEND.'&pmonth=';
echo '				<a href="'.$thisfile.$append.'&pmonth=';
if($pmonth>11) 
	echo '1&pyear='.($pyear+1).'" title="'.$LDNextMonth.'"><FONT  SIZE=4 color=silver><b>'.$monat[1]."\n";
else 
	echo ($pmonth+1).'&pyear='.$pyear.'" title="'.$LDNextMonth.'"><FONT  SIZE=4 color=silver><b>'.$monat[$pmonth+1]."\n";
echo '&gt;</a></td></tr><tr><td bgcolor=black colspan=3>'."\n";

echo '<table border="0" cellspacing=1 cellpadding=5 width="100%">'."\n";

echo '<tr>'."\n";
for($n=0;$n<6;$n++)
	{
		echo '<td bgcolor=white width="14%"><FONT SIZE=4 ><b>'.$tagename[$n].'</b></td>'."\n";
	}
echo '<td bgcolor="#ffffcc"><FONT SIZE=4 color=red ><b>'.$tagename[6].'</b></td>'."\n";
echo '</tr>'."\n";

$j=0;
for($x=0;$x<6;$x++)
{	echo '<tr>'."\n";
	
		for($n=0;$n<6;$n++)
		{
			if($daynumber[$j].$pmonth.$pyear==date(jnY)) echo '<td bgcolor=orange>'; else echo '<td bgcolor=white>';
			echo '<FONT face="times new roman"   SIZE=8  color=navy><b>&nbsp;<a href="javascript:optionwin(\''.$daynumber[$j].'\',\''.$pmonth.'\',\''.$pyear.'\')" title="'.$LDClk4Options.'">'.$daynumber[$j].' </a></b></td>'; $j++;
		}
	if($daynumber[$j].$pmonth.$pyear==date(jnY)) echo '<td bgcolor=orange>'; else echo '<td bgcolor=white>';
	echo '<b>&nbsp;<a href="javascript:optionwin(\''.$daynumber[$j].'\',\''.$pmonth.'\',\''.$pyear.'\')" title="'.$LDClk4Options.'"><FONT  face="times new roman"   SIZE=8  color=red>'.$daynumber[$j].'</a></b></td>'; 	$j++;
	echo '</tr>'."\n";
	if($daynumber[$j]=="") break;

}
echo '</table>'."\n";
echo '</td></tr></table>'."\n";
?>

<br><FONT color=navy>

<form name="direct" method=get onSubmit="return update()" >
<b><?php echo $LDDirectDial ?>:</b>&nbsp;&nbsp;<?php echo $LDMonth ?> 
<select name="month" size="1"> 
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
	<input type="hidden" name="sid" id="sid" value="<?php echo $sid ?>">
	<input type="hidden" name="lang" id="lang" value="<?php echo $lang ?>">
	<input type="hidden" name="rpath" id="rpath" value="<?php echo $root_path; ?>">
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
