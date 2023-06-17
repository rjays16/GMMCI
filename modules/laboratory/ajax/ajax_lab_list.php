<?php
require_once('roots.php');

require($root_path.'include/inc_environment_global.php');





function countRec($where) {
			 global $db;
		$sql = "SELECT count(distinct ls.refno) FROM seg_lab_serv ls
left join seg_lab_servdetails ld ON (ls.refno=ld.refno)
left JOIN care_encounter ce ON (ce.encounter_nr = ls.encounter_nr)
INNER JOIN care_department cd ON (cd.nr = ce.current_dept_nr)
WHERE (ld.is_served='1') $where $sort $limit";
		//echo $sql;
		if($result = $db->Execute($sql))
		{$row = $result->FetchRow();
		return $row[0];
		}else
		{
			echo 'error:'.$db->ErrorMsg();
		}
}



global $db;
$page = $_POST['page'];
$rp = $_POST['rp'];
$sortname = $_POST['sortname'];
$sortorder = $_POST['sortorder'];
//$target = $_GET['target'];
if (!$sortname) $sortname = 'request_date';
if (!$sortorder) $sortorder = 'desc';

$sort = "ORDER BY $sortname $sortorder";

if (!$page) $page = 1;
if (!$rp) $rp = 3;

$start = (($page-1) * $rp);

$limit = "LIMIT $start, $rp";

$query = $_POST['query'];
//$qtype = $_POST['qtype'];
$where = "";
if ($query) $where = " AND (ordername LIKE '%$query%' OR ls.pid='$query' OR DATE_FORMAT(ls.serv_dt, '%Y.%m.%d')='$query')";

/*if ($target == 'select_or_request') {
	$join = 'INNER JOIN seg_or_main som ON (som.ceo_refno <> ceo.refno)';
}
elseif ($target == 'select_or_main_request') {
	$join = 'INNER JOIN seg_or_main som ON (som.ceo_refno = ceo.refno)';
} */

$total = countRec($where);



//header("Expires: Mon, Jan 01 2009 05:00:00 GMT" );
header("Last-Modified: " . gmdate( "D, d M Y H:i:s" ) . "GMT" );
header("Cache-Control: no-cache, must-revalidate" );
header("Pragma: no-cache" );
header("Content-type: text/x-json");
$json = "";
$json .= "{\n";
$json .= "page: $page,\n";
$json .= "total: $total,\n";
$json .= "rows: [";
$rc = false;

$query ="SELECT distinct ls.refno, concat_ws(' ',ls.serv_dt, ls.serv_tm) as `request_date`, ls.pid, ls.ordername, cd.name_formal,
ce.current_ward_nr, ce.encounter_nr
FROM seg_lab_serv ls
left join seg_lab_servdetails ld ON (ls.refno=ld.refno)
left JOIN care_encounter ce ON (ce.encounter_nr = ls.encounter_nr)
INNER JOIN care_department cd ON (cd.nr = ce.current_dept_nr)
WHERE (ld.is_served='1') $where $sort $limit";

	 $result = $db->Execute($query);
	 while($row = $result->FetchRow()) {
			$select = '<a href="../../modules/laboratory/labor_test_request_pass.php'.URL_APPEND.'&refno='.$row['refno'].'&target=lab_other_charges'.'&encounter_nr='.$row['encounter_nr'].'&ward='.$row['current_ward_nr'].'&popUp=1&area=LAB&pid='.$row['pid'].'" id="charge"></a>';

		 if ($rc) $json .= ",";
		$json .= "\n{";
		$json .= "id:'".$row['refno']."',";
		$json .= "cell:['".$row['refno']."'";
		$json .= ",'".$row['request_date']."'";
		$json .= ",'".$row['pid']."'";
		$json .= ",'".ucwords(strtolower($row['ordername']))."'";
		$json .= ",'".$row['name_formal']."'";
		$json .= ",'".$select."']";
		$json .= "}";
		$rc = true;
	 }


$json .= "]\n";
$json .= "}";
echo $json;

?>

