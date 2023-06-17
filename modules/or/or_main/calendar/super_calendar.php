<? 

header("Last-Modified: " . gmdate( "D, d M Y H:i:s" ) . "GMT" ); 
header("Cache-Control: no-cache, must-revalidate" ); 
header("Pragma: no-cache" );
header("Content-Type: text/xml; charset=utf-8");



require_once('roots.php');
require($root_path.'include/inc_environment_global.php');
global $db;


$xml = '<?xml version="1.0" ?><response><content><![CDATA[';

if($_GET['event'] != '') {
	$fields = explode("-",$_GET['event']);
	$result = mysql_query("SELECT *,DATE_FORMAT(`date`,'%b %e, %Y at %l:%i%p') as thedate,DATE_FORMAT(`date`,'%c') as themonth,DATE_FORMAT(`date`,'%Y') as theyear FROM `events` WHERE YEAR(`date`) = ".$fields[0]." AND MONTH(`date`) = ".$fields[1]." AND DAYOFMONTH(`date`) = ".$fields[2]." ORDER BY `num` ASC");
	
	$i = 0;
	while($row = mysql_fetch_array($result)) {
		$xml .= "<div id='event'";
		if($i < (mysql_num_rows($result)-1)) $xml .= " style='border-bottom:none'";
		$xml .= "><div class='heading'><div class='title'>".$row['heading']."</div><div class='posted'>".$row['thedate']."</div>";
		if($i == 0) $xml .= "<div class='back'><a href='javascript:navigate(".$row['themonth'].",".$row['theyear'].",\"\")'>Return to calendar</a></div>";
		$xml .= "</div><div class='line'>".$row['body']."</div><br /></div><br />";
		$i++;
	}
	
} else {
	$month = $_GET['month'];
	$year = $_GET['year'];
		
	if($month == '' && $year == '') { 
		$time = time();
		$month = date('n',$time);
	    $year = date('Y',$time);
	}
	
	$date = getdate(mktime(0,0,0,$month,1,$year));
	$today = getdate();
	$hours = $today['hours'];
	$mins = $today['minutes'];
	$secs = $today['seconds'];
	
	if(strlen($hours)<2) $hours="0".$hours;
	if(strlen($mins)<2) $mins="0".$mins;
	if(strlen($secs)<2) $secs="0".$secs;
	
	$days=date("t",mktime(0,0,0,$month,1,$year));
	$start = $date['wday']+1;
	$name = $date['month'];
	$year2 = $date['year'];
	$offset = $days + $start - 1;
	 
	if($month==12) { 
		$next=1; 
		$nexty=$year + 1; 
	} else { 
		$next=$month + 1; 
		$nexty=$year; 
	}
	
	if($month==1) { 
		$prev=12; 
		$prevy=$year - 1; 
	} else { 
		$prev=$month - 1; 
		$prevy=$year; 
	}
	
	if($offset <= 28) $weeks=28; 
	elseif($offset > 35) $weeks = 42; 
	else $weeks = 35; 
	
	$xml .= "<table class='cal' cellpadding='0' cellspacing='1'>
			<tr>
				<td colspan='7' class='calhead'>
					<table>
					<tr>
						<td>
							<a href='javascript:navigate($prev,$prevy,\"\")' style='border:none'><img src='../../../modules/or/or_main/calendar/images/calLeft.gif' alt='prev' /></a> <a href='javascript:navigate(\"\",\"\",\"\")' style='border:none'><img src='../../../modules/or/or_main/calendar/images/calCenter.gif' alt='current' /></a> <a href='javascript:navigate($next,$nexty,\"\")' style='border:none'><img src='../../../modules/or/or_main/calendar/images/calRight.gif' alt='next' /></a> <a href='javascript:void(0)' onClick='showJump(this)' style='border:none'><img src='../../../modules/or/or_main/calendar/images/calDown.gif' alt='jump' /></a> 
						</td>
                        <td width='*' align='center'>OR Calendar</td>
						<td align='right'>
							$name $year2
						</td>
					</tr>
					</table>
				</td>
			</tr>
			<tr class='dayhead'>
				<td class=\"sunday\">Sun</td>
				<td>Mon</td>
				<td>Tue</td>
				<td>Wed</td>
				<td>Thu</td>
				<td>Fri</td>
				<td>Sat</td>
			</tr>";
	
	$col=1;
	$cur=1;
	$next=0;
	
	for($i=1;$i<=$weeks;$i++) { 
		if($next==3) $next=0;
		if($col==1) $xml.="\n<tr class='dayrow'>"; 
	  	
		$xml.="\t<td valign='top' onMouseOver=\"this.className='dayover'\" onMouseOut=\"this.className='dayout'\">";
	
		if($i <= ($days+($start-1)) && $i >= $start) {
            if ($col==1)
			  $xml.="<div class='sunday'><b";
            else
              $xml.="<div class='day'><b";
	
			if(($cur==$today[mday]) && ($name==$today[month]) && ($year2==$today[year])) $xml.=" style='color:#005BCC'";
	
			$xml.=">$cur</b></div>";
			
			/*$result = mysql_query("SELECT DATE_FORMAT(`date`,'%Y-%m-%e') FROM `events` WHERE MONTHNAME(`date`)='$name' AND DAYOFMONTH(`date`)=$cur AND YEAR(`date`)=$year2");
            <img src='../../../modules/or/or_main/calendar/images/calLeft.gif'
			if(mysql_num_rows($result) > 0) {
				$row = mysql_fetch_row($result);
				$xml.="<div class='calevent'><a href='javascript:navigate(\"\",\"\",\"".$row[0]."\")'>Event</a></div>";			
			} */
            $query = "SELECT (select count(sos.refno) from seg_ops_serv sos inner join care_encounter_op ceo on (sos.refno = ceo.refno)
                      inner join seg_or_main som on (som.ceo_refno = sos.refno) where som.status IN ('request', 'approved') AND
                      MONTHNAME(sos.request_date)='$name' AND DAYOFMONTH(sos.request_date)=$cur AND YEAR(sos.request_date)=$year2) as request_count,
                      (select count(sos.refno) from seg_ops_serv sos inner join care_encounter_op ceo on (sos.refno = ceo.refno)
                      inner join seg_or_main som on (som.ceo_refno = sos.refno) where som.status IN ('scheduled', 'pre_op') AND
                      MONTHNAME(ceo.op_date)='$name' AND DAYOFMONTH(ceo.op_date)=$cur AND YEAR(ceo.op_date)=$year2) as operation_count";
            //$result = mysql_query("SELECT DATE_FORMAT(`date`,'%Y-%m-%e') FROM `events` WHERE MONTHNAME(`date`)='$name' AND DAYOFMONTH(`date`)=$cur AND YEAR(`date`)=$year2");
            //<img src='../../../modules/or/or_main/calendar/images/calLeft.gif'
             
            $result = $db->Execute($query);
             
            
            $row = $result->FetchRow();
            $img = "";
            if ($row['request_count'] > 0 && $row['operation_count'] <= 0) {
              $img = '<img src="../../../modules/or/or_main/calendar/images/request_legend.png" onclick="show_popup(\''.$name.'\', '.$cur.', '.$year2.', \'requests\')" style="cursor:pointer" />'; 
            }
            elseif ($row['operation_count'] > 0 && $row['request_count'] <= 0) {                
              $img = '<img src="../../../modules/or/or_main/calendar/images/operate_legend.png" onclick="show_popup(\''.$name.'\', '.$cur.', '.$year2.', \'operations\')" style="cursor:pointer" />'; 
            }
            elseif ($row['operation_count'] > 0 && $row['request_count'] > 0) {
              $img = '<img src="../../../modules/or/or_main/calendar/images/request_operate_legend.png" onclick="show_popup(\''.$name.'\', '.$cur.', '.$year2.', \'request_operation\')"  style="cursor:pointer" />'; 
            }   
            //if(mysql_num_rows($result) > 0) {
              //  $row = mysql_fetch_row($result);
                $xml.="<div class='calevent'>$img</div>";            
            //} 
			
			$xml.="\n\t</td>\n";
	
			$cur++; 
			$col++; 
			
		} else { 
			$xml.="&nbsp;\n\t</td>\n"; 
			$col++; 
		}  
		    
	    if($col==8) { 
		    $xml.="\n</tr>\n"; 
		    $col=1; 
	    }
	}
	
	$xml.="</table>";
	  
}
	
$xml .= "]]></content></response>";
echo $xml;

?>