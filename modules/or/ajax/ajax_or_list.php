<?php
require_once('roots.php');

require($root_path.'include/inc_environment_global.php');





function countRec($where) {
			 global $db;
			 global $join;
		$sql = "SELECT count(sos.refno)
FROM seg_ops_serv sos
INNER JOIN care_encounter_op ceo ON (sos.refno = ceo.refno)
INNER JOIN care_department cd ON (cd.nr = ceo.dept_nr) $join INNER JOIN care_room cr ON (cr.nr=ceo.op_room)
WHERE (som.status not in ('cancelled', 'disapproved', 'request')) and cr.info!='' $where $sort $limit";
		$result = $db->Execute($sql);
		$row = $result->FetchRow();
		return $row[0];
}



global $db;
$page = $_POST['page'];
$rp = $_POST['rp'];
$sortname = $_POST['sortname'];
$sortorder = $_POST['sortorder'];
$target = $_GET['target'];
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
if ($query) $where = " AND (ordername LIKE '%$query%' OR sos.refno='$query' OR pid='$query' OR DATE_FORMAT(sos.request_date, '%m.%d.%Y')='$query')";

if ($target == 'select_or_request') {
	$join = 'INNER JOIN seg_or_main som ON (som.ceo_refno <> ceo.refno)';
}
elseif ($target == 'select_or_main_request') {
	$join = 'INNER JOIN seg_or_main som ON (som.ceo_refno = ceo.refno)';
}

//$total = countRec($where);



//header("Expires: Mon, Jan 01 2009 05:00:00 GMT" );
header("Last-Modified: " . gmdate( "D, d M Y H:i:s" ) . "GMT" );
header("Cache-Control: no-cache, must-revalidate" );
header("Pragma: no-cache" );
header("Content-type: text/x-json");
$json = "";
$json .= "{\n";
$json .= "page: $page,\n";
$json .= "rows: [";
$rc = false;
$query = "SELECT SQL_CALC_FOUND_ROWS sos.refno, sos.pid,
CONCAT_WS(' ',sos.request_date, sos.request_time) as request_date,
sos.pid, sos.ordername, ce.encounter_nr, ce.current_ward_nr
FROM seg_ops_serv sos INNER JOIN care_encounter_op ceo ON (sos.refno = ceo.refno)
INNER JOIN care_encounter ce ON (ce.encounter_nr = ceo.encounter_nr)
$join
WHERE (som.status not in ('cancelled', 'disapproved', 'request'))
$where $sort $limit";


//echo "or query: ".$query;

$result = $db->Execute($query);
$total = 0;
if ($result!==false) {
	$total = $db->GetOne("SELECT FOUND_ROWS()");
	while($row = $result->FetchRow()) {
		//$select = '<a href="'.$root_path.'modules/or/request/op_request_pass.php'.URL_APPEND.'&refno='.$row['refno'].'&target=or_other_charges_get'.'&encounter_nr='.$row['encounter_nr'].'&ward='.$row['current_ward_nr'].'&popUp=1&area=OR&pid='.$row['pid'].'" id="charge"></a>';
		$select = addslashes('<a href="#" id="charge" onclick="showCharges('.
			"'{$row['refno']}',".
			"'{$row['encounter_nr']}',".
			"'{$row['ward_nr']}',".
			"'{$row['pid']}'".
			');return false;"></a>');
		if ($rc) $json .= ",";
		$json .= "\n{";
		$json .= "id:'".$row['refno']."',";
		$json .= "cell:['".$row['refno']."'";
		$json .= ",'".$row['request_date']."'";
		$json .= ",'".$row['pid']."'";
		$json .= ",'".strtoupper(addslashes($row['ordername']))."'";
		$json .= ",'".$row['name_formal']."'";
		$json .= ",'".$select."']";
		$json .= "}";
		$rc = true;
	}
}
$json .= "],\n";
$json .= "total: $total\n";
$json .= "}";
echo $json;


