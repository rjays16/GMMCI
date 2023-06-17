<?php
	require('./roots.php');
	include_once($root_path.'modules/laboratory/seg_lab_cron_destination.php');		
	include_once($root_path.'modules/laboratory/seg_lab_cron_source.php');
	require($root_path.'include/inc_environment_global.php');
	
  global $db;
	
	$sql = "SELECT encounter_nr, count(result_code) AS counter
					FROM seg_encounter_result_copy 
					group by encounter_nr
					HAVING count(result_code) > 1
					order by count(result_code) desc";
					
	$rs = $db->Execute($sql);				
	if ($rs){
		while ($row=$rs->FetchRow()){
				#echo "<br>".$row['encounter_nr']." - ".$row["counter"];
			if ($row['counter']>1){	
				$limit = ($row['counter'])-1;
				echo "<br>cn = ".$row["counter"]." - ".$limit;
				$sql_del = "delete from seg_encounter_result_copy where encounter_nr = '".$row['encounter_nr']."' 
										order by create_time asc LIMIT ".$limit;
				#echo "<br>".$sql_del;						
				$db->Execute($sql_del);
			}	
		}
	}
?>
