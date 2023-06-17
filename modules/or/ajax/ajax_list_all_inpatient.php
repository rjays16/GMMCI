<?php
require_once('roots.php');

require($root_path.'include/inc_environment_global.php');

/*function countRec($where) {
			 global $db;
			 global $join;
		$sql = "SELECT SQL_CALC_FOUND_ROWS tbperson.pid, tbenc.encounter_nr, tbenc.admission_dt, tbenc.current_ward_nr, ".
		"concat(tbperson.name_last, ', ', tbperson.name_first) as `name`, cd.name_formal, tbenc.encounter_type ".
		"FROM care_person AS tbperson ".
		"LEFT JOIN care_encounter AS tbenc ON tbenc.encounter_nr=(SELECT encounter_nr FROM care_encounter AS enc ".
		"WHERE enc.pid=tbperson.pid AND enc.is_discharged=0 AND enc.encounter_status <> 'cancelled' ".
		"AND enc.status NOT IN ('deleted','hidden','inactive','void') ORDER BY enc.encounter_date DESC LIMIT 1) ".
		"LEFT JOIN care_department as cd on cd.nr=tbenc.current_dept_nr ".
		"WHERE $where tbperson.status NOT IN ('deleted','hidden','inactive','void')  ".
		"AND tbenc.encounter_type IN (1,3,4) AND tbenc.is_discharged=0 AND tbenc.is_maygohome=0 ".
		"AND tbenc.encounter_status <> 'cancelled' AND tbenc.status NOT IN ('deleted','hidden','inactive','void') ".
		"AND (death_date in (null,'0000-00-00','')) GROUP BY tbperson.pid ".
		"$sort $limit";
		if($result = $db->Execute($sql))
		{
		//$row = $result->FetchRow();
			//echo "rows:".$result->_numOfRows;
			return $result->_numOfRows;
		}else{
			echo "error1:".$db->ErrorMsg();
			echo "\nsql1=".$sql;
		}
}*/



global $db;
$page = $_POST['page'];
$rp = $_POST['rp'];
$sortname = $_POST['sortname'];
$sortorder = $_POST['sortorder'];
$target = $_GET['target'];
if (!$sortname) $sortname = 'admission_dt';
if (!$sortorder) $sortorder = 'desc';

$sort = "ORDER BY $sortname $sortorder";

if (!$page) $page = 1;
if (!$rp) $rp = 3;

$start = (($page-1) * $rp);

$limit = "LIMIT $start, $rp";

$query = $_POST['query'];
$where = "";
if ($query) $where = " (p.name_last LIKE '$query%' OR p.pid='$query' OR DATE_FORMAT(e.admission_dt, '%m.%d.%Y')='$query') AND";

//header("Expires: Mon, Jan 01 2009 05:00:00 GMT" );
header("Last-Modified: " . gmdate( "D, d M Y H:i:s" ) . "GMT" );
header("Cache-Control: no-cache, must-revalidate" );
header("Pragma: no-cache" );
header("Content-type: text/x-json");
$rc = false;
$sql = "SELECT SQL_CALC_FOUND_ROWS p.pid, e.encounter_nr, e.admission_dt, e.current_ward_nr,\n".
		"fn_get_person_name(p.pid) `name`, d.name_formal, e.encounter_type\n".
		"FROM care_person AS p\n".
		"INNER JOIN care_encounter AS e ON e.pid=p.pid AND e.encounter_nr=\n".
			"(SELECT encounter_nr FROM care_encounter AS enc\n".
			"WHERE enc.pid=p.pid AND enc.is_discharged=0 AND enc.encounter_status<>'cancelled'\n".
			"AND enc.status NOT IN ('deleted','hidden','inactive','void')\n".
			"ORDER BY enc.encounter_date DESC LIMIT 1) ".
		"INNER JOIN care_department d on d.nr=e.current_dept_nr\n".
		"WHERE $where p.status NOT IN ('deleted','hidden','inactive','void')\n".
		"AND e.encounter_type IN (1,3,4) AND e.is_discharged=0 AND e.is_maygohome=0\n".
		"AND e.encounter_status<>'cancelled' AND e.status NOT IN ('deleted','hidden','inactive','void')\n".
		"AND (death_date in (null,'0000-00-00','')) GROUP BY p.pid\n".
		"$sort $limit";

//echo "or query: ".$sql;
$total = 0;
	if($result=$db->Execute($sql)) {
	 $total = $db->GetOne('SELECT FOUND_ROWS()');
		$json = "";
		$json .= "{\n";
		$json .= "page: $page,\n";
		$json .= "total: $total,\n";
		$json .= "rows: [";
		while($row = $result->FetchRow()) {
			 //$select = '<a href="'.$root_path.'modules/or/request/op_request_pass.php'.URL_APPEND.'&target=or_other_charges_none'.'&encounter_nr='.$row['encounter_nr'].'&ward='.$row['current_ward_nr'].'&popUp=1&area=OR&pid='.$row['pid'].'" id="charge"></a>';
			 $select = addslashes('<a href="#" id="charge" onclick="showCharges('.
				"'{$row['encounter_nr']}',".
				"'{$row['current_ward_nr']}',".
				"'{$row['pid']}'".
			 ')"></a>');
			 if ($rc) $json .= ",";
			$json .= "\n{";
			//$json .= "id:'".$row['refno']."',";
			//$json .= "cell:['".$row['refno']."'";
			$json .= "cell:['".$row['admission_dt']."'";
			$json .= ",'".$row['pid']."'";
			$json .= ",'".ucwords(strtolower($row['name']))."'";
			$json .= ",'".$row['encounter_nr']."'";
			$json .= ",'".$row['name_formal']."'";
			//$json .= ",'".$row['info']."'";
			$json .= ",'".$select."']";
			$json .= "}";
			$rc = true;
		 }
	 }else{
		 echo "error2:".$db->ErrorMsg();
		 echo "\nsql2:".$sql;
	 }


$json .= "]\n";
$json .= "}";
echo $json;

